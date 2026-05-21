<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\YellikaSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;

class UserAuthFlowController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GOOGLE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Gère la connexion/inscription via Google (Firebase ID Token).
     *
     * Flux NOUVEAU COMPTE :
     *   → Les données sont stockées en session (pas en base).
     *   → Redirection vers la page de finalisation.
     *   → Le compte est créé uniquement dans submitFinalizeProfile().
     *
     * Flux COMPTE EXISTANT :
     *   → Connexion classique (Auth::login).
     */
    public function handleGoogleAuth(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $verifier = IdTokenVerifier::createWithProjectId(
                config('firebase.projects.app.project_id', 'plateau-apps-user')
            );
            $token   = $verifier->verifyIdToken($request->id_token);
            $payload = $token->payload();

            $googleId   = $payload['sub'];
            $email      = $payload['email'] ?? null;
            $nameStr    = $payload['name'] ?? 'Utilisateur';
            $pictureUrl = $payload['picture'] ?? null;

            // Chercher un compte existant
            $user = User::where('google_id', $googleId)
                        ->orWhere(function ($q) use ($email) {
                            if ($email) $q->where('email', $email);
                        })
                        ->first();

            // ── COMPTE EXISTANT ──────────────────────────────────────────────
            if ($user) {
                if (empty($user->google_id)) {
                    $user->google_id = $googleId;
                    $user->save();
                }

                Auth::login($user);

                // Profil incomplet → finalisation
                if (empty($user->contact) || empty($user->prenom)) {
                    return response()->json([
                        'success'  => true,
                        'redirect' => route('user.auth.profile.complete'),
                        'message'  => 'Veuillez finaliser votre profil.'
                    ]);
                }

                return response()->json([
                    'success'  => true,
                    'redirect' => route('user.dashboard'),
                    'message'  => 'Connexion réussie.'
                ]);
            }

            // ── NOUVEAU COMPTE : stocker en session, PAS en base ─────────────
            $parts  = explode(' ', $nameStr, 2);
            $prenom = $parts[0];
            $nom    = $parts[1] ?? $parts[0];

            session([
                'pending_google_auth' => [
                    'name'            => $nom,
                    'prenom'          => $prenom,
                    'email'           => $email,
                    'google_id'       => $googleId,
                    'profile_picture' => $pictureUrl,
                ],
                'pending_apple_auth' => null,
            ]);

            Log::info('Google Auth Web - Nouveau compte en attente de finalisation', [
                'email'     => $email,
                'google_id' => $googleId,
            ]);

            return response()->json([
                'success'  => true,
                'redirect' => route('user.auth.profile.complete'),
                'message'  => 'Compte Google détecté. Veuillez finaliser votre profil.'
            ]);

        } catch (IdTokenVerificationFailed $e) {
            Log::warning('Google Auth Web - Jeton invalide: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Jeton Google invalide ou expiré.'], 401);
        } catch (\Kreait\Firebase\Exception\InvalidArgumentException $e) {
            Log::error('Google Auth Web - Config Firebase: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Configuration Firebase incorrecte.'], 500);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('Google Auth Web - Réseau: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Impossible de vérifier votre identité Google. Problème réseau.'], 503);
        } catch (\Exception $e) {
            Log::error('Google Auth Web Error [' . get_class($e) . ']: ' . $e->getMessage(), [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de l\'authentification Google.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // APPLE
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Gère la connexion/inscription via Apple (Firebase ID Token).
     * Même logique que Google : pas de création en base pour les nouveaux comptes.
     */
    public function handleAppleAuth(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $verifier = IdTokenVerifier::createWithProjectId(
                config('firebase.projects.app.project_id', 'plateau-apps-user')
            );
            $token   = $verifier->verifyIdToken($request->id_token);
            $payload = $token->payload();

            $appleId = $payload['firebase']['identities']['apple.com'][0] ?? null;
            $email   = $payload['email'] ?? null;

            if (!$appleId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Identifiant Apple introuvable dans le jeton.'
                ], 401);
            }

            $user = User::where('apple_id', $appleId)
                        ->orWhere(function ($q) use ($email) {
                            if ($email) $q->where('email', $email);
                        })
                        ->first();

            // ── COMPTE EXISTANT ──────────────────────────────────────────────
            if ($user) {
                if (empty($user->apple_id)) {
                    $user->apple_id = $appleId;
                    $user->save();
                }

                Auth::login($user);

                if (empty($user->contact) || empty($user->prenom)) {
                    return response()->json([
                        'success'  => true,
                        'redirect' => route('user.auth.profile.complete'),
                        'message'  => 'Veuillez finaliser votre profil.'
                    ]);
                }

                return response()->json([
                    'success'  => true,
                    'redirect' => route('user.dashboard'),
                    'message'  => 'Connexion Apple réussie.'
                ]);
            }

            // ── NOUVEAU COMPTE : stocker en session, PAS en base ─────────────
            session([
                'pending_apple_auth' => [
                    'email'    => $email,
                    'apple_id' => $appleId,
                ],
                'pending_google_auth' => null,
            ]);

            Log::info('Apple Auth Web - Nouveau compte en attente de finalisation', [
                'email'    => $email,
                'apple_id' => $appleId,
            ]);

            return response()->json([
                'success'  => true,
                'redirect' => route('user.auth.profile.complete'),
                'message'  => 'Compte Apple détecté. Veuillez finaliser votre profil.'
            ]);

        } catch (IdTokenVerificationFailed $e) {
            Log::warning('Apple Auth Web - Jeton invalide: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Jeton Apple invalide ou expiré.'], 401);
        } catch (\Exception $e) {
            Log::error('Apple Auth Web Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de l\'authentification Apple.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // OTP PHONE (Web)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Envoie un code OTP par SMS.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'indicatif' => 'required|string',
            'contact'   => 'required|string',
        ]);

        try {
            $phone = preg_replace('/[^0-9]/', '', $request->indicatif . $request->contact);

            $existingUser = User::where('contact', $request->contact)->first();
            if ($existingUser && $existingUser->password !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce numéro de téléphone est déjà associé à un compte existant.',
                ], 403);
            }

            $otp = rand(100000, 999999);
            Cache::put('otp_' . $phone, $otp, now()->addMinutes(10));

            $smsService = app(YellikaSmsService::class);
            $result     = $smsService->sendSms($phone, "Votre code de vérification Plateau App est : " . $otp);

            if ($result && isset($result['success']) && $result['success']) {
                return response()->json(['success' => true, 'message' => 'Code envoyé.']);
            }

            return response()->json(['success' => false, 'message' => 'Erreur d\'envoi SMS.'], 500);

        } catch (\Exception $e) {
            Log::error('Send OTP Web Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    /**
     * Vérifie l'OTP.
     * - Compte finalisé existant → connexion directe.
     * - Nouveau numéro → stocke en session (PAS de User::create), redirige vers finalisation.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'indicatif' => 'required|string',
            'contact'   => 'required|string',
            'otp'       => 'required|string',
        ]);

        $phone     = preg_replace('/[^0-9]/', '', $request->indicatif . $request->contact);
        $cachedOtp = Cache::get('otp_' . $phone);

        if (!$cachedOtp || (string) $cachedOtp !== (string) $request->otp) {
            return response()->json(['success' => false, 'message' => 'Code invalide ou expiré.'], 400);
        }

        Cache::forget('otp_' . $phone);

        try {
            // Compte déjà finalisé ?
            $user = User::where('contact', $request->contact)
                        ->where('indicatif', $request->indicatif)
                        ->whereNotNull('name')
                        ->whereNotNull('password')
                        ->first();

            if ($user) {
                Auth::login($user);
                return response()->json([
                    'success'  => true,
                    'redirect' => route('user.dashboard'),
                    'message'  => 'Connexion réussie.'
                ]);
            }

            // Nouveau numéro → stocker en session, PAS en base
            session([
                'pending_phone_auth'  => [
                    'indicatif'         => $request->indicatif,
                    'contact'           => $request->contact,
                    'phone_verified_at' => now()->toDateTimeString(),
                    'diaspora'          => $request->input('diaspora') ? true : false,
                    'pays_residence'    => $request->input('pays_residence'),
                ],
                'pending_google_auth' => null,
                'pending_apple_auth'  => null,
            ]);

            Log::info('OTP Web vérifié - Nouveau compte en attente de finalisation', [
                'indicatif' => $request->indicatif,
                'contact'   => $request->contact,
            ]);

            return response()->json([
                'success'  => true,
                'redirect' => route('user.auth.profile.complete'),
                'message'  => 'Téléphone vérifié. Veuillez finaliser votre profil.'
            ]);

        } catch (\Exception $e) {
            Log::error('Verify OTP Web Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // FINALISATION DE PROFIL
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Affiche la page de finalisation du profil.
     * Accessible sans auth pour les nouveaux comptes (Google, Apple, Téléphone) via session.
     */
    public function showFinalizeProfile()
    {
        $pendingGoogle = session('pending_google_auth');
        $pendingApple  = session('pending_apple_auth');
        $pendingPhone  = session('pending_phone_auth');
        $user          = Auth::user();

        // Aucune donnée en session ET pas d'utilisateur → login
        if (!$pendingGoogle && !$pendingApple && !$pendingPhone && !$user) {
            return redirect()->route('login');
        }

        // Utilisateur connecté avec profil complet → dashboard
        if ($user && !$pendingGoogle && !$pendingApple && !$pendingPhone) {
            $isSocial   = !empty($user->google_id) || !empty($user->apple_id);
            $isComplete = !empty($user->name) && !empty($user->prenom);
            if (!$isSocial) {
                $isComplete = $isComplete && !empty($user->contact) && $user->password !== null;
            }
            if ($isComplete) {
                return redirect()->route('user.dashboard');
            }
        }

        // Données à pré-remplir
        $pendingData = $pendingGoogle ?? $pendingApple ?? [];
        $isSocial    = !empty($pendingGoogle) || !empty($pendingApple)
                       || ($user && (!empty($user->google_id) || !empty($user->apple_id)));
        $isPhoneNew  = !empty($pendingPhone);

        return view('user.auth.finalize-profile', compact('user', 'pendingData', 'isSocial', 'isPhoneNew', 'pendingPhone'));
    }

    /**
     * Enregistre les informations de finalisation et crée le compte si nécessaire.
     */
    public function submitFinalizeProfile(Request $request)
    {
        $pendingGoogle = session('pending_google_auth');
        $pendingApple  = session('pending_apple_auth');
        $pendingPhone  = session('pending_phone_auth');
        $user          = Auth::user();

        $isNewSocialUser = !empty($pendingGoogle) || !empty($pendingApple);
        $isNewPhoneUser  = !empty($pendingPhone);
        $isSocial        = $isNewSocialUser
                           || ($user && (!empty($user->google_id) || !empty($user->apple_id)));

        // ── Règles de validation ─────────────────────────────────────────────
        $rules = [
            'name'              => 'required|string|max:255',
            'prenom'            => 'required|string|max:255',
            'NNI'               => 'nullable|string|max:50',
            'diaspora'          => 'nullable|boolean',
            'pays_residence'    => 'required_if:diaspora,1|nullable|string|max:255',
            'ville_residence'   => 'nullable|string|max:255',
            'adresse_etrangere' => 'nullable|string|max:500',
        ];

        if (!$isSocial) {
            // Inscription téléphone ou utilisateur normal existant
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['email']    = 'nullable|email|unique:users,email' . ($user ? ',' . $user->id : '');
        }

        if ($isNewSocialUser) {
            // Nouveau compte Google/Apple : numéro obligatoire
            $rules['indicatif'] = 'required|string|max:10';
            $rules['contact']   = 'required|string|max:20|unique:users,contact';
        }

        $request->validate($rules, [
            'contact.unique' => 'Ce numéro est déjà associé à un autre compte.',
        ]);

        try {
            // ── CAS 1 : Nouveau compte téléphone ─────────────────────────────
            if ($isNewPhoneUser) {
                $user = User::create([
                    'name'               => $request->name,
                    'prenom'             => $request->prenom,
                    'email'              => $request->email,
                    'indicatif'          => $pendingPhone['indicatif'],
                    'contact'            => $pendingPhone['contact'],
                    'commune'            => 'plateau',
                    'password'           => Hash::make($request->password),
                    'NNI'                => $request->NNI,
                    'diaspora'           => $request->has('diaspora'),
                    'pays_residence'     => $request->pays_residence,
                    'ville_residence'    => $request->ville_residence,
                    'adresse_etrangere'  => $request->adresse_etrangere,
                    'phone_verified_at'  => $pendingPhone['phone_verified_at'],
                ]);

                session()->forget('pending_phone_auth');
                Auth::login($user);

                Log::info('Phone Auth Web - Compte créé et finalisé', [
                    'user_id'  => $user->id,
                    'contact'  => $user->contact,
                ]);

                return redirect()->route('user.dashboard')
                                 ->with('success', 'Inscription finalisée avec succès !');
            }

            // ── CAS 2 : Nouveau compte Google ────────────────────────────────
            if ($pendingGoogle) {
                $user = User::create([
                    'name'               => $request->name,
                    'prenom'             => $request->prenom,
                    'email'              => $pendingGoogle['email'],
                    'google_id'          => $pendingGoogle['google_id'],
                    'profile_picture'    => $pendingGoogle['profile_picture'] ?? null,
                    'commune'            => 'plateau',
                    'password'           => Hash::make(Str::random(24)),
                    'indicatif'          => $request->indicatif,
                    'contact'            => $request->contact,
                    'NNI'                => $request->NNI,
                    'diaspora'           => $request->has('diaspora'),
                    'pays_residence'     => $request->pays_residence,
                    'ville_residence'    => $request->ville_residence,
                    'adresse_etrangere'  => $request->adresse_etrangere,
                ]);

                session()->forget('pending_google_auth');
                Auth::login($user);

                Log::info('Google Auth Web - Compte créé et finalisé', [
                    'user_id' => $user->id,
                    'email'   => $user->email,
                ]);

                return redirect()->route('user.dashboard')
                                 ->with('success', 'Inscription Google finalisée avec succès !');
            }

            // ── CAS 3 : Nouveau compte Apple ─────────────────────────────────
            if ($pendingApple) {
                $user = User::create([
                    'name'               => $request->name,
                    'prenom'             => $request->prenom,
                    'email'              => $pendingApple['email'],
                    'apple_id'           => $pendingApple['apple_id'],
                    'commune'            => 'plateau',
                    'password'           => Hash::make(Str::random(24)),
                    'indicatif'          => $request->indicatif,
                    'contact'            => $request->contact,
                    'NNI'                => $request->NNI,
                    'diaspora'           => $request->has('diaspora'),
                    'pays_residence'     => $request->pays_residence,
                    'ville_residence'    => $request->ville_residence,
                    'adresse_etrangere'  => $request->adresse_etrangere,
                ]);

                session()->forget('pending_apple_auth');
                Auth::login($user);

                Log::info('Apple Auth Web - Compte créé et finalisé', [
                    'user_id'  => $user->id,
                    'apple_id' => $user->apple_id,
                ]);

                return redirect()->route('user.dashboard')
                                 ->with('success', 'Inscription Apple finalisée avec succès !');
            }

            // ── CAS 4 : Utilisateur déjà en base (social existant) ───────────
            $data = [
                'name'               => $request->name,
                'prenom'             => $request->prenom,
                'NNI'                => $request->NNI,
                'diaspora'           => $request->has('diaspora'),
                'pays_residence'     => $request->pays_residence,
                'ville_residence'    => $request->ville_residence,
                'adresse_etrangere'  => $request->adresse_etrangere,
            ];

            if (!$isSocial && $request->password) {
                $data['password'] = Hash::make($request->password);
            }
            if (!$isSocial) {
                $data['email'] = $request->email;
            }

            $user->update($data);

            return redirect()->route('user.dashboard')
                             ->with('success', 'Profil finalisé avec succès !');

        } catch (\Exception $e) {
            Log::error('Finalize Profile Error: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la finalisation.')->withInput();
        }
    }
}
