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
            'numberR' => 'required|string|max:255',
            'dateR' => 'required|date',

            // --- AJOUT --- (Basé sur DecesController.php)
            'quantite' => 'required|integer|min:1|max:10',

            'CNIdfnt' => 'required',
            'CNIdcl' => 'required',
            'documentMariage' => 'nullable',
            'RequisPolice' => 'nullable',
            'choix_option' => 'required|in:retrait,livraison',
            'communeD' => 'nullable|string|max:255',
            'commune_deces' => 'required|string|max:255',
            'payment_method' => 'required|string|in:wave,orange,mtn,moov,cinetpay',

            // Note : montant_timbre est maintenant le PRIX UNITAIRE
            'montant_timbre' => 'required_if:choix_option,livraison|numeric',
            'montant_livraison' => 'required_if:choix_option,livraison|numeric',

            'nom_destinataire' => 'required_if:choix_option,livraison|string|max:255',
            'prenom_destinataire' => 'required_if:choix_option,livraison|string|max:255',
            'email_destinataire' => 'required_if:choix_option,livraison|email',
            'contact_destinataire' => 'required_if:choix_option,livraison|string|max:20',
            'adresse_livraison' => 'required_if:choix_option,livraison|string|max:500',
            'code_postal' => 'nullable|string|max:10',
            'ville' => 'required_if:choix_option,livraison|string|max:255',
            'commune_livraison' => 'required_if:choix_option,livraison|string|max:255',
            'quartier' => 'nullable|string|max:255',
            'nom_prenoms_pere' => 'nullable|string|max:255',
            'nom_prenoms_mere' => 'nullable|string|max:255',
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
            $deces->name = $request->name;
            $deces->numberR = $request->numberR;
            $deces->dateR = Carbon::parse($request->dateR)->format('Y-m-d');

            // --- AJOUT ---
            $deces->quantite = $request->quantite;

            $deces->CNIdfnt = $uploadedPaths['CNIdfnt'] ?? null;
            $deces->CNIdcl = $uploadedPaths['CNIdcl'] ?? null;
            $deces->documentMariage = $uploadedPaths['documentMariage'] ?? null;
            $deces->RequisPolice = $uploadedPaths['RequisPolice'] ?? null;
            $deces->choix_option = $request->choix_option;
            $deces->commune = $request->communeD ?: $user->commune;
            $deces->commune_deces = $request->commune_deces;
            $deces->user_id = $user->id;
            $deces->reference = $reference;
            
            // --- GESTION DES DEMANDES GRATUITES ---
            $user->refresh();
            $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) $deces->quantite);
            Log::info("Demandes gratuites - Deces (API) {$deces->reference}: {$freeCalc['free_timbres']} timbres gratuits, {$freeCalc['paid_timbres']} payants");

            if ($request->choix_option === 'livraison') {
                $montantTimbreTotal = $freeCalc['montant_timbre_total'];
                $montantLivraison = (float) $request->montant_livraison;
                $totalAmount = $montantTimbreTotal + $montantLivraison;

                $deces->montant_timbre = $montantTimbreTotal; // Remplacé par le montant total des timbres calculé
                $deces->montant_livraison = $montantLivraison;
                $deces->is_free_request = $freeCalc['free_timbres'] > 0;
                $deces->free_timbres_count = $freeCalc['free_timbres'];

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
                // Retrait sur place
                $totalAmount = 0;
                $deces->etat = 'en attente';
                $deces->statut_livraison = null;
                $deces->montant_timbre = $freeCalc['montant_timbre_total'];
                $deces->is_free_request = $freeCalc['free_timbres'] > 0;
                $deces->free_timbres_count = $freeCalc['free_timbres'];
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
            return response()->json([
                'success' => true,
                'message' => 'Demande créée. Utilisez le payment_url pour payer.',
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

            // Sinon, utiliser CinetPay pour les autres moyens de paiement (Orange, MTN, Moov)
            $channels = 'ALL';
            if (in_array(strtolower($paymentMethod), ['orange', 'mtn', 'moov'])) {
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
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($deces->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier si l'option est 'livraison' (seules celles-là ont un paiement)
            if ($deces->choix_option !== 'livraison') {
                return response()->json(['success' => false, 'message' => 'Cette demande (retrait) ne nécessite pas de paiement.'], 400);
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
            $totalAmount = (float) $deces->montant_timbre + (float) $deces->montant_livraison;
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
            return response()->json([
                'success' => true,
                'message' => 'Nouveau lien de paiement généré. Utilisez le payment_url pour payer.',
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

            // 2. Vérifier que la demande peut être modifiée
            if (!$deces->peut_modifier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être modifiée'
                ], 400);
            }

            // 3. Récupérer les champs à modifier
            $champsAModifier = json_decode($deces->champs_a_modifier, true) ?? [];

            if (empty($champsAModifier)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun champ à modifier spécifié'
                ], 400);
            }

            // 4. Définir les règles de validation par champ
            $rules = [];
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
                        $rules['CNIdfnt'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic|max:25600';
                        break;
                    case 'CNIdcl':
                        $rules['CNIdcl'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic|max:25600';
                        break;
                    case 'documentMariage':
                        $rules['documentMariage'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic|max:25600';
                        break;
                    case 'RequisPolice':
                        $rules['RequisPolice'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic|max:25600';
                        break;
                }
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

            // 6. Mettre à jour les champs
            $fileFields = ['CNIdfnt', 'CNIdcl', 'documentMariage', 'RequisPolice'];
            $subDirs = [
                'CNIdfnt' => 'cnid',
                'CNIdcl' => 'cnid',
                'documentMariage' => 'mariage',
                'RequisPolice' => 'police',
            ];

            foreach ($champsAModifier as $champ) {
                if (in_array($champ, $fileFields) && $request->hasFile($champ)) {
                    // Supprimer l'ancien fichier si existe
                    if ($deces->$champ && Storage::disk('public')->exists($deces->$champ)) {
                        Storage::disk('public')->delete($deces->$champ);
                    }

                    // Enregistrer le nouveau fichier
                    $file = $request->file($champ);
                    $extension = $file->getClientOriginalExtension();
                    $newFileName = (string) Str::uuid() . '.' . $extension;
                    $subDir = $subDirs[$champ] ?? '';
                    $path = $file->storeAs("images/deces/$subDir", $newFileName, 'public');
                    $deces->$champ = $path;
                } elseif (isset($validated[$champ])) {
                    $deces->$champ = $validated[$champ];
                }
            }

            // 7. Réinitialiser l'état et désactiver la modification
            $deces->etat = 'en attente';
            $deces->peut_modifier = false;
            $deces->champs_a_modifier = null;
            $deces->motif_de_rejet = null;
            $deces->save();

            return response()->json([
                'success' => true,
                'message' => 'Demande modifiée avec succès et soumise à nouveau.',
                'data' => [
                    'demande' => $this->formatDemandeResponse($deces, true)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur DemandeDecesController@modifierDemande: ' . $e->getMessage(), ['deces_id' => $deces->id]);
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
        // montant_timbre est le TOTAL des timbres payants (après déduction des timbres gratuits)
        $montant_total = $deces->choix_option === 'livraison'
            ? (float) ($deces->montant_timbre ?? 0) + (float) ($deces->montant_livraison ?? 0)
            : 0;

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
                $deces->etat = 'en attente';
                $deces->statut_livraison = 'en attente';
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