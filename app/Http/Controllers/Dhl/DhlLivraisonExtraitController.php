<?php

namespace App\Http\Controllers\Dhl;

use App\Http\Controllers\Controller;
use App\Models\DHLAgence;
use App\Models\Livreur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DhlLivraisonExtraitController extends Controller
{
   // Affiche le formulaire de saisie de la référence
    public function create()
    {
        return view('dhl.delivery');
    }

    public function attribuerDemande(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|max:255',
        ]);

        // Récupérer la dhl connectée
        $dhl = Auth::guard('dhl')->user();

        // Rechercher la demande
        $demande = $this->trouverDemandeParReference($request->reference);

        if (!$demande) {
            return back()->with('error', 'Aucun colis trouvé avec cette référence.');
        }

        // Vérifier si le colis a déjà un livraison_id (déjà attribué à une livraison)
        if ($demande->livraison_id) {
            return back()->with('error', 'Ce colis est déjà enregistré dans le système par une poste et ne peut plus être attribué à DHL.');
        }

        // Vérifier si le colis est déjà attribué à cette dhl
        if ($demande->dhl_id == $dhl->id) {
            return back()->with('success', 'Ce colis est déjà enregistré dans votre DHL.');
        }

        // Vérifier si le colis est attribué à une autre dhl
        if ($demande->dhl_id && $demande->dhl_id != $dhl->id) {
            return back()->with('error', 'Ce colis est enregistré dans une autre DHL.');
        }

        // Vérifier les conditions préalables
        if ($demande->etat !== 'terminé') {
            return back()->with('error', 'Ce colis n\'est pas encore prêt pour la livraison.');
        }

        if ($demande->choix_option !== 'livraison') {
            return back()->with('error', 'Ce colis doit être récupéré sur place.');
        }

        // Si toutes les conditions sont remplies, attribuer le colis à la dhl
        try {
            $demande->dhl_id = $dhl->id;
            $demande->save();

            return back()->with('success', 'Colis attribué avec succès à votre DHL.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'attribution du colis: ' . $e->getMessage());
        }
    }

    /**
     * Trouve une demande par référence dans toutes les tables possibles
     */
    private function trouverDemandeParReference($code)
    {
        // Liste des modèles à vérifier
        $modeles = [
            'Naissance',
            'Deces',
            'Mariage'
        ];

        foreach ($modeles as $modele) {
            $classeModele = "App\\Models\\$modele";
            
            // Recherche dans les deux champs (reference ET livraison_code)
            $demande = $classeModele::where(function($query) use ($code) {
                            $query->where('reference', $code)
                                ->orWhere('livraison_code', $code);
                        })
                        ->first();
            
            if ($demande) {
                return $demande;
            }
        }

        return null;
    }

    public function demandesAttribuees()
    {
        $dhl = Auth::guard('dhl')->user();
        
        // Récupérer toutes les demandes attribuées à cette dhl
        $demandes = collect();
        
        // Liste des modèles à vérifier
        $modeles = [
            'Naissance',
            'Deces',
            'Mariage'
        ];
        
        foreach ($modeles as $modele) {
            $classeModele = "App\\Models\\$modele";
            $demandesModele = $classeModele::where('dhl_id', Auth::guard('dhl')->user()->id)
                ->with('user') // Si vous avez une relation avec l'utilisateur
                ->where('statut_livraison', '!=','livré')
                ->get()
                ->map(function($item) use ($modele) {
                    $item->type_demande = $this->getTypeDemande($modele);
                    return $item;
                });
                
            $demandes = $demandes->merge($demandesModele);
        }

        $livreurs = DHLAgence::where('disponible','1')->get();
        
        // Trier par date de création (les plus récentes en premier)
        $demandes = $demandes->sortBy('created_at');
        
        return view('dhl.demandes-attribuees', compact('demandes','livreurs'));
    }

    public function demandesLivree()
    {
        $dhl = Auth::guard('dhl')->user();
        
        // Récupérer toutes les demandes attribuées à cette dhl
        $demandes = collect();
        
        // Liste des modèles à vérifier
        $modeles = [
            'Naissance',
            'Deces',
            'Mariage'
        ];
        
        foreach ($modeles as $modele) {
            $classeModele = "App\\Models\\$modele";
            $demandesModele = $classeModele::where('dhl_id', Auth::guard('dhl')->user()->id)
                ->with('user','livreur') // Si vous avez une relation avec l'utilisateur
                ->where('statut_livraison','livré')
                ->get()
                ->map(function($item) use ($modele) {
                    $item->type_demande = $this->getTypeDemande($modele);
                    return $item;
                });
                
            $demandes = $demandes->merge($demandesModele);
        }

        $livreurs = DHLAgence::get();
        
        // Trier par date de création (les plus récentes en premier)
        $demandes = $demandes->sortBy('created_at');
        
        return view('dhl.demandes_livre', compact('demandes','livreurs'));
    }

    private function getTypeDemande($modele)
    {
        $types = [
            'Naissance' => 'Naissance',
            'Deces' => 'Deces',
            'Mariage' => 'Mariage'
        ];
        
        return $types[$modele] ?? 'Demande';
    }

    public function assignerLivreur(Request $request)
    {
        $request->validate([
            'livreur_id' => 'required|exists:d_h_l_agences,id',
            'demandes' => 'required|array'
        ]);

        try {
            foreach ($request->demandes as $demande) {
                $modelClass = "App\\Models\\" . Str::studly($demande['type']);
                
                if (class_exists($modelClass)) {
                    $modelClass::where('id', $demande['id'])
                        ->update([
                            'agence_id' => $request->livreur_id,
                            'statut_livraison' => 'en cours'
                        ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Colis assignées avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur assignation livreur: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'assignation'
            ], 500);
        }
    }
}
