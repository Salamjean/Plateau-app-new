<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use Illuminate\Http\JsonResponse;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Services\YellikaSmsService;
use App\Notifications\DemandeDecesConfirmationNotification;
use Illuminate\Support\Facades\Notification;
use App\Traits\HandlesFreeRequests;

class DemandeDecesController extends Controller
{
    use HandlesFreeRequests;

    /**
     * Liste des demandes de décès de l'utilisateur
     * GET /api/utilisateurs/demandes/deces
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $deces = Deces::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des demandes de décès récupérée avec succès',
                'data' => [
                    'demandes' => $deces->map(function ($demande) {
                        // Utilise l'helper pour la consistance
                        return $this->formatDemandeResponse($demande, true);
                    })
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des demandes'
            ], 500);
        }
    }

    /**
     * Créer une nouvelle demande de décès
     * POST /api/utilisateurs/demandes/deces
     *
     * @param Request $request
     * @param YellikaSmsService $yellikaSmsService
     * @return JsonResponse
     */
    public function store(Request $request, YellikaSmsService $yellikaSmsService, \App\Services\WaveService $waveService): JsonResponse
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'numberR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',
            'dateR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',

            // --- AJOUT --- (Basé sur DecesController.php)
            'qty_simple' => 'nullable|integer|min:0|max:10',
            'qty_integral' => 'nullable|integer|min:0|max:10',

            'CNIdfnt' => 'required',
            'CNIdcl' => 'required',
            'documentMariage' => 'nullable',
            'RequisPolice' => 'nullable',
            'choix_option' => 'required|in:retrait,livraison',
            'communeD' => 'nullable|string|max:255',
            'commune_deces' => 'required|string|max:255',
            'payment_method' => 'required|string|in:wave,orange,mtn,moov,cinetpay',
            'mtn_number' => 'required_if:payment_method,mtn|nullable|string|regex:/^05[0-9]{8}$/',
            'pour' => 'nullable|string|max:255',
            'relation' => 'nullable|string|in:enfant,parent,connaissance',
            'document_autorisation' => 'required_if:relation,connaissance|nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',

            // Note : montant_timbre est maintenant le PRIX UNITAIRE
            'montant_timbre' => 'required_if:choix_option,livraison|numeric',
            'montant_livraison' => 'required_if:choix_option,livraison|numeric',

