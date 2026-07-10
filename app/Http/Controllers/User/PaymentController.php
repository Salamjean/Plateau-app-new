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
        $validated = $request->validate([
            'reference' => 'nullable|string|max:255',
        ]);
        $reference = $validated['reference'] ?? null;

        Log::info("Page de succès paiement atteinte. Référence: {$reference}");

        if (!$reference) {
            return redirect()->route('user.login')->with('error', 'Référence de paiement manquante.');
        }

        // Identifier le type de demande à partir du préfixe de la référence
        $demande = null;
        $type = null;
        $isGroupe = false;
        $groupeColumn = null;

        // Extraire la référence de base si modification
        $baseReference = $reference;
        $isModification = str_contains($reference, '-MOD-');
        if ($isModification) {
            $baseReference = explode('-MOD-', $reference)[0];
        }

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
            Log::error("Demande introuvable pour la référence: {$reference} (base: {$baseReference})");
            return redirect()->route('user.login')->with('error', 'Référence de demande invalide.');
        }

        Log::info("Demande trouvée: {$type} ID={$demande->id}, user_id={$demande->user_id}");

        // Vérifier si la transaction spécifique est déjà enregistrée
        $paiementColumn = $isGroupe ? $groupeColumn : "{$type}_id";
        $paiement = Paiement::where('transaction_id', $reference)->first();

        // Fallback: Si pas encore enregistré, on le crée manuellement
        if (!$paiement) {
            Log::info("Webhook non encore reçu pour {$reference}. Enregistrement manuel du paiement.");

            // Calcul du montant total selon le type
            if ($isGroupe) {
                $totalAmount = (float) $demande->montant_total;
                $partTimbre = $totalAmount;
                $partLivraison = 0;
            } else {
                if ($isModification) {
                    $cacheKey = 'pending_delivery_update_' . $demande->reference;
                    $nouveauMontantTimbre = 0.0;
                    $nouveauMontantLivraison = 0.0;
                    $hasPendingCache = false;

                    if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                        $pendingData = \Illuminate\Support\Facades\Cache::get($cacheKey);
                        $nouveauMontantTimbre = (float) ($pendingData['montant_timbre'] ?? 0);
                        $nouveauMontantLivraison = (float) ($pendingData['montant_livraison'] ?? 0);
                        $nouveauMontantTotal = $nouveauMontantTimbre + $nouveauMontantLivraison;
                        $hasPendingCache = true;
                    } else {
                        $nouveauMontantTotal = (float) ($demande->montant_timbre ?? 0) + (float) ($demande->montant_livraison ?? 0);
                    }

                    // Somme de tous les paiements déjà ACCEPTED pour ce type d'acte (sans compter la transaction en cours)
                    $dejaPaye = Paiement::where("{$type}_id", $demande->id)
                        ->where('status', 'ACCEPTED')
                        ->where('transaction_id', '!=', $reference)
                        ->sum('montant');

                    $totalAmount = max(0.0, $nouveauMontantTotal - $dejaPaye);

                    // Calcul précis des parts de cette modification
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

                    if ($hasPendingCache) {
                        $partLivraison = max(0.0, $nouveauMontantLivraison - $dejaPayeLivraison);
                        $partTimbre = max(0.0, $totalAmount - $partLivraison);
                    } else {
                        $partTimbre = $totalAmount;
                        $partLivraison = 0.0;
                    }
                } else {
                    $partTimbre = (float) ($demande->montant_timbre ?? 0);
                    $partLivraison = (float) ($demande->montant_livraison ?? 0);
                    $totalAmount = $partTimbre + $partLivraison;
                }
            }

            $operator = 'WAVE';
            if (session()->has('mtn_ref_' . $reference) || session()->has('mtn_ref_' . $baseReference)) {
                $operator = 'MTN';
            }

            try {
                $paiementData = [
                    'user_id' => $demande->user_id,
                    'transaction_id' => $reference,
                    'operator_id' => $operator,
                    'montant' => $totalAmount,
                    'currency' => 'XOF',
                    'status' => 'ACCEPTED',
                    'paid_at' => now(),
                    $paiementColumn => $demande->id,
                    'raw_response' => [
                        'part_timbre' => $partTimbre,
                        'part_livraison' => $partLivraison,
                        'is_modification' => $isModification
                    ]
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
                str_contains($type, 'mariage') => 'mariage',
                str_contains($type, 'deces') => 'décès',
                default => $type,
            };

            $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de {$typeLabel} a été confirmé. Référence : {$demande->reference}. Votre demande est maintenant en attente de traitement par la mairie du plateau.";

            $yellikaSmsService->sendSms($phoneNumber, $message);

            // Envoi email
            $notificationClass = match (true) {
                str_contains($type, 'naissance') => DemandeNaissanceConfirmationNotification::class,
                str_contains($type, 'mariage') => DemandeMariageConfirmationNotification::class,
                str_contains($type, 'deces') => DemandeDecesConfirmationNotification::class,
                default => null,
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
        $validated = $request->validate([
            'reference' => 'required|string|max:255',
        ]);

        $reference = $validated['reference'];

        Log::info("Page d'annulation paiement atteinte. Référence: {$reference}");

        return view('user.payment.cancel', [
            'reference' => $reference
        ]);
    }

    public function mtnWaiting(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:255',
            'type'      => 'nullable|string|max:50',
        ]);
        $reference = $validated['reference'] ?? null;
        $type = $validated['type'] ?? null;

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
        $validated = $request->validate([
            'reference' => 'nullable|string|max:255',
            'type'      => 'nullable|string|max:50',
            'mtn_ref'   => 'nullable|string|max:255',
        ]);
        $reference = $validated['reference'] ?? null;
        $type = $validated['type'] ?? null;
        $mtnRef = $validated['mtn_ref'] ?? null;

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
                // Extraire la référence de base si modification
                $baseReference = $reference;
                $isModification = str_contains($reference, '-MOD-');
                if ($isModification) {
                    $baseReference = explode('-MOD-', $reference)[0];
                }

                // Find demande 
                $demande = null;
                $isGroupe = false;
                $groupeColumn = null;

                if ($type === 'naissance') {
                    $demande = Naissance::where('reference', $baseReference)->first();
                } elseif ($type === 'mariage') {
                    $demande = Mariage::where('reference', $baseReference)->first();
                } elseif ($type === 'deces') {
                    $demande = Deces::where('reference', $baseReference)->first();
                } elseif ($type === 'naissance_groupe') {
                    $demande = NaissanceGroupe::where('reference', $baseReference)->first();
                    $isGroupe = true;
                    $groupeColumn = 'naissance_groupe_id';
                } elseif ($type === 'mariage_groupe') {
                    $demande = MariageGroupe::where('reference', $baseReference)->first();
                    $isGroupe = true;
                    $groupeColumn = 'mariage_groupe_id';
                } elseif ($type === 'deces_groupe') {
                    $demande = DecesGroupe::where('reference', $baseReference)->first();
                    $isGroupe = true;
                    $groupeColumn = 'deces_groupe_id';
                }

                if ($demande) {
                    $partTimbre = 0;
                    $partLivraison = 0;

                    if ($isGroupe) {
                        $defaultAmount = (float) $demande->montant_total;
                        $partTimbre = $defaultAmount;
                        $partLivraison = 0.0;
                    } else {
                        $montantTimbre = (float) ($demande->montant_timbre ?? 0);
                        $montantLivraison = (float) ($demande->montant_livraison ?? 0);

                        if ($isModification) {
                            $cacheKey = 'pending_delivery_update_' . $demande->reference;
                            if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                                $pendingData = \Illuminate\Support\Facades\Cache::get($cacheKey);
                                $nouveauMontantTimbre = (float) ($pendingData['montant_timbre'] ?? 0);
                                $nouveauMontantLivraison = (float) ($pendingData['montant_livraison'] ?? 0);
                                $nouveauMontantTotal = $nouveauMontantTimbre + $nouveauMontantLivraison;
                            } else {
                                $nouveauMontantTotal = $montantTimbre + $montantLivraison;
                            }

                            $dejaPaye = Paiement::where("{$type}_id", $demande->id)
                                ->where('status', 'ACCEPTED')
                                ->where('transaction_id', '!=', $reference)
                                ->sum('montant');

                            $defaultAmount = max(0.0, $nouveauMontantTotal - $dejaPaye);
                        } else {
                            $defaultAmount = $montantTimbre + $montantLivraison;
                        }
                    }

                    $amount = isset($statusInfo['amount']) ? (float) $statusInfo['amount'] : $defaultAmount;

                    if (!$isGroupe) {
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
                    }

                    $paiementColumn = $isGroupe ? $groupeColumn : "{$type}_id";

                    Paiement::create([
                        'user_id' => $demande->user_id,
                        'transaction_id' => $reference,
                        'operator_id' => 'MTN',
                        'montant' => $amount,
                        'currency' => $statusInfo['currency'] ?? 'XOF',
                        'status' => 'ACCEPTED',
                        'paid_at' => now(),
                        $paiementColumn => $demande->id,
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

                    // Pour un groupe : propager l'état aux lignes filles
                    if ($isGroupe && method_exists($demande, 'lignes')) {
                        $demande->lignes()->update(['etat' => 'en attente']);
                    }

                    // Incrémenter le compteur de demandes gratuites si applicable
                    $this->incrementFreeRequestsFromDemande($demande);

                    // ✅ Envoi SMS + email de confirmation (paiement MTN confirmed via polling)
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

    public function tresorpayWaiting(Request $request)
    {
        $reference = $request->query('reference');
        $type = $request->query('type');

        if (!$reference) {
            return redirect()->route('user.extrait.index')->with('error', 'Référence TrésorPay introuvable.');
        }

        return view('user.payment.tresorpay-waiting', [
            'reference' => $reference,
            'type' => $type
        ]);
    }

    public function tresorpayCheck(Request $request)
    {
        $reference = $request->input('reference');
        $type = $request->input('type');

        // On cherche si un paiement TrésorPay accepté existe en base pour cette transaction (créé par le Webhook)
        $paiement = Paiement::where('transaction_id', $reference)
            ->where('operator_id', 'TRESORPAY')
            ->first();
            
        Log::info("tresorpayCheck appelé pour la référence: {$reference}. Paiement trouvé: " . ($paiement ? 'OUI' : 'NON') . ". Status: " . ($paiement ? $paiement->status : 'N/A'));

        if ($paiement && $paiement->status === 'ACCEPTED') {
            return response()->json(['status' => 'SUCCESSFUL', 'redirect' => route('payment.success', ['reference' => $reference, 'type' => $type])]);
        }
        
        if ($paiement && $paiement->status === 'FAILED') {
            return response()->json(['status' => 'FAILED', 'redirect' => route('payment.cancel', ['reference' => $reference, 'type' => $type])]);
        }

        return response()->json(['status' => 'PENDING']);
    }
}
