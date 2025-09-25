<?php

namespace App\Models;

use FontLib\Table\Type\post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Livreur extends Authenticatable
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
        'disponible',
        'poste_id',
    ];
    public function scopeActive($query)
    {
        return $query->where('name', 'actif');
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

    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id'); 
    }

    

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

       /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function archive()
    {
        $this->update(['archived_at' => now()]);
    }
}
