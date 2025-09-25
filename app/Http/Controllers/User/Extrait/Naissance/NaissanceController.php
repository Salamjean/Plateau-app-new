<?php

namespace App\Http\Controllers\User\Extrait\Naissance;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use App\Services\InfobipService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NaissanceController extends Controller
{
    public function index(){

        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        $naissances = Naissance::where('user_id', $user->id)->where('etat','!=','terminé')->paginate(20);

        return view('user.naissance.index', compact( 'naissances'));
        
    }

    public function create()
    {
        $userConnecté = Auth::user();
        return view('user.naissance.simple.create', [
            'userName' => $userConnecté ? $userConnecté->name : '', 
            'userPrenom' => $userConnecté ? $userConnecté->prenom : '', 
            'userCommune' => $userConnecté ? $userConnecté->commune : '', 
            'userCMU' => $userConnecté ? $userConnecté->CMU : '', 
        ]);
    }

    public function store(Request $request, InfobipService $infobipService)
    {
        $validated = $request->validate([
            'type' => 'required',
            'name' => 'required',
            'prenom' => 'required',
            'number' => 'required',
            'DateR' => 'required',
            'commune' => 'required',
            'CNI' => 'required|mimes:png,jpg,jpeg,pdf|max:1000',
        ],[
            'type.required' => 'le type d\'extrait que vous-voulez demander est obligatoire',
            'name.required' => 'Le nom est obligatoire',
            'prenom.required' => 'Le prénom est obligatoire',
            'number.required' => 'Le numéro de registre sur l\'extrait est obligatoire',
            'DateR.required' => 'La date de registre est obligatoire',
            'commune.required' => 'La commune est obligatoire',
            'CNI.required' => 'Le champ CNI est obligatoire',
            'CNI.mimes' => 'Le format du fichier doit être PNG, JPG, JPEG ou PDF',
            'CNI.max' => 'Le fichier ne doit pas dépasser 1Mo',
        ]);
        // Log des données de la requête
        Log::info('Store method called', $request->all());

        // Configuration des chemins pour le stockage des fichiers
        $imageBaseLink = '/images/naissances/';
        $filesToUpload = [
            'CNI' => 'cni/',
        ];
        $uploadedPaths = [];

        // Traitement des fichiers uploadés
        foreach ($filesToUpload as $fileKey => $subDir) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $file->storeAs("public/images/naissances/$subDir", $newFileName);
                $uploadedPaths[$fileKey] = $imageBaseLink . "$subDir" . $newFileName;
            }
        }

        // Récupération de l'utilisateur connecté
        $user = Auth::user();

        // Génération de la référence
        $communeInitiale = strtoupper(substr($user->commune ?? 'X', 0, 1)); // 'X' si commune est null ou vide
        $anneeCourante = Carbon::now()->year;
        $reference = 'AN' . str_pad(Naissance::getNextId(), 4, '0', STR_PAD_LEFT) . $communeInitiale . $anneeCourante;

        // Création de la demande d'extrait de naissance
        $naissance = new Naissance();
        $naissance->pour = $request->pour;
        $naissance->type = $request->type;
        $naissance->name = $request->name;
        $naissance->prenom = $request->prenom;
        $naissance->number = $request->number;
        $naissance->DateR = $request->DateR;
        $naissance->commune = $request->commune;
        $naissance->CNI = $uploadedPaths['CNI'] ?? null;
        $naissance->choix_option = $request->choix_option;
        $naissance->user_id = $user->id;
        $naissance->etat = 'en attente';
        $naissance->reference = $reference;
       

        // Ajout des informations de livraison si l'option "livraison" est choisie
        if ($request->input('choix_option') === 'livraison') {
            $naissance->montant_timbre = $request->input('montant_timbre');
            $naissance->montant_livraison = $request->input('montant_livraison');
            $naissance->nom_destinataire = $request->input('nom_destinataire');
            $naissance->prenom_destinataire = $request->input('prenom_destinataire');
            $naissance->email_destinataire = $request->input('email_destinataire');
            $naissance->contact_destinataire = $request->input('contact_destinataire');
            $naissance->adresse_livraison = $request->input('adresse_livraison');
            $naissance->code_postal = $request->input('code_postal');
            $naissance->ville = $request->input('ville');
            $naissance->commune_livraison = $request->input('commune_livraison');
            $naissance->quartier = $request->input('quartier');
        }

         $naissance->save();
         $phoneNumber = $user->indicatif . $user->contact;
    Log::info('Numéro de téléphone construit : ' . $phoneNumber);
    $message = "Bonjour {$user->name}, votre demande d'extrait de naissance a bien été transmise à la mairie de {$user->commune}. Référence : {$naissance->reference}.
Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com/home/search";
    $smsResult = $infobipService->sendSms($phoneNumber, $message);

        return redirect()->route('user.extrait.index')->with('success', 'Votre demande a été traitée avec succès.');
    }

