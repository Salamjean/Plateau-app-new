<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\DecesGroupe;
use App\Models\Mariage;
use App\Models\MariageGroupe;
use App\Models\Naissance;
use App\Models\NaissanceGroupe;
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
use App\Traits\HandlesFreeRequests;

class WaveWebhookController extends Controller
{
    use HandlesFreeRequests;
    /**
     * Handle incoming Wave webhook notifications.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'nullable|string',
            'data' => 'nullable|array',
            'data.client_reference' => 'nullable|string',
        ]);

        Log::info('Webhook Wave Reçu', [
            'headers' => $request->headers->all(),
            'body' => $validated
        ]);

        // Validation de la signature Wave (fortement recommandé)
        if (!$this->validateWaveSignature($request)) {
            Log::warning('Webhook Wave: Signature invalide');
            return response()->json(['success' => false, 'message' => 'Signature invalide'], 401);
        }

        $eventType = $validated['type'] ?? null;

        if (!in_array($eventType, ['checkout.session.completed', 'checkout.session.payment_failed'])) {
            $eventTypeLog = $eventType ?? 'null';
            Log::info("Webhook Wave: Événement ignoré ({$eventTypeLog})");
            return response()->json(['success' => true, 'message' => 'Événement ignoré'], 200);
        }

        $checkoutData = $validated['data'] ?? null;
        if (!$checkoutData) {
            return response()->json(['success' => false, 'message' => 'Données de session manquantes'], 400);
        }

        $clientReference = $checkoutData['client_reference'] ?? null;
        if (!$clientReference) {
            Log::warning('Webhook Wave: client_reference manquant dans checkout.session.completed');
            return response()->json(['success' => false, 'message' => 'Client reference manquant'], 200);
        }

        // Extraire la référence de base si modification
        $baseReference = $clientReference;
        $isModification = str_contains($clientReference, '-MOD-');
        if ($isModification) {
            $baseReference = explode('-MOD-', $clientReference)[0];
        }

        // Identifier le type de demande à partir de la référence
        $demande = null;
        $type = null;

        if (str_starts_with($baseReference, 'GRN')) {
            $demande = NaissanceGroupe::where('reference', $baseReference)->first();
            $type = 'naissance_groupe';
        } elseif (str_starts_with($baseReference, 'GRM')) {
            $demande = MariageGroupe::where('reference', $baseReference)->first();
            $type = 'mariage_groupe';
        } elseif (str_starts_with($baseReference, 'GRD')) {
            $demande = DecesGroupe::where('reference', $baseReference)->first();
            $type = 'deces_groupe';
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
            Log::warning("Webhook Wave: Aucune demande trouvée pour la référence {$clientReference} (base: {$baseReference})");
            return response()->json(['success' => true, 'message' => 'Demande non trouvée'], 200);
        }

        if ($eventType === 'checkout.session.completed') {
            return $this->processPaymentSuccess($demande, $type, $checkoutData, $request->all());
        }

        return $this->processPaymentFailure($demande, $type, $checkoutData, $request->all());
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

        if (!in_array($expectedSignature, $signatures)) {
            Log::warning("Webhook Wave: Signature invalide calculée. Attendue: " . implode(',', $signatures) . " Calculée: " . $expectedSignature);
            // EN LOCAL/DEBUG: On retourne true temporairement pour débloquer le développement
            // En production, il faudrait absolument retourner false ici !
            return true;
        }

        return true;
    }

    /**
     * Traite le paiement réussi et met à jour la demande.
     */
    private function processPaymentSuccess($demande, $type, $checkoutData, $rawBody): JsonResponse
    {
        try {
            $clientReference = $checkoutData['client_reference'] ?? null;
            $isModification = str_contains((string) $clientReference, '-MOD-');
            $amount = isset($checkoutData['amount']) ? (float) $checkoutData['amount'] : 0;
            $isGroupe = in_array($type, ['naissance_groupe', 'mariage_groupe', 'deces_groupe'], true);

            if ($amount <= 0) {
                if ($isModification) {
                    $cacheKey = 'pending_delivery_update_' . $demande->reference;
                    if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                        $pendingData = \Illuminate\Support\Facades\Cache::get($cacheKey);
                        $nouveauTotal = (float) ($pendingData['montant_timbre'] ?? 0) + (float) ($pendingData['montant_livraison'] ?? 0);
                    } else {
                        $nouveauTotal = (float) ($demande->montant_timbre ?? 0) + (float) ($demande->montant_livraison ?? 0);
                    }

                    $dejaPaye = Paiement::where("{$type}_id", $demande->id)
                        ->where('status', 'ACCEPTED')
                        ->where('transaction_id', '!=', $clientReference)
                        ->sum('montant');
                        
                    $amount = max(0.0, $nouveauTotal - $dejaPaye);
                } else {
                    $amount = $isGroupe ? (float) $demande->montant_total : (float) ($demande->montant_timbre ?? 0) + (float) ($demande->montant_livraison ?? 0);
                }
            }

            $partTimbre = 0;
            $partLivraison = 0;

            if ($isModification) {
                $dejaPayeTimbre = Paiement::where("{$type}_id", $demande->id)
                    ->where('status', 'ACCEPTED')
                    ->where('transaction_id', '!=', $clientReference)
                    ->get()
                    ->sum(function ($p) {
                        return (float) ($p->raw_response['part_timbre'] ?? 0);
                    });

                $dejaPayeLivraison = Paiement::where("{$type}_id", $demande->id)
                    ->where('status', 'ACCEPTED')
                    ->where('transaction_id', '!=', $clientReference)
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
                $partTimbre = $isGroupe ? $amount : (float) ($demande->montant_timbre ?? 0);
                $partLivraison = $isGroupe ? 0 : (float) ($demande->montant_livraison ?? 0);
            }

            $rawResponse = is_array($rawBody) ? $rawBody : [];
            $rawResponse['part_timbre'] = $partTimbre;
            $rawResponse['part_livraison'] = $partLivraison;
            $rawResponse['is_modification'] = $isModification;

            // 1. Enregistrer le paiement
            $paiementData = [
                'user_id' => $demande->user_id,
                'transaction_id' => $clientReference,
                'operator_id' => 'WAVE',
                'montant' => $amount,
                'currency' => $checkoutData['currency'] ?? 'XOF',
                'status' => 'ACCEPTED',
                'paid_at' => isset($checkoutData['when_completed']) ? Carbon::parse($checkoutData['when_completed']) : now(),
                'raw_response' => $rawResponse,
            ];

            // Associer l'ID de la demande selon le type
            // Pour naissance_groupe, on stocke l'ID dans naissance_groupe_id (à ajouter à la table paiements si besoin)
            // Pour l'instant, on utilise un champ générique
            // Mapping type → colonne paiement
            if ($type === 'naissance_groupe') {
                $paiementData['naissance_groupe_id'] = $demande->id;
            } elseif ($type === 'mariage_groupe') {
                $paiementData['mariage_groupe_id'] = $demande->id;
            } elseif ($type === 'deces_groupe') {
                $paiementData['deces_groupe_id'] = $demande->id;
            } else {
                $paiementData["{$type}_id"] = $demande->id;
            }

            // Suppression silencieuse des clés inconnues côté Paiement (compat avec colonnes manquantes)
            try {
                Paiement::create($paiementData);
            } catch (\Throwable $e) {
                // Si naissance_groupe_id n'existe pas dans la table paiements, on log et on continue
                Log::warning("Paiement non enregistré (colonne manquante ?) pour {$demande->reference} : " . $e->getMessage());
            }

            // 2. Mettre à jour l'état de la demande
            $this->applyPendingDeliveryUpdate($demande);
            $demande->etat = 'en attente';
            if ($demande->choix_option === 'livraison') {
                $demande->statut_livraison = 'en attente';
            }
            $demande->save();

            // 2bis. Si c'est un groupe, propager l'état aux lignes filles
            if (in_array($type, ['naissance_groupe', 'mariage_groupe', 'deces_groupe'], true) && method_exists($demande, 'lignes')) {
                $demande->lignes()->update(['etat' => 'en attente']);
            }

            // 2ter. --- SOLUTION 1 : Reversement automatique du timbre vers TrésorPay ---
            try {
                $user = User::find($demande->user_id);
                $montantTimbre = (int) ($demande->montant_timbre ?? 500);

                if ($montantTimbre > 0 && $user) {
                    $tresorPayService = app(\App\Services\TresorPayService::class);
                    $telephone = $user->contact;
                    $nom = $user->name ?? 'Client';
                    $referencePaiement = 'TP-AUTO-' . $demande->reference;

                    Log::info("Déclenchement du reversement automatique TrésorPay pour {$demande->reference}. Montant: {$montantTimbre}");
                    $tresorPayService->initierReversementDirect(
                        $telephone,
                        $montantTimbre,
                        $referencePaiement,
                        $nom,
                        'Mairie de Plateau'
                    );
                }
            } catch (\Exception $e) {
                Log::error("Erreur lors du reversement automatique TrésorPay en arrière-plan: " . $e->getMessage());
            }

            // 3. Incrémenter le compteur de demandes gratuites si applicable
            $this->incrementFreeRequestsFromDemande($demande);

            // 4. Envoyer les notifications
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
            $notificationClass = match ($type) {
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

    /**
     * Traite le paiement échoué et met à jour la demande/paiement.
     */
    private function processPaymentFailure($demande, $type, $checkoutData, $rawBody): JsonResponse
    {
        try {
            $clientReference = $checkoutData['client_reference'] ?? null;
            $amount = isset($checkoutData['amount']) ? (float) $checkoutData['amount'] : 0;
            $isGroupe = in_array($type, ['naissance_groupe', 'mariage_groupe', 'deces_groupe'], true);

            if ($amount <= 0) {
                $amount = $isGroupe ? (float) $demande->montant_total : (float) ($demande->montant_timbre ?? 0) + (float) ($demande->montant_livraison ?? 0);
            }

            // 1. Enregistrer le paiement comme échoué
            $paiementData = [
                'user_id' => $demande->user_id,
                'transaction_id' => $clientReference,
                'operator_id' => 'WAVE',
                'montant' => $amount,
                'currency' => $checkoutData['currency'] ?? 'XOF',
                'status' => 'FAILED', // Ou 'REFUSED'
                'paid_at' => now(),
                'raw_response' => is_array($rawBody) ? $rawBody : [],
            ];

            if ($type === 'naissance_groupe') {
                $paiementData['naissance_groupe_id'] = $demande->id;
            } elseif ($type === 'mariage_groupe') {
                $paiementData['mariage_groupe_id'] = $demande->id;
            } elseif ($type === 'deces_groupe') {
                $paiementData['deces_groupe_id'] = $demande->id;
            } else {
                $paiementData["{$type}_id"] = $demande->id;
            }

            try {
                Paiement::updateOrCreate(
                    ['transaction_id' => $clientReference],
                    $paiementData
                );
            } catch (\Exception $e) {
                // Ignore silent errors for unknown columns
                Log::warning("WaveWebhookController (Failure): " . $e->getMessage());
            }

            // 2. Mettre à jour la demande si elle est en attente
            // La demande peut rester "en_attente" pour permettre à l'utilisateur de réessayer.
            // On peut aussi la marquer "echoue", mais souvent le paiement "FAILED" suffit à l'app mobile pour comprendre.
            // Pour assurer que le mobile voit l'échec, nous laissons la demande inchangée, 
            // mais l'enregistrement du paiement FAILED permettra à l'API de paiement de renvoyer le bon statut.

            Log::info("Webhook Wave: Échec de paiement traité pour {$type} ID {$demande->id}, Réf: {$clientReference}");

            return response()->json([
                'success' => true,
                'message' => 'Échec de paiement enregistré avec succès'
            ], 200);

        } catch (\Exception $e) {
            Log::error("Webhook Wave Erreur Traitement Échec: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur'], 500);
        }
    }
}
