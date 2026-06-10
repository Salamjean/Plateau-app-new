<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\DecesGroupe;
use App\Models\Mariage;
use App\Models\MariageGroupe;
use App\Models\Naissance;
use App\Models\NaissanceGroupe;
use App\Models\Paiement;
use App\Models\User;
use App\Notifications\DemandeNaissanceConfirmationNotification;
use App\Notifications\DemandeMariageConfirmationNotification;
use App\Notifications\DemandeDecesConfirmationNotification;
use App\Services\WaveService;
use App\Services\YellikaSmsService;
use App\Traits\HandlesFreeRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PaymentController extends Controller
{
    use HandlesFreeRequests;
    public function success(Request $request)
    {
        $reference = $request->query('reference');

        Log::info("Page de succès paiement atteinte. Référence: {$reference}");

        if (!$reference) {
            return redirect()->route('user.login')->with('error', 'Référence de paiement manquante.');
        }

        // Identifier le type de demande à partir du préfixe de la référence
        $demande = null;
        $type = null;
        $isGroupe = false;
        $groupeColumn = null;

        if (str_starts_with($reference, 'GRN')) {
            $demande = NaissanceGroupe::where('reference', $reference)->first();
            $type = 'naissance_groupe';
            $isGroupe = true;
            $groupeColumn = 'naissance_groupe_id';
        } elseif (str_starts_with($reference, 'GRM')) {
            $demande = MariageGroupe::where('reference', $reference)->first();
            $type = 'mariage_groupe';
            $isGroupe = true;
            $groupeColumn = 'mariage_groupe_id';
        } elseif (str_starts_with($reference, 'GRD')) {
            $demande = DecesGroupe::where('reference', $reference)->first();
            $type = 'deces_groupe';
            $isGroupe = true;
            $groupeColumn = 'deces_groupe_id';
        } elseif (str_starts_with($reference, 'AN')) {
            $demande = Naissance::where('reference', $reference)->first();
            $type = 'naissance';
        } elseif (str_starts_with($reference, 'AM')) {
            $demande = Mariage::where('reference', $reference)->first();
            $type = 'mariage';
        } elseif (str_starts_with($reference, 'AD')) {
            $demande = Deces::where('reference', $reference)->first();
            $type = 'deces';
        }

        if (!$demande) {
            Log::error("Demande introuvable pour la référence: {$reference}");
            return redirect()->route('user.login')->with('error', 'Référence de demande invalide.');
        }

        Log::info("Demande trouvée: {$type} ID={$demande->id}, user_id={$demande->user_id}");

        // Vérifier si le paiement est déjà enregistré (par le webhook)
        $paiementColumn = $isGroupe ? $groupeColumn : "{$type}_id";
        $paiement = Paiement::where($paiementColumn, $demande->id)->first();

        // Fallback: Si pas encore enregistré, on le crée manuellement
        if (!$paiement) {
            Log::info("Webhook non encore reçu pour {$reference}. Enregistrement manuel du paiement.");

            // Calcul du montant total selon le type
            if ($isGroupe) {
                $totalAmount = (float) $demande->montant_total;
            } else {
                $montantTimbre = (float) ($demande->montant_timbre ?? 0);
                $quantite = (int) ($demande->quantite ?? 1);
                $montantLivraison = (float) ($demande->montant_livraison ?? 0);
                $totalAmount = ($montantTimbre * $quantite) + $montantLivraison;
            }

            try {
                $paiementData = [
                    'user_id' => $demande->user_id,
                    'transaction_id' => $demande->reference,
                    'operator_id' => 'WAVE',
                    'montant' => $totalAmount,
                    'currency' => 'XOF',
                    'status' => 'ACCEPTED',
                    'paid_at' => now(),
                    $paiementColumn => $demande->id,
                ];
                $paiement = Paiement::create($paiementData);
                Log::info("Paiement enregistré avec succès. ID: {$paiement->id}");
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'enregistrement du paiement: " . $e->getMessage());
            }

            // Mettre à jour l'état de la demande
            $this->applyPendingDeliveryUpdate($demande);
            $demande->etat = 'en attente';
            if ($demande->choix_option === 'livraison') {
                $demande->statut_livraison = 'en attente';
            }
            $demande->save();

            // Pour un groupe : propager l'état aux lignes filles
            if ($isGroupe && method_exists($demande, 'lignes')) {
                $demande->lignes()->update(['etat' => 'en attente']);
            }

            // --- SOLUTION 1 : Reversement automatique du timbre vers TrésorPay ---
            try {
                $user = User::find($demande->user_id);
                $montantTimbre = (int) ($demande->montant_timbre ?? 500);
                
                if ($montantTimbre > 0 && $user) {
                    $tresorPayService = app(\App\Services\TresorPayService::class);
                    $telephone = $user->contact;
                    $nom = $user->name ?? 'Client';
                    $referencePaiement = 'TP-AUTO-' . $demande->reference;
                    
                    Log::info("Déclenchement du reversement automatique TrésorPay pour {$demande->reference} (Fallback). Montant: {$montantTimbre}");
                    $tresorPayService->initierReversementDirect(
                        $telephone,
                        $montantTimbre,
                        $referencePaiement,
                        $nom,
                        'Mairie de Plateau'
                    );
                }
            } catch (\Exception $e) {
                Log::error("Erreur lors du reversement automatique TrésorPay en arrière-plan (Fallback): " . $e->getMessage());
            }

            // Incrémenter le compteur de demandes gratuites si applicable
            $this->incrementFreeRequestsFromDemande($demande);

            // ✅ Envoi SMS + email de confirmation
            //    (uniquement dans le fallback car le webhook s'en charge déjà s'il arrive en premier)
            $user = User::find($demande->user_id);
            if ($user) {
                $this->sendPaymentNotifications($user, $demande, $type);
            }
        } else {
            Log::info("Paiement déjà enregistré (webhook reçu). Paiement ID: {$paiement->id}");
        }

        return view('user.payment.success', [
            'demande' => $demande,
            'type' => $type,
            'reference' => $reference
        ]);
    }

    /**
     * Envoie SMS + email de confirmation après paiement (fallback web).
     * N'est appelé que si le webhook n'a pas encore reçu/traité.
     */
    private function sendPaymentNotifications($user, $demande, $type)
    {
        try {
            $yellikaSmsService = app(YellikaSmsService::class);
            $phoneNumber = $user->indicatif . $user->contact;

            // Normaliser le type pour les groupes
            $typeLabel = match (true) {
                str_contains($type, 'naissance') => 'naissance',
                str_contains($type, 'mariage')   => 'mariage',
                str_contains($type, 'deces')     => 'décès',
                default                          => $type,
            };

            $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de {$typeLabel} a été confirmé. Référence : {$demande->reference}. Votre demande est maintenant en attente de traitement par la mairie du plateau.";

            $yellikaSmsService->sendSms($phoneNumber, $message);

            // Envoi email
            $notificationClass = match (true) {
                str_contains($type, 'naissance') => DemandeNaissanceConfirmationNotification::class,
                str_contains($type, 'mariage')   => DemandeMariageConfirmationNotification::class,
                str_contains($type, 'deces')     => DemandeDecesConfirmationNotification::class,
                default                          => null,
            };

            if ($notificationClass) {
                Notification::send($user, new $notificationClass($user, $demande));
            }

            Log::info("PaymentController fallback: SMS + email envoyés pour {$demande->reference}");

        } catch (\Exception $e) {
            Log::error("Erreur notifications fallback PaymentController pour {$demande->reference}: " . $e->getMessage());
        }
    }

    public function cancel(Request $request)
    {
        $reference = $request->query('reference');
        
        Log::info("Page d'annulation paiement atteinte. Référence: {$reference}");

        return view('user.payment.cancel', [
            'reference' => $reference
        ]);
    }

    public function mtnWaiting(Request $request)
    {
        $reference = $request->query('reference');
        $type = $request->query('type');
        
        $mtnRef = session('mtn_ref_' . $reference);

        if (!$mtnRef) {
            return redirect()->route('user.extrait.index')->with('error', 'Référence MTN introuvable ou session expirée.');
        }

        return view('user.payment.mtn-waiting', [
            'reference' => $reference,
            'type' => $type,
            'mtnRef' => $mtnRef
        ]);
    }

    public function mtnCheck(Request $request)
    {
        $reference = $request->input('reference');
        $type = $request->input('type');
        $mtnRef = $request->input('mtn_ref');

        if (!$mtnRef) {
            return response()->json(['status' => 'ERROR', 'message' => 'No MTN ID provided']);
        }

        $mtnService = new \App\Services\MtnService();
        $statusInfo = $mtnService->getTransactionStatus($mtnRef);

        if (!$statusInfo) {
            return response()->json(['status' => 'PENDING', 'message' => 'Pending or API disconnected.']);
        }

        $status = $statusInfo['status'] ?? 'PENDING';

        if ($status === 'SUCCESSFUL') {
            // Check if already processed
            $paiement = Paiement::where('transaction_id', $reference)->where('operator_id', 'MTN')->first();
            if (!$paiement) {
                // Find demande 
                $demande = null;
                if ($type === 'naissance') {
                    $demande = Naissance::where('reference', $reference)->first();
                } elseif ($type === 'mariage') {
                    $demande = Mariage::where('reference', $reference)->first();
                } elseif ($type === 'deces') {
                    $demande = Deces::where('reference', $reference)->first();
                }

                if ($demande) {
                    $montantTimbre = (float) ($demande->montant_timbre ?? 0);
                    $quantite = (int) ($demande->quantite ?? 1);
                    $montantLivraison = (float) ($demande->montant_livraison ?? 0);
                    // The timbre total is already stored in montant_timbre if logic is correctly updated
                    // However, we just use the API's amount if available from $statusInfo['amount']
                    $amount = isset($statusInfo['amount']) ? (float)$statusInfo['amount'] : (($montantTimbre * $quantite) + $montantLivraison);

                    Paiement::create([
                        'user_id' => $demande->user_id,
                        'transaction_id' => $reference,
                        'operator_id' => 'MTN',
                        'montant' => $amount,
                        'currency' => $statusInfo['currency'] ?? 'XOF',
                        'status' => 'ACCEPTED',
                        'paid_at' => now(),
                        "{$type}_id" => $demande->id,
                    ]);

                    $this->applyPendingDeliveryUpdate($demande);
                    $demande->etat = 'en attente';
                    if ($demande->choix_option === 'livraison') {
                        $demande->statut_livraison = 'en attente';
                    }
                    $demande->save();

                    // Incrémenter le compteur de demandes gratuites si applicable
                    $this->incrementFreeRequestsFromDemande($demande);

                    // ✅ Envoi SMS + email de confirmation (paiement MTN confirmé via polling)
                    $user = User::find($demande->user_id);
                    if ($user) {
                        $this->sendPaymentNotifications($user, $demande, $type);
                    }
                }
            }
            return response()->json(['status' => 'SUCCESSFUL', 'redirect' => route('payment.success', ['reference' => $reference, 'type' => $type])]);
        } elseif ($status === 'FAILED') {
            return response()->json(['status' => 'FAILED', 'redirect' => route('payment.cancel', ['reference' => $reference, 'type' => $type])]);
        }

        return response()->json(['status' => $status]); // PENDING
    }
}
