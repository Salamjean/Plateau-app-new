<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\Timbre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceDashboard extends Controller
{
    public function dashboard()
    {
        // Récupérer l'utilisateur connecté
        $finance = Auth::guard('finance')->user();

        // Date du mois en cours
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Compter les demandes par type (total)
        $decesnombre = Deces::where('commune', $finance->communeM)->count();
        $naissancenombre = Naissance::where('commune', $finance->communeM)->count();
        $mariagenombre = Mariage::where('commune', $finance->communeM)->count();
        $total = $decesnombre + $naissancenombre + $mariagenombre;

        // Demandes d'aujourd'hui
        $today = Carbon::today();
        $decesAujourdhui = Deces::where('commune', $finance->communeM)
            ->whereDate('created_at', $today)
            ->count();
        $naissanceAujourdhui = Naissance::where('commune', $finance->communeM)
            ->whereDate('created_at', $today)
            ->count();
        $mariageAujourdhui = Mariage::where('commune', $finance->communeM)
            ->whereDate('created_at', $today)
            ->count();
        $totalAujourdhui = $decesAujourdhui + $naissanceAujourdhui + $mariageAujourdhui;

        // Statistiques de la semaine en cours
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        
        $decesSemaine = Deces::where('commune', $finance->communeM)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        $naissanceSemaine = Naissance::where('commune', $finance->communeM)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        $mariageSemaine = Mariage::where('commune', $finance->communeM)
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        $totalSemaine = $decesSemaine + $naissanceSemaine + $mariageSemaine;

        // Statistiques du mois en cours
        $decesMois = Deces::where('commune', $finance->commune)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();
        $naissanceMois = Naissance::where('commune', $finance->commune)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();
        $mariageMois = Mariage::where('commune', $finance->commune)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();
        $totalMois = $decesMois + $naissanceMois + $mariageMois;

        // Calculs basés sur la table timbres
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        // Requêtes pour les timbres
        $timbresAujourdhui = abs(Timbre::whereDate('created_at', $today)->where('nombre_timbre', '<', 0)->sum('nombre_timbre'));
        $timbresSemaine = abs(Timbre::whereBetween('created_at', [$weekStart, $weekEnd])->where('nombre_timbre', '<', 0)->sum('nombre_timbre'));
        $timbresMois = abs(Timbre::whereBetween('created_at', [$monthStart, $monthEnd])->where('nombre_timbre', '<', 0)->sum('nombre_timbre'));

        // Calcul des montants
        $montantAujourdhui = $timbresAujourdhui * 500;
        $montantSemaine = $timbresSemaine * 500;
        $montantMois = $timbresMois * 500;

        // Récupérer le solde total des timbres
        $soldeTimbres = Timbre::sum('nombre_timbre');

        // Récupérer les demandes récentes
        $demandesNaissance = Naissance::where('commune', $finance->communeM)->latest()->take(5)->get();
        $demandesDeces = Deces::where('commune', $finance->communeM)->latest()->take(5)->get();
        $demandesMariage = Mariage::where('commune', $finance->communeM)->latest()->take(5)->get();

        // Récupérer les statistiques par période pour le graphique
        $now = Carbon::now();
        
        // Données pour les 7 derniers jours
        $weeklyData = [
            'naissances' => $this->getWeeklyStats(Naissance::class, $finance->communeM),
            'deces' => $this->getWeeklyStats(Deces::class, $finance->communeM),
            'mariages' => $this->getWeeklyStats(Mariage::class, $finance->communeM)
        ];

        // Données pour les 30 derniers jours
        $monthlyData = [
            'naissances' => $this->getMonthlyStats(Naissance::class, $finance->communeM),
            'deces' => $this->getMonthlyStats(Deces::class, $finance->communeM),
            'mariages' => $this->getMonthlyStats(Mariage::class, $finance->communeM)
        ];

        // Données pour les 12 derniers mois
        $yearlyData = [
            'naissances' => $this->getYearlyStats(Naissance::class, $finance->communeM),
            'deces' => $this->getYearlyStats(Deces::class, $finance->communeM),
            'mariages' => $this->getYearlyStats(Mariage::class, $finance->communeM)
        ];

        return view('finance.dashboard', 
            compact(
                'total', 'soldeTimbres',
                'decesnombre', 'naissancenombre', 'mariagenombre',
                'demandesNaissance', 'demandesDeces', 'demandesMariage',
                'weeklyData', 'monthlyData', 'yearlyData',
                'decesMois', 'mariageMois', 'naissanceMois', 'totalMois',
                'totalAujourdhui', 'totalSemaine',
                'timbresAujourdhui', 'montantAujourdhui',
                'timbresSemaine', 'montantSemaine',
                'timbresMois', 'montantMois',
                'naissanceAujourdhui', 'decesAujourdhui',
                'mariageAujourdhui', 'naissanceSemaine',
                'decesSemaine', 'mariageSemaine',
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
        Auth::guard('finance')->logout();
        return redirect()->route('finance.login');
    }
}
