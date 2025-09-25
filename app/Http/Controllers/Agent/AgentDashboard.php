<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentDashboard extends Controller
{
    public function dashboard(Request $request) {
        // Récupérer l'admin connecté
        $admin = Auth::guard('agent')->user();
        
        // Récupérer le mois et l'année sélectionnés
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonthHops = $request->input('month_hops', date('m'));
        $selectedYearHops = $request->input('year_hops', date('Y'));

        // Récupérer les données associées à la commune de cet admin pour le mois sélectionné
        // Données pour naissances, décès, et mariages
        $naissances = Naissance::where('commune', $admin->communeM)
            ->where('is_read', false) // Filtrer pour les demandes non traitées
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->orderBy('created_at', 'asc')
            ->get();

        $deces = Deces::where('commune', $admin->communeM)
            ->where('is_read', false) // Filtrer pour les demandes non traitées
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->orderBy('created_at', 'asc')
            ->get();

        $mariages = Mariage::where('commune', $admin->communeM)
            ->where('is_read', false) // Filtrer pour les demandes non traitées
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->orderBy('created_at', 'asc')
            ->get();

        // Calcul des données globales
        $totalData = $naissances->count()+ $deces->count() + $mariages->count();

        // Pourcentages
        $naissancePercentage = $totalData > 0 ? ($naissances->count() / $totalData) * 100 : 0;
        $decesPercentage = $totalData > 0 ? ($deces->count() / $totalData) * 100 : 0;
        $mariagePercentage = $totalData > 0 ? ($mariages->count() / $totalData) * 100 : 0;

        $Dece = $decesPercentage ;
        $NaissP = $naissancePercentage ;    

        // Données pour le tableau de bord
        $naissancedash = $naissances->count();
        $decesdash = $deces->count();
        $mariagedash = $mariages->count();
        $Naiss = $naissancedash;

        // Récupération des données récentes (2 derniers éléments)
        $recentNaissances = $naissances->take(2);
        $recentDeces = $deces->take(2);
        $recentMariages = $mariages->take(2);

        // Retourne la vue avec les données
        return view('agent.dashboard', compact(
            'naissancedash', 'decesdash','NaissP', 'mariagedash', 
            'naissances','deces','mariages','totalData', 'naissancePercentage', 
            'decesPercentage', 'mariagePercentage','recentNaissances', 'recentDeces', 
            'recentMariages', 'Naiss','Dece','selectedMonth', 'selectedYear', 
            'selectedMonthHops', 'selectedYearHops',
        ));

    }

    public function logout(){
        Auth::guard('agent')->logout();
        return redirect()->route('agent.login');
    }
}
