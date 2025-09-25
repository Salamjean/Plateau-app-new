<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class EtatCivil extends Authenticatable
{
   use HasFactory, Notifiable;
     protected $fillable = [
        'name_respo',
        'email',
        'contact',
        'password',
        'profile_picture',
        'commune',
        'communeM',
        'mairie_id',
        'archived_at'
    ];

    public function mairie()
    {
        return $this->belongsTo(Mairie::class, 'mairie_id'); 
    }
}
