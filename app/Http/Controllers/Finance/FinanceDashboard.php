<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\Paiement;
use App\Models\Timbre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceDashboard extends Controller
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

        // Calculs financiers alignés sur le tableau de bord Admin
        $mairie = $finance->mairie;

        // Le solde de la mairie est directement géré dans la DB par markRecovered()
        // Il est déjà débité à chaque "Valider Sortie" (quantite * 500 FCFA)
        // Donc on l'utilise directement comme solde restant

        // Montant restant = solde actuel de la mairie (déjà à jour)
        $montantRestant = $mairie->solde ?? 0;

        // 2. Montant Total Débité (500 FCFA par TIMBRE) - pour affichage informatif uniquement
        // On utilise sum('quantite') pour compter le vrai nombre de timbres utilisés

        $naissDebit = Naissance::where('commune', $finance->communeM)
            ->where(function ($q) {
                $q->where('choix_option', '!=', 'Livraison')
                    ->orWhere('timbre_recupere', 1);
            })->sum('quantite') ?: Naissance::where('commune', $finance->communeM)
            ->where(function ($q) {
                $q->where('choix_option', '!=', 'Livraison')
                    ->orWhere('timbre_recupere', 1);
            })->count();

        $decesDebit = Deces::where('commune', $finance->communeM)
            ->where(function ($q) {
                $q->where('choix_option', '!=', 'Livraison')
                    ->orWhere('timbre_recupere', 1);
            })->sum('quantite') ?: Deces::where('commune', $finance->communeM)
            ->where(function ($q) {
                $q->where('choix_option', '!=', 'Livraison')
                    ->orWhere('timbre_recupere', 1);
            })->count();

        $mariageDebit = Mariage::where('commune', $finance->communeM)
            ->where(function ($q) {
                $q->where('choix_option', '!=', 'Livraison')
                    ->orWhere('timbre_recupere', 1);
            })->sum('quantite') ?: Mariage::where('commune', $finance->communeM)
            ->where(function ($q) {
                $q->where('choix_option', '!=', 'Livraison')
                    ->orWhere('timbre_recupere', 1);
            })->count();

        $totalDebiteCount = $naissDebit + $decesDebit + $mariageDebit;
        $montantTotalDebite = $totalDebiteCount * 500;

        // Montant total ajouté (pour affichage informatif : solde actuel + ce qui a été débité)
        $montantTotalAjoute = $montantRestant + $montantTotalDebite;

        // Solde en nombre de timbres
        $soldeTimbres = $montantRestant / 500;
        $totalTimbresAjoutes = $montantTotalAjoute / 500;
        $totalTimbresDebites = $total;

        // Calcul dynamique du solde du portefeuille (cumul du mois en cours)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $naissancesMonth = Paiement::whereHas('naissance', function ($query) use ($finance) {
            $query->where('commune', $finance->communeM);
        })
        ->where('status', 'ACCEPTED')
        ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
        ->get();
        $totalNaissanceMonth = $naissancesMonth->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $mariagesMonth = Paiement::whereHas('mariage', function ($query) use ($finance) {
            $query->where('commune', $finance->communeM);
        })
        ->where('status', 'ACCEPTED')
        ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
        ->get();
        $totalMariageMonth = $mariagesMonth->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $decesMonth = Paiement::whereHas('deces', function ($query) use ($finance) {
            $query->where('commune', $finance->communeM);
        })
        ->where('status', 'ACCEPTED')
        ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
        ->get();
        $totalDecesMonth = $decesMonth->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $soldePortefeuille = $totalNaissanceMonth + $totalMariageMonth + $totalDecesMonth;

        $demandesNaissance = Naissance::where('commune', $finance->communeM)->latest()->take(3)->get();
        $demandesDeces = Deces::where('commune', $finance->communeM)->latest()->take(3)->get();
        $demandesMariage = Mariage::where('commune', $finance->communeM)->latest()->take(3)->get();

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

        return view(
            'finance.dashboard',
            compact(
                'finance',
                'total',
                'soldeTimbres',
                'decesnombre',
                'naissancenombre',
                'mariagenombre',
                'demandesNaissance',
                'demandesDeces',
                'demandesMariage',
                'weeklyData',
                'monthlyData',
                'yearlyData',
                'decesMois',
                'mariageMois',
                'naissanceMois',
                'totalMois',
                'totalAujourdhui',
                'totalSemaine',
                'timbresAujourdhui',
                'montantAujourdhui',
                'timbresSemaine',
                'montantSemaine',
                'timbresMois',
                'montantMois',
                'naissanceAujourdhui',
                'decesAujourdhui',
                'mariageAujourdhui',
                'naissanceSemaine',
                'decesSemaine',
                'mariageSemaine',
                'montantTotalAjoute',
                'montantTotalDebite',
                'montantRestant',
                'totalTimbresAjoutes',
                'totalTimbresDebites',
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

            if ($partTimbre <= 0) {
                continue;
            }

            $feed->push((object)[
                'date' => Carbon::parse($p->paid_at ?? $p->created_at),
                'reference' => 'TP-' . $typeLabel . '-' . str_pad($p->id, 5, '0', STR_PAD_LEFT) . ($isModification ? '-M' : ''),
                'destinataire' => 'tresorPAY gtvB04rzE_wkvb4S2',
                'montant' => $partTimbre,
                'status' => 'Transféré'
            ]);
        }

        return $feed;
    }

    public function portefeuille(Request $request)
    {
        $finance = Auth::guard('finance')->user();

        // Calcul dynamique des timbres Naissance perçus en ligne
        $naissancesPaiements = Paiement::whereHas('naissance', function ($query) use ($finance) {
            $query->where('commune', $finance->communeM);
        })
        ->where('status', 'ACCEPTED')
        ->with('naissance')
        ->get();
        $totalNaissance = $naissancesPaiements->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        // Calcul dynamique des timbres Mariage perçus en ligne
        $mariagesPaiements = Paiement::whereHas('mariage', function ($query) use ($finance) {
            $query->where('commune', $finance->communeM);
        })
        ->where('status', 'ACCEPTED')
        ->with('mariage')
        ->get();
        $totalMariage = $mariagesPaiements->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        // Calcul dynamique des timbres Décès perçus en ligne
        $decesPaiements = Paiement::whereHas('deces', function ($query) use ($finance) {
            $query->where('commune', $finance->communeM);
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

        // Calcul dynamique du cumul du mois en cours
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalNaissanceMonth = $naissancesPaiements->whereBetween('paid_at', [$startOfMonth, $endOfMonth])->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $totalMariageMonth = $mariagesPaiements->whereBetween('paid_at', [$startOfMonth, $endOfMonth])->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $totalDecesMonth = $decesPaiements->whereBetween('paid_at', [$startOfMonth, $endOfMonth])->sum(function ($p) {
            return $this->getPaymentPartTimbre($p);
        });

        $soldePortefeuille = $totalNaissanceMonth + $totalMariageMonth + $totalDecesMonth;

        // Récupérer les derniers paiements réels de timbres pour alimenter l'historique des transferts instantanés
        $derniersPaiements = $this->getCommunePaymentsFeed($finance->communeM);

        $sortedFeed = $derniersPaiements->sortByDesc('date');
        $totalTransactionsCount = $sortedFeed->count();

        // Limiter strictement aux 5 derniers transferts (comme côté mairie)
        $transactions = $sortedFeed->take(5);

        return view('finance.portefeuille', compact(
            'finance',
            'soldePortefeuille',
            'totalPerçuEnLigne',
            'totalNaissance',
            'totalMariage',
            'totalDeces',
            'totalReversements',
            'transactions',
            'totalTransactionsCount'
        ));
    }

    public function historiqueReversements(Request $request)
    {
        $finance = Auth::guard('finance')->user();

        // Récupérer les transactions de timbres réels à partir des paiements
        $feed = $this->getCommunePaymentsFeed($finance->communeM);

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

        return view('finance.portefeuille_historique', compact(
            'finance',
            'transactions',
            'availableMonths',
            'availableYears'
        ));
    }

    public function exportPDF(Request $request)
    {
        $finance = Auth::guard('finance')->user();
        $commune = $finance->communeM;
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

        $userName = $finance->name;
        $roleLabel = 'Financier';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reversements_annuels', compact(
            'year',
            'commune',
            'userName',
            'roleLabel',
            'monthlyReport'
        ));

        return $pdf->download('transferts_finance_' . $year . '.pdf');
    }

    public function reverserPortefeuille(Request $request)
    {
        $request->validate([
            'montant' => 'required|numeric|min:1',
            'credential_id' => 'required|string'
        ]);

        $finance = Auth::guard('finance')->user();
        $montant = (float) $request->input('montant');
        $credentialId = $request->input('credential_id');

        // Récupérer les reversements actuels en session
        $reversements = session()->get('finance_reversements_' . $finance->id, []);

        // Enregistrer le nouveau reversement
        $reversements[] = [
            'reference' => 'REV-' . strtoupper(bin2hex(random_bytes(4))),
            'montant' => $montant,
            'status' => 'SUCCES',
            'destinataire' => $credentialId,
            'date' => now()->toDateTimeString()
        ];

        session()->put('finance_reversements_' . $finance->id, $reversements);

        return response()->json([
            'status' => 'success',
            'message' => 'Reversement de ' . number_format($montant) . ' XOF effectué avec succès vers TrésorPay.'
        ]);
    }

    public function logout()
    {
        Auth::guard('finance')->logout();
        return redirect()->route('finance.login');
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
