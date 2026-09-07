<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\Paiement;
use App\Models\Timbre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComptableDashboard extends Controller
{
    private function excludeRetraitSurPlace($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($sub) {
                $sub->whereNull('choix_option')
                    ->orWhere('choix_option', '!=', 'Retrait sur place');
            })->orWhere(function ($sub) {
                $sub->where('choix_option', 'Retrait sur place')
                    ->where('montant_timbre', '>', 0);
            });
        })->where(function ($q) {
            $q->whereNull('is_free_request')
                ->orWhere('is_free_request', '!=', 1);
        });
    }

    public function dashboard()
    {
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

        // Récupérer les demandes récentes (4 de chaque type)
        $naissancesRecent = Naissance::where('commune', $commune)->latest()->take(4)->get()->map(function ($item) {
            $item->type_demande = 'naissance';
            return $item;
        });
        $decesRecent = Deces::where('commune', $commune)->latest()->take(4)->get()->map(function ($item) {
            $item->type_demande = 'deces';
            return $item;
        });
        $mariagesRecent = Mariage::where('commune', $commune)->latest()->take(4)->get()->map(function ($item) {
            $item->type_demande = 'mariage';
            return $item;
        });

        // Fusionner et trier par date décroissante, puis limiter à 4 au total
        $recentDemandes = $naissancesRecent->concat($decesRecent)->concat($mariagesRecent)
            ->sortByDesc('created_at')
            ->take(4);

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

        // Montant ajouté par l'admin (Solde Mairie)
        // Le solde affiché correspond exactement au solde de la mairie, qui est déjà débité 
        // par ComptableDemandeController lors de la validation des sorties
        $montantTotalAjoute = 0;
        if ($comptable->finance && $comptable->finance->mairie) {
            $montantTotalAjoute = $comptable->finance->mairie->solde;
        }

        // Le solde restant est directement le solde actuel de la mairie
        $montantRestant = $montantTotalAjoute;

        // Calcul dynamique du solde du portefeuille (cumul du mois en cours)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $naissancesPaiementsMonth = Paiement::whereHas('naissance', function ($query) use ($commune) {
            $query->where('commune', $commune);
        })
            ->where('status', 'ACCEPTED')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalNaissanceMonth = $naissancesPaiementsMonth->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $mariagesPaiementsMonth = Paiement::whereHas('mariage', function ($query) use ($commune) {
            $query->where('commune', $commune);
        })
            ->where('status', 'ACCEPTED')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalMariageMonth = $mariagesPaiementsMonth->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $decesPaiementsMonth = Paiement::whereHas('deces', function ($query) use ($commune) {
            $query->where('commune', $commune);
        })
            ->where('status', 'ACCEPTED')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalDecesMonth = $decesPaiementsMonth->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $soldePortefeuille = $totalNaissanceMonth + $totalMariageMonth + $totalDecesMonth;

        return view(
            'comptable.dashboard',
            compact(
                'total',
                'decesnombre',
                'naissancenombre',
                'mariagenombre',
                'recentDemandes',
                'weeklyData',
                'monthlyData',
                'yearlyData',
                'decesMois',
                'mariageMois',
                'naissanceMois',
                'totalMois',
                'totalAujourdhui',
                'totalSemaine',
                'decesAujourdhui',
                'mariageAujourdhui',
                'naissanceAujourdhui',
                'decesSemaine',
                'mariageSemaine',
                'naissanceSemaine',
                // Données timbres
                'timbresAujourdhui',
                'timbresSemaine',
                'timbresMois',
                'montantAujourdhui',
                'montantSemaine',
                'montantMois',
                'soldeTimbres',
                'tendanceTimbres',
                'labelsTimbres',
                'valeursTimbres',
                'dernieresVentesTimbres',
                'montantTotalAjoute',
                'montantRestant', // Variable calculée
                'soldePortefeuille'
            )
        );
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

    private function getCommunePaymentsFeed($commune)
    {
        $payments = Paiement::where(function ($query) {
            $query->whereNotNull('naissance_id')
                ->orWhereNotNull('mariage_id')
                ->orWhereNotNull('deces_id');
        })
            ->where('status', 'ACCEPTED')
            ->where(function ($query) use ($commune) {
                $query->whereHas('naissance', function ($q) use ($commune) {
                    $q->where('commune', $commune);
                })
                    ->orWhereHas('mariage', function ($q) use ($commune) {
                        $q->where('commune', $commune);
                    })
                    ->orWhereHas('deces', function ($q) use ($commune) {
                        $q->where('commune', $commune);
                    });
            })
            ->with(['naissance', 'mariage', 'deces'])
            ->get();

        $feed = collect();
        foreach ($payments as $p) {
            $typeLabel = '';
            $relation = '';
            if ($p->naissance_id) {
                $typeLabel = 'NAIS';
                $relation = 'naissance';
            } elseif ($p->mariage_id) {
                $typeLabel = 'MAR';
                $relation = 'mariage';
            } elseif ($p->deces_id) {
                $typeLabel = 'DEC';
                $relation = 'deces';
            } else {
                continue;
            }

            $isModification = str_contains($p->transaction_id, '-MOD-');
            $partTimbre = $this->getPaymentPartTimbre($p);
            $totalPayment = (float)$p->montant;

            $feed->push((object)[
                'date' => Carbon::parse($p->paid_at ?? $p->created_at),
                'reference' => $p->transaction_id,
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $totalPayment,
                'part_timbre' => $partTimbre,
                'part_livraison' => max(0, $totalPayment - $partTimbre),
                'status' => 'Transféré',
                'payment_method' => $p->operator_id
            ]);
        }

        return $feed;
    }

    public function portefeuille(Request $request)
    {
        $comptable = Auth::guard('comptable')->user();
        $commune = $comptable->communeM;

        // Calcul dynamique des timbres Naissance perçus en ligne
        $naissancesPaiements = Paiement::whereHas('naissance', function ($query) use ($commune) {
            $query->where('commune', $commune);
        })
            ->where('status', 'ACCEPTED')
            ->with('naissance')
            ->get();
        $totalNaissance = $naissancesPaiements->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        // Calcul dynamique des timbres Mariage perçus en ligne
        $mariagesPaiements = Paiement::whereHas('mariage', function ($query) use ($commune) {
            $query->where('commune', $commune);
        })
            ->where('status', 'ACCEPTED')
            ->with('mariage')
            ->get();
        $totalMariage = $mariagesPaiements->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        // Calcul dynamique des timbres Décès perçus en ligne
        $decesPaiements = Paiement::whereHas('deces', function ($query) use ($commune) {
            $query->where('commune', $commune);
        })
            ->where('status', 'ACCEPTED')
            ->with('deces')
            ->get();
        $totalDeces = $decesPaiements->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        // Somme totale brute perçue en ligne
        $totalPerçuEnLigne = $totalNaissance + $totalMariage + $totalDeces;

        // Solution 1 : Tout est automatiquement et instantanément reversé à TrésorPay.
        $totalReversements = $totalPerçuEnLigne;

        // Calcul dynamique du cumul du mois sélectionné
        $selectedMonth = $request->input('month');
        if ($selectedMonth) {
            try {
                $date = Carbon::parse($selectedMonth . '-01');
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();
            } catch (\Exception $e) {
                $startOfMonth = Carbon::now()->startOfMonth();
                $endOfMonth = Carbon::now()->endOfMonth();
                $selectedMonth = Carbon::now()->format('Y-m');
            }
        } else {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $selectedMonth = Carbon::now()->format('Y-m');
        }

        $naissanceMonthPaiements = $naissancesPaiements->whereBetween('paid_at', [$startOfMonth, $endOfMonth]);
        $totalNaissanceMonth = $naissanceMonthPaiements->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });
        $naissanceWaveMonth = $naissanceMonthPaiements->filter(fn($p) => strtolower($p->operator_id) === 'wave')->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });
        $naissanceTresorpayMonth = $naissanceMonthPaiements->filter(fn($p) => strtolower($p->operator_id) === 'tresorpay')->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $mariageMonthPaiements = $mariagesPaiements->whereBetween('paid_at', [$startOfMonth, $endOfMonth]);
        $totalMariageMonth = $mariageMonthPaiements->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });
        $mariageWaveMonth = $mariageMonthPaiements->filter(fn($p) => strtolower($p->operator_id) === 'wave')->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });
        $mariageTresorpayMonth = $mariageMonthPaiements->filter(fn($p) => strtolower($p->operator_id) === 'tresorpay')->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $decesMonthPaiements = $decesPaiements->whereBetween('paid_at', [$startOfMonth, $endOfMonth]);
        $totalDecesMonth = $decesMonthPaiements->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });
        $decesWaveMonth = $decesMonthPaiements->filter(fn($p) => strtolower($p->operator_id) === 'wave')->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });
        $decesTresorpayMonth = $decesMonthPaiements->filter(fn($p) => strtolower($p->operator_id) === 'tresorpay')->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $soldePortefeuille = $totalNaissanceMonth + $totalMariageMonth + $totalDecesMonth;

        // Calcul des paiements Wave et TresorPay
        $allCommunePaiementsMonth = Paiement::where(function ($query) {
            $query->whereNotNull('naissance_id')
                ->orWhereNotNull('mariage_id')
                ->orWhereNotNull('deces_id');
        })
            ->where('status', 'ACCEPTED')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->where(function ($query) use ($commune) {
                $query->whereHas('naissance', function ($q) use ($commune) {
                    $q->where('commune', $commune);
                })
                    ->orWhereHas('mariage', function ($q) use ($commune) {
                        $q->where('commune', $commune);
                    })
                    ->orWhereHas('deces', function ($q) use ($commune) {
                        $q->where('commune', $commune);
                    });
            })
            ->get();

        $statsWave = ['total' => 0, 'timbre' => 0, 'livraison' => 0];
        $statsTresorpay = ['total' => 0, 'timbre' => 0, 'livraison' => 0];
        $statsStripe = ['total' => 0, 'timbre' => 0, 'livraison' => 0];

        foreach ($allCommunePaiementsMonth as $p) {
            $operator = strtolower($p->operator_id);
            if ($operator === 'wave' || $operator === 'tresorpay' || $operator === 'stripe') {
                $t = $this->getPaymentPartTimbre($p);
                $total = (float)$p->montant;
                $l = max(0, $total - $t);

                if ($operator === 'wave') {
                    $statsWave['total'] += $total;
                    $statsWave['timbre'] += $t;
                    $statsWave['livraison'] += $l;
                } elseif ($operator === 'tresorpay') {
                    $statsTresorpay['total'] += $total;
                    $statsTresorpay['timbre'] += $t;
                    $statsTresorpay['livraison'] += $l;
                } elseif ($operator === 'stripe') {
                    $statsStripe['total'] += $total;
                    $statsStripe['timbre'] += $t;
                    $statsStripe['livraison'] += $l;
                }
            }
        }

        // Récupérer les derniers paiements réels de timbres pour alimenter l'historique des reversements instantanés
        $derniersPaiements = $this->getCommunePaymentsFeed($commune);

        $historiqueFiltre = $derniersPaiements->filter(function ($item) use ($startOfMonth, $endOfMonth) {
            return Carbon::parse($item->date)->between($startOfMonth, $endOfMonth);
        });

        $sortedFeed = $historiqueFiltre->sortByDesc('date');
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

        foreach ($derniersPaiements as $item) {
            $date = Carbon::parse($item->date);
            if ($date->year == $currentYear && strtolower($item->payment_method) === 'tresorpay') {
                $comptabiliteMensuelle[$date->month]['montant'] += (float) $item->part_timbre;
            }
        }

        return view('comptable.portefeuille', compact(
            'comptable',
            'soldePortefeuille',
            'totalPerçuEnLigne',
            'totalNaissance',
            'totalMariage',
            'totalDeces',
            'totalReversements',
            'transactions',
            'totalTransactionsCount',
            'statsWave',
            'statsTresorpay',
            'statsStripe',
            'totalNaissanceMonth',
            'totalMariageMonth',
            'totalDecesMonth',
            'naissanceWaveMonth',
            'selectedMonth',
            'naissanceTresorpayMonth',
            'mariageWaveMonth',
            'mariageTresorpayMonth',
            'decesWaveMonth',
            'decesTresorpayMonth',
            'comptabiliteMensuelle',
            'currentYear'
        ));
    }

    public function historiqueReversements(Request $request)
    {
        $comptable = Auth::guard('comptable')->user();
        $commune = $comptable->communeM;

        // Récupérer les transactions de timbres réels à partir des paiements
        $feed = $this->getCommunePaymentsFeed($commune);

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

        return view('comptable.portefeuille_historique', compact(
            'comptable',
            'transactions',
            'availableMonths',
            'availableYears'
        ));
    }

    public function exportPDF(Request $request)
    {
        $comptable = Auth::guard('comptable')->user();
        $commune = $comptable->communeM;
        $year = $request->input('year', Carbon::now()->format('Y'));

        // Récupérer les transferts de timbres réels à partir des paiements
        $feed = $this->getCommunePaymentsFeed($commune);

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

        $userName = $comptable->name;
        $roleLabel = 'Comptable';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reversements_annuels', compact(
            'year',
            'commune',
            'userName',
            'roleLabel',
            'monthlyReport'
        ));

        return $pdf->download('transferts_comptable_' . $year . '.pdf');
    }

    public function reverserPortefeuille(Request $request)
    {
        $request->validate([
            'montant' => 'required|numeric|min:1',
            'credential_id' => 'required|string'
        ]);

        $comptable = Auth::guard('comptable')->user();
        $commune = $comptable->communeM;
        $montant = (float) $request->input('montant');
        $credentialId = $request->input('credential_id');

        // Trouver la mairie pour obtenir son id
        $mairieObj = \App\Models\Mairie::where('name', $commune)->first();
        if (!$mairieObj) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mairie introuvable pour cette commune.'
            ], 404);
        }

        // Récupérer les reversements actuels en session
        $reversements = session()->get('mairie_reversements_' . $mairieObj->id, []);

        // Enregistrer le nouveau reversement
        $reversements[] = [
            'reference' => 'REV-' . strtoupper(bin2hex(random_bytes(4))),
            'montant' => $montant,
            'status' => 'SUCCES',
            'destinataire' => $credentialId,
            'date' => now()->toDateTimeString()
        ];

        session()->put('mairie_reversements_' . $mairieObj->id, $reversements);

        return response()->json([
            'status' => 'success',
            'message' => 'Reversement de ' . number_format($montant) . ' XOF effectué avec succès vers TrésorPay.'
        ]);
    }

    public function logout()
    {
        Auth::guard('comptable')->logout();
        return redirect()->route('comptable.login');
    }

    /**
     * Calcule uniquement le montant des timbres (part_timbre) pour un paiement donné,
     * en excluant tout montant de livraison.
     *
     * @param \App\Models\Paiement $p
     * @return float
     */
    private function getPaymentPartTimbre($p): float
    {
        if (isset($p->raw_response['part_timbre'])) {
            return (float) $p->raw_response['part_timbre'];
        }

        $isModification = str_contains((string) $p->transaction_id, '-MOD-');
        $montant = (float) $p->montant;

        // Déterminer la relation
        $relation = null;
        if ($p->naissance_id) {
            $relation = 'naissance';
        } elseif ($p->mariage_id) {
            $relation = 'mariage';
        } elseif ($p->deces_id) {
            $relation = 'deces';
        } elseif ($p->naissance_groupe_id || $p->mariage_groupe_id || $p->deces_groupe_id) {
            // Les demandes groupées n'ont pas d'option de livraison individuelle
            return $montant;
        }

        if (!$relation) {
            return $montant;
        }

        $demande = $p->$relation;
        if (!$demande) {
            return $montant;
        }

        if ($demande->choix_option !== 'livraison') {
            return $montant;
        }

        // Si la demande est en mode livraison, une seule des transactions de paiement accepted
        // doit supporter le coût de la livraison (1500 FCFA).
        // On récupère tous les paiements ACCEPTED pour cette demande.
        $allPayments = Paiement::where("{$relation}_id", $demande->id)
            ->where('status', 'ACCEPTED')
            ->orderBy('id', 'asc')
            ->get();

        if ($allPayments->isEmpty()) {
            return $isModification ? $montant : max(0.0, $montant - 1500.0);
        }

        // Si l'un des paiements a déjà explicitement une part_livraison stockée en DB
        $deliveryPaymentId = null;
        foreach ($allPayments as $pay) {
            if (isset($pay->raw_response['part_livraison']) && (float)$pay->raw_response['part_livraison'] > 0) {
                $deliveryPaymentId = $pay->id;
                break;
            }
        }

        if ($deliveryPaymentId !== null) {
            if ($p->id === $deliveryPaymentId) {
                $partLivraison = (float)$p->raw_response['part_livraison'];
                return max(0.0, $montant - $partLivraison);
            }
            return $montant;
        }

        // Pour les anciens paiements (legacy) sans part_livraison enregistrée:
        if ($allPayments->count() === 1) {
            return max(0.0, $montant - 1500.0);
        }

        $modificationPayment = $allPayments->first(function ($pay) {
            return str_contains((string) $pay->transaction_id, '-MOD-');
        });

        $initialPayment = $allPayments->first(function ($pay) {
            return !str_contains((string) $pay->transaction_id, '-MOD-');
        });

        if ($modificationPayment && $initialPayment) {
            // Si le paiement initial est trop faible pour contenir la livraison, la livraison a été payée lors de la modif
            if ((float)$initialPayment->montant < 1500.0) {
                if ($p->id === $modificationPayment->id) {
                    return max(0.0, $montant - 1500.0);
                }
                return $montant;
            }
            // Sinon, assumez que le paiement initial a payé la livraison
            if ($p->id === $initialPayment->id) {
                return max(0.0, $montant - 1500.0);
            }
            return $montant;
        }

        // Par défaut, déduire les 1500 de la toute première transaction acceptée
        if ($p->id === $allPayments->first()->id) {
            return max(0.0, $montant - 1500.0);
        }

        return $montant;
    }
}
