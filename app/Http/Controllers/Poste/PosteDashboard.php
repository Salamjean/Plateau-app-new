<?php

namespace App\Http\Controllers\Poste;

use App\Http\Controllers\Controller;
use App\Models\DecesCertificat;
use App\Models\DecesSimple;
use App\Models\Livreur;
use App\Models\Mariage;
use App\Models\NaissanceCertificat;
use App\Models\NaissanceSimple;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosteDashboard extends Controller
{
    public function dashboard()
{
    Carbon::setLocale('fr');
    $poste = Auth::guard('poste')->user();
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
    $soldeDisponible = 0;

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
        $counts[$key] = $class::where('livraison_id', $poste->id)
            ->where('created_at', '>=', $startDate)
            ->count();
        
        // Mise à jour des stats globales
        $stats['total'] += $counts[$key];
        $stats['en_attente'] += $class::where('livraison_id', $poste->id)
            ->where('statut_livraison', 'en attente')
            ->count();
        $stats['en_cours'] += $class::where('livraison_id', $poste->id)
            ->where('statut_livraison', 'en cours')
            ->count();
        $stats['livre'] += $class::where('livraison_id', $poste->id)
            ->where('statut_livraison', 'livré')
            ->count();
        
        // COMPTAGE DES DEMANDES NON ATTRIBUÉES (ni à une poste ni à une DHL)
        $stats['non_attribue'] += $class::whereNull('livraison_id')
            ->whereNull('dhl_id')
            ->where('etat', 'terminé')
            ->where('choix_option', 'livraison')
            ->count();
        
        // Calcul du solde disponible
        $soldeDisponible += $class::where('livraison_id', $poste->id)
            ->where('statut_livraison', 'livré')
            ->sum('montant_livraison');
            
        // Activités récentes
        $activites = $activites->merge(
            $class::where('livraison_id', $poste->id)
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
            
            $chartData['livre'][$index] += $class::where('livraison_id', $poste->id)
                ->where('statut_livraison', 'livré')
                ->whereDate('updated_at', $date)
                ->count();
                
            $chartData['en_cours'][$index] += $class::where('livraison_id', $poste->id)
                ->where('statut_livraison', 'en cours')
                ->whereDate('updated_at', $date)
                ->count();
        }
    }

    $activites = $activites->sortByDesc('created_at')->where('poste_id', $poste->id)->take(3);
    $livreurDispo = Livreur::where('disponible','1')->where('poste_id', $poste->id)->count();
    $livreurIndispo = Livreur::where('disponible','0')->where('poste_id', $poste->id)->count();
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
    
    return view('poste.dashboard', [
        'stats' => $stats,
        'counts' => $counts,
        'livreurDispo' => $livreurDispo,
        'livreurIndispo' => $livreurIndispo,
        'totalLivreur' => $totalLivreur,
        'activites' => $activites,
        'chartData' => $chartData,
        'livreurs' => Livreur::active()->get(),
        'soldeDisponible' => $soldeDisponible,
        'colisParMairie' => $colisParMairie // Nouvelle donnée
    ]);
}


    public function logout(){
        Auth::guard('poste')->logout();
        return redirect()->route('post.login');
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



}
