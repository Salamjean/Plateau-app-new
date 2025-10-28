<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Mariage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemandeMariageController extends Controller
{
    /**
     * Liste des demandes de mariage de l'utilisateur
     * GET /api/utilisateurs/demandes/mariage
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $mariages = Mariage::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des demandes de mariage récupérée avec succès',
                'data' => [
                    'demandes' => $mariages->map(function ($demande) {
                        return [
                            'id' => $demande->id,
                            'nomEpoux' => $demande->nomEpoux,
                            'prenomEpoux' => $demande->prenomEpoux,
                            'dateNaissanceEpoux' => $demande->dateNaissanceEpoux,
                            'lieuNaissanceEpoux' => $demande->lieuNaissanceEpoux,
                            'reference' => $demande->reference,
                            'commune' => $demande->commune,
                            'etat' => $demande->etat,
                            'statut_livraison' => $demande->statut_livraison,
                            'choix_option' => $demande->choix_option,
                            'created_at' => $demande->created_at->format('Y-m-d H:i:s'),
                            'updated_at' => $demande->updated_at->format('Y-m-d H:i:s'),
                            'documents' => [
                                'pieceIdentite' => $demande->pieceIdentite ? Storage::url($demande->pieceIdentite) : null,
                                'extraitMariage' => $demande->extraitMariage ? Storage::url($demande->extraitMariage) : null,
                            ]
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des demandes'
            ], 500);
        }
    }

    /**
     * Créer une nouvelle demande de mariage
     * POST /api/utilisateurs/demandes/mariage
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nomEpoux' => 'required|string|max:255',
            'prenomEpoux' => 'required|string|max:255',
            'dateNaissanceEpoux' => 'required|date',
            'lieuNaissanceEpoux' => 'required|string|max:255',
            'pieceIdentite' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'extraitMariage' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'choix_option' => 'required|in:retrait,livraison',
            'commune' => 'nullable|string|max:255',
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
                'pieceIdentite' => 'identite',
                'extraitMariage' => 'extrait',
            ];

            $uploadedPaths = [];
            foreach ($filesToUpload as $fileKey => $subDir) {
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    $extension = $file->getClientOriginalExtension();
                    $newFileName = (string) Str::uuid() . '.' . $extension;
                    $path = $file->storeAs("images/mariages/$subDir", $newFileName, 'public');
                    $uploadedPaths[$fileKey] = "images/mariages/$subDir/$newFileName";
                }
            }

            // Génération de la référence
            $commune = $request->commune ?: $user->commune;
            $communeInitiale = strtoupper(substr($commune ?: 'X', 0, 1));
            $anneeCourante = Carbon::now()->year;
            $reference = 'AM' . str_pad(Mariage::getNextId(), 4, '0', STR_PAD_LEFT) . $communeInitiale . $anneeCourante;

            // Création de la demande
            $mariage = new Mariage();
            $mariage->nomEpoux = $request->nomEpoux;
            $mariage->prenomEpoux = $request->prenomEpoux;
            $mariage->dateNaissanceEpoux = $request->dateNaissanceEpoux;
            $mariage->lieuNaissanceEpoux = $request->lieuNaissanceEpoux;
            $mariage->pieceIdentite = $uploadedPaths['pieceIdentite'] ?? null;
            $mariage->extraitMariage = $uploadedPaths['extraitMariage'] ?? null;
            $mariage->commune = $commune;
            $mariage->choix_option = $request->choix_option;
            $mariage->etat = 'en attente';
            $mariage->user_id = $user->id;
            $mariage->reference = $reference;

            // Informations de livraison
            if ($request->choix_option === 'livraison') {
                $mariage->montant_timbre = $request->montant_timbre;
                $mariage->montant_livraison = $request->montant_livraison;
                $mariage->nom_destinataire = $request->nom_destinataire;
                $mariage->prenom_destinataire = $request->prenom_destinataire;
                $mariage->email_destinataire = $request->email_destinataire;
                $mariage->contact_destinataire = $request->contact_destinataire;
                $mariage->adresse_livraison = $request->adresse_livraison;
                $mariage->code_postal = $request->code_postal;
                $mariage->ville = $request->ville;
                $mariage->commune_livraison = $request->commune_livraison;
                $mariage->quartier = $request->quartier;
            }

            $mariage->save();

            return response()->json([
                'success' => true,
                'message' => 'Demande de mariage créée avec succès',
                'data' => [
                    'demande' => [
                        'id' => $mariage->id,
                        'reference' => $mariage->reference,
                        'nomEpoux' => $mariage->nomEpoux,
                        'prenomEpoux' => $mariage->prenomEpoux,
                        'commune' => $mariage->commune,
                        'etat' => $mariage->etat,
                        'choix_option' => $mariage->choix_option,
                        'created_at' => $mariage->created_at->format('Y-m-d H:i:s')
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la demande'
            ], 500);
        }
    }
}