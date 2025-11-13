<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        // Validation manuelle (champ 'commune' retiré)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            // 'commune' => 'required|string|max:255', // Retiré de la validation
            'indicatif' => 'required|string|max:10',
            'contact' => 'required|string|max:20',
            'CMU' => 'nullable|string|max:50',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'diaspora' => 'nullable|boolean',
            'pays_residence' => 'required_if:diaspora,true|string|max:255',
            'ville_residence' => 'required_if:diaspora,true|string|max:255',
            'adresse_etrangere' => 'required_if:diaspora,true|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $profilePicturePath = null;
            
            // Gestion de l'image de profil
            if ($request->hasFile('profile_picture')) {
                $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                Log::info('Profile picture stored at: ' . $profilePicturePath);
            }
            
            // Création de l'utilisateur (champ 'commune' défini sur 'Plateau')
            $user = User::create([
                'name' => $request->name,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'commune' => 'plateau', // <-- Valeur définie automatiquement
                'indicatif' => $request->indicatif,
                'contact' => $request->contact,
                'CMU' => $request->CMU,
                'password' => Hash::make($request->password),
                'profile_picture' => $profilePicturePath,
                'diaspora' => $request->boolean('diaspora', false),
                'pays_residence' => $request->boolean('diaspora') ? $request->pays_residence : null,
                'ville_residence' => $request->boolean('diaspora') ? $request->ville_residence : null,
                'adresse_etrangere' => $request->boolean('diaspora') ? $request->adresse_etrangere : null,
            ]);

            // Création du token Sanctum
            $token = $user->createToken('user-api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Votre compte a été créé avec succès.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'commune' => $user->commune, // Retournera 'Plateau'
                        'contact' => $user->contact,
                        'profile_picture' => $user->profile_picture ? Storage::url($user->profile_picture) : null,
                        'diaspora' => $user->diaspora,
                        'pays_residence' => $user->pays_residence,
                        'ville_residence' => $user->ville_residence,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error during API registration: ' . $e->getMessage());
            
            // Supprimer l'image uploadée en cas d'erreur
            if (isset($profilePicturePath) && Storage::disk('public')->exists($profilePicturePath)) {
                Storage::disk('public')->delete($profilePicturePath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la création du compte.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}