<?php

namespace App\Http\Controllers\User\Extrait\Mariage;

use App\Http\Controllers\Controller;
use App\Http\Requests\saveMariageRequest;
use App\Models\Mariage;
use App\Notifications\DemandeMariageConfirmationNotification;
use App\Services\YellikaSmsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
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
        $mariages = $query->where('etat', '!=', 'terminé')->get();

        // Fusionner les deux collections en une seule
        $allMariages = $mariages;

        // Retourner la vue avec les mariages fusionnés et les alertes
        return view('user.mariage.index', compact('allMariages'));
    }

    public function create()
    {
        return view('user.mariage.create');
    }

    public function store(saveMariageRequest $request, YellikaSmsService $yellikaSmsService)
    {
        $filesToUpload = [
            'pieceIdentite' => 'identite',
            'extraitMariage' => 'extrait',
            'quantite' => 'required|integer|min:1|max:10',
        ];

        $uploadedPaths = [];

        foreach ($filesToUpload as $fileKey => $subDir) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;

                // Stockage avec le disque 'public' explicitement
                $path = $file->storeAs("images/mariages/$subDir", $newFileName, 'public');

                // Chemin pour la base de données
                $uploadedPaths[$fileKey] = "images/mariages/$subDir/$newFileName";
            }
        }

        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Récupérer la commune du formulaire ou par défaut celle de l'utilisateur
        $commune = $request->input('commune', $user->commune);

        // Générer la référence ici dans le contrôleur
        $communeInitiale = strtoupper(substr($commune ?: 'X', 0, 1)); // 'X' si commune est null ou vide
        $anneeCourante = Carbon::now()->year;
        $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $increment = Mariage::getNextId();
        $reference = 'AM' . $randomDigits . $increment . $communeInitiale . $anneeCourante; // AM pour Acte de Mariage


        // Enregistrement de l'objet Mariage
        $mariage = new Mariage();
        $mariage->nomEpoux = $request->nomEpoux;
        $mariage->prenomEpoux = $request->prenomEpoux;
        $mariage->dateNaissanceEpoux = $request->dateNaissanceEpoux;
        $mariage->lieuNaissanceEpoux = $request->lieuNaissanceEpoux;
        $mariage->quantite = $request->quantite;
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
            $mariage->date_livraison = $request->input('date_livraison');
            $mariage->heure_livraison = $request->input('heure_livraison');
        }

        $mariage->save();
        $phoneNumber = $user->indicatif . $user->contact;
        $message = "Bonjour {$user->name}, votre demande d'extrait de mariage a bien été transmise à la mairie du plateau. Référence: {$mariage->reference}.
Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com/home/search";
        $yellikaSmsService->sendSms($phoneNumber, $message);

        // Envoi de l'email de confirmation
        Notification::send($user, new DemandeMariageConfirmationNotification($user, $mariage));

        return redirect()->route('user.extrait.mariage.index')->with('success', 'Votre demande a été traitée avec succès.');
    }

    /**
     * Modifier une demande rejetée
     */
    public function modifierDemande(Request $request, $id)
    {
        $demande = Mariage::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('peut_modifier', true)
            ->firstOrFail();

        // Valider uniquement les champs qui étaient à modifier
        $champsAModifier = json_decode($demande->champs_a_modifier, true);
        $rules = [];

        // Définir les règles de validation par champ
        foreach ($champsAModifier as $champ) {
            switch ($champ) {
                case 'typeDemande':
                    $rules['typeDemande'] = 'required|string|in:extraitSimple,copieIntegrale';
                    break;
                case 'nomEpoux':
                    $rules['nomEpoux'] = 'required|string|max:255';
                    break;
                case 'prenomEpoux':
                    $rules['prenomEpoux'] = 'required|string|max:255';
                    break;
                case 'dateNaissanceEpoux':
                    $rules['dateNaissanceEpoux'] = 'required|date';
                    break;
                case 'lieuNaissanceEpoux':
                    $rules['lieuNaissanceEpoux'] = 'required|string|max:255';
                    break;
                case 'commune':
                    $rules['commune'] = 'required|string';
                    break;
                case 'quantite':
                    $rules['quantite'] = 'required|integer|min:1|max:10';
                    break;
                case 'pieceIdentite':
                    $rules['pieceIdentite'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:1024';
                    break;
                case 'extraitMariage':
                    $rules['extraitMariage'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:1024';
                    break;
                case 'CMU':
                    $rules['CMU'] = 'required|string|max:50';
                    break;
            }
        }

        $validated = $request->validate($rules);

        // Mettre à jour uniquement les champs spécifiés
        foreach ($champsAModifier as $champ) {
            if (in_array($champ, ['pieceIdentite', 'extraitMariage']) && $request->hasFile($champ)) {
                // Supprimer l'ancien fichier si existe
                if ($demande->$champ) {
                    Storage::delete($demande->$champ);
                }

                // Enregistrer le nouveau fichier
                $file = $request->file($champ);

                // Déterminer le sous-dossier
                $subDir = match ($champ) {
                    'pieceIdentite' => 'pieces-identite',
                    'extraitMariage' => 'extraits-mariage',
                    default => ''
                };

                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $path = $file->storeAs("images/mariage/$subDir", $newFileName, 'public');

                $demande->$champ = "images/mariage/$subDir/$newFileName";
            } elseif (isset($validated[$champ])) {
                $demande->$champ = $validated[$champ];
            }
        }

        // Réinitialiser l'état et désactiver la modification
        $demande->etat = 'en attente';
        $demande->peut_modifier = false;
        $demande->champs_a_modifier = null;
        $demande->motif_de_rejet = null;
        $demande->save();

        return response()->json([
            'success' => true,
            'message' => 'Demande modifiée avec succès et soumise à nouveau.',
            'demande' => $demande
        ]);
    }

    public function delete(Mariage $mariage)
    {
        try {
            $mariage->delete();
            return redirect()->route('user.extrait.mariage.index')->with('success', 'La demande a été supprimée avec succès.');
        } catch (Exception $e) {
            // Log l'erreur pour le débogage
            Log::error('Erreur lors de la suppression de la demande : ' . $e->getMessage());
            // Rediriger avec un message d'erreur
            return redirect()->route('user.extrait.mariage.index')->with('error1', 'Une erreur est survenue lors de la suppression de la demande.');
        }
    }
}
