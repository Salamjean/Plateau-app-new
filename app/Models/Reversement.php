<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reversement extends Model
{
    use HasFactory;

    protected $table = 'reversements';

    protected $fillable = [
        'reference',
        'montant',
        'is_premier',
        'type_reversement',
        'date_reversement',
        'mois_concernes',
        'annee',
        'commentaire',
        'finance_id',
        'comptable_id',
        'auteur_role',
        'enregistre_par',
        'enregistre_par_email',
        'commune',
    ];

    protected $casts = [
        'is_premier' => 'boolean',
        'date_reversement' => 'datetime',
        'mois_concernes' => 'array',
        'montant' => 'float',
    ];

    public function finance()
    {
        return $this->belongsTo(Finance::class, 'finance_id');
    }

    public function comptable()
    {
        return $this->belongsTo(Comptable::class, 'comptable_id');
    }

    /**
     * Nom de la personne ayant enregistré le reversement
     */
    public function getNomAuteurAttribute()
    {
        if (!empty($this->enregistre_par) && $this->enregistre_par !== 'Régie des taxes') {
            return $this->enregistre_par;
        }

        if ($this->comptable_id && $this->comptable) {
            return trim($this->comptable->name . ' ' . $this->comptable->prenom);
        }

        if (!empty($this->enregistre_par_email)) {
            $user = User::where('email', $this->enregistre_par_email)->first();
            if ($user && (!empty($user->name) || !empty($user->prenom))) {
                return trim(($user->name ?? '') . ' ' . ($user->prenom ?? ''));
            }
        }

        return $this->enregistre_par ?: 'Responsable Financier';
    }
}

