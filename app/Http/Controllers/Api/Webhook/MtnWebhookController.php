<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\Paiement;
use App\Models\Naissance;
use App\Models\Mariage;
use App\Models\Deces;
use App\Models\User;
use App\Notifications\DemandeNaissanceConfirmationNotification;
use App\Notifications\DemandeMariageConfirmationNotification;
use App\Notifications\DemandeDecesConfirmationNotification;
use App\Services\YellikaSmsService;
use App\Traits\HandlesFreeRequests;

class MtnWebhookController extends Controller
{
    use HandlesFreeRequests;
    /**
     * Handle MTN Collection Webhook for RequestToPay.
     * MTN will send a PUT or POST request here with the transaction details.
     */
    public function handle(Request $request)
    {
        Log::info('MTN Webhook received: ' . json_encode($request->all()));

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'referenceId' => 'required|string',
            'status' => 'required|string',
            'externalId' => 'required|string',
            'amount' => 'nullable|numeric',
            'currency' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning('MTN Webhook validation failed: ' . json_encode($validator->errors()));
            return response()->json(['message' => 'Missing or invalid data', 'errors' => $validator->errors()], 400);
        }

        // MTN sends the reference ID, status, and other transaction details
        $statusInfo = $validator->validated();

        $mtnRef = $statusInfo['referenceId'];
        $status = $statusInfo['status'];
        $externalId = $statusInfo['externalId']; // This is our internal reference (e.g. AN...)

        if ($status === 'SUCCESSFUL') {
            Log::info("MTN Webhook: Transaction $externalId was successful");

            // Process payment completion
            $this->processSuccessfulPayment($externalId, $statusInfo);
        } elseif ($status === 'FAILED') {
            Log::warning("MTN Webhook: Transaction $externalId failed");
            // Optionally handle failed transaction logic here based on externalId
        }

        // Always return 200 OK so MTN knows we received the webhook
        return response()->json(['message' => 'Processed'], 200);
    }

    private function processSuccessfulPayment($reference, $statusInfo)
    {
        // Prevent duplicate processing
        $paiement = Paiement::where('transaction_id', $reference)->where('operator_id', 'MTN')->first();
        if ($paiement) {
            Log::info("MTN Webhook: Payment for $reference already processed");
            return;
        }

        // Extraire la référence de base si modification
        $baseReference = $reference;
        $isModification = str_contains($reference, '-MOD-');
        if ($isModification) {
            $baseReference = explode('-MOD-', $reference)[0];
        }

        // Determine request type
        $type = null;
        $demande = null;
        if (str_starts_with($baseReference, 'AN')) {
            $type = 'naissance';
            $demande = Naissance::where('reference', $baseReference)->first();
        } elseif (str_starts_with($baseReference, 'AM')) {
            $type = 'mariage';
            $demande = Mariage::where('reference', $baseReference)->first();
        } elseif (str_starts_with($baseReference, 'AD')) {
            $type = 'deces';
            $demande = Deces::where('reference', $baseReference)->first();
        }

        if ($demande) {
            // MTN returns amount and currency
            $amount = isset($statusInfo['amount']) ? (float)$statusInfo['amount'] : 0;

            // Calculer la part timbre et part livraison de cette transaction
            $partTimbre = 0;
            $partLivraison = 0;

            if ($isModification) {
                $dejaPayeTimbre = Paiement::where("{$type}_id", $demande->id)
                    ->where('status', 'ACCEPTED')
                    ->where('transaction_id', '!=', $reference)
                    ->get()
                    ->sum(function ($p) {
                        return (float) ($p->raw_response['part_timbre'] ?? 0);
                    });

                $dejaPayeLivraison = Paiement::where("{$type}_id", $demande->id)
                    ->where('status', 'ACCEPTED')
                    ->where('transaction_id', '!=', $reference)
                    ->get()
                    ->sum(function ($p) {
                        return (float) ($p->raw_response['part_livraison'] ?? 0);
                    });

                $cacheKey = 'pending_delivery_update_' . $demande->reference;
                if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                    $pendingData = \Illuminate\Support\Facades\Cache::get($cacheKey);
                    $nouveauMontantLivraison = (float) ($pendingData['montant_livraison'] ?? 0);

                    $partLivraison = max(0.0, $nouveauMontantLivraison - $dejaPayeLivraison);
                    $partTimbre = max(0.0, $amount - $partLivraison);
                } else {
                    $partTimbre = $amount;
                    $partLivraison = 0.0;
                }
            } else {
                $partTimbre = (float) ($demande->montant_timbre ?? 0);
                $partLivraison = (float) ($demande->montant_livraison ?? 0);
            }

            Paiement::create([
                'user_id' => $demande->user_id,
                'transaction_id' => $reference,
                'operator_id' => 'MTN',
                'montant' => $amount,
                'currency' => $statusInfo['currency'] ?? 'XOF',
                'status' => 'ACCEPTED',
                'paid_at' => now(),
                "{$type}_id" => $demande->id,
                'raw_response' => [
                    'part_timbre' => $partTimbre,
                    'part_livraison' => $partLivraison,
                    'is_modification' => $isModification,
                    'mtn_status_info' => $statusInfo
                ]
            ]);

            $this->applyPendingDeliveryUpdate($demande);
            $demande->etat = 'en attente';
            if ($demande->choix_option === 'livraison') {
                $demande->statut_livraison = 'en attente';
            }
            $demande->save();

            // Incrémenter les free_requests si applicable
            try {
                $this->incrementFreeRequestsFromDemande($demande);
            } catch (\Exception $e) {
                Log::warning("MTN Webhook: free_requests incrément échoué pour {$reference} : " . $e->getMessage());
            }

            // ✅ Envoi SMS + email de confirmation à l'utilisateur
            $user = User::find($demande->user_id);
            if ($user) {
                $this->sendNotifications($user, $demande, $type);
            }

            Log::info("MTN Webhook: Successfully processed payment for $reference ($type)");
        } else {
            Log::error("MTN Webhook: Could not find model for reference $reference");
        }
    }

    /**
     * Envoie SMS + email de confirmation après paiement MTN réussi.
     */
    private function sendNotifications($user, $demande, $type)
    {
        try {
            $yellikaSmsService = app(YellikaSmsService::class);
            $phoneNumber = $user->indicatif . $user->contact;
            $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de {$type} a été confirmé. Référence : {$demande->reference}. Votre demande est maintenant en attente de traitement par la mairie du plateau.";

            $yellikaSmsService->sendSms($phoneNumber, $message);

            // Envoi email
            $notificationClass = match ($type) {
                'naissance' => DemandeNaissanceConfirmationNotification::class,
                'mariage'   => DemandeMariageConfirmationNotification::class,
                'deces'     => DemandeDecesConfirmationNotification::class,
                default     => null,
            };

            if ($notificationClass) {
                Notification::send($user, new $notificationClass($user, $demande));
            }

            Log::info("MTN Webhook: SMS + email envoyés pour {$demande->reference}");
        } catch (\Exception $e) {
            Log::error("Erreur notifications Webhook MTN pour {$demande->reference}: " . $e->getMessage());
        }
    }
}
