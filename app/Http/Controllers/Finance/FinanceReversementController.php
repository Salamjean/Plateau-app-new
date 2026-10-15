<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Comptable;
use App\Models\Finance;
use App\Models\Reversement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceReversementController extends Controller
{
    /**
     * Identifie l'utilisateur connecté (Financier ou Comptable)
     */
    private function getAuthenticatedUser()
    {
        if (Auth::guard('finance')->check()) {
            $user = Auth::guard('finance')->user();

            // Rechercher le nom réel de la personne physique associée au compte finance
            $personName = null;
            $personUser = \App\Models\User::where('email', $user->email)->first();
            if ($personUser && (!empty($personUser->name) || !empty($personUser->prenom))) {
                $personName = trim(($personUser->name ?? '') . ' ' . ($personUser->prenom ?? ''));
            }
            if (!$personName && !empty($user->name_respo) && $user->name_respo !== 'Régie des taxes') {
                $personName = $user->name_respo;
            }
            if (!$personName) {
                $personName = !empty($user->name_respo) ? $user->name_respo : 'Responsable Financier';
            }

            return [
                'user' => $user,
                'role' => 'Financier',
                'name' => $personName,
                'email' => $user->email,
                'finance_id' => $user->id,
                'comptable_id' => null,
                'commune' => $user->communeM ?? $user->commune ?? 'Plateau',
                'layout' => 'finance.layouts.template'
            ];
        }

        if (Auth::guard('comptable')->check()) {
            $user = Auth::guard('comptable')->user();
            return [
                'user' => $user,
                'role' => 'Comptable',
                'name' => trim($user->name . ' ' . $user->prenom),
                'email' => $user->email,
                'finance_id' => $user->finance_id,
                'comptable_id' => $user->id,
                'commune' => $user->communeM ?? $user->commune ?? 'Plateau',
                'layout' => 'comptable.layouts.template'
            ];
        }

        return null;
    }

    /**
     * Page principale : formulaire de reversement + historique
     */
    public function index(Request $request)
    {
        $authData = $this->getAuthenticatedUser();
        if (!$authData) {
            abort(403, 'Accès non autorisé.');
        }

        $financeId = $authData['finance_id'];
        $commune = $authData['commune'];

        // Compter les reversements existants pour déterminer si c'est le premier reversement
        $totalReversementsCount = Reversement::where(function ($q) use ($financeId, $commune) {
            if ($financeId) {
                $q->where('finance_id', $financeId);
            } else {
                $q->where('commune', $commune);
            }
        })->count();

        $isPremier = ($totalReversementsCount === 0);

        // Liste paginée des reversements
        $reversements = Reversement::where(function ($q) use ($financeId, $commune) {
            if ($financeId) {
                $q->where('finance_id', $financeId);
            } else {
                $q->where('commune', $commune);
            }
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        // Statistiques
        $totalMontant = Reversement::where(function ($q) use ($financeId, $commune) {
            if ($financeId) {
                $q->where('finance_id', $financeId);
            } else {
                $q->where('commune', $commune);
            }
        })->sum('montant');

        $dernierReversement = Reversement::where(function ($q) use ($financeId, $commune) {
            if ($financeId) {
                $q->where('finance_id', $financeId);
            } else {
                $q->where('commune', $commune);
            }
        })->latest('created_at')->first();

        // 12 mois de l'année
        $frenchMonths = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];

        $currentYear = Carbon::now()->year;
        $currentDateFormatted = Carbon::now()->format('d/m/Y');

        return view('finance.reversements.index', compact(
            'authData',
            'isPremier',
            'totalReversementsCount',
            'reversements',
            'totalMontant',
            'dernierReversement',
            'frenchMonths',
            'currentYear',
            'currentDateFormatted'
        ));
    }

    /**
     * Enregistrement d'un reversement
     */
    public function store(Request $request)
    {
        $authData = $this->getAuthenticatedUser();
        if (!$authData) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'montant' => 'required|numeric|min:1',
            'mois_concernes' => 'required|array|min:1',
            'commentaire' => 'nullable|string|max:1000',
            'is_premier' => 'nullable|boolean',
        ], [
            'montant.required' => 'Le montant du reversement est obligatoire.',
            'montant.numeric' => 'Le montant doit être une valeur numérique.',
            'montant.min' => 'Le montant doit être supérieur à 0 FCFA.',
            'mois_concernes.required' => 'Veuillez sélectionner au moins un mois concerné.',
            'mois_concernes.min' => 'Veuillez sélectionner au moins un mois.',
        ]);

        $financeId = $authData['finance_id'];
        $commune = $authData['commune'];

        // Détection du premier reversement
        $totalCount = Reversement::where(function ($q) use ($financeId, $commune) {
            if ($financeId) {
                $q->where('finance_id', $financeId);
            } else {
                $q->where('commune', $commune);
            }
        })->count();

        $isPremier = ($totalCount === 0) || $request->boolean('is_premier');
        $typeReversement = $isPremier ? 'Premier reversement' : 'Reversement standard';

        // Référence automatique
        $reference = 'REV-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

        // Date et année verrouillées (système, non modifiables)
        $dateReversement = Carbon::now();
        $anneeCourante = (string) Carbon::now()->year;

        Reversement::create([
            'reference' => $reference,
            'montant' => $request->montant,
            'is_premier' => $isPremier,
            'type_reversement' => $typeReversement,
            'date_reversement' => $dateReversement,
            'mois_concernes' => $request->mois_concernes,
            'annee' => $anneeCourante,
            'commentaire' => $request->commentaire,
            'finance_id' => $financeId,
            'comptable_id' => $authData['comptable_id'],
            'auteur_role' => $authData['role'],
            'enregistre_par' => $authData['name'],
            'enregistre_par_email' => $authData['email'],
            'commune' => $commune,
        ]);

        return redirect()->back()->with('success', "Le reversement de " . number_format($request->montant, 0, ',', ' ') . " FCFA a été enregistré avec succès sous la référence {$reference}.");
    }

    /**
     * Page d'historique complet des reversements
     */
    public function historique(Request $request)
    {
        $authData = $this->getAuthenticatedUser();
        if (!$authData) {
            abort(403, 'Accès non autorisé.');
        }

        $financeId = $authData['finance_id'];
        $commune = $authData['commune'];

        $query = Reversement::where(function ($q) use ($financeId, $commune) {
            if ($financeId) {
                $q->where('finance_id', $financeId);
            } else {
                $q->where('commune', $commune);
            }
        });

        // Filtre par année si sélectionné
        $selectedYear = $request->input('year');
        if ($selectedYear) {
            $query->where('annee', $selectedYear);
        }

        // Filtre par mois si sélectionné
        $selectedMonth = $request->input('month');
        if ($selectedMonth) {
            $query->whereJsonContains('mois_concernes', $selectedMonth);
        }

        $totalCount = (clone $query)->count();
        $totalMontant = (clone $query)->sum('montant');

        $reversements = $query->orderBy('created_at', 'desc')->paginate(15);

        // Années disponibles
        $availableYears = Reversement::where(function ($q) use ($financeId, $commune) {
            if ($financeId) {
                $q->where('finance_id', $financeId);
            } else {
                $q->where('commune', $commune);
            }
        })->whereNotNull('annee')->distinct()->pluck('annee')->sortDesc()->values();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([Carbon::now()->year]);
        }

        $frenchMonths = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];

        return view('finance.reversements.historique', compact(
            'authData',
            'reversements',
            'totalCount',
            'totalMontant',
            'availableYears',
            'frenchMonths',
            'selectedYear',
            'selectedMonth'
        ));
    }

    /**
     * Détails d'un reversement (pour modal ou reçu)
     */
    public function showRecu($id)
    {
        $authData = $this->getAuthenticatedUser();
        if (!$authData) {
            abort(403, 'Accès non autorisé.');
        }

        $reversement = Reversement::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $reversement->id,
                'reference' => $reversement->reference,
                'montant' => number_format($reversement->montant, 0, ',', ' ') . ' FCFA',
                'is_premier' => $reversement->is_premier,
                'type_reversement' => $reversement->type_reversement,
                'date' => $reversement->date_reversement ? $reversement->date_reversement->format('d/m/Y H:i') : $reversement->created_at->format('d/m/Y H:i'),
                'mois_concernes' => $reversement->mois_concernes ?? [],
                'annee' => $reversement->annee,
                'commentaire' => $reversement->commentaire ?? 'Aucun commentaire',
                'enregistre_par' => $reversement->nom_auteur,
                'enregistre_par_email' => $reversement->enregistre_par_email,
                'auteur_role' => $reversement->auteur_role,
                'commune' => $reversement->commune,
            ]
        ]);
    }
}
