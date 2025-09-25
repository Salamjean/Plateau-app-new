<?php

namespace App\Http\Controllers\Dhl\Agence;

use App\Http\Controllers\Controller;
use App\Models\DHLAgence;
use App\Models\ResetCodePasswordAgency;
use App\Models\ResetCodePasswordAgent;
use App\Models\ResetCodePasswordDhl;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthenticateDhlAgence extends Controller
{
    public function defineAccess($email){
        //Vérification si le sous-admin existe déjà
        $checkSousadminExiste = DHLAgence::where('email', $email)->first();
        if($checkSousadminExiste){
            return view('agency.auth.defineAcces', compact('email'));
        }else{
            return redirect()->route('agency.login');
        };
    }

    public function submitDefineAccess(Request $request){
        // Validation des données
        $request->validate([
                'code'=>'required|exists:reset_code_password_agencies,code',
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
            $dhlagency = DHLAgence::where('email', $request->email)->first();
        
            if ($dhlagency) {
                // Mise à jour du mot de passe
                $dhlagency->password = Hash::make($request->password);
        
                // Vérifier si une image est uploadée
                if ($request->hasFile('profile_picture')) {
                    // Supprimer l'ancienne photo si elle existe
                    if ($dhlagency->profile_picture) {
                        Storage::delete('public/' . $dhlagency->profile_picture);
                    }
        
                    // Stocker la nouvelle image
                    $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
                    $dhlagency->profile_picture = $imagePath;
                }
                $dhlagency->update();
        
                if($dhlagency){
                   $existingcodeajoint =  ResetCodePasswordAgency::where('email', $dhlagency->email)->count();
        
                   if($existingcodeajoint > 1){
                    ResetCodePasswordAgency::where('email', $dhlagency->email)->delete();
                   }
                }
        
                return redirect()->route('agency.login')->with('success', 'Compte mis à jour avec succès');
            } else {
                return redirect()->route('agency.login')->with('error', 'Email inconnu');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function login(){
        if (auth('agency')->check()) {
            return redirect()->route('agency.dashboard');
        }
        return view('agency.auth.login');
    }

     public function handleLogin(Request $request)
    {
        // Validation des champs du formulaire
        $request->validate([
            'email' => 'required|exists:d_h_l_agences,email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Le mail est obligatoire.',
            'email.exists' => 'Cette adresse mail n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            // Récupérer la caisse par son email
            $caisse = DHLAgence::where('email', $request->email)->first();

            // Vérifier si la caisse est archivée
            if ($caisse && $caisse->archived_at !== null) {
                return redirect()->back()->with('error', 'Votre compte a été suprrimé. Vous ne pouvez pas vous connecter.');
            }

            // Tenter la connexion
            if (auth('agency')->attempt($request->only('email', 'password'))) {
                return redirect()->route('agency.dashboard')->with('success', 'Bienvenue sur votre page.');
            } else {
                return redirect()->back()->with('error', 'Votre mot de passe est incorrect.');
            }
        } catch (Exception $e) {
            // Gérer les erreurs
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la connexion.');
        }
    }
}
