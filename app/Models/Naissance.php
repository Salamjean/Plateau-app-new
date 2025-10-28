<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Naissance extends Model
{
    protected $fillable = [
        'type',
        'pour',
        'name',
        'prenom',
        'number',
        'DateR',
        'CNI',
        'reference',
        'commune',
        'etat',
        'statut_livraison',
        'user_id',
        'agent_id',
        'livreur_id',
        'agence_id',
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
        'quartier'
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


    public static function getNextId() // Rendez la méthode publique et statique
    {
        // Récupérer le dernier ID et incrémenter
        $lastNaissance = self::orderBy('id', 'desc')->first();
        if ($lastNaissance) {
            return $lastNaissance->id + 1;
        } else {
            return 1; // Si c'est le premier enregistrement
        }
    }
}
