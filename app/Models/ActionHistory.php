<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActionHistory extends Model
{
    protected $table = 'action_histories';

    protected $fillable = [
        'agent_id',
        'demande_type',
        'demande_id',
        'reference',
        'action',
        'ancien_etat',
        'nouvel_etat',
        'motif',
        'champs_modifies',
        'details',
    ];

    protected $casts = [
        'champs_modifies' => 'array',
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    // =========================================================================
    // MÉTHODES STATIQUES
    // =========================================================================

    /**
     * Enregistrer une action dans l'historique
     */
    public static function logAction(
        string $demandeType,
        int $demandeId,
        ?string $reference,
        string $action,
        ?string $ancienEtat = null,
        ?string $nouvelEtat = null,
        ?string $motif = null,
        ?array $champsModifies = null,
        ?string $details = null
    ) {
        $agentId = Auth::guard('agent')->id();

        if (!$agentId) {
            return null;
        }

        return self::create([
            'agent_id' => $agentId,
            'demande_type' => $demandeType,
            'demande_id' => $demandeId,
            'reference' => $reference,
            'action' => $action,
            'ancien_etat' => $ancienEtat,
            'nouvel_etat' => $nouvelEtat,
            'motif' => $motif,
            'champs_modifies' => $champsModifies,
            'details' => $details,
        ]);
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    /**
     * Obtenir le libellé de l'action
     */
    public function getActionLabelAttribute(): string
    {
        $labels = [
            'changement_etat' => 'Changement d\'état',
            'rejet' => 'Rejet de demande',
            'recuperation' => 'Récupération de demande',
            'livraison' => 'Marquée comme livrée',
            'annulation' => 'Annulation',
        ];

        return $labels[$this->action] ?? $this->action;
    }

    /**
     * Obtenir le libellé du type de demande
     */
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'naissance' => 'Extrait de Naissance',
            'mariage' => 'Extrait de Mariage',
            'deces' => 'Extrait de Décès',
        ];

        return $labels[$this->demande_type] ?? $this->demande_type;
    }

    /**
     * Obtenir la couleur du badge selon l'action
     */
    public function getActionColorAttribute(): string
    {
        $colors = [
            'changement_etat' => 'primary',
            'rejet' => 'danger',
            'recuperation' => 'info',
            'livraison' => 'success',
            'annulation' => 'warning',
        ];

        return $colors[$this->action] ?? 'secondary';
    }

    /**
     * Obtenir la couleur du badge selon le nouvel état
     */
    public function getEtatColorAttribute(): string
    {
        $colors = [
            'en attente' => 'warning',
            'réçu' => 'info',
            'rejetée' => 'danger',
            'terminé' => 'success',
            'annulé' => 'secondary',
        ];

        return $colors[$this->nouvel_etat] ?? 'secondary';
    }
}
