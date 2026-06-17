<?php

namespace App\Traits;

use App\Models\Deces;
use App\Models\MaintenanceSetting;
use App\Models\Mariage;
use App\Models\Naissance;

trait HandlesFreeRequests
{
    /**
     * Vérifie si la demande actuelle est une demande gratuite (mode test)
     * et met à jour le compteur de l'utilisateur si c'est le cas.
     * 
     * @param \App\Models\User $user
     * @return bool true si la demande est gratuite
     */
    protected function isFreeRequest($user): bool
    {
        // Vérifier si le mode demandes gratuites est activé
        if (!MaintenanceSetting::isFreeRequestsModeActive()) {
            return false;
        }

        // Vérifier si l'utilisateur a encore des demandes gratuites disponibles
        return $user->free_requests_used < 2;
    }

    /**
     * Calcule le nombre de demandes gratuites restantes pour un utilisateur.
     * Source de vérité unique : le champ free_requests_used du User.
     *
     * @param \App\Models\User $user
     * @return int
     */
    protected function getRemainingFreeRequests($user): int
    {
        if (!MaintenanceSetting::isFreeRequestsModeActive()) {
            return 0;
        }

        return max(0, 2 - (int) $user->free_requests_used);
    }

    /**
     * Traite une demande en tenant compte des demandes gratuites.
     * Pour une demande avec livraison et quantité N :
     * - Si l'utilisateur a 2 demandes gratuites restantes et quantité >= 2 : 2 timbres gratuits
     * - Si l'utilisateur a 1 demande gratuite restante : 1 timbre gratuit
     * - Les timbres restants sont payés normalement
     * 
     * @param \App\Models\User $user
     * @param int $quantite
     * @return array ['free_timbres' => int, 'paid_timbres' => int, 'montant_timbre_total' => float, 'montant_timbre_gratuit' => float]
     */
    protected function calculateFreeRequestsDiscount($user, int $quantite): array
    {
        $montantTimbreUnitaire = 500;
        $freeRemaining = $this->getRemainingFreeRequests($user);

        if ($freeRemaining <= 0) {
            return [
                'free_timbres' => 0,
                'paid_timbres' => $quantite,
                'montant_timbre_total' => $quantite * $montantTimbreUnitaire,
                'montant_timbre_gratuit' => 0,
                'is_free' => false,
            ];
        }

        // Les timbres gratuits = min(demandes gratuites restantes, quantité demandée)
        $freeTimbres = min($freeRemaining, $quantite);
        $paidTimbres = $quantite - $freeTimbres;

        return [
            'free_timbres' => $freeTimbres,
            'paid_timbres' => $paidTimbres,
            'montant_timbre_total' => $paidTimbres * $montantTimbreUnitaire,
            'montant_timbre_gratuit' => $freeTimbres * $montantTimbreUnitaire,
            'is_free' => $paidTimbres == 0,
        ];
    }

    /**
     * Incrémente le compteur de demandes gratuites utilisées
     */
    protected function incrementFreeRequestsUsed($user, int $count = 1): void
    {
        $user->free_requests_used = min($user->free_requests_used + $count, 2);
        $user->save();
    }

    /**
     * Incrémente le compteur après confirmation du paiement d'une demande.
     * À appeler dans les webhooks / handlers de succès de paiement.
     * Sans effet si la demande n'a pas de timbres gratuits.
     *
     * @param \App\Models\Naissance|\App\Models\Mariage|\App\Models\Deces $demande
     */
    protected function incrementFreeRequestsFromDemande($demande): void
    {
        if (empty($demande->is_free_request) || empty($demande->free_timbres_count)) {
            return;
        }

        $user = \App\Models\User::find($demande->user_id);
        if (!$user) {
            return;
        }

        $this->incrementFreeRequestsUsed($user, (int) $demande->free_timbres_count);

        \Illuminate\Support\Facades\Log::info("FreeRequests incrément après paiement [{$demande->reference}]: +{$demande->free_timbres_count} (total: {$user->free_requests_used})");
    }

    /**
     * Compte le nombre total de demandes existantes d'un utilisateur
     * (toutes les demandes, peu importe le statut)
     * 
     * @param int $userId
     * @return int
     */
    protected function getTotalDemandesCount(int $userId): int
    {
        $naissance = Naissance::where('user_id', $userId)->count();
        $deces = Deces::where('user_id', $userId)->count();
        $mariage = Mariage::where('user_id', $userId)->count();

        return $naissance + $deces + $mariage;
    }

