<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reversement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminReversementController extends Controller
{
    /**
     * Affiche la liste des reversements pour l'administrateur (lecture seule / consultation).
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'commune' => 'nullable|string|max:100',
            'year' => 'nullable|digits:4',
            'month' => 'nullable|string|max:50',
            'search' => 'nullable|string|max:255',
        ]);

        $query = Reversement::query();

        // Filtre par commune si spécifié
        if (!empty($validated['commune'])) {
            $query->where('commune', $validated['commune']);
        }

        // Filtre par année
        if ($request->filled('year')) {
            $query->where('annee', $request->year);
        }

        // Filtre par mois
        if ($request->filled('month')) {
            $query->whereJsonContains('mois_concernes', $request->month);
        }

        // Recherche par mot-clé (référence, auteur, commentaire, email...)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('enregistre_par', 'like', "%{$search}%")
                  ->orWhere('enregistre_par_email', 'like', "%{$search}%")
                  ->orWhere('commentaire', 'like', "%{$search}%")
                  ->orWhere('commune', 'like', "%{$search}%");
            });
        }

        // Statistiques
        $totalCount = (clone $query)->count();
        $totalMontant = (clone $query)->sum('montant');
        $dernierReversement = (clone $query)->orderBy('date_reversement', 'desc')->first();

        // Liste paginée
        $reversements = $query->orderBy('date_reversement', 'desc')
                              ->orderBy('created_at', 'desc')
                              ->paginate(15)
                              ->appends($request->query());

        // Données pour les filtres
        $availableYears = Reversement::whereNotNull('annee')->distinct()->pluck('annee')->sortDesc()->values();
        if ($availableYears->isEmpty()) {
            $availableYears = collect([Carbon::now()->year]);
        }

        $availableCommunes = Reversement::whereNotNull('commune')->distinct()->pluck('commune')->sort()->values();

        $frenchMonths = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];

        $selectedYear = $request->year;
        $selectedMonth = $request->month;
        $selectedCommune = $request->commune;
        $searchTerm = $request->search;

        return view('admin.reversements.index', compact(
            'reversements',
            'totalCount',
            'totalMontant',
            'dernierReversement',
            'availableYears',
            'availableCommunes',
            'frenchMonths',
            'selectedYear',
            'selectedMonth',
            'selectedCommune',
            'searchTerm'
        ));
    }

    /**
     * Détails d'un reversement (pour le pop-up modal ou reçu).
     */
    public function showRecu($id)
    {
        $reversement = Reversement::findOrFail((int) $id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $reversement->id,
                'reference' => $reversement->reference,
                'montant' => number_format($reversement->montant, 0, ',', ' ') . ' FCFA',
                'is_premier' => $reversement->is_premier,
                'type_reversement' => $reversement->type_reversement,
                'date' => $reversement->date_reversement ? $reversement->date_reversement->format('d/m/Y à H:i') : $reversement->created_at->format('d/m/Y à H:i'),
                'mois_concernes' => $reversement->mois_concernes ?? [],
                'annee' => $reversement->annee,
                'commentaire' => $reversement->commentaire ?? 'Aucune observation particulière',
                'enregistre_par' => $reversement->nom_auteur,
                'enregistre_par_email' => $reversement->enregistre_par_email,
                'auteur_role' => $reversement->auteur_role ?? 'Financier',
                'commune' => $reversement->commune ?? 'Plateau',
            ]
        ]);
    }
}
