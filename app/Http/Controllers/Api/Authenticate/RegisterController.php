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
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Inscription minimale (Etape 2 après l'OTP)
     * Enregistre l'utilisateur avec son numéro uniquement.
     */
    public function registerMinimal(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'indicatif' => 'required|string|max:10',
            'contact' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        // Vérification OTP
        $indicatif = preg_replace('/[^0-9]/', '', $request->indicatif);
        $contact = preg_replace('/[^0-9]/', '', $request->contact);
        $phone = $indicatif . $contact;
        
        $isPhoneVerified = \Illuminate\Support\Facades\Cache::get('otp_verified_' . $phone);
        
        if (!$isPhoneVerified && !config('app.debug')) {
            return response()->json([
                'success' => false, 
                'message' => 'Le numéro de téléphone n\'a pas été vérifié par OTP.'
            ], 403);
        }

        try {
            // Cherche si l'utilisateur existe déjà
            $user = User::where('contact', $contact)
                        ->where('indicatif', $request->indicatif)
                        ->first();

            if (!$user) {
                // Création d'un utilisateur "vide" (nom/prénom nullables)
                $user = User::create([
                    'indicatif' => $request->indicatif,
                    'contact' => $request->contact,
                    'commune' => 'plateau',
                    'phone_verified_at' => now(),
                    // Pas de mot de passe encore
                ]);
            }

            if ($isPhoneVerified) {
                \Illuminate\Support\Facades\Cache::forget('otp_verified_' . $phone);
            }

            // Génération du token (valide 10 minutes)
            $token = $user->createToken('user-api-token', ['*'], now()->addMinutes(10))->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Inscription initiale réussie. Veuillez compléter votre profil.',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'contact' => $user->contact,
                        'indicatif' => $user->indicatif,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur registerMinimal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription initiale.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function register(Request $request): JsonResponse
    {
        // Validation manuelle (champ 'commune' retiré)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'NNI' => 'nullable|string|max:50',
            'password' => 'required|string|min:8|confirmed',
            // 'commune' => 'required|string|max:255', // Retiré de la validation
            'indicatif' => 'required|string|max:10',
            'contact' => 'required|string|max:20|unique:users,contact',
            'CMU' => 'nullable|string|max:50',
            'profile_picture' => 'nullable',
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

        // Vérification que le numéro a été vérifié (Optionnel mais sécurisé)
        $indicatif = preg_replace('/[^0-9]/', '', $request->indicatif);
        $contact = preg_replace('/[^0-9]/', '', $request->contact);
        $phone = $indicatif . $contact;
        
        $isPhoneVerified = \Illuminate\Support\Facades\Cache::get('otp_verified_' . $phone);
        // Si vous voulez forcer la vérification OTP dans tous les cas, décommentez ceci :
        // if (!$isPhoneVerified) {
        //     return response()->json(['success' => false, 'message' => 'Le numéro de téléphone na pas été vérifié par OTP.'], 403);
        // }

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
                'NNI' => $request->NNI,
                'commune' => 'plateau', // <-- Valeur définie automatiquement
                'indicatif' => $request->indicatif,
                'contact' => $request->contact,
                'phone_verified_at' => $isPhoneVerified ? now() : null,
                'CMU' => $request->CMU,
                'password' => Hash::make($request->password),
                'profile_picture' => $profilePicturePath,
                'diaspora' => $request->boolean('diaspora', false),
                'pays_residence' => $request->boolean('diaspora') ? $request->pays_residence : null,
                'ville_residence' => $request->boolean('diaspora') ? $request->ville_residence : null,
                'adresse_etrangere' => $request->boolean('diaspora') ? $request->adresse_etrangere : null,
            ]);

            if ($isPhoneVerified) {
                \Illuminate\Support\Facades\Cache::forget('otp_verified_' . $phone);
            }

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
                        'commune' => $user->commune,
                        'contact' => $user->contact,
                        'profile_picture' => $user->profile_picture ? (Str::startsWith($user->profile_picture, ['http://', 'https://']) ? $user->profile_picture : Storage::url($user->profile_picture)) : null,
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