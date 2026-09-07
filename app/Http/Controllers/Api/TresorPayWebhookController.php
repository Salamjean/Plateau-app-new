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
use App\Services\YellikaSmsService;
use App\Notifications\DemandeNaissanceConfirmationNotification;
use App\Notifications\DemandeMariageConfirmationNotification;
use App\Notifications\DemandeDecesConfirmationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Traits\HandlesFreeRequests;

class TresorPayWebhookController extends Controller
{
    use HandlesFreeRequests;
    public function handle(Request $request)
    {
        Log::info('Webhook TrésorPay reçu', $request->all());

        $codePaiement = $request->input('codePaiement') ?? $request->input('code_paiement') ?? $request->input('transaction_id');
        $statutCode = $request->input('code');
        $statutStr = $request->input('status') ?? $request->input('statut'); // Vérifier la clé exacte
        $montant = $request->input('montant') ?? $request->input('Montant') ?? 0;
        
        // TrésorPay envoie généralement code=200 ou code=0 ou status=SUCCESS
        $isSuccess = ($statutCode == '200' || $statutCode == '0' || $statutStr === 'SUCCESS' || $statutStr === 'ACCEPTED');

        if (!$codePaiement) {
            return response()->json(['error' => 'Code de paiement manquant'], 400);
        }

        // Identifier la demande
        $demande = null;
        $type = 'inconnu';
        $isGroupe = false;
        $paiementColumn = null;

        $baseReference = $codePaiement;
        $isModification = str_contains($codePaiement, '-MOD-');
        if ($isModification) {
            $baseReference = explode('-MOD-', $codePaiement)[0];
        }

        if (str_starts_with($baseReference, 'GRN')) {
            $demande = NaissanceGroupe::where('reference', $baseReference)->first();
            $type = 'naissance_groupe';
            $isGroupe = true;
            $paiementColumn = 'naissance_groupe_id';
        } elseif (str_starts_with($baseReference, 'GRM')) {
            $demande = MariageGroupe::where('reference', $baseReference)->first();
            $type = 'mariage_groupe';
            $isGroupe = true;
            $paiementColumn = 'mariage_groupe_id';
        } elseif (str_starts_with($baseReference, 'GRD')) {
            $demande = DecesGroupe::where('reference', $baseReference)->first();
            $type = 'deces_groupe';
            $isGroupe = true;
            $paiementColumn = 'deces_groupe_id';
        } elseif (str_starts_with($baseReference, 'AN')) {
            $demande = Naissance::where('reference', $baseReference)->first();
            $type = 'naissance';
            $paiementColumn = 'naissance_id';
        } elseif (str_starts_with($baseReference, 'AM')) {
            $demande = Mariage::where('reference', $baseReference)->first();
            $type = 'mariage';
            $paiementColumn = 'mariage_id';
        } elseif (str_starts_with($baseReference, 'AD')) {
            $demande = Deces::where('reference', $baseReference)->first();
            $type = 'deces';
            $paiementColumn = 'deces_id';
        }

        if (!$demande) {
            Log::warning("Demande introuvable pour le code_paiement: {$codePaiement}");
            // On continue pour enregistrer le paiement si possible, bien qu'orphelin
        }

        // On cherche le paiement existant
        $paiement = Paiement::where('transaction_id', $codePaiement)->first();

        if ($paiement) {
            if ($paiement->status === 'ACCEPTED') {
                return response()->json(['message' => 'Déjà traité'], 200);
            }
            $paiement->status = $isSuccess ? 'ACCEPTED' : 'FAILED';
            $paiement->raw_response = array_merge((array)$paiement->raw_response, $request->all());
            $paiement->save();
        } else {
            // Création du paiement si inexistant
            if ($demande) {
                if ($isModification) {
                    $dejaPayeTimbre = Paiement::where("{$type}_id", $demande->id)
                        ->where('status', 'ACCEPTED')
                        ->where('transaction_id', '!=', $codePaiement)
                        ->get()
                        ->sum(function ($p) {
                            return (float) ($p->raw_response['part_timbre'] ?? 0);
                        });

                    $dejaPayeLivraison = Paiement::where("{$type}_id", $demande->id)
                        ->where('status', 'ACCEPTED')
                        ->where('transaction_id', '!=', $codePaiement)
                        ->get()
                        ->sum(function ($p) {
                            return (float) ($p->raw_response['part_livraison'] ?? 0);
                        });

                    $dejaPaye = Paiement::where("{$type}_id", $demande->id)
                        ->where('status', 'ACCEPTED')
                        ->where('transaction_id', '!=', $codePaiement)
                        ->sum('montant');

                    $cacheKey = 'pending_delivery_update_' . $demande->reference;
                    if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                        $pendingData = \Illuminate\Support\Facades\Cache::get($cacheKey);
                        $nouveauTotal = (float) ($pendingData['montant_timbre'] ?? 0) + (float) ($pendingData['montant_livraison'] ?? 0);
                        $nouveauMontantLivraison = (float) ($pendingData['montant_livraison'] ?? 0);
                    } else {
                        $nouveauTotal = (float) ($demande->montant_timbre ?? 0) + (float) ($demande->montant_livraison ?? 0);
                        $nouveauMontantLivraison = (float) ($demande->montant_livraison ?? 0);
                    }
                    
                    if ($montant <= 0) {
                        $montant = max(0.0, $nouveauTotal - $dejaPaye);
                    }

                    $partLivraison = max(0.0, $nouveauMontantLivraison - $dejaPayeLivraison);
                    $partTimbre = max(0.0, $montant - $partLivraison);
                } else {
                    if ($montant <= 0) {
                        $montant = $isGroupe ? (float) $demande->montant_total : (float) ($demande->montant_timbre ?? 0) + (float) ($demande->montant_livraison ?? 0);
                    }
                    $partTimbre = (float) ($demande->montant_timbre ?? 0);
                    $partLivraison = (float) ($demande->montant_livraison ?? 0);

                    if ($isGroupe) {
                        $partTimbre = (float) $demande->montant_total;
                        $partLivraison = 0.0;
                    }
                }

                Paiement::create([
                    'user_id' => $demande->user_id,
                    'transaction_id' => $codePaiement,
                    'operator_id' => 'TRESORPAY',
                    'montant' => $montant > 0 ? $montant : ($partTimbre + $partLivraison),
                    'currency' => 'XOF',
                    'status' => $isSuccess ? 'ACCEPTED' : 'FAILED',
                    'paid_at' => $isSuccess ? now() : null,
                    $paiementColumn => $demande->id,
                    'raw_response' => [
                        'webhook_data' => $request->all(),
                        'part_timbre' => $partTimbre,
                        'part_livraison' => $partLivraison,
                        'is_modification' => $isModification,
                    ]
                ]);
            }
        }

