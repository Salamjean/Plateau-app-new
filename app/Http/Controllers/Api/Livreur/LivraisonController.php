<?php

namespace App\Http\Controllers\Api\Livreur;

use App\Http\Controllers\Controller;
use App\Models\Livreur;
use App\Models\Naissance;
use App\Models\Deces;
use App\Models\Mariage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class LivraisonController extends Controller
{
    /**
     * Liste des livraisons en cours
     */
    public function listeLivraisons(): JsonResponse
    {
        try {
            $livreur = Auth::guard('livreur')->user();
            
            if (!$livreur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livreur non authentifié'
                ], 401);
            }
            
            // Récupérer toutes les demandes attribuées à ce livreur
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
                    ->with('user', 'livreur')
                    ->where('statut_livraison', '!=', 'livré')
                    ->get()
                    ->map(function($item) use ($modele) {
                        $item->type_demande = $this->getTypeDemande($modele);
                        $item->type_model = strtolower($modele);
                        return $item;
                    });
                    
                $demandes = $demandes->merge($demandesModele);
            }
            
            // Trier par date de création (les plus récentes en premier)
            $demandes = $demandes->sortByDesc('created_at')->values();
            
            return response()->json([
                'success' => true,
                'message' => 'Liste des livraisons récupérée avec succès',
                'data' => [
                    'livraisons' => $demandes,
                    'total' => $demandes->count()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des livraisons: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer une livraison spécifique
     */
    public function getLivraison(Request $request, $type, $id): JsonResponse
    {
        try {
            $livreur = Auth::guard('livreur')->user();
            
            if (!$livreur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livreur non authentifié'
                ], 401);
            }

            // Validation du type
            $typesValides = ['naissance', 'deces', 'mariage'];
            if (!in_array(strtolower($type), $typesValides)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type de demande invalide'
                ], 400);
            }

            $modelClass = "App\\Models\\" . Str::studly($type);
            
            if (!class_exists($modelClass)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type de demande non supporté'
                ], 400);
            }

            $demande = $modelClass::where('id', $id)
                ->where('livreur_id', $livreur->id)
                ->with('user', 'livreur')
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livraison non trouvée ou non attribuée à ce livreur'
                ], 404);
            }

            // Ajouter le type de demande
            $demande->type_demande = $this->getTypeDemande(Str::studly($type));
            $demande->type_model = strtolower($type);

            return response()->json([
                'success' => true,
                'message' => 'Livraison récupérée avec succès',
                'data' => $demande
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la livraison: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Valider une livraison
     */
    public function validerLivraison(Request $request): JsonResponse
    {
        try {
            $livreur = Auth::guard('livreur')->user();
            
            if (!$livreur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livreur non authentifié'
                ], 401);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'reference' => 'required|string|max:255',
                'demande_id' => 'required|integer',
                'demande_type' => 'required|string|in:naissance,deces,mariage'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $modelClass = "App\\Models\\" . Str::studly($request->demande_type);
            
            if (!class_exists($modelClass)) {
                return response()->json([
                    'success' => false,
                    'message' => "Type de demande non valide"
                ], 400);
            }

            $demande = $modelClass::where('id', $request->demande_id)
                        ->where('reference', $request->reference)
                        ->where('statut_livraison', 'en cours')
                        ->where('livreur_id', $livreur->id)
                        ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demande non trouvée, déjà livrée ou non attribuée à ce livreur'
                ], 404);
            }

            // Mise à jour avec la date et heure actuelle
            $demande->update([
                'statut_livraison' => 'livré',
                'date_livraison' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Livraison confirmée avec succès',
                'data' => [
                    'reference' => $demande->reference,
                    'statut' => $demande->statut_livraison,
                    'date_livraison' => $demande->date_livraison
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la validation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des livraisons
     */
    public function statistiques(): JsonResponse
    {
        try {
            $livreur = Auth::guard('livreur')->user();
            
            if (!$livreur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livreur non authentifié'
                ], 401);
            }

            $statistiques = [
                'total' => 0,
                'livrees' => 0,
                'en_cours' => 0,
                'par_type' => [
                    'naissance' => 0,
                    'deces' => 0,
                    'mariage' => 0
                ]
            ];

            // Liste des modèles à vérifier
            $modeles = [
                'Naissance',
                'Deces',
                'Mariage'
            ];

            foreach ($modeles as $modele) {
                $classeModele = "App\\Models\\$modele";
                $type = strtolower($modele);
                
                // Total pour ce type
                $totalType = $classeModele::where('livreur_id', $livreur->id)->count();
                $statistiques['par_type'][$type] = $totalType;
                $statistiques['total'] += $totalType;
                
                // Livrées pour ce type
                $livreesType = $classeModele::where('livreur_id', $livreur->id)
                    ->where('statut_livraison', 'livré')
                    ->count();
                $statistiques['livrees'] += $livreesType;
                
                // En cours pour ce type
                $enCoursType = $classeModele::where('livreur_id', $livreur->id)
                    ->where('statut_livraison', 'en cours')
                    ->count();
                $statistiques['en_cours'] += $enCoursType;
            }

            // Pourcentage
            $statistiques['taux_livraison'] = $statistiques['total'] > 0 
                ? round(($statistiques['livrees'] / $statistiques['total']) * 100, 2)
                : 0;

            return response()->json([
                'success' => true,
                'message' => 'Statistiques récupérées avec succès',
                'data' => $statistiques
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier une référence
     */
    public function checkReference(Request $request): JsonResponse
    {
        try {
            $livreur = Auth::guard('livreur')->user();
            
            if (!$livreur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livreur non authentifié'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'reference' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $models = [
                'Naissance',
                'Deces',
                'Mariage'
            ];

            foreach ($models as $model) {
                $class = "App\\Models\\$model";
                $demande = $class::where('reference', $request->reference)
                            ->where('statut_livraison', 'en cours')
                            ->where('livreur_id', $livreur->id)
                            ->first();

                if ($demande) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'id' => $demande->id,
                            'type' => Str::slug($model),
                            'type_demande' => $this->getTypeDemande($model),
                            'destinataire' => $demande->nom_destinataire . ' ' . $demande->prenom_destinataire,
                            'contact' => $demande->contact_destinataire,
                            'adresse' => $demande->adresse_livraison,
                            'email' => $demande->email_destinataire,
                            'livraison_code' => $demande->livraison_code,
                            'commune' => $demande->commune_livraison,
                            'quartier' => $demande->quartier,
                            'ville' => $demande->ville,
                            'montant' => $demande->montant_livraison,
                            'reference' => $demande->reference
                        ]
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Aucune demande trouvée avec cette référence, déjà livrée ou non attribuée à vous'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper pour obtenir le type de demande
     */
    private function getTypeDemande($modele)
    {
        $types = [
            'Naissance' => 'Naissance',
            'Deces' => 'Deces',
            'Mariage' => 'Mariage'
        ];
        
        return $types[$modele] ?? 'Demande';
    }
}