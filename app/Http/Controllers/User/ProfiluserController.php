<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\DeletionRequestMail;

class ProfiluserController extends Controller
{
    /**
     * Affiche le formulaire du profil de l'utilisateur.
     */
    public function show()
    {
        return view('user.auth.profil');
    }

    /**
     * Met à jour les informations ET/OU le mot de passe de l'utilisateur.
     */
    public function update(Request $request)
    {
        $user      = Auth::user();
        $isSocial  = !empty($user->google_id) || !empty($user->apple_id);

        $rules = [
            'name'              => 'required|string|max:255',
            'prenom'            => 'required|string|max:255',
            'email'             => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'indicatif'         => 'nullable|string|max:10',
            'contact'           => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'CMU'               => 'nullable|string|max:255',
            'diaspora'          => 'sometimes|boolean',
            'pays_residence'    => 'nullable|string|max:255',
            'ville_residence'   => 'nullable|string|max:255',
            'adresse_etrangere' => 'nullable|string|max:255',
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:25600',
        ];

        // Mot de passe requis uniquement pour les utilisateurs normaux
        if (!$isSocial) {
            $rules['password']     = 'required|current_password';
            $rules['new_password'] = ['nullable', 'confirmed', 'different:password', Password::min(8)];
        }

        $request->validate($rules, [
            'password.current_password' => 'Le mot de passe actuel de confirmation est incorrect.',
            'new_password.different'    => 'Le nouveau mot de passe doit être différent du mot de passe actuel.',
            'contact.unique'            => 'Ce numéro est déjà attribué à un autre utilisateur.',
        ]);
        
        // Mettre à jour les informations du profil
        $user->fill($request->except(['password', 'new_password', 'new_password_confirmation', 'profile_picture', 'diaspora']));
        $user->diaspora = $request->has('diaspora');

        // Mettre à jour le mot de passe SEULEMENT s'il a été fourni
        if ($request->filled('new_password')) {
            $user->password = Hash::make($request->new_password);
        }

        // Gérer la photo de profil
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        $user->save();

        return redirect()->route('user.profile.show')->with('success', 'Votre profil a été mis à jour avec succès.');
    }
    
    /**
     * Supprime la photo de profil de l'utilisateur.
     */
    public function deleteProfilePicture()
    {
        $user = Auth::user();

        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
            $user->profile_picture = null;
            $user->save();
            return back()->with('success', 'Votre photo de profil a été supprimée.');
        }

        return back()->with('error', 'Aucune photo de profil à supprimer.');
    }

    /**
     * Affiche la page publique de demande de suppression de compte.
     */
    public function showDeletionForm()
    {
        return view('user.profile.delete');
    }

    /**
     * Envoie une demande de suppression de compte par e-mail.
     */
    public function sendDeletionRequest(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'reason' => 'nullable|string|max:1000',
        ]);

        try {
            Mail::to('infos@plateau-apps.com')->send(new DeletionRequestMail(
                $request->name,
                $request->email,
                $request->reason
            ));

            return back()->with('success', 'Votre demande de suppression de compte a été envoyée avec succès. Notre équipe traitera votre demande prochainement.');
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur s\'est produite lors de l\'envoi de votre demande. Veuillez réessayer plus tard.')->withInput();
        }
    }

    /**
     * Envoie un OTP au nouvel email pour confirmer le changement (Web AJAX).
     */
    public function requestEmailOtp(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre compte.',
        ]);

        try {
            $otp = rand(100000, 999999);

            Cache::put('email_change_otp_' . $user->id, [
                'otp'       => $otp,
                'new_email' => $request->email,
            ], now()->addMinutes(10));

            Log::info('Web EmailOTP Tentative envoi', [
                'user_id'   => $user->id,
                'new_email' => $request->email,
            ]);

            Mail::raw(
                "Bonjour " . ($user->prenom ?? $user->name) . ",\n\n"
                . "Votre code de confirmation pour changer votre adresse email sur Plateau App est :\n\n"
                . "    " . $otp . "\n\n"
                . "Ce code est valable 10 minutes.\n"
                . "Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.",
                function ($message) use ($request) {
                    $message->to($request->email)
                            ->subject('Code de confirmation - Changement d\'email Plateau App');
                }
            );

            Log::info('Web EmailOTP Envoyé avec succès', [
                'user_id'   => $user->id,
                'new_email' => $request->email,
            ]);

            return response()->json(['success' => true, 'message' => 'Code envoyé à ' . $request->email]);

        } catch (\Exception $e) {
            Log::error('Web EmailOTP Échec', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'envoi du code.'], 500);
        }
    }

    /**
     * Vérifie l'OTP et met à jour l'email (Web AJAX).
     */
    public function updateEmail(Request $request)
    {
        $user = Auth::user();

        $request->validate(['otp' => 'required|string|size:6']);

        $cached = Cache::get('email_change_otp_' . $user->id);

        if (!$cached) {
            return response()->json(['success' => false, 'message' => 'Aucun code en attente. Faites d\'abord une demande.'], 400);
        }

        if ((string) $cached['otp'] !== (string) $request->otp) {
            return response()->json(['success' => false, 'message' => 'Code incorrect ou expiré.'], 422);
        }

        try {
            Cache::forget('email_change_otp_' . $user->id);
            $ancienEmail = $user->email;
            $user->update(['email' => $cached['new_email']]);

            Log::info('Web EmailOTP Email mis à jour', [
                'user_id'       => $user->id,
                'ancien_email'  => $ancienEmail,
                'nouveau_email' => $cached['new_email'],
            ]);

            return response()->json(['success' => true, 'message' => 'Email mis à jour avec succès.', 'email' => $user->email]);

        } catch (\Exception $e) {
            Log::error('Web EmailOTP Échec mise à jour', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur serveur.'], 500);
        }
    }

    /**
     * Vérifie le mot de passe actuel de l'utilisateur pour les requêtes AJAX.
     */
    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (Hash::check($request->password, Auth::user()->password)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 422);
    }

    /**
     * Met à jour uniquement le contact (téléphone) via AJAX.
     */
    public function updateContact(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'indicatif' => 'required|string|max:10',
            'contact'   => ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
        ], [
            'contact.unique' => 'Ce numéro est déjà attribué à un autre utilisateur.',
        ]);

        $user->indicatif = $request->indicatif;
        $user->contact = $request->contact;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Numéro de téléphone enregistré avec succès.']);
    }
}