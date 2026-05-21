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

        // Calcul du montant des timbres gratuits (mode test)
        $freeTimbresMontant = 0;
        $freeTimbresMontant += Naissance::where('is_free_request', true)->sum('free_timbres_count') * $debit;
        $freeTimbresMontant += Deces::where('is_free_request', true)->sum('free_timbres_count') * $debit;
        $freeTimbresMontant += Mariage::where('is_free_request', true)->sum('free_timbres_count') * $debit;
        $soldeDebite += $freeTimbresMontant;

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

            // Activités récentes (uniquement les demandes payées)
            $activites = $activites->merge(
                $class::paye()
                    ->with('user')
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function ($item) use ($model) {
                        $item->type = $model;
                        return $item;
                    })
            );
        }

        $activites = $activites->sortByDesc('created_at');

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

    public function transactions(Request $request)
    {
        // 1. Récupérer toutes les demandes payées
        $naissancesQuery = Naissance::paye()->with('user');
        $mariagesQuery = Mariage::paye()->with('user');
        $decesQuery = Deces::paye()->with('user');

        // Appliquer le filtre par nom du demandeur si spécifié
        $searchDemandeur = $request->input('demandeur');
        if (!empty($searchDemandeur)) {
            $naissancesQuery->whereHas('user', function ($q) use ($searchDemandeur) {
                $q->where(function ($sub) use ($searchDemandeur) {
                    $sub->where('name', 'like', "%{$searchDemandeur}%")
                        ->orWhere('prenom', 'like', "%{$searchDemandeur}%");
                });
            });
            $mariagesQuery->whereHas('user', function ($q) use ($searchDemandeur) {
                $q->where(function ($sub) use ($searchDemandeur) {
                    $sub->where('name', 'like', "%{$searchDemandeur}%")
                        ->orWhere('prenom', 'like', "%{$searchDemandeur}%");
                });
            });
            $decesQuery->whereHas('user', function ($q) use ($searchDemandeur) {
                $q->where(function ($sub) use ($searchDemandeur) {
                    $sub->where('name', 'like', "%{$searchDemandeur}%")
                        ->orWhere('prenom', 'like', "%{$searchDemandeur}%");
                });
            });
        }

        $naissances = $naissancesQuery->get();
        $mariages = $mariagesQuery->get();
        $deces = $decesQuery->get();

        $feed = collect();

        foreach ($naissances as $n) {
            $feed->push((object)[
                'date' => Carbon::parse($n->created_at),
                'reference' => 'TP-NAIS-' . str_pad($n->id, 5, '0', STR_PAD_LEFT),
                'commune' => $n->commune,
                'type' => 'Naissance',
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $n->montant_timbre ?? 500,
                'status' => 'Transféré',
                'demandeur' => $n->user ? ($n->user->name . ' ' . $n->user->prenom) : 'N/A'
            ]);
        }

        foreach ($mariages as $m) {
            $feed->push((object)[
                'date' => Carbon::parse($m->created_at),
                'reference' => 'TP-MAR-' . str_pad($m->id, 5, '0', STR_PAD_LEFT),
                'commune' => $m->commune,
                'type' => 'Mariage',
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $m->montant_timbre ?? 500,
                'status' => 'Transféré',
                'demandeur' => $m->user ? ($m->user->name . ' ' . $m->user->prenom) : 'N/A'
            ]);
        }

        foreach ($deces as $d) {
            $feed->push((object)[
                'date' => Carbon::parse($d->created_at),
                'reference' => 'TP-DEC-' . str_pad($d->id, 5, '0', STR_PAD_LEFT),
                'commune' => $d->commune,
                'type' => 'Décès',
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $d->montant_timbre ?? 500,
                'status' => 'Transféré',
                'demandeur' => $d->user ? ($d->user->name . ' ' . $d->user->prenom) : 'N/A'
            ]);
        }

        // Extraire la liste de tous les mois uniques de transactions (format 'Y-m') pour alimenter le filtre
        $availableMonths = $feed->map(function ($item) {
            return $item->date->format('Y-m');
        })->unique()->sortDesc()->values();

        // Appliquer le filtre par mois si spécifié
        $selectedMonth = $request->input('month');
        if (!empty($selectedMonth)) {
            $feed = $feed->filter(function ($item) use ($selectedMonth) {
                return $item->date->format('Y-m') === $selectedMonth;
            });
        }

        // Appliquer le filtre par type si spécifié
        $selectedType = $request->input('type');
        if (!empty($selectedType)) {
            $feed = $feed->filter(function ($item) use ($selectedType) {
                return $item->type === $selectedType;
            });
        }

        // Trier les transferts par date décroissante
        $sortedFeed = $feed->sortByDesc('date');

        // Paginer les transactions
        $page = $request->input('page', 1);
        $perPage = 10;
        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedFeed->forPage($page, $perPage),
            $sortedFeed->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.transactions.index', compact(
            'transactions',
            'availableMonths',
            'searchDemandeur',
            'selectedMonth',
            'selectedType'
        ));
    }
}
