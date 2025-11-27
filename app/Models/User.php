<?php

namespace App\Models;

use App\Notifications\UserResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'prenom', // AJOUTEZ CE CHAMP
        'email',
        'password',
        'indicatif', // AJOUTEZ CE CHAMP
        'contact', // AJOUTEZ CE CHAMP
        'commune', // AJOUTEZ CE CHAMP
        'CMU', // AJOUTEZ CE CHAMP
        'profile_picture', // AJOUTEZ CE CHAMP
        'diaspora', // AJOUTEZ CE CHAMP
        'pays_residence', // AJOUTEZ CE CHAMP
        'ville_residence', // AJOUTEZ CE CHAMP
        'adresse_etrangere', // AJOUTEZ CE CHAMP
        'push_notification',
        'deactivated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'diaspora' => 'boolean', // AJOUTEZ CE CAST
            'deactivated_at' => 'datetime',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPasswordNotification($token));
    }
}