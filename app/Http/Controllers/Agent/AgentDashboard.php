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
    public function dashboard(Request $request)
    {
        // Récupérer l'admin connecté
        $admin = Auth::guard('agent')->user();

        // Récupérer les filtres
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');
        $selectedType = $request->input('type');
        
        $selectedMonthHops = $request->input('month_hops', date('m'));
        $selectedYearHops = $request->input('year_hops', date('Y'));

        // Récupérer les données associées à la commune de cet admin
        $requestsData = $this->getRequestsData($admin, $selectedMonth, $selectedYear, $selectedType);
        
        $naissances = $requestsData['naissances'];
        $deces = $requestsData['deces'];
        $mariages = $requestsData['mariages'];
        $allRequestsCollection = $requestsData['allRequests'];

        // Pagination manuelle pour la collection fusionnée
        $currentPage = $request->input('page', 1);
        $perPage = 10;
        $pagedData = $allRequestsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $allRequests = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $allRequestsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Calcul des données globales (stats basées sur la collection complète filtrée)
        $totalData = $naissances->count() + $deces->count() + $mariages->count();

        // Pourcentages
        $naissancePercentage = $totalData > 0 ? ($naissances->count() / $totalData) * 100 : 0;
        $decesPercentage = $totalData > 0 ? ($deces->count() / $totalData) * 100 : 0;
        $mariagePercentage = $totalData > 0 ? ($mariages->count() / $totalData) * 100 : 0;

        $Dece = $decesPercentage;
        $NaissP = $naissancePercentage;

        // Données pour le tableau de bord
        $naissancedash = $naissances->count();
        $decesdash = $deces->count();
        $mariagedash = $mariages->count();
        $Naiss = $naissancedash;

        // Récupération des données récentes (2 derniers éléments) - optionnel car on a déjà la table
        $recentNaissances = $naissances->take(2);
        $recentDeces = $deces->take(2);
        $recentMariages = $mariages->take(2);

        // Retourne la vue avec les données
        return view('agent.dashboard', compact(
            'naissancedash',
            'decesdash',
            'NaissP',
            'mariagedash',
            'naissances',
            'deces',
            'mariages',
            'totalData',
            'naissancePercentage',
            'decesPercentage',
            'mariagePercentage',
            'recentNaissances',
            'recentDeces',
            'recentMariages',
            'Naiss',
            'Dece',
            'selectedMonth',
            'selectedYear',
            'selectedType',
            'selectedMonthHops',
            'selectedYearHops',
            'allRequests'
        ));
    }

    public function refreshTable(Request $request)
    {
        $admin = Auth::guard('agent')->user();
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');
        $selectedType = $request->input('type');

        $requestsData = $this->getRequestsData($admin, $selectedMonth, $selectedYear, $selectedType);
        $allRequestsCollection = $requestsData['allRequests'];

        // Pagination pour le refresh AJAX aussi
        $currentPage = $request->input('page', 1);
        $perPage = 10;
        $pagedData = $allRequestsCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        
        $allRequests = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedData,
            $allRequestsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => route('agent.dashboard'), 'query' => $request->query()]
        );

        return view('agent.partials.table_partial', compact('allRequests'))->render();
    }

    private function getRequestsData($admin, $month, $year, $type = null)
    {
        $naissances = collect();
        $deces = collect();
        $mariages = collect();

        if (!$type || $type == 'naissance') {
            $naissancesQuery = Naissance::where('commune', $admin->communeM)
                ->where('etat', 'en attente');
            if ($month) $naissancesQuery->whereMonth('created_at', $month);
            if ($year) $naissancesQuery->whereYear('created_at', $year);
            $naissances = $naissancesQuery->orderBy('created_at', 'asc')->get();
        }

        if (!$type || $type == 'deces') {
            $decesQuery = Deces::where('commune', $admin->communeM)
                ->where('etat', 'en attente');
            if ($month) $decesQuery->whereMonth('created_at', $month);
            if ($year) $decesQuery->whereYear('created_at', $year);
            $deces = $decesQuery->orderBy('created_at', 'asc')->get();
        }

        if (!$type || $type == 'mariage') {
            $mariagesQuery = Mariage::where('commune', $admin->communeM)
                ->where('etat', 'en attente');
            if ($month) $mariagesQuery->whereMonth('created_at', $month);
            if ($year) $mariagesQuery->whereYear('created_at', $year);
            $mariages = $mariagesQuery->orderBy('created_at', 'asc')->get();
        }

        $allRequests = collect()
            ->concat($naissances->map(function ($item) {
                $item->request_type = 'naissance';
                return $item;
            }))
            ->concat($deces->map(function ($item) {
                $item->request_type = 'deces';
                return $item;
            }))
            ->concat($mariages->map(function ($item) {
                $item->request_type = 'mariage';
                return $item;
            }))
            ->sortBy('created_at');

        return [
            'naissances' => $naissances,
            'deces' => $deces,
            'mariages' => $mariages,
            'allRequests' => $allRequests
        ];
    }

    public function logout()
    {
        Auth::guard('agent')->logout();
        return redirect()->route('agent.login');
    }
}
