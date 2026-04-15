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
use Carbon\Carbon;
class UserLoginController extends Controller
{
    /**
     * Gère la connexion d'un utilisateur via l'API.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'login_identifier' => 'required|string', // Peut être un email ou un numéro de téléphone
                'password' => 'required|string',
                'push_notification' => 'nullable|string|max:255', 
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
            }

            $loginIdentifier = $request->login_identifier;

            // Déterminer si l'identifiant est un email ou un téléphone (s'il ne contient que des chiffres et des +, espaces, etc.)
            $cleanPhone = preg_replace('/[^0-9]/', '', $loginIdentifier);
            
            if (filter_var($loginIdentifier, FILTER_VALIDATE_EMAIL)) {
                $user = User::where('email', $loginIdentifier)->first();
            } else {
                // Si c'est un numéro de téléphone, vérifier s'il correspond au 'contact' ou à la concaténation 'indicatif'+'contact'
                $user = User::where('contact', $cleanPhone)
                            ->orWhereRaw("CONCAT(indicatif, contact) = ?", [$cleanPhone])
                            ->orWhereRaw("CONCAT(REPLACE(indicatif,'+',''), contact) = ?", [$cleanPhone])
                            ->first();
            }

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Identifiant ou mot de passe incorrect.'], 401);
            }

            // =================================================================
            // LOGIQUE DE RÉACTIVATION / SUPPRESSION
            // =================================================================
            if ($user->deactivated_at) {
            $dateDesactivation = Carbon::parse($user->deactivated_at);
            // On ajoute 30 jours à la date de désactivation
            $dateLimite = $dateDesactivation->copy()->addDays(30);

            if (now()->greaterThan($dateLimite)) {
                // CAS 1 : Plus de 30 jours passés.
                // ON NE SUPPRIME PAS LE COMPTE (pour l'historique),
                // MAIS ON INTERDIT L'ACCÈS.
                return response()->json([
                    'success' => false,
                    'message' => 'Ce compte a été désactivé définitivement. Veuillez contacter le support pour plus d\'informations.'
                ], 403); // 403 Forbidden
            } else {
                // CAS 2 : Moins de 30 jours.
                // L'utilisateur est revenu à temps : on RÉACTIVE le compte.
                $user->update(['deactivated_at' => null]);
                    
                    // Optionnel : Tu peux ajouter un message spécial dans la réponse pour dire "Bon retour !"
                }
            }
            // =================================================================

            // Mise à jour du push token
            if ($request->filled('push_notification')) {
                if ($user->push_notification !== $request->push_notification) {
                    $user->push_notification = $request->push_notification;
                    $user->save();
                }
            }

            $token = $user->createToken('user-api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => $user->wasChanged('deactivated_at') ? 'Compte réactivé avec succès. Bienvenue !' : 'Connexion réussie.',
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
                        'push_notification' => $user->push_notification, 
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error during API login: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur.', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
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
   public function togglePushnotification(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->push_notification = $request->push_notification;
            $user->save();
            return response()->json([
                'success' => true,
                'message' => 'Notification push activée/désactivée avec succès.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error during API toggle push notification: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'activation/d\'activation de la notification push.'
            ], 500);
        }
    }
     /**
     * Permet à l'utilisateur de désactiver son compte.
     */
    public function deactivateAccount(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // On définit la date de désactivation à maintenant
            $user->deactivated_at = now();
            
            // On peut aussi supprimer le token push pour ne plus qu'il reçoive de notifs
            $user->push_notification = null;
            
            $user->save();

            // On déconnecte l'utilisateur (supprime les tokens)
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Votre compte a été désactivé. Vous avez 30 jours pour vous reconnecter et l\'annuler, sinon il sera supprimé définitivement.'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deactivation: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la désactivation.'], 500);
        }
    }
}