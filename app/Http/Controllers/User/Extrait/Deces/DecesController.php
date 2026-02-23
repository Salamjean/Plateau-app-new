<?php

namespace App\Http\Controllers\User\Extrait\Deces;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Notifications\DemandeDecesConfirmationNotification;
use App\Services\YellikaSmsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DecesController extends Controller
{
    public function index()
    {
        // Récupérer les utilisateurs connecté
        $user = Auth::user();

        $deces = Deces::where('user_id', $user->id)->where('etat', '!=', 'terminé')->paginate(10);

        return view('user.deces.index', compact('deces'));
    }

    public function create()
    {
        return view('user.deces.simple.create');
    }

    public function store(Request $request, YellikaSmsService $yellikaSmsService)
    {
        $request->validate([
            'type' => 'required',
            'name' => 'required',
            'numberR' => 'required',
            'dateR' => 'required',
            'CNIdfnt' => 'required',
            'quantite' => 'required|integer|min:1|max:10',
            'CNIdcl' => 'required',
        ], [
            'name.required' => 'Le nom du défunt est obligatoire.',
            'numberR.required' => 'Le numéro de l\'extrait de décès est obligatoire.',
            'dateR.required' => 'La date de l\'extrait de décès est obligatoire.',
            'CNIdfnt.required' => 'Cet document est obligatoire.',
            'CNIdcl.required' => 'Cet document est obligatoire.',
            'quantite.required' => 'La quantité est obligatoire',
            'quantite.integer' => 'La quantité doit être un nombre entier',
            'quantite.min' => 'La quantité doit être au moins de 1',
            'quantite.max' => 'La quantité ne peut pas dépasser 10',
            'documentMariage.required' => 'Cet document de mariage est obligatoire.',
            'RequisPolice.required' => 'Cet document requis de police est obligatoire.',
            'pActe.mimes' => 'Cet document d\'extrait de décès doit être un format de fichier valide (png, jpg, jpeg, pdf).',
            'CNIdfnt.mimes' => 'Cet document doit être un format de fichier valide (png, jpg, jpeg, pdf).',
        ]);

        $filesToUpload = [
            'pActe' => '', // Pas de sous-dossier
            'CNIdfnt' => 'cnid',
            'CNIdcl' => 'cnid',
            'documentMariage' => 'mariage',
            'RequisPolice' => 'police',
        ];

        $uploadedPaths = [];

        foreach ($filesToUpload as $fileKey => $subDir) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;

                // Stockage avec le disque 'public'
                $path = $file->storeAs("images/deces/$subDir", $newFileName, 'public');

                // Même format que naissance/mariage
                $uploadedPaths[$fileKey] = "images/deces/$subDir/$newFileName";
            }
        }

        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Vous devez être connecté pour effectuer cette action.');
        }

        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Générer la référence ici dans le contrôleur
        $communeInitiale = strtoupper(substr($request->communeD ?: $user->commune ?: 'X', 0, 1)); // 'X' si commune est null ou vide (prend communeD du request si existe sinon commune user sinon X)
        $anneeCourante = Carbon::now()->year;
        $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $increment = Deces::getNextId();
        $reference = 'AD' . $randomDigits . $increment . $communeInitiale . $anneeCourante; // AD pour Acte de Decès


        // Enregistrement de l'objet deces
        $deces = new Deces();
        $deces->type = $request->type;
        $deces->name = $request->name;
        $deces->numberR = $request->numberR;
        $deces->dateR = $request->dateR;
        $deces->CNIdfnt = $uploadedPaths['CNIdfnt'] ?? null;
        $deces->CNIdcl = $uploadedPaths['CNIdcl'] ?? null;
        $deces->documentMariage = $uploadedPaths['documentMariage'] ?? null;
        $deces->RequisPolice = $uploadedPaths['RequisPolice'] ?? null;
        $deces->choix_option = $request->choix_option;
        $deces->quantite = $request->quantite;
        $deces->commune = $request->communeD ?: $user->commune; // Déterminer la commune
        $deces->etat = 'en attente';
        $deces->user_id = $user->id; // Lier la demande à l'utilisateur connecté
        $deces->reference = $reference; // Assignez la référence générée

        // Ajout des informations de livraison si option livraison est choisie
        if ($request->input('choix_option') === 'livraison') {
            $deces->montant_timbre = $request->input('montant_timbre');
            $deces->montant_livraison = $request->input('montant_livraison');
            $deces->nom_destinataire = $request->input('nom_destinataire');
            $deces->prenom_destinataire = $request->input('prenom_destinataire');
            $deces->email_destinataire = $request->input('email_destinataire');
            $deces->contact_destinataire = $request->input('contact_destinataire');
            $deces->adresse_livraison = $request->input('adresse_livraison');
            $deces->code_postal = $request->input('code_postal');
            $deces->ville = $request->input('ville');
            $deces->commune_livraison = $request->input('commune_livraison');
            $deces->quartier = $request->input('quartier');
        }

        $deces->save();
        $phoneNumber = $user->indicatif . $user->contact;
        $message = "Bonjour {$user->name}, votre demande d'extrait de décès a bien été transmise à la mairie du plateau. Référence: {$deces->reference}
Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com/home/search";
        $yellikaSmsService->sendSms($phoneNumber, $message);

        // Envoi de l'email de confirmation
        Notification::send($user, new DemandeDecesConfirmationNotification($user, $deces));

        return redirect()->route('user.extrait.deces.index')->with('success', 'Demande envoyée avec succès.');
    }

    /**
     * Modifier une demande rejetée
     */
    public function modifierDemande(Request $request, $id)
    {
        $demande = Deces::where('user_id', Auth::id())
            ->where('id', $id)
            ->where('peut_modifier', true)
            ->firstOrFail();

        // Valider uniquement les champs qui étaient à modifier
        $champsAModifier = json_decode($demande->champs_a_modifier, true);
        $rules = [];

        // Définir les règles de validation par champ
        foreach ($champsAModifier as $champ) {
            switch ($champ) {
                case 'name':
                    $rules['name'] = 'required|string|max:255';
                    break;
                case 'numberR':
                    $rules['numberR'] = 'required|string|max:50';
                    break;
                case 'dateR':
                    $rules['dateR'] = 'required|date';
                    break;
                case 'commune':
                    $rules['commune'] = 'required|string';
                    break;
                case 'quantite':
                    $rules['quantite'] = 'required|integer|min:1|max:10';
                    break;
                case 'CNIdfnt':
                    $rules['CNIdfnt'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:1024';
                    break;
                case 'CNIdcl':
                    $rules['CNIdcl'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:1024';
                    break;
                case 'documentMariage':
                    $rules['documentMariage'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:1024';
                    break;
                case 'RequisPolice':
                    $rules['RequisPolice'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:1024';
                    break;
            }
        }

        $validated = $request->validate($rules);

        // Mettre à jour uniquement les champs spécifiés
        foreach ($champsAModifier as $champ) {
            if (in_array($champ, ['CNIdfnt', 'CNIdcl', 'documentMariage', 'RequisPolice']) && $request->hasFile($champ)) {
                // Supprimer l'ancien fichier si existe
                if ($demande->$champ) {
                    Storage::delete($demande->$champ);
                }

                // Enregistrer le nouveau fichier
                $file = $request->file($champ);

                // Déterminer le sous-dossier
                $subDir = match ($champ) {
                    'CNIdfnt' => 'cnid',
                    'CNIdcl' => 'cnid',
                    'documentMariage' => 'mariage',
                    'RequisPolice' => 'police',
                    default => ''
                };

                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $path = $file->storeAs("images/deces/$subDir", $newFileName, 'public');

                $demande->$champ = "images/deces/$subDir/$newFileName";
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

    public function delete(Deces $dece)
    {
        try {
            $dece->delete();
            return redirect()->route('user.extrait.deces.index')->with('success', 'La demande a été supprimée avec succès.');
        } catch (Exception $e) {
            // Log l'erreur pour le débogage
            Log::error('Erreur lors de la suppression de la demande : ' . $e->getMessage());
            return redirect()->route('user.extrait.deces.index')->with('error', 'Une erreur est survenue lors de la suppression de la demande.');
        }
    }
}