    /**
     * Applique les modifications d'informations en attente pour une demande payée avec succès.
     *
     * @param \App\Models\Naissance|\App\Models\Mariage|\App\Models\Deces $demande
     */
    protected function applyPendingModificationUpdate($demande): void
    {
        $cacheKey = 'pending_modification_update_' . $demande->reference;
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $pendingData = \Illuminate\Support\Facades\Cache::get($cacheKey);
            \Illuminate\Support\Facades\Log::info("Application des modifications d'informations en attente pour {$demande->reference} : ", $pendingData);

            // 1. Suppression physique des anciens fichiers remplacés
            if (isset($pendingData['old_files_to_delete']) && is_array($pendingData['old_files_to_delete'])) {
                foreach ($pendingData['old_files_to_delete'] as $filePath) {
                    if ($filePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($filePath);
                    }
                }
            }

            // 2. Application des nouveaux champs/attributs
            if (isset($pendingData['attributes']) && is_array($pendingData['attributes'])) {
                foreach ($pendingData['attributes'] as $key => $value) {
                    $demande->$key = $value;
                }
            }

            $demande->save();
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }
    }

    /**
     * Applique les modifications de livraison en attente pour une demande payée avec succès.
     *
     * @param \App\Models\Naissance|\App\Models\Mariage|\App\Models\Deces $demande
     */
    protected function applyPendingDeliveryUpdate($demande): void
    {
        $this->applyPendingModificationUpdate($demande);

        $cacheKey = 'pending_delivery_update_' . $demande->reference;
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $pendingData = \Illuminate\Support\Facades\Cache::get($cacheKey);
            \Illuminate\Support\Facades\Log::info("Application des données de livraison/retrait en attente pour {$demande->reference} : ", $pendingData);

            $demande->choix_option = $pendingData['choix_option'] ?? 'livraison';
            if (isset($pendingData['montant_timbre'])) {
                $demande->montant_timbre = $pendingData['montant_timbre'];
            }
            if (isset($pendingData['montant_livraison'])) {
                $demande->montant_livraison = $pendingData['montant_livraison'];
            }

            if ($demande->choix_option === 'livraison') {
                if (isset($pendingData['nom_destinataire'])) {
                    $demande->nom_destinataire = $pendingData['nom_destinataire'];
                }
                if (isset($pendingData['prenom_destinataire'])) {
                    $demande->prenom_destinataire = $pendingData['prenom_destinataire'];
                }
                if (isset($pendingData['email_destinataire'])) {
                    $demande->email_destinataire = $pendingData['email_destinataire'];
                }
                if (isset($pendingData['contact_destinataire'])) {
                    $demande->contact_destinataire = $pendingData['contact_destinataire'];
                }
                if (isset($pendingData['adresse_livraison'])) {
                    $demande->adresse_livraison = $pendingData['adresse_livraison'];
                }
                if (isset($pendingData['code_postal'])) {
                    $demande->code_postal = $pendingData['code_postal'];
                }
                if (isset($pendingData['ville'])) {
                    $demande->ville = $pendingData['ville'];
                }
                if (isset($pendingData['commune_livraison'])) {
                    $demande->commune_livraison = $pendingData['commune_livraison'];
                }
                if (isset($pendingData['quartier'])) {
                    $demande->quartier = $pendingData['quartier'];
                }
                if (isset($pendingData['date_livraison'])) {
                    $demande->date_livraison = $pendingData['date_livraison'];
                }
                if (isset($pendingData['heure_livraison'])) {
                    $demande->heure_livraison = $pendingData['heure_livraison'];
                }

                $demande->statut_livraison = 'en attente';
            } else {
                $demande->nom_destinataire = null;
                $demande->prenom_destinataire = null;
                $demande->email_destinataire = null;
                $demande->contact_destinataire = null;
                $demande->adresse_livraison = null;
                $demande->code_postal = null;
                $demande->ville = null;
                $demande->commune_livraison = null;
                $demande->quartier = null;
                $demande->date_livraison = null;
                $demande->heure_livraison = null;
                $demande->statut_livraison = null;
            }

            $demande->save();

            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }
    }
}
