<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Demande groupée d'actes de mariage : panier contenant N actes (simple/intégral)
 * pour des mariages potentiellement différents (le sien, celui des parents, etc.).
 */
class MariageGroupe extends Model
{
    protected $table = 'mariage_groupes';

    protected $fillable = [
        'reference', 'user_id', 'commune', 'etat', 'choix_option',
        'montant_timbre_total', 'montant_livraison', 'montant_total',
        'is_free_request', 'free_timbres_count',
        'qty_simple', 'qty_integral',
        'nom_destinataire', 'prenom_destinataire', 'email_destinataire',
        'contact_destinataire', 'adresse_livraison', 'code_postal',
        'ville', 'commune_livraison', 'quartier',
        'date_livraison', 'heure_livraison',
        'livraison_code', 'qr_code_path', 'statut_livraison',
        'agent_id', 'livraison_id', 'dhl_id', 'livreur_id',
        'timbre_recupere',
    ];

    protected $casts = [
        'is_free_request'      => 'boolean',
        'timbre_recupere'      => 'boolean',
        'montant_timbre_total' => 'float',
        'montant_livraison'    => 'float',
        'montant_total'        => 'float',
        'free_timbres_count'   => 'integer',
        'qty_simple'           => 'integer',
        'qty_integral'         => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(Mariage::class, 'groupe_id')->orderBy('position_in_groupe');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function livreur(): BelongsTo
    {
        return $this->belongsTo(Livreur::class, 'livreur_id');
    }

    public function poste(): BelongsTo
    {
        return $this->belongsTo(Poste::class, 'livraison_id');
    }

    public function getQuantiteTotaleAttribute(): int
    {
        return (int) $this->qty_simple + (int) $this->qty_integral;
    }

    public function scopePaye($query)
    {
        return $query->whereNotIn('etat', ['non_paye', 'paiement_en_attente', 'en attente de paiement']);
    }

    public static function generateReference(?string $commune = null): string
    {
        $communeInitiale = strtoupper(substr($commune ?: 'X', 0, 1));
        $annee = now()->year;
        $rand = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $next = (static::max('id') ?? 0) + 1;
        return 'GRM' . $rand . $next . $communeInitiale . $annee;
    }
}
