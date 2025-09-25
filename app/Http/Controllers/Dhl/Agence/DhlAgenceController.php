<?php

namespace App\Http\Controllers\Dhl\Agence;

use App\Http\Controllers\Controller;
use App\Models\Dhl;
use App\Models\DHLAgence;
use App\Models\ResetCodePasswordAgency;
use App\Notifications\SendEmailToAgencyAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class DhlAgenceController extends Controller
{
    public function index(){
        $livreurs = DHLAgence::get();
        return view('dhl.agence.index', compact('livreurs'));
    }

    public function create(){
        return view('dhl.agence.create');
    }

    public function store(Request $request){
        // Validation des données
        $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|email|unique:d_h_l_agences,email',
           'contact' => 'required|string|min:10',
           'commune' => 'required|string|max:255',
           'profile_picture' => 'nullable|image|max:2048',

        ],[
            'name.required' => 'Le nom est obligatoire.',
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
           // Récupérer le mairie connecté
           $dhl = Auth::guard('dhl')->user();
   
           if (!$dhl || !$dhl->name) {
               return redirect()->back()->withErrors(['error' => 'Impossible de récupérer les informations du poste.']);
           }
   
           // Création du docteur
           $agency = new DHLAgence();
           $agency->name = $request->name;
           $agency->email = $request->email;
           $agency->contact = $request->contact;
           $agency->password = Hash::make('default');
           
           if ($request->hasFile('profile_picture')) {
               $agency->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
           }
   
           $agency->commune = $request->commune;
           $agency->dhl_id = Auth::guard('dhl')->user()->id;
           
           $agency->save();
   
           // Envoi de l'e-mail de vérification
           ResetCodePasswordAgency::where('email', $agency->email)->delete();
           $code1 = rand(1000, 4000);
           $code = $code1.''.$agency->id;
   
           ResetCodePasswordAgency::create([
               'code' => $code,
               'email' => $agency->email,
           ]);
   
           Notification::route('mail', $agency->email)
               ->notify(new SendEmailToAgencyAfterRegistrationNotification($code, $agency->email));
   
           return redirect()->route('agency.index')->with('success', 'Le livreur a bien été enregistré avec succès.');
       } catch (\Exception $e) {
           Log::error('Error creating agent: ' . $e->getMessage()); // Enregistrement de l'erreur dans les logs
           return redirect()->back()->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()]);
       }
    }
}
