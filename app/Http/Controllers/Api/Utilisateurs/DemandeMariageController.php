<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Mariage; // ✅ Modèle Mariage
use App\Models\Paiement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Services\YellikaSmsService;
use App\Notifications\DemandeMariageConfirmationNotification;
use Illuminate\Support\Facades\Notification;
use App\Traits\HandlesFreeRequests;

class DemandeMariageController extends Controller
{
    use HandlesFreeRequests;
    /**
     * Liste des demandes de mariage de l'utilisateur
     * GET /api/utilisateurs/demandes/mariage
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $mariages = Mariage::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des demandes de mariage récupérée avec succès',
                'data' => [
                    'demandes' => $mariages->map(function ($demande) {
                        // Utilise l'helper pour la consistance
                        return $this->formatDemandeResponse($demande, true);
                    })
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des demandes'
            ], 500);
        }
    }

    /**
     * Créer une nouvelle demande de mariage
     * POST /api/utilisateurs/demandes/mariage
     */
    public function store(Request $request, YellikaSmsService $yellikaSmsService, \App\Services\WaveService $waveService): JsonResponse
    {
        // 1. Validation (Spécifique au Mariage)
        $validator = Validator::make($request->all(), [
            'qty_simple' => 'nullable|integer|min:0|max:10',
            'qty_integral' => 'nullable|integer|min:0|max:10',
            'payment_method' => 'required|string|in:wave,orange,mtn,moov,cinetpay,tresorpay',
            'mtn_number' => 'required_if:payment_method,mtn|nullable|string|regex:/^05[0-9]{8}$/',
            'pieceIdentite' => 'required',
            'extraitMariage' => 'nullable',
            'commune_mariage' => 'required|string',
            'pour' => 'nullable|string|max:255',
            'relation' => 'nullable|string|in:enfant,parent,connaissance',
            'document_autorisation' => 'required_if:relation,connaissance|nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            'mtn_number.required_if' => 'Le numéro MTN est obligatoire lorsque le moyen de paiement choisi est MTN.',
            'mtn_number.regex' => 'Le numéro MTN doit comporter exactement 10 chiffres et commencer par 05.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        try {
            $user = Auth::user();

            // 2. Upload des fichiers (Spécifique au Mariage)
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
                    // ✅ Chemin d'upload Mariage
                    $path = $file->storeAs("images/mariages/$subDir", $newFileName, 'public');
                    $uploadedPaths[$fileKey] = $path;
                }
            }

            // 3. Génération de référence (Spécifique au Mariage)
            $commune = $request->commune ?: $user->commune;
            $communeInitiale = strtoupper(substr($commune ?: 'X', 0, 1));
            $anneeCourante = Carbon::now()->year;
            $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $increment = Mariage::max('id') + 1;
            $reference = 'AM' . $randomDigits . $increment . $communeInitiale . $anneeCourante; // ✅ Préfixe AM

            // 4. Création de la demande (Spécifique au Mariage)
            $mariage = new Mariage();
            $mariage->pour = $request->pour;
            $mariage->relation = $request->relation;
            $mariage->document_autorisation = $uploadedPaths['document_autorisation'] ?? null;
            $mariage->type = $request->input('typeDemande'); // Harmonisation (simple, integrale, groupee)
            $mariage->nomEpoux = $request->nomEpoux;
            $mariage->prenomEpoux = $request->prenomEpoux;
            $mariage->dateNaissanceEpoux = $request->dateNaissanceEpoux;
            $mariage->lieuNaissanceEpoux = $request->lieuNaissanceEpoux;
            $mariage->nomEpouse = $request->nomEpouse;
            $mariage->prenomEpouse = $request->prenomEpouse;
            $mariage->dateNaissanceEpouse = $request->dateNaissanceEpouse;
            $mariage->lieuNaissanceEpouse = $request->lieuNaissanceEpouse;
            $mariage->commune_mariage = $request->commune_mariage;

            // Calcul des quantités
            $qtySimple = (int) $request->input('qty_simple', 0);
            $qtyIntegral = (int) $request->input('qty_integral', 0);

            $totalQuantity = $qtySimple + $qtyIntegral;

            if ($totalQuantity === 0) {
                $type = $request->input('typeDemande');
                if ($type === 'integrale') {
                    $qtyIntegral = 1;
                } else {
                    $qtySimple = 1;
                }
                $totalQuantity = 1;
            }

            $mariage->qty_simple = $qtySimple;
            $mariage->qty_integral = $qtyIntegral;
            $mariage->quantite = $totalQuantity;

            $mariage->pieceIdentite = $uploadedPaths['pieceIdentite'] ?? null;
            $mariage->extraitMariage = $uploadedPaths['extraitMariage'] ?? null;
            $mariage->commune = "plateau";
            // Normalisation : le mobile envoie 'retrait'/'livraison' (minuscules)
            // mais le backend attend 'Retrait sur place'/'Livraison' (comme le web)
            $mariage->choix_option = strtolower($request->choix_option) === 'livraison'
                ? 'livraison'
                : 'Retrait sur place';
            $mariage->user_id = $user->id;
            $mariage->reference = $reference;

            // --- GESTION DES DEMANDES GRATUITES ---
            $user->refresh();
            $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) $mariage->quantite);
            Log::info("Demandes gratuites - Mariage (API) {$mariage->reference}: {$freeCalc['free_timbres']} timbres gratuits, {$freeCalc['paid_timbres']} payants");

            // Calcul unifié du montant des timbres (indépendant du mode retrait/livraison)
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];
            $mariage->montant_timbre = $montantTimbreTotal;
            $mariage->is_free_request = $freeCalc['free_timbres'] > 0;
            $mariage->free_timbres_count = $freeCalc['free_timbres'];

            if ($mariage->choix_option === 'livraison') {
                $montantLivraison = (float) $request->montant_livraison;
                $totalAmount = $montantTimbreTotal + $montantLivraison;

                $mariage->montant_livraison = $montantLivraison;
                $mariage->nom_destinataire = $request->nom_destinataire;
                $mariage->prenom_destinataire = $request->prenom_destinataire;
                $mariage->email_destinataire = $request->email_destinataire;
                $mariage->contact_destinataire = $request->contact_destinataire;
                $mariage->adresse_livraison = $request->adresse_livraison;
                $mariage->code_postal = $request->code_postal;
                $mariage->ville = $request->ville;
                $mariage->commune_livraison = $request->commune_livraison;
                $mariage->quartier = $request->quartier;

                if ($totalAmount > 0) {
                    $mariage->etat = 'en attente de paiement';
                    $mariage->statut_livraison = 'en attente de paiement';
                } else {
                    $mariage->etat = 'en attente';
                    $mariage->statut_livraison = null;
                }
            } else {
                // Retrait sur place : les timbres sont toujours payants (comme sur le web)
                $totalAmount = $montantTimbreTotal;
                $mariage->statut_livraison = null;

                if ($totalAmount > 0) {
                    $mariage->etat = 'en attente de paiement';
                } else {
                    $mariage->etat = 'en attente';
                }
            }

            $mariage->save();

            // 5. Réponse conditionnelle (Cas "Retrait" ou "Gratuit Livraison")
            if ($totalAmount == 0) {
                // Incrémenter le compteur SEULEMENT maintenant (pas de paiement requis)
                if ($freeCalc['free_timbres'] > 0) {
                    $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                }

                // Envoi des notifications (SMS & Email)
                try {
                    $phoneNumber = $user->indicatif . $user->contact;
                    $message = "Bonjour {$user->name}, votre demande d'extrait de mariage a bien été transmise à la mairie du plateau. Référence : {$mariage->reference}.
Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com/home/search";
                    $yellikaSmsService->sendSms($phoneNumber, $message);

                    Notification::send($user, new DemandeMariageConfirmationNotification($user, $mariage));
                } catch (\Exception $e) {
                    Log::error("Erreur notifications DemandeMariage (API): " . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Demande de mariage créée avec succès (gratuite ou retrait)',
                    'requires_payment' => false,
                    'free_requests' => [
                        'timbres_gratuits_appliques' => $freeCalc['free_timbres'],
                        'economie' => $freeCalc['montant_timbre_gratuit'],
                        'restants' => $this->getRemainingFreeRequests($user),
                    ],
                    'data' => ['demande' => $this->formatDemandeResponse($mariage)]
                ], 201);
            }

            // --- DEBUT DE LA LOGIQUE DE PAIEMENT ---

            $paymentMethod = $request->input('payment_method');
            $paymentLinkResult = $this->generatePaymentLink($mariage, $totalAmount, $paymentMethod);

            // 7. Gérer l'échec de la génération de lien
            if (!$paymentLinkResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demande créée, mais échec de la génération du lien de paiement. Veuillez réessayer.',
                    'error_details' => $paymentLinkResult['error_details']
                ], 500);
            }

            // 8. Succès ! Construire la réponse
            $msg = in_array(strtolower($paymentMethod), ['mtn', 'tresorpay'])
                ? 'Demande créée. Un message de validation de paiement (Push USSD) a été envoyé sur votre numéro pour finaliser le paiement.'
                : 'Demande créée. Utilisez le payment_url pour payer.';

            return response()->json([
                'success' => true,
                'message' => $msg,
                'payment_method' => $paymentMethod,
                'mtn_number' => $request->input('mtn_number'),
                'requires_payment' => true,
                'free_requests' => [
                    'timbres_gratuits_appliques' => $freeCalc['free_timbres'],
                    'economie' => $freeCalc['montant_timbre_gratuit'],
                    'restants_apres_paiement' => max(0, $this->getRemainingFreeRequests($user) - $freeCalc['free_timbres']),
                ],
                'payment_details' => [
                    'payment_url' => $paymentLinkResult['payment_url'] ?? null,
                    'is_ussd_push' => $paymentLinkResult['is_ussd_push'] ?? false,
                    'mtn_ref' => $paymentLinkResult['mtn_ref'] ?? null,
                    'transaction_id' => $paymentLinkResult['generated_transaction_id'] ?? null,
                    'mode' => 'PRODUCTION',
                    'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'] ?? null,
                    'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'] ?? null,
                    'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'] ?? null,
                    'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'] ?? null,
                ],

                'data' => [
                    'demande' => $this->formatDemandeResponse($mariage)
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@store: ' . $e->getMessage() . ' Ligne: ' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    // --------------------------------------------------------------------
    // NOUVELLE MÉTHODE PRIVÉE (Logique extraite de store())
    // --------------------------------------------------------------------
    /**
     * Génère un nouveau lien de paiement (Wave ou CinetPay) pour une demande de mariage existante.
     */
    private function generatePaymentLink(Mariage $mariage, $totalAmount, $paymentMethod, $customReference = null): array
    {
        try {
            $transactionReference = $customReference ?: $mariage->reference;

            // 1. Préparer les URLs
            $baseUrl = config('app.url');
            $returnUrl = "plateauapps://app/payment-result?method={$paymentMethod}&status=success&transactionId={$transactionReference}";
            $cancelUrl = "plateauapps://app/payment-result?method={$paymentMethod}&status=cancel&transactionId={$transactionReference}";
            $fallbackReturnUrl = $baseUrl . "/mariage/paiement/" . urlencode($transactionReference);
            $fallbackCancelUrl = $baseUrl . "/mariage/paiement/" . urlencode($transactionReference);

            // Si c'est Wave, utiliser le service Wave
            if (strtolower($paymentMethod) === 'wave') {
                $waveService = app(\App\Services\WaveService::class);
                $checkoutSession = $waveService->createCheckoutSession(
                    $totalAmount,
                    'XOF',
                    $fallbackReturnUrl,
                    $fallbackCancelUrl,
                    $transactionReference
                );

                if (!$checkoutSession || !isset($checkoutSession['wave_launch_url'])) {
                    return [
                        'success' => false,
                        'message' => 'Échec de la génération du lien de paiement Wave.',
                        'error_details' => $checkoutSession
                    ];
                }

                return [
                    'success' => true,
                    'payment_url' => $checkoutSession['wave_launch_url'],
                    'generated_transaction_id' => $transactionReference,
                    'return_url_deep_link' => $returnUrl,
                    'cancel_url_deep_link' => $cancelUrl,
                    'return_url_web_fallback' => $fallbackReturnUrl,
                    'cancel_url_web_fallback' => $fallbackCancelUrl,
                ];
            }

            // Si c'est MTN, utiliser MTN MoMo API en direct (MtnService)
            if (strtolower($paymentMethod) === 'mtn') {
                $mtnPhoneNumber = request()->input('mtn_number') ?: $mariage->contact_destinataire ?: (auth()->check() ? auth()->user()->contact : '');

                // Formater le numéro
                $mtnPhoneNumber = preg_replace('/[^0-9]/', '', $mtnPhoneNumber);
                if (!str_starts_with($mtnPhoneNumber, '225') && strlen($mtnPhoneNumber) == 10) {
                    $mtnPhoneNumber = '225' . $mtnPhoneNumber;
                }

                $mtnService = new \App\Services\MtnService();
                $response = $mtnService->requestToPay(
                    $totalAmount,
                    $mtnPhoneNumber,
                    $transactionReference,
                    'Extrait Mariage',
                    'Mairie Plateau'
                );

                if ($response && $response['status'] === 'PENDING') {
                    // Stocker le ReferenceId en cache pour la vérification
                    \Illuminate\Support\Facades\Cache::put('mtn_ref_' . $transactionReference, $response['referenceId'], now()->addHours(1));

                    return [
                        'success' => true,
                        'payment_url' => null, // Pas de lien de redirection pour le push USSD
                        'is_ussd_push' => true,
                        'mtn_ref' => $response['referenceId'],
                        'generated_transaction_id' => $transactionReference,
                        'return_url_deep_link' => $returnUrl,
                        'cancel_url_deep_link' => $cancelUrl,
                        'return_url_web_fallback' => $fallbackReturnUrl,
                        'cancel_url_web_fallback' => $fallbackCancelUrl,
                    ];
                }

            // Si c'est TrésorPay, utiliser TresorPayService
            }
            
            if (strtolower($paymentMethod) === 'tresorpay') {
                $tresorPhone = request()->input('mtn_number') ?: $mariage->contact_destinataire ?: (auth()->check() ? auth()->user()->contact : '');
                $tresorPhone = preg_replace('/[^0-9]/', '', $tresorPhone);

                $tresorService = app(\App\Services\TresorPayService::class);
                $nom = auth()->check() ? auth()->user()->name : 'Client';
                $prenoms = auth()->check() ? auth()->user()->prenoms : 'Plateau';
                
                $response = $tresorService->initierPaiementDirect($tresorPhone, $totalAmount, $transactionReference, $nom, $prenoms);

                if ($response && ($response['success'] ?? false)) {
                    return [
                        'success' => true,
                        'payment_url' => null, // Pas de lien pour le push USSD
                        'is_ussd_push' => true,
                        'generated_transaction_id' => $transactionReference,
                        'return_url_deep_link' => $returnUrl,
                        'cancel_url_deep_link' => $cancelUrl,
                        'return_url_web_fallback' => $fallbackReturnUrl,
                        'cancel_url_web_fallback' => $fallbackCancelUrl,
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Échec de l\'initiation du paiement TrésorPay (Push USSD). ' . ($response['message'] ?? ''),
                    'error_details' => $response
                ];
            }

                return [
                    'success' => false,
                    'message' => 'Échec de l\'initiation du paiement MTN direct (Push USSD).',
                    'error_details' => $response
                ];

            // Sinon, utiliser CinetPay pour les autres moyens de paiement (Orange, Moov)
            $channels = 'ALL';
            if (in_array(strtolower($paymentMethod), ['orange', 'moov'])) {
                $channels = 'MOBILE_MONEY';
            }

            $cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548');
            $cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');

            $response = Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $transactionReference,
                'amount' => $totalAmount,
                'currency' => 'XOF',
                'description' => "Paiement pour " . $transactionReference,
                'return_url' => $fallbackReturnUrl,
                'notify_url' => $baseUrl . '/api/webhook/cinetpay',
                'channels' => $channels,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']['payment_url'])) {
                    return [
                        'success' => true,
                        'payment_url' => $data['data']['payment_url'],
                        'generated_transaction_id' => $transactionReference,
                        'return_url_deep_link' => $returnUrl,
                        'cancel_url_deep_link' => $cancelUrl,
                        'return_url_web_fallback' => $fallbackReturnUrl,
                        'cancel_url_web_fallback' => $fallbackCancelUrl,
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Échec de la génération du lien CinetPay.',
                'error_details' => $response->body()
            ];
        } catch (\Exception $e) {
            Log::error('Exception in generatePaymentLink: ' . $e->getMessage(), ['reference' => $mariage->reference]);
            return [
                'success' => false,
                'message' => 'Erreur interne lors de la génération du lien: ' . $e->getMessage(),
                'error_details' => null
            ];
        }
    }


    // --------------------------------------------------------------------
    // NOUVELLE MÉTHODE PUBLIQUE (Pour la relance)
    // --------------------------------------------------------------------
    /**
     * Retente le paiement pour une demande de mariage échouée.
     * POST /api/utilisateurs/demandes/mariage/{mariage}/retry-payment
     */
    public function retryPayment(Request $request, Mariage $mariage): JsonResponse
    {
        // 0. Validation de la méthode et du numéro MTN
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string|in:wave,orange,mtn,moov,cinetpay,tresorpay',
            'mtn_number' => 'required_if:payment_method,mtn|nullable|string|regex:/^05[0-9]{8}$/',
        ], [
            'mtn_number.required_if' => 'Le numéro MTN est obligatoire lorsque le moyen de paiement choisi est MTN.',
            'mtn_number.regex' => 'Le numéro MTN doit comporter exactement 10 chiffres et commencer par 05.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($mariage->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier si un montant est dû (timbres + livraison éventuelle)
            $totalAmountDue = (float) $mariage->montant_timbre + (float) $mariage->montant_livraison;
            if ($totalAmountDue <= 0) {
                return response()->json(['success' => false, 'message' => 'Cette demande ne nécessite pas de paiement (montant nul).'], 400);
            }

            // 3. Vérifier l'état
            if (!in_array($mariage->etat, ['paiement_echoue', 'en attente de paiement'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être payée à nouveau (état actuel: ' . $mariage->etat . ')'
                ], 400);
            }

            // 4. Mettre à jour l'état
            $mariage->etat = 'paiement_echoue';
            $mariage->statut_livraison = 'paiement_echoue';
            $mariage->save();

            // 5. Générer le nouveau lien de paiement 
            $paymentMethod = $request->input('payment_method', 'wave'); // Par défaut
            $totalAmount = $totalAmountDue; // Déjà calculé ci-dessus
            $paymentLinkResult = $this->generatePaymentLink($mariage, $totalAmount, $paymentMethod);

            // 6. Gérer l'échec de la génération
            if (!$paymentLinkResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de la génération du nouveau lien de paiement.',
                    'error_details' => $paymentLinkResult['error_details']
                ], 500);
            }

            // 7. Succès !
            $msg = strtolower($paymentMethod) === 'mtn'
                ? 'Un message de validation de paiement (Push USSD) a été envoyé sur votre numéro MTN pour finaliser le paiement.'
                : 'Nouveau lien de paiement généré. Utilisez le payment_url pour payer.';

            return response()->json([
                'success' => true,
                'message' => $msg,
                'requires_payment' => true,

                'payment_details' => [
                    'payment_url' => $paymentLinkResult['payment_url'] ?? null,
                    'is_ussd_push' => $paymentLinkResult['is_ussd_push'] ?? false,
                    'mtn_ref' => $paymentLinkResult['mtn_ref'] ?? null,
                    'transaction_id' => $paymentLinkResult['generated_transaction_id'] ?? null,
                    'mode' => 'PRODUCTION',
                    'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'] ?? null,
                    'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'] ?? null,
                    'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'] ?? null,
                    'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'] ?? null,
                ],

                'data' => [
                    'demande' => $this->formatDemandeResponse($mariage)
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@retryPayment: ' . $e->getMessage(), ['mariage_id' => $mariage->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne lors de la tentative de paiement: ' . $e->getMessage()
            ], 500);
        }
    }
    public function relancerDemande(Request $request, Mariage $mariage): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($mariage->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier l'état
            if ($mariage->etat !== 'rejetée') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être relancée (état actuel: ' . $mariage->etat . ')'
                ], 400);
            }

            // 3. Mettre à jour l'état
            $mariage->etat = 'en attente';

            if ($mariage->choix_option === 'livraison') {
                $mariage->statut_livraison = 'en attente';
            } else {
                $mariage->statut_livraison = null;
            }

            // --- MISE À JOUR DEMANDÉE ---
            // Force la date de création à être la date actuelle.
            $mariage->created_at = now();

            $mariage->save();

            // 4. Succès !
            return response()->json([
                'success' => true,
                'message' => 'Demande relancée avec succès. Elle est maintenant "en attente".',
                'data' => [
                    'demande' => $this->formatDemandeResponse($mariage)
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@relancerDemande: ' . $e->getMessage(), ['mariage_id' => $mariage->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne lors de la relance de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier une demande rejetée
     * POST /api/utilisateurs/demandes/mariage/{mariage}/modifier
     */
    public function modifierDemande(Request $request, Mariage $mariage): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($mariage->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier si la demande a été rejetée (peut_modifier == true ou etat == 'rejetée')
            if ($mariage->peut_modifier || $mariage->etat === 'rejetée') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande a été rejetée. Veuillez utiliser l\'API de modification des informations rejetées pour corriger les champs requis.'
                ], 400);
            }

            // 3. Vérifier que la demande est libre de modification (agent_id est NULL)
            $isNotAssigned = is_null($mariage->agent_id);
            if (!$isNotAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être modifiée complètement (déjà attribuée à un agent)'
                ], 400);
            }

            // 3. Règles de validation pour modification complète
            $rules = [
                'typeDemande' => 'nullable|string|in:simple,integrale,groupee',
                'type' => 'nullable|string|in:simple,integrale,groupee',
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
                'pieceIdentite' => $mariage->pieceIdentite ? 'nullable' : 'required',
                'extraitMariage' => 'nullable',
                'document_autorisation' => 'required_if:relation,connaissance|nullable|file|mimes:jpeg,png,jpg,pdf,heic',
                'CMU' => 'nullable|string|max:50',
                'choix_option' => 'required|in:retrait,livraison',
            ];

            // Règles de livraison si choix_option est livraison
            if ($request->input('choix_option') === 'livraison') {
                $rules['montant_timbre'] = 'required|numeric';
                $rules['montant_livraison'] = 'required|numeric';
                $rules['nom_destinataire'] = 'required|string|max:255';
                $rules['prenom_destinataire'] = 'required|string|max:255';
                $rules['email_destinataire'] = 'nullable|email';
                $rules['contact_destinataire'] = 'required|string|max:20';
                $rules['adresse_livraison'] = 'required|string|max:500';
                $rules['code_postal'] = 'nullable|string|max:10';
                $rules['ville'] = 'nullable|string|max:255';
                $rules['commune_livraison'] = 'nullable|string|max:255';
                $rules['quartier'] = 'nullable|string|max:255';
            }

            // 4. Valider les données
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 5. Validation IA Gemini de la pièce d'identité si elle est fournie
            if ($request->hasFile('pieceIdentite')) {
                $geminiService = app(\App\Services\GeminiValidationService::class);
                $validation = $geminiService->validateIdentityDocument($request->file('pieceIdentite'));
                if (!$validation['isValid']) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité a été rejetée par l'IA de la mairie : " . $validation['reason']
                    ], 422);
                }
            }

            // 6. Enregistrer les fichiers (sauvegarder les anciens chemins pour restauration si nécessaire)
            $fileKeysMariage = ['pieceIdentite', 'extraitMariage', 'document_autorisation'];
            $anciensFichiers = [];
            foreach ($fileKeysMariage as $fk) {
                $anciensFichiers[$fk] = $mariage->$fk;
            }

            $filesToUpload = [
                'pieceIdentite' => 'identite',
                'extraitMariage' => 'extrait',
                'document_autorisation' => 'autorisations',
            ];

            $nouveauxFichiers = [];
            foreach ($filesToUpload as $fileKey => $subDir) {
                if ($request->hasFile($fileKey)) {
                    if ($mariage->$fileKey && Storage::disk('public')->exists($mariage->$fileKey)) {
                        Storage::disk('public')->delete($mariage->$fileKey);
                    }
                    $file = $request->file($fileKey);
                    $extension = $file->getClientOriginalExtension();
                    $newFileName = (string) Str::uuid() . '.' . $extension;
                    $path = $file->storeAs("images/mariages/$subDir", $newFileName, 'public');
                    $mariage->$fileKey = $path;
                    $nouveauxFichiers[$fileKey] = $path;
                }
            }

            // Valeurs originales en base (avant modification) pour restauration si paiement requis
            $originalData = [
                'pour' => $mariage->pour,
                'relation' => $mariage->relation,
                'type' => $mariage->type,
                'nomEpoux' => $mariage->nomEpoux,
                'prenomEpoux' => $mariage->prenomEpoux,
                'dateNaissanceEpoux' => $mariage->dateNaissanceEpoux,
                'lieuNaissanceEpoux' => $mariage->lieuNaissanceEpoux,
                'nomEpouse' => $mariage->nomEpouse,
                'prenomEpouse' => $mariage->prenomEpouse,
                'dateNaissanceEpouse' => $mariage->dateNaissanceEpouse,
                'lieuNaissanceEpouse' => $mariage->lieuNaissanceEpouse,
                'commune' => $mariage->commune,
                'commune_mariage' => $mariage->commune_mariage,
                'CMU' => $mariage->CMU,
                'qty_simple' => $mariage->qty_simple,
                'qty_integral' => $mariage->qty_integral,
                'quantite' => $mariage->quantite,
                'fichiers' => $anciensFichiers,
                'montant_timbre' => $mariage->montant_timbre,
                'is_free_request' => $mariage->is_free_request,
                'free_timbres_count' => $mariage->free_timbres_count,
                'etat' => $mariage->etat,
            ];

            // 7. Calculer les nouvelles valeurs en mémoire
            $mariage->pour = $request->input('pour', $mariage->pour);
            $mariage->relation = $request->input('relation', $mariage->relation);
            // Ne mettre à jour le type que si une valeur valide est envoyée (simple/integrale/groupee)
            $nouveauTypeMariage = $request->input('typeDemande') ?: $request->input('type');
            if (in_array($nouveauTypeMariage, ['simple', 'integrale', 'groupee'])) {
                $mariage->type = $nouveauTypeMariage;
            }
            $mariage->nomEpoux = $request->input('nomEpoux', $mariage->nomEpoux);
            $mariage->prenomEpoux = $request->input('prenomEpoux', $mariage->prenomEpoux);
            $mariage->dateNaissanceEpoux = $request->dateNaissanceEpoux ? Carbon::parse($request->dateNaissanceEpoux)->format('Y-m-d') : $mariage->dateNaissanceEpoux;
            $mariage->lieuNaissanceEpoux = $request->input('lieuNaissanceEpoux', $mariage->lieuNaissanceEpoux);
            $mariage->nomEpouse = $request->input('nomEpouse', $mariage->nomEpouse);
            $mariage->prenomEpouse = $request->input('prenomEpouse', $mariage->prenomEpouse);
            $mariage->dateNaissanceEpouse = $request->dateNaissanceEpouse ? Carbon::parse($request->dateNaissanceEpouse)->format('Y-m-d') : $mariage->dateNaissanceEpouse;
            $mariage->lieuNaissanceEpouse = $request->input('lieuNaissanceEpouse', $mariage->lieuNaissanceEpouse);
            $mariage->commune = $request->input('commune', $mariage->commune);
            $mariage->commune_mariage = $request->input('commune_mariage', $mariage->commune_mariage);
            $mariage->CMU = $request->input('CMU', $mariage->CMU);

            // Quantités : si l'utilisateur n'envoie pas de nouvelles valeurs, on conserve les valeurs existantes
            $qtySimpleInput = $request->has('qty_simple') ? (int) $request->input('qty_simple') : null;
            $qtyIntegralInput = $request->has('qty_integral') ? (int) $request->input('qty_integral') : null;

            if ($qtySimpleInput === null && $qtyIntegralInput === null) {
                $qtySimple = (int) $mariage->qty_simple;
                $qtyIntegral = (int) $mariage->qty_integral;
            } else {
                $qtySimple = $qtySimpleInput ?? 0;
                $qtyIntegral = $qtyIntegralInput ?? 0;

                if ($mariage->type === 'simple') {
                    $qtyIntegral = 0;
                    if ($qtySimple <= 0)
                        $qtySimple = max(1, (int) $mariage->qty_simple);
                } elseif ($mariage->type === 'integrale') {
                    $qtySimple = 0;
                    if ($qtyIntegral <= 0)
                        $qtyIntegral = max(1, (int) $mariage->qty_integral);
                } else {
                    if ($qtySimple <= 0 && $qtyIntegral <= 0) {
                        $qtySimple = (int) $mariage->qty_simple ?: 1;
                        $qtyIntegral = (int) $mariage->qty_integral ?: 0;
                    }
                }
            }
            $mariage->qty_simple = $qtySimple;
            $mariage->qty_integral = $qtyIntegral;
            $mariage->quantite = $qtySimple + $qtyIntegral;

            // 8. Gestion de la livraison et du paiement
            $originalChoixOption = $mariage->choix_option;
            $nouveauChoixOption = $request->input('choix_option', $originalChoixOption);

            // Normaliser le choix option
            $nouveauChoixOptionNormalise = strtolower($nouveauChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';
            $originalChoixOptionNormalise = strtolower($originalChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';

            // Calcul du reste à payer comme sur le web
            $user->refresh();
            // Créditer temporairement les timbres gratuits déjà accordés à cette demande pour le calcul
            $anciensTimbresGratuits = (int) $mariage->free_timbres_count;
            if ($anciensTimbresGratuits > 0) {
                $user->free_requests_used = max(0, $user->free_requests_used - $anciensTimbresGratuits);
            }

            // Si la quantité totale ne change pas, on réutilise le nombre de timbres gratuits déjà accordés
            $nouvelleQuantite = $qtySimple + $qtyIntegral;
            if ((int) $mariage->quantite === $nouvelleQuantite) {
                $freeCalc = [
                    'free_timbres' => (int) $mariage->free_timbres_count,
                    'paid_timbres' => $nouvelleQuantite - (int) $mariage->free_timbres_count,
                    'montant_timbre_total' => ($nouvelleQuantite - (int) $mariage->free_timbres_count) * 500,
                    'montant_timbre_gratuit' => (int) $mariage->free_timbres_count * 500,
                ];
            } else {
                $freeCalc = $this->calculateFreeRequestsDiscount($user, $nouvelleQuantite);
            }
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];

            // Si l'option était déjà la livraison, on conserve le montant historique de la base de données
            $montantLivraisonCible = 0;
            if ($nouveauChoixOptionNormalise === 'livraison') {
                $montantLivraisonCible = ($originalChoixOptionNormalise === 'livraison')
                    ? (float) $mariage->montant_livraison
                    : (float) $request->input('montant_livraison', 0);
            }
            $nouveauMontantTotal = $montantTimbreTotal + $montantLivraisonCible;

            // Calcul du montant déjà payé s'il a déjà effectué un paiement
            // États réellement payés (paiement confirmé) : on utilise une liste blanche
            $etatsPayes = ['en attente', 'en cours', 'traité', 'livré', 'terminé', 'complété', 'paye', 'payé'];
            $demandeDejaPayee = in_array(strtolower($mariage->etat), array_map('strtolower', $etatsPayes));
            $ancienMontantPaye = $demandeDejaPayee ? ((float) $mariage->montant_timbre + (float) $mariage->montant_livraison) : 0;

            // Si le montant total ne change pas et que la demande n'est pas encore payée,
            // il n'y a pas de reste à payer (même si ancienMontantPaye = 0)
            if (!$demandeDejaPayee && $nouveauMontantTotal === (float) ($mariage->montant_timbre + $mariage->montant_livraison)) {
                $resteAPayer = 0;
            } else {
                $resteAPayer = $nouveauMontantTotal - $ancienMontantPaye;
            }
            $needsPayment = $resteAPayer > 0;
            $pendingDeliveryData = null;

            if ($needsPayment) {
                if ($nouveauChoixOptionNormalise === 'livraison') {
                    $pendingDeliveryData = [
                        'choix_option' => 'livraison',
                        'montant_timbre' => $montantTimbreTotal,
                        'montant_livraison' => $montantLivraisonCible,
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
                    // Conserver l'ancienne option jusqu'au paiement
                    $mariage->choix_option = $originalChoixOption;
                } else {
                    $pendingDeliveryData = [
                        'choix_option' => 'Retrait sur place',
                        'montant_timbre' => $montantTimbreTotal,
                        'montant_livraison' => 0,
                    ];
                    // Conserver l'ancienne option jusqu'au paiement
                    $mariage->choix_option = $originalChoixOption;
                }
            } else {
                $mariage->choix_option = $nouveauChoixOptionNormalise;
                $mariage->montant_timbre = $montantTimbreTotal;
                $mariage->is_free_request = $freeCalc['free_timbres'] > 0;
                $mariage->free_timbres_count = $freeCalc['free_timbres'];

                if ($nouveauChoixOptionNormalise === 'livraison') {
                    $mariage->montant_livraison = $montantLivraisonCible;
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
                } else {
                    $mariage->montant_livraison = 0;
                    $mariage->nom_destinataire = null;
                    $mariage->prenom_destinataire = null;
                    $mariage->email_destinataire = null;
                    $mariage->contact_destinataire = null;
                    $mariage->adresse_livraison = null;
                    $mariage->code_postal = null;
                    $mariage->ville = null;
                    $mariage->commune_livraison = null;
                    $mariage->quartier = null;
                    $mariage->date_livraison = null;
                    $mariage->heure_livraison = null;
                }
            }

            // Réinitialiser l'état et désactiver la modification
            $mariage->etat = $needsPayment ? $originalData['etat'] : 'en attente';
            $mariage->peut_modifier = false;
            $mariage->champs_a_modifier = null;
            $mariage->motif_de_rejet = null;

            // Gestion de l'initiation du paiement si nécessaire
            if ($needsPayment && $pendingDeliveryData) {
                $totalAmount = $resteAPayer;

                // *** RESTAURER les valeurs ORIGINALES en base (pas de modification avant paiement) ***
                $mariage->pour = $originalData['pour'];
                $mariage->relation = $originalData['relation'];
                $mariage->type = $originalData['type'];
                $mariage->nomEpoux = $originalData['nomEpoux'];
                $mariage->prenomEpoux = $originalData['prenomEpoux'];
                $mariage->dateNaissanceEpoux = $originalData['dateNaissanceEpoux'];
                $mariage->lieuNaissanceEpoux = $originalData['lieuNaissanceEpoux'];
                $mariage->nomEpouse = $originalData['nomEpouse'];
                $mariage->prenomEpouse = $originalData['prenomEpouse'];
                $mariage->dateNaissanceEpouse = $originalData['dateNaissanceEpouse'];
                $mariage->lieuNaissanceEpouse = $originalData['lieuNaissanceEpouse'];
                $mariage->commune = $originalData['commune'];
                $mariage->commune_mariage = $originalData['commune_mariage'];
                $mariage->CMU = $originalData['CMU'];
                $mariage->qty_simple = $originalData['qty_simple'];
                $mariage->qty_integral = $originalData['qty_integral'];
                $mariage->quantite = $originalData['quantite'];
                foreach ($originalData['fichiers'] as $fk => $oldPath) {
                    $mariage->$fk = $oldPath;
                }
                // Conserver l'ancien état, l'ancien montant timbre, et l'ancien statut free
                $mariage->montant_timbre = $originalData['montant_timbre'];
                $mariage->is_free_request = $originalData['is_free_request'];
                $mariage->free_timbres_count = $originalData['free_timbres_count'];
                $mariage->etat = $originalData['etat'];
                $mariage->save();

                // Stocker TOUTES les nouvelles valeurs en cache pour application après paiement
                $newAttributes = [
                    'pour' => $request->input('pour', $originalData['pour']),
                    'relation' => $request->input('relation', $originalData['relation']),
                    'type' => in_array($nouveauTypeMariage, ['simple', 'integrale', 'groupee']) ? $nouveauTypeMariage : $originalData['type'],
                    'nomEpoux' => $request->input('nomEpoux', $originalData['nomEpoux']),
                    'prenomEpoux' => $request->input('prenomEpoux', $originalData['prenomEpoux']),
                    'dateNaissanceEpoux' => $request->dateNaissanceEpoux ? Carbon::parse($request->dateNaissanceEpoux)->format('Y-m-d') : $originalData['dateNaissanceEpoux'],
                    'lieuNaissanceEpoux' => $request->input('lieuNaissanceEpoux', $originalData['lieuNaissanceEpoux']),
                    'nomEpouse' => $request->input('nomEpouse', $originalData['nomEpouse']),
                    'prenomEpouse' => $request->input('prenomEpouse', $originalData['prenomEpouse']),
                    'dateNaissanceEpouse' => $request->dateNaissanceEpouse ? Carbon::parse($request->dateNaissanceEpouse)->format('Y-m-d') : $originalData['dateNaissanceEpouse'],
                    'lieuNaissanceEpouse' => $request->input('lieuNaissanceEpouse', $originalData['lieuNaissanceEpouse']),
                    'commune' => $request->input('commune', $originalData['commune']),
                    'commune_mariage' => $request->input('commune_mariage', $originalData['commune_mariage']),
                    'CMU' => $request->input('CMU', $originalData['CMU']),
                    'qty_simple' => $qtySimple,
                    'qty_integral' => $qtyIntegral,
                    'quantite' => $qtySimple + $qtyIntegral,
                    'is_free_request' => $freeCalc['free_timbres'] > 0,
                    'free_timbres_count' => $freeCalc['free_timbres'],
                ];
                foreach ($nouveauxFichiers as $fk => $newPath) {
                    $newAttributes[$fk] = $newPath;
                }
                \Illuminate\Support\Facades\Cache::put(
                    'pending_modification_update_' . $mariage->reference,
                    ['attributes' => $newAttributes],
                    now()->addDays(7)
                );
                \Illuminate\Support\Facades\Cache::put('pending_delivery_update_' . $mariage->reference, $pendingDeliveryData, now()->addDays(7));

                if ($totalAmount > 0) {
                    $paymentMethod = $request->input('payment_method', 'wave');
                    $transactionReference = $mariage->reference . '-MOD-' . time();
                    $paymentLinkResult = $this->generatePaymentLink($mariage, $totalAmount, $paymentMethod, $transactionReference);

                    if (!$paymentLinkResult['success']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Demande modifiée, mais échec de la génération du lien de paiement.',
                            'error_details' => $paymentLinkResult['error_details']
                        ], 500);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Modifications enregistrées. Elles seront appliquées après confirmation du paiement.',
                        'requires_payment' => true,
                        'free_requests' => [
                            'timbres_gratuits_appliques' => $freeCalc['free_timbres'],
                            'economie' => $freeCalc['montant_timbre_gratuit'],
                            'restants_apres_paiement' => max(0, $this->getRemainingFreeRequests($user) - $freeCalc['free_timbres']),
                        ],
                        'payment_details' => [
                            'payment_url' => $paymentLinkResult['payment_url'] ?? null,
                            'is_ussd_push' => $paymentLinkResult['is_ussd_push'] ?? false,
                            'mtn_ref' => $paymentLinkResult['mtn_ref'] ?? null,
                            'transaction_id' => $paymentLinkResult['generated_transaction_id'] ?? null,
                            'mode' => 'PRODUCTION',
                            'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'] ?? null,
                            'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'] ?? null,
                            'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'] ?? null,
                            'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'] ?? null,
                        ],
                        'data' => [
                            'demande' => $this->formatDemandeResponse($mariage, true)
                        ]
                    ]);
                } else {
                    $mariage->etat = 'en attente';
                    if ($freeCalc['free_timbres'] > 0) {
                        $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                    }
                    $mariage->save();
                    $this->applyPendingDeliveryUpdate($mariage);
                }
            } else {
                if ($freeCalc['free_timbres'] > 0) {
                    $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                }
                $mariage->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Demande modifiée avec succès et soumise à nouveau.',
                'requires_payment' => false,
                'data' => [
                    'demande' => $this->formatDemandeResponse($mariage, true)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@modifierDemande: ' . $e->getMessage() . ' Ligne: ' . $e->getLine(), ['mariage_id' => $mariage->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier uniquement les informations rejetées d'une demande de mariage
     * POST /api/utilisateurs/demandes/mariage/{mariage}/modifier-rejete
     */
    public function modifierDemandeRejete(Request $request, Mariage $mariage): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($mariage->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier que la demande a bien été rejetée et marquée comme modifiable par la mairie
            if (!$mariage->peut_modifier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être modifiée via cette API (elle n\'a pas été marquée comme rejetée/modifiable par la mairie)'
                ], 400);
            }

            // 3. Déterminer les champs rejetés à modifier
            $champsAModifier = json_decode($mariage->champs_a_modifier, true) ?? [];
            if (empty($champsAModifier)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun champ à modifier spécifié par la mairie'
                ], 400);
            }

            // Vérification restrictive : rejeter la modification de champs non spécifiés par la mairie
            $champsDemande = ['type', 'typeDemande', 'pour', 'relation', 'nomEpoux', 'prenomEpoux', 'dateNaissanceEpoux', 'lieuNaissanceEpoux', 'nomEpouse', 'prenomEpouse', 'dateNaissanceEpouse', 'lieuNaissanceEpouse', 'commune', 'commune_mariage', 'qty_simple', 'qty_integral', 'quantite', 'pieceIdentite', 'extraitMariage', 'document_autorisation', 'CMU'];
            $champsEnvoyes = array_keys($request->all());
            $champsNonAutorises = [];

            foreach ($champsEnvoyes as $champ) {
                if (in_array($champ, $champsDemande)) {
                    $isAllowed = false;
                    if (in_array($champ, $champsAModifier)) {
                        $isAllowed = true;
                    }
                    // Gérer les équivalences de type et typeDemande
                    if (($champ === 'typeDemande' || $champ === 'type') && (in_array('type', $champsAModifier) || in_array('typeDemande', $champsAModifier))) {
                        $isAllowed = true;
                    }
                    // Gérer les équivalences de quantité
                    if (($champ === 'qty_simple' || $champ === 'qty_integral') && in_array('quantite', $champsAModifier)) {
                        $isAllowed = true;
                    }
                    if (!$isAllowed) {
                        $champsNonAutorises[] = $champ;
                    }
                }
            }

            if (!empty($champsNonAutorises)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez modifier que les champs rejetés par l\'agent.',
                    'champs_autorises' => $champsAModifier
                ], 400);
            }

            // 4. Définir dynamiquement les règles de validation
            $rules = [];
            foreach ($champsAModifier as $champ) {
                switch ($champ) {
                    case 'typeDemande':
                    case 'type':
                        $rules['typeDemande'] = 'nullable|string|in:simple,integrale,groupee';
                        $rules['type'] = 'nullable|string|in:simple,integrale,groupee';
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
                    case 'nomEpouse':
                        $rules['nomEpouse'] = 'required|string|max:255';
                        break;
                    case 'prenomEpouse':
                        $rules['prenomEpouse'] = 'required|string|max:255';
                        break;
                    case 'dateNaissanceEpouse':
                        $rules['dateNaissanceEpouse'] = 'required|date';
                        break;
                    case 'lieuNaissanceEpouse':
                        $rules['lieuNaissanceEpouse'] = 'required|string|max:255';
                        break;
                    case 'commune':
                        $rules['commune'] = 'required|string';
                        break;
                    case 'quantite':
                        $rules['quantite'] = 'required|integer|min:1|max:10';
                        break;
                    case 'pieceIdentite':
                        $rules['pieceIdentite'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic|max:25600';
                        break;
                    case 'extraitMariage':
                        $rules['extraitMariage'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic|max:25600';
                        break;
                }
            }

            // Toujours permettre de modifier le choix d'option si présent dans la requête
            if ($request->has('choix_option')) {
                $rules['choix_option'] = 'required|in:retrait,livraison';
            }

            // Règles de livraison si choix_option est livraison
            if ($request->input('choix_option') === 'livraison') {
                $rules['payment_method'] = 'required|string|in:wave,orange,mtn,moov,cinetpay,tresorpay';
                $rules['montant_timbre'] = 'required|numeric';
                $rules['montant_livraison'] = 'required|numeric';
                $rules['nom_destinataire'] = 'required|string|max:255';
                $rules['prenom_destinataire'] = 'required|string|max:255';
                $rules['email_destinataire'] = 'nullable|email';
                $rules['contact_destinataire'] = 'required|string|max:20';
                $rules['adresse_livraison'] = 'required|string|max:500';
                $rules['code_postal'] = 'nullable|string|max:10';
                $rules['ville'] = 'nullable|string|max:255';
                $rules['commune_livraison'] = 'nullable|string|max:255';
                $rules['quartier'] = 'nullable|string|max:255';
            }

            // 5. Valider les données
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            // 6. Validation IA Gemini de la pièce d'identité si elle est fournie
            if ($request->hasFile('pieceIdentite')) {
                $geminiService = app(\App\Services\GeminiValidationService::class);
                $validation = $geminiService->validateIdentityDocument($request->file('pieceIdentite'));
                if (!$validation['isValid']) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité a été rejetée par l'IA de la mairie : " . $validation['reason']
                    ], 422);
                }
            }

            // Sauvegarder les anciens chemins pour restauration/comparaison si besoin
            $anciennePieceIdentite = $mariage->pieceIdentite;
            $ancienExtraitMariage = $mariage->extraitMariage;
            $ancienDocumentAutorisation = $mariage->document_autorisation;

            // Valeurs originales en base (avant modification) pour restauration si paiement requis
            $originalData = [
                'pour' => $mariage->pour,
                'relation' => $mariage->relation,
                'type' => $mariage->type,
                'nomEpoux' => $mariage->nomEpoux,
                'prenomEpoux' => $mariage->prenomEpoux,
                'dateNaissanceEpoux' => $mariage->dateNaissanceEpoux,
                'lieuNaissanceEpoux' => $mariage->lieuNaissanceEpoux,
                'nomEpouse' => $mariage->nomEpouse,
                'prenomEpouse' => $mariage->prenomEpouse,
                'dateNaissanceEpouse' => $mariage->dateNaissanceEpouse,
                'lieuNaissanceEpouse' => $mariage->lieuNaissanceEpouse,
                'commune' => $mariage->commune,
                'commune_mariage' => $mariage->commune_mariage,
                'CMU' => $mariage->CMU,
                'qty_simple' => $mariage->qty_simple,
                'qty_integral' => $mariage->qty_integral,
                'quantite' => $mariage->quantite,
                'pieceIdentite' => $anciennePieceIdentite,
                'extraitMariage' => $ancienExtraitMariage,
                'document_autorisation' => $ancienDocumentAutorisation,
                'montant_timbre' => $mariage->montant_timbre,
                'is_free_request' => $mariage->is_free_request,
                'free_timbres_count' => $mariage->free_timbres_count,
                'etat' => $mariage->etat,
            ];

            // 7. Enregistrer les fichiers
            $filesToUpload = [
                'pieceIdentite' => 'identite',
                'extraitMariage' => 'extrait',
                'document_autorisation' => 'autorisations',
            ];

            $nouvellePieceIdentite = $mariage->pieceIdentite;
            $nouvelExtraitMariage = $mariage->extraitMariage;
            $nouveauDocumentAutorisation = $mariage->document_autorisation;

            foreach ($filesToUpload as $fileKey => $subDir) {
                if ($request->hasFile($fileKey)) {
                    if ($mariage->$fileKey && Storage::disk('public')->exists($mariage->$fileKey)) {
                        Storage::disk('public')->delete($mariage->$fileKey);
                    }
                    $file = $request->file($fileKey);
                    $extension = $file->getClientOriginalExtension();
                    $newFileName = (string) Str::uuid() . '.' . $extension;
                    $path = $file->storeAs("images/mariages/$subDir", $newFileName, 'public');
                    $mariage->$fileKey = $path;

                    if ($fileKey === 'pieceIdentite')
                        $nouvellePieceIdentite = $path;
                    if ($fileKey === 'extraitMariage')
                        $nouvelExtraitMariage = $path;
                    if ($fileKey === 'document_autorisation')
                        $nouveauDocumentAutorisation = $path;
                }
            }

            // 8. Mettre à jour uniquement les champs rejetés
            foreach ($champsAModifier as $champ) {
                if (in_array($champ, ['dateNaissanceEpoux', 'dateNaissanceEpouse']) && isset($validated[$champ])) {
                    $mariage->$champ = Carbon::parse($validated[$champ])->format('Y-m-d');
                } elseif ($champ === 'typeDemande' || $champ === 'type') {
                    $mariage->type = $request->input('typeDemande') ?: $request->input('type') ?: $mariage->type;
                } elseif (!in_array($champ, ['pieceIdentite', 'extraitMariage']) && isset($validated[$champ])) {
                    $mariage->$champ = $validated[$champ];
                }
            }

            // Si la quantité a été modifiée ou le type dans les champs rejetés
            if (in_array('quantite', $champsAModifier) || in_array('typeDemande', $champsAModifier) || in_array('type', $champsAModifier)) {
                $mariage->quantite = (int) $request->input('quantite', $mariage->quantite);
                if ($mariage->type === 'integrale') {
                    $qtyIntegral = $mariage->quantite;
                    $qtySimple = 0;
                    $mariage->qty_integral = $qtyIntegral;
                    $mariage->qty_simple = $qtySimple;
                } else {
                    $qtySimple = $mariage->quantite;
                    $qtyIntegral = 0;
                    $mariage->qty_simple = $qtySimple;
                    $mariage->qty_integral = $qtyIntegral;
                }
            }

            // Calcul du reste à payer comme sur le web
            $originalChoixOption = $mariage->choix_option;
            $nouveauChoixOption = $request->input('choix_option', $originalChoixOption);

            // Normaliser le choix option
            $nouveauChoixOptionNormalise = strtolower($nouveauChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';
            $originalChoixOptionNormalise = strtolower($originalChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';

            $user->refresh();
            // Créditer temporairement les timbres gratuits déjà accordés à cette demande pour le calcul
            $anciensTimbresGratuits = (int) $mariage->free_timbres_count;
            if ($anciensTimbresGratuits > 0) {
                $user->free_requests_used = max(0, $user->free_requests_used - $anciensTimbresGratuits);
            }

            // Si la quantité totale ne change pas, on réutilise le nombre de timbres gratuits déjà accordés
            if ((int) $mariage->quantite === (int) $mariage->qty_simple + (int) $mariage->qty_integral) {
                $freeCalc = [
                    'free_timbres' => (int) $mariage->free_timbres_count,
                    'paid_timbres' => (int) $mariage->quantite - (int) $mariage->free_timbres_count,
                    'montant_timbre_total' => ((int) $mariage->quantite - (int) $mariage->free_timbres_count) * 500,
                    'montant_timbre_gratuit' => (int) $mariage->free_timbres_count * 500,
                ];
            } else {
                $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) $mariage->quantite);
            }
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];

            // Si l'option était déjà la livraison, on conserve le montant historique de la base de données
            $montantLivraisonCible = 0;
            if ($nouveauChoixOptionNormalise === 'livraison') {
                $montantLivraisonCible = ($originalChoixOptionNormalise === 'livraison')
                    ? (float) $mariage->montant_livraison
                    : (float) $request->input('montant_livraison', 0);
            }
            $nouveauMontantTotal = $montantTimbreTotal + $montantLivraisonCible;

            // Calcul du montant déjà payé s'il a déjà effectué un paiement
            $etatsPayes = ['en attente', 'en cours', 'traité', 'livré', 'terminé', 'complété', 'paye', 'payé'];
            $demandeDejaPayee = in_array(strtolower($mariage->etat), array_map('strtolower', $etatsPayes));
            $ancienMontantPaye = $demandeDejaPayee ? ((float) $mariage->montant_timbre + (float) $mariage->montant_livraison) : 0;

            if (!$demandeDejaPayee && $nouveauMontantTotal === (float) ($mariage->montant_timbre + $mariage->montant_livraison)) {
                $resteAPayer = 0;
            } else {
                $resteAPayer = $nouveauMontantTotal - $ancienMontantPaye;
            }
            $needsPayment = $resteAPayer > 0;
            $pendingDeliveryData = null;

            if ($needsPayment) {
                if ($nouveauChoixOptionNormalise === 'livraison') {
                    $pendingDeliveryData = [
                        'choix_option' => 'livraison',
                        'montant_timbre' => $montantTimbreTotal,
                        'montant_livraison' => $montantLivraisonCible,
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
                    $mariage->choix_option = $originalChoixOption;
                } else {
                    $pendingDeliveryData = [
                        'choix_option' => 'Retrait sur place',
                        'montant_timbre' => $montantTimbreTotal,
                        'montant_livraison' => 0,
                    ];
                    $mariage->choix_option = $originalChoixOption;
                }
            } else {
                $mariage->choix_option = $nouveauChoixOptionNormalise;
                $mariage->montant_timbre = $montantTimbreTotal;
                $mariage->is_free_request = $freeCalc['free_timbres'] > 0;
                $mariage->free_timbres_count = $freeCalc['free_timbres'];

                if ($nouveauChoixOptionNormalise === 'livraison') {
                    $mariage->montant_livraison = $montantLivraisonCible;
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
                } else {
                    $mariage->montant_livraison = 0;
                    $mariage->nom_destinataire = null;
                    $mariage->prenom_destinataire = null;
                    $mariage->email_destinataire = null;
                    $mariage->contact_destinataire = null;
                    $mariage->adresse_livraison = null;
                    $mariage->code_postal = null;
                    $mariage->ville = null;
                    $mariage->commune_livraison = null;
                    $mariage->quartier = null;
                    $mariage->date_livraison = null;
                    $mariage->heure_livraison = null;
                }
            }

            // Réinitialiser l'état et désactiver la modification
            $mariage->etat = $needsPayment ? $originalData['etat'] : 'en attente';
            $mariage->peut_modifier = false;
            $mariage->champs_a_modifier = null;
            $mariage->motif_de_rejet = null;

            // Gestion de l'initiation du paiement si nécessaire
            if ($needsPayment && $pendingDeliveryData) {
                $totalAmount = $resteAPayer;

                // *** RESTAURER les valeurs ORIGINALES en base (pas de modification avant paiement) ***
                $mariage->pour = $originalData['pour'];
                $mariage->relation = $originalData['relation'];
                $mariage->type = $originalData['type'];
                $mariage->nomEpoux = $originalData['nomEpoux'];
                $mariage->prenomEpoux = $originalData['prenomEpoux'];
                $mariage->dateNaissanceEpoux = $originalData['dateNaissanceEpoux'];
                $mariage->lieuNaissanceEpoux = $originalData['lieuNaissanceEpoux'];
                $mariage->nomEpouse = $originalData['nomEpouse'];
                $mariage->prenomEpouse = $originalData['prenomEpouse'];
                $mariage->dateNaissanceEpouse = $originalData['dateNaissanceEpouse'];
                $mariage->lieuNaissanceEpouse = $originalData['lieuNaissanceEpouse'];
                $mariage->commune = $originalData['commune'];
                $mariage->commune_mariage = $originalData['commune_mariage'];
                $mariage->CMU = $originalData['CMU'];
                $mariage->qty_simple = $originalData['qty_simple'];
                $mariage->qty_integral = $originalData['qty_integral'];
                $mariage->quantite = $originalData['quantite'];
                $mariage->pieceIdentite = $originalData['pieceIdentite'];
                $mariage->extraitMariage = $originalData['extraitMariage'];
                $mariage->document_autorisation = $originalData['document_autorisation'];

                $mariage->montant_timbre = $originalData['montant_timbre'];
                $mariage->is_free_request = $originalData['is_free_request'];
                $mariage->free_timbres_count = $originalData['free_timbres_count'];
                $mariage->etat = $originalData['etat'];
                $mariage->save();

                // Stocker TOUTES les nouvelles valeurs en cache pour application après paiement
                $pendingModificationData = [
                    'attributes' => [
                        'pour' => $request->input('pour', $originalData['pour']),
                        'relation' => $request->input('relation', $originalData['relation']),
                        'type' => in_array($request->input('typeDemande') ?: $request->input('type'), ['simple', 'integrale', 'groupee']) ? ($request->input('typeDemande') ?: $request->input('type')) : $originalData['type'],
                        'nomEpoux' => $request->input('nomEpoux', $originalData['nomEpoux']),
                        'prenomEpoux' => $request->input('prenomEpoux', $originalData['prenomEpoux']),
                        'dateNaissanceEpoux' => $request->dateNaissanceEpoux ? Carbon::parse($request->dateNaissanceEpoux)->format('Y-m-d') : $originalData['dateNaissanceEpoux'],
                        'lieuNaissanceEpoux' => $request->input('lieuNaissanceEpoux', $originalData['lieuNaissanceEpoux']),
                        'nomEpouse' => $request->input('nomEpouse', $originalData['nomEpouse']),
                        'prenomEpouse' => $request->input('prenomEpouse', $originalData['prenomEpouse']),
                        'dateNaissanceEpouse' => $request->dateNaissanceEpouse ? Carbon::parse($request->dateNaissanceEpouse)->format('Y-m-d') : $originalData['dateNaissanceEpouse'],
                        'lieuNaissanceEpouse' => $request->input('lieuNaissanceEpouse', $originalData['lieuNaissanceEpouse']),
                        'commune' => $request->input('commune', $originalData['commune']),
                        'commune_mariage' => $request->input('commune_mariage', $originalData['commune_mariage']),
                        'CMU' => $request->input('CMU', $originalData['CMU']),
                        'qty_simple' => $qtySimple ?? $mariage->qty_simple,
                        'qty_integral' => $qtyIntegral ?? $mariage->qty_integral,
                        'quantite' => (isset($qtySimple) && isset($qtyIntegral)) ? ($qtySimple + $qtyIntegral) : $mariage->quantite,
                        'pieceIdentite' => $nouvellePieceIdentite,
                        'extraitMariage' => $nouvelExtraitMariage,
                        'document_autorisation' => $nouveauDocumentAutorisation,
                        'is_free_request' => $freeCalc['free_timbres'] > 0,
                        'free_timbres_count' => $freeCalc['free_timbres'],
                    ],
                ];

                \Illuminate\Support\Facades\Cache::put(
                    'pending_modification_update_' . $mariage->reference,
                    $pendingModificationData,
                    now()->addDays(7)
                );

                \Illuminate\Support\Facades\Cache::put('pending_delivery_update_' . $mariage->reference, $pendingDeliveryData, now()->addDays(7));

                if ($totalAmount > 0) {
                    $paymentMethod = $request->input('payment_method', 'wave');
                    $transactionReference = $mariage->reference . '-MOD-' . time();
                    $paymentLinkResult = $this->generatePaymentLink($mariage, $totalAmount, $paymentMethod, $transactionReference);

                    if (!$paymentLinkResult['success']) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Demande modifiée, mais échec de la génération du lien de paiement.',
                            'error_details' => $paymentLinkResult['error_details']
                        ], 500);
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Demande modifiée. Utilisez le payment_url pour payer.',
                        'requires_payment' => true,
                        'free_requests' => [
                            'timbres_gratuits_appliques' => $freeCalc['free_timbres'],
                            'economie' => $freeCalc['montant_timbre_gratuit'],
                            'restants_apres_paiement' => max(0, $this->getRemainingFreeRequests($user) - $freeCalc['free_timbres']),
                        ],
                        'payment_details' => [
                            'payment_url' => $paymentLinkResult['payment_url'] ?? null,
                            'is_ussd_push' => $paymentLinkResult['is_ussd_push'] ?? false,
                            'mtn_ref' => $paymentLinkResult['mtn_ref'] ?? null,
                            'transaction_id' => $paymentLinkResult['generated_transaction_id'] ?? null,
                            'mode' => 'PRODUCTION',
                            'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'] ?? null,
                            'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'] ?? null,
                            'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'] ?? null,
                            'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'] ?? null,
                        ],
                        'data' => [
                            'demande' => $this->formatDemandeResponse($mariage, true)
                        ]
                    ]);
                } else {
                    $mariage->etat = 'en attente';
                    if ($freeCalc['free_timbres'] > 0) {
                        $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                    }
                    $mariage->save();

                    // Appliquer la livraison en attente
                    $this->applyPendingDeliveryUpdate($mariage);
                }
            } else {
                if ($freeCalc['free_timbres'] > 0) {
                    $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                }
                $mariage->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Demande modifiée avec succès et soumise à nouveau.',
                'requires_payment' => false,
                'data' => [
                    'demande' => $this->formatDemandeResponse($mariage, true)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@modifierDemandeRejete: ' . $e->getMessage() . ' Ligne: ' . $e->getLine(), ['mariage_id' => $mariage->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les champs à modifier pour une demande rejetée
     * GET /api/utilisateurs/demandes/mariage/{mariage}/champs-a-modifier
     */
    public function getChampsAModifier(Mariage $mariage): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($mariage->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier que la demande peut être modifiée
            if (!$mariage->peut_modifier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être modifiée'
                ], 400);
            }

            // 3. Récupérer les champs à modifier
            $champsNoms = json_decode($mariage->champs_a_modifier, true) ?? [];

            if (empty($champsNoms)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun champ à modifier spécifié'
                ], 400);
            }

            // 4. Mapping des champs avec label et type
            $fieldsMapping = [
                'typeDemande' => ['label' => 'Type de demande', 'type' => 'text'],
                'nomEpoux' => ['label' => 'Nom du conjoint', 'type' => 'text'],
                'prenomEpoux' => ['label' => 'Prénom du conjoint', 'type' => 'text'],
                'dateNaissanceEpoux' => ['label' => 'Date de naissance du conjoint', 'type' => 'date'],
                'lieuNaissanceEpoux' => ['label' => 'Lieu de naissance du conjoint', 'type' => 'text'],
                'pieceIdentite' => ['label' => 'Pièce d\'identité', 'type' => 'file'],
                'extraitMariage' => ['label' => 'Extrait de mariage', 'type' => 'file'],
            ];

            // 5. Construire la réponse avec les valeurs actuelles
            $champsAvecValeurs = [];
            foreach ($champsNoms as $champNom) {
                $fieldInfo = $fieldsMapping[$champNom] ?? ['label' => $champNom, 'type' => 'text'];
                $champsAvecValeurs[$champNom] = [
                    'label' => $fieldInfo['label'],
                    'type' => $fieldInfo['type'],
                    'value' => $mariage->$champNom ?? null,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $mariage->id,
                    'reference' => $mariage->reference,
                    'motif_de_rejet' => $mariage->motif_de_rejet,
                    'champs_a_modifier' => $champsAvecValeurs,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@getChampsAModifier: ' . $e->getMessage(), ['mariage_id' => $mariage->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper pour formater la réponse de la demande (Spécifique au Mariage)
     */
    private function formatDemandeResponse(Mariage $mariage, bool $includeFiles = false)
    {
        // montant_total = timbres + livraison (les timbres sont dus pour retrait ET livraison)
        $montant_total = (float) ($mariage->montant_timbre ?? 0) + (float) ($mariage->montant_livraison ?? 0);

        $data = [
            'id' => $mariage->id,
            'reference' => $mariage->reference,
            'nomEpoux' => $mariage->nomEpoux,
            'prenomEpoux' => $mariage->prenomEpoux,
            'dateNaissanceEpoux' => $mariage->dateNaissanceEpoux,
            'lieuNaissanceEpoux' => $mariage->lieuNaissanceEpoux,
            'commune' => $mariage->commune,
            'etat' => $mariage->etat,
            'choix_option' => $mariage->choix_option,
            'quantite' => (int) ($mariage->quantite ?? 1),
            'montant_timbre_unitaire' => (float) $mariage->montant_timbre,
            'montant_livraison' => (float) $mariage->montant_livraison,
            'montant_total' => $montant_total,
            'created_at' => $mariage->created_at->format('Y-m-d H:i:s'),
            'nom_destinataire' => $mariage->nom_destinataire,
            'prenom_destinataire' => $mariage->prenom_destinataire,
            'email_destinataire' => $mariage->email_destinataire,
            'contact_destinataire' => $mariage->contact_destinataire,
            'adresse_livraison' => $mariage->adresse_livraison,
            'ville' => $mariage->ville,
            'commune_livraison' => $mariage->commune_livraison,
            'quartier' => $mariage->quartier,
            'statut_livraison' => $mariage->statut_livraison,
        ];

        if ($includeFiles) {
            $data['documents'] = [
                'pieceIdentite' => $mariage->pieceIdentite ? Storage::url($mariage->pieceIdentite) : null,
                'extraitMariage' => $mariage->extraitMariage ? Storage::url($mariage->extraitMariage) : null,
            ];
            $data['updated_at'] = $mariage->updated_at->format('Y-m-d H:i:s');
        }

        return $data;
    }

    /**
     * NOUVEAU (Adapté)
     * Gère la notification de webhook de CinetPay pour les mariages
     */
    public function handlePaymentNotification(Request $request): JsonResponse
    {
        Log::info('Webhook CinetPay Reçu (Mariage) - request:', $request->all());

        $cinetpayTransactionId = $request->input('cpm_trans_id')
            ?? $request->input('transaction_id')
            ?? $request->input('data.cpm_trans_id')
            ?? null;

        if (empty($cinetpayTransactionId)) {
            Log::warning('Webhook CinetPay (Mariage): transaction_id manquant dans le webhook.', $request->all());
            return response()->json(['success' => false, 'message' => 'Transaction ID manquant'], 200);
        }

        // Extraire la référence originale
        $reference = $cinetpayTransactionId;
        if (strpos($cinetpayTransactionId, '_') !== false) {
            $parts = explode('_', $cinetpayTransactionId);
            $reference = $parts[0];
        }

        try {
            // ✅ Trouver la demande Mariage
            $mariage = Mariage::where('reference', $reference)->first();

            if (!$mariage) {
                Log::warning("Webhook CinetPay (Mariage): Aucune demande trouvée pour reference {$reference}.");
                return response()->json(['success' => true, 'message' => 'Demande non trouvée'], 200);
            }

            // Vérifier le statut auprès de CinetPay
            $cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548');
            $cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');
            $cinetpayUrl = 'https://api-checkout.cinetpay.com/v2/payment/check';

            $response = Http::withoutVerifying()->post($cinetpayUrl, [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $cinetpayTransactionId,
            ]);

            Log::info("CinetPay (Mariage) check response status: {$response->status()} for transaction {$cinetpayTransactionId}");

            if ($response->failed()) {
                Log::error("Webhook CinetPay (Mariage) {$cinetpayTransactionId}: échec check API.", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['success' => false, 'message' => 'Vérification CinetPay échouée'], 500);
            }

            $verificationData = $response->json();
            Log::info("CinetPay (Mariage) check body for {$cinetpayTransactionId}:", $verificationData);

            // Extraire info
            $data = $verificationData['data'] ?? $verificationData ?? [];
            $status = $data['status'] ?? $data['payment_status'] ?? null;
            $amount = $data['amount'] ?? $data['cpm_amount'] ?? $data['amount_paid'] ?? null;
            $operatorId = $data['cpm_payid'] ?? $data['payid'] ?? $data['pay_id'] ?? null;
            $paymentToken = $data['payment_token'] ?? $data['cpm_token'] ?? null;
            $paymentDate = $data['payment_date'] ?? $data['cpm_trans_date'] ?? $data['created_at'] ?? null;
            $operatorTransId = $data['cpm_trans_id'] ?? $data['transaction_id'] ?? $cinetpayTransactionId;
            $payerName = $data['customer_name'] ?? $data['payer'] ?? ($data['client_name'] ?? null);
            $currency = $data['currency'] ?? 'XOF';

            // Normaliser montant
            $montantFloat = null;
            if (!is_null($amount)) {
                $normalized = preg_replace('/[^\d\.,-]/', '', (string) $amount);
                $normalized = str_replace(',', '.', $normalized);
                if (is_numeric($normalized)) {
                    $montantFloat = (float) $normalized;
                }
            }

            // Si status ACCEPTED
            if (strtoupper($status) === 'ACCEPTED') {
                try {
                    $paiement = Paiement::create([
                        'mariage_id' => $mariage->id, // ✅ Clé étrangère Mariage
                        'user_id' => $mariage->user_id ?? null,
                        'transaction_id' => $operatorTransId,
                        'operator_id' => $operatorId,
                        'payment_token' => $paymentToken,
                        'payer_name' => $payerName,
                        'montant' => $montantFloat,
                        'currency' => $currency,
                        'status' => 'ACCEPTED',
                        'paid_at' => $paymentDate ? Carbon::parse($paymentDate) : now(),
                        'raw_response' => $verificationData,
                    ]);
                    Log::info("Paiement (Mariage) enregistré pour {$cinetpayTransactionId}, id paiement: {$paiement->id}");
                } catch (\Exception $e) {
                    Log::error("Erreur enregistrement paiement (Mariage) pour {$cinetpayTransactionId}: " . $e->getMessage(), [
                        'exception' => $e
                    ]);
                }

                // Mettre à jour le Mariage
                $this->applyPendingDeliveryUpdate($mariage);
                $mariage->etat = 'en attente';
                if ($mariage->choix_option === 'livraison') {
                    $mariage->statut_livraison = 'en attente';
                }
                $mariage->save();

                Log::info("Demande (Mariage) {$cinetpayTransactionId} mise à jour : en attente");

                // Envoi des notifications suite au paiement réussi
                try {
                    $user = $mariage->user;
                    if ($user) {
                        $yellikaSmsService = app(YellikaSmsService::class);
                        $phoneNumber = $user->indicatif . $user->contact;
                        $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de mariage a été confirmé. Référence : {$mariage->reference}.
Votre demande est maintenant en attente de traitement.";
                        $yellikaSmsService->sendSms($phoneNumber, $message);

                        Notification::send($user, new DemandeMariageConfirmationNotification($user, $mariage));
                    }
                } catch (\Exception $e) {
                    Log::error("Erreur notifications Webhook Mariage: " . $e->getMessage());
                }

                return response()->json(['success' => true, 'message' => 'Paiement accepté et traité'], 200);
            }

            // Si status différent
            $upper = strtoupper((string) $status);
            if ($upper === 'PENDING' || $upper === 'AWAITING') {
                $mariage->etat = 'en attente de paiement';
                $mariage->statut_livraison = 'en attente de paiement'; // Corrigé (ou 'en attente' selon votre logique)
                $mariage->save();
                Log::info("Demande (Mariage) {$cinetpayTransactionId} marquée en attente (CinetPay status: {$status})");
                return response()->json(['success' => true, 'message' => 'Paiement en attente'], 200);
            }

            // Pour REFUSED ou autres
            $mariage->etat = 'paiement_echoue';
            $mariage->statut_livraison = 'paiement_echoue';
            $mariage->save();
            Log::warning("Demande (Mariage) {$cinetpayTransactionId} paiement non accepté (status: {$status}).");
            return response()->json(['success' => true, 'message' => 'Paiement non accepté traité'], 200);
        } catch (\Exception $e) {
            Log::error("Webhook CinetPay (Mariage) {$cinetpayTransactionId}: Exception critique : " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur interne'], 500);
        }
    }

    /**
     * NOUVEAU (Adapté)
     * API pour vérifier le statut d'un paiement de mariage.
     * GET /api/mariage/payment-status/{reference}
     */
    public function getPaymentStatus(Request $request, $reference): JsonResponse
    {
        try {
            // Extraire la vraie référence s'il s'agit d'une modification
            $baseReference = $reference;
            if (str_contains($reference, '-MOD-')) {
                $baseReference = explode('-MOD-', $reference)[0];
            }

            // 1. Trouver la demande de mariage
            $mariage = Mariage::where('reference', $baseReference)->first();

            if (!$mariage) {
                return response()->json(['status' => 'not_found', 'message' => 'Demande non trouvée'], 404);
            }

            // Vérification en direct du statut MTN si applicable
            if ($mariage->etat === 'en attente de paiement' || $mariage->etat === 'non_paye' || $mariage->etat === 'paiement_en_attente') {
                $mtnRef = \Illuminate\Support\Facades\Cache::get('mtn_ref_' . $reference);
                if ($mtnRef) {
                    $mtnService = new \App\Services\MtnService();
                    $statusInfo = $mtnService->getTransactionStatus($mtnRef);
                    if ($statusInfo && isset($statusInfo['status'])) {
                        $status = $statusInfo['status'];
                        if ($status === 'SUCCESSFUL') {
                            $paiement = Paiement::where('transaction_id', $reference)->where('operator_id', 'MTN')->first();
                            if (!$paiement) {
                                $baseReference = $reference;
                                $isModification = str_contains($reference, '-MOD-');
                                if ($isModification) {
                                    $baseReference = explode('-MOD-', $reference)[0];
                                }

                                $montantTimbre = (float) ($mariage->montant_timbre ?? 0);
                                $montantLivraison = (float) ($mariage->montant_livraison ?? 0);

                                if ($isModification) {
                                    $cacheKey = 'pending_delivery_update_' . $mariage->reference;
                                    if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                                        $pendingData = \Illuminate\Support\Facades\Cache::get($cacheKey);
                                        $nouveauMontantTimbre = (float) ($pendingData['montant_timbre'] ?? 0);
                                        $nouveauMontantLivraison = (float) ($pendingData['montant_livraison'] ?? 0);
                                        $nouveauMontantTotal = $nouveauMontantTimbre + $nouveauMontantLivraison;
                                    } else {
                                        $nouveauMontantTotal = $montantTimbre + $montantLivraison;
                                    }

                                    $dejaPaye = Paiement::where("mariage_id", $mariage->id)
                                        ->where('status', 'ACCEPTED')
                                        ->where('transaction_id', '!=', $reference)
                                        ->sum('montant');

                                    $defaultAmount = max(0.0, $nouveauMontantTotal - $dejaPaye);
                                } else {
                                    $defaultAmount = $montantTimbre + $montantLivraison;
                                }

                                $amount = isset($statusInfo['amount']) ? (float) $statusInfo['amount'] : $defaultAmount;

                                $partTimbre = 0;
                                $partLivraison = 0;

                                if ($isModification) {
                                    $dejaPayeTimbre = Paiement::where("mariage_id", $mariage->id)
                                        ->where('status', 'ACCEPTED')
                                        ->where('transaction_id', '!=', $reference)
                                        ->get()
                                        ->sum(function ($p) {
                                            return (float) ($p->raw_response['part_timbre'] ?? 0);
                                        });

                                    $dejaPayeLivraison = Paiement::where("mariage_id", $mariage->id)
                                        ->where('status', 'ACCEPTED')
                                        ->where('transaction_id', '!=', $reference)
                                        ->get()
                                        ->sum(function ($p) {
                                            return (float) ($p->raw_response['part_livraison'] ?? 0);
                                        });

                                    $cacheKey = 'pending_delivery_update_' . $mariage->reference;
                                    if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                                        $pendingData = \Illuminate\Support\Facades\Cache::get($cacheKey);
                                        $nouveauMontantLivraison = (float) ($pendingData['montant_livraison'] ?? 0);

                                        $partLivraison = max(0.0, $nouveauMontantLivraison - $dejaPayeLivraison);
                                        $partTimbre = max(0.0, $amount - $partLivraison);
                                    } else {
                                        $partTimbre = $amount;
                                        $partLivraison = 0.0;
                                    }
                                } else {
                                    $partTimbre = (float) ($mariage->montant_timbre ?? 0);
                                    $partLivraison = (float) ($mariage->montant_livraison ?? 0);
                                }

                                Paiement::create([
                                    'user_id' => $mariage->user_id,
                                    'transaction_id' => $reference,
                                    'operator_id' => 'MTN',
                                    'montant' => $amount,
                                    'currency' => $statusInfo['currency'] ?? 'XOF',
                                    'status' => 'ACCEPTED',
                                    'paid_at' => now(),
                                    "mariage_id" => $mariage->id,
                                    'raw_response' => [
                                        'part_timbre' => $partTimbre,
                                        'part_livraison' => $partLivraison,
                                        'is_modification' => $isModification,
                                        'mtn_status_info' => $statusInfo
                                    ]
                                ]);

                                $this->applyPendingDeliveryUpdate($mariage);
                                $mariage->etat = 'en attente';
                                if ($mariage->choix_option === 'livraison') {
                                    $mariage->statut_livraison = 'en attente';
                                }
                                $mariage->save();

                                $this->incrementFreeRequestsFromDemande($mariage);

                                $user = \App\Models\User::find($mariage->user_id);
                                if ($user) {
                                    try {
                                        $yellikaSmsService = app(\App\Services\YellikaSmsService::class);
                                        $phoneNumber = $user->indicatif . $user->contact;
                                        $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de mariage a été confirmé. Référence : {$mariage->reference}. Votre demande est maintenant en attente de traitement.";
                                        $yellikaSmsService->sendSms($phoneNumber, $message);

                                        \Illuminate\Support\Facades\Notification::send($user, new \App\Notifications\DemandeMariageConfirmationNotification($user, $mariage));
                                    } catch (\Exception $e) {
                                        Log::error("Erreur notifications MTN API: " . $e->getMessage());
                                    }
                                }
                            }
                        } elseif ($status === 'FAILED') {
                            $mariage->etat = 'paiement_echoue';
                            $mariage->save();
                        }
                    }
                }
            }

            // 2. Calculer le montant total
            $montant_total_timbres = (float) ($mariage->montant_timbre ?? 0) * (int) ($mariage->quantite ?? 1);
            $montant_total = $mariage->choix_option === 'livraison'
                ? $montant_total_timbres + (float) ($mariage->montant_livraison ?? 0)
                : 0;

            // 3. Déterminer la date et l'heure
            $date_heure = $mariage->created_at->format('Y-m-d H:i:s');

            if ($mariage->etat === 'en attente') {
                // Si payé, chercher la date de paiement
                $paiement = Paiement::where('mariage_id', $mariage->id) // ✅ Clé étrangère Mariage
                    ->where('status', 'ACCEPTED')
                    ->orderBy('paid_at', 'desc')
                    ->first();
                if ($paiement && $paiement->paid_at) {
                    $date_heure = Carbon::parse($paiement->paid_at)->format('Y-m-d H:i:s');
                } else {
                    $date_heure = $mariage->updated_at->format('Y-m-d H:i:s');
                }
            } elseif ($mariage->etat === 'paiement_echoue') {
                $date_heure = $mariage->updated_at->format('Y-m-d H:i:s');
            }

            // 4. Construire le JSON de réponse
            $responseData = [
                'status' => $mariage->etat,
                'data' => [
                    'type_document' => 'Acte de mariage', // ✅ Type Mariage
                    'quantite' => (int) $mariage->quantite,
                    'montant' => $montant_total,
                    'date_heure' => $date_heure,
                    'id_transaction' => $mariage->reference
                ]
            ];

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error("Erreur getPaymentStatus (Mariage) pour {$reference}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * NOUVEAU (Générique)
     * Affiche la page de redirection/statut après le paiement.
     */
    public function showRedirectPage(Request $request, $transactionId = null)
    {
        $transactionId = $transactionId ?? $request->input('transactionId') ?? $request->input('transaction_id');

        if (!$transactionId) {
            return view('payments.redirect_to_app', ['transactionId' => null]);
        }

        return view('payments.redirect_to_app', [
            'transactionId' => $transactionId
        ]);
    }

    /**
     * Supprimer une demande de mariage
     * DELETE /api/utilisateurs/demandes/mariage/{mariage}
     */
    public function destroy(Mariage $mariage): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier que l'utilisateur est propriétaire de la demande
            if ($mariage->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à supprimer cette demande'
                ], 403);
            }

            // 2. Vérifier l'état de la demande (SÉCURITÉ AJOUTÉE)
            // Normalisation : minuscule et suppression des espaces pour la comparaison
            $etatActuel = mb_strtolower(trim($mariage->etat), 'UTF-8');

            // Liste des états interdits (avec et sans accents pour être sûr)
            $etatsInterdits = ['reçu', 'terminé', 'recu', 'termine', 'réçu'];

            if (in_array($etatActuel, $etatsInterdits)) {
                return response()->json([
                    'success' => false,
                    'message' => "Impossible de supprimer cette demande car elle est déjà en traitement ou finalisée. (État actuel : {$mariage->etat})"
                ], 400);
            }

            // Optionnel: Supprimer les fichiers physiques si nécessaire
            // Storage::disk('public')->delete([
            //     $mariage->pieceIdentite,
            //     $mariage->extraitMariage,
            // ]);

            // 3. Suppression
            $mariage->delete();

            return response()->json([
                'success' => true,
                'message' => 'Demande de mariage supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la demande'
            ], 500);
        }
    }
}
