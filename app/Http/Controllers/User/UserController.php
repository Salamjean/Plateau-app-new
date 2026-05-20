<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\DecesCertificat;
use App\Models\DecesSimple;
use App\Models\MaintenanceSetting;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\NaissanceCertificat;
use App\Models\NaissanceSimple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Définir les états à inclure dans les comptes principaux
            $etatsInclus = ['en attente', 'terminé', 'récu'];

            // Récupérer les demandes avec les états spécifiés
            $naissances = Naissance::where('user_id', $user->id)
                ->whereIn('etat', $etatsInclus) // <-- MODIFIÉ
                ->get()->map(function ($naissance) {
                    $naissance->type = 'naissance';
                    return $naissance;
                });

            $deces = Deces::where('user_id', $user->id)
                ->whereIn('etat', $etatsInclus) // <-- MODIFIÉ
                ->get()->map(function ($deces) {
                    $deces->type = 'deces';
                    return $deces;
                });

            $mariages = Mariage::where('user_id', $user->id)
                ->whereIn('etat', $etatsInclus) // <-- MODIFIÉ
                ->get()->map(function ($mariage) {
                    $mariage->type = 'mariage';
                    return $mariage;
                });

            // Combiner toutes les demandes filtrées
            $demandes = $naissances->concat($deces)->concat($mariages);

            // Trier les demandes par date de création (les plus récentes en premier)
            $demandesRecente = $demandes->sortByDesc('created_at')->take(5);

            // Calcul des totaux pour les demandes avec les états spécifiés
            $naissancesCount = Naissance::where('user_id', $user->id)->whereIn('etat', $etatsInclus)->count(); // <-- MODIFIÉ
            $decesCount = Deces::where('user_id', $user->id)->whereIn('etat', $etatsInclus)->count(); // <-- MODIFIÉ
            $mariageCount = Mariage::where('user_id', $user->id)->whereIn('etat', $etatsInclus)->count(); // <-- MODIFIÉ

            // Calcul des totaux fusionnés
            $totalNaissances = $naissancesCount;
            $totalDeces = $decesCount;

            // Compter le nombre total de demandes (basé sur les états filtrés)
            $nombreDemandes = $demandes->count();

            // --- Le reste de votre code reste inchangé ---

            // Données mensuelles (celles-ci comptent TOUT, sauf si getMonthlyCount est modifié)
            $naissancesMonthly = $this->getMonthlyCount(Naissance::class, $user->id);
            $decesMonthly = $this->getMonthlyCount(Deces::class, $user->id);
            $mariageMonthly = $this->getMonthlyCount(Mariage::class, $user->id);

            // ... (combinaison des données mensuelles) ...
            $totalNaissancesMonthly = array_map(function ($n) {
                return $n;
            }, $naissancesMonthly);

            $totalDecesMonthly = array_map(function ($d) {
                return $d;
            }, $decesMonthly);


            // Compter les colis avec un état 'réçu' (SÉPARÉMENT)
            $naissancesEtatCount = Naissance::where('user_id', $user->id)->where('etat', 'réçu')->count();
            $decesEtatCount = Deces::where('user_id', $user->id)->where('etat', 'réçu')->count();
            $mariageEtatCount = Mariage::where('user_id', $user->id)->where('etat', 'réçu')->count();
            $TotalEtatCount = $naissancesEtatCount + $decesEtatCount + $mariageEtatCount;


            // Compter les colis avec un état 'en cours de livraison' (SÉPARÉMENT)
            $naissancesLivreCount = Naissance::where('user_id', $user->id)->where('statut_livraison', 'en cours')->count();
            $decesLivreCount = Deces::where('user_id', $user->id)->where('statut_livraison', 'en cours')->count();
            $mariageLivreCount = Mariage::where('user_id', $user->id)->where('statut_livraison', 'en cours')->count();
            $TotalLivreCount = $naissancesLivreCount + $decesLivreCount + $mariageLivreCount;


            // Données des demandes gratuites (mode test)
            $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();

            // Synchronisation de free_requests_used avec le nombre de demandes réelles
            $totalToutesDemandes = \App\Models\Naissance::where('user_id', $user->id)->count() +
                \App\Models\Deces::where('user_id', $user->id)->count() +
                \App\Models\Mariage::where('user_id', $user->id)->count();

            if ($totalToutesDemandes > $user->free_requests_used) {
                $user->free_requests_used = min(2, max($user->free_requests_used, $totalToutesDemandes));
                $user->save();
            }

            $freeRequestsRemaining = max(0, 2 - $user->free_requests_used);
            $showFreeRequestsMessage = $freeRequestsModeActive && !$user->has_seen_free_requests_message && $freeRequestsRemaining > 0;

            // Passer les demandes récentes à la vue
            return view('user.dashboard', compact(
                'user',
                'demandesRecente',
                'nombreDemandes',
                'naissancesCount',
                'decesCount',
                'mariageCount',
                'totalNaissances',
                'totalDeces',
                'totalNaissancesMonthly',
                'totalDecesMonthly',
                'mariageMonthly',
                'TotalEtatCount',
                'TotalLivreCount',
                'freeRequestsModeActive',
                'freeRequestsRemaining',
                'showFreeRequestsMessage',
            ));
        }
    }

    private function getMonthlyCount($model, $userId)
    {
        $currentYear = now()->year;
        $monthlyData = array_fill(0, 12, 0); // Initialise un tableau pour 12 mois (0-11)

        // Définir les états à inclure dans le graphique
        $etatsInclus = ['en attente', 'terminé', 'récu'];

        $records = $model::where('user_id', $userId)
            ->whereIn('etat', $etatsInclus) // Ajout du filtre par état
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->get();

        foreach ($records as $record) {
            $monthlyData[$record->month - 1] = $record->count; // -1 car les mois vont de 1-12
        }

        return $monthlyData;
    }

    public function dismissFreeRequestsMessage()
    {
        $user = Auth::user();
        $user->has_seen_free_requests_message = true;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }
}
