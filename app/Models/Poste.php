<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Poste extends Authenticatable
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
        'archived_at'
    ];

    public function naissances()
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

    public function archive()
    {
        $this->update(['archived_at' => now()]);
    }
}
