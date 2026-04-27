<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

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
        $user = Auth::user();
        $isGoogleUser = !empty($user->google_id);

        $rules = [
            'name'              => 'required|string|max:255',
            'prenom'            => 'required|string|max:255',
            'email'             => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'indicatif'         => 'nullable|string|max:10',
            'contact'           => 'nullable|string|max:20',
            'CMU'               => 'nullable|string|max:255',
            'diaspora'          => 'sometimes|boolean',
            'pays_residence'    => 'nullable|string|max:255',
            'ville_residence'   => 'nullable|string|max:255',
            'adresse_etrangere' => 'nullable|string|max:255',
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:25600',
        ];

        // Les utilisateurs Google n'ont pas de mot de passe → pas de confirmation requise
        if (!$isGoogleUser) {
            $rules['password']     = 'required|current_password';
            $rules['new_password'] = ['nullable', 'confirmed', 'different:password', Password::min(8)];
        }

        $request->validate($rules, [
            'password.current_password' => 'Le mot de passe actuel de confirmation est incorrect.',
            'new_password.different'    => 'Le nouveau mot de passe doit être différent du mot de passe actuel.',
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
        $request->validate([
            'indicatif' => 'required|string|max:10',
            'contact' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        $user->indicatif = $request->indicatif;
        $user->contact = $request->contact;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Numéro de téléphone enregistré avec succès.']);
    }
}