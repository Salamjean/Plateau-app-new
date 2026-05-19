<?php

namespace App\Http\Controllers\Poste;

use App\Http\Controllers\Controller;
use App\Models\Poste;
use App\Models\ResetCodePasswordPoste;
use App\Notifications\SendEmailToPosteAfterRegistrationNotification;
use App\Notifications\SendEmailToPosteForPasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PosteController extends Controller
{
     public function index(){
        $mairie = Auth::guard('mairie')->user();

        $postes = Poste::whereNull('archived_at')
                ->with(['livreurs' => function ($q) {
                    $q->whereNull('archived_at')->orderBy('created_at', 'desc');
                }])
                ->paginate(10);
        return view('admin.poste.index',compact('postes'));
    }

    public function create(){
        return view('admin.poste.create');
    }

     public function store(Request $request){
        // Validation des données
        $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|email|unique:postes,email',
           'contact' => 'required|string|min:10',
           'commune' => 'required|string|max:255',
           'profile_picture' => 'nullable|image|max:25600',

        ],[
            'name.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique' => 'Cette adresse e-mail est déjà associée à un compte.',
            'contact.required' => 'Le contact est obligatoire.',
            'contact.min' => 'Le contact doit avoir au moins 10 chiffres.',
            'commune.required' => 'La commune est obligatoire.',
            'profile_picture.image' => 'Le fichier doit être une image.',
            'profile_picture.mimes' => 'L\'image doit être au format jpeg, png, jpg, gif ou svg.',
            'profile_picture.max' => 'L\'image ne doit pas dépasser 2048 KB.',
       
       ]);
   
       try {
           // Création du docteur
           $poste = new Poste();
           $poste->name = $request->name;
           $poste->prenom = 'RAS';
           $poste->email = $request->email;
           $poste->contact = $request->contact;
           $poste->password = Hash::make('default');
           
           if ($request->hasFile('profile_picture')) {
               $poste->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
           }
   
           $poste->commune = $request->commune;
           $poste->communeM = 'abidjan';
           
           $poste->save();
   
           // Envoi de l'e-mail de vérification
           ResetCodePasswordPoste::where('email', $poste->email)->delete();
           $code1 = rand(1000, 4000);
           $code = $code1.''.$poste->id;
   
           ResetCodePasswordPoste::create([
               'code' => $code,
               'email' => $poste->email,
           ]);
   
           Notification::route('mail', $poste->email)
               ->notify(new SendEmailToPosteAfterRegistrationNotification($code, $poste->email));
   
           return redirect()->route('post.index')->with('success', 'La poste a bien été enregistré avec succès.');
       } catch (\Exception $e) {
           return redirect()->back()->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()]);
       }
    }

    public function edit($id)
    {
        try {
            $etatCivil = Poste::findOrFail($id);
            return view('admin.poste.edit', compact('etatCivil'));
        } catch (\Exception $e) {
            return redirect()->route('post.index')
                ->with('error', 'Responsable non trouvé.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validation des données
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'contact' => 'required|string|max:20',
                'commune' => 'required|string|max:255',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:25600'
            ]);

            // Recherche de l'enregistrement
            $etatCivil = Poste::findOrFail($id);
            
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
            return redirect()->route('post.index')
                ->with('success', 'Le service a été modifié avec succès.');
                
        } catch (\Exception $e) {
            // En cas d'erreur
            return redirect()->route('post.edit', $id)
                ->with('error', 'Une erreur est survenue lors de la modification.')
                ->withInput();
        }
    }

    public function requestPasswordReset($id)
    {
        Log::info("Début du traitement de demande de réinitialisation de mot de passe par clé pour Poste (Livraison)", ['poste_id' => $id]);
        try {
            $poste = Poste::findOrFail($id);
            Log::info("Poste cible trouvé", ['id' => $poste->id, 'name' => $poste->name, 'email' => $poste->email]);

            // Générer un code OTP de réinitialisation (avec l'ID à la fin comme dans le store)
            ResetCodePasswordPoste::where('email', $poste->email)->delete();
            $code1 = rand(1000, 4000);
            $code = $code1.''.$poste->id;
            Log::info("Code OTP de réinitialisation généré pour Poste", ['email' => $poste->email, 'code' => $code]);

            ResetCodePasswordPoste::create([
                'code' => $code,
                'email' => $poste->email,
            ]);
            Log::info("OTP sauvegardé avec succès dans reset_code_password_postes");

            // Envoyer la notification par e-mail
            Log::info("Début de l'envoi de l'email de réinitialisation Poste", ['to' => $poste->email]);
            Notification::route('mail', $poste->email)
                ->notify(new SendEmailToPosteForPasswordResetNotification($code, $poste->email));
            Log::info("E-mail de réinitialisation Poste envoyé avec succès", ['to' => $poste->email]);

            return redirect()->route('post.index')
                ->with('success', 'Demande de mise à jour du mot de passe envoyée avec succès par email à ' . $poste->email);
        } catch (\Exception $e) {
            Log::error('Erreur critique lors de la demande de réinitialisation de mot de passe de Poste: ' . $e->getMessage(), [
                'poste_id' => $id,
                'exception' => $e
            ]);

            return redirect()->route('post.index')
                ->with('error', 'Une erreur est survenue lors de l\'envoi du code de réinitialisation : ' . $e->getMessage());
        }
    }
}
