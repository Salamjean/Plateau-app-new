<?php

namespace App\Http\Controllers\Mairie;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\Paiement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MairieDashboard extends Controller
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

        $naissancesPaiementsMonth = Paiement::whereHas('naissance', function ($query) use ($mairie) {
            $query->where('commune', $mairie->name);
        })
            ->where('status', 'ACCEPTED')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalNaissanceMonth = $naissancesPaiementsMonth->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $mariagesPaiementsMonth = Paiement::whereHas('mariage', function ($query) use ($mairie) {
            $query->where('commune', $mairie->name);
        })
            ->where('status', 'ACCEPTED')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalMariageMonth = $mariagesPaiementsMonth->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $decesPaiementsMonth = Paiement::whereHas('deces', function ($query) use ($mairie) {
            $query->where('commune', $mairie->name);
        })
            ->where('status', 'ACCEPTED')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->get();
        $totalDecesMonth = $decesPaiementsMonth->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
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
        $mairie = Auth::guard('mairie')->user();

        // Calcul dynamique des timbres Naissance perçus en ligne
        $naissancesPaiements = Paiement::whereHas('naissance', function ($query) use ($mairie) {
            $query->where('commune', $mairie->name);
        })
            ->where('status', 'ACCEPTED')
            ->with('naissance')
            ->get();
        $totalNaissance = $naissancesPaiements->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        // Calcul dynamique des timbres Mariage perçus en ligne
        $mariagesPaiements = Paiement::whereHas('mariage', function ($query) use ($mairie) {
            $query->where('commune', $mairie->name);
        })
            ->where('status', 'ACCEPTED')
            ->with('mariage')
            ->get();
        $totalMariage = $mariagesPaiements->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        // Calcul dynamique des timbres Décès perçus en ligne
        $decesPaiements = Paiement::whereHas('deces', function ($query) use ($mairie) {
            $query->where('commune', $mairie->name);
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
            ->where(function ($query) use ($mairie) {
                $query->whereHas('naissance', function ($q) use ($mairie) {
                    $q->where('commune', $mairie->name);
                })
                    ->orWhereHas('mariage', function ($q) use ($mairie) {
                        $q->where('commune', $mairie->name);
                    })
                    ->orWhereHas('deces', function ($q) use ($mairie) {
                        $q->where('commune', $mairie->name);
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
        $derniersPaiements = $this->getCommunePaymentsFeed($mairie->name);

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
            'currentYear',
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
            'decesTresorpayMonth'
        ));
    }

    public function historiqueReversements(Request $request)
    {
        $mairie = Auth::guard('mairie')->user();

        // Récupérer les transactions de timbres réels à partir des paiements
        $feed = $this->getCommunePaymentsFeed($mairie->name);

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
                'total_montant' => $monthTransactions->sum('part_timbre')
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
