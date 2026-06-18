<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Naissance; // ✅ Modèle Naissance
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
use App\Notifications\DemandeNaissanceConfirmationNotification;
use Illuminate\Support\Facades\Notification;
use App\Traits\HandlesFreeRequests;

class DemandeNaissanceController extends Controller
{
    use HandlesFreeRequests;
    /**
     * Liste des demandes de naissance de l'utilisateur
     */
    public function index(): JsonResponse
    {
        try {
            $user = Auth::user();
            $naissances = Naissance::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des demandes de naissance récupérée avec succès',
                'data' => [
                    'demandes' => $naissances->map(function ($demande) {
                        return $this->formatDemandeResponse($demande, true);
                    })
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeNaissanceController@index: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des demandes'
            ], 500);
        }
    }

    /**
     * Créer une nouvelle demande de naissance
     */
    public function store(Request $request, YellikaSmsService $yellikaSmsService, \App\Services\WaveService $waveService): JsonResponse
    {
        // 1. Validation (Spécifique à Naissance)
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'pour' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'number' => 'nullable|string|max:255',
            'DateR' => 'nullable|date',
            'commune' => 'required|string|max:255',
            'commune_naissance' => 'required|string|max:255',
            'qty_simple' => 'nullable|integer|min:0|max:10',
            'qty_integral' => 'nullable|integer|min:0|max:10',
            'payment_method' => 'required|string|in:wave,orange,mtn,moov,cinetpay',
            'CNI' => 'required',
            'nom_prenoms_pere' => 'nullable|string|max:255',
            'nom_prenoms_mere' => 'nullable|string|max:255',
            'choix_option' => 'required|in:retrait,livraison',
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
        ]);

        // 1. Validation (Spécifique à Naissance)
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'pour' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'number' => 'nullable|string|max:255',
            'DateR' => 'nullable|date',
            'commune' => 'required|string|max:255',
            'commune_naissance' => 'required|string|max:255',
            'qty_simple' => 'nullable|integer|min:0|max:10',
            'qty_integral' => 'nullable|integer|min:0|max:10',
            'payment_method' => 'required|string|in:wave,orange,mtn,moov,cinetpay',
            'CNI' => 'required',
            'nom_prenoms_pere' => 'nullable|string|max:255',
            'nom_prenoms_mere' => 'nullable|string|max:255',
            'choix_option' => 'required|in:retrait,livraison',
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
            'relation' => 'nullable|string|in:enfant,parent,connaissance',
            'document_autorisation' => 'required_if:relation,connaissance|nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
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

            // 2. Upload du fichier CNI et document_autorisation
            $uploadedPaths = [];
            if ($request->hasFile('CNI')) {
                $file = $request->file('CNI');
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $path = $file->storeAs("images/naissances/cni", $newFileName, 'public');
                $uploadedPaths['CNI'] = $path;
            }

            if ($request->hasFile('document_autorisation')) {
                $file = $request->file('document_autorisation');
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $path = $file->storeAs("images/naissances/autorisations", $newFileName, 'public');
                $uploadedPaths['document_autorisation'] = $path;
            }

            // 3. Génération de la référence (AN)
            $communeInitiale = strtoupper(substr($request->commune ?: 'X', 0, 1));
            $anneeCourante = Carbon::now()->year;
            $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $increment = Naissance::max('id') + 1;
            $reference = 'AN' . $randomDigits . $increment . $communeInitiale . $anneeCourante;

            // 4. Création de la demande
            $naissance = new Naissance();
            $naissance->pour = $request->pour;
            $naissance->type = $request->input('typeDemande'); // Harmonisation (simple, integrale, groupee)
            $naissance->name = $request->name;
            $naissance->prenom = $request->prenom;
            $naissance->nom_prenoms_pere = $request->nom_prenoms_pere;
            $naissance->nom_prenoms_mere = $request->nom_prenoms_mere;
            $naissance->number = $request->number;
            $naissance->DateR = $request->DateR ? Carbon::parse($request->DateR)->format('Y-m-d') : null;
            $naissance->commune = $request->commune;
            $naissance->commune_naissance = $request->commune_naissance;
            $naissance->relation = $request->relation;
            $naissance->document_autorisation = $uploadedPaths['document_autorisation'] ?? null;

            // Calcul des quantités comme dans NaissanceController web
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

            $naissance->qty_simple = $qtySimple;
            $naissance->qty_integral = $qtyIntegral;
            $naissance->quantite = $totalQuantity;

            $naissance->CNI = $uploadedPaths['CNI'] ?? null;
            // Normalisation : le mobile envoie 'retrait'/'livraison' (minuscules)
            // mais le backend attend 'Retrait sur place'/'Livraison' (comme le web)
            $naissance->choix_option = strtolower($request->choix_option) === 'livraison'
                ? 'livraison'
                : 'Retrait sur place';
            $naissance->user_id = $user->id;
            $naissance->reference = $reference;

            // --- GESTION DES DEMANDES GRATUITES ---
            $user->refresh();
            $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) $naissance->quantite);
            Log::info("Demandes gratuites - Naissance (API) {$naissance->reference}: {$freeCalc['free_timbres']} timbres gratuits, {$freeCalc['paid_timbres']} payants");

            // 5. Informations de livraison et paiement
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];
            $montantLivraison = strtolower($request->choix_option) === 'livraison' ? (float) $request->montant_livraison : 0;
            $totalAmount = $montantTimbreTotal + $montantLivraison;

            $naissance->montant_timbre = $montantTimbreTotal;
            $naissance->montant_livraison = $montantLivraison;
            $naissance->is_free_request = $freeCalc['free_timbres'] > 0;
            $naissance->free_timbres_count = $freeCalc['free_timbres'];

            if (strtolower($request->choix_option) === 'livraison') {
                $naissance->nom_destinataire = $request->nom_destinataire;
                $naissance->prenom_destinataire = $request->prenom_destinataire;
                $naissance->email_destinataire = $request->email_destinataire;
                $naissance->contact_destinataire = $request->contact_destinataire;
                $naissance->adresse_livraison = $request->adresse_livraison;
                $naissance->code_postal = $request->code_postal;
                $naissance->ville = $request->ville;
                $naissance->commune_livraison = $request->commune_livraison;
                $naissance->quartier = $request->quartier;

                if ($totalAmount > 0) {
                    $naissance->etat = 'en attente de paiement';
                    $naissance->statut_livraison = 'en attente de paiement';
                } else {
                    $naissance->etat = 'en attente';
                    $naissance->statut_livraison = null;
                }
            } else {
                $naissance->nom_destinataire = null;
                $naissance->prenom_destinataire = null;
                $naissance->email_destinataire = null;
                $naissance->contact_destinataire = null;
                $naissance->adresse_livraison = null;
                $naissance->code_postal = null;
                $naissance->ville = null;
                $naissance->commune_livraison = null;
                $naissance->quartier = null;

                if ($totalAmount > 0) {
                    $naissance->etat = 'en attente de paiement';
                } else {
                    $naissance->etat = 'en attente';
                }
                $naissance->statut_livraison = null;
            }

            $naissance->save();

            // 6. Cas "Retrait" ou "Gratuit Livraison"
            if ($totalAmount == 0) {
                // Incrémenter le compteur SEULEMENT maintenant (pas de paiement requis)
                if ($freeCalc['free_timbres'] > 0) {
                    $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                }

                // Envoi des notifications (SMS & Email)
                try {
                    $phoneNumber = $user->indicatif . $user->contact;
                    $message = "Bonjour {$user->name}, votre demande d'extrait de naissance a bien été transmise à la mairie du plateau. Référence : {$naissance->reference}.
Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com/home/search";
                    $yellikaSmsService->sendSms($phoneNumber, $message);

                    Notification::send($user, new DemandeNaissanceConfirmationNotification($user, $naissance));
                } catch (\Exception $e) {
                    Log::error("Erreur notifications DemandeNaissance (API): " . $e->getMessage());
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Demande de naissance créée avec succès (gratuite ou retrait)',
                    'requires_payment' => false,
                    'free_requests' => [
                        'timbres_gratuits_appliques' => $freeCalc['free_timbres'],
                        'economie' => $freeCalc['montant_timbre_gratuit'],
                        'restants' => $this->getRemainingFreeRequests($user),
                    ],
                    'data' => ['demande' => $this->formatDemandeResponse($naissance)]
                ], 201);
            }

            // Pour "livraison", les notifications SMS & Email seront envoyées
            // dans handlePaymentNotification() après confirmation du paiement

            // --- DEBUT LOGIQUE PAIEMENT (Cas "Livraison") ---

            $paymentMethod = $request->input('payment_method');
            $paymentLinkResult = $this->generatePaymentLink($naissance, $totalAmount, $paymentMethod);

            // 8. Gérer l'échec de la génération
            if (!$paymentLinkResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demande créée, mais échec de la génération du lien de paiement. Veuillez réessayer.',
                    'error_details' => $paymentLinkResult['error_details']
                ], 500);
            }

            // 9. Renvoyer la réponse JSON
            return response()->json([
                'success' => true,
                'message' => 'Demande créée. Utilisez le payment_url pour payer.',
                'requires_payment' => true,
                'payment_method' => $paymentMethod,
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
                    'demande' => $this->formatDemandeResponse($naissance)
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeNaissanceController@store: ' . $e->getMessage() . ' Ligne: ' . $e->getLine());
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
     * Génère un nouveau lien de paiement (Wave ou CinetPay) pour une demande de naissance existante.
     */
    private function generatePaymentLink(Naissance $naissance, $totalAmount, $paymentMethod): array
    {
        try {
            // 1. Préparer les URLs
            $baseUrl = config('app.url');
            $returnUrl = "plateauapps://app/payment-result?method={$paymentMethod}&status=success&transactionId={$naissance->reference}";
            $cancelUrl = "plateauapps://app/payment-result?method={$paymentMethod}&status=cancel&transactionId={$naissance->reference}";
            $fallbackReturnUrl = $baseUrl . "/user/payment/success?reference=" . urlencode($naissance->reference) . "&type=naissance";
            $fallbackCancelUrl = $baseUrl . "/user/payment/cancel?reference=" . urlencode($naissance->reference) . "&type=naissance";

            // Si c'est Wave, utiliser le service Wave
            if (strtolower($paymentMethod) === 'wave') {
                $waveService = app(\App\Services\WaveService::class);
                $checkoutSession = $waveService->createCheckoutSession(
                    $totalAmount,
                    'XOF',
                    $fallbackReturnUrl,
                    $fallbackCancelUrl,
                    $naissance->reference
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
                    'generated_transaction_id' => $naissance->reference,
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
                'transaction_id' => $naissance->reference,
                'amount' => $totalAmount,
                'currency' => 'XOF',
                'description' => "Paiement pour " . $naissance->reference,
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
                        'generated_transaction_id' => $naissance->reference,
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
            Log::error('Exception in generatePaymentLink: ' . $e->getMessage(), ['reference' => $naissance->reference]);
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
     * Retente le paiement pour une demande de naissance échouée.
     * POST /api/utilisateurs/demandes/naissance/{naissance}/retry-payment
     */
    public function retryPayment(Request $request, Naissance $naissance): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($naissance->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier si le paiement est nécessaire
            $totalAmount = (float) $naissance->montant_timbre + (float) $naissance->montant_livraison;
            if ($totalAmount <= 0) {
                return response()->json(['success' => false, 'message' => 'Cette demande ne nécessite pas de paiement.'], 400);
            }

            // 3. Vérifier l'état
            if (!in_array($naissance->etat, ['paiement_echoue', 'en attente de paiement'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être payée à nouveau (état actuel: ' . $naissance->etat . ')'
                ], 400);
            }

            // 4. Mettre à jour l'état
            $naissance->etat = 'paiement_echoue';
            $naissance->statut_livraison = 'paiement_echoue';
            $naissance->save();

            // 5. Générer le nouveau lien de paiement 
            $paymentMethod = $request->input('payment_method', 'wave'); // Valeur par défaut
            $totalAmount = (float) $naissance->montant_timbre + (float) $naissance->montant_livraison;
            $paymentLinkResult = $this->generatePaymentLink($naissance, $totalAmount, $paymentMethod);

            // 6. Gérer l'échec de la génération
            if (!$paymentLinkResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de la génération du nouveau lien de paiement.',
                    'error_details' => $paymentLinkResult['error_details']
                ], 500);
            }

            // 7. Succès !
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
                    'demande' => $this->formatDemandeResponse($naissance)
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeNaissanceController@retryPayment: ' . $e->getMessage(), ['naissance_id' => $naissance->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne lors de la tentative de paiement: ' . $e->getMessage()
            ], 500);
        }
    }
    public function relancerDemande(Request $request, Naissance $naissance): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($naissance->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier l'état
            if ($naissance->etat !== 'rejetée') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être relancée (état actuel: ' . $naissance->etat . ')'
                ], 400);
            }

            // 3. Mettre à jour l'état
            $naissance->etat = 'en attente';

            if ($naissance->choix_option === 'livraison') {
                $naissance->statut_livraison = 'en attente';
            } else {
                $naissance->statut_livraison = null;
            }

            // --- MISE À JOUR DEMANDÉE ---
            // Force la date de création à être la date actuelle.
            $naissance->created_at = now();

            $naissance->save();

            // 4. Succès !
            return response()->json([
                'success' => true,
                'message' => 'Demande relancée avec succès. Elle est maintenant "en attente".',
                'data' => [
                    'demande' => $this->formatDemandeResponse($naissance)
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeNaissanceController@relancerDemande: ' . $e->getMessage(), ['naissance_id' => $naissance->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne lors de la relance de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier une demande rejetée
     * POST /api/utilisateurs/demandes/naissance/{naissance}/modifier
     */
    public function modifierDemande(Request $request, Naissance $naissance): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($naissance->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier si la demande a été rejetée (peut_modifier == true ou etat == 'rejetée')
            if ($naissance->peut_modifier || $naissance->etat === 'rejetée') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande a été rejetée. Veuillez utiliser l\'API de modification des informations rejetées pour corriger les champs requis.'
                ], 400);
            }

            // 3. Vérifier que la demande est libre de modification (agent_id est NULL)
            $isNotAssigned = is_null($naissance->agent_id);
            if (!$isNotAssigned) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être modifiée complètement (déjà attribuée à un agent)'
                ], 400);
            }

            // 3. Règles de validation pour modification complète
            $rules = [
                'pour' => 'nullable|string|max:255',
                'type' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'number' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere|nullable|string|max:255',
                'DateR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere|nullable|date',
                'nom_prenoms_pere' => 'nullable|string|max:255',
                'nom_prenoms_mere' => 'nullable|string|max:255',
                'commune' => 'required|string|max:255',
                'commune_naissance' => 'required|string|max:255',
                'qty_simple' => 'nullable|integer|min:0|max:10',
                'qty_integral' => 'nullable|integer|min:0|max:10',
                'CNI' => $naissance->CNI ? 'nullable' : 'required',
                'relation' => 'nullable|string|in:enfant,parent,connaissance',
                'document_autorisation' => 'required_if:relation,connaissance|nullable|file|mimes:jpeg,png,jpg,pdf,heic',
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

            // 5. Validation IA Gemini de la pièce d'identité CNI si elle est fournie
            if ($request->hasFile('CNI')) {
                $geminiService = app(\App\Services\GeminiValidationService::class);
                $validation = $geminiService->validateIdentityDocument($request->file('CNI'));
                if (!$validation['isValid']) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité (CNI) a été rejetée par l'IA de la mairie : " . $validation['reason']
                    ], 422);
                }
            }

            // 6. Enregistrer les fichiers
            if ($request->hasFile('CNI')) {
                if ($naissance->CNI && Storage::disk('public')->exists($naissance->CNI)) {
                    Storage::disk('public')->delete($naissance->CNI);
                }
                $file = $request->file('CNI');
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $path = $file->storeAs('images/naissances/cni', $newFileName, 'public');
                $naissance->CNI = $path;
            }

            if ($request->hasFile('document_autorisation')) {
                if ($naissance->document_autorisation && Storage::disk('public')->exists($naissance->document_autorisation)) {
                    Storage::disk('public')->delete($naissance->document_autorisation);
                }
                $file = $request->file('document_autorisation');
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $path = $file->storeAs('images/naissances/autorisations', $newFileName, 'public');
                $naissance->document_autorisation = $path;
            }

            // 7. Mettre à jour tous les champs pour la modification complète
            $naissance->pour = $request->input('pour', $naissance->pour);
            $naissance->type = $request->input('type', $naissance->type);
            $naissance->name = $request->input('name', $naissance->name);
            $naissance->prenom = $request->input('prenom', $naissance->prenom);
            $naissance->nom_prenoms_pere = $request->input('nom_prenoms_pere', $naissance->nom_prenoms_pere);
            $naissance->nom_prenoms_mere = $request->input('nom_prenoms_mere', $naissance->nom_prenoms_mere);
            $naissance->number = $request->input('number', $naissance->number);
            $naissance->DateR = $request->DateR ? Carbon::parse($request->DateR)->format('Y-m-d') : $naissance->DateR;
            $naissance->commune = $request->input('commune', $naissance->commune);
            $naissance->commune_naissance = $request->input('commune_naissance', $naissance->commune_naissance);
            $naissance->relation = $request->input('relation', $naissance->relation);

            // Quantités
            $qtySimple = (int) $request->input('qty_simple', 0);
            $qtyIntegral = (int) $request->input('qty_integral', 0);
            if ($naissance->type === 'simple') {
                $qtyIntegral = 0;
                if ($qtySimple <= 0) $qtySimple = 1;
            } elseif ($naissance->type === 'integrale') {
                $qtySimple = 0;
                if ($qtyIntegral <= 0) $qtyIntegral = 1;
            } else {
                if ($qtySimple <= 0) $qtySimple = 1;
                if ($qtyIntegral <= 0) $qtyIntegral = 1;
            }
            $naissance->qty_simple = $qtySimple;
            $naissance->qty_integral = $qtyIntegral;
            $naissance->quantite = $qtySimple + $qtyIntegral;

            // 8. Gestion de la livraison et du paiement (calcul selon la logique du web)
            $originalChoixOption = $naissance->choix_option;
            $nouveauChoixOption = $request->input('choix_option', $originalChoixOption);
            
            // Normaliser le choix option
            $nouveauChoixOptionNormalise = strtolower($nouveauChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';
            $originalChoixOptionNormalise = strtolower($originalChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';

            // Calcul du reste à payer comme sur le web
            $user->refresh();
            // Créditer temporairement les timbres gratuits déjà accordés à cette demande pour le calcul
            $anciensTimbresGratuits = (int) $naissance->free_timbres_count;
            if ($anciensTimbresGratuits > 0) {
                $user->free_requests_used = max(0, $user->free_requests_used - $anciensTimbresGratuits);
                $user->save();
            }

            $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) $naissance->quantite);
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];
            $montantLivraisonCible = $nouveauChoixOptionNormalise === 'livraison' ? (float) $request->input('montant_livraison', 0) : 0;
            $nouveauMontantTotal = $montantTimbreTotal + $montantLivraisonCible;

            // Calcul du montant déjà payé s'il a déjà effectué un paiement
            $demandeDejaPayee = !in_array($naissance->etat, ['non_paye', 'paiement_en_attente', 'en attente de paiement']);
            $ancienMontantPaye = $demandeDejaPayee ? ((float)$naissance->montant_timbre + (float)$naissance->montant_livraison) : 0;

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
                    $naissance->choix_option = $originalChoixOption;
                } else {
                    $pendingDeliveryData = [
                        'choix_option' => 'Retrait sur place',
                        'montant_timbre' => $montantTimbreTotal,
                        'montant_livraison' => 0,
                    ];
                    // Conserver l'ancienne option jusqu'au paiement
                    $naissance->choix_option = $originalChoixOption;
                }
            } else {
                $naissance->choix_option = $nouveauChoixOptionNormalise;
                $naissance->montant_timbre = $montantTimbreTotal;
                $naissance->is_free_request = $freeCalc['free_timbres'] > 0;
                $naissance->free_timbres_count = $freeCalc['free_timbres'];

                if ($nouveauChoixOptionNormalise === 'livraison') {
                    $naissance->montant_livraison = $montantLivraisonCible;
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
                } else {
                    $naissance->montant_livraison = 0;
                    $naissance->nom_destinataire = null;
                    $naissance->prenom_destinataire = null;
                    $naissance->email_destinataire = null;
                    $naissance->contact_destinataire = null;
                    $naissance->adresse_livraison = null;
                    $naissance->code_postal = null;
                    $naissance->ville = null;
                    $naissance->commune_livraison = null;
                    $naissance->quartier = null;
                }
            }

            // Réinitialiser l'état et désactiver la modification
            $naissance->etat = $needsPayment ? 'en attente de paiement' : 'en attente';
            $naissance->peut_modifier = false;
            $naissance->champs_a_modifier = null;
            $naissance->motif_de_rejet = null;
            $naissance->save();

            // Gestion de l'initiation du paiement si nécessaire
            if ($needsPayment && $pendingDeliveryData) {
                $totalAmount = $resteAPayer; // C'est le reste à payer

                $naissance->montant_timbre = $montantTimbreTotal;
                $naissance->is_free_request = $freeCalc['free_timbres'] > 0;
                $naissance->free_timbres_count = $freeCalc['free_timbres'];
                $naissance->save();

                // Mettre en cache les données de livraison
                \Illuminate\Support\Facades\Cache::put('pending_delivery_update_' . $naissance->reference, $pendingDeliveryData, now()->addDays(7));

                if ($totalAmount > 0) {
                    $paymentMethod = $request->input('payment_method', 'wave');
                    $paymentLinkResult = $this->generatePaymentLink($naissance, $totalAmount, $paymentMethod);

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
                            'demande' => $this->formatDemandeResponse($naissance, true)
                        ]
                    ]);
                } else {
                    $naissance->etat = 'en attente';
                    if ($freeCalc['free_timbres'] > 0) {
                        $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                    }
                    $naissance->save();
                    
                    // Appliquer la livraison en attente
                    $this->applyPendingDeliveryUpdate($naissance);
                }
            } else {
                if ($nouveauChoixOptionNormalise !== 'livraison') {
                    if ($freeCalc['free_timbres'] > 0) {
                        $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                    }
                    $naissance->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Demande modifiée avec succès et soumise à nouveau.',
                'requires_payment' => false,
                'data' => [
                    'demande' => $this->formatDemandeResponse($naissance, true)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeNaissanceController@modifierDemande: ' . $e->getMessage() . ' Ligne: ' . $e->getLine(), ['naissance_id' => $naissance->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Modifier uniquement les informations rejetées d'une demande de naissance
     * POST /api/utilisateurs/demandes/naissance/{naissance}/modifier-rejete
     */
    public function modifierDemandeRejete(Request $request, Naissance $naissance): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($naissance->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier que la demande a bien été rejetée et marquée comme modifiable par la mairie
            if (!$naissance->peut_modifier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être modifiée via cette API (elle n\'a pas été marquée comme rejetée/modifiable par la mairie)'
                ], 400);
            }

            // 3. Déterminer les champs rejetés à modifier
            $champsAModifier = json_decode($naissance->champs_a_modifier, true) ?? [];
            if (empty($champsAModifier)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun champ à modifier spécifié par la mairie'
                ], 400);
            }

            // Vérification restrictive : rejeter la modification de champs non spécifiés par la mairie
            $champsDemande = ['type', 'pour', 'name', 'prenom', 'number', 'DateR', 'commune', 'commune_naissance', 'quantite', 'qty_simple', 'qty_integral', 'CNI', 'relation', 'document_autorisation', 'nom_prenoms_pere', 'nom_prenoms_mere'];
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
                        $rules['type'] = 'required|string|max:255';
                        break;
                    case 'pour':
                        $rules['pour'] = 'nullable|string|max:255';
                        break;
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
                    case 'quantite':
                        $rules['quantite'] = 'required|integer|min:1|max:10';
                        break;
                    case 'CNI':
                        $rules['CNI'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic|max:25600';
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

            // 6. Validation IA Gemini de la pièce d'identité CNI si elle est fournie
            if ($request->hasFile('CNI')) {
                $geminiService = app(\App\Services\GeminiValidationService::class);
                $validation = $geminiService->validateIdentityDocument($request->file('CNI'));
                if (!$validation['isValid']) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité (CNI) a été rejetée par l'IA de la mairie : " . $validation['reason']
                    ], 422);
                }
            }

            // 7. Enregistrer les fichiers
            if ($request->hasFile('CNI')) {
                if ($naissance->CNI && Storage::disk('public')->exists($naissance->CNI)) {
                    Storage::disk('public')->delete($naissance->CNI);
                }
                $file = $request->file('CNI');
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $path = $file->storeAs('images/naissances/cni', $newFileName, 'public');
                $naissance->CNI = $path;
            }

            if ($request->hasFile('document_autorisation')) {
                if ($naissance->document_autorisation && Storage::disk('public')->exists($naissance->document_autorisation)) {
                    Storage::disk('public')->delete($naissance->document_autorisation);
                }
                $file = $request->file('document_autorisation');
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $path = $file->storeAs('images/naissances/autorisations', $newFileName, 'public');
                $naissance->document_autorisation = $path;
            }

            // 8. Mettre à jour uniquement les champs rejetés
            foreach ($champsAModifier as $champ) {
                if ($champ === 'DateR' && isset($validated['DateR'])) {
                    $naissance->DateR = Carbon::parse($validated['DateR'])->format('Y-m-d');
                } elseif ($champ !== 'CNI' && isset($validated[$champ])) {
                    $naissance->$champ = $validated[$champ];
                }
            }

            // Si la quantité a été modifiée ou que le type de document a changé dans les champs rejetés
            if (in_array('quantite', $champsAModifier) || in_array('type', $champsAModifier)) {
                $naissance->quantite = (int) $request->input('quantite', $naissance->quantite);
                if ($naissance->type === 'integrale') {
                    $naissance->qty_integral = $naissance->quantite;
                    $naissance->qty_simple = 0;
                } else {
                    $naissance->qty_simple = $naissance->quantite;
                    $naissance->qty_integral = 0;
                }
            }

            // 9. Gestion de la livraison et du paiement (calcul selon la logique du web)
            $originalChoixOption = $naissance->choix_option;
            $nouveauChoixOption = $request->input('choix_option', $originalChoixOption);
            
            // Normaliser le choix option
            $nouveauChoixOptionNormalise = strtolower($nouveauChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';
            $originalChoixOptionNormalise = strtolower($originalChoixOption) === 'livraison' ? 'livraison' : 'Retrait sur place';

            // Calcul du reste à payer comme sur le web
            $user->refresh();
            // Créditer temporairement les timbres gratuits déjà accordés à cette demande pour le calcul
            $anciensTimbresGratuits = (int) $naissance->free_timbres_count;
            if ($anciensTimbresGratuits > 0) {
                $user->free_requests_used = max(0, $user->free_requests_used - $anciensTimbresGratuits);
                $user->save();
            }

            $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) $naissance->quantite);
            $montantTimbreTotal = $freeCalc['montant_timbre_total'];
            $montantLivraisonCible = $nouveauChoixOptionNormalise === 'livraison' ? (float) $request->input('montant_livraison', 0) : 0;
            $nouveauMontantTotal = $montantTimbreTotal + $montantLivraisonCible;

            // Calcul du montant déjà payé s'il a déjà effectué un paiement
            $demandeDejaPayee = !in_array($naissance->etat, ['non_paye', 'paiement_en_attente', 'en attente de paiement']);
            $ancienMontantPaye = $demandeDejaPayee ? ((float)$naissance->montant_timbre + (float)$naissance->montant_livraison) : 0;

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
                    $naissance->choix_option = $originalChoixOption;
                } else {
                    $pendingDeliveryData = [
                        'choix_option' => 'Retrait sur place',
                        'montant_timbre' => $montantTimbreTotal,
                        'montant_livraison' => 0,
                    ];
                    // Conserver l'ancienne option jusqu'au paiement
                    $naissance->choix_option = $originalChoixOption;
                }
            } else {
                $naissance->choix_option = $nouveauChoixOptionNormalise;
                $naissance->montant_timbre = $montantTimbreTotal;
                $naissance->is_free_request = $freeCalc['free_timbres'] > 0;
                $naissance->free_timbres_count = $freeCalc['free_timbres'];

                if ($nouveauChoixOptionNormalise === 'livraison') {
                    $naissance->montant_livraison = $montantLivraisonCible;
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
                } else {
                    $naissance->montant_livraison = 0;
                    $naissance->nom_destinataire = null;
                    $naissance->prenom_destinataire = null;
                    $naissance->email_destinataire = null;
                    $naissance->contact_destinataire = null;
                    $naissance->adresse_livraison = null;
                    $naissance->code_postal = null;
                    $naissance->ville = null;
                    $naissance->commune_livraison = null;
                    $naissance->quartier = null;
                }
            }

            // Réinitialiser l'état et désactiver la modification
            $naissance->etat = $needsPayment ? 'en attente de paiement' : 'en attente';
            $naissance->peut_modifier = false;
            $naissance->champs_a_modifier = null;
            $naissance->motif_de_rejet = null;
            $naissance->save();

            // Gestion de l'initiation du paiement si nécessaire
            if ($needsPayment && $pendingDeliveryData) {
                $totalAmount = $resteAPayer; // C'est le reste à payer

                $naissance->montant_timbre = $montantTimbreTotal;
                $naissance->is_free_request = $freeCalc['free_timbres'] > 0;
                $naissance->free_timbres_count = $freeCalc['free_timbres'];
                $naissance->save();

                // Mettre en cache les données de livraison
                \Illuminate\Support\Facades\Cache::put('pending_delivery_update_' . $naissance->reference, $pendingDeliveryData, now()->addDays(7));

                if ($totalAmount > 0) {
                    $paymentMethod = $request->input('payment_method', 'wave');
                    $paymentLinkResult = $this->generatePaymentLink($naissance, $totalAmount, $paymentMethod);

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
                            'demande' => $this->formatDemandeResponse($naissance, true)
                        ]
                    ]);
                } else {
                    $naissance->etat = 'en attente';
                    if ($freeCalc['free_timbres'] > 0) {
                        $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                    }
                    $naissance->save();
                    
                    // Appliquer la livraison en attente
                    $this->applyPendingDeliveryUpdate($naissance);
                }
            } else {
                if ($nouveauChoixOptionNormalise !== 'livraison') {
                    if ($freeCalc['free_timbres'] > 0) {
                        $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                    }
                    $naissance->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Demande modifiée avec succès et soumise à nouveau.',
                'requires_payment' => false,
                'data' => [
                    'demande' => $this->formatDemandeResponse($naissance, true)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeNaissanceController@modifierDemandeRejete: ' . $e->getMessage() . ' Ligne: ' . $e->getLine(), ['naissance_id' => $naissance->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les champs à modifier pour une demande rejetée
     * GET /api/utilisateurs/demandes/naissance/{naissance}/champs-a-modifier
     */
    public function getChampsAModifier(Naissance $naissance): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($naissance->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier que la demande peut être modifiée
            if (!$naissance->peut_modifier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être modifiée'
                ], 400);
            }

            // 3. Récupérer les champs à modifier
            $champsNoms = json_decode($naissance->champs_a_modifier, true) ?? [];

            if (empty($champsNoms)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun champ à modifier spécifié'
                ], 400);
            }

            // 4. Mapping des champs avec label et type
            $fieldsMapping = [
                'type' => ['label' => 'Type de document', 'type' => 'text'],
                'pour' => ['label' => 'Bénéficiaire', 'type' => 'text'],
                'name' => ['label' => 'Nom', 'type' => 'text'],
                'prenom' => ['label' => 'Prénoms', 'type' => 'text'],
                'number' => ['label' => 'Numéro de registre', 'type' => 'text'],
                'DateR' => ['label' => 'Date de registre', 'type' => 'date'],
                'CNI' => ['label' => 'Pièce d\'identité', 'type' => 'file'],
                'commune' => ['label' => 'Commune', 'type' => 'text'],
                'quantite' => ['label' => 'Quantité', 'type' => 'number'],
            ];

            // 5. Construire la réponse avec les valeurs actuelles
            $champsAvecValeurs = [];
            foreach ($champsNoms as $champNom) {
                $fieldInfo = $fieldsMapping[$champNom] ?? ['label' => $champNom, 'type' => 'text'];
                $champsAvecValeurs[$champNom] = [
                    'label' => $fieldInfo['label'],
                    'type' => $fieldInfo['type'],
                    'value' => $naissance->$champNom ?? null,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $naissance->id,
                    'reference' => $naissance->reference,
                    'motif_de_rejet' => $naissance->motif_de_rejet,
                    'champs_a_modifier' => $champsAvecValeurs,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeNaissanceController@getChampsAModifier: ' . $e->getMessage(), ['naissance_id' => $naissance->id]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper pour formater la réponse de la demande (Spécifique à Naissance)
     */
    private function formatDemandeResponse(Naissance $naissance, bool $includeFiles = false)
    {
        // montant_timbre est le TOTAL des timbres payants (après déduction des timbres gratuits)
        $montant_total = $naissance->choix_option === 'livraison'
            ? (float) ($naissance->montant_timbre ?? 0) + (float) ($naissance->montant_livraison ?? 0)
            : 0;

        $data = [
            'id' => $naissance->id,
            'reference' => $naissance->reference,
            'type' => $naissance->type,
            'pour' => $naissance->pour,
            'name' => $naissance->name,
            'prenom' => $naissance->prenom,
            'number' => $naissance->number,
            'DateR' => $naissance->DateR,
            'commune' => $naissance->commune,
            'etat' => $naissance->etat,
            'choix_option' => $naissance->choix_option,
            'quantite' => (int) ($naissance->quantite ?? 1),
            'montant_timbre_unitaire' => (float) $naissance->montant_timbre,
            'montant_livraison' => (float) $naissance->montant_livraison,
            'montant_total' => $montant_total,
            'created_at' => $naissance->created_at->format('Y-m-d H:i:s'),
            'nom_destinataire' => $naissance->nom_destinataire,
            'prenom_destinataire' => $naissance->prenom_destinataire,
            'email_destinataire' => $naissance->email_destinataire,
            'contact_destinataire' => $naissance->contact_destinataire,
            'adresse_livraison' => $naissance->adresse_livraison,
            'ville' => $naissance->ville,
            'commune_livraison' => $naissance->commune_livraison,
            'quartier' => $naissance->quartier,
            'statut_livraison' => $naissance->statut_livraison,
        ];

        if ($includeFiles) {
            $data['documents'] = [
                'CNI' => $naissance->CNI ? Storage::url($naissance->CNI) : null,
            ];
            $data['updated_at'] = $naissance->updated_at->format('Y-m-d H:i:s');
        }

        return $data;
    }

    /**
     * Gère la notification de webhook de CinetPay pour les naissances
     */
    public function handlePaymentNotification(Request $request): JsonResponse
    {
        Log::info('Webhook CinetPay Reçu (Naissance) - request:', $request->all());

        $cinetpayTransactionId = $request->input('cpm_trans_id')
            ?? $request->input('transaction_id')
            ?? $request->input('data.cpm_trans_id')
            ?? null;

        if (empty($cinetpayTransactionId)) {
            Log::warning('Webhook CinetPay (Naissance): transaction_id manquant.', $request->all());
            return response()->json(['success' => false, 'message' => 'Transaction ID manquant'], 200);
        }

        $reference = $cinetpayTransactionId;
        if (strpos($cinetpayTransactionId, '_') !== false) {
            $parts = explode('_', $cinetpayTransactionId);
            $reference = $parts[0];
        }

        try {
            // ✅ Trouver la demande Naissance
            $naissance = Naissance::where('reference', $reference)->first();

            if (!$naissance) {
                Log::warning("Webhook CinetPay (Naissance): Aucune demande trouvée pour reference {$reference}.");
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

            Log::info("CinetPay (Naissance) check response status: {$response->status()} for {$cinetpayTransactionId}");

            if ($response->failed()) {
                Log::error("Webhook CinetPay (Naissance) {$cinetpayTransactionId}: échec check API.", ['body' => $response->body()]);
                return response()->json(['success' => false, 'message' => 'Vérification CinetPay échouée'], 500);
            }

            $verificationData = $response->json();
            Log::info("CinetPay (Naissance) check body for {$cinetpayTransactionId}:", $verificationData);

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
                        'naissance_id' => $naissance->id, // ✅ Clé étrangère Naissance
                        'user_id' => $naissance->user_id ?? null,
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
                    Log::info("Paiement (Naissance) enregistré pour {$cinetpayTransactionId}, id: {$paiement->id}");
                } catch (\Exception $e) {
                    Log::error("Erreur enregistrement paiement (Naissance) {$cinetpayTransactionId}: " . $e->getMessage(), ['exception' => $e]);
                }

                $this->applyPendingDeliveryUpdate($naissance);
                $naissance->etat = 'en attente';
                if ($naissance->choix_option === 'livraison') {
                    $naissance->statut_livraison = 'en attente';
                }
                $naissance->save();

                Log::info("Demande Naissance {$reference} mise à jour : en attente");

                // Envoi des notifications suite au paiement réussi
                try {
                    $user = $naissance->user;
                    if ($user) {
                        $yellikaSmsService = app(YellikaSmsService::class);
                        $phoneNumber = $user->indicatif . $user->contact;
                        $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de naissance a été confirmé. Référence : {$naissance->reference}.
Votre demande est maintenant en attente de traitement.";
                        $yellikaSmsService->sendSms($phoneNumber, $message);

                        Notification::send($user, new DemandeNaissanceConfirmationNotification($user, $naissance));
                    }
                } catch (\Exception $e) {
                    Log::error("Erreur notifications Webhook Naissance: " . $e->getMessage());
                }

                return response()->json(['success' => true, 'message' => 'Paiement accepté et traité'], 200);
            }

            // Si status différent
            $upper = strtoupper((string) $status);
            if ($upper === 'PENDING' || $upper === 'AWAITING') {
                $naissance->etat = 'en attente de paiement';
                $naissance->statut_livraison = 'en attente de paiement'; // Corrigé
                $naissance->save();
                Log::info("Demande Naissance {$reference} marquée en attente (Status: {$status})");
                return response()->json(['success' => true, 'message' => 'Paiement en attente'], 200);
            }

            // Pour REFUSED ou autres
            $naissance->etat = 'paiement_echoue';
            $naissance->statut_livraison = 'paiement_echoue';
            $naissance->save();
            Log::warning("Demande Naissance {$reference} paiement non accepté (status: {$status}).");
            return response()->json(['success' => true, 'message' => 'Paiement non accepté traité'], 200);
        } catch (\Exception $e) {
            Log::error("Webhook CinetPay (Naissance) {$cinetpayTransactionId}: Exception critique : " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Erreur interne'], 500);
        }
    }

    /**
     * NOUVEAU (Adapté)
     * API pour vérifier le statut d'un paiement de naissance.
     * GET /api/naissance/payment-status/{reference}
     */
    public function getPaymentStatus(Request $request, $reference): JsonResponse
    {
        try {
            // 1. Trouver la demande de naissance
            $naissance = Naissance::where('reference', $reference)->first();

            if (!$naissance) {
                return response()->json(['status' => 'not_found', 'message' => 'Demande non trouvée'], 404);
            }

            // 2. Calculer le montant total
            $montant_total_timbres = (float) ($naissance->montant_timbre ?? 0) * (int) ($naissance->quantite ?? 1);
            $montant_total = $naissance->choix_option === 'livraison'
                ? $montant_total_timbres + (float) ($naissance->montant_livraison ?? 0)
                : 0;

            // 3. Déterminer la date et l'heure
            $date_heure = $naissance->created_at->format('Y-m-d H:i:s');

            if ($naissance->etat === 'en attente') {
                $paiement = Paiement::where('naissance_id', $naissance->id) // ✅ Clé étrangère Naissance
                    ->where('status', 'ACCEPTED')
                    ->orderBy('paid_at', 'desc')
                    ->first();
                if ($paiement && $paiement->paid_at) {
                    $date_heure = Carbon::parse($paiement->paid_at)->format('Y-m-d H:i:s');
                } else {
                    $date_heure = $naissance->updated_at->format('Y-m-d H:i:s');
                }
            } elseif ($naissance->etat === 'paiement_echoue') {
                $date_heure = $naissance->updated_at->format('Y-m-d H:i:s');
            }

            // 4. Construire le JSON de réponse
            $responseData = [
                'status' => $naissance->etat,
                'data' => [
                    'type_document' => 'Acte de naissance', // ✅ Type Naissance
                    'quantite' => (int) $naissance->quantite,
                    'montant' => $montant_total,
                    'date_heure' => $date_heure,
                    'id_transaction' => $naissance->reference
                ]
            ];

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error("Erreur getPaymentStatus (Naissance) pour {$reference}: " . $e->getMessage());
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
    public function showRedirectPage(Request $request)
    {
        $transactionId = $request->input('transactionId') ?? $request->input('transaction_id');

        if (!$transactionId) {
            return view('payments.redirect_to_app', ['transactionId' => null]);
        }

        return view('payments.redirect_to_app', [
            'transactionId' => $transactionId
        ]);
    }

    /**
     * Supprimer une demande de naissance
     * DELETE /api/utilisateurs/demandes/naissance/{naissance}
     */
    public function destroy(Naissance $naissance): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Vérifier que l'utilisateur est propriétaire de la demande
            if ($naissance->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à supprimer cette demande'
                ], 403);
            }

            // 2. Vérifier l'état de la demande (SÉCURITÉ AJOUTÉE)
            // On nettoie la chaîne (minuscule + trim) pour éviter les erreurs de casse/espaces
            $etatActuel = mb_strtolower(trim($naissance->etat), 'UTF-8');

            // Liste des états interdits (versions avec et sans accents)
            $etatsInterdits = ['reçu', 'terminé', 'recu', 'termine', 'réçu'];

            if (in_array($etatActuel, $etatsInterdits)) {
                return response()->json([
                    'success' => false,
                    'message' => "Impossible de supprimer cette demande car elle est déjà en traitement ou finalisée. (État actuel : {$naissance->etat})"
                ], 400);
            }

            // Optionnel: Supprimer le fichier CNI
            // if ($naissance->CNI && Storage::disk('public')->exists($naissance->CNI)) {
            //     Storage::disk('public')->delete($naissance->CNI);
            // }

            // 3. Suppression
            $naissance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Demande de naissance supprimée avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeNaissanceController@destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la demande'
            ], 500);
        }
    }
}
