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
     * Calcule le nombre de demandes gratuites restantes pour un utilisateur
     * 
     * @param \App\Models\User $user
     * @return int
     */
    protected function getRemainingFreeRequests($user): int
    {
        if (!MaintenanceSetting::isFreeRequestsModeActive()) {
            return 0;
        }

        return max(0, 2 - $user->free_requests_used);
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
     * 
     * @param \App\Models\User $user
     * @param int $count Nombre de demandes gratuites utilisées
     */
    protected function incrementFreeRequestsUsed($user, int $count = 1): void
    {
        $user->free_requests_used = min($user->free_requests_used + $count, 2);
        $user->save();
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
}
