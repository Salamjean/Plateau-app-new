<?php

namespace App\Http\Controllers\Mairie;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MairieDashboard extends Controller
{
    private function excludeRetraitSurPlace($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('choix_option')
                ->orWhere('choix_option', '!=', 'Retrait sur place');
        });
    }

    public function dashboard(Request $request)
    {
        Carbon::setLocale('fr');
        // Récupérer l'admin connecté
        $mairie = Auth::guard('mairie')->user();

        // Récupérer le mois et l'année sélectionnés (optionnels)
        $selectedMonth = $request->input('month');
        $selectedYear = $request->input('year');

        // Récupérer le mois et l'année sélectionnés pour les naisshops et deceshops
        $selectedMonthHops = $request->input('month_hops', date('m'));
        $selectedYearHops = $request->input('year_hops', date('Y'));

        // Requêtes de base
        $naissancesQuery = Naissance::where('commune', $mairie->name)->paye();
        $decesQuery = Deces::where('commune', $mairie->name)->paye();
        $mariagesQuery = Mariage::where('commune', $mairie->name)->paye();

        // Appliquer les filtres si présents
        if ($selectedMonth) {
            $naissancesQuery->whereMonth('created_at', $selectedMonth);
            $decesQuery->whereMonth('created_at', $selectedMonth);
            $mariagesQuery->whereMonth('created_at', $selectedMonth);
        }
        if ($selectedYear) {
            $naissancesQuery->whereYear('created_at', $selectedYear);
            $decesQuery->whereYear('created_at', $selectedYear);
            $mariagesQuery->whereYear('created_at', $selectedYear);
        }

        $naissances = $naissancesQuery->orderBy('created_at', 'desc')->get();
        $deces = $decesQuery->orderBy('created_at', 'desc')->get();
        $mariages = $mariagesQuery->orderBy('created_at', 'desc')->get();

        // Calcul des données globales
        $totalData = $naissances->count() + $deces->count() + $mariages->count();

        // Pourcentages
        $naissancePercentage = $totalData > 0 ? ($naissances->count() / $totalData) * 100 : 0;
        $decesPercentage = $totalData > 0 ? ($deces->count() / $totalData) * 100 : 0;
        $mariagePercentage = $totalData > 0 ? ($mariages->count() / $totalData) * 100 : 0;

        $NaissP = $naissancePercentage;
        $DecesP = $decesPercentage;

        // Données pour le tableau de bord
        $naissancedash = $naissances->count();
        $decesdash = $deces->count();
        $mariagedash = $mariages->count();
        $Naiss = $naissancedash;
        $Dece = $decesdash;

        // Récupération des données récentes (3 derniers éléments)
        $recentNaissances = $naissances->take(2);
        $recentDeces = $deces->take(2);
        $recentMariages = $mariages->take(2);

        // Calcul dynamique du solde du portefeuille (cumul du mois en cours)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $naissancesMonth = $this->excludeRetraitSurPlace(
            Naissance::where('commune', $mairie->name)
                ->paye()
        )
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalNaissanceMonth = $naissancesMonth->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $mariagesMonth = $this->excludeRetraitSurPlace(
            Mariage::where('commune', $mairie->name)
                ->paye()
        )
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalMariageMonth = $mariagesMonth->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $decesMonth = $this->excludeRetraitSurPlace(
            Deces::where('commune', $mairie->name)
                ->paye()
        )
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalDecesMonth = $decesMonth->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $soldePortefeuille = $totalNaissanceMonth + $totalMariageMonth + $totalDecesMonth;

        // Retourne la vue avec les données
        return view('mairie.dashboard', compact(
            'mairie',
            'naissancedash',
            'decesdash',
            'NaissP',
            'DecesP',
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
            'selectedMonthHops',
            'selectedYearHops',
            'soldePortefeuille'
        ));
    }

    public function logout()
    {
        Auth::guard('mairie')->logout();
        return redirect()->route('mairie.login');
    }

    public function salesTimbre(Request $request)
    {
        $mairie = Auth::guard('mairie')->user();

        // On récupère les ventes de timbres liées à cette mairie
        $finances = \App\Models\Finance::where('mairie_id', $mairie->id)->get();
        $financeIds = $finances->pluck('id');
        $comptables = \App\Models\Comptable::whereIn('finance_id', $financeIds)->get();
        $comptableIds = $comptables->pluck('id');

        $query = \App\Models\Timbre::where(function ($q) use ($financeIds, $comptableIds) {
            $q->whereIn('finance_id', $financeIds)
                ->orWhereIn('comptable_id', $comptableIds);
        })->where('nombre_timbre', '<', 0); // Uniquement les ventes

        // Filtrage par mois
        $selectedMonth = $request->input('month');
        if ($selectedMonth) {
            $query->whereMonth('created_at', $selectedMonth);
            $query->whereYear('created_at', date('Y'));
        }

        // Filtrage par comptable
        $selectedComptable = $request->input('comptable_id');
        if ($selectedComptable) {
            $query->where('comptable_id', $selectedComptable);
        }

        // Statistiques (sur la requête filtrée pour certaines, ou globales si nécessaire)
        // Note: On clone pour garder les stats globales ou filtrées selon le besoin
        // Ici on va garder les stats globales pour les cartes, mais la liste sera filtrée
        $statsQuery = clone $query;

        $totalTimbresVendus = abs((clone $query)->sum('nombre_timbre'));
        $totalRecettes = $totalTimbresVendus * 500;

        $ventesAujourdhui = abs((clone $query)->whereDate('created_at', today())->sum('nombre_timbre'));
        $ventesMois = abs((clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('nombre_timbre'));

        // Statistiques du stock (Solde) - Toujours global à la mairie
        $soldeMontant = $mairie->solde ?? 0;
        $soldeTimbres = $soldeMontant > 0 ? floor($soldeMontant / 500) : 0;

        // Liste des transactions filtrées
        $ventes = $query->with(['finance', 'comptable'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('mairie.ventes_timbre', compact(
            'mairie',
            'ventes',
            'totalTimbresVendus',
            'totalRecettes',
            'ventesAujourdhui',
            'ventesMois',
            'soldeMontant',
            'soldeTimbres',
            'comptables',
            'selectedMonth',
            'selectedComptable'
        ));
    }

    public function portefeuille(Request $request)
    {
        $mairie = Auth::guard('mairie')->user();

        // Calcul dynamique des timbres Naissance perçus en ligne
        $naissances = $this->excludeRetraitSurPlace(
            Naissance::where('commune', $mairie->name)
                ->paye()
        )
            ->with('user')
            ->get();
        $totalNaissance = $naissances->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        // Calcul dynamique des timbres Mariage perçus en ligne
        $mariages = $this->excludeRetraitSurPlace(
            Mariage::where('commune', $mairie->name)
                ->paye()
        )
            ->with('user')
            ->get();
        $totalMariage = $mariages->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        // Calcul dynamique des timbres Décès perçus en ligne
        $deces = $this->excludeRetraitSurPlace(
            Deces::where('commune', $mairie->name)
                ->paye()
        )
            ->with('user')
            ->get();
        $totalDeces = $deces->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        // Somme totale brute perçue en ligne
        $totalPerçuEnLigne = $totalNaissance + $totalMariage + $totalDeces;

        // Solution 1 : Tout est automatiquement et instantanément reversé à TrésorPay.
        // Donc le total reversé est égal au total perçu en ligne.
        $totalReversements = $totalPerçuEnLigne;

        // Calcul dynamique du cumul du mois en cours
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $naissancesMonth = $this->excludeRetraitSurPlace(
            Naissance::where('commune', $mairie->name)
                ->paye()
        )
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalNaissanceMonth = $naissancesMonth->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $mariagesMonth = $this->excludeRetraitSurPlace(
            Mariage::where('commune', $mairie->name)
                ->paye()
        )
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalMariageMonth = $mariagesMonth->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $decesMonth = $this->excludeRetraitSurPlace(
            Deces::where('commune', $mairie->name)
                ->paye()
        )
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalDecesMonth = $decesMonth->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $soldePortefeuille = $totalNaissanceMonth + $totalMariageMonth + $totalDecesMonth;

        // Récupérer les derniers paiements réels de timbres pour alimenter l'historique des reversements instantanés
        $derniersPaiements = collect();

        foreach ($naissances as $n) {
            $derniersPaiements->push((object)[
                'date' => Carbon::parse($n->created_at),
                'reference' => 'TP-NAIS-' . str_pad($n->id, 5, '0', STR_PAD_LEFT),
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $n->montant_timbre ?? 500,
                'status' => 'Transféré'
            ]);
        }

        foreach ($mariages as $m) {
            $derniersPaiements->push((object)[
                'date' => Carbon::parse($m->created_at),
                'reference' => 'TP-MAR-' . str_pad($m->id, 5, '0', STR_PAD_LEFT),
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $m->montant_timbre ?? 500,
                'status' => 'Transféré'
            ]);
        }

        foreach ($deces as $d) {
            $derniersPaiements->push((object)[
                'date' => Carbon::parse($d->created_at),
                'reference' => 'TP-DEC-' . str_pad($d->id, 5, '0', STR_PAD_LEFT),
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $d->montant_timbre ?? 500,
                'status' => 'Transféré'
            ]);
        }

        $sortedFeed = $derniersPaiements->sortByDesc('date');
        $totalTransactionsCount = $sortedFeed->count();
        $transactions = $sortedFeed->take(5);

        // Comptabilisation mensuelle des timbres reçus par TrésorPay pour l'année en cours
        $currentYear = Carbon::now()->year;
        $comptabiliteMensuelle = [];

        for ($m = 1; $m <= 12; $m++) {
            $comptabiliteMensuelle[$m] = [
                'nom' => ucfirst(Carbon::create(null, $m, 1)->locale('fr')->translatedFormat('F')),
                'montant' => 0
            ];
        }

        foreach ($naissances as $item) {
            $date = Carbon::parse($item->created_at);
            if ($date->year == $currentYear) {
                $comptabiliteMensuelle[$date->month]['montant'] += $item->montant_timbre ?? 500;
            }
        }

        foreach ($mariages as $item) {
            $date = Carbon::parse($item->created_at);
            if ($date->year == $currentYear) {
                $comptabiliteMensuelle[$date->month]['montant'] += $item->montant_timbre ?? 500;
            }
        }

        foreach ($deces as $item) {
            $date = Carbon::parse($item->created_at);
            if ($date->year == $currentYear) {
                $comptabiliteMensuelle[$date->month]['montant'] += $item->montant_timbre ?? 500;
            }
        }

        return view('mairie.portefeuille', compact(
            'mairie',
            'soldePortefeuille',
            'totalPerçuEnLigne',
            'totalNaissance',
            'totalMariage',
            'totalDeces',
            'totalReversements',
            'transactions',
            'totalTransactionsCount',
            'comptabiliteMensuelle',
            'currentYear'
        ));
    }

    public function historiqueReversements(Request $request)
    {
        $mairie = Auth::guard('mairie')->user();

        // Récupérer toutes les demandes payées pour reconstituer l'historique des transferts complets
        $naissances = $this->excludeRetraitSurPlace(
            Naissance::where('commune', $mairie->name)
                ->paye()
        )
            ->with('user')
            ->get();

        $mariages = $this->excludeRetraitSurPlace(
            Mariage::where('commune', $mairie->name)
                ->paye()
        )
            ->with('user')
            ->get();

        $deces = $this->excludeRetraitSurPlace(
            Deces::where('commune', $mairie->name)
                ->paye()
        )
            ->with('user')
            ->get();

        $feed = collect();

        foreach ($naissances as $n) {
            $feed->push((object)[
                'date' => Carbon::parse($n->created_at),
                'reference' => 'TP-NAIS-' . str_pad($n->id, 5, '0', STR_PAD_LEFT),
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $n->montant_timbre ?? 500,
                'status' => 'Transféré'
            ]);
        }

        foreach ($mariages as $m) {
            $feed->push((object)[
                'date' => Carbon::parse($m->created_at),
                'reference' => 'TP-MAR-' . str_pad($m->id, 5, '0', STR_PAD_LEFT),
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $m->montant_timbre ?? 500,
                'status' => 'Transféré'
            ]);
        }

        foreach ($deces as $d) {
            $feed->push((object)[
                'date' => Carbon::parse($d->created_at),
                'reference' => 'TP-DEC-' . str_pad($d->id, 5, '0', STR_PAD_LEFT),
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $d->montant_timbre ?? 500,
                'status' => 'Transféré'
            ]);
        }

        // Extraire la liste de tous les mois uniques de transactions (format 'Y-m') pour alimenter le filtre
        $availableMonths = $feed->map(function ($item) {
            return $item->date->format('Y-m');
        })->unique()->sortDesc()->values();

        // Extraire la liste de tous les ans uniques de transactions (format 'Y') pour l'export PDF
        $availableYears = $feed->map(function ($item) {
            return $item->date->format('Y');
        })->unique()->sortDesc()->values();

        // Appliquer le filtre par mois si spécifié
        $selectedMonth = $request->input('month');
        if (!empty($selectedMonth)) {
            $feed = $feed->filter(function ($item) use ($selectedMonth) {
                return $item->date->format('Y-m') === $selectedMonth;
            });
        }

        // Trier les transferts par date décroissante
        $sortedFeed = $feed->sortByDesc('date');

        // Paginer l'historique complet par lot de 10
        $page = $request->input('page', 1);
        $perPage = 10;
        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedFeed->forPage($page, $perPage),
            $sortedFeed->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('mairie.portefeuille_historique', compact(
            'mairie',
            'transactions',
            'availableMonths',
            'availableYears'
        ));
    }

    public function exportPDF(Request $request)
    {
        $mairie = Auth::guard('mairie')->user();
        $commune = $mairie->name; // Mairie name contient le nom de la commune
        $year = $request->input('year', Carbon::now()->format('Y'));

        // Récupérer toutes les demandes payées pour reconstituer l'historique des transferts complets
        $naissances = Naissance::where('commune', $mairie->name)
            ->paye()
            ->get();

        $mariages = Mariage::where('commune', $mairie->name)
            ->paye()
            ->get();

        $deces = Deces::where('commune', $mairie->name)
            ->paye()
            ->get();

        $feed = collect();

        foreach ($naissances as $n) {
            $feed->push((object)[
                'date' => Carbon::parse($n->created_at),
                'reference' => 'TP-NAIS-' . str_pad($n->id, 5, '0', STR_PAD_LEFT),
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $n->montant_timbre ?? 500,
                'status' => 'Transféré'
            ]);
        }

        foreach ($mariages as $m) {
            $feed->push((object)[
                'date' => Carbon::parse($m->created_at),
                'reference' => 'TP-MAR-' . str_pad($m->id, 5, '0', STR_PAD_LEFT),
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $m->montant_timbre ?? 500,
                'status' => 'Transféré'
            ]);
        }

        foreach ($deces as $d) {
            $feed->push((object)[
                'date' => Carbon::parse($d->created_at),
                'reference' => 'TP-DEC-' . str_pad($d->id, 5, '0', STR_PAD_LEFT),
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $d->montant_timbre ?? 500,
                'status' => 'Transféré'
            ]);
        }

        // Filtrer par année sélectionnée
        $feed = $feed->filter(function ($item) use ($year) {
            return $item->date->format('Y') === $year;
        });

        // Préparer le rapport mensuel pour cette année (de janvier à décembre) avec traduction française robuste
        $frenchMonths = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre'
        ];

        $monthlyReport = [];
        for ($m = 1; $m <= 12; $m++) {
            $date = Carbon::create($year, $m, 1);
            $monthKey = $date->format('Y-m');
            $monthLabel = $frenchMonths[$m];

            $monthTransactions = $feed->filter(function ($item) use ($monthKey) {
                return $item->date->format('Y-m') === $monthKey;
            });

            $monthlyReport[] = [
                'label' => $monthLabel,
                'count' => $monthTransactions->count(),
                'total_montant' => $monthTransactions->sum('montant')
            ];
        }

        $userName = $mairie->name;
        $roleLabel = 'Mairie';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reversements_annuels', compact(
            'year',
            'commune',
            'userName',
            'roleLabel',
            'monthlyReport'
        ));

        return $pdf->download('transferts_mairie_' . $year . '.pdf');
    }

    public function reverserPortefeuille(Request $request)
    {
        $request->validate([
            'montant' => 'required|numeric|min:1',
            'credential_id' => 'required|string'
        ]);

        $mairie = Auth::guard('mairie')->user();
        $montant = (float) $request->input('montant');
        $credentialId = $request->input('credential_id');

        // Récupérer les reversements actuels en session
        $reversements = session()->get('mairie_reversements_' . $mairie->id, []);

        // Enregistrer le nouveau reversement
        $reversements[] = [
            'reference' => 'REV-' . strtoupper(bin2hex(random_bytes(4))),
            'montant' => $montant,
            'status' => 'SUCCES',
            'destinataire' => $credentialId,
            'date' => now()->toDateTimeString()
        ];

        session()->put('mairie_reversements_' . $mairie->id, $reversements);

        return response()->json([
            'status' => 'success',
            'message' => 'Reversement de ' . number_format($montant) . ' XOF effectué avec succès vers TrésorPay.'
        ]);
    }
}