            'nom_destinataire' => 'required_if:choix_option,livraison|string|max:255',
            'prenom_destinataire' => 'required_if:choix_option,livraison|string|max:255',
            'email_destinataire' => 'nullable|email',
            'contact_destinataire' => 'required_if:choix_option,livraison|string|max:20',
            'adresse_livraison' => 'required_if:choix_option,livraison|string|max:500',
            'code_postal' => 'nullable|string|max:10',
            'ville' => 'nullable|string|max:255',
            'commune_livraison' => 'nullable|string|max:255',
            'quartier' => 'nullable|string|max:255',
            'nom_prenoms_pere' => 'nullable|string|max:255',
            'nom_prenoms_mere' => 'nullable|string|max:255',
        ], [
            'mtn_number.required_if' => 'Le numéro MTN est obligatoire lorsque le moyen de paiement choisi est MTN.',
            'mtn_number.regex' => 'Le numéro MTN doit comporter exactement 10 chiffres et commencer par 05.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
        }

        try {
            $user = Auth::user();

            // 2. Upload des fichiers
            $filesToUpload = [
                'CNIdfnt' => 'cnid',
                'CNIdcl' => 'cnid',
                'documentMariage' => 'mariage',
                'RequisPolice' => 'police',
                'document_autorisation' => 'autorisations',
            ];
            $uploadedPaths = [];
            foreach ($filesToUpload as $fileKey => $subDir) {
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    $extension = $file->getClientOriginalExtension();
                    $newFileName = (string) Str::uuid() . '.' . $extension;
                    $path = $file->storeAs("images/deces/$subDir", $newFileName, 'public');
                    $uploadedPaths[$fileKey] = $path;
                }
            }

            // 3. Génération de référence
            $communeInitiale = strtoupper(substr($request->communeD ?: $user->commune ?: 'X', 0, 1));
            $anneeCourante = Carbon::now()->year;
            $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $increment = Deces::max('id') + 1;
            $reference = 'AD' . $randomDigits . $increment . $communeInitiale . $anneeCourante;

            // 4. Création de la demande
            $deces = new Deces();
            $deces->type = $request->input('typeDemande'); // Harmonisation (simple, integrale, groupee)
            $deces->name = $request->name;
            $deces->numberR = $request->numberR;
            $deces->dateR = $request->dateR ? Carbon::parse($request->dateR)->format('Y-m-d') : null;

            // --- AJOUT ---
            $qtySimple = (int) $request->input('qty_simple', 0);
            $qtyIntegral = (int) $request->input('qty_integral', 0);
            if ($qtySimple === 0 && $qtyIntegral === 0) {
                $type = $request->input('typeDemande');
                if ($type === 'integrale') {
                    $qtyIntegral = 1;
                } else {
                    $qtySimple = 1;
                }
            }
            $totalQuantity = $qtySimple + $qtyIntegral;

            $deces->qty_simple = $qtySimple;
            $deces->qty_integral = $qtyIntegral;
            $deces->quantite = $totalQuantity;

            $deces->CNIdfnt = $uploadedPaths['CNIdfnt'] ?? null;
            $deces->CNIdcl = $uploadedPaths['CNIdcl'] ?? null;
            $deces->documentMariage = $uploadedPaths['documentMariage'] ?? null;
            $deces->RequisPolice = $uploadedPaths['RequisPolice'] ?? null;
            $deces->pour = $request->pour;
            $deces->relation = $request->relation;
            $deces->document_autorisation = $uploadedPaths['document_autorisation'] ?? null;
            // Normalisation : le mobile envoie 'retrait'/'livraison' (minuscules)
            // mais le backend attend 'Retrait sur place'/'Livraison' (comme le web)
            $deces->choix_option = strtolower($request->choix_option) === 'livraison'
                ? 'livraison'
                : 'Retrait sur place';
            $deces->commune = $request->communeD ?: $user->commune;
            $deces->commune_deces = $request->commune_deces;
            $deces->user_id = $user->id;
            $deces->reference = $reference;

            // --- GESTION DES DEMANDES GRATUITES ---
            $user->refresh();
            $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) $deces->quantite);
            Log::info("Demandes gratuites - Deces (API) {$deces->reference}: {$freeCalc['free_timbres']} timbres gratuits, {$freeCalc['paid_timbres']} payants");

            // Calcul unifié du montant des timbres (indépendant du mode retrait/livraison)
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];
            $deces->montant_timbre = $montantTimbreTotal;
            $deces->is_free_request = $freeCalc['free_timbres'] > 0;
            $deces->free_timbres_count = $freeCalc['free_timbres'];

            if ($deces->choix_option === 'livraison') {
                $montantLivraison = (float) $request->montant_livraison;
                $totalAmount = $montantTimbreTotal + $montantLivraison;

                $deces->montant_livraison = $montantLivraison;
                $deces->nom_destinataire = $request->nom_destinataire;
                $deces->prenom_destinataire = $request->prenom_destinataire;
                $deces->email_destinataire = $request->email_destinataire;
                $deces->contact_destinataire = $request->contact_destinataire;
                $deces->adresse_livraison = $request->adresse_livraison;
                $deces->code_postal = $request->code_postal;
                $deces->ville = $request->ville;
                $deces->commune_livraison = $request->commune_livraison;
                $deces->quartier = $request->quartier;

                if ($totalAmount > 0) {
                    $deces->etat = 'en attente de paiement';
                    $deces->statut_livraison = 'en attente de paiement';
                } else {
                    $deces->etat = 'en attente';
                    $deces->statut_livraison = null;
                }
            } else {
                // Retrait sur place : les timbres sont toujours payants (comme sur le web)
                $totalAmount = $montantTimbreTotal;
                $deces->statut_livraison = null;

                if ($totalAmount > 0) {
                    $deces->etat = 'en attente de paiement';
                } else {
                    $deces->etat = 'en attente';
                }
            }

            $deces->save();

            // 5. Réponse conditionnelle (Cas "Retrait" ou "Gratuit Livraison")
            if ($totalAmount == 0) {
                // Incrémenter le compteur SEULEMENT maintenant (pas de paiement requis)
                if ($freeCalc['free_timbres'] > 0) {
                    $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                }

                // Envoi des notifications (SMS & Email)
                try {
                    $phoneNumber = $user->indicatif . $user->contact;
                    $message = "Bonjour {$user->name}, votre demande d'extrait de décès a bien été transmise à la mairie du plateau. Référence : {$deces->reference}.
Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com/home/search";
                    $yellikaSmsService->sendSms($phoneNumber, $message);

                    Notification::send($user, new DemandeDecesConfirmationNotification($user, $deces));
                } catch (\Exception $e) {
                    Log::error("Erreur notifications DemandeDeces (API): " . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Demande de décès créée avec succès (gratuite ou retrait sur place)',
                    'requires_payment' => false,
                    'free_requests' => [
                        'timbres_gratuits_appliques' => $freeCalc['free_timbres'],
                        'economie' => $freeCalc['montant_timbre_gratuit'],
                        'restants' => $this->getRemainingFreeRequests($user),
                    ],
                    'data' => ['demande' => $this->formatDemandeResponse($deces)]
                ], 201);
            }

            // --- DEBUT DE LA LOGIQUE DE PAIEMENT ---

            // 6. Générer le lien de paiement (Wave ou CinetPay)
            $paymentMethod = $request->input('payment_method');
            $paymentLinkResult = $this->generatePaymentLink($deces, $totalAmount, $paymentMethod);

            // 7. Gérer l'échec de la génération de lien
            if (!$paymentLinkResult['success']) {
                // La demande est créée, mais le lien a échoué. L'utilisateur pourra réessayer.
                return response()->json([
                    'success' => false,
                    'message' => 'Demande créée, mais échec de la génération du lien de paiement. Veuillez réessayer.',
                    'error_details' => $paymentLinkResult['error_details']
                ], 500);
            }

            // 8. Succès ! Construire la réponse
            $msg = strtolower($paymentMethod) === 'mtn'
                ? 'Demande créée. Un message de validation de paiement (Push USSD) a été envoyé sur votre numéro MTN pour finaliser le paiement.'
                : 'Demande créée. Utilisez le payment_url pour payer.';

            return response()->json([
                'success' => true,
                'payment_method' => $paymentMethod,
                'mtn_number' => $request->input('mtn_number'),
                'message' => $msg,
                'requires_payment' => true,
                'free_requests' => [
                    'timbres_gratuits_appliques' => $freeCalc['free_timbres'],
                    'economie' => $freeCalc['montant_timbre_gratuit'],
                    'restants_apres_paiement' => max(0, $this->getRemainingFreeRequests($user) - $freeCalc['free_timbres']),
                ],
                'payment_details' => [
                    'payment_url' => $paymentLinkResult['payment_url'],
                    'transaction_id' => $paymentLinkResult['generated_transaction_id'],
                    'mode' => 'PRODUCTION',
                    'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'],
                    'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'],
                    'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'],
                    'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'],
                ],

                'data' => [
                    'demande' => $this->formatDemandeResponse($deces)
                ]
            ], 201);

            // --- FIN DE LA LOGIQUE MISE A JOUR ---

        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@store: ' . $e->getMessage() . ' Ligne: ' . $e->getLine());
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
     * Génère un nouveau lien de paiement (Wave ou CinetPay) pour une demande de décès existante.
     */
    private function generatePaymentLink(Deces $deces, $totalAmount, $paymentMethod): array
    {
        try {
            // 1. Préparer les URLs
            $baseUrl = config('app.url');
            $returnUrl = "plateauapps://app/payment-result?method={$paymentMethod}&status=success&transactionId={$deces->reference}";
            $cancelUrl = "plateauapps://app/payment-result?method={$paymentMethod}&status=cancel&transactionId={$deces->reference}";
            $fallbackReturnUrl = $baseUrl . "/user/payment/success?reference=" . urlencode($deces->reference) . "&type=deces";
            $fallbackCancelUrl = $baseUrl . "/user/payment/cancel?reference=" . urlencode($deces->reference) . "&type=deces";

            // Si c'est Wave, utiliser le service Wave
            if (strtolower($paymentMethod) === 'wave') {
                $waveService = app(\App\Services\WaveService::class);
                $checkoutSession = $waveService->createCheckoutSession(
                    $totalAmount,
                    'XOF',
                    $fallbackReturnUrl,
                    $fallbackCancelUrl,
                    $deces->reference
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
                    'generated_transaction_id' => $deces->reference,
                    'return_url_deep_link' => $returnUrl,
                    'cancel_url_deep_link' => $cancelUrl,
                    'return_url_web_fallback' => $fallbackReturnUrl,
                    'cancel_url_web_fallback' => $fallbackCancelUrl,
                ];
            }

            // Si c'est MTN, utiliser MTN MoMo API en direct (MtnService)
            if (strtolower($paymentMethod) === 'mtn') {
                $mtnPhoneNumber = request()->input('mtn_number') ?: $deces->contact_destinataire ?: (auth()->check() ? auth()->user()->contact : '');

                // Formater le numéro
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
                    // Stocker le ReferenceId en cache pour la vérification
                    \Illuminate\Support\Facades\Cache::put('mtn_ref_' . $deces->reference, $response['referenceId'], now()->addHours(1));

                    return [
                        'success' => true,
                        'payment_url' => null, // Pas de lien de redirection pour le push USSD
                        'is_ussd_push' => true,
                        'mtn_ref' => $response['referenceId'],
                        'generated_transaction_id' => $deces->reference,
                        'return_url_deep_link' => $returnUrl,
                        'cancel_url_deep_link' => $cancelUrl,
                        'return_url_web_fallback' => $fallbackReturnUrl,
                        'cancel_url_web_fallback' => $fallbackCancelUrl,
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Échec de l\'initiation du paiement MTN direct (Push USSD).',
                    'error_details' => $response
                ];
            }

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
                'transaction_id' => $deces->reference,
                'amount' => $totalAmount,
                'currency' => 'XOF',
                'description' => "Paiement pour " . $deces->reference,
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
                        'generated_transaction_id' => $deces->reference,
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
            Log::error('Exception in generatePaymentLink: ' . $e->getMessage(), ['reference' => $deces->reference]);
            return [
                'success' => false,
                'message' => 'Erreur interne lors de la génération du lien: ' . $e->getMessage(),
                'error_details' => null
            ];
        }
    }


    // --------------------------------------------------------------------
    // NOUVELLE MÉTHODE PUBLIQUE (Votre Endpoint)
    // --------------------------------------------------------------------
    /**
     * Retente le paiement pour une demande de décès échouée.
     * POST /api/utilisateurs/demandes/deces/{deces}/retry-payment
     */
    public function retryPayment(Request $request, Deces $deces): JsonResponse
    {
        // 0. Validation de la méthode et du numéro MTN
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string|in:wave,orange,mtn,moov,cinetpay',
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
            if ($deces->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier si un montant est dû (timbres + livraison éventuelle)
            $totalAmountDue = (float) $deces->montant_timbre + (float) $deces->montant_livraison;
            if ($totalAmountDue <= 0) {
                return response()->json(['success' => false, 'message' => 'Cette demande ne nécessite pas de paiement (montant nul).'], 400);
            }

            // 3. Vérifier l'état
            // On autorise la régénération si le paiement a échoué OU s'il est encore en attente (l'utilisateur a peut-être perdu le lien)
            if (!in_array($deces->etat, ['paiement_echoue', 'en attente de paiement'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être payée à nouveau (état actuel: ' . $deces->etat . ')'
                ], 400);
            }

            // 4. Mettre à jour l'état (avant de générer le lien)
            // L'état 'en attente' sera défini par le webhook si le paiement réussit.
            $deces->etat = 'paiement_echoue';
            $deces->statut_livraison = 'paiement_echoue';
            $deces->save();

            // 5. Générer le nouveau lien de paiement (par défaut Wave vu qu'on a pas de paramètre save)
            $paymentMethod = $request->input('payment_method', 'wave'); // Valeur par défaut si non passée
            $totalAmount = $totalAmountDue; // Déjà calculé ci-dessus
            $paymentLinkResult = $this->generatePaymentLink($deces, $totalAmount, $paymentMethod);

            // 6. Gérer l'échec de la génération de lien
            if (!$paymentLinkResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de la génération du nouveau lien de paiement.',
                    'error_details' => $paymentLinkResult['error_details']
                ], 500);
            }

            // 7. Succès ! Construire la réponse
            $msg = strtolower($paymentMethod) === 'mtn'
                ? 'Un message de validation de paiement (Push USSD) a été envoyé sur votre numéro MTN pour finaliser le paiement.'
                : 'Nouveau lien de paiement généré. Utilisez le payment_url pour payer.';

            return response()->json([
                'success' => true,
                'message' => $msg,
                'requires_payment' => true,

                'payment_details' => [
                    'payment_url' => $paymentLinkResult['payment_url'],
                    'transaction_id' => $paymentLinkResult['generated_transaction_id'],
                    'mode' => 'PRODUCTION',
                    'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'],
                    'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'],
                    'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'],
                    'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'],
                ],

                'data' => [
                    'demande' => $this->formatDemandeResponse($deces)
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@retryPayment: ' . $e->getMessage(), ['deces_id' => $deces->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne lors de la tentative de paiement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function relancerDemande(Request $request, Deces $deces): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($deces->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier l'état
            if ($deces->etat !== 'rejetée') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être relancée (état actuel: ' . $deces->etat . ')'
                ], 400);
            }

            // 3. Mettre à jour l'état
            $deces->etat = 'en attente';

            if ($deces->choix_option === 'livraison') {
                $deces->statut_livraison = 'en attente';
            } else {
                $deces->statut_livraison = null;
            }

            // --- MISE À JOUR DEMANDÉE ---
            // Force la date de création à être la date actuelle.
            // Le updated_at sera aussi mis à jour automatiquement.
            $deces->created_at = now();
            // --- FIN DE L'AJOUT ---

            $deces->save();

            // 4. Succès ! Construire la réponse
            return response()->json([
                'success' => true,
                'message' => 'Demande relancée avec succès. Elle est maintenant "en attente".',
                'data' => [
                    'demande' => $this->formatDemandeResponse($deces)
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@relancerDemande: ' . $e->getMessage(), ['deces_id' => $deces->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne lors de la relance de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier une demande rejetée
     * POST /api/utilisateurs/demandes/deces/{deces}/modifier
     */
    public function modifierDemande(Request $request, Deces $deces): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($deces->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier si la demande a été rejetée (peut_modifier == true ou etat == 'rejetée')
            if ($deces->peut_modifier || $deces->etat === 'rejetée') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande a été rejetée. Veuillez utiliser l\'API de modification des informations rejetées pour corriger les champs requis.'
                ], 400);
            }

            // 3. Vérifier que la demande est libre de modification (agent_id est NULL)
            $isNotAssigned = is_null($deces->agent_id);
            if (!$isNotAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être modifiée complètement (déjà attribuée à un agent)'
                ], 400);
            }

            // 3. Règles de validation pour modification complète
            $rules = [
                'type' => 'required',
                'pour' => 'nullable|string',
                'relation' => 'nullable|string|in:enfant,parent,connaissance',
                'document_autorisation' => 'required_if:relation,connaissance|nullable|file|mimes:jpeg,png,jpg,pdf|max:25600',
                'name' => 'required|string|max:255',
                'numberR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere|nullable|string|max:50',
                'dateR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere|nullable|date',
                'nom_prenoms_pere' => 'nullable|string|max:255',
                'nom_prenoms_mere' => 'nullable|string|max:255',
                'qty_simple' => 'nullable|integer|min:0|max:10',
                'qty_integral' => 'nullable|integer|min:0|max:10',
                'CNIdfnt' => $deces->CNIdfnt ? 'nullable' : 'required',
                'CNIdcl' => $deces->CNIdcl ? 'nullable' : 'required',
                'documentMariage' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:25600',
                'RequisPolice' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:25600',
                'commune' => 'required|string',
                'commune_deces' => 'required|string|max:255',
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

            // 5. Validation IA Gemini des pièces d'identité si fournies
            $geminiService = app(\App\Services\GeminiValidationService::class);
            if ($request->hasFile('CNIdfnt')) {
                $validation = $geminiService->validateIdentityDocument($request->file('CNIdfnt'));
                if (!$validation['isValid']) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité du défunt (CNIdfnt) a été rejetée par l'IA : " . $validation['reason']
                    ], 422);
                }
            }

            if ($request->hasFile('CNIdcl')) {
                $validation = $geminiService->validateIdentityDocument($request->file('CNIdcl'));
                if (!$validation['isValid']) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité du déclarant (CNIdcl) a été rejetée par l'IA : " . $validation['reason']
                    ], 422);
                }
            }

            // 6. Enregistrer les fichiers
            $filesToUpload = [
                'pActe' => '',
                'CNIdfnt' => 'cnid',
                'CNIdcl' => 'cnid',
                'documentMariage' => 'mariage',
                'RequisPolice' => 'police',
                'document_autorisation' => 'autorisations',
            ];

            foreach ($filesToUpload as $fileKey => $subDir) {
                if ($request->hasFile($fileKey)) {
                    if ($deces->$fileKey && Storage::disk('public')->exists($deces->$fileKey)) {
                        Storage::disk('public')->delete($deces->$fileKey);
                    }
                    $file = $request->file($fileKey);
                    $extension = $file->getClientOriginalExtension();
                    $newFileName = (string) Str::uuid() . '.' . $extension;
                    $path = $file->storeAs("images/deces/$subDir", $newFileName, 'public');
                    $deces->$fileKey = $path;
                }
            }

            // 7. Mettre à jour tous les champs pour la modification complète
            $deces->pour = $request->input('pour', $deces->pour);
            $deces->relation = $request->input('relation', $deces->relation);
            $deces->type = $request->input('type', $deces->type);
            $deces->name = $request->input('name', $deces->name);
            $deces->nom_prenoms_pere = $request->input('nom_prenoms_pere', $deces->nom_prenoms_pere);
            $deces->nom_prenoms_mere = $request->input('nom_prenoms_mere', $deces->nom_prenoms_mere);
            $deces->numberR = $request->input('numberR', $deces->numberR);
            $deces->dateR = $request->dateR ? Carbon::parse($request->dateR)->format('Y-m-d') : $deces->dateR;
            $deces->commune = $request->input('commune', $deces->commune);
            $deces->commune_deces = $request->input('commune_deces', $deces->commune_deces);

            // Quantités
            $qtySimple = (int) $request->input('qty_simple', 0);
            $qtyIntegral = (int) $request->input('qty_integral', 0);
            if ($deces->type === 'simple') {
                $qtyIntegral = 0;
                if ($qtySimple <= 0)
                    $qtySimple = 1;
            } elseif ($deces->type === 'integrale') {
                $qtySimple = 0;
                if ($qtyIntegral <= 0)
                    $qtyIntegral = 1;
            } else {
                if ($qtySimple <= 0)
                    $qtySimple = 1;
                if ($qtyIntegral <= 0)
                    $qtyIntegral = 1;
            }
            $deces->qty_simple = $qtySimple;
            $deces->qty_integral = $qtyIntegral;
            $deces->quantite = $qtySimple + $qtyIntegral;

            // 8. Gestion de la livraison et du paiement
            $originalChoixOption = $deces->choix_option;
            $nouveauChoixOption = $request->input('choix_option', $originalChoixOption);

            // Normaliser le choix option
            $nouveauChoixOptionNormalise = strtolower($nouveauChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';
            $originalChoixOptionNormalise = strtolower($originalChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';

            // Calcul du reste à payer comme sur le web
            $user->refresh();
            // Créditer temporairement les timbres gratuits déjà accordés à cette demande pour le calcul
            $anciensTimbresGratuits = (int) $deces->free_timbres_count;
            if ($anciensTimbresGratuits > 0) {
                $user->free_requests_used = max(0, $user->free_requests_used - $anciensTimbresGratuits);
                $user->save();
            }

            $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) $deces->quantite);
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];
            $montantLivraisonCible = $nouveauChoixOptionNormalise === 'livraison' ? (float) $request->input('montant_livraison', 0) : 0;
            $nouveauMontantTotal = $montantTimbreTotal + $montantLivraisonCible;

            // Calcul du montant déjà payé s'il a déjà effectué un paiement
            $demandeDejaPayee = !in_array($deces->etat, ['non_paye', 'paiement_en_attente', 'en attente de paiement']);
            $ancienMontantPaye = $demandeDejaPayee ? ((float) $deces->montant_timbre + (float) $deces->montant_livraison) : 0;

            $resteAPayer = $nouveauMontantTotal - $ancienMontantPaye;
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
                    $deces->choix_option = $originalChoixOption;
                } else {
                    $pendingDeliveryData = [
                        'choix_option' => 'Retrait sur place',
                        'montant_timbre' => $montantTimbreTotal,
                        'montant_livraison' => 0,
                    ];
                    // Conserver l'ancienne option jusqu'au paiement
                    $deces->choix_option = $originalChoixOption;
                }
            } else {
                $deces->choix_option = $nouveauChoixOptionNormalise;
                $deces->montant_timbre = $montantTimbreTotal;
                $deces->is_free_request = $freeCalc['free_timbres'] > 0;
                $deces->free_timbres_count = $freeCalc['free_timbres'];

                if ($nouveauChoixOptionNormalise === 'livraison') {
                    $deces->montant_livraison = $montantLivraisonCible;
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
                } else {
                    $deces->montant_livraison = 0;
                    $deces->nom_destinataire = null;
                    $deces->prenom_destinataire = null;
                    $deces->email_destinataire = null;
                    $deces->contact_destinataire = null;
                    $deces->adresse_livraison = null;
                    $deces->code_postal = null;
                    $deces->ville = null;
                    $deces->commune_livraison = null;
                    $deces->quartier = null;
                    $deces->date_livraison = null;
                    $deces->heure_livraison = null;
                }
            }

            // Réinitialiser l'état et désactiver la modification
            $deces->etat = $needsPayment ? 'en attente de paiement' : 'en attente';
            $deces->peut_modifier = false;
            $deces->champs_a_modifier = null;
            $deces->motif_de_rejet = null;
            $deces->save();

            // Gestion de l'initiation du paiement si nécessaire
            if ($needsPayment && $pendingDeliveryData) {
                $totalAmount = $resteAPayer; // C'est le reste à payer

                $deces->montant_timbre = $montantTimbreTotal;
                $deces->is_free_request = $freeCalc['free_timbres'] > 0;
                $deces->free_timbres_count = $freeCalc['free_timbres'];
                $deces->save();

                // Mettre en cache les données de livraison
                \Illuminate\Support\Facades\Cache::put('pending_delivery_update_' . $deces->reference, $pendingDeliveryData, now()->addDays(7));

                if ($totalAmount > 0) {
                    $paymentMethod = $request->input('payment_method', 'wave');
                    $paymentLinkResult = $this->generatePaymentLink($deces, $totalAmount, $paymentMethod);

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
                            'payment_url' => $paymentLinkResult['payment_url'],
                            'transaction_id' => $paymentLinkResult['generated_transaction_id'],
                            'mode' => 'PRODUCTION',
                            'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'],
                            'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'],
                            'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'],
                            'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'],
                        ],
                        'data' => [
                            'demande' => $this->formatDemandeResponse($deces, true)
                        ]
                    ]);
                } else {
                    $deces->etat = 'en attente';
                    if ($freeCalc['free_timbres'] > 0) {
                        $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                    }
                    $deces->save();

                    // Appliquer la livraison en attente
                    $this->applyPendingDeliveryUpdate($deces);
                }
            } else {
                if ($nouveauChoixOptionNormalise !== 'livraison') {
                    if ($freeCalc['free_timbres'] > 0) {
                        $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                    }
                    $deces->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Demande modifiée avec succès et soumise à nouveau.',
                'requires_payment' => false,
                'data' => [
                    'demande' => $this->formatDemandeResponse($deces, true)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@modifierDemande: ' . $e->getMessage() . ' Ligne: ' . $e->getLine(), ['deces_id' => $deces->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier uniquement les informations rejetées d'une demande de décès
     * POST /api/utilisateurs/demandes/deces/{deces}/modifier-rejete
     */
    public function modifierDemandeRejete(Request $request, Deces $deces): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($deces->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier que la demande a bien été rejetée et marquée comme modifiable par la mairie
            if (!$deces->peut_modifier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être modifiée via cette API (elle n\'a pas été marquée comme rejetée/modifiable par la mairie)'
                ], 400);
            }

            // 3. Déterminer les champs rejetés à modifier
            $champsAModifier = json_decode($deces->champs_a_modifier, true) ?? [];
            if (empty($champsAModifier)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun champ à modifier spécifié par la mairie'
                ], 400);
            }

            // Vérification restrictive : rejeter la modification de champs non spécifiés par la mairie
            $champsDemande = ['type', 'pour', 'relation', 'document_autorisation', 'name', 'numberR', 'dateR', 'nom_prenoms_pere', 'nom_prenoms_mere', 'qty_simple', 'qty_integral', 'quantite', 'CNIdfnt', 'CNIdcl', 'documentMariage', 'RequisPolice', 'commune', 'commune_deces'];
            $champsEnvoyes = array_keys($request->all());
            $champsNonAutorises = [];

            foreach ($champsEnvoyes as $champ) {
                if (in_array($champ, $champsDemande)) {
                    $isAllowed = false;
                    if (in_array($champ, $champsAModifier)) {
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
                    case 'type':
                        $rules['type'] = 'required';
                        break;
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
                        $rules['CNIdfnt'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic';
                        break;
                    case 'CNIdcl':
                        $rules['CNIdcl'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic';
                        break;
                    case 'documentMariage':
                        $rules['documentMariage'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic';
                        break;
                    case 'RequisPolice':
                        $rules['RequisPolice'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic';
                        break;
                }
            }

            // Toujours permettre de modifier le choix d'option si présent dans la requête
            if ($request->has('choix_option')) {
                $rules['choix_option'] = 'required|in:retrait,livraison';
            }

            // Règles de livraison si choix_option est livraison
            if ($request->input('choix_option') === 'livraison') {
                $rules['payment_method'] = 'required|string|in:wave,orange,mtn,moov,cinetpay';
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

            // 6. Validation IA Gemini des pièces d'identité si fournies
            $geminiService = app(\App\Services\GeminiValidationService::class);
            if ($request->hasFile('CNIdfnt')) {
                $validation = $geminiService->validateIdentityDocument($request->file('CNIdfnt'));
                if (!$validation['isValid']) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité du défunt (CNIdfnt) a été rejetée par l'IA : " . $validation['reason']
                    ], 422);
                }
            }

            if ($request->hasFile('CNIdcl')) {
                $validation = $geminiService->validateIdentityDocument($request->file('CNIdcl'));
                if (!$validation['isValid']) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité du déclarant (CNIdcl) a été rejetée par l'IA : " . $validation['reason']
                    ], 422);
                }
            }

            // 7. Enregistrer les fichiers
            $filesToUpload = [
                'pActe' => '',
                'CNIdfnt' => 'cnid',
                'CNIdcl' => 'cnid',
                'documentMariage' => 'mariage',
                'RequisPolice' => 'police',
                'document_autorisation' => 'autorisations',
            ];

            foreach ($filesToUpload as $fileKey => $subDir) {
                if ($request->hasFile($fileKey)) {
                    if ($deces->$fileKey && Storage::disk('public')->exists($deces->$fileKey)) {
                        Storage::disk('public')->delete($deces->$fileKey);
                    }
                    $file = $request->file($fileKey);
                    $extension = $file->getClientOriginalExtension();
                    $newFileName = (string) Str::uuid() . '.' . $extension;
                    $path = $file->storeAs("images/deces/$subDir", $newFileName, 'public');
                    $deces->$fileKey = $path;
                }
            }

            // 8. Mettre à jour uniquement les champs rejetés
            foreach ($champsAModifier as $champ) {
                if ($champ === 'dateR' && isset($validated['dateR'])) {
                    $deces->dateR = Carbon::parse($validated['dateR'])->format('Y-m-d');
                } elseif (!in_array($champ, ['CNIdfnt', 'CNIdcl', 'documentMariage', 'RequisPolice']) && isset($validated[$champ])) {
                    $deces->$champ = $validated[$champ];
                }
            }

            // Si la quantité a été modifiée ou le type dans les champs rejetés
            if (in_array('quantite', $champsAModifier) || in_array('type', $champsAModifier)) {
                $deces->quantite = (int) $request->input('quantite', $deces->quantite);
                if ($deces->type === 'integrale') {
                    $deces->qty_integral = $deces->quantite;
                    $deces->qty_simple = 0;
                } else {
                    $deces->qty_simple = $deces->quantite;
                    $deces->qty_integral = 0;
                }
            }

            // 9. Gestion de la livraison et du paiement
            $originalChoixOption = $deces->choix_option;
            $nouveauChoixOption = $request->input('choix_option', $originalChoixOption);

            // Normaliser le choix option
            $nouveauChoixOptionNormalise = strtolower($nouveauChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';
            $originalChoixOptionNormalise = strtolower($originalChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';

            $needsPayment = ($nouveauChoixOptionNormalise === 'livraison' && $originalChoixOptionNormalise !== 'livraison');
            $pendingDeliveryData = null;

            if ($nouveauChoixOptionNormalise === 'livraison') {
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
                    $deces->choix_option = $originalChoixOption;
                } else {
                    $deces->choix_option = 'livraison';
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
            } else {
                $deces->choix_option = 'Retrait sur place';
            }

            // Réinitialiser l'état et désactiver la modification
            $deces->etat = 'en attente';
            $deces->peut_modifier = false;
            $deces->champs_a_modifier = null;
            $deces->motif_de_rejet = null;
            $deces->save();

            // Gestion de l'initiation du paiement si nécessaire
            if ($needsPayment && $pendingDeliveryData) {
                $user->refresh();
                $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) $deces->quantite);
                $montantTimbreTotal = $freeCalc['montant_timbre_total'];
                $montantLivraison = (float) $pendingDeliveryData['montant_livraison'];
                $totalAmount = $montantTimbreTotal + $montantLivraison;

                $deces->montant_timbre = $montantTimbreTotal;
                $deces->is_free_request = $freeCalc['free_timbres'] > 0;
                $deces->free_timbres_count = $freeCalc['free_timbres'];
                $deces->save();

                // Mettre en cache les données de livraison
                \Illuminate\Support\Facades\Cache::put('pending_delivery_update_' . $deces->reference, $pendingDeliveryData, now()->addDays(7));

                if ($totalAmount > 0) {
                    $paymentMethod = $request->input('payment_method', 'wave');
                    $paymentLinkResult = $this->generatePaymentLink($deces, $totalAmount, $paymentMethod);

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
                            'payment_url' => $paymentLinkResult['payment_url'],
                            'transaction_id' => $paymentLinkResult['generated_transaction_id'],
                            'mode' => 'PRODUCTION',
                            'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'],
                            'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'],
                            'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'],
                            'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'],
                        ],
                        'data' => [
                            'demande' => $this->formatDemandeResponse($deces, true)
                        ]
                    ]);
                } else {
                    $deces->etat = 'en attente';
                    if ($freeCalc['free_timbres'] > 0) {
                        $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                    }
                    $deces->save();

                    // Appliquer la livraison en attente
                    $this->applyPendingDeliveryUpdate($deces);
                }
            } else {
                if ($nouveauChoixOptionNormalise !== 'livraison') {
                    $user->refresh();
                    $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) $deces->quantite);
                    $deces->montant_timbre = $freeCalc['montant_timbre_total'];
                    $deces->is_free_request = $freeCalc['free_timbres'] > 0;
                    $deces->free_timbres_count = $freeCalc['free_timbres'];
                    if ($freeCalc['free_timbres'] > 0) {
                        $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                    }
                    $deces->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Demande modifiée avec succès et soumise à nouveau.',
                'requires_payment' => false,
                'data' => [
                    'demande' => $this->formatDemandeResponse($deces, true)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@modifierDemandeRejete: ' . $e->getMessage() . ' Ligne: ' . $e->getLine(), ['deces_id' => $deces->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les champs à modifier pour une demande rejetée
     * GET /api/utilisateurs/demandes/deces/{deces}/champs-a-modifier
     */
    public function getChampsAModifier(Deces $deces): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($deces->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier que la demande peut être modifiée
            if (!$deces->peut_modifier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être modifiée'
                ], 400);
            }

            // 3. Récupérer les champs à modifier
            $champsNoms = json_decode($deces->champs_a_modifier, true) ?? [];

            if (empty($champsNoms)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun champ à modifier spécifié'
                ], 400);
            }

            // 4. Mapping des champs avec label et type
            $fieldsMapping = [
                'name' => ['label' => 'Nom et Prénoms du défunt', 'type' => 'text'],
                'numberR' => ['label' => 'Numéro de Registre', 'type' => 'text'],
                'dateR' => ['label' => 'Date de Registre', 'type' => 'date'],
                'commune' => ['label' => 'Commune', 'type' => 'text'],
                'quantite' => ['label' => 'Quantité', 'type' => 'number'],
                'CNIdfnt' => ['label' => 'CNI/Extrait de naissance du défunt', 'type' => 'file'],
                'CNIdcl' => ['label' => 'Certificat médical de décès', 'type' => 'file'],
                'documentMariage' => ['label' => 'Document de mariage', 'type' => 'file'],
                'RequisPolice' => ['label' => 'Réquisition de police', 'type' => 'file'],
            ];

            // 5. Construire la réponse avec les valeurs actuelles
            $champsAvecValeurs = [];
            foreach ($champsNoms as $champNom) {
                $fieldInfo = $fieldsMapping[$champNom] ?? ['label' => $champNom, 'type' => 'text'];
                $champsAvecValeurs[$champNom] = [
                    'label' => $fieldInfo['label'],
                    'type' => $fieldInfo['type'],
                    'value' => $deces->$champNom ?? null,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $deces->id,
                    'reference' => $deces->reference,
                    'motif_de_rejet' => $deces->motif_de_rejet,
                    'champs_a_modifier' => $champsAvecValeurs,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@getChampsAModifier: ' . $e->getMessage(), ['deces_id' => $deces->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper pour formater la réponse de la demande
     */
    private function formatDemandeResponse(Deces $deces, bool $includeFiles = false)
    {
        // montant_total = timbres + livraison (les timbres sont dus pour retrait ET livraison)
        $montant_total = (float) ($deces->montant_timbre ?? 0) + (float) ($deces->montant_livraison ?? 0);

        $data = [
            'id' => $deces->id,
            'reference' => $deces->reference,
            'name' => $deces->name,
            'numberR' => $deces->numberR,
            'dateR' => $deces->dateR,
            'commune' => $deces->commune,
            'etat' => $deces->etat,
            'choix_option' => $deces->choix_option,

            // --- AJOUT ---
            'quantite' => (int) $deces->quantite,

            'montant_timbre_unitaire' => (float) $deces->montant_timbre, // Renommé pour plus de clarté
            'montant_livraison' => (float) $deces->montant_livraison,
            'montant_total' => $montant_total, // Montant total calculé

            'created_at' => $deces->created_at->format('Y-m-d H:i:s'),

            // Infos de livraison (si elles existent)
            'nom_destinataire' => $deces->nom_destinataire,
            'prenom_destinataire' => $deces->prenom_destinataire,
            'email_destinataire' => $deces->email_destinataire,
            'contact_destinataire' => $deces->contact_destinataire,
            'adresse_livraison' => $deces->adresse_livraison,
            'ville' => $deces->ville,
            'commune_livraison' => $deces->commune_livraison,
            'quartier' => $deces->quartier,
            'statut_livraison' => $deces->statut_livraison,
        ];

        // Ajout conditionnel des documents (pour la méthode index)
        if ($includeFiles) {
            $data['documents'] = [
                'CNIdfnt' => $deces->CNIdfnt ? Storage::url($deces->CNIdfnt) : null,
                'CNIdcl' => $deces->CNIdcl ? Storage::url($deces->CNIdcl) : null,
                'documentMariage' => $deces->documentMariage ? Storage::url($deces->documentMariage) : null,
                'RequisPolice' => $deces->RequisPolice ? Storage::url($deces->RequisPolice) : null,
            ];
            $data['updated_at'] = $deces->updated_at->format('Y-m-d H:i:s');
        }

        return $data;
    }

    public function handlePaymentNotification(Request $request): JsonResponse
    {
        Log::info('Webhook CinetPay Reçu (Deces) - request:', $request->all());

        // Récupérer l'ID de transaction CinetPay
        $cinetpayTransactionId = $request->input('cpm_trans_id')
            ?? $request->input('transaction_id')
            ?? $request->input('data.cpm_trans_id')
            ?? null;

        if (empty($cinetpayTransactionId)) {
            Log::warning('Webhook CinetPay (Deces): transaction_id manquant dans le webhook.', $request->all());
            return response()->json(['success' => false, 'message' => 'Transaction ID manquant'], 200);
        }

        // Extraire la référence originale (enlever le suffixe unique)
        $reference = $cinetpayTransactionId;
        if (strpos($cinetpayTransactionId, '_') !== false) {
            $parts = explode('_', $cinetpayTransactionId);
            $reference = $parts[0]; // Prendre la partie avant le _
        }

        try {
            // Trouver la demande Deces par la référence originale
            $deces = Deces::where('reference', $reference)->first();

            if (!$deces) {
                Log::warning("Webhook CinetPay (Deces): Aucune demande trouvée pour reference {$reference}.");
                return response()->json(['success' => true, 'message' => 'Demande non trouvée'], 200);
            }
            // 3. Vérifier le statut auprès de CinetPay via l'API de check
            $cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548');
            $cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');
            $cinetpayUrl = 'https://api-checkout.cinetpay.com/v2/payment/check';

            // ⚠️ CORRECTION : Utiliser la variable $cinetpayTransactionId
            $response = Http::withoutVerifying()->post($cinetpayUrl, [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $cinetpayTransactionId, // ✅ CORRIGÉ
            ]);

            // ⚠️ CORRECTION : Utiliser la variable $cinetpayTransactionId pour les logs
            Log::info("CinetPay check response status: {$response->status()} for transaction {$cinetpayTransactionId}"); // ✅ CORRIGÉ

            if ($response->failed()) {
                // ⚠️ CORRECTION : Utiliser la variable $cinetpayTransactionId pour les logs
                Log::error("Webhook CinetPay (Deces) {$cinetpayTransactionId}: échec check API.", [ // ✅ CORRIGÉ
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['success' => false, 'message' => 'Vérification CinetPay échouée'], 500);
            }

            $verificationData = $response->json();
            // ⚠️ CORRECTION : Utiliser la variable $cinetpayTransactionId pour les logs
            Log::info("CinetPay check body for {$cinetpayTransactionId}:", $verificationData); // ✅ CORRIGÉ

            // 4. Extraire info avec tolérance (plusieurs clés possibles)
            $data = $verificationData['data'] ?? $verificationData ?? [];

            $status = $data['status'] ?? $data['payment_status'] ?? null; // ACCEPTED / REFUSED / PENDING

            // Montant : essayer plusieurs clés
            $amount = $data['amount'] ?? $data['cpm_amount'] ?? $data['amount_paid'] ?? null;

            $operatorId = $data['cpm_payid'] ?? $data['payid'] ?? $data['pay_id'] ?? null;
            $paymentToken = $data['payment_token'] ?? $data['cpm_token'] ?? null;
            $paymentDate = $data['payment_date'] ?? $data['cpm_trans_date'] ?? $data['created_at'] ?? null;
            // ⚠️ CORRECTION : Utiliser la variable $cinetpayTransactionId comme fallback
            $operatorTransId = $data['cpm_trans_id'] ?? $data['transaction_id'] ?? $cinetpayTransactionId; // ✅ CORRIGÉ
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

            // 5. Si status ACCEPTED -> créer paiement et mettre à jour la demande
            if (strtoupper($status) === 'ACCEPTED') {

                try {
                    $paiement = Paiement::create([
                        'deces_id' => $deces->id,
                        'user_id' => $deces->user_id ?? null,
                        'transaction_id' => $operatorTransId,
                        'operator_id' => $operatorId,
                        'payment_token' => $paymentToken,
                        'payer_name' => $payerName,
                        'montant' => $montantFloat, // Le montant total payé
                        'currency' => $currency,
                        'status' => 'ACCEPTED',
                        'paid_at' => $paymentDate ? Carbon::parse($paymentDate) : now(),
                        'raw_response' => $verificationData,
                    ]);
                    // ⚠️ CORRECTION : Utiliser la variable $cinetpayTransactionId pour les logs
                    Log::info("Paiement enregistré pour {$cinetpayTransactionId}, id paiement: {$paiement->id}"); // ✅ CORRIGÉ
                } catch (\Exception $e) {
                    // ⚠️ CORRECTION : Utiliser la variable $cinetpayTransactionId pour les logs
                    Log::error("Erreur enregistrement paiement pour {$cinetpayTransactionId}: " . $e->getMessage(), [ // ✅ CORRIGÉ
                        'exception' => $e
                    ]);
                    // Ne pas aborter la suite : on continue à mettre à jour la demande
                }

                // Mettre à jour le Deces (quelque soit l'état antérieur)
                // C'est ici que l'état passe à 'en attente' (de traitement/livraison)
                $this->applyPendingDeliveryUpdate($deces);
                $deces->etat = 'en attente';
                if ($deces->choix_option === 'livraison') {
                    $deces->statut_livraison = 'en attente';
                }
                $deces->save();

                // ⚠️ CORRECTION : Utiliser la variable $cinetpayTransactionId pour les logs
                Log::info("Demande {$cinetpayTransactionId} mise à jour : en attente"); // ✅ CORRIGÉ

                // Envoi des notifications suite au paiement réussi
                try {
                    $user = $deces->user;
                    if ($user) {
                        $yellikaSmsService = app(YellikaSmsService::class);
                        $phoneNumber = $user->indicatif . $user->contact;
                        $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de décès a été confirmé. Référence : {$deces->reference}.
Votre demande est maintenant en attente de traitement.";
                        $yellikaSmsService->sendSms($phoneNumber, $message);

                        Notification::send($user, new DemandeDecesConfirmationNotification($user, $deces));
                    }
                } catch (\Exception $e) {
                    Log::error("Erreur notifications Webhook Deces: " . $e->getMessage());
                }

                return response()->json(['success' => true, 'message' => 'Paiement accepté et traité'], 200);
            }

            // 6. Si status différent -> marquer selon le cas
            $upper = strtoupper((string) $status);
            if ($upper === 'PENDING' || $upper === 'AWAITING') {
                $deces->etat = 'en attente de paiement';
                $deces->statut_livraison = 'en attente'; // Doit être 'en attente de paiement' ?
                $deces->save();

                // ⚠️ CORRECTION : Utiliser la variable $cinetpayTransactionId pour les logs
                Log::info("Demande {$cinetpayTransactionId} marquée en attente (CinetPay status: {$status})"); // ✅ CORRIGÉ
                return response()->json(['success' => true, 'message' => 'Paiement en attente'], 200);
            }

            // Pour REFUSED ou autres
            $deces->etat = 'paiement_echoue';
            $deces->statut_livraison = 'paiement_echoue';
            $deces->save();

            // ⚠️ CORRECTION : Utiliser la variable $cinetpayTransactionId pour les logs
            Log::warning("Demande {$cinetpayTransactionId} paiement non accepté (status: {$status})."); // ✅ CORRIGÉ

            return response()->json(['success' => true, 'message' => 'Paiement non accepté traité'], 200);
        } catch (\Exception $e) {
            // ⚠️ CORRECTION : Utiliser la variable $cinetpayTransactionId pour les logs
            Log::error("Webhook CinetPay (Deces) {$cinetpayTransactionId}: Exception critique : " . $e->getMessage(), [ // ✅ CORRIGÉ
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur interne'], 500);
        }
    }


    /**
     * Obtenir le statut de paiement pour le polling
     * GET /api/deces/payment-status/{reference}
     */
    public function getPaymentStatus(Request $request, $reference): JsonResponse
    {
        Log::info("API getPaymentStatus appelée pour la référence : " . $reference);
        try {
            // 1. Trouver la demande de décès
            $deces = Deces::where('reference', $reference)->first();

            if (!$deces) {
                return response()->json(['status' => 'not_found', 'message' => 'Demande non trouvée'], 404);
            }

            // Vérification en direct du statut MTN si applicable
            if ($deces->etat === 'en attente de paiement' || $deces->etat === 'non_paye' || $deces->etat === 'paiement_en_attente') {
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

                                $montantTimbre = (float) ($deces->montant_timbre ?? 0);
                                $montantLivraison = (float) ($deces->montant_livraison ?? 0);

                                if ($isModification) {
                                    $cacheKey = 'pending_delivery_update_' . $deces->reference;
                                    if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                                        $pendingData = \Illuminate\Support\Facades\Cache::get($cacheKey);
                                        $nouveauMontantTimbre = (float) ($pendingData['montant_timbre'] ?? 0);
                                        $nouveauMontantLivraison = (float) ($pendingData['montant_livraison'] ?? 0);
                                        $nouveauMontantTotal = $nouveauMontantTimbre + $nouveauMontantLivraison;
                                    } else {
                                        $nouveauMontantTotal = $montantTimbre + $montantLivraison;
                                    }

                                    $dejaPaye = Paiement::where("deces_id", $deces->id)
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
                                    $dejaPayeTimbre = Paiement::where("deces_id", $deces->id)
                                        ->where('status', 'ACCEPTED')
                                        ->where('transaction_id', '!=', $reference)
                                        ->get()
                                        ->sum(function ($p) {
                                            return (float) ($p->raw_response['part_timbre'] ?? 0);
                                        });

                                    $dejaPayeLivraison = Paiement::where("deces_id", $deces->id)
                                        ->where('status', 'ACCEPTED')
                                        ->where('transaction_id', '!=', $reference)
                                        ->get()
                                        ->sum(function ($p) {
                                            return (float) ($p->raw_response['part_livraison'] ?? 0);
                                        });

                                    $cacheKey = 'pending_delivery_update_' . $deces->reference;
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
                                    $partTimbre = (float) ($deces->montant_timbre ?? 0);
                                    $partLivraison = (float) ($deces->montant_livraison ?? 0);
                                }

                                Paiement::create([
                                    'user_id' => $deces->user_id,
                                    'transaction_id' => $reference,
                                    'operator_id' => 'MTN',
                                    'montant' => $amount,
                                    'currency' => $statusInfo['currency'] ?? 'XOF',
                                    'status' => 'ACCEPTED',
                                    'paid_at' => now(),
                                    "deces_id" => $deces->id,
                                    'raw_response' => [
                                        'part_timbre' => $partTimbre,
                                        'part_livraison' => $partLivraison,
                                        'is_modification' => $isModification,
                                        'mtn_status_info' => $statusInfo
                                    ]
                                ]);

                                $this->applyPendingDeliveryUpdate($deces);
                                $deces->etat = 'en attente';
                                if ($deces->choix_option === 'livraison') {
                                    $deces->statut_livraison = 'en attente';
                                }
                                $deces->save();

                                $this->incrementFreeRequestsFromDemande($deces);

                                $user = \App\Models\User::find($deces->user_id);
                                if ($user) {
                                    try {
                                        $yellikaSmsService = app(\App\Services\YellikaSmsService::class);
                                        $phoneNumber = $user->indicatif . $user->contact;
                                        $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de décès a été confirmé. Référence : {$deces->reference}. Votre demande est maintenant en attente de traitement.";
                                        $yellikaSmsService->sendSms($phoneNumber, $message);

                                        \Illuminate\Support\Facades\Notification::send($user, new \App\Notifications\DemandeDecesConfirmationNotification($user, $deces));
                                    } catch (\Exception $e) {
                                        Log::error("Erreur notifications MTN API: " . $e->getMessage());
                                    }
                                }
                            }
                        } elseif ($status === 'FAILED') {
                            $deces->etat = 'paiement_echoue';
                            $deces->save();
                        }
                    }
                }
            }

            // --- Construction des données de réponse ---

            // 2. Calculer le montant total (logique identique à formatDemandeResponse)
            $montant_total_timbres = (float) ($deces->montant_timbre ?? 0) * (int) ($deces->quantite ?? 1);
            $montant_total = $deces->choix_option === 'livraison'
                ? $montant_total_timbres + (float) ($deces->montant_livraison ?? 0)
                : 0;

            // 3. Déterminer la date et l'heure
            $date_heure = $deces->created_at->format('Y-m-d H:i:s'); // Par défaut: date de création

            if ($deces->etat === 'en attente') {
                // Si payé, chercher la date de paiement
                $paiement = Paiement::where('deces_id', $deces->id)
                    ->where('status', 'ACCEPTED')
                    ->orderBy('paid_at', 'desc')
                    ->first();
                if ($paiement && $paiement->paid_at) {
                    $date_heure = Carbon::parse($paiement->paid_at)->format('Y-m-d H:i:s');
                } else {
                    // Fallback si on ne trouve pas le paiement mais que le statut est bon
                    $date_heure = $deces->updated_at->format('Y-m-d H:i:s');
                }
            } elseif ($deces->etat === 'paiement_echoue') {
                // Si échoué, utiliser la date de mise à jour
                $date_heure = $deces->updated_at->format('Y-m-d H:i:s');
            }

            // 4. Construire le JSON de réponse
            $responseData = [
                'status' => $deces->etat, // Le statut est crucial pour le polling
                'data' => [
                    // Le type est "Acte de décès" car nous sommes dans le DemandeDecesController
                    'type_document' => 'Acte de décès',
                    'quantite' => (int) $deces->quantite,
                    'montant' => $montant_total,
                    'date_heure' => $date_heure,
                    'id_transaction' => $deces->reference
                ]
            ];

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error("Erreur getPaymentStatus pour {$reference}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Afficher la page de redirection web (Fallback)
     */
    public function showRedirectPage(Request $request)
    {
        $transactionId = $request->input('transactionId') ?? $request->input('transaction_id');

        if (!$transactionId) {
            // ❌ ANCIEN CHEMIN: 'redirect_to_app'
            // ✅ NOUVEAU CHEMIN: 'payments.redirect_to_app'
            return view('payments.redirect_to_app', ['transactionId' => null]);
        }

        // ❌ ANCIEN CHEMIN: 'redirect_to_app'
        // ✅ NOUVEAU CHEMIN: 'payments.redirect_to_app'
        return view('payments.redirect_to_app', [
            'transactionId' => $transactionId
        ]);
    }

    /**
     * Supprimer une demande de décès
     * DELETE /api/utilisateurs/demandes/deces/{deces}
     */
    public function destroy(Deces $deces): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier que l'utilisateur est bien le propriétaire de la demande
            if ($deces->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à supprimer cette demande'
                ], 403);
            }

            // 2. Vérifier l'état de la demande (CORRECTION ICI)
            // On convertit l'état en minuscule et on gère les caractères spéciaux (UTF-8)
            // Cela permet de bloquer "Terminé", "TERMINÉ", "terminé", etc.
            $etatActuel = mb_strtolower(trim($deces->etat), 'UTF-8');

            // Liste des états qui BLOQUENT la suppression (en minuscule)
            $etatsInterdits = ['reçu', 'terminé', 'recu', 'termine', 'réçu']; // J'ajoute les versions sans accent par sécurité

            if (in_array($etatActuel, $etatsInterdits)) {
                return response()->json([
                    'success' => false,
                    'message' => "Impossible de supprimer cette demande car elle est déjà en traitement ou finalisée. (État actuel : {$deces->etat})"
                ], 400); // 400 Bad Request car l'action logique est impossible
            }

            // 3. Suppression des fichiers associés (Optionnel : décommenter si tu veux nettoyer le stockage)
            /*
            $files = [$deces->CNIdfnt, $deces->CNIdcl, $deces->documentMariage, $deces->RequisPolice];
            foreach ($files as $path) {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            */

            // 4. Suppression de la demande en base de données
            $deces->delete();

            return response()->json([
                'success' => true,
                'message' => 'Demande de décès supprimée avec succès'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la demande'
            ], 500);
        }
    }
}
