<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetCodePasswordMairie extends Model
{
    protected $fillable = ['code', 'email'];
}
