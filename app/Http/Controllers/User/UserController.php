<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\DecesCertificat;
use App\Models\DecesSimple;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\NaissanceCertificat;
use App\Models\NaissanceSimple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function dashboard(){
        if (Auth::check()) {
            $user = Auth::user();

            // Récupérer les demandes avec leurs types respectifs
            $naissances = Naissance::where('user_id', $user->id)->get()->map(function ($naissance) {
                $naissance->type = 'naissance';
                return $naissance;
            });

            $deces = Deces::where('user_id', $user->id)->get()->map(function ($deces) {
                $deces->type = 'deces';
                return $deces;
            });

            $mariages = Mariage::where('user_id', $user->id)->get()->map(function ($mariage) {
                $mariage->type = 'mariage';
                return $mariage;
            });

            // Combiner toutes les demandes
            $demandes = $naissances->concat($deces)->concat($mariages);

            // Trier les demandes par date de création (les plus récentes en premier)
            $demandesRecente = $demandes->sortByDesc('created_at')->take(5);;

            // Calcul des totaux pour les groupes fusionnés
            $naissancesCount = Naissance::where('user_id', $user->id)->count();
            $decesCount = Deces::where('user_id', $user->id)->count();
            $mariageCount = Mariage::where('user_id', $user->id)->count();
    
            // Calcul des totaux fusionnés
            $totalNaissances = $naissancesCount;
            $totalDeces = $decesCount ;

            // Compter le nombre total de demandes
            $nombreDemandes = $demandes->count();

             // Ajoutez ces nouvelles méthodes pour obtenir les données mensuelles
            $naissancesMonthly = $this->getMonthlyCount(Naissance::class, $user->id);
            $decesMonthly = $this->getMonthlyCount(Deces::class, $user->id);
            $mariageMonthly = $this->getMonthlyCount(Mariage::class, $user->id);

            // Combinez les données similaires
            $totalNaissancesMonthly = array_map(function ($n) {
                return $n ;
            }, $naissancesMonthly);

            $totalDecesMonthly = array_map(function ($d) {
                return $d ;
            }, $decesMonthly);

            //Compter les colis avec un état réçu
            $naissancesEtatCount = Naissance::where('user_id', $user->id)->where('etat','réçu')->count();
            $decesEtatCount = Deces::where('user_id', $user->id)->where('etat','réçu')->count();
            $mariageEtatCount = Mariage::where('user_id', $user->id)->where('etat','réçu')->count();
            $TotalEtatCount = $naissancesEtatCount + $decesEtatCount + $mariageEtatCount;


            //Compter les colis avec un état en cours de livraison
            $naissancesLivreCount = Naissance::where('user_id', $user->id)->where('statut_livraison','en cours')->count();
            $decesLivreCount = Deces::where('user_id', $user->id)->where('statut_livraison','en cours')->count();
            $mariageLivreCount = Mariage::where('user_id', $user->id)->where('statut_livraison','en cours')->count();
            $TotalLivreCount = $naissancesLivreCount + $decesLivreCount + $mariageLivreCount;


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
            ));
        }
    }

    private function getMonthlyCount($model, $userId)
    {
        $currentYear = now()->year;
        $monthlyData = array_fill(0, 12, 0); // Initialise un tableau pour 12 mois (0-11)

        $records = $model::where('user_id', $userId)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->get();

        foreach ($records as $record) {
            $monthlyData[$record->month - 1] = $record->count; // -1 car les mois vont de 1-12
        }

        return $monthlyData;
    }

    public function logout(){
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }
}
