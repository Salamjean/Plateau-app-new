<?php

namespace App\Http\Controllers\Mairie;

use App\Http\Controllers\Controller;
use App\Models\Mairie;
use App\Models\ResetCodePasswordMairie;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MairieAuthenticate extends Controller
{
   public function defineAccess($email){
        $checkSousadminExiste = Mairie::where('email', $email)->first();

        if($checkSousadminExiste){
            return view('mairie.auth.defineAcces', compact('email'));
        }else{
            return redirect()->route('mairie.login');
        };
    }

    public function submitDefineAccess(Request $request){

        // Validation des données
        $request->validate([
                'code'=>'required|exists:reset_code_password_mairies,code',
                'password' => 'required|same:confirme_password',
                'confirme_password' => 'required|same:password',
            ], [
                'code.exists' => 'Le code de réinitialisation est invalide.',
                'password.same' => 'Les mots de passe doivent être identiques.',
                'confirme_password.same' => 'Les mots de passe doivent être identiques.',
        ]);
        try {
            $vendor = Mairie::where('email', $request->email)->first();

            if ($vendor) {
                // Mise à jour du mot de passe
                $vendor->password = Hash::make($request->password);
                $vendor->update();

                if($vendor){
                $existingcodehop =  ResetCodePasswordMairie::where('email', $vendor->email)->count();

                if($existingcodehop > 1){
                    ResetCodePasswordMairie::where('email', $vendor->email)->delete();
                }
                }

                return redirect()->route('mairie.login')->with('success', 'Compte mis à jour avec succès');
            } else {
                return redirect()->route('mairie.login')->with('error', 'Email inconnu');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function login(){
        if (auth('mairie')->check()) {
            return redirect()->route('mairie.dashboard');
        }
        return view('mairie.auth.login');
    }

    public function handleLogin(Request $request)
    {
        $request->validate([
            'email' =>'required|exists:mairies,email',
            'password' => 'required|min:8',
        ], [
            
            
            'email.required' => 'Le mail est obligatoire.',
            'email.exists' => 'Cette adresse mail n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min'=> 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            if(auth('mairie')->attempt($request->only('email', 'password')))
            {
                return redirect()->route('mairie.dashboard')->with('Bienvenu sur votre page ');
            }else{
                return redirect()->back()->with('error', 'Votre mot de passe.');
            }
        } catch (Exception $e) {
            dd($e);
        }
    }
}
