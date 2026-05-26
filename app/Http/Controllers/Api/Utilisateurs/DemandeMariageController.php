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
            'payment_method' => 'required|string|in:wave,orange,mtn,moov,cinetpay',
            'pieceIdentite' => 'required',
            'extraitMariage' => 'nullable',
            'commune_mariage' => 'required|string',
            'pour' => 'nullable|string|max:255',
            'relation' => 'nullable|string|in:enfant,parent,connaissance',
            'document_autorisation' => 'required_if:relation,connaissance|nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
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

            if ($request->choix_option === 'livraison') {
                $montantTimbreTotal = $freeCalc['montant_timbre_total'];
                $montantLivraison = (float) $request->montant_livraison;
                $totalAmount = $montantTimbreTotal + $montantLivraison;

                $mariage->montant_timbre = $montantTimbreTotal;
                $mariage->montant_livraison = $montantLivraison;
                $mariage->is_free_request = $freeCalc['free_timbres'] > 0;
                $mariage->free_timbres_count = $freeCalc['free_timbres'];

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
                $totalAmount = 0;
                $mariage->etat = 'en attente';
                $mariage->statut_livraison = null;
                $mariage->montant_timbre = $freeCalc['montant_timbre_total'];
                $mariage->is_free_request = $freeCalc['free_timbres'] > 0;
                $mariage->free_timbres_count = $freeCalc['free_timbres'];
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
    private function generatePaymentLink(Mariage $mariage, $totalAmount, $paymentMethod): array
    {
        try {
            // 1. Préparer les URLs
            $baseUrl = config('app.url');
            $returnUrl = "plateauapps://app/payment-result?method={$paymentMethod}&status=success&transactionId={$mariage->reference}";
            $cancelUrl = "plateauapps://app/payment-result?method={$paymentMethod}&status=cancel&transactionId={$mariage->reference}";
            $fallbackReturnUrl = $baseUrl . "/user/payment/success?reference=" . urlencode($mariage->reference) . "&type=mariage";
            $fallbackCancelUrl = $baseUrl . "/user/payment/cancel?reference=" . urlencode($mariage->reference) . "&type=mariage";

            // Si c'est Wave, utiliser le service Wave
            if (strtolower($paymentMethod) === 'wave') {
                $waveService = app(\App\Services\WaveService::class);
                $checkoutSession = $waveService->createCheckoutSession(
                    $totalAmount,
                    'XOF',
                    $fallbackReturnUrl,
                    $fallbackCancelUrl,
                    $mariage->reference
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
                    'generated_transaction_id' => $mariage->reference,
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
                'transaction_id' => $mariage->reference,
                'amount' => $totalAmount,
                'currency' => 'XOF',
                'description' => "Paiement pour " . $mariage->reference,
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
                        'generated_transaction_id' => $mariage->reference,
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
        try {
            $user = Auth::user();

            // 1. Vérifier l'autorisation
            if ($mariage->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // 2. Vérifier si l'option est 'livraison'
            if ($mariage->choix_option !== 'livraison') {
                return response()->json(['success' => false, 'message' => 'Cette demande (retrait) ne nécessite pas de paiement.'], 400);
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
            $totalAmount = (float) $mariage->montant_timbre + (float) $mariage->montant_livraison;
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

            // 2. Vérifier que la demande peut être modifiée
            if (!$mariage->peut_modifier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette demande ne peut pas être modifiée'
                ], 400);
            }

            // 3. Récupérer les champs à modifier
            $champsAModifier = json_decode($mariage->champs_a_modifier, true) ?? [];

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
                        $rules['pieceIdentite'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic|max:25600';
                        break;
                    case 'extraitMariage':
                        $rules['extraitMariage'] = 'required|file|mimes:jpeg,png,jpg,pdf,heic|max:25600';
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
            $fileFields = ['pieceIdentite', 'extraitMariage'];
            $subDirs = [
                'pieceIdentite' => 'identite',
                'extraitMariage' => 'extrait',
            ];

            foreach ($champsAModifier as $champ) {
                if (in_array($champ, $fileFields) && $request->hasFile($champ)) {
                    // Supprimer l'ancien fichier si existe
                    if ($mariage->$champ && Storage::disk('public')->exists($mariage->$champ)) {
                        Storage::disk('public')->delete($mariage->$champ);
                    }

                    // Enregistrer le nouveau fichier
                    $file = $request->file($champ);
                    $extension = $file->getClientOriginalExtension();
                    $newFileName = (string) Str::uuid() . '.' . $extension;
                    $subDir = $subDirs[$champ] ?? '';
                    $path = $file->storeAs("images/mariages/$subDir", $newFileName, 'public');
                    $mariage->$champ = $path;
                } elseif (isset($validated[$champ])) {
                    $mariage->$champ = $validated[$champ];
                }
            }

            // 7. Réinitialiser l'état et désactiver la modification
            $mariage->etat = 'en attente';
            $mariage->peut_modifier = false;
            $mariage->champs_a_modifier = null;
            $mariage->motif_de_rejet = null;
            $mariage->save();

            return response()->json([
                'success' => true,
                'message' => 'Demande modifiée avec succès et soumise à nouveau.',
                'data' => [
                    'demande' => $this->formatDemandeResponse($mariage, true)
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@modifierDemande: ' . $e->getMessage(), ['mariage_id' => $mariage->id]);
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
        // montant_timbre est le TOTAL des timbres payants (après déduction des timbres gratuits)
        $montant_total = $mariage->choix_option === 'livraison'
            ? (float) ($mariage->montant_timbre ?? 0) + (float) ($mariage->montant_livraison ?? 0)
            : 0;

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
                $mariage->etat = 'en attente';
                $mariage->statut_livraison = 'en attente';
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
            // 1. Trouver la demande de mariage
            $mariage = Mariage::where('reference', $reference)->first();

            if (!$mariage) {
                return response()->json(['status' => 'not_found', 'message' => 'Demande non trouvée'], 404);
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
