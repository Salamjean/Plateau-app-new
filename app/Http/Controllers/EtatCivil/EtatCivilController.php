<?php

namespace App\Http\Controllers\EtatCivil;

use App\Http\Controllers\Controller;
use App\Http\Requests\etatCivilRequest;
use App\Models\EtatCivil;
use App\Models\ResetCodePasswordEtatCivil;
use App\Notifications\SendEmailToEtatCivilAfterRegistrationNotification;
use App\Notifications\SendEmailToEtatCivilForPasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class EtatCivilController extends Controller
{
    public function index(){
        $mairie = Auth::guard('mairie')->user();

        $etatCivils = EtatCivil::whereNull('archived_at')
                ->where('mairie_id', $mairie->id)
                ->with(['agents' => function ($q) {
                    $q->whereNull('archived_at')->orderBy('created_at', 'desc');
                }])
                ->paginate(10);
        return view('mairie.etatCivil.index',compact('etatCivils'));
    }

    public function create(){
        return view('mairie.etatCivil.create');
    }

    public function store(etatCivilRequest $request){
           $mairie = Auth::guard('mairie')->user();

       try {
           $etatCivil = new EtatCivil();
           $etatCivil->name_respo = $request->name_respo;
           $etatCivil->email = $request->email;
           $etatCivil->contact = $request->contact;
           $etatCivil->password = Hash::make('default');
           
           if ($request->hasFile('profile_picture')) {
               $etatCivil->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
           }
   
           $etatCivil->commune = $request->commune;
           $etatCivil->mairie_id = $mairie->id;
           $etatCivil->communeM = $mairie->name;
           
           $etatCivil->save();
   
           // Envoi de l'e-mail de vérification
           ResetCodePasswordEtatCivil::where('email', $etatCivil->email)->delete();
           $code1 = rand(1000, 4000);
           $code = $code1.''.$etatCivil->id;
   
           ResetCodePasswordEtatCivil::create([
               'code' => $code,
               'email' => $etatCivil->email,
           ]);
   
           Notification::route('mail', $etatCivil->email)
               ->notify(new SendEmailToEtatCivilAfterRegistrationNotification($code, $etatCivil->email));
   
           return redirect()->route('mairie.state.index')->with('success', 'Le service d\'état civil a bien été enregistré avec succès.');
       } catch (\Exception $e) {
           return redirect()->back()->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()]);
       }
    }

     public function destroy($id)
    {
        try {
            // Recherche de l'enregistrement
            $etatCivil = EtatCivil::findOrFail($id);
            
            // Suppression
            $etatCivil->delete();
            
            // Redirection avec message de succès
            return redirect()->route('mairie.state.index')
                ->with('success', 'Le responsable a été supprimé avec succès.');
                
        } catch (\Exception $e) {
            // En cas d'erreur
            return redirect()->route('mairie.state.index')
                ->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

     public function edit($id)
    {
        try {
            $etatCivil = EtatCivil::findOrFail($id);
            return view('mairie.etatCivil.edit', compact('etatCivil'));
        } catch (\Exception $e) {
            return redirect()->route('mairie.state.index')
                ->with('error', 'Responsable non trouvé.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation des données
            $validated = $request->validate([
                'name_respo' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'contact' => 'required|string|max:20',
                'commune' => 'required|string|max:255',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:25600'
            ]);

            // Recherche de l'enregistrement
            $etatCivil = EtatCivil::findOrFail($id);
            
            // Traitement de l'image si elle est fournie
            if ($request->hasFile('profile_picture')) {
                // Supprimer l'ancienne image si elle existe
                if ($etatCivil->profile_picture) {
                    Storage::delete('public/profile_pictures/' . $etatCivil->profile_picture);
                }
                
                // Enregistrer la nouvelle image
                $imageName = time() . '.' . $request->profile_picture->extension();
                $request->profile_picture->storeAs('public/profile_pictures', $imageName);
                $validated['profile_picture'] = $imageName;
            }
            
            // Mise à jour
            $etatCivil->update($validated);
            
            // Redirection avec message de succès
            return redirect()->route('mairie.state.index')
                ->with('success', 'Le responsable a été modifié avec succès.');
                
        } catch (\Exception $e) {
            // En cas d'erreur
            return redirect()->route('etat-civil.edit', $id)
                ->with('error', 'Une erreur est survenue lors de la modification.')
                ->withInput();
        }
    }

    public function requestPasswordReset($id)
    {
        Log::info("Début du traitement de demande de réinitialisation de mot de passe par clé pour EtatCivil", ['etat_civil_id' => $id]);
        try {
            $etatCivil = EtatCivil::findOrFail($id);
            Log::info("EtatCivil cible trouvé", ['id' => $etatCivil->id, 'name_respo' => $etatCivil->name_respo, 'email' => $etatCivil->email]);

            // Générer un code OTP de réinitialisation (avec l'ID à la fin comme dans le store)
            ResetCodePasswordEtatCivil::where('email', $etatCivil->email)->delete();
            $code1 = rand(1000, 4000);
            $code = $code1.''.$etatCivil->id;
            Log::info("Code OTP de réinitialisation généré pour EtatCivil", ['email' => $etatCivil->email, 'code' => $code]);

            ResetCodePasswordEtatCivil::create([
                'code' => $code,
                'email' => $etatCivil->email,
            ]);
            Log::info("OTP sauvegardé avec succès dans reset_code_password_etat_civils");

            // Envoyer la notification par e-mail
            Log::info("Début de l'envoi de l'email de réinitialisation EtatCivil", ['to' => $etatCivil->email]);
            Notification::route('mail', $etatCivil->email)
                ->notify(new SendEmailToEtatCivilForPasswordResetNotification($code, $etatCivil->email));
            Log::info("E-mail de réinitialisation EtatCivil envoyé avec succès", ['to' => $etatCivil->email]);

            return redirect()->route('mairie.state.index')
                ->with('success', 'Demande de mise à jour du mot de passe envoyée avec succès par email à ' . $etatCivil->email);
        } catch (\Exception $e) {
            Log::error('Erreur critique lors de la demande de réinitialisation de mot de passe de EtatCivil: ' . $e->getMessage(), [
                'etat_civil_id' => $id,
                'exception' => $e
            ]);

            return redirect()->route('mairie.state.index')
                ->with('error', 'Une erreur est survenue lors de l\'envoi du code de réinitialisation : ' . $e->getMessage());
        }
    }
}
