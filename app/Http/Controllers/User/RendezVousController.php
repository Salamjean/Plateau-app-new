<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Rendezvous;
use App\Models\RendezVousMariage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RendezVousController extends Controller
{
    public function index() {
        // Récupérer les utilisateurs connecté
        $user = Auth::user();
        $rendezvous= Rendezvous::where('user_id', $user->id)->paginate(10);

        return view('user.rendezvous.index', compact('rendezvous'));
    }

    public function create()
    {
        return view('user.rendezvous.create');
    }

    

    public function store(Request $request)
    {
       $validated = $request->validate([
            'nom_epoux' => 'required|string|max:255',
            'prenom_epoux' => 'required|string|max:255',
            'date_naissance_epoux' => 'required|date',
            'lieu_naissance_epoux' => 'required|string|max:255',
            'nom_epouse' => 'required|string|max:255',
            'prenom_epouse' => 'required|string|max:255',
            'date_naissance_epouse' => 'required|date',
            'lieu_naissance_epouse' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'date_mariage_souhaitee' => 'required|date|after:today',
            'heure_souhaitee' => 'required',
            'motif' => 'required|string|max:255',
        ], [
            'required' => 'Le champ :attribute est obligatoire.',
            'string' => 'Le champ :attribute doit être une chaîne de caractères.',
            'max' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
            'date' => 'Le champ :attribute doit être une date valide.',
            'email' => 'Le champ :attribute doit être une adresse email valide.',
            'after' => 'La date de mariage doit être postérieure à aujourd\'hui.',
            
            // Messages spécifiques par champ
            'nom_epoux.required' => 'Le nom de l\'époux est obligatoire.',
            'prenom_epoux.required' => 'Le prénom de l\'époux est obligatoire.',
            'nom_epouse.required' => 'Le nom de l\'épouse est obligatoire.',
            'prenom_epouse.required' => 'Le prénom de l\'épouse est obligatoire.',
            'telephone.required' => 'Un numéro de téléphone est nécessaire pour vous contacter.',
            'email.required' => 'Une adresse email est nécessaire pour la confirmation.',
            'date_mariage_souhaitee.after' => 'La date du mariage doit être dans le futur.',
            'motif.required' => 'Le motif du rendez-vous est obligatoire.',
        ]);

        // Création d'une nouvelle instance de Rendezvous
        $rendezvous = new Rendezvous();
        
        // Attribution des valeurs
        $rendezvous->nom_epoux = $validated['nom_epoux'];
        $rendezvous->prenom_epoux = $validated['prenom_epoux'];
        $rendezvous->date_naissance_epoux = $validated['date_naissance_epoux'];
        $rendezvous->lieu_naissance_epoux = $validated['lieu_naissance_epoux'];
        $rendezvous->nom_epouse = $validated['nom_epouse'];
        $rendezvous->prenom_epouse = $validated['prenom_epouse'];
        $rendezvous->date_naissance_epouse = $validated['date_naissance_epouse'];
        $rendezvous->lieu_naissance_epouse = $validated['lieu_naissance_epouse'];
        $rendezvous->adresse = $validated['adresse'];
        $rendezvous->telephone = $validated['telephone'];
        $rendezvous->email = $validated['email'];
        $rendezvous->date_mariage_souhaitee = $validated['date_mariage_souhaitee'];
        $rendezvous->heure_souhaitee = $validated['heure_souhaitee'];
        $rendezvous->motif = $validated['motif'];
        $rendezvous->mairie = 'plateau';
        
        // Champs supplémentaires
        $rendezvous->user_id = Auth::user()->id;
        $rendezvous->statut = 'en attente'; // Valeur par défaut
        
        // Sauvegarde dans la base de données
        $rendezvous->save();

        return redirect()->route('user.rendezvous.index')
                        ->with('success', 'Votre demande de rendez-vous a été soumise avec succès.');
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $rendezvous = Rendezvous::findOrFail($id);

            // Vérifier que le rendez-vous appartient bien à l'utilisateur
            if ($rendezvous->user_id !== $user->id) {
                return redirect()->route('user.rendezvous.index')
                                 ->with('error', 'Vous n\'êtes pas autorisé à supprimer ce rendez-vous.');
            }

            // Optionnel : on ne supprime que si c'est en attente
            if ($rendezvous->statut !== 'en attente') {
                return redirect()->route('user.rendezvous.index')
                                 ->with('error', 'Vous ne pouvez plus supprimer ce rendez-vous car il a déjà été traité.');
            }

            $rendezvous->delete();

            return redirect()->route('user.rendezvous.index')
                            ->with('success', 'Votre rendez-vous a été supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du rendez-vous: ' . $e->getMessage());
            return redirect()->route('user.rendezvous.index')
                            ->with('error', 'Une erreur est survenue lors de la suppression du rendez-vous.');
        }
    }
}

