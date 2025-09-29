<?php

namespace App\Http\Controllers\Admin\Extrait;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use Illuminate\Http\Request;

class AdminExtraitController extends Controller
{
    public function birth(){
        $naissances = Naissance::get();
        return view('admin.extraits.naissance',compact('naissances'));
    }
    public function death(){
        $deces = Deces::get();
        return view('admin.extraits.deces',compact('deces'));
    }
    public function mariage(){
         $mariages = Mariage::get();
        return view('admin.extraits.mariage',compact('mariages'));
    }
}
