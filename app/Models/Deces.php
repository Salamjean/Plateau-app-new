<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deces extends Model
{
   protected $fillable = [
        'name',
        'numberR',
        'dateR',
        'CNIdfnt',
        'CNIdcl',
        'documentMariage',
        'RequisPolice',
        'commune',
        'etat',
        'user_id',  
        'agent_id', 
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
        $lastDeces = self::orderBy('id', 'desc')->first();
        if ($lastDeces) {
            return $lastDeces->id + 1;
        } else {
            return 1;
        }
    }
}
