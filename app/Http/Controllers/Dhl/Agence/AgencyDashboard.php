<?php

namespace App\Http\Controllers\Dhl\Agence;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgencyDashboard extends Controller
{
    public function dashboard()
    {
        $livreur = Auth::guard('agency')->user();
        $now = Carbon::now();
        $oneWeekAgo = $now->copy()->subWeek();

        // Modèles à vérifier
        $modeles = [
            'Naissance',
            'Deces',
            'Mariage'
        ];

        // Initialisation des compteurs
        $stats = [
            'en_cours' => 0,
            'livrees' => 0,
            'en_retard' => 0,
            'cette_semaine' => 0,
            'montant_total' => 0 // Nouveau compteur pour le montant total
        ];

        $activites = [];

        foreach ($modeles as $modele) {
            $classe = "App\\Models\\$modele";

            // Livraisons en cours
            $stats['en_cours'] += $classe::where('agence_id', $livreur->id)
                ->where('statut_livraison', 'en cours')
                ->count();

            // Livraisons terminées
            $livrees = $classe::where('agence_id', $livreur->id)
                ->where('statut_livraison', 'livré')
                ->get();

            $stats['livrees'] += $livrees->count();
            
            // Calcul du montant total des livraisons terminées
            $stats['montant_total'] += $livrees->sum('montant_livraison');

            // Activités récentes (dernière semaine)
            $recentes = $classe::where('agence_id', $livreur->id)
                ->where('updated_at', '>=', $oneWeekAgo)
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->get();

            foreach ($recentes as $demande) {
                $activites[] = [
                    'type' => $this->getTypeDemande($modele),
                    'reference' => $demande->reference,
                    'livraison_code' => $demande->livraison_code,
                    'statut' => $demande->statut_livraison,
                    'date' => $demande->updated_at,
                    'destinataire' => $demande->nom_destinataire ?? $demande->nom_complet ?? 'N/A',
                    'montant' => $demande->montant_livraison // Ajout du montant
                ];
            }
        }

        // Trier les activités par date
        usort($activites, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        // Garder seulement les 5 plus récentes
        $activites = array_slice($activites, 0, 3);

        return view('agency.dashboard', [
            'stats' => $stats,
            'activites' => $activites,
            'livreur' => $livreur
        ]);
    }

    private function getTypeDemande($modele)
    {
        $types = [
            'Naissance' => 'Naissance',
            'Deces' => 'Décès',
            'Mariage' => 'Mariage'
        ];
        
        return $types[$modele] ?? 'Demande';
    }

    public function logout(){
        Auth::guard('agency')->logout();
        return redirect()->route('agency.login');
    }

    public function toggleDisponibilite(Request $request)
    {
        $livreur = Auth::guard('agency')->user();
        $livreur->disponible = !$livreur->disponible;
        $livreur->save();

        return redirect()->back()->with('success', 'Statut de disponibilité mis à jour');
    }
}
