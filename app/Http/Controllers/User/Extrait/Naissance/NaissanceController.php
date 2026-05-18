<?php

namespace App\Http\Controllers\User\Extrait\Naissance;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use App\Models\MaintenanceSetting;
use App\Notifications\DemandeNaissanceConfirmationNotification;
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

class NaissanceController extends Controller
{
    use HandlesFreeRequests;
    public function index()
    {

        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        $naissances = Naissance::where('user_id', $user->id)->paye()->where('etat', '!=', 'terminé')->paginate(20);

        return view('user.naissance.index', compact('naissances'));
    }

    /**
     * Modifier une demande rejetée
     */
    public function modifierDemande(Request $request, $id)
    {
        $demande = Naissance::where('user_id', Auth::id())
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
                case 'prenom':
                    $rules['prenom'] = 'required|string|max:255';
                    break;
                case 'number':
                    $rules['number'] = 'required|string|max:50';
                    break;
                case 'DateR':
                    $rules['DateR'] = 'required|date';
                    break;
                case 'commune':
                    $rules['commune'] = 'required|string';
                    break;
                case 'type':
                    $rules['type'] = 'required|string|in:simple,extrait_integral';
                    break;
                case 'quantite':
                    $rules['quantite'] = 'required|integer|min:1|max:10';
                    break;
                case 'CNI':
                    $rules['CNI'] = 'required|file|mimes:jpeg,png,jpg,pdf|max:25600';
                    break;
            }
        }

        $validated = $request->validate($rules);

        // Mettre à jour uniquement les champs spécifiés
        foreach ($champsAModifier as $champ) {
            if ($champ === 'CNI' && $request->hasFile('CNI')) {
                // Supprimer l'ancien fichier si existe
                if ($demande->CNI) {
                    Storage::delete($demande->CNI);
                }

                // Enregistrer le nouveau fichier
                $file = $request->file('CNI');
                $path = $file->store('pieces-identite', 'public');
                $demande->CNI = $path;
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

        // Notification à l'admin/agent
        // Vous pouvez ajouter ici une notification ou un email

        return response()->json([
            'success' => true,
            'message' => 'Demande modifiée avec succès et soumise à nouveau.',
            'demande' => $demande
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining = $this->getRemainingFreeRequests($user);

        return view('user.naissance.simple.create', [
            'user' => $user,
            'userName' => $user ? $user->name : '',
            'userPrenom' => $user ? $user->prenom : '',
            'userEmail' => $user ? $user->email : '',
            'userContact' => $user ? $user->contact : '',
            'userCommune' => $user ? $user->commune : '',
            'userCMU' => $user ? $user->CMU : '',
            'freeRequestsModeActive' => $freeRequestsModeActive,
            'freeRequestsRemaining' => $freeRequestsRemaining,
        ]);
    }

    public function store(Request $request, YellikaSmsService $yellikaSmsService, \App\Services\WaveService $waveService)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'type' => 'required',
            'name' => 'required',
            'prenom' => 'required',
            'number' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',
            'DateR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',
            'nom_prenoms_pere' => 'nullable|string|max:255',
            'nom_prenoms_mere' => 'nullable|string|max:255',
            'commune' => 'required',
            'commune_naissance' => 'required|string|max:255',
            'qty_simple' => 'nullable|integer|min:0|max:10',
            'qty_integral' => 'nullable|integer|min:0|max:10',
            'CNI' => 'required',
        ], [
            'type.required' => 'le type d\'extrait que vous-voulez demander est obligatoire',
            'name.required' => 'Le nom est obligatoire',
            'prenom.required' => 'Le prénom est obligatoire',
            'number.required_without_all' => 'Le numéro de registre est obligatoire si les informations parentales ne sont pas fournies',
            'DateR.required_without_all' => 'La date de registre est obligatoire si les informations parentales ne sont pas fournies',
            'commune.required' => 'La commune est obligatoire',
            'commune_naissance.required' => 'La commune de naissance est obligatoire',
            'CNI.required' => 'Le champ CNI est obligatoire',
            'qty_simple.integer' => 'La quantité simple doit être un nombre entier',
            'qty_integral.integer' => 'La quantité intégrale doit être un nombre entier',
            'CNI.mimes' => 'Le format du fichier doit être PNG, JPG, JPEG ou PDF',
            'CNI.max' => 'Le fichier ne doit pas dépasser 1Mo',
        ]);

        if ($validator->fails()) {
            Log::error('Validation échouée pour la demande de naissance : ', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all(),
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        // Log des données de la requête
        Log::info('Store method called', $request->all());

        // Configuration des chemins pour le stockage des fichiers
        $filesToUpload = [
            'CNI' => 'cni/',
        ];
        $uploadedPaths = [];

        foreach ($filesToUpload as $fileKey => $subDir) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;

                // Stockage avec le disque 'public' explicitement
                $file->storeAs("images/naissances/$subDir", $newFileName, 'public');

                // Même format que mariage (sans 'storage/' au début)
                $uploadedPaths[$fileKey] = "images/naissances/$subDir$newFileName";
            }
        }

        // Récupération de l'utilisateur connecté
        $user = Auth::user();

        // Génération de la référence
        $communeInitiale = strtoupper(substr($request->communeD ?: $user->commune ?: 'X', 0, 1));
        $anneeCourante = Carbon::now()->year;
        $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $increment = Naissance::getNextId();
        $reference = 'AN' . $randomDigits . $increment . $communeInitiale . $anneeCourante;

        // Récupération des quantités
        $qtySimple = (int) $request->input('qty_simple', 0);
        $qtyIntegral = (int) $request->input('qty_integral', 0);
        if ($qtySimple === 0 && $qtyIntegral === 0) {
            $type = $request->input('type');
            if ($type === 'extrait_integral') {
                $qtyIntegral = 1;
            } else {
                $qtySimple = 1;
            }
        }
        $totalQuantity = $qtySimple + $qtyIntegral;

        // Création de la demande d'extrait de naissance
        $naissance = new Naissance();
        $naissance->pour = $request->pour;
        $naissance->type = $request->type;
        $naissance->name = $request->name;
        $naissance->qty_simple = $qtySimple;
        $naissance->qty_integral = $qtyIntegral;
        $naissance->quantite = $totalQuantity;
        $naissance->prenom = $request->prenom;
        $naissance->nom_prenoms_pere = $request->nom_prenoms_pere;
        $naissance->nom_prenoms_mere = $request->nom_prenoms_mere;
        $naissance->number = $request->number;
        $naissance->DateR = $request->DateR;
        $naissance->commune = $request->commune;
        $naissance->commune_naissance = $request->commune_naissance;
        $naissance->CNI = $uploadedPaths['CNI'] ?? null;
        $naissance->choix_option = $request->choix_option;
        $naissance->user_id = $user->id;
        $naissance->etat = 'non_paye';
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
            $naissance->date_livraison = $request->input('date_livraison');
            $naissance->heure_livraison = $request->input('heure_livraison');
        }

        $naissance->save();

        // === GESTION DES DEMANDES GRATUITES (MODE TEST) ===
        $user->refresh();
        $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) ($naissance->qty_simple + $naissance->qty_integral));

        if ($request->input('choix_option') === 'livraison') {
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];
            $montantLivraison = (float) $naissance->montant_livraison;
            $totalAmount = $montantTimbreTotal + $montantLivraison;

            $naissance->montant_timbre = $montantTimbreTotal;
            $naissance->is_free_request = $freeCalc['free_timbres'] > 0;
            $naissance->free_timbres_count = $freeCalc['free_timbres'];
            $naissance->save();

            Log::info("Demandes gratuites - Naissance {$naissance->reference}: {$freeCalc['free_timbres']} timbres gratuits, {$freeCalc['paid_timbres']} timbres payants");

            if ($totalAmount > 0) {
                // Paiement requis → le compteur sera incrémenté APRÈS confirmation du paiement
                $paymentMethod = $request->input('payment_method', 'wave');

                // Préparer les URLs de retour
                $baseUrl = config('app.url');
                $successUrl = $baseUrl . '/user/payment/success?reference=' . urlencode($naissance->reference) . "&type=naissance";
                $errorUrl = $baseUrl . '/user/payment/cancel?reference=' . urlencode($naissance->reference) . "&type=naissance";

                if (strtolower($paymentMethod) === 'wave') {
                    // Créer une session de paiement Wave
                    $checkoutSession = $waveService->createCheckoutSession(
                        $totalAmount,
                        'XOF',
                        $successUrl,
                        $errorUrl,
                        $naissance->reference
                    );

                    if ($checkoutSession && isset($checkoutSession['wave_launch_url'])) {
                        return redirect($checkoutSession['wave_launch_url']);
                    }

                    Log::error('Échec de la création de la session Wave pour ' . $naissance->reference);
                    return redirect()->route('user.extrait.index')->with('error', 'Erreur lors de la préparation du paiement Wave. Veuillez réessayer.');
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
                        $naissance->reference,
                        'Extrait Naissance',
                        'Mairie Plateau'
                    );

                    if ($response && $response['status'] === 'PENDING') {
                        // Stocker le ReferenceId en session pour la vérification
                        session(['mtn_ref_' . $naissance->reference => $response['referenceId']]);

                        return redirect()->route('user.payment.mtn.waiting', [
                            'reference' => $naissance->reference,
                            'type' => 'naissance'
                        ]);
                    }

                    Log::error('Échec de la création de la session MTN pour ' . $naissance->reference);
                    return redirect()->route('user.extrait.index')->with('error', 'Erreur lors de la préparation du paiement MTN. Veuillez réessayer.');
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
                            'transaction_id' => $naissance->reference,
                            'amount' => $totalAmount,
                            'currency' => 'XOF',
                            'description' => "Paiement pour " . $naissance->reference,
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
                        return redirect()->route('user.extrait.index')->with('error', 'Erreur de génération du lien CinetPay.');

                    } catch (\Exception $e) {
                        Log::error('Erreur Exception CinetPay: ' . $e->getMessage());
                        return redirect()->route('user.extrait.index')->with('error', 'Erreur interne de paiement.');
                    }
                }
            } else {
                // Livraison entièrement gratuite (0 à payer) → incrémenter maintenant
                $naissance->etat = 'en attente';
                $naissance->save();
                if ($freeCalc['free_timbres'] > 0) {
                    $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                }
            }
        } else {
            // Retrait sur place (pas de paiement en ligne) → incrémenter maintenant
            $naissance->etat = 'en attente';
            $naissance->montant_timbre = $freeCalc['montant_timbre_total'];
            if ($freeCalc['free_timbres'] > 0) {
                $naissance->is_free_request = true;
                $naissance->free_timbres_count = $freeCalc['free_timbres'];
                $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
            }
            $naissance->save();
        }

        $phoneNumber = $user->indicatif . $user->contact;
        Log::info('Numéro de téléphone construit : ' . $phoneNumber);
        $message = "Bonjour {$user->name}, votre demande d'extrait de naissance a bien été transmise à la mairie du plateau. Référence : {$naissance->reference}.
Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com/home/search";
        $smsResult = $yellikaSmsService->sendSms($phoneNumber, $message);

        // Envoi de l'email de confirmation
        Notification::send($user, new DemandeNaissanceConfirmationNotification($user, $naissance));

        return redirect()->route('user.extrait.index')->with('success', 'Votre demande a été traitée avec succès.');
    }

    public function delete(Naissance $naissance)
    {
        try {
            $naissance->delete();
            return redirect()->route('user.extrait.index')->with('success', 'La demande a été supprimée avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur lors de la suppression de la demande : ' . $e->getMessage());
            return redirect()->route('user.extrait.index')->with('error1', 'Une erreur est survenue lors de la suppression de la demande.');
        }
    }
}
