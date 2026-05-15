<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSetting;
use App\Traits\HandlesFreeRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    use HandlesFreeRequests;
    /**
     * Gère la connexion et l'inscription via Google.
     *
     * Flux NOUVEAU COMPTE :
     *   → Les données sont stockées en Cache (pas en base).
     *   → Un pending_token (UUID) est retourné.
     *   → Le compte est créé uniquement lors de /finalize-profile/google.
     *
     * Flux COMPTE EXISTANT :
     *   → Connexion classique avec Sanctum token.
     */
    public function handleGoogleAuth(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_token'          => 'required_without:google_id|string',
            'email'             => 'required_without:id_token|email',
            'google_id'         => 'required_without:id_token|string',
            'name'              => 'nullable|string|max:255',
            'prenom'            => 'nullable|string|max:255',
            'profile_picture'   => 'nullable|string',
            'action'            => 'nullable|string|in:login,register',
            'push_notification' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $email               = $request->email;
            $googleId            = $request->google_id;
            $nameStr             = $request->name;
            $prenomStr           = $request->prenom;
            $pictureUrl          = $request->profile_picture;
            $pushNotificationStr = $request->push_notification;

            // 1. Vérification sécurisée du jeton Firebase
            if ($request->filled('id_token')) {
                $verifier = \Kreait\Firebase\JWT\IdTokenVerifier::createWithProjectId('plateau-apps-user');
                try {
                    $token   = $verifier->verifyIdToken($request->id_token);
                    $payload = $token->payload();

                    $googleId   = $payload['sub'];
                    $email      = $payload['email'] ?? $email;
                    $pictureUrl = $payload['picture'] ?? $pictureUrl;

                    if (isset($payload['name'])) {
                        $parts     = explode(' ', $payload['name'], 2);
                        $prenomStr = $parts[0];
                        $nameStr   = $parts[1] ?? $parts[0];
                    }
                } catch (\Kreait\Firebase\JWT\Error\IdTokenVerificationFailed $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jeton d\'authentification Firebase invalide ou expiré.',
                        'error'   => config('app.debug') ? $e->getMessage() : null
                    ], 401);
                }
            }

            // 2. Chercher si le compte existe déjà
            $user = User::where('google_id', $googleId)
                        ->orWhere(function ($q) use ($email) {
                            if ($email) $q->where('email', $email);
                        })
                        ->first();

            $action = $request->input('action');

            // ── COMPTE EXISTANT ──────────────────────────────────────────────
            if ($user) {
                if ($action === 'register') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ce compte existe déjà. Veuillez plutôt vous connecter.'
                    ], 409);
                }

                // Associer google_id si manquant
                if (empty($user->google_id)) {
                    $user->google_id = $googleId;
                    $user->save();
                }

                // Mettre à jour la photo si absente
                if (!empty($pictureUrl) && empty($user->profile_picture)) {
                    $user->profile_picture = $pictureUrl;
                    $user->save();
                }

                // Mettre à jour le push token
                if (!empty($pushNotificationStr) && $user->push_notification !== $pushNotificationStr) {
                    $user->push_notification = $pushNotificationStr;
                    $user->save();
                }

                if ($user->deactivated_at) {
                    $dateDesactivation = \Carbon\Carbon::parse($user->deactivated_at);
                    if (now()->greaterThan($dateDesactivation->copy()->addDays(30))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Ce compte a été désactivé définitivement.'
                        ], 403);
                    }
                    $user->update(['deactivated_at' => null]);
                }

                $token = $user->createToken('user-api-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie.',
                    'data'    => [
                        'is_new_user' => false,
                        'user'        => $this->formatUser($user),
                        'token'       => $token,
                        'token_type'  => 'Bearer',
                    ]
                ], 200);
            }

            // ── NOUVEAU COMPTE : stocker en Cache, PAS en base ───────────────
            if ($action === 'login') {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun compte trouvé. Veuillez vous inscrire.'
                ], 404);
            }

            $pendingToken = Str::uuid()->toString();

            $pendingData = [
                'name'              => $nameStr,
                'prenom'            => $prenomStr,
                'email'             => $email,
                'google_id'         => $googleId,
                'profile_picture'   => $pictureUrl,
                'push_notification' => $pushNotificationStr,
            ];

            Cache::put('pending_google_' . $pendingToken, $pendingData, now()->addHours(1));

            // Double indexation : permet de retrouver le pending via email ou google_id
            // si le mobile oublie d'envoyer le pending_token lors de la finalisation.
            if ($email) {
                Cache::put('pending_google_by_email_' . md5($email), $pendingToken, now()->addHours(1));
            }
            Cache::put('pending_google_by_googleid_' . md5($googleId), $pendingToken, now()->addHours(1));

            Log::info('Google Auth - Nouveau compte en attente de finalisation', [
                'email'     => $email,
                'google_id' => $googleId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Veuillez finaliser votre inscription.',
                'data'    => [
                    'is_new_user'   => true,
                    'pending_token' => $pendingToken,
                    'user'          => [
                        'name'            => $nameStr,
                        'prenom'          => $prenomStr,
                        'email'           => $email,
                        'profile_picture' => $pictureUrl,
                    ],
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur Google Auth : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de l\'authentification Google.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    private function formatUser(User $user): array
    {
        $pic = $user->profile_picture;
        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining  = $this->getRemainingFreeRequests($user);

        return [
            'id'                        => $user->id,
            'name'                      => $user->name,
            'prenom'                    => $user->prenom,
            'email'                     => $user->email,
            'commune'                   => $user->commune,
            'contact'                   => $user->contact,
            'profile_picture'           => $pic
                ? (Str::startsWith($pic, ['http://', 'https://']) ? $pic : Storage::url($pic))
                : null,
            'diaspora'                  => $user->diaspora,
            'free_requests_mode_active' => $freeRequestsModeActive,
            'free_requests_used'        => (int) $user->free_requests_used,
            'free_requests_remaining'   => $freeRequestsRemaining,
            'push_notification'         => $user->push_notification,
        ];
    }
}
