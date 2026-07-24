<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\DecesGroupe;
use App\Models\Mariage;
use App\Models\MariageGroupe;
use App\Models\Naissance;
use App\Models\NaissanceGroupe;
use App\Models\Paiement;
use App\Models\User;
use App\Services\YellikaSmsService;
use App\Traits\HandlesFreeRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stripe\Event;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    use HandlesFreeRequests;

    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        Log::info('Stripe API Webhook reçu');

        $event = null;

        if ($endpointSecret) {
            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } catch (\UnexpectedValueException $e) {
                Log::error('Stripe Webhook: Invalid payload - ' . $e->getMessage());
                return response()->json(['error' => 'Invalid payload'], 400);
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                Log::error('Stripe Webhook: Invalid signature - ' . $e->getMessage());
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        } else {
            // Si le secret n'est pas encore configuré en local/dev, décoder l'événement directement
            $data = json_decode($payload, true);
            if (!$data || !isset($data['type'])) {
                return response()->json(['error' => 'Invalid payload format'], 400);
            }
            $event = Event::constructFrom($data);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $reference = $session->metadata->reference ?? null;
            $type = $session->metadata->type ?? null;

            Log::info("Stripe Webhook checkout.session.completed pour référence: {$reference}, type: {$type}");

            if ($reference) {
                $this->processPaymentSuccess($reference, $type, $session);
            }
        }

        return response()->json(['status' => 'success']);
    }

    private function processPaymentSuccess(string $reference, ?string $type, $session): void
    {
        $baseReference = $reference;
        $isModification = str_contains($reference, '-MOD-');
        if ($isModification) {
            $baseReference = explode('-MOD-', $reference)[0];
        }

        $demande = null;
        $isGroupe = false;
        $groupeColumn = null;

        if (str_starts_with($baseReference, 'GRN')) {
            $demande = NaissanceGroupe::where('reference', $baseReference)->first();
            $type = 'naissance_groupe';
            $isGroupe = true;
            $groupeColumn = 'naissance_groupe_id';
        } elseif (str_starts_with($baseReference, 'GRM')) {
            $demande = MariageGroupe::where('reference', $baseReference)->first();
            $type = 'mariage_groupe';
            $isGroupe = true;
            $groupeColumn = 'mariage_groupe_id';
        } elseif (str_starts_with($baseReference, 'GRD')) {
            $demande = DecesGroupe::where('reference', $baseReference)->first();
            $type = 'deces_groupe';
            $isGroupe = true;
            $groupeColumn = 'deces_groupe_id';
        } elseif (str_starts_with($baseReference, 'AN')) {
            $demande = Naissance::where('reference', $baseReference)->first();
            $type = 'naissance';
        } elseif (str_starts_with($baseReference, 'AM')) {
            $demande = Mariage::where('reference', $baseReference)->first();
            $type = 'mariage';
        } elseif (str_starts_with($baseReference, 'AD')) {
            $demande = Deces::where('reference', $baseReference)->first();
            $type = 'deces';
        }

        if (!$demande) {
            Log::error("Stripe Webhook: Demande introuvable pour la référence {$reference}");
            return;
        }

        $paiementColumn = $isGroupe ? $groupeColumn : "{$type}_id";
        $existingPaiement = Paiement::where('transaction_id', $reference)->first();

        if ($existingPaiement) {
            Log::info("Stripe Webhook: Paiement déjà enregistré pour {$reference}");
            return;
        }

        $amountPaid = isset($session->amount_total) ? ((float) $session->amount_total) : 0;

        $partTimbre = (float) ($demande->montant_timbre ?? 0);
        $partLivraison = (float) ($demande->montant_livraison ?? 0);

        try {
            $paiement = Paiement::create([
                'user_id' => $demande->user_id,
                'transaction_id' => $reference,
                'operator_id' => 'STRIPE',
                'montant' => $amountPaid > 0 ? $amountPaid : ($partTimbre + $partLivraison),
                'currency' => strtoupper($session->currency ?? 'XOF'),
                'status' => 'ACCEPTED',
                'paid_at' => now(),
                $paiementColumn => $demande->id,
                'raw_response' => [
                    'stripe_session_id' => $session->id ?? null,
                    'part_timbre' => $partTimbre,
                    'part_livraison' => $partLivraison,
                    'is_modification' => $isModification,
                ]
            ]);

            $this->applyPendingDeliveryUpdate($demande);
            $demande->etat = 'en attente';
            if ($demande->choix_option === 'livraison') {
                $demande->statut_livraison = 'en attente';
            }
            $demande->save();

            if ($isGroupe && method_exists($demande, 'lignes')) {
                $demande->lignes()->update(['etat' => 'en attente']);
            }

            $this->incrementFreeRequestsFromDemande($demande);

            $user = User::find($demande->user_id);
            if ($user) {
                $typeLabel = match (true) {
                    str_contains($type, 'naissance') => 'naissance',
                    str_contains($type, 'mariage') => 'mariage',
                    str_contains($type, 'deces') => 'décès',
                    default => $type,
                };
                $message = "Bonjour {$user->name}, votre paiement Stripe pour la demande d'extrait de {$typeLabel} a été confirmé. Référence : {$demande->reference}.";
                app(YellikaSmsService::class)->sendSms($user->indicatif . $user->contact, $message);
            }

            Log::info("Stripe Webhook: Traitement terminé avec succès pour {$reference}");
        } catch (\Exception $e) {
            Log::error("Stripe Webhook: Erreur lors de l'enregistrement du paiement: " . $e->getMessage());
        }
    }
}
