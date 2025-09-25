<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mariage extends Model
{
    protected $fillable = [
        'nomEpoux',
        'prenomEpoux',
        'dateNaissanceEpoux',
        'lieuNaissanceEpoux',
        'pieceIdentite',
        'extraitMariage',
        'commune',
        'etat',
        'agent_id',  // Ajout de agent_id
        'livraison_id', 
        'livreur_id', 
        'agence_id', 
        'livraison_code', 
        'statut_livraison', 
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

    public static function getNextId()
    {
        $lastMariage = self::orderBy('id', 'desc')->first();
        if ($lastMariage) {
            return $lastMariage->id + 1;
        } else {
            return 1;
        }
    }
}
