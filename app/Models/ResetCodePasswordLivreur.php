<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetCodePasswordLivreur extends Model
{
    protected $fillable = ['code', 'email'];
}
