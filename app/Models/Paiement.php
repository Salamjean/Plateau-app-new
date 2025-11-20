<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $table = 'paiements';

    protected $fillable = [
        'deces_id',
        'mariage_id',
        'naissance_id',
        'user_id',
        'transaction_id',
        'operator_id',
        'payment_token',
        'payer_name',
        'montant',
        'currency',
        'status',
        'paid_at',
        'raw_response',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'raw_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function deces()
    {
        return $this->belongsTo(Deces::class, 'deces_id');
    }

    public function mariage()
    {
        return $this->belongsTo(Mariage::class, 'mariage_id');
    }

    public function naissance()
    {
        return $this->belongsTo(Naissance::class, 'naissance_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}