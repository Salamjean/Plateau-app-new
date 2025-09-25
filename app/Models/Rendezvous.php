<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rendezvous extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_epoux',
        'prenom_epoux',
        'date_naissance_epoux',
        'lieu_naissance_epoux',
        'nom_epouse',
        'prenom_epouse',
        'date_naissance_epouse',
        'lieu_naissance_epouse',
        'adresse',
        'telephone',
        'email',
        'date_mariage_souhaitee',
        'heure_souhaitee',
        'mairie',
        'statut',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
