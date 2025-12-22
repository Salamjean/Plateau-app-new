<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Rendezvous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class RdvApiController extends Controller
{
    /**
     * Affiche la liste des rendez-vous pour l'utilisateur authentifié.
     */
    public function index(Request $request)
    {
        try {
            // Récupérer l'utilisateur authentifié via Sanctum
            $user = $request->user();
            
            // Récupérer les rendez-vous triés par date de création
            $rendezvous = Rendezvous::where('user_id', $user->id)
                                    ->orderBy('created_at', 'desc') // J'ai mis 'desc' pour voir les plus récents en premier
                                    ->get();

            // Retourner les données en JSON
            return response()->json($rendezvous);

        } catch (Exception $e) {
            Log::error("Erreur API [RdvApiController@index]: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Impossible de récupérer la liste des rendez-vous.'
            ], 500);
        }
    }

    /**
     * Stocke une nouvelle demande de rendez-vous.
     */
    public function store(Request $request)
    {
        try {
            // Validation des données entrantes
            $validated = $request->validate([
                'nom_epoux' => 'required|string|max:255',
                'prenom_epoux' => 'required|string|max:255',
                'date_naissance_epoux' => 'required|date',
                'lieu_naissance_epoux' => 'required|string|max:255',
                'nom_epouse' => 'required|string|max:255',
                'prenom_epouse' => 'required|string|max:255',
                'date_naissance_epouse' => 'required|date',
                'lieu_naissance_epouse' => 'required|string|max:255',
                'adresse' => 'required|string|max:255',
                'telephone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'date_mariage_souhaitee' => 'required|date|after:today',
                'heure_souhaitee' => 'required',
                'motif' => 'required|string|max:255', // <--- AJOUT DU CHAMP MOTIF
            ], [
                // Messages d'erreur personnalisés
                'required' => 'Le champ :attribute est obligatoire.',
                'string' => 'Le champ :attribute doit être une chaîne de caractères.',
                'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
                'date' => 'Le champ :attribute doit être une date valide.',
                'email' => 'Le champ :attribute doit être une adresse email valide.',
                'after' => 'La date de mariage doit être postérieure à aujourd\'hui.',
                'nom_epoux.required' => 'Le nom de l\'époux est obligatoire.',
                'prenom_epoux.required' => 'Le prénom de l\'époux est obligatoire.',
                'nom_epouse.required' => 'Le nom de l\'épouse est obligatoire.',
                'prenom_epouse.required' => 'Le prénom de l\'épouse est obligatoire.',
                'telephone.required' => 'Un numéro de téléphone est nécessaire pour vous contacter.',
                'email.required' => 'Une adresse email est nécessaire pour la confirmation.',
                'date_mariage_souhaitee.after' => 'La date du mariage doit être dans le futur.',
                'motif.required' => 'Le motif du rendez-vous est obligatoire.', // <--- MESSAGE PERSO
            ]);

            // Création de l'instance
            $rendezvous = new Rendezvous();
            
            // Attribution des valeurs validées
            $rendezvous->nom_epoux = $validated['nom_epoux'];
            $rendezvous->prenom_epoux = $validated['prenom_epoux'];
            $rendezvous->date_naissance_epoux = $validated['date_naissance_epoux'];
            $rendezvous->lieu_naissance_epoux = $validated['lieu_naissance_epoux'];
            $rendezvous->nom_epouse = $validated['nom_epouse'];
            $rendezvous->prenom_epouse = $validated['prenom_epouse'];
            $rendezvous->date_naissance_epouse = $validated['date_naissance_epouse'];
            $rendezvous->lieu_naissance_epouse = $validated['lieu_naissance_epouse'];
            $rendezvous->adresse = $validated['adresse'];
            $rendezvous->telephone = $validated['telephone'];
            $rendezvous->email = $validated['email'];
            $rendezvous->date_mariage_souhaitee = $validated['date_mariage_souhaitee'];
            $rendezvous->heure_souhaitee = $validated['heure_souhaitee'];
            $rendezvous->motif = $validated['motif']; // <--- ENREGISTREMENT DU MOTIF
            
            // Attribution des champs supplémentaires
            $rendezvous->mairie = 'plateau';
            $rendezvous->user_id = Auth::user()->id; 
            $rendezvous->statut = 'en attente';
            
            // Sauvegarde
            $rendezvous->save();

            // Retourner une réponse JSON de succès (Code 201 Created)
            return response()->json(
                $rendezvous,
                201
            );

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error("Erreur API [RdvApiController@store]: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue lors de la création de la demande.'
            ], 500);
        }
    }

    /**
     * Annule un rendez-vous existant.
     */
    public function cancel(Request $request, $id)
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = $request->user();
            
            // Trouver le rendez-vous
            $rendezvous = Rendezvous::find($id);
            
            // Vérifier si le rendez-vous existe
            if (!$rendezvous) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rendez-vous introuvable.'
                ], 404);
            }
            
            // Vérifier que le rendez-vous appartient bien à l'utilisateur
            if ($rendezvous->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à annuler ce rendez-vous.'
                ], 403);
            }
            
            // Vérifier si le rendez-vous n'est pas déjà annulé
            if ($rendezvous->statut === 'annulé') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce rendez-vous est déjà annulé.'
                ], 400);
            }
            
            // Mettre à jour le statut
            $rendezvous->statut = 'annulé';
            $rendezvous->save();
            
            // Retourner une réponse JSON de succès
            return response()->json([
                'success' => true,
                'message' => 'Rendez-vous annulé avec succès.',
                'rendezvous' => $rendezvous
            ], 200);
            
        } catch (Exception $e) {
            Log::error("Erreur API [RdvApiController@cancel]: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur interne est survenue lors de l\'annulation du rendez-vous.'
            ], 500);
        }
    }
  
}