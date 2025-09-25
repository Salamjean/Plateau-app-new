<?php

namespace App\Http\Controllers\Dhl;

use App\Http\Controllers\Controller;
use App\Models\Dhl;
use App\Models\ResetCodePasswordDhl;
use App\Notifications\SendEmailToDhlAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class Dhlcontroller extends Controller
{
    public function index(){
        $postes = Dhl::get();
        return view('admin.dhl.index', compact('postes'));
    }

    public function create(){
        return view('admin.dhl.create');
    }

    public function store(Request $request){
        // Validation des données
        $request->validate([
           'name' => 'required|string|max:255',
           'prenom' => 'required|string|max:255',
           'email' => 'required|email|unique:dhls,email',
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
           $dhl = new Dhl();
           $dhl->name = $request->name;
           $dhl->prenom = $request->prenom;
           $dhl->email = $request->email;
           $dhl->contact = $request->contact;
           $dhl->password = Hash::make('default');
           
           if ($request->hasFile('profile_picture')) {
               $dhl->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
           }
   
           $dhl->commune = $request->commune;
           $dhl->communeM = 'abidjan';
           
           $dhl->save();
   
           // Envoi de l'e-mail de vérification
           ResetCodePasswordDhl::where('email', $dhl->email)->delete();
           $code1 = rand(1000, 4000);
           $code = $code1.''.$dhl->id;
   
           ResetCodePasswordDhl::create([
               'code' => $code,
               'email' => $dhl->email,
           ]);
   
           Notification::route('mail', $dhl->email)
               ->notify(new SendEmailToDhlAfterRegistrationNotification($code, $dhl->email));
   
           return redirect()->route('dhl.index')->with('success', 'La DHL a bien été enregistré avec succès.');
       } catch (\Exception $e) {
           return redirect()->back()->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()]);
       }
    }
}
