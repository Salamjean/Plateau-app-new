<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\Timbre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComptableDashboard extends Controller
{
    public function dashboard(){
        // Récupérer l'utilisateur connecté (comptable)
        $comptable = Auth::guard('comptable')->user();
        $comptableId = $comptable->id;
        $commune = $comptable->communeM;

        // Date du mois en cours
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Compter les demandes par type pour la commune de l'utilisateur (total)
        $decesnombre = Deces::where('commune', $commune)->count();
        $mariagenombre = Mariage::where('commune', $commune)->count();
        $naissancenombre = Naissance::where('commune', $commune)->count();
        $total = $decesnombre + $naissancenombre + $mariagenombre;

        // Statistiques du jour
        $today = Carbon::today();
        $decesAujourdhui = Deces::where('commune', $commune)
            ->whereDate('created_at', $today)
            ->count();
        $mariageAujourdhui = Mariage::where('commune', $commune)
            ->whereDate('created_at', $today)
            ->count();
        $naissanceAujourdhui = Naissance::where('commune', $commune)
            ->whereDate('created_at', $today)
            ->count();
        $totalAujourdhui = $decesAujourdhui + $naissanceAujourdhui + $mariageAujourdhui;

        // Statistiques de la semaine en cours
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        $decesSemaine = Deces::where('commune', $commune)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        $mariageSemaine = Mariage::where('commune', $commune)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        $naissanceSemaine = Naissance::where('commune', $commune)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        $totalSemaine = $decesSemaine + $naissanceSemaine + $mariageSemaine;

        // Statistiques du mois en cours
        $decesMois = Deces::where('commune', $commune)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();
        $mariageMois = Mariage::where('commune', $commune)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();
        $naissanceMois = Naissance::where('commune', $commune)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();
        $totalMois = $decesMois + $naissanceMois + $mariageMois;

        // Récupérer les demandes récentes
        $demandesNaissance = Naissance::where('commune', $commune)->latest()->take(5)->get();
        $demandesDeces = Deces::where('commune', $commune)->latest()->take(5)->get();
        $demandesMariage = Mariage::where('commune', $commune)->latest()->take(5)->get();

        // Récupérer les statistiques par période pour le graphique
        $weeklyData = [
            'naissances' => $this->getWeeklyStats(Naissance::class, $commune),
            'deces' => $this->getWeeklyStats(Deces::class, $commune),
            'mariages' => $this->getWeeklyStats(Mariage::class, $commune)
        ];

        $monthlyData = [
            'naissances' => $this->getMonthlyStats(Naissance::class, $commune),
            'deces' => $this->getMonthlyStats(Deces::class, $commune),
            'mariages' => $this->getMonthlyStats(Mariage::class, $commune)
        ];

        $yearlyData = [
            'naissances' => $this->getYearlyStats(Naissance::class, $commune),
            'deces' => $this->getYearlyStats(Deces::class, $commune),
            'mariages' => $this->getYearlyStats(Mariage::class, $commune)
        ];

        // STATISTIQUES DE VENTE DE TIMBRES
        $timbresAujourdhui = Timbre::where('nombre_timbre', '<', 0)
            ->where('comptable_id', $comptableId)
            ->whereDate('created_at', today())
            ->sum(DB::raw('ABS(nombre_timbre)'));

        $timbresSemaine = Timbre::where('nombre_timbre', '<', 0)
            ->where('comptable_id', $comptableId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum(DB::raw('ABS(nombre_timbre)'));

        $timbresMois = Timbre::where('nombre_timbre', '<', 0)
            ->where('comptable_id', $comptableId)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum(DB::raw('ABS(nombre_timbre)'));

        // Calcul des montants (500 FCFA par timbre)
        $montantAujourdhui = $timbresAujourdhui * 500;
        $montantSemaine = $timbresSemaine * 500;
        $montantMois = $timbresMois * 500;

        // Solde actuel de timbres
        $soldeTimbres = Timbre::sum('nombre_timbre');

        // Tendance des ventes de timbres (7 derniers jours)
        $tendanceTimbres = [];
        $labelsTimbres = [];
        $valeursTimbres = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labelsTimbres[] = $date->format('d M');
            
            $ventesJour = Timbre::where('nombre_timbre', '<', 0)
                ->where('comptable_id', $comptableId)
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum(DB::raw('ABS(nombre_timbre)'));
            
            $valeursTimbres[] = $ventesJour;
        }

        // Dernières ventes de timbres
        $dernieresVentesTimbres = Timbre::where('nombre_timbre', '<', 0)
            ->where('comptable_id', $comptableId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('comptable.dashboard',
            compact(
                'total', 'decesnombre', 'naissancenombre', 'mariagenombre',
                'demandesNaissance', 'demandesDeces', 'demandesMariage',
                'weeklyData', 'monthlyData', 'yearlyData',
                'decesMois', 'mariageMois', 'naissanceMois', 'totalMois',
                'totalAujourdhui', 'totalSemaine',
                'decesAujourdhui', 'mariageAujourdhui', 'naissanceAujourdhui',
                'decesSemaine', 'mariageSemaine', 'naissanceSemaine',
                // Données timbres
                'timbresAujourdhui', 'timbresSemaine', 'timbresMois',
                'montantAujourdhui', 'montantSemaine', 'montantMois',
                'soldeTimbres', 'tendanceTimbres', 'labelsTimbres', 'valeursTimbres',
                'dernieresVentesTimbres'
            ));
    }
    // Méthodes helper pour récupérer les statistiques
    private function getWeeklyStats($model, $commune)
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = $model::where('commune', $commune)
                ->whereDate('created_at', $date->toDateString())
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getMonthlyStats($model, $commune)
    {
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = $model::where('commune', $commune)
                ->whereDate('created_at', $date->toDateString())
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getYearlyStats($model, $commune)
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $start = Carbon::now()->subMonths($i)->startOfMonth();
            $end = Carbon::now()->subMonths($i)->endOfMonth();
            
            $count = $model::where('commune', $commune)
                ->whereBetween('created_at', [$start, $end])
                ->count();
            $data[] = $count;
        }
        return $data;
    }

    public function logout(){
        Auth::guard('comptable')->logout();
        return redirect()->route('comptable.login');
    }
}
