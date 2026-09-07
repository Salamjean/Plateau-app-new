<?php

namespace App\Http\Controllers\Poste;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Http\Controllers\Controller;
use App\Models\Livreur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\GeneralPushNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LivraisonExtraitController extends Controller
{
    // Affiche le formulaire de saisie de la référence
    public function create()
    {
        return view('poste.delivery');
    }

   public function attribuerDemande(Request $request)
    {
        $request->validate([
            'reference' => 'required|string|max:255',
        ]);

        // Récupérer la poste connectée
        $poste = Auth::guard('poste')->user();

        // Rechercher la demande
        $demande = $this->trouverDemandeParReference($request->reference);

        if (!$demande) {
            return back()->with('error', 'Aucun colis trouvé avec cette référence.');
        }

        // Vérifier si le colis a déjà un dhl_id (déjà attribué à DHL)
        if ($demande->dhl_id) {
            return back()->with('error', 'Ce colis est déjà enregistré dans le système DHL et ne peut plus être attribué à la poste.');
        }

        // Vérifier si le colis est déjà attribué à cette poste
        if ($demande->livraison_id == $poste->id) {
            return back()->with('success', 'Ce colis est déjà enregistré dans votre poste.');
        }

        // Vérifier si le colis est attribué à une autre poste
        if ($demande->livraison_id && $demande->livraison_id != $poste->id) {
            return back()->with('error', 'Ce colis est enregistré dans une autre poste.');
        }

        // Vérifier les conditions préalables
        if ($demande->etat !== 'terminé') {
            return back()->with('error', 'Ce colis n\'est pas encore prêt pour la livraison.');
        }

        if ($demande->choix_option !== 'livraison') {
            return back()->with('error', 'Ce colis doit être récupéré sur place.');
        }

        // Si toutes les conditions sont remplies, attribuer le colis à la poste
        try {
            $demande->livraison_id = $poste->id;
            $demande->save();

            return back()->with('success', 'Colis attribué avec succès à votre poste.');
            
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
        $poste = Auth::guard('poste')->user();
        
        // Récupérer toutes les demandes attribuées à cette poste
        $demandes = collect();
        
        // Liste des modèles à vérifier
        $modeles = [
            'Naissance',
            'Deces',
            'Mariage'
        ];
        
        foreach ($modeles as $modele) {
            $classeModele = "App\\Models\\$modele";
            $demandesModele = $classeModele::where('livraison_id', Auth::guard('poste')->user()->id)
                ->with('user') // Si vous avez une relation avec l'utilisateur
                ->where('statut_livraison', '!=','livré')
                ->get()
                ->map(function($item) use ($modele) {
                    $item->type_demande = $this->getTypeDemande($modele);
                    return $item;
                });
                
            $demandes = $demandes->merge($demandesModele);
        }

        $livreurs = Livreur::where('disponible', '1')->whereNull('archived_at')->get();
        
        // Trier par date de création (les plus récentes en premier)
        $demandes = $demandes->sortBy('created_at');
        
        return view('poste.demandes-attribuees', compact('demandes','livreurs'));
    }

    public function demandesLivree()
    {
         $poste = Auth::guard('poste')->user();
        
        // Récupérer toutes les demandes attribuées à cette poste
        $demandes = collect();
        
        // Liste des modèles à vérifier
        $modeles = [
            'Naissance',
            'Deces',
            'Mariage'
        ];
        
        foreach ($modeles as $modele) {
            $classeModele = "App\\Models\\$modele";
            $demandesModele = $classeModele::where('livraison_id', Auth::guard('poste')->user()->id)
                ->with('user','livreur') // Si vous avez une relation avec l'utilisateur
                ->where('statut_livraison','livré')
                ->get()
                ->map(function($item) use ($modele) {
                    $item->type_demande = $this->getTypeDemande($modele);
                    return $item;
                });
                
            $demandes = $demandes->merge($demandesModele);
        }

        $livreurs = Livreur::get();
        
        // Trier par date de création (les plus récentes en premier)
        $demandes = $demandes->sortBy('created_at');
        
        return view('poste.demandes_livre', compact('demandes','livreurs'));
    }

    private function getTypeDemande($modele)
    {
        $types = [
            'Naissance' => 'Naissance',
            'Deces' => 'Décès',
            'Mariage' => 'Mariage'
        ];
        
        // Correction : "Décès" pour être propre
        return $types[$modele] ?? 'Demande';
    }

    public function assignerLivreur(Request $request)
    {
        Log::info('Assignation livreur debut', $request->all());

        $request->validate([
            'livreur_id' => 'required|exists:livreurs,id',
            'demandes' => 'required|array'
        ]);

        try {
            $updatedCount = 0;
            foreach ($request->demandes as $demande) {
                // On cherche par référence car le front envoie 'reference'
                $ref = $demande['reference'];
                $type = $demande['type'];
                
                // Mapper les types français aux noms de modèles
                $typeMap = [
                    'Naissance' => 'Naissance',
                    'Décès' => 'Deces',
                    'Mariage' => 'Mariage'
                ];
                
                $realType = $typeMap[$type] ?? $type;
                $modelClass = "App\\Models\\" . Str::studly($realType);
            
                if (class_exists($modelClass)) {
                    $item = $modelClass::where('reference', $ref)->first();
                    
                    if ($item) {
                        $item->livreur_id = $request->livreur_id;
                        $item->statut_livraison = 'en cours';
                        $item->save();
                        $updatedCount++;

                        // Notification push + DB
                        $user = $item->user;
                        if ($user) {
                            $user->notify(new GeneralPushNotification(
                                'Documents en cours de livraison',
                                "Votre acte de " . strtolower($realType) . " a été confié à un livreur.",
                                ['type' => 'livraison_en_cours', 'reference' => $item->reference]
                            ));
                        }
                    } else {
                        Log::warning("Colis non trouvé: $ref ($modelClass)");
                    }
                } else {
                    Log::error("Classe non trouvée: $modelClass");
                }
            }

            return back()->with('success', "$updatedCount colis assignés avec succès.");
        } catch (\Exception $e) {
            Log::error('Erreur assignation livreur: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'assignation : ' . $e->getMessage());
        }
    }

}