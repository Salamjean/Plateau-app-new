<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse; // <-- AJOUTEZ CET IMPORT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // <-- AJOUTEZ CET IMPORT
use Illuminate\Support\Facades\Storage; // <-- AJOUTEZ CET IMPORT
use Illuminate\Support\Facades\Validator;

class UserLoginController extends Controller
{
    /**
     * Gère la connexion d'un utilisateur via l'API.
     */
    public function login(Request $request): JsonResponse // <-- JsonResponse vient de Illuminate\Http\JsonResponse
    {
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
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
    public function logout(): JsonResponse
    {
        try {
            // Suppression de tous les tokens de l'utilisateur
            auth()->user()->tokens()->delete();

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