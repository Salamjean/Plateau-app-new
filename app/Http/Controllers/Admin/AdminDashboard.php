<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LivraisonsExport;
use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mairie;
use App\Models\Mariage;
use App\Models\Naissance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class AdminDashboard extends Controller
{
    public function dashboard()
    {
        $deces = Deces::count();
        $mariage = Mariage::count();
        $naissance = Naissance::count();

        $mairie = Mairie::whereNull('archived_at')->count();
        $total = $deces + $mariage + $naissance;

        // Récupérer le solde total de toutes les mairies
        $soldeTotalMairies = Mairie::whereNull('archived_at')->sum('solde');
        // Solde initial
        $soldeActuel = $soldeTotalMairies;

        // Déduction pour chaque nouvelle demande (Uniquement si timbre récupéré pour les livraisons)
        $debit = 500;

        $naissDebit = Naissance::where(function ($q) {
            $q->where('choix_option', '!=', 'Livraison')
                ->orWhere('timbre_recupere', 1);
        })->count();

        $decesDebit = Deces::where(function ($q) {
            $q->where('choix_option', '!=', 'Livraison')
                ->orWhere('timbre_recupere', 1);
        })->count();

        $mariageDebit = Mariage::where(function ($q) {
            $q->where('choix_option', '!=', 'Livraison')
                ->orWhere('timbre_recupere', 1);
        })->count();

        $soldeDebite = ($naissDebit + $decesDebit + $mariageDebit) * $debit;
        $soldeRestant = $soldeActuel - $soldeDebite; // Calcul du solde restant

        $modelMap = [
            'Naissance' => 'naissance',
            'Deces' => 'deces',
            'Mariage' => 'mariage'
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
        $soldeMoisEnCours = 0; // Nouvelle variable pour le solde du mois en cours

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
            $counts[$key] = $class::count();

            // Mise à jour des stats globales
            $stats['total'] += $counts[$key];
            $stats['en_attente'] += $class::where('statut_livraison', 'en attente')
                ->count();
            $stats['en_cours'] += $class::where('statut_livraison', 'en cours')
                ->count();
            $stats['livre'] += $class::where('statut_livraison', 'livré')
                ->count();

            // COMPTAGE DES DEMANDES NON ATTRIBUÉES (ni à une poste ni à une DHL)
            $stats['non_attribue'] += $class::whereNull('livraison_id')
                ->whereNull('dhl_id')
                ->where('etat', 'terminé')
                ->where('choix_option', 'livraison')
                ->count();

            // Calcul du solde disponible TOTAL (toutes les livraisons)
            $soldeDisponible += $class::where('statut_livraison', 'livré')
                ->where('choix_option', 'livraison')
                ->sum('montant_livraison');

            // NOUVEAU: Calcul du solde du MOIS EN COURS uniquement
            $soldeMoisEnCours += $class::where('statut_livraison', 'livré')
                ->whereYear('updated_at', Carbon::now()->year)
                ->whereMonth('updated_at', Carbon::now()->month)
                ->sum('montant_livraison');

            // Activités récentes
            $activites = $activites->merge(
                $class::with('user')
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function ($item) use ($model) {
                        $item->type = $model;
                        return $item;
                    })
            );
        }

        return view('admin.dashboard', compact(
            'deces',
            'mariage',
            'naissance',
            'total',
            'soldeActuel',
            'soldeDebite',
            'soldeRestant',
            'mairie',
            'soldeTotalMairies',
            'activites',
            'soldeDisponible',
            'stats',
            'soldeMoisEnCours' // Ajout de la nouvelle variable
        ));
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    public function downloadRapport(Request $request)
    {
        $request->validate([
            'annee' => 'required|integer|min:2020|max:' . date('Y'),
            'mois' => 'required|string|size:2',
            'type_rapport' => 'required|in:excel,pdf'
        ]);

        $annee = $request->annee;
        $mois = $request->mois;
        $typeRapport = $request->type_rapport;

        // Récupérer les données des livraisons pour le mois et l'année sélectionnés
        $modelMap = [
            'Naissance' => 'App\\Models\\Naissance',
            'Deces' => 'App\\Models\\Deces',
            'Mariage' => 'App\\Models\\Mariage'
        ];

        $livraisons = collect();

        foreach ($modelMap as $modelName => $modelClass) {
            $modelLivraisons = $modelClass::where('statut_livraison', 'livré')
                ->where('choix_option', 'livraison')
                ->whereYear('updated_at', $annee)
                ->whereMonth('updated_at', $mois)
                ->with('user')
                ->get()
                ->map(function ($item) use ($modelName) {
                    $item->type_document = $modelName;
                    return $item;
                });

            $livraisons = $livraisons->merge($modelLivraisons);
        }

        $data = [
            'livraisons' => $livraisons,
            'annee' => $annee,
            'mois' => $mois,
            'mois_nom' => $this->getMonthName($mois),
            'total_montant' => $livraisons->sum('montant_livraison'),
            'total_livraisons' => $livraisons->count()
        ];

        if ($typeRapport === 'excel') {
            return Excel::download(new LivraisonsExport($data), "rapport_livraisons_{$mois}_{$annee}.xlsx");
        } else {
            $pdf = PDF::loadView('admin.rapports.livraisons-pdf', $data);
            return $pdf->download("rapport_livraisons_{$mois}_{$annee}.pdf");
        }
    }

    private function getMonthName($monthNumber)
    {
        $months = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre'
        ];

        return $months[$monthNumber] ?? 'Mois inconnu';
    }
}
