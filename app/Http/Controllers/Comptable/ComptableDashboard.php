<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\Timbre;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComptableDashboard extends Controller
{
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

        // Calcul dynamique du solde du portefeuille en ligne
        $naissancesAll = Naissance::where('commune', $commune)->paye()->get();
        $totalNaissancePortefeuille = $naissancesAll->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $mariagesAll = Mariage::where('commune', $commune)->paye()->get();
        $totalMariagePortefeuille = $mariagesAll->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $decesAll = Deces::where('commune', $commune)->paye()->get();
        $totalDecesPortefeuille = $decesAll->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $totalPerçuEnLigne = $totalNaissancePortefeuille + $totalMariagePortefeuille + $totalDecesPortefeuille;

        // Trouver la mairie pour obtenir son id afin de récupérer ses reversements
        $mairieObj = \App\Models\Mairie::where('name', $commune)->first();
        $totalReversements = 0;
        if ($mairieObj) {
            $reversements = session()->get('mairie_reversements_' . $mairieObj->id, []);
            $totalReversements = collect($reversements)->sum('montant');
        }
        $soldePortefeuille = $totalPerçuEnLigne - $totalReversements;

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

    public function portefeuille(Request $request)
    {
        $comptable = Auth::guard('comptable')->user();
        $commune = $comptable->communeM;

        // Calcul dynamique des timbres Naissance perçus en ligne
        $naissances = Naissance::where('commune', $commune)
            ->paye()
            ->with('user')
            ->get();
        $totalNaissance = $naissances->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        // Calcul dynamique des timbres Mariage perçus en ligne
        $mariages = Mariage::where('commune', $commune)
            ->paye()
            ->with('user')
            ->get();
        $totalMariage = $mariages->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        // Calcul dynamique des timbres Décès perçus en ligne
        $deces = Deces::where('commune', $commune)
            ->paye()
            ->with('user')
            ->get();
        $totalDeces = $deces->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        // Somme totale brute perçue en ligne
        $totalPerçuEnLigne = $totalNaissance + $totalMariage + $totalDeces;

        // Trouver la mairie pour obtenir son id afin de récupérer ses reversements
        $mairieObj = \App\Models\Mairie::where('name', $commune)->first();
        $reversements = [];
        $mairieId = null;
        if ($mairieObj) {
            $mairieId = $mairieObj->id;
            $reversements = session()->get('mairie_reversements_' . $mairieId, []);
        }
        
        $totalReversements = collect($reversements)->sum('montant');

        // Solde net du portefeuille
        $soldePortefeuille = $totalPerçuEnLigne - $totalReversements;

        // Créer la liste des reversements effectués (Débits)
        $feed = collect();

        foreach ($reversements as $rev) {
            $feed->push((object)[
                'reference' => $rev['reference'],
                'montant' => $rev['montant'],
                'destinataire' => $rev['destinataire'],
                'date' => Carbon::parse($rev['date']),
                'status' => 'Reversé'
            ]);
        }

        // Trier les reversements par date décroissante
        $sortedFeed = $feed->sortByDesc('date');
        $totalTransactionsCount = $sortedFeed->count();

        // Limiter strictement aux 3 derniers reversements
        $transactions = $sortedFeed->take(3);

        return view('comptable.portefeuille', compact(
            'comptable',
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
        $comptable = Auth::guard('comptable')->user();
        $commune = $comptable->communeM;

        // Trouver la mairie pour obtenir son id afin de récupérer ses reversements
        $mairieObj = \App\Models\Mairie::where('name', $commune)->first();
        $reversements = [];
        if ($mairieObj) {
            $reversements = session()->get('mairie_reversements_' . $mairieObj->id, []);
        }

        // Créer la liste des reversements effectués (Débits)
        $feed = collect();

        foreach ($reversements as $rev) {
            $feed->push((object)[
                'reference' => $rev['reference'],
                'montant' => $rev['montant'],
                'destinataire' => $rev['destinataire'],
                'date' => Carbon::parse($rev['date']),
                'status' => 'Reversé'
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

        // Trier les reversements par date décroissante
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
            'availableMonths'
        ));
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
}
