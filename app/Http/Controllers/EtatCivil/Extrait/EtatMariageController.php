<?php

namespace App\Http\Controllers\EtatCivil\Extrait;

use App\Http\Controllers\Controller;
use App\Models\Mariage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EtatMariageController extends Controller
{
    public function weddingRequest(Request $request){
        $etatCivil = Auth::guard('etatCivil')->user();
        
        // Récupérer les paramètres de filtrage
        $etat = $request->input('etat');
        $type = $request->input('type');
        $livraison = $request->input('livraison');
        
        // Construire la requête avec les filtres
        $query = Mariage::where('commune', $etatCivil->commune);
        
        if ($etat) {
            $query->where('etat', $etat);
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($livraison) {
            $query->where('statut_livraison', $livraison);
        }
        
        $mariages = $query->paginate(10);
        
        return view('etatCivil.extraits.mariage.index', compact('etatCivil', 'mariages', 'etat', 'type', 'livraison'));
    }
}
