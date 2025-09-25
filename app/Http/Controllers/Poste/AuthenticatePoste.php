<?php

namespace App\Http\Controllers\Poste;

use App\Http\Controllers\Controller;
use App\Models\Poste;
use App\Models\ResetCodePasswordPoste;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthenticatePoste extends Controller
{
    public function defineAccess($email){
        //Vérification si le sous-admin existe déjà
        $checkSousadminExiste = Poste::where('email', $email)->first();
        if($checkSousadminExiste){
            return view('poste.auth.defineAcces', compact('email'));
        }else{
            return redirect()->route('post.login');
        };
    }

     public function submitDefineAccess(Request $request){
        // Validation des données
        $request->validate([
                'code'=>'required|exists:reset_code_password_postes,code',
                'password' => 'required|same:confirme_password',
                'confirme_password' => 'required|same:password',
            ], [
                'code.exists' => 'Le code de réinitialisation est invalide.',
                'code.required' => 'Le code de réinitialisation est obligatoire verifié votre mail.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.same' => 'Les mots de passe doivent être identiques.',
                'confirme_password.same' => 'Les mots de passe doivent être identiques.',
                'confirme_password.required' => 'Le mot de passe de confirmation est obligatoire.',
        ]);
        try {
            $poste = Poste::where('email', $request->email)->first();
        
            if ($poste) {
                // Mise à jour du mot de passe
                $poste->password = Hash::make($request->password);
        
                // Vérifier si une image est uploadée
                if ($request->hasFile('profile_picture')) {
                    // Supprimer l'ancienne photo si elle existe
                    if ($poste->profile_picture) {
                        Storage::delete('public/' . $poste->profile_picture);
                    }
        
                    // Stocker la nouvelle image
                    $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                    $poste->profile_picture = $imagePath;
                }
                $poste->update();
        
                if($poste){
                   $existingcodeajoint =  ResetCodePasswordPoste::where('email', $poste->email)->count();
        
                   if($existingcodeajoint > 1){
                    ResetCodePasswordPoste::where('email', $poste->email)->delete();
                   }
                }
        
                return redirect()->route('post.login')->with('success', 'Compte mis à jour avec succès');
            } else {
                return redirect()->route('post.login')->with('error', 'Email inconnu');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function login(){
        if (auth('poste')->check()) {
            return redirect()->route('post.dashboard');
        }
        return view('poste.auth.login');
    }

     public function handleLogin(Request $request)
    {
        // Validation des champs du formulaire
        $request->validate([
            'email' => 'required|exists:postes,email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Le mail est obligatoire.',
            'email.exists' => 'Cette adresse mail n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            // Récupérer la caisse par son email
            $caisse = Poste::where('email', $request->email)->first();

            // Vérifier si la caisse est archivée
            if ($caisse && $caisse->archived_at !== null) {
                return redirect()->back()->with('error', 'Votre compte a été suprrimé. Vous ne pouvez pas vous connecter.');
            }

            // Tenter la connexion
            if (auth('poste')->attempt($request->only('email', 'password'))) {
                return redirect()->route('post.dashboard')->with('success', 'Bienvenue sur votre page.');
            } else {
                return redirect()->back()->with('error', 'Votre mot de passe est incorrect.');
            }
        } catch (Exception $e) {
            // Gérer les erreurs
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la connexion.');
        }
    }
}
