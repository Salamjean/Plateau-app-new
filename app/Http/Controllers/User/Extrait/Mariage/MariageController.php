<?php

namespace App\Http\Controllers\User\Extrait\Mariage;

use App\Http\Controllers\Controller;
use App\Http\Requests\saveMariageRequest;
use App\Models\Mariage;
use App\Models\MaintenanceSetting;
use App\Notifications\DemandeMariageConfirmationNotification;
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

class MariageController extends Controller
{
    use HandlesFreeRequests;
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
        $mariages = $query->paye()->where('etat', '!=', 'terminé')->get();

        // Fusionner les deux collections en une seule
        $allMariages = $mariages;

        // Retourner la vue avec les mariages fusionnés et les alertes
        return view('user.mariage.index', compact('allMariages'));
    }

    public function create()
    {
        $user = Auth::user();
        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining = $this->getRemainingFreeRequests($user);

        return view('user.mariage.create', [
            'user' => $user,
            'freeRequestsModeActive' => $freeRequestsModeActive,
            'freeRequestsRemaining' => $freeRequestsRemaining,
        ]);
    }

    public function store(saveMariageRequest $request, YellikaSmsService $yellikaSmsService, \App\Services\WaveService $waveService)
    {
        // Validation IA Gemini de la pièce d'identité CNI
        if ($request->hasFile('pieceIdentite')) {
            $geminiService = app(\App\Services\GeminiValidationService::class);
            $validation = $geminiService->validateIdentityDocument($request->file('pieceIdentite'));
            if (!$validation['isValid']) {
                return redirect()->back()
                    ->withErrors(['pieceIdentite' => "La pièce d'identité a été rejetée par l'IA de la mairie : " . $validation['reason']])
                    ->withInput();
            }
        }

        $filesToUpload = [
            'pieceIdentite' => 'identite',
            'extraitMariage' => 'extrait',
            'document_autorisation' => 'autorisations',
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

        // Récupérer les quantités simple et intégrale selon le type de demande
        $qtySimple = (int) $request->input('qty_simple', 0);
        $qtyIntegral = (int) $request->input('qty_integral', 0);

        if ($request->typeDemande === 'simple') {
            $qtyIntegral = 0;
            if ($qtySimple <= 0) {
                $qtySimple = 1;
            }
        } elseif ($request->typeDemande === 'integrale') {
            $qtySimple = 0;
            if ($qtyIntegral <= 0) {
                $qtyIntegral = 1;
            }
        } else {
            // groupee (simple + integrale)
            if ($qtySimple <= 0) {
                $qtySimple = 1;
            }
            if ($qtyIntegral <= 0) {
                $qtyIntegral = 1;
            }
        }
        $totalQuantity = $qtySimple + $qtyIntegral;

        // Enregistrement de l'objet Mariage
        $mariage = new Mariage();
        $mariage->type = $request->typeDemande;
        $mariage->pour = $request->pour;
        $mariage->relation = $request->relation;
        $mariage->document_autorisation = $uploadedPaths['document_autorisation'] ?? null;
        $mariage->nomEpoux = $request->nomEpoux;
        $mariage->prenomEpoux = $request->prenomEpoux;
        $mariage->dateNaissanceEpoux = $request->dateNaissanceEpoux;
        $mariage->lieuNaissanceEpoux = $request->lieuNaissanceEpoux;
        $mariage->nomEpouse = $request->nomEpouse;
        $mariage->prenomEpouse = $request->prenomEpouse;
        $mariage->dateNaissanceEpouse = $request->dateNaissanceEpouse;
        $mariage->lieuNaissanceEpouse = $request->lieuNaissanceEpouse;
        $mariage->qty_simple = $qtySimple;
        $mariage->qty_integral = $qtyIntegral;
        $mariage->quantite = $totalQuantity;
        $mariage->pieceIdentite = $uploadedPaths['pieceIdentite'] ?? null;
        $mariage->extraitMariage = $uploadedPaths['extraitMariage'] ?? null;
        $mariage->commune = $commune; // Utilisation de la commune spécifiée
        $mariage->commune_mariage = $request->commune_mariage;
        $mariage->choix_option = $request->choix_option;
        $mariage->etat = 'non_paye';
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

        // === GESTION DES DEMANDES GRATUITES (MODE TEST) ===
        $user->refresh();
        $freeCalc = $this->calculateFreeRequestsDiscount($user, $totalQuantity);

        if ($request->input('choix_option') === 'livraison') {
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];
            $montantLivraison = (float) $mariage->montant_livraison;
            $totalAmount = $montantTimbreTotal + $montantLivraison;

            $mariage->montant_timbre = $montantTimbreTotal;
            $mariage->is_free_request = $freeCalc['free_timbres'] > 0;
            $mariage->free_timbres_count = $freeCalc['free_timbres'];
            $mariage->save();

            Log::info("Demandes gratuites - Mariage {$mariage->reference}: {$freeCalc['free_timbres']} timbres gratuits, {$freeCalc['paid_timbres']} timbres payants");

            if ($totalAmount > 0) {
                // Paiement requis → le compteur sera incrémenté APRÈS confirmation du paiement
                $paymentMethod = $request->input('payment_method', 'wave');

                $baseUrl = config('app.url');
                $successUrl = $baseUrl . '/user/payment/success?reference=' . urlencode($mariage->reference) . "&type=mariage";
                $errorUrl = $baseUrl . '/user/payment/cancel?reference=' . urlencode($mariage->reference) . "&type=mariage";

                if (strtolower($paymentMethod) === 'wave') {
                    $checkoutSession = $waveService->createCheckoutSession(
                        $totalAmount,
                        'XOF',
                        $successUrl,
                        $errorUrl,
                        $mariage->reference
                    );

                    if ($checkoutSession && isset($checkoutSession['wave_launch_url'])) {
                        return redirect($checkoutSession['wave_launch_url']);
                    }

                    Log::error('Échec de la création de la session Wave pour ' . $mariage->reference);
                    return redirect()->route('user.extrait.mariage.index')->with('error', 'Erreur lors de la préparation du paiement Wave. Veuillez réessayer.');
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
                        $mariage->reference,
                        'Extrait Mariage',
                        'Mairie Plateau'
                    );

                    if ($response && $response['status'] === 'PENDING') {
                        // Stocker le ReferenceId en session pour la vérification
                        session(['mtn_ref_' . $mariage->reference => $response['referenceId']]);

                        return redirect()->route('user.payment.mtn.waiting', [
                            'reference' => $mariage->reference,
                            'type' => 'mariage'
                        ]);
                    }

                    Log::error('Échec de la création de la session MTN pour ' . $mariage->reference);
                    return redirect()->route('user.extrait.mariage.index')->with('error', 'Erreur lors de la préparation du paiement MTN. Veuillez réessayer.');
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
                            'transaction_id' => $mariage->reference,
                            'amount' => $totalAmount,
                            'currency' => 'XOF',
                            'description' => "Paiement pour " . $mariage->reference,
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
                        return redirect()->route('user.extrait.mariage.index')->with('error', 'Erreur de génération du lien CinetPay.');
                    } catch (\Exception $e) {
                        Log::error('Erreur Exception CinetPay: ' . $e->getMessage());
                        return redirect()->route('user.extrait.mariage.index')->with('error', 'Erreur interne de paiement.');
                    }
                }
            } else {
                // Livraison totalement gratuite (timbres + livraison = 0)
                $mariage->etat = 'en attente';
                $mariage->save();
                if ($freeCalc['free_timbres'] > 0) {
                    $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                }
            }
        } else {
            // Retrait sur place
            $mariage->etat = 'en attente';
            $mariage->montant_timbre = $freeCalc['montant_timbre_total'];
            if ($freeCalc['free_timbres'] > 0) {
                $mariage->is_free_request = true;
                $mariage->free_timbres_count = $freeCalc['free_timbres'];
                $mariage->save();
                $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
            } else {
                $mariage->save();
            }
        }

        $phoneNumber = $user->indicatif . $user->contact;
        $message = "Bonjour {$user->name}, votre demande d'extrait de mariage a bien été transmise à la mairie du plateau. Référence: {$mariage->reference}.
Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com";
        $yellikaSmsService->sendSms($phoneNumber, $message);

        // Envoi de l'email de confirmation
        Notification::send($user, new DemandeMariageConfirmationNotification($user, $mariage));

        return redirect()->route('user.extrait.mariage.index')->with('success', 'Votre demande a été traitée avec succès.');
    }

    /**
     * Modifier une demande rejetée ou non encore attribuée à un agent
     */
    public function modifierDemande(Request $request, $id)
    {
        $demande = Mariage::where('user_id', Auth::id())
            ->where('id', $id)
            ->where(function ($query) {
                $query->where('peut_modifier', true)
                    ->orWhereNull('agent_id');
            })
            ->firstOrFail();

        $originalChoixOption = $demande->choix_option;

        $rules = [
            'typeDemande' => 'required|string',
            'pour' => 'nullable|string',
            'relation' => 'nullable|string',
            'nomEpoux' => 'nullable|string|max:255',
            'prenomEpoux' => 'nullable|string|max:255',
            'dateNaissanceEpoux' => 'nullable|date',
            'lieuNaissanceEpoux' => 'nullable|string|max:255',
            'nomEpouse' => 'nullable|string|max:255',
            'prenomEpouse' => 'nullable|string|max:255',
            'dateNaissanceEpouse' => 'nullable|date',
            'lieuNaissanceEpouse' => 'nullable|string|max:255',
            'commune' => 'required|string',
            'commune_mariage' => 'required|string|max:255',
            'qty_simple' => 'nullable|integer|min:0|max:10',
            'qty_integral' => 'nullable|integer|min:0|max:10',
            'pieceIdentite' => $demande->pieceIdentite ? 'nullable|mimes:png,jpg,jpeg,pdf|max:25600' : 'required|mimes:png,jpg,jpeg,pdf|max:25600',
            'extraitMariage' => 'nullable|mimes:png,jpg,jpeg,pdf|max:25600',
            'document_autorisation' => [
                \Illuminate\Validation\Rule::requiredIf(function () use ($request, $demande) {
                    return $request->pour === 'une_autre_personne' && $request->relation === 'connaissance' && !$demande->document_autorisation;
                }),
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:25600'
            ],
            'CMU' => 'nullable|string|max:50',
        ];

        $validated = $request->validate($rules);

        // Validation IA Gemini de la pièce d'identité
        if ($request->hasFile('pieceIdentite')) {
            $geminiService = app(\App\Services\GeminiValidationService::class);
            $validation = $geminiService->validateIdentityDocument($request->file('pieceIdentite'));
            if (!$validation['isValid']) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité a été rejetée par l'IA : " . $validation['reason']
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['pieceIdentite' => "La pièce d'identité a été rejetée par l'IA de la mairie : " . $validation['reason']])
                    ->withInput();
            }
        }

        // Upload files
        $filesToUpload = [
            'pieceIdentite' => 'identite',
            'extraitMariage' => 'extrait',
            'document_autorisation' => 'autorisations',
        ];

        foreach ($filesToUpload as $fileKey => $subDir) {
            if ($request->hasFile($fileKey)) {
                if ($demande->$fileKey) {
                    Storage::disk('public')->delete($demande->$fileKey);
                }
                $file = $request->file($fileKey);
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $file->storeAs("images/mariages/$subDir", $newFileName, 'public');
                $demande->$fileKey = "images/mariages/$subDir/$newFileName";
            }
        }

        // Calculate quantities
        $qtySimple = (int) $request->input('qty_simple', 0);
        $qtyIntegral = (int) $request->input('qty_integral', 0);

        if ($request->typeDemande === 'simple') {
            $qtyIntegral = 0;
            if ($qtySimple <= 0) {
                $qtySimple = 1;
            }
        } elseif ($request->typeDemande === 'integrale') {
            $qtySimple = 0;
            if ($qtyIntegral <= 0) {
                $qtyIntegral = 1;
            }
        } else {
            // groupee
            if ($qtySimple <= 0) {
                $qtySimple = 1;
            }
            if ($qtyIntegral <= 0) {
                $qtyIntegral = 1;
            }
        }
        $totalQuantity = $qtySimple + $qtyIntegral;

        // Update fields
        $demande->type = $request->input('typeDemande');
        $demande->pour = $request->input('pour');
        $demande->relation = $request->input('relation');
        $demande->nomEpoux = $request->input('nomEpoux');
        $demande->prenomEpoux = $request->input('prenomEpoux');
        $demande->dateNaissanceEpoux = $request->input('dateNaissanceEpoux');
        $demande->lieuNaissanceEpoux = $request->input('lieuNaissanceEpoux');
        $demande->nomEpouse = $request->input('nomEpouse');
        $demande->prenomEpouse = $request->input('prenomEpouse');
        $demande->dateNaissanceEpouse = $request->input('dateNaissanceEpouse');
        $demande->lieuNaissanceEpouse = $request->input('lieuNaissanceEpouse');
        $demande->commune = $request->input('commune');
        $demande->commune_mariage = $request->input('commune_mariage');
        $demande->qty_simple = $qtySimple;
        $demande->qty_integral = $qtyIntegral;
        $demande->quantite = $totalQuantity;
        $demande->CMU = $request->input('CMU');

        // Delivery
        $needsPayment = ($request->input('choix_option') === 'livraison' && $originalChoixOption !== 'livraison');
        $pendingDeliveryData = null;

        if ($request->input('choix_option') === 'livraison') {
            $deliveryData = [
                'choix_option' => 'livraison',
                'montant_timbre' => $request->input('montant_timbre'),
                'montant_livraison' => $request->input('montant_livraison'),
                'nom_destinataire' => $request->input('nom_destinataire'),
                'prenom_destinataire' => $request->input('prenom_destinataire'),
                'email_destinataire' => $request->input('email_destinataire'),
                'contact_destinataire' => $request->input('contact_destinataire'),
                'adresse_livraison' => $request->input('adresse_livraison'),
                'code_postal' => $request->input('code_postal'),
                'ville' => $request->input('ville'),
                'commune_livraison' => $request->input('commune_livraison'),
                'quartier' => $request->input('quartier'),
                'date_livraison' => $request->input('date_livraison'),
                'heure_livraison' => $request->input('heure_livraison'),
            ];

            if ($needsPayment) {
                $pendingDeliveryData = $deliveryData;
                $demande->choix_option = $originalChoixOption; // Conserver l'ancien choix (retrait) en BDD avant paiement
            } else {
                $demande->choix_option = 'livraison';
                $demande->montant_timbre = $request->input('montant_timbre');
                $demande->montant_livraison = $request->input('montant_livraison');
                $demande->nom_destinataire = $request->input('nom_destinataire');
                $demande->prenom_destinataire = $request->input('prenom_destinataire');
                $demande->email_destinataire = $request->input('email_destinataire');
                $demande->contact_destinataire = $request->input('contact_destinataire');
                $demande->adresse_livraison = $request->input('adresse_livraison');
                $demande->code_postal = $request->input('code_postal');
                $demande->ville = $request->input('ville');
                $demande->commune_livraison = $request->input('commune_livraison');
                $demande->quartier = $request->input('quartier');
                $demande->date_livraison = $request->input('date_livraison');
                $demande->heure_livraison = $request->input('heure_livraison');
            }
        } else {
            $demande->choix_option = $request->choix_option;
        }

        // Réinitialiser l'état et désactiver la modification
        $demande->etat = 'en attente';
        $demande->peut_modifier = false;
        $demande->champs_a_modifier = null;
        $demande->motif_de_rejet = null;
        $demande->save();

        if ($needsPayment && $pendingDeliveryData) {
            $user = Auth::user();
            $user->refresh();
            $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) ($demande->qty_simple + $demande->qty_integral));
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];
            $montantLivraison = (float) $pendingDeliveryData['montant_livraison'];
            $totalAmount = $montantTimbreTotal + $montantLivraison;

            $demande->montant_timbre = $montantTimbreTotal;
            $demande->is_free_request = $freeCalc['free_timbres'] > 0;
            $demande->free_timbres_count = $freeCalc['free_timbres'];
            $demande->save();

            // Mettre en cache les données de livraison
            \Illuminate\Support\Facades\Cache::put('pending_delivery_update_' . $demande->reference, $pendingDeliveryData, now()->addDays(7));

            if ($totalAmount > 0) {
                $paymentMethod = $request->input('payment_method', 'wave');
                $baseUrl = config('app.url');
                $successUrl = $baseUrl . '/user/payment/success?reference=' . urlencode($demande->reference) . "&type=mariage";
                $errorUrl = $baseUrl . '/user/payment/cancel?reference=' . urlencode($demande->reference) . "&type=mariage";

                if (strtolower($paymentMethod) === 'wave') {
                    $waveService = app(\App\Services\WaveService::class);
                    $checkoutSession = $waveService->createCheckoutSession(
                        $totalAmount,
                        'XOF',
                        $successUrl,
                        $errorUrl,
                        $demande->reference
                    );

                    if ($checkoutSession && isset($checkoutSession['wave_launch_url'])) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => true,
                                'redirect_url' => $checkoutSession['wave_launch_url']
                            ]);
                        }
                        return redirect($checkoutSession['wave_launch_url']);
                    }
                    return redirect()->route('user.extrait.mariage.index')->with('error', 'Erreur lors de la préparation du paiement Wave. Veuillez réessayer.');
                } elseif (strtolower($paymentMethod) === 'mtn') {
                    $mtnPhoneNumber = $request->input('mtn_number') ?: $demande->contact_destinataire;
                    $mtnPhoneNumber = preg_replace('/[^0-9]/', '', $mtnPhoneNumber);
                    if (!str_starts_with($mtnPhoneNumber, '225') && strlen($mtnPhoneNumber) == 10) {
                        $mtnPhoneNumber = '225' . $mtnPhoneNumber;
                    }

                    $mtnService = new \App\Services\MtnService();
                    $response = $mtnService->requestToPay(
                        $totalAmount,
                        $mtnPhoneNumber,
                        $demande->reference,
                        'Extrait Mariage',
                        'Mairie Plateau'
                    );

                    if ($response && $response['status'] === 'PENDING') {
                        session(['mtn_ref_' . $demande->reference => $response['referenceId']]);
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => true,
                                'redirect_url' => route('user.payment.mtn.waiting', [
                                    'reference' => $demande->reference,
                                    'type' => 'mariage'
                                ])
                            ]);
                        }
                        return redirect()->route('user.payment.mtn.waiting', [
                            'reference' => $demande->reference,
                            'type' => 'mariage'
                        ]);
                    }
                    return redirect()->route('user.extrait.mariage.index')->with('error', 'Erreur lors de la préparation du paiement MTN. Veuillez réessayer.');
                } else {
                    $cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548');
                    $cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');
                    try {
                        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', [
                            'apikey' => $cinetpayApiKey,
                            'site_id' => $cinetpaySiteId,
                            'transaction_id' => $demande->reference,
                            'amount' => $totalAmount,
                            'currency' => 'XOF',
                            'description' => "Paiement pour " . $demande->reference,
                            'return_url' => $successUrl,
                            'notify_url' => $baseUrl . '/api/webhook/cinetpay',
                            'channels' => 'ALL',
                        ]);

                        if ($response->successful()) {
                            $data = $response->json();
                            if (isset($data['data']['payment_url'])) {
                                if ($request->expectsJson()) {
                                    return response()->json([
                                        'success' => true,
                                        'redirect_url' => $data['data']['payment_url']
                                    ]);
                                }
                                return redirect($data['data']['payment_url']);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Exception CinetPay: ' . $e->getMessage());
                    }
                }
            } else {
                $demande->etat = 'en attente';
                $demande->save();
                if ($freeCalc['free_timbres'] > 0) {
                    $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Demande modifiée avec succès et soumise à nouveau.',
                'demande' => $demande
            ]);
        }

        return redirect()->route('user.extrait.mariage.index')->with('success', 'Votre demande a été modifiée avec succès.');
    }

    public function edit($id)
    {
        $demande = Mariage::where('user_id', Auth::id())
            ->where('id', $id)
            ->where(function ($query) {
                $query->where('peut_modifier', true)
                    ->orWhereNull('agent_id');
            })
            ->firstOrFail();

        $user = Auth::user();
        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining = $this->getRemainingFreeRequests($user);

        return view('user.mariage.create', [
            'mariage' => $demande,
            'user' => $user,
            'freeRequestsModeActive' => $freeRequestsModeActive,
            'freeRequestsRemaining' => $freeRequestsRemaining,
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
