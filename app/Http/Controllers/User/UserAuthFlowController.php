<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\YellikaSmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kreait\Firebase\JWT\IdTokenVerifier;
use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;

class UserAuthFlowController extends Controller
{
    /**
     * Gère la connexion/inscription via Google (Firebase ID Token).
     */
    public function handleGoogleAuth(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $verifier = IdTokenVerifier::createWithProjectId(config('firebase.projects.app.project_id', 'plateau-apps-user'));
            $token = $verifier->verifyIdToken($request->id_token);
            $payload = $token->payload();

            $googleId = $payload['sub'];
            $email = $payload['email'] ?? null;
            $nameStr = $payload['name'] ?? 'Utilisateur';
            $pictureUrl = $payload['picture'] ?? null;

            // Chercher l'utilisateur
            $user = User::where('google_id', $googleId)
                        ->orWhere('email', $email)
                        ->first();

            if ($user) {
                // Mise à jour si nécessaire
                if (empty($user->google_id)) {
                    $user->google_id = $googleId;
                    $user->save();
                }

                Auth::login($user);
                
                // Si le profil n'est pas complet (pas de contact ou pas de nom), rediriger vers finalisation
                if (empty($user->contact) || empty($user->prenom)) {
                    return response()->json([
                        'success' => true,
                        'redirect' => route('user.auth.profile.complete'),
                        'message' => 'Veuillez finaliser votre profil.'
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'redirect' => route('user.dashboard'),
                    'message' => 'Connexion réussie.'
                ]);
            } else {
                // Inscription minimale
                $parts = explode(' ', $nameStr, 2);
                $prenom = $parts[0];
                $nom = $parts[1] ?? $prenom;

                $user = User::create([
                    'name' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'google_id' => $googleId,
                    'commune' => 'plateau',
                    'password' => Hash::make(Str::random(24)),
                    'profile_picture' => $pictureUrl,
                ]);

                Auth::login($user);

                return response()->json([
                    'success' => true,
                    'redirect' => route('user.auth.profile.complete'),
                    'message' => 'Compte créé. Veuillez finaliser votre profil.'
                ]);
            }

        } catch (IdTokenVerificationFailed $e) {
            Log::warning('Google Auth - Jeton invalide: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Jeton Google invalide ou expiré. Veuillez réessayer.'], 401);
        } catch (\Kreait\Firebase\Exception\InvalidArgumentException $e) {
            Log::error('Google Auth - Config Firebase invalide: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Configuration Firebase incorrecte.'], 500);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('Google Auth - Impossible de joindre les serveurs Google: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Impossible de vérifier votre identité Google. Problème réseau serveur.'], 503);
        } catch (\Exception $e) {
            Log::error('Google Auth Web Error [' . get_class($e) . ']: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            // ⚠️ DEBUG TEMPORAIRE - À RETIRER APRÈS DIAGNOSTIC
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur lors de l\'authentification Google.',
                'debug_error'   => $e->getMessage(),
                'debug_class'   => get_class($e),
                'debug_file'    => $e->getFile(),
                'debug_line'    => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Gère la connexion/inscription via Apple (Firebase ID Token).
     */
    public function handleAppleAuth(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            $verifier = IdTokenVerifier::createWithProjectId(config('firebase.projects.app.project_id', 'plateau-apps-user'));
            $token    = $verifier->verifyIdToken($request->id_token);
            $payload  = $token->payload();

            // Apple identities are nested under the 'firebase' claim
            $appleId = $payload['firebase']['identities']['apple.com'][0] ?? null;
            $email   = $payload['email'] ?? null;

            if (!$appleId) {
                return response()->json(['success' => false, 'message' => 'Identifiant Apple introuvable dans le jeton.'], 401);
            }

            $user = User::where('apple_id', $appleId)
                        ->when($email, fn($q) => $q->orWhere('email', $email))
                        ->first();

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

            // Nouvelle inscription
            $user = User::create([
                'name'     => $email ? explode('@', $email)[0] : 'Utilisateur',
                'prenom'   => '',
                'email'    => $email,
                'apple_id' => $appleId,
                'commune'  => 'plateau',
                'password' => Hash::make(Str::random(24)),
            ]);

            Auth::login($user);

            return response()->json([
                'success'  => true,
                'redirect' => route('user.auth.profile.complete'),
                'message'  => 'Compte Apple créé. Veuillez finaliser votre profil.'
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

    /**
     * Envoie un code OTP par SMS.
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'indicatif' => 'required|string',
            'contact' => 'required|string',
        ]);

        try {
            $phone = preg_replace('/[^0-9]/', '', $request->indicatif . $request->contact);
            
            // Vérifier si le numéro de téléphone est déjà associé à un compte complet (avec mot de passe)
            $contact = $request->contact;
            $existingUser = \App\Models\User::where(function ($query) use ($contact, $request) {
                $query->where('contact', $contact)
                      ->orWhere('contact', $request->contact);
            })->first();
            
            // Si l'utilisateur a un mot de passe, c'est que son compte est finalisé
            if ($existingUser && $existingUser->password !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce numéro de téléphone est déjà associé à un compte existant.',
                ], 403);
            }

            $otp = rand(100000, 999999);
            Cache::put('otp_' . $phone, $otp, now()->addMinutes(10));

            $message = "Votre code de vérification Plateau App est : " . $otp;
            $smsService = app(YellikaSmsService::class);
            $result = $smsService->sendSms($phone, $message);

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
     * Vérifie l'OTP et connecte l'utilisateur.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'indicatif' => 'required|string',
            'contact' => 'required|string',
            'otp' => 'required|string',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->indicatif . $request->contact);
        $cachedOtp = Cache::get('otp_' . $phone);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Code invalide ou expiré.'], 400);
        }

        Cache::forget('otp_' . $phone);

        try {
            $user = User::where('contact', $request->contact)
                        ->where('indicatif', $request->indicatif)
                        ->first();

            if (!$user) {
                $user = User::create([
                    'indicatif' => $request->indicatif,
                    'contact' => $request->contact,
                    'commune' => 'plateau',
                    'phone_verified_at' => now(),
                ]);
            } else {
                $user->update(['phone_verified_at' => now()]);
            }

            Auth::login($user);
            
            // Si le profil est complet, on va au dashboard, sinon on finalise
            $isComplete = !empty($user->name) && !empty($user->prenom) && $user->password !== null;
            $redirect = $isComplete ? route('user.dashboard') : route('user.auth.profile.complete');

            return response()->json([
                'success' => true,
                'redirect' => $redirect,
                'message' => $isComplete ? 'Connexion réussie.' : 'Téléphone vérifié. Veuillez finaliser votre profil.'
            ]);

        } catch (\Exception $e) {
            Log::error('Verify OTP Web Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    /**
     * Affiche la page de finalisation du profil.
     */
    public function showFinalizeProfile()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        
        // Si le profil est déjà complet, rediriger vers le dashboard
        // Pour les utilisateurs Google : nom + prénom suffisent
        // Pour les utilisateurs téléphone : nom + prénom + mot de passe requis
        $isComplete = !empty($user->name) && !empty($user->prenom);
        if (empty($user->google_id)) {
            $isComplete = $isComplete && !empty($user->contact) && $user->password !== null;
        }
        
        if ($isComplete) {
            return redirect()->route('user.dashboard');
        }

        return view('user.auth.finalize-profile', compact('user'));
    }

    /**
     * Enregistre les informations de finalisation.
     */
    public function submitFinalizeProfile(Request $request)
    {
        $user = Auth::user();
        
        // Règles de validation de base
        $rules = [
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'NNI' => 'nullable|string|max:50',
            'diaspora' => 'nullable|boolean',
            'pays_residence' => 'required_if:diaspora,1',
        ];

        // Le mot de passe n'est requis que pour les utilisateurs non-Google (inscription par téléphone)
        $isSocialUser = !empty($user->google_id) || !empty($user->apple_id);
        if (!$isSocialUser) {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['email'] = 'nullable|email|unique:users,email,' . $user->id;
        }

        $request->validate($rules);

        try {
            $data = [
                'name' => $request->name,
                'prenom' => $request->prenom,
                'NNI' => $request->NNI,
                'diaspora' => $request->has('diaspora'),
                'pays_residence' => $request->pays_residence,
                'ville_residence' => $request->ville_residence,
                'adresse_etrangere' => $request->adresse_etrangere,
            ];

            $isSocialUser = !empty($user->google_id) || !empty($user->apple_id);
            if (!$isSocialUser && $request->password) {
                $data['password'] = Hash::make($request->password);
            }

            if (!$isSocialUser) {
                $data['email'] = $request->email;
            }

            $user->update($data);

            return redirect()->route('user.dashboard')->with('success', 'Inscription finalisée avec succès !');

        } catch (\Exception $e) {
            Log::error('Finalize Profile Error: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la finalisation.')->withInput();
        }
    }
}
