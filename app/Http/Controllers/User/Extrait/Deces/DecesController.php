<?php

namespace App\Http\Controllers\User\Extrait\Deces;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\MaintenanceSetting;
use App\Notifications\DemandeDecesConfirmationNotification;
use App\Services\YellikaSmsService;
use App\Traits\HandlesFreeRequests;
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
    use HandlesFreeRequests;
    public function index()
    {
        // Récupérer les utilisateurs connecté
        $user = Auth::user();

        $deces = Deces::where('user_id', $user->id)->paye()->where('etat', '!=', 'terminé')->paginate(10);

        return view('user.deces.index', compact('deces'));
    }

    public function create()
    {
        $user = Auth::user();
        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining = $this->getRemainingFreeRequests($user);

        return view('user.deces.simple.create', [
            'user' => $user,
            'freeRequestsModeActive' => $freeRequestsModeActive,
            'freeRequestsRemaining' => $freeRequestsRemaining,
        ]);
    }

    public function store(Request $request, YellikaSmsService $yellikaSmsService, \App\Services\WaveService $waveService)
    {
        $request->validate([
            'type' => 'required',
            'name' => 'required',
            'numberR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',
            'dateR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',
            'nom_prenoms_pere' => 'nullable|string|max:255',
            'nom_prenoms_mere' => 'nullable|string|max:255',
            'CNIdfnt' => 'required',
            'qty_simple' => 'nullable|integer|min:0|max:10',
            'qty_integral' => 'nullable|integer|min:0|max:10',
            'CNIdcl' => 'required',
            'commune' => 'required',
            'commune_deces' => 'required|string|max:255',
        ], [
            'name.required' => 'Le nom du défunt est obligatoire.',
            'numberR.required_without_all' => 'Le numéro de registre est obligatoire si les informations parentales ne sont pas fournies.',
            'dateR.required_without_all' => 'La date de registre est obligatoire si les informations parentales ne sont pas fournies.',
            'CNIdfnt.required' => 'Cet document est obligatoire.',
            'CNIdcl.required' => 'Cet document est obligatoire.',
            'qty_simple.integer' => 'La quantité doit être un nombre entier',
            'qty_integral.integer' => 'La quantité intégrale doit être un nombre entier',
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
        $communeInitiale = strtoupper(substr($request->commune ?: $user->commune ?: 'X', 0, 1)); // 'X' si commune est null ou vide
        $anneeCourante = Carbon::now()->year;
        $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $increment = Deces::getNextId();
        $reference = 'AD' . $randomDigits . $increment . $communeInitiale . $anneeCourante; // AD pour Acte de Decès

        // Récupérer les quantités
        $qtySimple = (int) $request->input('qty_simple', 0);
        $qtyIntegral = (int) $request->input('qty_integral', 0);
        if ($qtySimple === 0 && $qtyIntegral === 0) {
            $qtySimple = 1;
        }
        $totalQuantity = $qtySimple + $qtyIntegral;

        // Enregistrement de l'objet deces
        $deces = new Deces();
        $deces->type = $request->type;
        $deces->name = $request->name;
        $deces->nom_prenoms_pere = $request->nom_prenoms_pere;
        $deces->nom_prenoms_mere = $request->nom_prenoms_mere;
        $deces->numberR = $request->numberR;
        $deces->dateR = $request->dateR;
        $deces->CNIdfnt = $uploadedPaths['CNIdfnt'] ?? null;
        $deces->CNIdcl = $uploadedPaths['CNIdcl'] ?? null;
        $deces->documentMariage = $uploadedPaths['documentMariage'] ?? null;
        $deces->RequisPolice = $uploadedPaths['RequisPolice'] ?? null;
        $deces->choix_option = $request->choix_option;
        $deces->qty_simple = $qtySimple;
        $deces->qty_integral = $qtyIntegral;
        $deces->quantite = $totalQuantity;
        $deces->commune = $request->commune ?: $user->commune; // Déterminer la commune
        $deces->commune_deces = $request->commune_deces;
        $deces->etat = 'non_paye';
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
            $deces->date_livraison = $request->input('date_livraison');
            $deces->heure_livraison = $request->input('heure_livraison');
        }

        $deces->save();

        // === GESTION DES DEMANDES GRATUITES (MODE TEST) ===
        $user->refresh();
        $freeCalc = $this->calculateFreeRequestsDiscount($user, $totalQuantity);

        Log::info("Demandes gratuites - Deces {$deces->reference}: {$freeCalc['free_timbres']} timbres gratuits, {$freeCalc['paid_timbres']} timbres payants");

        if ($request->input('choix_option') === 'livraison') {
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];
            $montantLivraison = (float) $deces->montant_livraison;
            $totalAmount = $montantTimbreTotal + $montantLivraison;

            $deces->montant_timbre = $montantTimbreTotal;
            $deces->is_free_request = $freeCalc['free_timbres'] > 0;
            $deces->free_timbres_count = $freeCalc['free_timbres'];
            $deces->save();

            if ($totalAmount > 0) {
                $paymentMethod = $request->input('payment_method', 'wave');

                // Préparer les URLs de retour
                $baseUrl = config('app.url');
                $successUrl = $baseUrl . '/user/payment/success?reference=' . urlencode($deces->reference) . "&type=deces";
                $errorUrl = $baseUrl . '/user/payment/cancel?reference=' . urlencode($deces->reference) . "&type=deces";

                if (strtolower($paymentMethod) === 'wave') {
                    // Créer une session de paiement Wave
                    $checkoutSession = $waveService->createCheckoutSession(
                        $totalAmount,
                        'XOF',
                        $successUrl,
                        $errorUrl,
                        $deces->reference
                    );

                    if ($checkoutSession && isset($checkoutSession['wave_launch_url'])) {
                        return redirect($checkoutSession['wave_launch_url']);
                    }

                    Log::error('Échec de la création de la session Wave pour ' . $deces->reference);
                    return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur lors de la préparation du paiement Wave. Veuillez réessayer.');
                } elseif (strtolower($paymentMethod) === 'mtn') {
                    $mtnPhoneNumber = $request->input('mtn_number');
                    // Format number to international format (starting with 225)
                    $mtnPhoneNumber = preg_replace('/[^0-9]/', '', $mtnPhoneNumber);
                    if (!str_starts_with($mtnPhoneNumber, '225') && strlen($mtnPhoneNumber) == 10) {
                        $mtnPhoneNumber = '225' . $mtnPhoneNumber;
                    }

                    $mtnService = new \App\Services\MtnService();
                    $response = $mtnService->requestToPay(
                        $totalAmount,
                        $mtnPhoneNumber,
                        $deces->reference,
                        'Extrait Deces',
                        'Mairie Plateau'
                    );

                    if ($response && $response['status'] === 'PENDING') {
                        // Stocker le ReferenceId en session pour la vérification
                        session(['mtn_ref_' . $deces->reference => $response['referenceId']]);

                        return redirect()->route('user.payment.mtn.waiting', [
                            'reference' => $deces->reference,
                            'type' => 'deces'
                        ]);
                    }

                    Log::error('Échec de la création de la session MTN pour ' . $deces->reference);
                    return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur lors de la préparation du paiement MTN. Veuillez réessayer.');
                } else {
                    // Générer la session CinetPay
                    $channels = 'ALL';
                    if (in_array(strtolower($paymentMethod), ['orange', 'mtn', 'moov'])) {
                        $channels = 'MOBILE_MONEY';
                    }

                    $cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548');
                    $cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');

                    try {
                        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', [
                            'apikey' => $cinetpayApiKey,
                            'site_id' => $cinetpaySiteId,
                            'transaction_id' => $deces->reference,
                            'amount' => $totalAmount,
                            'currency' => 'XOF',
                            'description' => "Paiement pour " . $deces->reference,
                            'return_url' => $successUrl,
                            'notify_url' => $baseUrl . '/api/webhook/cinetpay',
                            'channels' => $channels,
                        ]);

                        if ($response->successful()) {
                            $data = $response->json();
                            if (isset($data['data']['payment_url'])) {
                                return redirect($data['data']['payment_url']);
                            }
                        }

                        Log::error('Échec CinetPay: ' . $response->body());
                        return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur de génération du lien CinetPay.');

                    } catch (\Exception $e) {
                        Log::error('Erreur Exception CinetPay: ' . $e->getMessage());
                        return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur interne de paiement.');
                    }
                }
            } else {
                // Livraison totalement gratuite (timbres + livraison = 0)
                $deces->etat = 'en attente';
                $deces->save();
                if ($freeCalc['free_timbres'] > 0) {
                    $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                }
            }
        } else {
            // Retrait sur place
            $deces->etat = 'en attente';
            $deces->montant_timbre = $freeCalc['montant_timbre_total'];
            if ($freeCalc['free_timbres'] > 0) {
                $deces->is_free_request = true;
                $deces->free_timbres_count = $freeCalc['free_timbres'];
                $deces->save();
                $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
            } else {
                $deces->save();
            }
        }

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
                    $rules['CNIdfnt'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:25600';
                    break;
                case 'CNIdcl':
                    $rules['CNIdcl'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:25600';
                    break;
                case 'documentMariage':
                    $rules['documentMariage'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:25600';
                    break;
                case 'RequisPolice':
                    $rules['RequisPolice'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:25600';
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
