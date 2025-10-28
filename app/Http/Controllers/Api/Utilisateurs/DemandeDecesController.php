<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemandeDecesController extends Controller
{
    /**
     * Liste des demandes de décès de l'utilisateur
     * GET /api/utilisateurs/demandes/deces
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $deces = Deces::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des demandes de décès récupérée avec succès',
                'data' => [
                    'demandes' => $deces->map(function ($demande) {
                        return [
                            'id' => $demande->id,
                            'name' => $demande->name,
                            'numberR' => $demande->numberR,
                            'dateR' => $demande->dateR,
                            'reference' => $demande->reference,
                            'commune' => $demande->commune,
                            'etat' => $demande->etat,
                            'statut_livraison' => $demande->statut_livraison,
                            'choix_option' => $demande->choix_option,
                            'created_at' => $demande->created_at->format('Y-m-d H:i:s'),
                            'updated_at' => $demande->updated_at->format('Y-m-d H:i:s'),
                            'documents' => [
                                'CNIdfnt' => $demande->CNIdfnt ? Storage::url($demande->CNIdfnt) : null,
                                'CNIdcl' => $demande->CNIdcl ? Storage::url($demande->CNIdcl) : null,
                                'documentMariage' => $demande->documentMariage ? Storage::url($demande->documentMariage) : null,
                                'RequisPolice' => $demande->RequisPolice ? Storage::url($demande->RequisPolice) : null,
                            ]
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des demandes'
            ], 500);
        }
    }

    /**
     * Créer une nouvelle demande de décès
     * POST /api/utilisateurs/demandes/deces
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'numberR' => 'required|string|max:255',
            'dateR' => 'required|date',
            'CNIdfnt' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'CNIdcl' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'documentMariage' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'RequisPolice' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'choix_option' => 'required|in:retrait,livraison',
            'communeD' => 'nullable|string|max:255',
            'montant_timbre' => 'required_if:choix_option,livraison|numeric',
            'montant_livraison' => 'required_if:choix_option,livraison|numeric',
            'nom_destinataire' => 'required_if:choix_option,livraison|string|max:255',
            'prenom_destinataire' => 'required_if:choix_option,livraison|string|max:255',
            'email_destinataire' => 'required_if:choix_option,livraison|email',
            'contact_destinataire' => 'required_if:choix_option,livraison|string|max:20',
            'adresse_livraison' => 'required_if:choix_option,livraison|string|max:500',
            'code_postal' => 'nullable|string|max:10',
            'ville' => 'required_if:choix_option,livraison|string|max:255',
            'commune_livraison' => 'required_if:choix_option,livraison|string|max:255',
            'quartier' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();

            // Upload des fichiers
            $filesToUpload = [
                'CNIdfnt' => 'cnid',
                'CNIdcl' => 'cnid',
                'documentMariage' => 'mariage',
                'RequisPolice' => 'police',
            ];

            $uploadedPaths = [];
            foreach ($filesToUpload as $fileKey => $subDir) {
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    $extension = $file->getClientOriginalExtension();
                    $newFileName = (string) Str::uuid() . '.' . $extension;
                    $path = $file->storeAs("images/deces/$subDir", $newFileName, 'public');
                    $uploadedPaths[$fileKey] = "images/deces/$subDir/$newFileName";
                }
            }

            // Génération de la référence
            $communeInitiale = strtoupper(substr($request->communeD ?: $user->commune ?: 'X', 0, 1));
            $anneeCourante = Carbon::now()->year;
            $reference = 'AD' . str_pad(Deces::getNextId(), 4, '0', STR_PAD_LEFT) . $communeInitiale . $anneeCourante;

            // Création de la demande
            $deces = new Deces();
            $deces->name = $request->name;
            $deces->numberR = $request->numberR;
            $deces->dateR = $request->dateR;
            $deces->CNIdfnt = $uploadedPaths['CNIdfnt'] ?? null;
            $deces->CNIdcl = $uploadedPaths['CNIdcl'] ?? null;
            $deces->documentMariage = $uploadedPaths['documentMariage'] ?? null;
            $deces->RequisPolice = $uploadedPaths['RequisPolice'] ?? null;
            $deces->choix_option = $request->choix_option;
            $deces->commune = $request->communeD ?: $user->commune;
            $deces->etat = 'en attente';
            $deces->user_id = $user->id;
            $deces->reference = $reference;

            // Informations de livraison
            if ($request->choix_option === 'livraison') {
                $deces->montant_timbre = $request->montant_timbre;
                $deces->montant_livraison = $request->montant_livraison;
                $deces->nom_destinataire = $request->nom_destinataire;
                $deces->prenom_destinataire = $request->prenom_destinataire;
                $deces->email_destinataire = $request->email_destinataire;
                $deces->contact_destinataire = $request->contact_destinataire;
                $deces->adresse_livraison = $request->adresse_livraison;
                $deces->code_postal = $request->code_postal;
                $deces->ville = $request->ville;
                $deces->commune_livraison = $request->commune_livraison;
                $deces->quartier = $request->quartier;
            }

            $deces->save();

            return response()->json([
                'success' => true,
                'message' => 'Demande de décès créée avec succès',
                'data' => [
                    'demande' => [
                        'id' => $deces->id,
                        'reference' => $deces->reference,
                        'name' => $deces->name,
                        'numberR' => $deces->numberR,
                        'dateR' => $deces->dateR,
                        'commune' => $deces->commune,
                        'etat' => $deces->etat,
                        'choix_option' => $deces->choix_option,
                        'created_at' => $deces->created_at->format('Y-m-d H:i:s')
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la demande'
            ], 500);
        }
    }

    /**
     * Supprimer une demande de décès
     * DELETE /api/utilisateurs/demandes/deces/{deces}
     */
    public function destroy(Deces $deces): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Vérifier que l'utilisateur est propriétaire de la demande
            if ($deces->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à supprimer cette demande'
                ], 403);
            }

            $deces->delete();

            return response()->json([
                'success' => true,
                'message' => 'Demande de décès supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la demande'
            ], 500);
        }
    }
}