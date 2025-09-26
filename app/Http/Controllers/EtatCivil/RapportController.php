<?php

namespace App\Http\Controllers\EtatCivil;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RapportController extends Controller
{
    /**
 * Affiche la page des rapports détaillés
 */
public function rapports()
{
    // Période par défaut : 12 derniers mois
    $periode = request()->get('periode', '12mois');
    
    switch ($periode) {
        case '3mois':
            $months = 3;
            break;
        case '6mois':
            $months = 6;
            break;
        default:
            $months = 12;
    }

    // Données pour les graphiques
    $donneesGraphiques = $this->getDemandesParPeriode($months);
    
    // Statistiques détaillées
    $statsDetaillees = $this->getStatsDetaillees($months);
    
    // Top agents
    $topAgents = $this->getTopAgents();

    return view('etatCivil.rapports', compact(
        'donneesGraphiques',
        'statsDetaillees',
        'topAgents',
        'periode',
        'months'
    ));
}

/**
 * Récupère les statistiques détaillées pour les rapports
 */
private function getStatsDetaillees($months)
{
    return [
        'naissances' => [
            'total' => Naissance::count(),
            'termine' => Naissance::where('etat', 'terminé')->count(),
            'en_cours' => Naissance::where('etat', 'en cours')->count(),
            'en_attente' => Naissance::where('etat', 'en attente')->count(),
            'taux_completion' => Naissance::count() > 0 ? 
                round((Naissance::where('etat', 'terminé')->count() / Naissance::count()) * 100, 1) : 0
        ],
        'deces' => [
            'total' => Deces::count(),
            'termine' => Deces::where('etat', 'terminé')->count(),
            'en_cours' => Deces::where('etat', 'en cours')->count(),
            'en_attente' => Deces::where('etat', 'en attente')->count(),
            'taux_completion' => Deces::count() > 0 ? 
                round((Deces::where('etat', 'terminé')->count() / Deces::count()) * 100, 1) : 0
        ],
        'mariages' => [
            'total' => Mariage::count(),
            'termine' => Mariage::where('etat', 'terminé')->count(),
            'en_cours' => Mariage::where('etat', 'en cours')->count(),
            'en_attente' => Mariage::where('etat', 'en attente')->count(),
            'taux_completion' => Mariage::count() > 0 ? 
                round((Mariage::where('etat', 'terminé')->count() / Mariage::count()) * 100, 1) : 0
        ]
    ];
}

/**
 * Récupère le top des agents par performance
 */
private function getTopAgents()
{
    // Si vous avez une relation entre les demandes et les agents
    // Sinon, retournez des données factices ou ajustez selon votre structure
    return Agent::withCount(['naissance', 'deces', 'mariage'])
        ->orderByRaw('(naissance_count + deces_count + mariage_count) DESC')
        ->take(5)
        ->get()
        ->map(function($agent) {
            return [
                'name' => $agent->name . ' ' . $agent->prenom,
                'demandes_traitees' => $agent->naissance_count + $agent->deces_count + $agent->mariage_count,
                'taux_satisfaction' => $this->calculerTauxSatisfaction($agent)
            ];
        })
        ->toArray();
}

/**
 * Calcule le taux de satisfaction d'un agent
 */
private function calculerTauxSatisfaction($agent)
{
    $totalDemandes = $agent->naissance_count + $agent->deces_count + $agent->mariage_count;
    
    if ($totalDemandes == 0) {
        return 0;
    }
    
    // Compter les demandes terminées pour cet agent
    $demandesTerminees = Naissance::where('agent_id', $agent->id)->where('etat', 'terminé')->count()
                        + Deces::where('agent_id', $agent->id)->where('etat', 'terminé')->count()
                        + Mariage::where('agent_id', $agent->id)->where('etat', 'terminé')->count();

    // Compter les demandes pour cet agent
    $demandes = Naissance::count()
                        + Deces::count()
                        + Mariage::count();
    
    // Taux de satisfaction basé sur le pourcentage de demandes terminées
    $tauxTerminaison = ($demandesTerminees / $demandes) * 100;
    
    return round($tauxTerminaison, 1);
}
/**
 * Exporte le rapport en PDF
 */
public function exportPdf(Request $request)
{
    $periode = $request->get('periode', '12mois');
    
    switch ($periode) {
        case '3mois':
            $months = 3;
            break;
        case '6mois':
            $months = 6;
            break;
        default:
            $months = 12;
    }

    // Données pour le PDF
    $donneesGraphiques = $this->getDemandesParPeriode($months);
    $statsDetaillees = $this->getStatsDetaillees($months);
    $topAgents = $this->getTopAgents();

    $data = [
        'donneesGraphiques' => $donneesGraphiques,
        'statsDetaillees' => $statsDetaillees,
        'topAgents' => $topAgents,
        'periode' => $periode,
        'months' => $months,
        'dateExport' => Carbon::now()->format('d/m/Y à H:i'),
        'titre' => 'Rapport État Civil - ' . Carbon::now()->format('F Y')
    ];

    $pdf = Pdf::loadView('etatCivil.pdf.rapport', $data);
    
    return $pdf->download('rapport-etat-civil-' . Carbon::now()->format('Y-m-d') . '.pdf');
}

/**
 * Aperçu du PDF
 */
public function previewPdf(Request $request)
{
    $periode = $request->get('periode', '12mois');
    
    switch ($periode) {
        case '3mois':
            $months = 3;
            break;
        case '6mois':
            $months = 6;
            break;
        default:
            $months = 12;
    }

    $donneesGraphiques = $this->getDemandesParPeriode($months);
    $statsDetaillees = $this->getStatsDetaillees($months);
    $topAgents = $this->getTopAgents();

    $data = [
        'donneesGraphiques' => $donneesGraphiques,
        'statsDetaillees' => $statsDetaillees,
        'topAgents' => $topAgents,
        'periode' => $periode,
        'months' => $months,
        'dateExport' => Carbon::now()->format('d/m/Y à H:i'),
        'titre' => 'Rapport État Civil - ' . Carbon::now()->format('F Y')
    ];

    return view('etatCivil.pdf.rapport', $data);
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

    
}
