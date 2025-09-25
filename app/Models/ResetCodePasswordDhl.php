<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetCodePasswordDhl extends Model
{
   protected $fillable = ['code', 'email'];
}
