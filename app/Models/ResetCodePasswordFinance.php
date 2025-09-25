<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetCodePasswordFinance extends Model
{
    protected $fillable = ['code', 'email'];
}
