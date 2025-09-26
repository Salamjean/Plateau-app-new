<?php

namespace App\Http\Controllers\EtatCivil\Extrait;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EtatDecesController extends Controller
{
     public function deathRequest(Request $request){
        $etatCivil = Auth::guard('etatCivil')->user();
        
        // Récupérer les paramètres de filtrage
        $etat = $request->input('etat');
        $type = $request->input('type');
        $livraison = $request->input('livraison');
        
        // Construire la requête avec les filtres
        $query = Deces::where('commune', $etatCivil->communeM);
        
        if ($etat) {
            $query->where('etat', $etat);
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($livraison) {
            $query->where('statut_livraison', $livraison);
        }
        
        $deces = $query->paginate(10);
        
        return view('etatCivil.extraits.deces.index', compact('etatCivil', 'deces', 'etat', 'type', 'livraison'));
    }
}
