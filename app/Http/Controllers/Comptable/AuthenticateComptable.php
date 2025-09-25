<?php

namespace App\Http\Controllers\Comptable;

use App\Http\Controllers\Controller;
use App\Models\Comptable;
use App\Models\ResetCodePasswordComptable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticateComptable extends Controller
{
    public function defineAccess($email){
        $checkSousadminExiste = Comptable::where('email', $email)->first();

        if($checkSousadminExiste){
            return view('comptable.auth.defineAcces', compact('email'));
        }else{
            return redirect()->route('comptable.login');
        };
    }

     public function submitDefineAccess(Request $request){

        // Validation des données
        $request->validate([
                'code'=>'required|exists:reset_code_password_comptables,code',
                'password' => 'required|same:confirme_password',
                'confirme_password' => 'required|same:password',
            ], [
                'code.exists' => 'Le code de réinitialisation est invalide.',
                'password.same' => 'Les mots de passe doivent être identiques.',
                'confirme_password.same' => 'Les mots de passe doivent être identiques.',
        ]);
        try {
            $comptable = Comptable::where('email', $request->email)->first();

            if ($comptable) {
                // Mise à jour du mot de passe
                $comptable->password = Hash::make($request->password);
                $comptable->update();

                if($comptable){
                $existingcodehop =  ResetCodePasswordComptable::where('email', $comptable->email)->count();

                if($existingcodehop > 1){
                    ResetCodePasswordComptable::where('email', $comptable->email)->delete();
                }
            }

                return redirect()->route('comptable.login')->with('success', 'Compte mis à jour avec succès');
            } else {
                return redirect()->route('comptable.login')->with('error', 'Email inconnu');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function login(){
        if (auth('comptable')->check()) {
            return redirect()->route('comptable.dashboard');
        }
        return view('comptable.auth.login');
    }

    public function handleLogin(Request $request)
    {
        // Validation des champs du formulaire
        $request->validate([
            'email' => 'required|exists:comptables,email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Le mail est obligatoire.',
            'email.exists' => 'Cette adresse mail n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            // Récupérer finance par son email
            $comptable = Comptable::where('email', $request->email)->first();

            // Vérifier si l'finance est archivé
            if ($comptable && $comptable->archived_at !== null) {
                return redirect()->back()->with('error', 'Votre compte a été supprimé. Vous ne pouvez pas vous connecter.');
            }

            if (auth('comptable')->attempt($request->only('email', 'password'))) {
                return redirect()->route('comptable.dashboard')->with('success', 'Bienvenue sur la page des demandes en attente');
            } else {
                return redirect()->back()->with('error', 'Votre mot de passe est incorrect.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la connexion.');
        }
    }
}
