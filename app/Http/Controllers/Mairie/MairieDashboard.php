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

        // Calcul dynamique du solde du portefeuille
        $naissancesAll = Naissance::where('commune', $mairie->name)->paye()->get();
        $totalNaissance = $naissancesAll->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $mariagesAll = Mariage::where('commune', $mairie->name)->paye()->get();
        $totalMariage = $mariagesAll->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $decesAll = Deces::where('commune', $mairie->name)->paye()->get();
        $totalDeces = $decesAll->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        $totalPerçuEnLigne = $totalNaissance + $totalMariage + $totalDeces;
        $reversements = session()->get('mairie_reversements_' . $mairie->id, []);
        $totalReversements = collect($reversements)->sum('montant');
        $soldePortefeuille = $totalPerçuEnLigne - $totalReversements;

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
        $naissances = Naissance::where('commune', $mairie->name)
            ->paye()
            ->with('user')
            ->get();
        $totalNaissance = $naissances->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        // Calcul dynamique des timbres Mariage perçus en ligne
        $mariages = Mariage::where('commune', $mairie->name)
            ->paye()
            ->with('user')
            ->get();
        $totalMariage = $mariages->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        // Calcul dynamique des timbres Décès perçus en ligne
        $deces = Deces::where('commune', $mairie->name)
            ->paye()
            ->with('user')
            ->get();
        $totalDeces = $deces->sum(function ($item) {
            return $item->montant_timbre ?? 500;
        });

        // Somme totale brute perçue en ligne
        $totalPerçuEnLigne = $totalNaissance + $totalMariage + $totalDeces;

        // Récupérer les reversements simulés en session
        $reversements = session()->get('mairie_reversements_' . $mairie->id, []);
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

        return view('mairie.portefeuille', compact(
            'mairie',
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
        $mairie = Auth::guard('mairie')->user();

        // Récupérer les reversements simulés en session
        $reversements = session()->get('mairie_reversements_' . $mairie->id, []);

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

        return view('mairie.portefeuille_historique', compact(
            'mairie',
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
