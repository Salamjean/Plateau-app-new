<?php

namespace App\Http\Controllers\Comptable\Timbre;

use App\Http\Controllers\Controller;
use App\Models\Timbre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimbreController extends Controller
{
    // Méthode pour obtenir les ventes d'aujourd'hui
   public function ventesAujourdhui()
   {
      $financeId = Auth::guard('comptable')->user()->id;
      
      return Timbre::where('nombre_timbre', '<', 0)
         ->where('comptable_id', $financeId)
         ->whereDate('created_at', today())
         ->sum(DB::raw('ABS(nombre_timbre)'));
   }

   // Méthode pour obtenir les ventes de la semaine
   public function ventesSemaine()
   {
      $financeId = Auth::guard('comptable')->user()->id;
      
      return Timbre::where('nombre_timbre', '<', 0)
         ->where('comptable_id', $financeId)
         ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
         ->sum(DB::raw('ABS(nombre_timbre)'));
   }

   // Méthode pour obtenir les ventes du mois
   public function ventesMois()
   {
      $financeId = Auth::guard('comptable')->user()->id;
      
      return Timbre::where('nombre_timbre', '<', 0)
         ->where('comptable_id', $financeId)
         ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
         ->sum(DB::raw('ABS(nombre_timbre)'));
   }

    public function create()
   {
      $financeId = Auth::guard('comptable')->user()->id;
      
      // Récupérer le solde total de timbres
      $solde_timbres = Timbre::sum('nombre_timbre');
      
      // Récupérer les dernières ventes
      $dernieres_ventes = Timbre::where('nombre_timbre', '<', 0)
                              ->where('comptable_id', $financeId)
                              ->orderBy('created_at', 'desc')
                              ->take(3)
                              ->get();
      
      // Récupérer les totaux de ventes
      $ventes_aujourdhui = $this->ventesAujourdhui();
      $ventes_semaine = $this->ventesSemaine();
      $ventes_mois = $this->ventesMois();
      
      return view('comptable.timbre.create', compact(
         'solde_timbres', 
         'dernieres_ventes',
         'ventes_aujourdhui',
         'ventes_semaine',
         'ventes_mois'
      ));
   }

   // Nouvelle méthode pour les statistiques
   public function statistiques()
   {
      $financeId = Auth::guard('comptable')->user()->id;
      
      // Ventes aujourd'hui
      $ventesAujourdhui = Timbre::where('nombre_timbre', '<', 0)
         ->where('comptable_id', $financeId)
         ->whereDate('created_at', today())
         ->sum(DB::raw('ABS(nombre_timbre)'));
      
      // Ventes cette semaine
      $ventesSemaine = Timbre::where('nombre_timbre', '<', 0)
         ->where('comptable_id', $financeId)
         ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
         ->sum(DB::raw('ABS(nombre_timbre)'));
      
      // Ventes ce mois
      $ventesMois = Timbre::where('nombre_timbre', '<', 0)
         ->where('comptable_id', $financeId)
         ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
         ->sum(DB::raw('ABS(nombre_timbre)'));
      
      // Tendance des ventes (7 derniers jours)
      $tendanceVentes = [];
      $labels = [];
      $valeurs = [];
      
      for ($i = 6; $i >= 0; $i--) {
         $date = now()->subDays($i);
         $labels[] = $date->format('d M');
         
         $ventesJour = Timbre::where('nombre_timbre', '<', 0)
               ->where('comptable_id', $financeId)
               ->whereDate('created_at', $date->format('Y-m-d'))
               ->sum(DB::raw('ABS(nombre_timbre)'));
         
         $valeurs[] = $ventesJour;
      }
      
      return response()->json([
         'ventesAujourdhui' => $ventesAujourdhui,
         'ventesSemaine' => $ventesSemaine,
         'ventesMois' => $ventesMois,
         'objectifMois' => 1000, // Vous pouvez ajuster cette valeur
         'tendanceVentes' => [
               'labels' => $labels,
               'valeurs' => $valeurs
         ]
      ]);
   }

   // Méthode pour la tendance des ventes avec filtre
   public function tendanceVentes(Request $request)
   {
      $jours = $request->get('jours', 7);
      $financeId = Auth::guard('comptable')->user()->id;
      
      $labels = [];
      $valeurs = [];
      
      for ($i = $jours - 1; $i >= 0; $i--) {
         $date = now()->subDays($i);
         $labels[] = $date->format('d M');
         
         $ventesJour = Timbre::where('nombre_timbre', '<', 0)
               ->where('comptable_id', $financeId)
               ->whereDate('created_at', $date->format('Y-m-d'))
               ->sum(DB::raw('ABS(nombre_timbre)'));
         
         $valeurs[] = $ventesJour;
      }
      
      return response()->json([
         'labels' => $labels,
         'valeurs' => $valeurs
      ]);
   }

   public function store(Request $request)
   {
      $financeId = Auth::guard('comptable')->user()->id;
      $request->validate([
         'nombre_timbre' => 'required|numeric|min:0.01|max:999999.99'
      ]);

      try {
         DB::beginTransaction();

         // Vérifier si le solde est suffisant
         $solde_actuel = Timbre::sum('nombre_timbre');
         
         if ($solde_actuel < $request->nombre_timbre) {
               throw new \Exception('Solde insuffisant. Solde disponible: ' . number_format($solde_actuel, 2));
         }

         // Créer un enregistrement de vente (nombre négatif)
         $timbre = Timbre::create([
               'nombre_timbre' => -$request->nombre_timbre, // Négatif pour une vente
               'comptable_id' => $financeId,
         ]);

         DB::commit();

         return redirect()->back()
               ->with('success', number_format($request->nombre_timbre, 2) . ' timbres vendus avec succès!');

      } catch (\Exception $e) {
         DB::rollBack();
         
         return redirect()->back()
               ->withInput()
               ->with('error', 'Erreur lors de la vente: ' . $e->getMessage());
      }
   }

  public function history(Request $request)
   {
      $financeId = Auth::guard('comptable')->user()->id;
      $query = Timbre::with(['finance:id,name,prenom'])
                  ->where('nombre_timbre', '<', 0)
                  ->where('comptable_id', $financeId);
      
      // Filtre par date
      if ($request->has('date_debut') && $request->date_debut) {
         $query->whereDate('created_at', '>=', $request->date_debut);
      }
      
      if ($request->has('date_fin') && $request->date_fin) {
         $query->whereDate('created_at', '<=', $request->date_fin);
      }
      
      $ventes = $query->orderBy('created_at', 'desc')->paginate(10);
      $total_ventes = abs($query->sum('nombre_timbre'));
      
      // Calcul des statistiques
      $stats = [
         'today' => abs(Timbre::where('comptable_id', $financeId)
                     ->where('nombre_timbre', '<', 0)
                     ->whereDate('created_at', today())
                     ->sum('nombre_timbre')),
         
         'this_week' => abs(Timbre::where('comptable_id', $financeId)
                     ->where('nombre_timbre', '<', 0)
                     ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                     ->sum('nombre_timbre')),
         
         'this_month' => abs(Timbre::where('comptable_id', $financeId)
                     ->where('nombre_timbre', '<', 0)
                     ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                     ->sum('nombre_timbre'))
      ];
      
      return view('comptable.timbre.history', compact('ventes', 'total_ventes', 'stats'));
   }
}
