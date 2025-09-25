<?php

namespace App\Http\Controllers\EtatCivil;

use App\Http\Controllers\Controller;
use App\Models\EtatCivil;
use App\Models\ResetCodePasswordEtatCivil;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticateEtatCivil extends Controller
{
    public function defineAccess($email){
        $checkSousadminExiste = EtatCivil::where('email', $email)->first();

        if($checkSousadminExiste){
            return view('etatCivil.auth.defineAcces', compact('email'));
        }else{
            return redirect()->route('etat_civil.login');
        };
    }

    public function submitDefineAccess(Request $request){

        // Validation des données
        $request->validate([
                'code'=>'required|exists:reset_code_password_etat_civils,code',
                'password' => 'required|same:confirme_password',
                'confirme_password' => 'required|same:password',
            ], [
                'code.exists' => 'Le code de réinitialisation est invalide.',
                'password.same' => 'Les mots de passe doivent être identiques.',
                'confirme_password.same' => 'Les mots de passe doivent être identiques.',
        ]);
        try {
            $etatCivil = EtatCivil::where('email', $request->email)->first();

            if ($etatCivil) {
                // Mise à jour du mot de passe
                $etatCivil->password = Hash::make($request->password);
                $etatCivil->update();

                if($etatCivil){
                $existingcodehop =  ResetCodePasswordEtatCivil::where('email', $etatCivil->email)->count();

                if($existingcodehop > 1){
                    ResetCodePasswordEtatCivil::where('email', $etatCivil->email)->delete();
                }
            }

                return redirect()->route('etat_civil.login')->with('success', 'Compte mis à jour avec succès');
            } else {
                return redirect()->route('etat_civil.login')->with('error', 'Email inconnu');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function login(){
        if (auth('etatCivil')->check()) {
            return redirect()->route('etat_civil.dashboard');
        }
        return view('etatCivil.auth.login');
    }

    public function handleLogin(Request $request)
    {
        $request->validate([
            'email' =>'required|exists:etat_civils,email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Le mail est obligatoire.',
            'email.exists' => 'Cette adresse mail n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min'=> 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            if(auth('etatCivil')->attempt($request->only('email', 'password')))
            {
                return redirect()->route('etat_civil.dashboard')->with('Bienvenu sur votre page ');
            }else{
                return redirect()->back()->with('error', 'Votre mot de passe.');
            }
        } catch (Exception $e) {
            dd($e);
        }
    }
}
