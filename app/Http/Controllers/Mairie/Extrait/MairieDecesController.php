<?php

namespace App\Http\Controllers\Mairie\Extrait;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MairieDecesController extends Controller
{
     public function deathRequest(Request $request){
        $mairie = Auth::guard('mairie')->user();
        
        // Récupérer les paramètres de filtrage
        $etat = $request->input('etat');
        $type = $request->input('type');
        $livraison = $request->input('livraison');
        
        // Construire la requête avec les filtres
        $query = Deces::where('commune', $mairie->name);
        
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
        
        return view('mairie.extraits.deces.index', compact('mairie', 'deces', 'etat', 'type', 'livraison'));
    }
}
