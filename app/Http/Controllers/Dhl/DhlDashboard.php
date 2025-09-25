<?php

namespace App\Http\Controllers\Dhl;

use App\Http\Controllers\Controller;
use App\Models\DHLAgence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DhlDashboard extends Controller
{
    public function dashboard()
    {
        Carbon::setLocale('fr');
        $dhl = Auth::guard('dhl')->user();
        $startDate = Carbon::now()->subDays(7);
        
        $modelMap = [
            'Naissance'     => 'naissance',
            'Deces'         => 'deces',
            'Mariage'       => 'mariage'
        ];
        
        $stats = [
            'total' => 0,
            'en_attente' => 0,
            'en_cours' => 0,
            'livre' => 0,
            'non_attribue' => 0 // Nouveau statut pour les demandes non attribuées
        ];
        
        $counts = array_fill_keys(array_values($modelMap), 0);
        $soldeDisponible = 0; // Nouvelle variable pour le solde

        $activites = collect();
        $chartData = ['labels' => [], 'livre' => [], 'en_cours' => []];

        // Préparer les labels du graphique sur 7 jours
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartData['labels'][] = $date->translatedFormat('l');
            $chartData['livre'][] = 0;
            $chartData['en_cours'][] = 0;
        }

        // Parcours des modèles
        foreach ($modelMap as $model => $key) {
            $class = "App\\Models\\$model";
            
            // Comptage sur les 7 derniers jours
            $counts[$key] = $class::where('dhl_id', $dhl->id)
                ->where('created_at', '>=', $startDate)
                ->count();
            
            // Mise à jour des stats globales
            $stats['total'] += $counts[$key];
            $stats['en_attente'] += $class::where('dhl_id', $dhl->id)
                ->where('statut_livraison', 'en attente')
                ->count();
            $stats['en_cours'] += $class::where('dhl_id', $dhl->id)
                ->where('statut_livraison', 'en cours')
                ->count();
            $stats['livre'] += $class::where('dhl_id', $dhl->id)
                ->where('statut_livraison', 'livré')
                ->count();
            // COMPTAGE DES DEMANDES NON ATTRIBUÉES (ni à une poste ni à une DHL)
            $stats['non_attribue'] += $class::whereNull('livraison_id')
                ->whereNull('dhl_id')
                ->where('etat', 'terminé')
                ->where('choix_option', 'livraison')
                ->count();
            
            // Calcul du solde disponible (somme de tous les montants de livraison)
            $soldeDisponible += $class::where('dhl_id', $dhl->id)
                ->where('statut_livraison', 'livré')
                ->sum('montant_livraison');
                
            // Activités récentes
            $activites = $activites->merge(
                $class::where('dhl_id', $dhl->id)
                    ->with('user')
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function($item) use ($model) {
                        $item->type = $model;
                        return $item;
                    })
            );
            
            // Données graphiques par jour
            foreach ($chartData['labels'] as $index => $label) {
                $date = Carbon::now()->subDays(6 - $index);
                
                $chartData['livre'][$index] += $class::where('dhl_id', $dhl->id)
                    ->where('statut_livraison', 'livré')
                    ->whereDate('updated_at', $date)
                    ->count();
                    
                $chartData['en_cours'][$index] += $class::where('dhl_id', $dhl->id)
                    ->where('statut_livraison', 'en cours')
                    ->whereDate('updated_at', $date)
                    ->count();
            }
        }
        
        // Trier les activités
        $activites = $activites->sortByDesc('created_at')->where('dhl_id', $dhl->id)->take(3);
        $livreurDispo = DHLAgence::where('disponible','1')->where('dhl_id', $dhl->id)->count();
        $livreurIndispo = DHLAgence::where('disponible','0')->where('dhl_id', $dhl->id)->count();
        $totalLivreur = $livreurDispo + $livreurIndispo;
         // Ajouter cette partie pour récupérer les colis par mairie
        $colisParMairie = [
            // Cette donnée devrait venir de votre base de données
            // Exemple de structure:
            ['commune' => 'Mairie Paris 1', 'total' => 15],
            ['commune' => 'Mairie Paris 2', 'total' => 8],
            ['commune' => 'Mairie Paris 3', 'total' => 12],
            // etc.
        ];
        
        return view('dhl.dashboard', [
            'stats' => $stats,
            'counts' => $counts,
            'livreurDispo' => $livreurDispo,
            'livreurIndispo' => $livreurIndispo,
            'totalLivreur' => $totalLivreur,
            'activites' => $activites,
            'chartData' => $chartData,
            'livreurs' => DHLAgence::active()->get(),
            'soldeDisponible' => $soldeDisponible, // Ajout du solde à la vue
            'colisParMairie' => $colisParMairie // Nouvelle donnée
        ]);
    }


        // Ajouter une nouvelle méthode pour l'API AJAX
public function getColisParMairie()
{
    // Ici, vous devriez interroger votre base de données
    // pour récupérer le nombre de colis par mairie
    $colisParMairie = DB::table('naissances')
        ->select('commune', DB::raw('COUNT(*) as total'))
        ->whereNull('livraison_id')
        ->whereNull('dhl_id')
        ->where('etat', 'terminé')
        ->where('choix_option', 'livraison')
        ->groupBy('commune')
        ->unionAll(
            DB::table('deces')
                ->select('commune', DB::raw('COUNT(*) as total'))
                ->whereNull('livraison_id')
                ->whereNull('dhl_id')
                ->where('etat', 'terminé')
                ->where('choix_option', 'livraison')
                ->groupBy('commune')
        )
        ->unionAll(
            DB::table('mariages')
                ->select('commune', DB::raw('COUNT(*) as total'))
                ->whereNull('livraison_id')
                ->whereNull('dhl_id')
                ->where('etat', 'terminé')
                ->where('choix_option', 'livraison')
                ->groupBy('commune')
        )
        ->get()
        ->groupBy('commune')
        ->map(function ($items, $commune) {
            return [
                'commune' => $commune,
                'total' => $items->sum('total')
            ];
        })
        ->values()
        ->toArray();

    return response()->json([
        'success' => true,
        'mairies' => $colisParMairie
    ]);
}

public function logout(){
        Auth::guard('dhl')->logout();
        return redirect()->route('dhl.login');
    }

}