//     public function store(Request $request, OrangeSmsService $orangeSmsService)
// {
//     $validated = $request->validate([
//         'type' => 'required',
//         'name' => 'required',
//         'prenom' => 'required',
//         'number' => 'required',
//         'DateR' => 'required',
//         'commune' => 'required',
//         'CNI' => 'required|mimes:png,jpg,jpeg,pdf|max:1000',
//     ],[
//         'type.required' => 'le type d\'extrait que vous-voulez demander est obligatoire',
//         'name.required' => 'Le nom est obligatoire',
//         'prenom.required' => 'Le prénom est obligatoire',
//         'number.required' => 'Le numéro de registre sur l\'extrait est obligatoire',
//         'DateR.required' => 'La date de registre est obligatoire',
//         'commune.required' => 'La commune est obligatoire',
//         'CNI.required' => 'Le champ CNI est obligatoire',
//         'CNI.mimes' => 'Le format du fichier doit être PNG, JPG, JPEG ou PDF',
//         'CNI.max' => 'Le fichier ne doit pas dépasser 1Mo',
//     ]);

//     // Log des données de la requête
//     Log::info('Store method called', $request->all());

//     // Configuration des chemins pour le stockage des fichiers
//     $imageBaseLink = '/images/naissances/';
//     $filesToUpload = [
//         'CNI' => 'cni/',
//     ];
//     $uploadedPaths = [];

//     // Traitement des fichiers uploadés
//     foreach ($filesToUpload as $fileKey => $subDir) {
//         if ($request->hasFile($fileKey)) {
//             $file = $request->file($fileKey);
//             $extension = $file->getClientOriginalExtension();
//             $newFileName = (string) Str::uuid() . '.' . $extension;
//             $file->storeAs("public/images/naissances/$subDir", $newFileName);
//             $uploadedPaths[$fileKey] = $imageBaseLink . "$subDir" . $newFileName;
//         }
//     }

//     // Récupération de l'utilisateur connecté
//     $user = Auth::user();

//     // Génération de la référence
//     $communeInitiale = strtoupper(substr($user->commune ?? 'X', 0, 1));
//     $anneeCourante = Carbon::now()->year;
//     $reference = 'AN' . str_pad(Naissance::getNextId(), 4, '0', STR_PAD_LEFT) . $communeInitiale . $anneeCourante;

//     // Création de la demande d'extrait de naissance
//     $naissance = new Naissance();
//     $naissance->pour = $request->pour;
//     $naissance->type = $request->type;
//     $naissance->name = $request->name;
//     $naissance->prenom = $request->prenom;
//     $naissance->number = $request->number;
//     $naissance->DateR = $request->DateR;
//     $naissance->commune = $request->commune;
//     $naissance->CNI = $uploadedPaths['CNI'] ?? null;
//     $naissance->choix_option = $request->choix_option;
//     $naissance->user_id = $user->id;
//     $naissance->etat = 'en attente';
//     $naissance->reference = $reference;

//     // Ajout des informations de livraison si l'option "livraison" est choisie
//     if ($request->input('choix_option') === 'livraison') {
//         $naissance->montant_timbre = $request->input('montant_timbre');
//         $naissance->montant_livraison = $request->input('montant_livraison');
//         $naissance->nom_destinataire = $request->input('nom_destinataire');
//         $naissance->prenom_destinataire = $request->input('prenom_destinataire');
//         $naissance->email_destinataire = $request->input('email_destinataire');
//         $naissance->contact_destinataire = $request->input('contact_destinataire');
//         $naissance->adresse_livraison = $request->input('adresse_livraison');
//         $naissance->code_postal = $request->input('code_postal');
//         $naissance->ville = $request->input('ville');
//         $naissance->commune_livraison = $request->input('commune_livraison');
//         $naissance->quartier = $request->input('quartier');
//     }

//     $naissance->save();
    
//     // Envoi du SMS avec Orange
//     $phoneNumber = $user->indicatif . $user->contact;
//     Log::info('Numéro de téléphone construit : ' . $phoneNumber);
    
//     $message = "Bonjour {$user->name}, votre demande d'extrait de naissance a bien été transmise à la mairie de {$user->commune}. Référence : {$naissance->reference}.
// Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com/home/search";
    
//     $smsResult = $orangeSmsService->sendSms($phoneNumber, $message);

//     return redirect()->route('user.extrait.index')->with('success', 'Votre demande a été traitée avec succès.');
// }

    public function delete(Naissance $naissance)
    {
        try {
            $naissance->delete();
            return redirect()->route('user.extrait.index')->with('success', 'La demande a été supprimée avec succès.');
        } catch (Exception $e) {
            // Log l'erreur pour le débogage
            Log::error('Erreur lors de la suppression de la demande : ' . $e->getMessage());
            // Rediriger avec un message d'erreur
            return redirect()->route('user.extrait.index')->with('error1', 'Une erreur est survenue lors de la suppression de la demande.');
        }
    }
}
