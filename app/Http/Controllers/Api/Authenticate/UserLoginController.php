<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Validator;

class UserLoginController extends Controller
{
    /**
     * Gère la connexion d'un utilisateur via l'API.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
                // <-- NOUVEAU : On accepte le token de l'app mobile
                'push_notification' => 'nullable|string|max:255', 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Recherche de l'utilisateur par email
            $user = User::where('email', $request->email)->first();

            // Vérification du mot de passe
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect.'
                ], 401);
            }

            // =================================================================
            // <-- NOUVEAU : ENREGISTREMENT DU TOKEN PUSH
            // =================================================================
            // Si l'application mobile envoie un token de notification,
            // on le met à jour pour cet utilisateur.
            // L'ancien token (si d'un autre appareil) sera écrasé.
            if ($request->filled('push_notification')) {
                if ($user->push_notification !== $request->push_notification) {
                    $user->push_notification = $request->push_notification;
                    $user->save();
                }
            }
            // =================================================================
            // FIN DU BLOC AJOUTÉ
            // =================================================================

            // Création du token Sanctum
            $token = $user->createToken('user-api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'commune' => $user->commune,
                        'contact' => $user->contact,
                        'profile_picture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
                        'diaspora' => $user->diaspora,
                        // On retourne le token push pour confirmation (optionnel)
                        'push_notification' => $user->push_notification, 
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error during API login: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la connexion.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request): JsonResponse // <-- MODIFIÉ : Ajout de Request
    {
        try {
            // Récupérer l'utilisateur authentifié
            $user = $request->user();

            // Suppression de tous les tokens d'API
            $user->tokens()->delete();

            // <-- AJOUT : Supprimer aussi le token de notification push
            // Puisque votre fonction déconnecte tous les appareils,
            // il est logique de désactiver aussi les notifications push.
            if ($user->push_notification) {
                $user->push_notification = null;
                $user->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error during API logout: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la déconnexion.'
            ], 500);
        }
    }
}