<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GoogleAuthController extends Controller
{
    /**
     * Gère la connexion et l'inscription via Google.
     */
    public function handleGoogleAuth(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_token' => 'required_without:google_id|string', // Jeton Firebase
            
            // Anciens champs (optionnels si id_token est présent)
            'email' => 'required_without:id_token|email',
            'google_id' => 'required_without:id_token|string',
            'name' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $email = $request->email;
            $googleId = $request->google_id;
            $nameStr = $request->name;
            $prenomStr = $request->prenom;
            $pictureUrl = $request->profile_picture;

            // 1. Vérification sécurisée si un id_token Firebase est fourni
            if ($request->filled('id_token')) {
                $verifier = \Kreait\Firebase\JWT\IdTokenVerifier::createWithProjectId('plateau-apps-user');
                try {
                    $token = $verifier->verifyIdToken($request->id_token);
                    $payload = $token->payload();
                    
                    $googleId = $payload['sub']; // L'identifiant unique Firebase
                    $email = $payload['email'] ?? $email;
                    $pictureUrl = $payload['picture'] ?? $pictureUrl;

                    // Séparation du nom et prénom depuis Firebase
                    if (isset($payload['name'])) {
                        $parts = explode(' ', $payload['name'], 2);
                        if (count($parts) > 1) {
                            $prenomStr = $parts[0];
                            $nameStr = $parts[1];
                        } else {
                            $nameStr = $payload['name'];
                            $prenomStr = '';
                        }
                    }

                } catch (\Kreait\Firebase\JWT\Error\IdTokenVerificationFailed $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jeton d\'authentification Firebase invalide ou expiré.',
                        'error' => config('app.debug') ? $e->getMessage() : null
                    ], 401);
                }
            }

            // Cherche si l'utilisateur existe déjà
            $user = User::where('google_id', $googleId)
                        ->orWhere('email', $email)
                        ->first();

            if ($user) {
                // CONNEXION
                if (empty($user->google_id)) {
                    $user->google_id = $googleId;
                    $user->save();
                }

                if ($user->deactivated_at) {
                    $dateDesactivation = \Carbon\Carbon::parse($user->deactivated_at);
                    if (now()->greaterThan($dateDesactivation->copy()->addDays(30))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Ce compte a été désactivé définitivement.'
                        ], 403);
                    } else {
                        $user->update(['deactivated_at' => null]);
                    }
                }

                $token = $user->createToken('user-api-token')->plainTextToken;
                
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion de compte existant réussie.',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'prenom' => $user->prenom,
                            'email' => $user->email,
                        ],
                        'token' => $token,
                        'token_type' => 'Bearer'
                    ]
                ], 200);
            } else {
                // INSCRIPTION Minimale
                $user = User::create([
                    'name' => $nameStr,
                    'prenom' => $prenomStr,
                    'email' => $email,
                    'google_id' => $googleId,
                    'commune' => 'plateau', 
                    'password' => Hash::make(Str::random(24)),
                ]);

                if (!empty($pictureUrl)) {
                    $user->profile_picture = $pictureUrl;
                    $user->save();
                }

                $token = $user->createToken('user-api-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Compte Google créé. Veuillez finaliser votre profil.',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'prenom' => $user->prenom,
                            'email' => $user->email,
                        ],
                        'token' => $token,
                        'token_type' => 'Bearer'
                    ]
                ], 201);
            }
        } catch (\Exception $e) {
            Log::error('Erreur Google Auth : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de lauthentification Google.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}

