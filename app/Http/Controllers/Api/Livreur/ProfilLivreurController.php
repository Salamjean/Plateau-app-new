<?php

namespace App\Http\Controllers\Api\Livreur;
use App\Http\Controllers\Controller;
use App\Models\Livreur;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
class ProfilLivreurController extends Controller
{
    /**
     * Récupérer le profil du livreur connecté
     */
    public function getProfil(Request $request): JsonResponse
    {
        try {
            // Méthode 1: Récupérer l'utilisateur via Sanctum
            $user = $request->user();
            
            // Méthode 2: Vérifier si c'est un Livreur
            if (!$user || !$user instanceof Livreur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié ou non livreur',
                    'user_type' => $user ? get_class($user) : 'null'
                ], 401);
            }

            // Préparer les données de réponse
            $profileData = [
                'id' => $user->id,
                'name' => $user->name,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'contact' => $user->contact,
                'profile_picture' => $user->profile_picture,
                'profile_picture_url' => $user->profile_picture ? 
                    Storage::url($user->profile_picture) : null,
                'commune' => $user->commune,
                'communeM' => $user->communeM,
                'cas_urgence' => $user->cas_urgence,
                'disponible' => $user->disponible,
                'poste_id' => $user->poste_id,
                'archived_at' => $user->archived_at,
            ];

            // Charger la relation poste si elle existe
            if ($user->relationLoaded('poste')) {
                $profileData['poste'] = $user->poste;
            } elseif (method_exists($user, 'poste')) {
                $profileData['poste'] = $user->poste;
            }

            return response()->json([
                'success' => true,
                'message' => 'Profil récupéré avec succès',
                'data' => $profileData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mise à jour de la photo de profil
     */
    public function updateProfilePicture(Request $request): JsonResponse
    {
        try {
            $livreur = $request->user();
           
            $validator = Validator::make($request->all(), [
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            ], [
                'profile_picture.required' => 'La photo de profil est obligatoire',
                'profile_picture.image' => 'Le fichier doit être une image',
                'profile_picture.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif',
                'profile_picture.max' => 'L\'image ne doit pas dépasser 5MB',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Supprimer l'ancienne photo si elle existe
            if ($livreur->profile_picture) {
                Storage::disk('public')->delete($livreur->profile_picture);
            }

            // Stocker la nouvelle image
            $imagePath = $request->file('profile_picture')->store('profile_pictures/livreurs', 'public');
            
            // Mise à jour du livreur
            $livreur->update([
                'profile_picture' => $imagePath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès',
                'data' => [
                    'profile_picture' => $imagePath,
                    'profile_picture_url' => Storage::url($imagePath)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer la photo de profil
     */
    public function deleteProfilePicture(Request $request): JsonResponse // AJOUT DU PARAMÈTRE
    {
        try {
            $livreur = $request->user(); // CORRECTION : Utilisation de $request

            if (!$livreur || !$livreur instanceof Livreur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livreur non authentifié'
                ], 401);
            }

            if (!$livreur->profile_picture) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune photo de profil à supprimer'
                ], 404);
            }

            // Vérifier si le fichier existe avant de le supprimer
            if (Storage::disk('public')->exists($livreur->profile_picture)) {
                Storage::disk('public')->delete($livreur->profile_picture);
            }

            // Mettre à jour la base de données
            $livreur->update([
                'profile_picture' => null
            ]);

            // Recharger les données
            $livreur->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil supprimée avec succès',
                'data' => [
                    'profile_picture' => null,
                    'profile_picture_url' => null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Delete Profile Picture Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mise à jour des informations personnelles
     */
    public function updateInformations(Request $request): JsonResponse
    {
        try {
            $livreur = $request->user();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'prenom' => 'sometimes|string|max:255',
                'contact' => 'sometimes|string|max:20',
                'commune' => 'sometimes|string|max:255',
                'communeM' => 'sometimes|string|max:255',
                'cas_urgence' => 'sometimes|string|max:255',
                'disponible' => 'sometimes|boolean',
            ], [
                'name.string' => 'Le nom doit être une chaîne de caractères',
                'name.max' => 'Le nom ne doit pas dépasser 255 caractères',
                'prenom.string' => 'Le prénom doit être une chaîne de caractères',
                'prenom.max' => 'Le prénom ne doit pas dépasser 255 caractères',
                'contact.string' => 'Le contact doit être une chaîne de caractères',
                'contact.max' => 'Le contact ne doit pas dépasser 20 caractères',
                'commune.string' => 'La commune doit être une chaîne de caractères',
                'commune.max' => 'La commune ne doit pas dépasser 255 caractères',
                'communeM.string' => 'La commune M doit être une chaîne de caractères',
                'communeM.max' => 'La commune M ne doit pas dépasser 255 caractères',
                'cas_urgence.string' => 'Le cas d\'urgence doit être une chaîne de caractères',
                'cas_urgence.max' => 'Le cas d\'urgence ne doit pas dépasser 255 caractères',
                'disponible.boolean' => 'Le champ disponible doit être vrai ou faux',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Préparer les données à mettre à jour
            $updateData = [];
            $champsAutorises = ['name', 'prenom', 'contact', 'commune', 'communeM', 'cas_urgence', 'disponible'];
            
            foreach ($champsAutorises as $champ) {
                if ($request->has($champ)) {
                    $updateData[$champ] = $request->$champ;
                }
            }

            // Vérifier s'il y a des données à mettre à jour
            if (empty($updateData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune donnée à mettre à jour'
                ], 400);
            }

            // Mise à jour du livreur
            $livreur->update($updateData);

            // Recharger les données fraîches
            $livreur->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Informations mises à jour avec succès',
                'data' => [
                    'user' => $livreur
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des informations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mise à jour du mot de passe
     */
    public function updatePassword(Request $request): JsonResponse
    {
        try {
            $livreur = $request->user();

            if (!$livreur || !$livreur instanceof Livreur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livreur non authentifié'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|different:current_password',
                'confirm_password' => 'required|string|same:new_password',
            ], [
                'current_password.required' => 'Le mot de passe actuel est obligatoire',
                'new_password.required' => 'Le nouveau mot de passe est obligatoire',
                'new_password.min' => 'Le nouveau mot de passe doit avoir au moins 8 caractères',
                'new_password.different' => 'Le nouveau mot de passe doit être différent de l\'actuel',
                'confirm_password.required' => 'La confirmation du mot de passe est obligatoire',
                'confirm_password.same' => 'La confirmation du mot de passe ne correspond pas',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier le mot de passe actuel
            if (!Hash::check($request->current_password, $livreur->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe actuel est incorrect'
                ], 422);
            }

            // Mettre à jour le mot de passe
            $livreur->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe mis à jour avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Update Password Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du mot de passe: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mise à jour de la disponibilité
     */
    public function updateDisponibilite(Request $request): JsonResponse
    {
        try {
            $livreur = $request->user();

            $validator = Validator::make($request->all(), [
                'disponible' => 'required|boolean',
            ], [
                'disponible.required' => 'Le champ disponible est obligatoire',
                'disponible.boolean' => 'Le champ disponible doit être vrai ou faux',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $livreur->update([
                'disponible' => $request->disponible
            ]);

            $statut = $request->disponible ? 'disponible' : 'indisponible';

            return response()->json([
                'success' => true,
                'message' => "Statut de disponibilité mis à jour: {$statut}",
                'data' => [
                    'disponible' => $livreur->disponible
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la disponibilité: ' . $e->getMessage()
            ], 500);
        }
    }
}