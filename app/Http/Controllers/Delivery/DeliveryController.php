<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Livreur;
use App\Models\ResetCodePasswordLivreur;
use App\Notifications\SendEmailToLivreurAfterRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class DeliveryController extends Controller
{
   public function index(){
        $livreurs = Livreur::withCount([
                'deces as total_colis',
                'deces as total_livraisons_deces' => function($query) {
                    $query->where('statut_livraison', 'livré');
                    $query->where('livraison_id',Auth::guard('poste')->user()->id);
                },
                'mariage as total_livraisons_mariages' => function($query) {
                    $query->where('statut_livraison', 'livré');
                    $query->where('livraison_id',Auth::guard('poste')->user()->id);
                },
                'naissance as total_livraisons_naissances' => function($query) {
                    $query->where('statut_livraison', 'livré');
                    $query->where('livraison_id',Auth::guard('poste')->user()->id);
                }
            ])
            ->get()
            ->map(function ($livreur) {
                // Calcul du total des livraisons pour tous les modèles
                $livreur->total_livraisons = 
                    ($livreur->total_livraisons_deces ?? 0) +
                    ($livreur->total_livraisons_mariages ?? 0) +
                    ($livreur->total_livraisons_naissances ?? 0);
                
                // Calcul du montant total pour tous les modèles
                $livreur->montant_total = 
                    ($livreur->montant_total_deces ?? 0) +
                    ($livreur->montant_total_mariages ?? 0) +
                    ($livreur->montant_total_naissances ?? 0);
                
                // Calcul du solde disponible (montant total - versements)
                $livreur->solde_disponible = $livreur->montant_total - ($livreur->total_versements ?? 0);
                
                return $livreur;
            });
        
        // Calcul des totaux généraux
        $totalMontantLivraisons = $livreurs->sum('montant_total');
        $totalColisLivre = $livreurs->sum('total_livraisons');
        $totalSoldeDisponible = $livreurs->sum('solde_disponible');
        
        return view('poste.livreur.index', compact('livreurs', 'totalMontantLivraisons', 'totalColisLivre', 'totalSoldeDisponible'));
    }
    public function create(){
        return view('poste.livreur.create');
    }

    public function store(Request $request){
        $poste = Auth::guard('poste')->user();
        // Validation des données
        $request->validate([
           'name' => 'required|string|max:255',
           'prenom' => 'required|string|max:255',
           'email' => 'required|email|unique:livreurs,email',
           'contact' => 'required|string|min:10',
           'commune' => 'required|string|max:255',
           'cas_urgence' => 'required|string|max:255',
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
            'cas_urgence.required' => 'La personne à contacter est obligatoire.',
            'profile_picture.image' => 'Le fichier doit être une image.',
            'profile_picture.mimes' => 'L\'image doit être au format jpeg, png, jpg, gif ou svg.',
            'profile_picture.max' => 'L\'image ne doit pas dépasser 2048 KB.',
       
       ]);
   
       try {
           // Récupérer le mairie connecté
           $poste = Auth::guard('poste')->user();
   
           if (!$poste || !$poste->name) {
               return redirect()->back()->withErrors(['error' => 'Impossible de récupérer les informations du poste.']);
           }
   
           // Création du docteur
           $livreur = new Livreur();
           $livreur->name = $request->name;
           $livreur->prenom = $request->prenom;
           $livreur->email = $request->email;
           $livreur->contact = $request->contact;
           $livreur->cas_urgence = $request->cas_urgence;
           $livreur->password = Hash::make('default');
           
           if ($request->hasFile('profile_picture')) {
               $livreur->profile_picture = $request->file('profile_picture')->store('profile_pictures', 'public');
           }
   
           $livreur->commune = $request->commune;
           $livreur->communeM = $poste->communeM;
           $livreur->poste_id = $poste->id;
           
           $livreur->save();
   
           // Envoi de l'e-mail de vérification
           ResetCodePasswordLivreur::where('email', $livreur->email)->delete();
           $code1 = rand(1000, 4000);
           $code = $code1.''.$livreur->id;
   
           ResetCodePasswordLivreur::create([
               'code' => $code,
               'email' => $livreur->email,
           ]);
   
           Notification::route('mail', $livreur->email)
               ->notify(new SendEmailToLivreurAfterRegistrationNotification($code, $livreur->email));
   
           return redirect()->route('delivery.index')->with('success', 'Le livreur a bien été enregistré avec succès.');
       } catch (\Exception $e) {
           return redirect()->back()->withErrors(['error' => 'Une erreur est survenue : ' . $e->getMessage()]);
       }
    }
}
