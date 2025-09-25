<?php

namespace App\Http\Controllers;

use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(){
        return view('home.home');
    }

    public function recherche(Request $request){
        $etatDemande = null; // Initialisation de l'état de la demande
        $reference = null; // Initialisation de la référence recherchée

        if ($request->isMethod('post')) { // Vérifier si c'est une requête POST (formulaire soumis)
            $reference = $request->input('reference_naissance'); // On utilise toujours le même nom de champ depuis la vue

            if ($reference) {
                // Rechercher dans la table 'naissances'
                $naissance = Naissance::where('reference', $reference)->first();
                if ($naissance) {
                    $etatDemande = $naissance->etat;
                    return view('home.recherche', compact('etatDemande', 'reference')); // Passer $etatDemande et $reference à la vue
                }
                // Rechercher dans la table 'deces'
                $deces = Deces::where('reference', $reference)->first();
                if ($deces) {
                    $etatDemande = $deces->etat;
                    return view('home.recherche', compact('etatDemande', 'reference'));
                }

                // Rechercher dans la table 'mariages'
                $mariage = Mariage::where('reference', $reference)->first();
                if ($mariage) {
                    $etatDemande = $mariage->etat;
                    return view('home.recherche', compact('etatDemande', 'reference'));
                }

                $etatDemande = false; // Aucune demande trouvée pour cette référence dans aucune table
            }
        }

        return view('home.recherche', compact('etatDemande', 'reference')); // Passer $etatDemande et $reference à la vue
    }

    public function about(){
        return view('home.about');
    }

    public function service(){
        return view('home.service');
    }
    public function department(){
        return view('home.department');
    }
    public function birth(){
        return view('home.naissance');
    }
    public function death(){
        return view('home.deces');
    }
    public function wedding(){
        return view('home.mariage');
    }

    public function rendezvous(){
        return view('home.rendezvous');
    }
    public function contact(){
        return view('home.contact');
    }
}
