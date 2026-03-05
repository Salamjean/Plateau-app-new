<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\Paiement;
use App\Models\User;
use App\Notifications\DemandeDecesConfirmationNotification;
use App\Notifications\DemandeMariageConfirmationNotification;
use App\Notifications\DemandeNaissanceConfirmationNotification;
use App\Services\YellikaSmsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class WaveWebhookController extends Controller
{
    /**
     * Handle incoming Wave webhook notifications.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        Log::info('Webhook Wave Reçu', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        // Validation de la signature Wave (fortement recommandé)
        if (!$this->validateWaveSignature($request)) {
            Log::warning('Webhook Wave: Signature invalide');
            return response()->json(['success' => false, 'message' => 'Signature invalide'], 401);
        }

        $eventType = $request->input('type');

        if ($eventType !== 'checkout.session.completed') {
            Log::info("Webhook Wave: Événement ignoré ({$eventType})");
            return response()->json(['success' => true, 'message' => 'Événement ignoré'], 200);
        }

        $checkoutData = $request->input('data');
        if (!$checkoutData) {
            return response()->json(['success' => false, 'message' => 'Données de session manquantes'], 400);
        }

        $clientReference = $checkoutData['client_reference'] ?? null;
        if (!$clientReference) {
            Log::warning('Webhook Wave: client_reference manquant dans checkout.session.completed');
            return response()->json(['success' => false, 'message' => 'Client reference manquant'], 200);
        }

        // Identifier le type de demande à partir de la référence
        $demande = null;
        $type = null;

        if (str_starts_with($clientReference, 'AN')) {
            $demande = Naissance::where('reference', $clientReference)->first();
            $type = 'naissance';
        } elseif (str_starts_with($clientReference, 'AM')) {
            $demande = Mariage::where('reference', $clientReference)->first();
            $type = 'mariage';
        } elseif (str_starts_with($clientReference, 'AD')) {
            $demande = Deces::where('reference', $clientReference)->first();
            $type = 'deces';
        }

        if (!$demande) {
            Log::warning("Webhook Wave: Aucune demande trouvée pour la référence {$clientReference}");
            return response()->json(['success' => true, 'message' => 'Demande non trouvée'], 200);
        }

        return $this->processPaymentSuccess($demande, $type, $checkoutData, $request->all());
    }

    /**
     * Valide la signature transmise par Wave.
     */
    private function validateWaveSignature(Request $request): bool
    {
        $waveSignature = $request->header('wave-signature');
        $waveWebhookSecret = config('services.wave.webhook_secret');

        if (!$waveWebhookSecret || !$waveSignature) {
            return true; // Si pas de secret configuré, on laisse passer pour le moment (optionnel)
        }

        $timestamp = null;
        $signatures = [];

        $parts = explode(',', (string) $waveSignature);
        foreach ($parts as $part) {
            list($prefix, $value) = array_pad(explode('=', trim($part), 2), 2, null);
            if ($prefix === 't') {
                $timestamp = $value;
            } elseif ($prefix === 'v1') {
                $signatures[] = $value;
            }
        }

        if (!$timestamp || empty($signatures)) {
            return false;
        }

        $payload = $timestamp . $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $waveWebhookSecret);

        return in_array($expectedSignature, $signatures);
    }

    /**
     * Traite le paiement réussi et met à jour la demande.
     */
    private function processPaymentSuccess($demande, $type, $checkoutData, $rawBody): JsonResponse
    {
        try {
            // 1. Enregistrer le paiement
            $paiementData = [
                'user_id' => $demande->user_id,
                'transaction_id' => $checkoutData['client_reference'],
                'operator_id' => $checkoutData['id'],
                'montant' => $checkoutData['amount'] ?? null,
                'currency' => $checkoutData['currency'] ?? 'XOF',
                'status' => 'ACCEPTED',
                'paid_at' => isset($checkoutData['when_completed']) ? Carbon::parse($checkoutData['when_completed']) : now(),
                'raw_response' => $rawBody,
            ];

            // Associer l'ID de la demande selon le type
            $paiementData["{$type}_id"] = $demande->id;

            Paiement::create($paiementData);

            // 2. Mettre à jour l'état de la demande
            $demande->etat = 'en attente';
            $demande->save();

            // 3. Envoyer les notifications
            $user = User::find($demande->user_id);
            if ($user) {
                $this->sendNotifications($user, $demande, $type);
            }

            return response()->json(['success' => true, 'message' => 'Paiement traité avec succès'], 200);

        } catch (\Exception $e) {
            Log::error("Erreur lors du traitement du paiement Wave pour {$demande->reference}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur interne de traitement'], 500);
        }
    }

    /**
     * Gère l'envoi des SMS et emails de confirmation.
     */
    private function sendNotifications($user, $demande, $type)
    {
        try {
            $yellikaSmsService = app(YellikaSmsService::class);
            $phoneNumber = $user->indicatif . $user->contact;
            $typeLabel = ucfirst($type);
            
            $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de {$type} a été confirmé. Référence : {$demande->reference}. Votre demande est maintenant en attente de traitement.";
            
            $yellikaSmsService->sendSms($phoneNumber, $message);

            // Envoi de l'email
            $notificationClass = match($type) {
                'naissance' => DemandeNaissanceConfirmationNotification::class,
                'mariage' => DemandeMariageConfirmationNotification::class,
                'deces' => DemandeDecesConfirmationNotification::class,
                default => null,
            };

            if ($notificationClass) {
                Notification::send($user, new $notificationClass($user, $demande));
            }
            
        } catch (\Exception $e) {
            Log::error("Erreur notifications Webhook Wave: " . $e->getMessage());
        }
    }
}
