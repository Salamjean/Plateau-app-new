<?php

namespace App\Http\Controllers\EtatCivil;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EtatCivilDashboard extends Controller
{
    public function dashboard()
    {
        // Statistiques des demandes
        $totalNaissances = Naissance::count();
        $totalDeces = Deces::count();
        $totalMariages = Mariage::count();
        $totalAgents = Agent::count();

        // Évolution des demandes des 6 derniers mois
        $demandesParMois = $this->getDemandesParMois();

        // Répartition des statuts
        $statutsNaissance = $this->getRepartitionStatuts('naissances');
        $statutsDeces = $this->getRepartitionStatuts('deces');
        $statutsMariage = $this->getRepartitionStatuts('mariages');

        // Temps de traitement moyen
        $tempsTraitement = $this->getTempsTraitementMoyen();

        // Activité récente
        $activiteRecente = $this->getActiviteRecente();

        // Pourcentages de variation
        $variationNaissances = $this->getVariationMensuelle('naissances');
        $variationDeces = $this->getVariationMensuelle('deces');
        $variationMariages = $this->getVariationMensuelle('mariages');

        return view('etatCivil.dashboard', compact(
            'totalNaissances',
            'totalDeces',
            'totalMariages',
            'totalAgents',
            'demandesParMois',
            'statutsNaissance',
            'statutsDeces',
            'statutsMariage',
            'tempsTraitement',
            'activiteRecente',
            'variationNaissances',
            'variationDeces',
            'variationMariages'
        ));
    }

    /**
     * Récupère l'évolution des demandes des 6 derniers mois
     */
    private function getDemandesParMois()
    {
        $mois = [];
        $naissances = [];
        $deces = [];
        $mariages = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mois[] = $date->format('M');

            // Naissances
            $naissances[] = Naissance::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            // Décès
            $deces[] = Deces::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            // Mariages
            $mariages[] = Mariage::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'mois' => $mois,
            'naissances' => $naissances,
            'deces' => $deces,
            'mariages' => $mariages
        ];
    }

    /**
     * Récupère la répartition des statuts pour un type de demande
     */
    private function getRepartitionStatuts($type)
    {
        switch ($type) {
            case 'naissances':
                $model = new Naissance();
                break;
            case 'deces':
                $model = new Deces();
                break;
            case 'mariages':
                $model = new Mariage();
                break;
            default:
                return [];
        }

        return [
            'termine' => $model->where('etat', 'terminé')->count(),
            'en_cours' => $model->where('etat', 'en cours')->count(),
            'en_attente' => $model->where('etat', 'en attente')->count()
        ];
    }

    /**
     * Récupère le temps de traitement moyen par type de demande
     */
    private function getTempsTraitementMoyen()
    {
        return [
            'naissances' => Naissance::where('etat', 'terminé')
                ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as moyenne')
                ->first()->moyenne ?? 0,

            'deces' => Deces::where('etat', 'terminé')
                ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as moyenne')
                ->first()->moyenne ?? 0,

            'mariages' => Mariage::where('etat', 'terminé')
                ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as moyenne')
                ->first()->moyenne ?? 0
        ];
    }

    /**
     * Récupère l'activité récente (dernières 24 heures)
     */
    private function getActiviteRecente()
    {
        $activites = [];

        // Demandes de naissance récentes
        $naissances = Naissance::with('user')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        foreach ($naissances as $naissance) {
            $activites[] = [
                'type' => 'naissance',
                'message' => 'Nouvelle demande de naissance #' . $naissance->reference,
                'time' => $naissance->created_at->diffForHumans(),
                'icon' => 'fas fa-baby'
            ];
        }

        // Demandes de décès récentes
        $deces = Deces::with('user')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        foreach ($deces as $decesItem) {
            $activites[] = [
                'type' => 'deces',
                'message' => 'Nouvelle demande de décès #' . $decesItem->reference,
                'time' => $decesItem->created_at->diffForHumans(),
                'icon' => 'fas fa-cross'
            ];
        }

        // Demandes de mariage récentes
        $mariages = Mariage::with('user')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        foreach ($mariages as $mariage) {
            $activites[] = [
                'type' => 'mariage',
                'message' => 'Nouvelle demande de mariage #' . $mariage->reference,
                'time' => $mariage->created_at->diffForHumans(),
                'icon' => 'fas fa-ring'
            ];
        }

        // Trier par date décroissante et limiter à 5 éléments
        usort($activites, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activites, 0, 5);
    }

    /**
     * Calcule la variation mensuelle pour un type de demande
     */
    private function getVariationMensuelle($type)
    {
        switch ($type) {
            case 'naissances':
                $model = new Naissance();
                break;
            case 'deces':
                $model = new Deces();
                break;
            case 'mariages':
                $model = new Mariage();
                break;
            default:
                return 0;
        }

        $moisActuel = $model->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $moisPrecedent = $model->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->count();

        if ($moisPrecedent == 0) {
            return $moisActuel > 0 ? 100 : 0;
        }

        return round((($moisActuel - $moisPrecedent) / $moisPrecedent) * 100, 1);
    }

    /**
     * Récupère les statistiques détaillées pour les graphiques
     */
    public function getStatsDetails(Request $request)
    {
        $periode = $request->get('periode', '6mois');

        switch ($periode) {
            case '3mois':
                $months = 3;
                break;
            case '12mois':
                $months = 12;
                break;
            default:
                $months = 6;
        }

        $data = $this->getDemandesParPeriode($months);

        return response()->json($data);
    }

    /**
     * Récupère les demandes par période
     */
    private function getDemandesParPeriode($months)
    {
        $mois = [];
        $naissances = [];
        $deces = [];
        $mariages = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $mois[] = $date->format('M Y');

            $naissances[] = Naissance::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $deces[] = Deces::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $mariages[] = Mariage::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'labels' => $mois,
            'datasets' => [
                [
                    'label' => 'Naissances',
                    'data' => $naissances,
                    'borderColor' => '#ff8800',
                    'backgroundColor' => 'rgba(255, 136, 0, 0.1)'
                ],
                [
                    'label' => 'Décès',
                    'data' => $deces,
                    'borderColor' => '#6c757d',
                    'backgroundColor' => 'rgba(108, 117, 125, 0.1)'
                ],
                [
                    'label' => 'Mariages',
                    'data' => $mariages,
                    'borderColor' => '#007e00',
                    'backgroundColor' => 'rgba(0, 126, 0, 0.1)'
                ]
            ]
        ];
    }


    public function logout(){
        Auth::guard('etatCivil')->logout();
        return redirect()->route('etat_civil.login');
    }
}
