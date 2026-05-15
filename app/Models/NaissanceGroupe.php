<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Représente une "demande groupée" d'actes de naissance :
 * un panier contenant N actes (simples et/ou intégraux) commandés en une seule fois.
 *
 * Les sous-actes sont stockés dans la table `naissances` via `groupe_id`.
 */
class NaissanceGroupe extends Model
{
    protected $table = 'naissance_groupes';

    protected $fillable = [
        'reference',
        'user_id',
        'commune',
        'etat',
        'choix_option',
        'montant_timbre_total',
        'montant_livraison',
        'montant_total',
        'is_free_request',
        'free_timbres_count',
        'qty_simple',
        'qty_integral',
        'nom_destinataire',
        'prenom_destinataire',
        'email_destinataire',
        'contact_destinataire',
        'adresse_livraison',
        'code_postal',
        'ville',
        'commune_livraison',
        'quartier',
        'date_livraison',
        'heure_livraison',
        'livraison_code',
        'qr_code_path',
        'statut_livraison',
        'agent_id',
        'livraison_id',
        'dhl_id',
        'livreur_id',
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
        return $this->hasMany(Naissance::class, 'groupe_id')
                    ->orderBy('position_in_groupe');
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

    /**
     * Quantité totale de timbres dans le groupe.
     */
    public function getQuantiteTotaleAttribute(): int
    {
        return (int) $this->qty_simple + (int) $this->qty_integral;
    }

    /**
     * Scope pour exclure les groupes non payés des listes.
     */
    public function scopePaye($query)
    {
        return $query->whereNotIn('etat', ['non_paye', 'paiement_en_attente', 'en attente de paiement']);
    }

    /**
     * Génère une référence unique pour un nouveau groupe.
     * Format : GRN<random4><id><communeInitiale><year>
     */
    public static function generateReference(?string $commune = null): string
    {
        $communeInitiale = strtoupper(substr($commune ?: 'X', 0, 1));
        $annee = now()->year;
        $rand = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $next = (static::max('id') ?? 0) + 1;
        return 'GRN' . $rand . $next . $communeInitiale . $annee;
    }
}
