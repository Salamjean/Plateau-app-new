<?php

namespace App\Http\Controllers\Api\Utilisateurs\Profil;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserProfilController extends Controller
{
    /**
     * Récupérer les informations du profil utilisateur
     * GET /api/utilisateurs/profil
     */
    public function getProfil(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'message' => 'Profil récupéré avec succès',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'indicatif' => $user->indicatif,
                        'contact' => $user->contact,
                        'commune' => $user->commune,
                        'CMU' => $user->CMU,
                        'profile_picture' => $user->profile_picture ? (Str::startsWith($user->profile_picture, ['http://', 'https://']) ? $user->profile_picture : Storage::url($user->profile_picture)) : null,
                        'diaspora' => (bool) $user->diaspora,
                        'pays_residence' => $user->pays_residence,
                        'ville_residence' => $user->ville_residence,
                        'adresse_etrangere' => $user->adresse_etrangere,
                        'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur getProfil: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du profil'
            ], 500);
        }
    }

    /**
     * Mise à jour de la photo de profil
     * POST /api/utilisateurs/profil/photo
     */
    public function updateProfilePicture(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $profilePicture = $request->input('profile_picture');
            $file = $request->file('profile_picture');

            // Supprimer l'ancienne photo
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = null;

            // CAS 1: Base64 (mobile, caméra)
            if (is_string($profilePicture)) {
                // Nettoyer et décoder
                $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $profilePicture);
                $imageData = base64_decode($base64Data);

                if ($imageData !== false) {
                    $fileName = 'profile_' . time() . '_' . Str::random(10) . '.jpg';
                    $path = 'profile_pictures/' . $fileName;
                    Storage::disk('public')->put($path, $imageData);
                }
            }
            // CAS 2: Fichier uploadé (tous formats acceptés)
            elseif ($file && $file->isValid()) {
                $extension = $file->getClientOriginalExtension();
                $fileName = 'profile_' . time() . '_' . Str::random(10) . '.' . $extension;
                $path = $file->storeAs('profile_pictures', $fileName, 'public');
            }

            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de traiter l\'image'
                ], 400);
            }

            $user->update(['profile_picture' => $path]);

            return response()->json([
                'success' => true,
                'message' => 'Photo mise à jour avec succès',
                'data' => [
                    'profile_picture' => Storage::url($path),
                    'profile_picture_path' => $path
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur updateProfilePicture: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer la photo de profil
     * DELETE /api/utilisateurs/profil/photo
     */
    public function deleteProfilePicture(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $user->update(['profile_picture' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur deleteProfilePicture: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la photo de profil'
            ], 500);
        }
    }
    /**
     * Mise à jour des informations personnelles
     * PUT /api/utilisateurs/profil/informations
     */
    public function updateInformations(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string', // Ajout de la vérification du mot de passe
            'name' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'indicatif' => 'sometimes|required|string|max:10',
            'contact' => 'sometimes|required|string|max:20',
            'commune' => 'sometimes|required|string|max:255',
            'CMU' => 'sometimes|nullable|string|max:255',
            'diaspora' => 'sometimes|boolean',
            'pays_residence' => 'nullable|required_if:diaspora,true|string|max:255',
            'ville_residence' => 'nullable|required_if:diaspora,true|string|max:255',
            'adresse_etrangere' => 'nullable|required_if:diaspora,true|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Vérifier le mot de passe actuel
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe incorrect. Veuillez vérifier votre mot de passe actuel.'
                ], 422);
            }

            // Préparer les données de mise à jour
            $updateData = [];
            $champs = [
                'name',
                'prenom',
                'email',
                'indicatif',
                'contact',
                'commune',
                'CMU',
                'diaspora',
                'pays_residence',
                'ville_residence',
                'adresse_etrangere'
            ];

            foreach ($champs as $champ) {
                if ($request->has($champ)) {
                    $updateData[$champ] = $request->$champ;
                }
            }

            // Gestion spéciale pour la diaspora
            if (isset($updateData['diaspora']) && !$updateData['diaspora']) {
                $updateData['pays_residence'] = null;
                $updateData['ville_residence'] = null;
                $updateData['adresse_etrangere'] = null;
            }

            // Mise à jour de l'utilisateur
            $user->update($updateData);
            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Informations mises à jour avec succès',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'prenom' => $user->prenom,
                        'email' => $user->email,
                        'indicatif' => $user->indicatif,
                        'contact' => $user->contact,
                        'commune' => $user->commune,
                        'CMU' => $user->CMU,
                        'diaspora' => (bool) $user->diaspora,
                        'pays_residence' => $user->pays_residence,
                        'ville_residence' => $user->ville_residence,
                        'adresse_etrangere' => $user->adresse_etrangere,
                        'profile_picture' => $user->profile_picture ? (Str::startsWith($user->profile_picture, ['http://', 'https://']) ? $user->profile_picture : Storage::url($user->profile_picture)) : null,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur updateInformations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des informations'
            ], 500);
        }
    }

    /**
     * Mise à jour du mot de passe
     * PUT /api/utilisateurs/profil/password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();

            // Vérifier le mot de passe actuel
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe actuel est incorrect'
                ], 422);
            }

            // Mettre à jour le mot de passe
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur updatePassword: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du mot de passe'
            ], 500);
        }
    }
}
