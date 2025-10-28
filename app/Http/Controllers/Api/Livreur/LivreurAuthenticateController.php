<?php

namespace App\Http\Controllers\Api\Livreur;

use App\Http\Controllers\Controller;
use App\Models\Livreur;
use App\Models\ResetCodePasswordLivreur;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
class LivreurAuthenticateController extends Controller
{
    public function defineAccess(Request $request): JsonResponse
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:livreurs,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérification si le livreur existe déjà
        $livreur = Livreur::where('email', $request->email)->first();
        
        if ($livreur) {
            return response()->json([
                'success' => true,
                'message' => 'Livreur trouvé',
                'email' => $livreur->email
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Livreur non trouvé'
            ], 404);
        }
    }

    public function submitDefineAccess(Request $request): JsonResponse
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:livreurs,email',
            'code' => 'required|exists:reset_code_password_livreurs,code',
            'password' => 'required|min:8|same:confirme_password',
            'confirme_password' => 'required|min:8|same:password',
        ], [
            'code.exists' => 'Le code de réinitialisation est invalide.',
            'code.required' => 'Le code de réinitialisation est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
            'password.same' => 'Les mots de passe doivent être identiques.',
            'confirme_password.same' => 'Les mots de passe doivent être identiques.',
            'confirme_password.required' => 'Le mot de passe de confirmation est obligatoire.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $livreur = Livreur::where('email', $request->email)->first();
        
            if ($livreur) {
                // Mise à jour du mot de passe
                $livreur->password = Hash::make($request->password);
        
                // Vérifier si une image est uploadée
                if ($request->hasFile('profile_picture')) {
                    // Supprimer l'ancienne photo si elle existe
                    if ($livreur->profile_picture) {
                        Storage::delete('public/' . $livreur->profile_picture);
                    }
        
                    // Stocker la nouvelle image
                    $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                    $livreur->profile_picture = $imagePath;
                }
                
                $livreur->update();
        
                if($livreur){
                   $existingcodeajoint =  ResetCodePasswordLivreur::where('email', $livreur->email)->count();
        
                   if($existingcodeajoint > 1){
                    ResetCodePasswordLivreur::where('email', $livreur->email)->delete();
                   }
                }
        
                return response()->json([
                    'success' => true,
                    'message' => 'Compte mis à jour avec succès'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Email inconnu'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue : ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleLogin(Request $request): JsonResponse
    {
        try {
            Log::info('Livreur Login Attempt', $request->all());

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:livreurs,email',
                'password' => 'required|min:8',
            ], [
                'email.required' => 'Le mail est obligatoire.',
                'email.email' => 'Le format de l\'email est invalide.',
                'email.exists' => 'Cette adresse mail n\'existe pas.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Récupérer le livreur par son email
            $livreur = Livreur::where('email', $request->email)->first();

            if (!$livreur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livreur non trouvé'
                ], 404);
            }

            // Vérifier si le livreur est archivé
            if ($livreur->archived_at !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte a été supprimé. Vous ne pouvez pas vous connecter.'
                ], 403);
            }

            // Vérifier le mot de passe manuellement d'abord
            if (!Hash::check($request->password, $livreur->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre mot de passe est incorrect.'
                ], 401);
            }

            // Tenter la connexion avec le guard
            if (auth('livreur')->attempt($request->only('email', 'password'))) {
                $user = auth('livreur')->user();
                
                // Vérifier si le modèle utilise Sanctum
                if (method_exists($user, 'createToken')) {
                    $token = $user->createToken('LivreurToken')->plainTextToken;
                } else {
                    // Fallback si Sanctum n'est pas configuré
                    $token = bin2hex(random_bytes(32));
                }

                Log::info('Livreur Login Success', ['user_id' => $user->id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'token' => $token,
                    'user' => $user
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de l\'authentification'
                ], 401);
            }
        } catch (Exception $e) {
            Log::error('Livreur Login Error: ' . $e->getMessage());
            Log::error('Stack Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la connexion.'
            ], 500);
        }
    }
}