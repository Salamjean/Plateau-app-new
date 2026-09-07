<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeletedDemande extends Model
{
    protected $fillable = [
        'type_demande',
        'original_id',
        'user_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
