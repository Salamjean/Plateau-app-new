<?php

namespace App\Http\Controllers\Api\Authenticate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileCompletionController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GOOGLE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Finalise l'inscription/profil d'un utilisateur Google.
     * Endpoint PUBLIC — deux modes de fonctionnement :
     *
     *   MODE 1 — Nouveau compte (pending_token fourni) :
     *     Lit le Cache → crée le compte → retourne un token Sanctum.
     *
     *   MODE 2 — Compte existant avec profil incomplet (Bearer token fourni, pas de pending_token) :
     *     Authentifie via Sanctum → met à jour le profil → retourne un token Sanctum.
     *
     * POST /api/utilisateurs/finalize-profile/google
     */
    public function finalizeProfileGoogle(Request $request): JsonResponse
    {
        // ────────── DEBUG TEMPORAIRE — À RETIRER APRÈS FIX ──────────
        Log::info('Finalize Google - Payload reçu du mobile', [
            'all_keys'              => array_keys($request->all()),
            'content_type'          => $request->header('Content-Type'),
            'has_authorization'     => $request->hasHeader('Authorization'),
            'has_pending_token'     => $request->filled('pending_token'),
            'has_pendingToken'      => $request->filled('pendingToken'),
            'pending_token_value'   => $request->input('pending_token'),
            'pendingToken_value'    => $request->input('pendingToken'),
            'email'                 => $request->input('email'),
            'google_id'             => $request->input('google_id'),
        ]);
        // ────────────────────────────────────────────────────────────

        // Accepter les deux conventions (snake_case et camelCase) côté backend
        if (!$request->filled('pending_token') && $request->filled('pendingToken')) {
            $request->merge(['pending_token' => $request->input('pendingToken')]);
        }

        // Fallback : si le mobile n'envoie pas pending_token,
        // on essaie de retrouver la session pending via email ou google_id.
        if (!$request->filled('pending_token')) {
            $email    = $request->input('email');
            $googleId = $request->input('google_id');

            if ($email) {
                $foundToken = Cache::get('pending_google_by_email_' . md5($email));
                if ($foundToken) {
                    $request->merge(['pending_token' => $foundToken]);
                    Log::info('Finalize Google - pending_token retrouvé via email', ['email' => $email]);
                }
            }

            if (!$request->filled('pending_token') && $googleId) {
                $foundToken = Cache::get('pending_google_by_googleid_' . md5($googleId));
                if ($foundToken) {
                    $request->merge(['pending_token' => $foundToken]);
                    Log::info('Finalize Google - pending_token retrouvé via google_id', ['google_id' => $googleId]);
                }
            }
        }

        $hasPendingToken = $request->filled('pending_token');

        // Résoudre l'utilisateur selon le mode
        $authUser    = null;
        $pendingData = null;

        if ($hasPendingToken) {
            // MODE 1 : nouveau compte
            $pendingData = Cache::get('pending_google_' . $request->pending_token);
            if (!$pendingData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expirée ou invalide. Veuillez recommencer la connexion Google.'
                ], 400);
            }
        } else {
            // MODE 2 : utilisateur existant (Bearer token)
            $authUser = auth('sanctum')->user();
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé. Fournissez un pending_token ou un Bearer token valide.'
                ], 401);
            }
        }

        // Règles de validation communes
        $contactUniqueRule = $hasPendingToken
            ? 'required|string|max:20|unique:users,contact'
            : 'required|string|max:20|unique:users,contact,' . ($authUser?->id ?? 0);

        $validator = Validator::make($request->all(), [
            'pending_token'     => 'nullable|string',
            'password'          => 'nullable|string|min:8|confirmed', // optionnel pour MODE 2
            'indicatif'         => 'required|string|max:10',
            'contact'           => $contactUniqueRule,
            'NNI'               => 'nullable|string|max:50',
            'diaspora'          => 'nullable|boolean',
            'pays_residence'    => 'nullable|string|max:255',
            'push_notification' => 'nullable|string|max:255',
        ], [
            'contact.unique' => 'Ce numéro est déjà associé à un autre compte.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // ── MODE 1 : Nouveau compte depuis le Cache ───────────────────────
            if ($hasPendingToken) {
                // Vérifier si le compte a déjà été créé (double appel)
                $existing = User::where('google_id', $pendingData['google_id'])
                    ->orWhere(function ($q) use ($pendingData) {
                        if (!empty($pendingData['email'])) {
                            $q->where('email', $pendingData['email']);
                        }
                    })
                    ->first();

                if ($existing) {
                    $this->forgetGooglePendingCache($request->pending_token, $pendingData);
                    $token = $existing->createToken('user-api-token')->plainTextToken;
                    return response()->json([
                        'success' => true,
                        'message' => 'Compte déjà existant — connexion effectuée.',
                        'data'    => [
                            'token'      => $token,
                            'token_type' => 'Bearer',
                            'user'       => $this->formatUser($existing),
                        ]
                    ], 200);
                }

                $user = User::create([
                    'name'              => $pendingData['name'],
                    'prenom'            => $pendingData['prenom'],
                    'email'             => $pendingData['email'],
                    'google_id'         => $pendingData['google_id'],
                    'profile_picture'   => $pendingData['profile_picture'] ?? null,
                    'commune'           => 'plateau',
                    'password'          => Hash::make($request->password ?? Str::random(24)),
                    'indicatif'         => $request->indicatif,
                    'contact'           => $request->contact,
                    'NNI'               => $request->NNI,
                    'diaspora'          => $request->boolean('diaspora'),
                    'pays_residence'    => $request->pays_residence,
                    'push_notification' => $request->push_notification ?? $pendingData['push_notification'] ?? null,
                ]);

                $this->forgetGooglePendingCache($request->pending_token, $pendingData);

                Log::info('Google Auth - Nouveau compte créé et finalisé', [
                    'user_id' => $user->id,
                    'email'   => $user->email,
                ]);

                $token = $user->createToken('user-api-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Inscription Google finalisée avec succès.',
                    'data'    => [
                        'token'      => $token,
                        'token_type' => 'Bearer',
                        'user'       => $this->formatUser($user),
                    ]
                ], 201);
            }

            // ── MODE 2 : Mise à jour d'un compte existant ────────────────────
            $updateData = [
                'indicatif'      => $request->indicatif,
                'contact'        => $request->contact,
                'diaspora'       => $request->boolean('diaspora'),
                'pays_residence' => $request->pays_residence,
            ];

            if ($request->filled('NNI'))               $updateData['NNI']               = $request->NNI;
            if ($request->filled('push_notification')) $updateData['push_notification'] = $request->push_notification;
            if ($request->filled('password'))          $updateData['password']          = Hash::make($request->password);

            $authUser->update($updateData);

            Log::info('Google Auth - Profil complété (compte existant)', [
                'user_id' => $authUser->id,
            ]);

            $token = $authUser->createToken('user-api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Profil Google complété avec succès.',
                'data'    => [
                    'token'      => $token,
                    'token_type' => 'Bearer',
                    'user'       => $this->formatUser($authUser),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur finalizeProfileGoogle: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la finalisation.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // APPLE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Finalise l'inscription/profil d'un utilisateur Apple.
     * Endpoint PUBLIC — deux modes de fonctionnement :
     *
     *   MODE 1 — Nouveau compte (pending_token fourni) :
     *     Lit le Cache → crée le compte → retourne un token Sanctum.
     *
     *   MODE 2 — Compte existant avec profil incomplet (Bearer token fourni, pas de pending_token) :
     *     Authentifie via Sanctum → met à jour le profil → retourne un token Sanctum.
     *
     * POST /api/utilisateurs/finalize-profile/apple
     */
    public function finalizeProfileApple(Request $request): JsonResponse
    {
        // ────────── DEBUG TEMPORAIRE — À RETIRER APRÈS FIX ──────────
        Log::info('Finalize Apple - Payload reçu du mobile', [
            'all_keys'                => array_keys($request->all()),
            'content_type'            => $request->header('Content-Type'),
            'has_authorization'       => $request->hasHeader('Authorization'),
            'authorization_preview'   => $request->header('Authorization')
                ? substr($request->header('Authorization'), 0, 30) . '...'
                : null,
            'has_pending_token'       => $request->filled('pending_token'),
            'has_pendingToken'        => $request->filled('pendingToken'),
            'pending_token_value'     => $request->input('pending_token'),
            'pendingToken_value'      => $request->input('pendingToken'),
            'name'                    => $request->input('name'),
            'prenom'                  => $request->input('prenom'),
        ]);
        // ────────────────────────────────────────────────────────────

        // Accepter les deux conventions (snake_case et camelCase) côté backend
        if (!$request->filled('pending_token') && $request->filled('pendingToken')) {
            $request->merge(['pending_token' => $request->input('pendingToken')]);
        }

        // Fallback : si le mobile n'envoie pas pending_token,
        // on essaie de retrouver la session pending via email ou apple_id.
        if (!$request->filled('pending_token')) {
            $email   = $request->input('email');
            $appleId = $request->input('apple_id');

            if ($email) {
                $foundToken = Cache::get('pending_apple_by_email_' . md5($email));
                if ($foundToken) {
                    $request->merge(['pending_token' => $foundToken]);
                    Log::info('Finalize Apple - pending_token retrouvé via email', ['email' => $email]);
                }
            }

            if (!$request->filled('pending_token') && $appleId) {
                $foundToken = Cache::get('pending_apple_by_appleid_' . md5($appleId));
                if ($foundToken) {
                    $request->merge(['pending_token' => $foundToken]);
                    Log::info('Finalize Apple - pending_token retrouvé via apple_id', ['apple_id' => $appleId]);
                }
            }
        }

        $hasPendingToken = $request->filled('pending_token');

        $authUser    = null;
        $pendingData = null;

        if ($hasPendingToken) {
            $pendingData = Cache::get('pending_apple_' . $request->pending_token);
            if (!$pendingData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expirée ou invalide. Veuillez recommencer la connexion Apple.'
                ], 400);
            }
        } else {
            $authUser = auth('sanctum')->user();
            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé. Fournissez un pending_token ou un Bearer token valide.'
                ], 401);
            }
        }

        $contactUniqueRule = $hasPendingToken
            ? 'nullable|string|max:20|unique:users,contact'
            : 'nullable|string|max:20|unique:users,contact,' . ($authUser?->id ?? 0);

        $validator = Validator::make($request->all(), [
            'pending_token'     => 'nullable|string',
            'name'              => 'required|string|max:255',
            'prenom'            => 'required|string|max:255',
            'indicatif'         => 'nullable|string|max:10',
            'contact'           => $contactUniqueRule,
            'NNI'               => 'nullable|string|max:50',
            'diaspora'          => 'nullable|boolean',
            'pays_residence'    => 'nullable|string|max:255',
            'push_notification' => 'nullable|string|max:255',
        ], [
            'contact.unique' => 'Ce numéro est déjà associé à un autre compte.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // ── MODE 1 : Nouveau compte depuis le Cache ───────────────────────
            if ($hasPendingToken) {
                $existing = User::where('apple_id', $pendingData['apple_id'])
                    ->orWhere(function ($q) use ($pendingData) {
                        if (!empty($pendingData['email'])) {
                            $q->where('email', $pendingData['email']);
                        }
                    })
                    ->first();

                if ($existing) {
                    $this->forgetApplePendingCache($request->pending_token, $pendingData);
                    $token = $existing->createToken('user-api-token')->plainTextToken;
                    return response()->json([
                        'success' => true,
                        'message' => 'Compte déjà existant — connexion effectuée.',
                        'data'    => [
                            'token'      => $token,
                            'token_type' => 'Bearer',
                            'user'       => $this->formatUser($existing),
                        ]
                    ], 200);
                }

                $user = User::create([
                    'name'              => $request->name,
                    'prenom'            => $request->prenom,
                    'email'             => $pendingData['email'],
                    'apple_id'          => $pendingData['apple_id'],
                    'commune'           => 'plateau',
                    'password'          => Hash::make(Str::random(24)),
                    'indicatif'         => $request->indicatif,
                    'contact'           => $request->contact,
                    'NNI'               => $request->NNI,
                    'diaspora'          => $request->boolean('diaspora'),
                    'pays_residence'    => $request->pays_residence,
                    'push_notification' => $request->push_notification ?? $pendingData['push_notification'] ?? null,
                ]);

                $this->forgetApplePendingCache($request->pending_token, $pendingData);

                Log::info('Apple Auth - Nouveau compte créé et finalisé', [
                    'user_id'  => $user->id,
                    'apple_id' => $user->apple_id,
                ]);

                $token = $user->createToken('user-api-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Inscription Apple finalisée avec succès.',
                    'data'    => [
                        'token'      => $token,
                        'token_type' => 'Bearer',
                        'user'       => $this->formatUser($user),
                    ]
                ], 201);
            }

            // ── MODE 2 : Mise à jour d'un compte existant ────────────────────
            $updateData = [
                'name'   => $request->name,
                'prenom' => $request->prenom,
            ];

            if ($request->filled('indicatif'))         $updateData['indicatif']         = $request->indicatif;
            if ($request->filled('contact'))           $updateData['contact']           = $request->contact;
            if ($request->filled('NNI'))               $updateData['NNI']               = $request->NNI;
            if ($request->has('diaspora'))             $updateData['diaspora']          = $request->boolean('diaspora');
            if ($request->filled('pays_residence'))    $updateData['pays_residence']    = $request->pays_residence;
            if ($request->filled('push_notification')) $updateData['push_notification'] = $request->push_notification;

            $authUser->update($updateData);

            Log::info('Apple Auth - Profil complété (compte existant)', [
                'user_id' => $authUser->id,
            ]);

            $token = $authUser->createToken('user-api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Profil Apple complété avec succès.',
                'data'    => [
                    'token'      => $token,
                    'token_type' => 'Bearer',
                    'user'       => $this->formatUser($authUser),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur finalizeProfileApple: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la finalisation.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Nettoie toutes les clés de cache liées à une session pending Apple
     * (clé principale + clés secondaires indexées par email/apple_id).
     */
    private function forgetApplePendingCache(?string $pendingToken, ?array $pendingData): void
    {
        if ($pendingToken) {
            Cache::forget('pending_apple_' . $pendingToken);
        }
        if (!empty($pendingData['email'])) {
            Cache::forget('pending_apple_by_email_' . md5($pendingData['email']));
        }
        if (!empty($pendingData['apple_id'])) {
            Cache::forget('pending_apple_by_appleid_' . md5($pendingData['apple_id']));
        }
    }

    /**
     * Nettoie toutes les clés de cache liées à une session pending Google
     * (clé principale + clés secondaires indexées par email/google_id).
     */
    private function forgetGooglePendingCache(?string $pendingToken, ?array $pendingData): void
    {
        if ($pendingToken) {
            Cache::forget('pending_google_' . $pendingToken);
        }
        if (!empty($pendingData['email'])) {
            Cache::forget('pending_google_by_email_' . md5($pendingData['email']));
        }
        if (!empty($pendingData['google_id'])) {
            Cache::forget('pending_google_by_googleid_' . md5($pendingData['google_id']));
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PHONE (OTP)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Finalise l'inscription/profil d'un utilisateur Téléphone (OTP).
     * Endpoint PUBLIC — deux modes de fonctionnement :
     *
     *   MODE 1 — Nouveau compte (pending_token fourni) :
     *     Lit le Cache → crée le compte → retourne un token Sanctum.
     *
     *   MODE 2 — Compte existant avec profil incomplet (Bearer token fourni, pas de pending_token) :
     *     Authentifie via Sanctum → met à jour le profil → retourne un token Sanctum.
     *
     * POST /api/utilisateurs/finalize-profile/phone
     */
    public function finalizeProfilePhone(Request $request): JsonResponse
    {
        // 1. Détermination du mode :
        //    - MODE 1 (nouveau compte) : si indicatif + contact fournis ET pas de Bearer valide
        //                                → on vérifie le cache OTP `otp_verified_{phone}`
        //    - MODE 2 (compte existant) : si Bearer token Sanctum valide → mode update
        //
        //    ⚠ Plus de pending_token nécessaire — on s'appuie uniquement sur le cache OTP.

        $authUser  = auth('sanctum')->user();
        $hasPhone  = $request->filled('indicatif') && $request->filled('contact');
        $isNewUser = !$authUser && $hasPhone;

        if (!$authUser && !$isNewUser) {
            return response()->json([
                'success' => false,
                'message' => 'Non autorisé. Fournissez indicatif + contact (nouveau compte) ou un Bearer token valide.'
            ], 401);
        }

        $otpVerifiedData = null;
        $phoneKey        = null;

        if ($isNewUser) {
            $indicatifClean = preg_replace('/[^0-9]/', '', $request->indicatif);
            $contactClean   = preg_replace('/[^0-9]/', '', $request->contact);
            $phoneKey       = $indicatifClean . $contactClean;
            $otpVerifiedData = Cache::get('otp_verified_' . $phoneKey);

            if (!$otpVerifiedData && !config('app.debug')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le numéro n\'a pas été vérifié par OTP. Veuillez recommencer l\'étape SMS.'
                ], 403);
            }
        }

        // 2. Validation des champs
        $emailUniqueRule = $isNewUser
            ? 'nullable|email|unique:users,email'
            : 'nullable|email|unique:users,email,' . ($authUser?->id ?? 0);

        $contactUniqueRule = $isNewUser
            ? 'required|string|max:20|unique:users,contact'
            : 'nullable|string|max:20';

        $validator = Validator::make($request->all(), [
            'name'              => 'required|string|max:255',
            'prenom'            => 'required|string|max:255',
            'indicatif'         => $isNewUser ? 'required|string|max:10' : 'nullable|string|max:10',
            'contact'           => $contactUniqueRule,
            'NNI'               => 'nullable|string|max:50',
            'email'             => $emailUniqueRule,
            'password'          => $isNewUser ? 'required|string|min:8|confirmed' : 'nullable|string|min:8|confirmed',
            'diaspora'          => 'nullable|boolean',
            'pays_residence'    => 'nullable|string|max:255',
            'push_notification' => 'nullable|string|max:255',
        ], [
            'email.unique'   => 'Cette adresse email est déjà utilisée par un autre compte.',
            'contact.unique' => 'Ce numéro est déjà associé à un compte. Veuillez vous connecter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // ── MODE 1 : Création du nouveau compte (OTP vérifié) ───────────────
            if ($isNewUser) {
                // Vérifier qu'aucun compte finalisé n'existe avec ce numéro
                $existing = User::where('contact', $request->contact)
                    ->where('indicatif', $request->indicatif)
                    ->whereNotNull('password')
                    ->first();

                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Un compte existe déjà avec ce numéro. Veuillez vous connecter.'
                    ], 409);
                }

                $user = User::create([
                    'name'              => $request->name,
                    'prenom'            => $request->prenom,
                    'email'             => $request->email,
                    'indicatif'         => $request->indicatif,
                    'contact'           => $request->contact,
                    'commune'           => 'plateau',
                    'password'          => Hash::make($request->password),
                    'NNI'               => $request->NNI,
                    'diaspora'          => $request->boolean('diaspora'),
                    'pays_residence'    => $request->pays_residence,
                    'phone_verified_at' => $otpVerifiedData['phone_verified_at'] ?? now(),
                    'push_notification' => $request->push_notification,
                ]);

                // Nettoyer le cache OTP — vérification consommée
                if ($phoneKey) {
                    Cache::forget('otp_verified_' . $phoneKey);
                }

                Log::info('Phone Auth - Nouveau compte créé et finalisé', [
                    'user_id'   => $user->id,
                    'indicatif' => $user->indicatif,
                    'contact'   => $user->contact,
                ]);

                $token = $user->createToken('user-api-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Inscription finalisée avec succès.',
                    'data'    => [
                        'token'      => $token,
                        'token_type' => 'Bearer',
                        'user'       => $this->formatUser($user),
                    ]
                ], 201);
            }

            // ── MODE 2 : Mise à jour d'un compte existant ────────────────────
            $updateData = [
                'name'   => $request->name,
                'prenom' => $request->prenom,
            ];

            // Le mot de passe est optionnel en MODE 2
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }
            if ($request->filled('indicatif'))         $updateData['indicatif']         = $request->indicatif;
            if ($request->filled('contact'))           $updateData['contact']           = $request->contact;
            if ($request->filled('email'))             $updateData['email']             = $request->email;
            if ($request->filled('NNI'))               $updateData['NNI']               = $request->NNI;
            if ($request->has('diaspora'))             $updateData['diaspora']          = $request->boolean('diaspora');
            if ($request->filled('pays_residence'))    $updateData['pays_residence']    = $request->pays_residence;
            if ($request->filled('push_notification')) $updateData['push_notification'] = $request->push_notification;

            $authUser->update($updateData);

            Log::info('Phone Auth - Profil complété (compte existant)', [
                'user_id' => $authUser->id,
            ]);

            $token = $authUser->createToken('user-api-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Profil finalisé avec succès.',
                'data'    => [
                    'token'      => $token,
                    'token_type' => 'Bearer',
                    'user'       => $this->formatUser($authUser),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur finalizeProfilePhone: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la finalisation.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function formatUser(User $user): array
    {
        $pic = $user->profile_picture;
        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'prenom'            => $user->prenom,
            'email'             => $user->email,
            'indicatif'         => $user->indicatif,
            'contact'           => $user->contact,
            'NNI'               => $user->NNI,
            'diaspora'          => (bool) $user->diaspora,
            'pays_residence'    => $user->pays_residence,
            'push_notification' => $user->push_notification,
            'profile_picture'   => $pic
                ? (Str::startsWith($pic, ['http://', 'https://']) ? $pic : Storage::url($pic))
                : null,
        ];
    }
}
