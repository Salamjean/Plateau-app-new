<?php

namespace App\Http\Controllers\Poste;

use App\Http\Controllers\Controller;
use App\Models\Poste;
use App\Models\ResetCodePasswordPoste;
use App\Notifications\SendEmailToPosteAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PosteController extends Controller
{
     public function index(){
        $mairie = Auth::guard('mairie')->user();

        $postes = Poste::whereNull('archived_at')
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
           'profile_picture' => 'nullable|image|max:2048',

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
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
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
}
