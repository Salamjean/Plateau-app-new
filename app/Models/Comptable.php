<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Comptable extends Authenticatable
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
        'archived_at',
        'cas_urgence',
        'finance_id',
    ];

    public function finance()
    {
        return $this->belongsTo(Finance::class, 'finance_id'); 
    }

    public function timbres()
    {
        return $this->hasMany(Timbre::class);
    }
}
