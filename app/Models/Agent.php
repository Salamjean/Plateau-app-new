<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Agent extends Authenticatable
{
     use HasFactory, Notifiable;
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'contact',
        'password',
        'profile_picture',
        'commune',
        'communeM',
        'etat_civil_id',
        'archived_at'
    ];

    public function etatCivil()
    {
        return $this->belongsTo(Mairie::class, 'mairie_id'); 
    }
    public function naissance()
    {
        return $this->hasMany(Naissance::class, 'agent_id');
    }
    public function deces()
    {
        return $this->hasMany(Deces::class, 'agent_id');
    }
    public function mariage()
    {
        return $this->hasMany(Mariage::class, 'agent_id');
    }
}
