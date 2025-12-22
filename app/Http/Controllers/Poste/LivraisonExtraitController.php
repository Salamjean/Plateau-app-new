<?php

namespace App\Http\Controllers\Poste;

use App\Http\Controllers\Controller;
use App\Models\Livreur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http; // <-- AJOUT

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

        $livreurs = Livreur::where('disponible','1')->get();
        
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
        $request->validate([
            'livreur_id' => 'required|exists:livreurs,id',
            'demandes' => 'required|array'
        ]);

        try {
            foreach ($request->demandes as $demande) {
                $modelClass = "App\\Models\\" . Str::studly($demande['type']);
                
                if (class_exists($modelClass)) {
                    // <-- MODIFICATION : On ne fait pas un 'update' groupé, on 'find'
                    $item = $modelClass::find($demande['id']);
                    
                    // On vérifie que la demande existe ET qu'elle est bien 'terminé'
                    if ($item && $item->etat === 'terminé') {
                        // On met à jour les champs et on sauvegarde
                        $item->livreur_id = $request->livreur_id;
                        $item->statut_livraison = 'en cours'; // Le statut est mis à jour ici
                        $item->save();

                        // =================================================================
                        // <-- NOUVEAU : BLOC D'ENVOI DE NOTIFICATION PUSH
                        // =================================================================
                        $item->load('user');
                        $user = $item->user;

                        if ($user && !empty($user->push_notification)) {
                            $title = 'Demande en cours de livraison';
                            $modelName = strtolower($this->getTypeDemande($demande['type'])); // "naissance", "décès"...
                            $body = "Votre demande ({$item->reference}) a été confiée à un livreur et est en cours de livraison.";
                            $data = ['url' => 'plateauapps://demande?reference=' . $item->reference];
                            
                            $this->sendPushNotification($user->push_notification, $title, $body, $data);
                        }
                        // =================================================================
                        // FIN DU BLOC DE NOTIFICATION
                        // =================================================================
                    }
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

    // =================================================================
    // <-- NOUVEAU : FONCTION PRIVÉE POUR ENVOYER LA NOTIFICATION
    // =================================================================
    private function sendPushNotification(string $userToken, string $title, string $body, array $data = [])
    {
        if (empty($userToken)) {
            Log::warning('Tentative d\'envoi de notif push sans token utilisateur.');
            return;
        }

        $payload = [
            'to' => $userToken,
            'sound' => 'default',
            'title' => $title,
            'body' => $body,
            'data' => (object) $data,
        ];

        try {
            $response = Http::withoutVerifying() // Utilise la solution pour localhost
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate',
            ])->post('https://exp.host/--/api/v2/push/send', $payload);

            if ($response->failed()) {
                Log::error('Échec envoi notification Expo: ' . $response->body());
            } else {
                Log::info('Notification Expo envoyée: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Exception lors de lenvoi de notification Expo: ' . $e->getMessage());
        }
    }
}