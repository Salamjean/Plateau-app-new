<?php

namespace App\Http\Controllers\EtatCivil\Extrait;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EtatHistoriquesController extends Controller
{
    public function history(Request $request)
    {
        // Récupérer l'agent connecté
        $admin = Auth::guard('etatCivil')->user();
        
        // Déterminer le type de demande (naissance, deces, mariage)
        $type = $request->get('type', 'naissance');
        $perPage = 10;
        
        // Récupérer les demandes terminées selon le type
        switch ($type) {
            case 'deces':
                $tasks = Deces::where('etat', 'terminé')
                    ->where('commune', $admin->communeM)
                    ->latest()
                    ->paginate($perPage);
                $title = "Historique des décès terminés";
                $icon = "fas fa-cross";
                break;
                
            case 'mariage':
                $tasks = Mariage::where('etat', 'terminé')
                    ->where('commune', $admin->communeM)
                    ->latest()
                    ->paginate($perPage);
                $title = "Historique des mariages terminés";
                $icon = "fas fa-ring";
                break;
                
            case 'naissance':
            default:
                $tasks = Naissance::where('etat', 'terminé')
                    ->where('commune', $admin->communeM)
                    ->latest()
                    ->paginate($perPage);
                $title = "Historique des naissances terminées";
                $icon = "fas fa-baby";
                break;
        }
        
        return view('etatCivil.extraits.historiques', compact('tasks', 'type', 'title', 'icon'));
    }

    public function livree(Request $request)
    {
        // Récupérer l'agent connecté
        $admin = Auth::guard('etatCivil')->user();
        
        // Déterminer le type de demande (naissance, deces, mariage)
        $type = $request->get('type', 'naissance');
        $perPage = 10;
        
        // Récupérer les demandes terminées selon le type
        switch ($type) {
            case 'deces':
                $tasks = Deces::where('statut_livraison', 'livré')
                    ->where('commune', $admin->communeM)
                    ->latest()
                    ->paginate($perPage);
                $title = "Historique des extraits décès livrés";
                $icon = "fas fa-cross";
                break;
                
            case 'mariage':
                $tasks = Mariage::where('statut_livraison', 'livré')
                    ->where('commune', $admin->communeM)
                    ->latest()
                    ->paginate($perPage);
                $title = "Historique des extraits de mariages livrés";
                $icon = "fas fa-ring";
                break;
                
            case 'naissance':
            default:
                $tasks = Naissance::where('statut_livraison', 'livré')
                    ->where('commune', $admin->communeM)
                    ->latest()
                    ->paginate($perPage);
                $title = "Historique des extraits naissances livrés";
                $icon = "fas fa-baby";
                break;
        }
        
        return view('etatCivil.extraits.livree', compact('tasks', 'type', 'title', 'icon'));
    }
}
