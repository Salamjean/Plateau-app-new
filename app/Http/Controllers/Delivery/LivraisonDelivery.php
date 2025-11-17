<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Livreur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http; // <-- AJOUT
use Illuminate\Support\Facades\Log;  // <-- AJOUT

class LivraisonDelivery extends Controller
{
    public function delivery(){
        $livreur = Auth::guard('livreur')->user();
        
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
            $demandesModele = $classeModele::where('livreur_id', $livreur->id)
                ->with('user','livreur') // Si vous avez une relation avec l'utilisateur
                ->where('statut_livraison', '!=','livré')
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
        return view('livreur.livraison.demandes-attribuees', compact('demandes','livreurs'));
    }

    public function livree()
    {
        $livreur = Auth::guard('livreur')->user();
        
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
            $demandesModele = $classeModele::where('livreur_id', $livreur->id)
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
        
        return view('livreur.livraison.demandes_livre', compact('demandes','livreurs'));
    }

    private function getTypeDemande($modele)
    {
        $types = [
            'Naissance' => 'Naissance',
            'Deces' => 'Décès', // Correction
            'Mariage' => 'Mariage'
        ];
        
        return $types[$modele] ?? 'Demande';
    }

    public function validated(Request $request)
    {
        // Vérification de la méthode HTTP
        if (!$request->isMethod('post')) {
            return view('livreur.livraison.validate');
        }

        $request->validate([
            'reference' => 'required|string|max:255',
            'demande_id' => 'required|integer',
            'demande_type' => 'required|string', // Assurez-vous que le type est envoyé
        ]);

        try {
            $modelClass = "App\\Models\\" . Str::studly(str_replace('-', '', $request->demande_type));
            
            if (!class_exists($modelClass)) {
                throw new \Exception("Type de demande non valide");
            }

            $demande = $modelClass::where('id', $request->demande_id)
                                ->where('reference', $request->reference)
                                ->where('statut_livraison', 'en cours')
                                ->firstOrFail();

            // L'état 'terminé' est requis pour que 'statut_livraison' soit 'en cours',
            // donc la condition est remplie.

            // Mise à jour avec la date et heure actuelle
            $demande->statut_livraison = 'livré';
            $demande->livreur_id = Auth::guard('livreur')->user()->id;
            $demande->save(); // On utilise save() au lieu de update() pour garder l'objet

            // =================================================================
            // <-- NOUVEAU : BLOC D'ENVOI DE NOTIFICATION PUSH
            // =================================================================
            $demande->load('user');
            $user = $demande->user;

            if ($user && !empty($user->push_notification)) {
                $title = 'Demande Livrée';
                $modelName = strtolower($this->getTypeDemande(Str::studly($request->demande_type)));
                $body = "Votre acte demande de {$modelName} ({$demande->reference}) a été livrée avec succès.";
                $data = ['url' => 'plateauapps://demande?reference=' . $demande->reference];
                
                $this->sendPushNotification($user->push_notification, $title, $body, $data);
            }
            // =================================================================
            // FIN DU BLOC DE NOTIFICATION
            // =================================================================

            return response()->json([
                'success' => true,
                'message' => 'Livraison confirmée avec succès',
                'redirect' => route('livreur.livree') // URL de la page de confirmation
            ]);

        } catch (\Exception $e) {
            // <-- MODIFIÉ : Retourner une réponse JSON en cas d'erreur
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 422); // 422 Unprocessable Entity ou 404 Not Found
        }
    }

    public function checkReference(Request $request)
    {
        $request->validate(['reference' => 'required|string']);

        $models = [
            'Naissance',
            'Deces',
            'Mariage'
        ];

        foreach ($models as $model) {
            $class = "App\\Models\\$model";
            $demande = $class::where('reference', $request->reference)
                            ->where('statut_livraison', 'en cours')
                            ->first();

            if ($demande) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $demande->id,
                        'type' => Str::slug($model), // 'naissance', 'deces', 'mariage'
                        'destinataire' => $demande->nom_destinataire . ' ' . $demande->prenom_destinataire,
                        'contact' => $demande->contact_destinataire,
                        'adresse' => $demande->adresse_livraison,
                        'email' => $demande->email_destinataire,
                        'livraison_code' => $demande->livraison_code,
                        'commune' => $demande->commune_livraison,
                        'quartier' => $demande->quartier,
                        'ville' => $demande->ville,
                        'montant' => $demande->montant_livraison
                    ]
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucune demande trouvée avec cette référence ou déjà livrée'
        ], 404);
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