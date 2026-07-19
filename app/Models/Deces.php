<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deces extends Model
{
    protected $fillable = [
        'type',
        'name',
        'pour',
        'relation',
        'document_autorisation',
        'numberR',
        'dateR',
        'nom_prenoms_pere',
        'nom_prenoms_mere',
        'CNIdfnt',
        'CNIdcl',
        'documentMariage',
        'RequisPolice',
        'reference',
        'commune',
        'etat',
        'motif_de_rejet',
        'user_id',
        'agent_id',
        'livraison_id',
        'livreur_id',
        'agence_id',
        'livraison_code',
        'statut_livraison',
        'qty_simple',
        'qty_integral',
        'quantite',
        // AJOUTER CES CHAMPS
        'choix_option',
        'montant_timbre',
        'montant_livraison',
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
        'timbre_recupere',
        'is_free_request',
        'free_timbres_count',
        'groupe_id',
        'position_in_groupe',
        'type_document',
        'commune_deces',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function poste()
    {
        return $this->belongsTo(Poste::class, 'livraison_id');
    }
    public function livreur()
    {
        return $this->belongsTo(Livreur::class, 'livreur_id');
    }
    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function groupe()
    {
        return $this->belongsTo(DecesGroupe::class, 'groupe_id');
    }

    public function appartientAUnGroupe(): bool
    {
        return !is_null($this->groupe_id);
    }

    /**
     * Scope pour exclure les demandes non payées.
     */
    public function scopePaye($query)
    {
        return $query->whereNotIn('etat', ['non_paye', 'paiement_en_attente', 'en attente de paiement', 'paiement_echoue']);
    }

    public function scopeIndividuelle($query)
    {
        return $query->whereNull('groupe_id');
    }

    public static function getNextId()
    {
        $lastDeces = self::orderBy('id', 'desc')->first();
        if ($lastDeces) {
            return $lastDeces->id + 1;
        } else {
            return 1;
        }
    }
}
