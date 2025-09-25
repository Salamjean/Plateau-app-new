<?php

namespace App\Http\Controllers\User\Extrait\Mariage;

use App\Http\Controllers\Controller;
use App\Http\Requests\saveMariageRequest;
use App\Models\Mariage;
use App\Services\InfobipService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MariageController extends Controller
{
    public function index(Request $request)
    {
        // Récupérer l'admin connecté
        $user = Auth::user();

        // Initialiser la requête pour Mariage et filtrer par commune de l'user
        $query = Mariage::where('user_id', $user->id); // Filtrer par commune de l'user connecté

        // Vérifier le type de recherche et appliquer le filtre
        if ($request->filled('searchType') && $request->filled('searchInput')) {
            if ($request->searchType === 'nomConjoint') {
                $query->where('nomEpoux', 'like', '%' . $request->searchInput . '%')
                    ->orWhere('nomEpouse', 'like', '%' . $request->searchInput . '%'); 
            } elseif ($request->searchType === 'prenomConjoint') {
                $query->where('prenomEpoux', 'like', '%' . $request->searchInput . '%')
                    ->orWhere('prenomEpouse', 'like', '%' . $request->searchInput . '%'); 
            } elseif ($request->searchType === 'lieuNaissance') {
                $query->where('lieuNaissanceEpoux', 'like', '%' . $request->searchInput . '%')
                    ->orWhere('lieuNaissanceEpouse', 'like', '%' . $request->searchInput . '%'); 
            }
        }

        // Récupérer tous les mariages correspondant aux critères de filtrage
        $mariages = $query->where('etat','!=','terminé')->get();

        // Fusionner les deux collections en une seule
        $allMariages = $mariages;

        // Retourner la vue avec les mariages fusionnés et les alertes
        return view('user.mariage.index', compact('allMariages'));
    }

    public function create(){
        return view('user.mariage.create');
    }

    public function store(saveMariageRequest $request, InfobipService $infobipService)
    {
        $imageBaseLink = '/images/mariages/';

        // Liste des fichiers à traiter
        $filesToUpload = [
            'pieceIdentite' => 'identite/',
            'extraitMariage' => 'extrait/', // Si ce fichier est présent
        ];

        $uploadedPaths = []; // Contiendra les chemins des fichiers uploadés

        foreach ($filesToUpload as $fileKey => $subDir) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $file->storeAs("public/images/mariages/$subDir", $newFileName);

                // Ajouter le chemin public à $uploadedPaths
                $uploadedPaths[$fileKey] = $imageBaseLink . "$subDir" . $newFileName;
            }
        }

        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Récupérer la commune du formulaire ou par défaut celle de l'utilisateur
        $commune = $request->input('commune', $user->commune);

        // Générer la référence ici dans le contrôleur
        $communeInitiale = strtoupper(substr($commune ?: 'X', 0, 1)); // 'X' si commune est null ou vide
        $anneeCourante = Carbon::now()->year;
        $reference = 'AM' . str_pad(Mariage::getNextId(), 4, '0', STR_PAD_LEFT) . $communeInitiale . $anneeCourante; // AM pour Acte de Mariage


        // Enregistrement de l'objet Mariage
        $mariage = new Mariage();
        $mariage->nomEpoux = $request->nomEpoux;
        $mariage->prenomEpoux = $request->prenomEpoux;
        $mariage->dateNaissanceEpoux = $request->dateNaissanceEpoux;
        $mariage->lieuNaissanceEpoux = $request->lieuNaissanceEpoux;
        $mariage->pieceIdentite = $uploadedPaths['pieceIdentite'] ?? null;
        $mariage->extraitMariage = $uploadedPaths['extraitMariage'] ?? null;
        $mariage->commune = $commune; // Utilisation de la commune spécifiée
        $mariage->choix_option = $request->choix_option;
        $mariage->etat = 'en attente';
        $mariage->user_id = $user->id;  // Lier la demande à l'utilisateur connecté
        $mariage->reference = $reference; // Assignez la référence générée


        if ($request->input('choix_option') === 'livraison') {
            $mariage->montant_timbre = $request->input('montant_timbre');
            $mariage->montant_livraison = $request->input('montant_livraison');
            $mariage->nom_destinataire = $request->input('nom_destinataire');
            $mariage->prenom_destinataire = $request->input('prenom_destinataire');
            $mariage->email_destinataire = $request->input('email_destinataire');
            $mariage->contact_destinataire = $request->input('contact_destinataire');
            $mariage->adresse_livraison = $request->input('adresse_livraison');
            $mariage->code_postal = $request->input('code_postal');
            $mariage->ville = $request->input('ville');
            $mariage->commune_livraison = $request->input('commune_livraison');
            $mariage->quartier = $request->input('quartier');
        }

        $mariage->save();
        $phoneNumber = $user->indicatif . $user->contact;
        $message = "Bonjour {$user->name}, votre demande d'extrait de mariage a bien été transmise à la mairie de {$user->commune}. Référence: {$mariage->reference}.
Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com/home/search";
        $infobipService->sendSms($phoneNumber, $message);

        return redirect()->route('user.extrait.mariage.index')->with('success', 'Votre demande a été traitée avec succès.');
    }
}