        // Mettre à jour l'état de la demande si c'est un succès
        if ($isSuccess && $demande) {
            $this->applyPendingDeliveryUpdate($demande);
            $demande->etat = 'en attente';
            if ($demande->choix_option === 'livraison') {
                $demande->statut_livraison = 'en attente';
            }
            $demande->save();
            
            // Si c'est un groupe, propager l'état aux lignes filles
            if ($isGroupe && method_exists($demande, 'lignes')) {
                $demande->lignes()->update(['etat' => 'en attente']);
            }
            Log::info("Etat de la demande {$codePaiement} mis à jour à 'en attente'.");

            // Envoyer la notification SMS
            $user = User::find($demande->user_id);
            if ($user) {
                try {
                    $yellikaSmsService = app(YellikaSmsService::class);
                    $phoneNumber = $user->indicatif . $user->contact;
                    $typeLabel = str_replace('_groupe', '', $type);
                    $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de {$typeLabel} a été confirmé. Référence : {$demande->reference}. Votre demande est maintenant en attente de traitement par la mairie du plateau.";
                    $yellikaSmsService->sendSms($phoneNumber, $message);

                    $notificationClass = match ($typeLabel) {
                        'naissance' => DemandeNaissanceConfirmationNotification::class,
                        'mariage' => DemandeMariageConfirmationNotification::class,
                        'deces' => DemandeDecesConfirmationNotification::class,
                        default => null,
                    };
                    if ($notificationClass) {
                        Notification::send($user, new $notificationClass($user, $demande));
                    }

                    // Push Notification Firebase pour la validation
                    if ($user->push_token) {
                        $titre = "Paiement Validé";
                        $body = "Votre paiement pour la demande {$demande->reference} a été validé avec succès.";
                        app(\App\Http\Controllers\Api\Utilisateurs\NotificationController::class)->sendPushNotification($user->push_token, $titre, $body);
                    }
                } catch (\Exception $e) {
                    Log::error("Erreur notifications Webhook TrésorPay: " . $e->getMessage());
                }
            }
        }

        return response()->json(['message' => 'Webhook traité avec succès'], 200);
    }
}
