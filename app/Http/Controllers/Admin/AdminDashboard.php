<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LivraisonsExport;
use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mairie;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class AdminDashboard extends Controller
{
    public function dashboard(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $deces = Deces::paye()->count();
        $mariage = Mariage::paye()->count();
        $naissance = Naissance::paye()->count();

        $mairie = Mairie::whereNull('archived_at')->count();
        $utilisateurs = User::count();
        $total = $deces + $mariage + $naissance;

        // Récupérer le solde total de toutes les mairies
        $soldeTotalMairies = Mairie::whereNull('archived_at')->sum('solde');
        // Solde initial
        $soldeActuel = $soldeTotalMairies;

        $timbresSortis = abs(\App\Models\Timbre::where('nombre_timbre', '<', 0)->sum('nombre_timbre'));

        // Calcul des timbres gratuits et payants sortis (uniquement si marqués sortis / récupérés / livrés)
        $timbresGratuitsSortis = 0;
        $timbresPayantsSortis = 0;

        foreach ([\App\Models\Naissance::class, \App\Models\Mariage::class, \App\Models\Deces::class] as $modelClass) {
            $demandesSorties = $modelClass::paye()
                ->where(function ($q) {
                    $q->where('timbre_recupere', 1)
                      ->orWhere('statut_livraison', 'livré')
                      ->orWhere('statut_livraison', 'livre');
                })
                ->get(['quantite', 'qty_simple', 'qty_integral', 'free_timbres_count', 'is_free_request']);

            foreach ($demandesSorties as $d) {
                $totalQty = (int) ($d->quantite ?? (($d->qty_simple ?? 0) + ($d->qty_integral ?? 0)));
                if ($totalQty <= 0) {
                    $totalQty = 1;
                }

                $freeQty = (int) ($d->free_timbres_count ?? 0);
                if ($freeQty === 0 && !empty($d->is_free_request)) {
                    $freeQty = 1;
                }
                $freeQty = min($totalQty, $freeQty);
                $paidQty = max(0, $totalQty - $freeQty);

                $timbresGratuitsSortis += $freeQty;
                $timbresPayantsSortis += $paidQty;
            }
        }

        if ($timbresSortis > ($timbresGratuitsSortis + $timbresPayantsSortis)) {
            $timbresPayantsSortis = max($timbresPayantsSortis, $timbresSortis - $timbresGratuitsSortis);
        }

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

            // Comptage (uniquement les demandes payées)
            $counts[$key] = $class::paye()->count();

            // Mise à jour des stats globales
            $stats['total'] += $counts[$key];
            $stats['en_attente'] += $class::paye()->where('statut_livraison', 'en attente')
                ->count();
            $stats['en_cours'] += $class::paye()->where('statut_livraison', 'en cours')
                ->count();
            $stats['livre'] += $class::paye()->where('statut_livraison', 'livré')
                ->count();

            // COMPTAGE DES DEMANDES NON ATTRIBUÉES (ni à une poste ni à une DHL)
            $stats['non_attribue'] += $class::paye()->whereNull('livraison_id')
                ->whereNull('dhl_id')
                ->where('etat', 'terminé')
                ->where('choix_option', 'livraison')
                ->count();

            // Remplissage des données du graphique pour les 7 derniers jours
            for ($i = 6; $i >= 0; $i--) {
                $dateObj = Carbon::now()->subDays($i);
                $dateStart = $dateObj->copy()->startOfDay();
                $dateEnd = $dateObj->copy()->endOfDay();
                
                $chartData['livre'][6-$i] += $class::paye()
                    ->where('statut_livraison', 'livré')
                    ->whereBetween('updated_at', [$dateStart, $dateEnd])->count();
                    
                $chartData['en_cours'][6-$i] += $class::paye()
                    ->where('statut_livraison', 'en cours')
                    ->whereBetween('updated_at', [$dateStart, $dateEnd])->count();
            }

            // Calcul du solde disponible TOTAL (toutes les livraisons payées)
            $soldeDisponible += $class::paye()
                ->where('choix_option', 'livraison')
                ->sum('montant_livraison');

            // Calcul du solde du MOIS SÉLECTIONNÉ uniquement (Porte-feuille)
            $soldeMoisEnCours += $class::paye()
                ->where('choix_option', 'livraison')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
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
            'timbresSortis',
            'timbresGratuitsSortis',
            'timbresPayantsSortis',
            'mairie',
            'utilisateurs',
            'soldeTotalMairies',
            'activites',
            'soldeDisponible',
            'soldeMoisEnCours', // Ajout de la nouvelle variable
            'month',
            'year'
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

    private function getPaymentParts($p)
    {
        $montant = (float) $p->montant;
        $partTimbre = $montant;
        $partLivraison = 0.0;

        $isModification = str_contains((string) $p->transaction_id, '-MOD-');

        if (isset($p->raw_response['part_timbre'])) {
            $partTimbre = (float) $p->raw_response['part_timbre'];
            $partLivraison = max(0, $montant - $partTimbre);
            return ['timbre' => $partTimbre, 'livraison' => $partLivraison];
        }

        $relation = null;
        if ($p->naissance_id) $relation = 'naissance';
        elseif ($p->mariage_id) $relation = 'mariage';
        elseif ($p->deces_id) $relation = 'deces';
        
        if (!$relation || $p->naissance_groupe_id || $p->mariage_groupe_id || $p->deces_groupe_id) {
            return ['timbre' => $partTimbre, 'livraison' => 0.0];
        }

        $demande = $p->$relation;
        if (!$demande || $demande->choix_option !== 'livraison') {
            return ['timbre' => $partTimbre, 'livraison' => 0.0];
        }

        $allPayments = \App\Models\Paiement::where("{$relation}_id", $demande->id)
            ->where('status', 'ACCEPTED')
            ->orderBy('id', 'asc')
            ->get();

        if ($allPayments->isEmpty()) {
            $partTimbre = $isModification ? $montant : max(0.0, $montant - 1500.0);
            return ['timbre' => $partTimbre, 'livraison' => max(0.0, $montant - $partTimbre)];
        }

        $firstPayment = $allPayments->first();
        if ($p->id === $firstPayment->id && !$isModification) {
            $partTimbre = max(0.0, $montant - 1500.0);
            return ['timbre' => $partTimbre, 'livraison' => 1500.0];
        }

        return ['timbre' => $montant, 'livraison' => 0.0];
    }

    public function transactions(Request $request)
    {
        \Carbon\Carbon::setLocale('fr');
        
        $selectedMonth = $request->input('month');
        if ($selectedMonth) {
            try {
                $date = \Carbon\Carbon::parse($selectedMonth . '-01');
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();
            } catch (\Exception $e) {
                $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
                $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
                $selectedMonth = \Carbon\Carbon::now()->format('Y-m');
            }
        } else {
            $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
            $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
            $selectedMonth = \Carbon\Carbon::now()->format('Y-m');
        }

        // Historique de tous les paiements
        $allPayments = \App\Models\Paiement::where('status', 'ACCEPTED')
            ->with(['naissance', 'mariage', 'deces'])
            ->get();
            
        $stats = [
            'total' => 0, 'timbre' => 0, 'livraison' => 0,
            'wave_total' => 0, 'wave_timbre' => 0, 'wave_livraison' => 0,
            'tresor_total' => 0, 'tresor_timbre' => 0, 'tresor_livraison' => 0,
            'naissance_total' => 0, 'mariage_total' => 0, 'deces_total' => 0
        ];

        $feed = collect();
        $comptabiliteMensuelle = [];
        $currentYear = \Carbon\Carbon::now()->year;

        for ($m = 1; $m <= 12; $m++) {
            $comptabiliteMensuelle[$m] = [
                'nom' => ucfirst(\Carbon\Carbon::create(null, $m, 1)->locale('fr')->translatedFormat('F')),
                'timbre' => 0,
                'livraison' => 0,
                'total' => 0
            ];
        }

        foreach ($allPayments as $p) {
            $parts = $this->getPaymentParts($p);
            $date = \Carbon\Carbon::parse($p->paid_at ?? $p->created_at);
            
            // Si le paiement est dans le mois sélectionné, on met à jour les stats
            if ($date->between($startOfMonth, $endOfMonth)) {
                $total = (float) $p->montant;
                $t = $parts['timbre'];
                $l = $parts['livraison'];

                $stats['total'] += $total;
                $stats['timbre'] += $t;
                $stats['livraison'] += $l;

                $operator = strtolower($p->operator_id);
                if ($operator === 'wave') {
                    $stats['wave_total'] += $total;
                    $stats['wave_timbre'] += $t;
                    $stats['wave_livraison'] += $l;
                } else if ($operator === 'tresorpay') {
                    $stats['tresor_total'] += $total;
                    $stats['tresor_timbre'] += $t;
                    $stats['tresor_livraison'] += $l;
                }

                if ($p->naissance_id || $p->naissance_groupe_id) $stats['naissance_total'] += $total;
                elseif ($p->mariage_id || $p->mariage_groupe_id) $stats['mariage_total'] += $total;
                elseif ($p->deces_id || $p->deces_groupe_id) $stats['deces_total'] += $total;

                $feed->push((object)[
                    'date' => $date,
                    'reference' => $p->transaction_id ?? ('TXN-' . str_pad($p->id, 5, '0', STR_PAD_LEFT)),
                    'commune' => $p->naissance->commune ?? $p->mariage->commune ?? $p->deces->commune ?? 'Multiples',
                    'type' => $p->naissance_id ? 'Naissance' : ($p->mariage_id ? 'Mariage' : ($p->deces_id ? 'Décès' : 'Groupe')),
                    'montant_total' => $total,
                    'part_timbre' => $t,
                    'part_livraison' => $l,
                    'payment_method' => $p->operator_id ?? 'Inconnu',
                    'status' => 'Succès'
                ]);
            }

            // Comptabilité mensuelle de l'année en cours
            if ($date->year == $currentYear) {
                $comptabiliteMensuelle[$date->month]['timbre'] += $parts['timbre'];
                $comptabiliteMensuelle[$date->month]['livraison'] += $parts['livraison'];
                $comptabiliteMensuelle[$date->month]['total'] += (float)$p->montant;
            }
        }

        $sortedFeed = $feed->sortByDesc('date');
        
        $sortedFeed = $feed->sortByDesc('date');
        $transactions = $sortedFeed->take(6);
        $totalTransactionsCount = $sortedFeed->count();

        return view('admin.transactions.index', compact(
            'stats',
            'transactions',
            'totalTransactionsCount',
            'comptabiliteMensuelle',
            'selectedMonth',
            'currentYear'
        ));
    }

    public function allTransactions(Request $request)
    {
        \Carbon\Carbon::setLocale('fr');
        
        $selectedMonth = $request->input('month');
        if ($selectedMonth) {
            try {
                $date = \Carbon\Carbon::parse($selectedMonth . '-01');
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();
            } catch (\Exception $e) {
                $startOfMonth = null;
                $endOfMonth = null;
                $selectedMonth = null;
            }
        } else {
            $startOfMonth = null;
            $endOfMonth = null;
            $selectedMonth = null;
        }

        $query = \App\Models\Paiement::where('status', 'ACCEPTED')->with(['naissance', 'mariage', 'deces']);
        
        if ($startOfMonth && $endOfMonth) {
            $query->where(function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                  ->orWhere(function($subQ) use ($startOfMonth, $endOfMonth) {
                      $subQ->whereNull('paid_at')
                           ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                  });
            });
        }
        
        $allPayments = $query->latest('paid_at')->get();
        $feed = collect();

        foreach ($allPayments as $p) {
            $parts = $this->getPaymentParts($p);
            $date = \Carbon\Carbon::parse($p->paid_at ?? $p->created_at);
            
            $feed->push((object)[
                'date' => $date,
                'reference' => $p->transaction_id ?? ('TXN-' . str_pad($p->id, 5, '0', STR_PAD_LEFT)),
                'commune' => $p->naissance->commune ?? $p->mariage->commune ?? $p->deces->commune ?? 'Multiples',
                'type' => $p->naissance_id ? 'Naissance' : ($p->mariage_id ? 'Mariage' : ($p->deces_id ? 'Décès' : 'Groupe')),
                'montant_total' => (float)$p->montant,
                'part_timbre' => $parts['timbre'],
                'part_livraison' => $parts['livraison'],
                'payment_method' => $p->operator_id ?? 'Inconnu',
                'status' => 'Succès'
            ]);
        }

        $sortedFeed = $feed->sortByDesc('date');
        
        $page = $request->input('page', 1);
        $perPage = 20;
        $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $sortedFeed->forPage($page, $perPage),
            $sortedFeed->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.transactions.all', compact('transactions', 'selectedMonth'));
    }

    public function deletedDemandes()
    {
        $deletedDemandes = \App\Models\DeletedDemande::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.deleted_demandes.index', compact('deletedDemandes'));
    }
}
