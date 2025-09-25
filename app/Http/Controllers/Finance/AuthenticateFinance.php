<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Models\ResetCodePasswordFinance;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticateFinance extends Controller
{
    public function defineAccess($email){
        $checkSousadminExiste = Finance::where('email', $email)->first();

        if($checkSousadminExiste){
            return view('finance.auth.defineAcces', compact('email'));
        }else{
            return redirect()->route('finance.login');
        };
    }

    public function submitDefineAccess(Request $request){

        // Validation des données
        $request->validate([
                'code'=>'required|exists:reset_code_password_finances,code',
                'password' => 'required|same:confirme_password',
                'confirme_password' => 'required|same:password',
            ], [
                'code.exists' => 'Le code de réinitialisation est invalide.',
                'password.same' => 'Les mots de passe doivent être identiques.',
                'confirme_password.same' => 'Les mots de passe doivent être identiques.',
        ]);
        try {
            $finance = Finance::where('email', $request->email)->first();

            if ($finance) {
                // Mise à jour du mot de passe
                $finance->password = Hash::make($request->password);
                $finance->update();

                if($finance){
                $existingcodehop =  ResetCodePasswordFinance::where('email', $finance->email)->count();

                if($existingcodehop > 1){
                    ResetCodePasswordFinance::where('email', $finance->email)->delete();
                }
            }

                return redirect()->route('finance.login')->with('success', 'Compte mis à jour avec succès');
            } else {
                return redirect()->route('finance.login')->with('error', 'Email inconnu');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function login(){
        if (auth('finance')->check()) {
            return redirect()->route('finance.dashboard');
        }
        return view('finance.auth.login');
    }

    public function handleLogin(Request $request)
    {
        // Validation des champs du formulaire
        $request->validate([
            'email' => 'required|exists:finances,email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Le mail est obligatoire.',
            'email.exists' => 'Cette adresse mail n\'existe pas.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
        ]);

        try {
            // Récupérer finance par son email
            $finance = Finance::where('email', $request->email)->first();

            // Vérifier si l'finance est archivé
            if ($finance && $finance->archived_at !== null) {
                return redirect()->back()->with('error', 'Votre compte a été supprimé. Vous ne pouvez pas vous connecter.');
            }

            if (auth('finance')->attempt($request->only('email', 'password'))) {
                return redirect()->route('finance.dashboard')->with('success', 'Bienvenue sur la page des demandes en attente');
            } else {
                return redirect()->back()->with('error', 'Votre mot de passe est incorrect.');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la connexion.');
        }
    }
}
