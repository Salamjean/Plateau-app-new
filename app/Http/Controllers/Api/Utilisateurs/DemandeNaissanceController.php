<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemandeNaissanceController extends Controller
{
    /**
     * Liste des demandes de naissance de l'utilisateur
     * GET /api/utilisateurs/demandes/naissance
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $naissances = Naissance::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des demandes de naissance récupérée avec succès',
                'data' => [
                    'demandes' => $naissances->map(function ($demande) {
                        return [
                            'id' => $demande->id,
                            'type' => $demande->type,
                            'pour' => $demande->pour,
                            'name' => $demande->name,
                            'prenom' => $demande->prenom,
                            'number' => $demande->number,
                            'DateR' => $demande->DateR,
                            'reference' => $demande->reference,
                            'commune' => $demande->commune,
                            'etat' => $demande->etat,
                            'statut_livraison' => $demande->statut_livraison,
                            'choix_option' => $demande->choix_option,
                            'created_at' => $demande->created_at->format('Y-m-d H:i:s'),
                            'updated_at' => $demande->updated_at->format('Y-m-d H:i:s'),
                            'documents' => [
                                'CNI' => $demande->CNI ? Storage::url($demande->CNI) : null,
                            ]
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur DemandeNaissanceController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des demandes'
            ], 500);
        }
    }

    /**
     * Créer une nouvelle demande de naissance
     * POST /api/utilisateurs/demandes/naissance
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'pour' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'number' => 'required|string|max:255',
            'DateR' => 'required|date',
            'commune' => 'required|string|max:255',
            'CNI' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'choix_option' => 'required|in:retrait,livraison',
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

            // Upload du fichier CNI
            $uploadedPaths = [];
            if ($request->hasFile('CNI')) {
                $file = $request->file('CNI');
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $path = $file->storeAs("images/naissances/cni", $newFileName, 'public');
                $uploadedPaths['CNI'] = "images/naissances/cni/$newFileName";
            }

            // Génération de la référence
            $communeInitiale = strtoupper(substr($request->commune ?: 'X', 0, 1));
            $anneeCourante = Carbon::now()->year;
            $reference = 'AN' . str_pad(Naissance::getNextId(), 4, '0', STR_PAD_LEFT) . $communeInitiale . $anneeCourante;

            // Création de la demande
            $naissance = new Naissance();
            $naissance->pour = $request->pour;
            $naissance->type = $request->type;
            $naissance->name = $request->name;
            $naissance->prenom = $request->prenom;
            $naissance->number = $request->number;
            $naissance->DateR = $request->DateR;
            $naissance->commune = $request->commune;
            $naissance->CNI = $uploadedPaths['CNI'] ?? null;
            $naissance->choix_option = $request->choix_option;
            $naissance->user_id = $user->id;
            $naissance->etat = 'en attente';
            $naissance->reference = $reference;

            // Informations de livraison
            if ($request->choix_option === 'livraison') {
                $naissance->montant_timbre = $request->montant_timbre;
                $naissance->montant_livraison = $request->montant_livraison;
                $naissance->nom_destinataire = $request->nom_destinataire;
                $naissance->prenom_destinataire = $request->prenom_destinataire;
                $naissance->email_destinataire = $request->email_destinataire;
                $naissance->contact_destinataire = $request->contact_destinataire;
                $naissance->adresse_livraison = $request->adresse_livraison;
                $naissance->code_postal = $request->code_postal;
                $naissance->ville = $request->ville;
                $naissance->commune_livraison = $request->commune_livraison;
                $naissance->quartier = $request->quartier;
            }

            $naissance->save();

            return response()->json([
                'success' => true,
                'message' => 'Demande de naissance créée avec succès',
                'data' => [
                    'demande' => [
                        'id' => $naissance->id,
                        'reference' => $naissance->reference,
                        'type' => $naissance->type,
                        'name' => $naissance->name,
                        'prenom' => $naissance->prenom,
                        'commune' => $naissance->commune,
                        'etat' => $naissance->etat,
                        'choix_option' => $naissance->choix_option,
                        'created_at' => $naissance->created_at->format('Y-m-d H:i:s')
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur DemandeNaissanceController@store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la demande'
            ], 500);
        }
    }
}