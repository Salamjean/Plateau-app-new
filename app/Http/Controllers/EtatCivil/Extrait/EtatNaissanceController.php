<?php

namespace App\Http\Controllers\EtatCivil\Extrait;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EtatNaissanceController extends Controller
{
    public function birthRequest(Request $request){
        $etatCivil = Auth::guard('etatCivil')->user();
        
        // Récupérer les paramètres de filtrage
        $etat = $request->input('etat');
        $type = $request->input('type');
        $livraison = $request->input('livraison');
        
        // Construire la requête avec les filtres
        $query = Naissance::where('commune', $etatCivil->commune);
        
        if ($etat) {
            $query->where('etat', $etat);
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($livraison) {
            $query->where('statut_livraison', $livraison);
        }
        
        $naissances = $query->paginate(10);
        
        return view('etatCivil.extraits.naissance.index', compact('etatCivil', 'naissances', 'etat', 'type', 'livraison'));
    }
}
