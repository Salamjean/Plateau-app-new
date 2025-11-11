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

class DemandeMariageController extends Controller
{
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
    public function store(Request $request): JsonResponse
    {
        // 1. Validation (Spécifique au Mariage)
        $validator = Validator::make($request->all(), [
            'nomEpoux' => 'required|string|max:255',
            'prenomEpoux' => 'required|string|max:255',
            'dateNaissanceEpoux' => 'required|date',
            'lieuNaissanceEpoux' => 'required|string|max:255',
            'quantite' => 'required|integer|min:1|max:10',
            'pieceIdentite' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'extraitMariage' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'choix_option' => 'required|in:retrait,livraison',
            'commune' => 'nullable|string|max:255',
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
            $nextId = Mariage::max('id') + 1; 
            $reference = 'AM' . str_pad($nextId, 4, '0', STR_PAD_LEFT) . $communeInitiale . $anneeCourante; // ✅ Préfixe AM

            // 4. Création de la demande (Spécifique au Mariage)
            $mariage = new Mariage();
            $mariage->nomEpoux = $request->nomEpoux;
            $mariage->prenomEpoux = $request->prenomEpoux;
            $mariage->dateNaissanceEpoux = $request->dateNaissanceEpoux;
            $mariage->lieuNaissanceEpoux = $request->lieuNaissanceEpoux;
            $mariage->quantite = $request->quantite;
            $mariage->pieceIdentite = $uploadedPaths['pieceIdentite'] ?? null;
            $mariage->extraitMariage = $uploadedPaths['extraitMariage'] ?? null;
            $mariage->commune = $commune;
            $mariage->choix_option = $request->choix_option;
            $mariage->user_id = $user->id;
            $mariage->reference = $reference;

            if ($request->choix_option === 'livraison') {
                $mariage->montant_timbre = $request->montant_timbre; // Prix unitaire
                $mariage->montant_livraison = $request->montant_livraison;
                $mariage->nom_destinataire = $request->nom_destinataire;
                $mariage->prenom_destinataire = $request->prenom_destinataire;
                $mariage->email_destinataire = $request->email_destinataire;
                $mariage->contact_destinataire = $request->contact_destinataire;
                $mariage->adresse_livraison = $request->adresse_livraison;
                $mariage->code_postal = $request->code_postal;
                $mariage->ville = $request->ville;
                $mariage->commune_livraison = $request->commune_livraison;
                $mariage->quartier = $request->quartier;
                $mariage->etat = 'en attente de paiement';
                $mariage->statut_livraison = 'en attente de paiement';
            } else {
                $mariage->etat = 'en attente';
                $mariage->statut_livraison = null;
            }

            $mariage->save();

            // 5. Réponse conditionnelle (Cas "Retrait")
            if ($mariage->choix_option === 'retrait') {
                return response()->json([
                    'success' => true,
                    'message' => 'Demande de mariage (retrait) créée avec succès',
                    'requires_payment' => false,
                    'data' => ['demande' => $this->formatDemandeResponse($mariage)]
                ], 201);
            }

            // --- DEBUT DE LA LOGIQUE DE PAIEMENT (Adaptée) ---
            
            // 6. Préparer l'appel à CinetPay
            $baseUrl = config('app.url'); // ✅ Utilisation de l'URL de config
            
            // URLs de retour Deep link
            $returnUrl = "plateauapps://payment?cinetpay=true&transactionId={$mariage->reference}";
            $cancelUrl = "plateauapps://payment?cinetpay=false&transactionId={$mariage->reference}";
            
            // ✅ URLs dynamiques pour Mariage
            $fallbackReturnUrl = $baseUrl . "/mariage/paiement/redirect-to-app?transactionId=" . urlencode($mariage->reference);
            $fallbackCancelUrl = $baseUrl . "/mariage/paiement/redirect-to-app?cancel=1&transactionId=" . urlencode($mariage->reference);
            $notifyUrl = $baseUrl . "/api/webhooks/cinetpay/notify/mariage";
            
            // Calcul du montant total
            $cout_total_timbres = (float)$mariage->montant_timbre * (int)$mariage->quantite;
            $totalAmount = $cout_total_timbres + (float)$mariage->montant_livraison;

            $cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548'); 
            $cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');

            // ID de transaction CinetPay unique
            $cinetpayTransactionId = $mariage->reference . '_' . time();

            $paymentData = [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $cinetpayTransactionId,
                'amount' => $totalAmount,
                'currency' => 'XOF',
                 // ✅ Description Mariage
                'description' => "Paiement ({$mariage->quantite}x timbre + livr) Acte Mariage {$mariage->reference}",
                'notify_url' => $notifyUrl, // ✅ URL dynamique
                'return_url' => $fallbackReturnUrl, // ✅ URL dynamique
                'cancel_url' => $fallbackCancelUrl, // ✅ URL dynamique

                'mode' => 'PRODUCTION',
                'channels' => 'ALL',

                // Customer info
                'customer_name' => $mariage->nom_destinataire,
                'customer_surname' => $mariage->prenom_destinataire,
                'customer_email' => $mariage->email_destinataire,
                'customer_phone_number' => $mariage->contact_destinataire,
                'customer_address' => $mariage->adresse_livraison,
                'customer_city' => $mariage->ville,
                'customer_country' => 'CI',
                'customer_zip_code' => $mariage->code_postal ?? '00225'
            ];
            
            // 7. Appel API à CinetPay
            $response = Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);
 
            // 8. Gérer l'échec de l'appel
            if ($response->failed() || $response->json('code') !== '201') {
                Log::error('Erreur CinetPay (Génération lien Mariage): ' . $response->body(), ['transaction_id' => $mariage->reference]);
                return response()->json([
                    'success' => false,
                    'message' => 'Demande créée, mais échec de la génération du lien de paiement. Veuillez réessayer.',
                    'error_details' => $response->json() ?? $response->body()
                ], 500);
            }
 
            // 9. Succès ! Extraire le lien de paiement
            $cinetpayResponseData = $response->json('data');
 
            // 10. Renvoyer la réponse JSON
            return response()->json([
                'success' => true,
                'message' => 'Demande créée. Utilisez le payment_url pour payer.',
                'requires_payment' => true,
 
                'payment_details' => [
                    'payment_url' => $cinetpayResponseData['payment_url'],
                    'payment_token' => $cinetpayResponseData['payment_token'],
                    'transaction_id' => $cinetpayTransactionId,
                    'mode' => 'PRODUCTION',
                    'return_url_deep_link' => $returnUrl,
                    'cancel_url_deep_link' => $cancelUrl,
                    'return_url_web_fallback' => $fallbackReturnUrl,
                    'cancel_url_web_fallback' => $fallbackCancelUrl,
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

    /**
     * Helper pour formater la réponse de la demande (Spécifique au Mariage)
     */
    private function formatDemandeResponse(Mariage $mariage, bool $includeFiles = false)
    {
        $montant_total_timbres = (float)($mariage->montant_timbre ?? 0) * (int)($mariage->quantite ?? 1);
        $montant_total = $mariage->choix_option === 'livraison' 
            ? $montant_total_timbres + (float)($mariage->montant_livraison ?? 0) 
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
            'quantite' => (int)($mariage->quantite ?? 1),
            'montant_timbre_unitaire' => (float)$mariage->montant_timbre,
            'montant_livraison' => (float)$mariage->montant_livraison,
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
                $normalized = preg_replace('/[^\d\.,-]/', '', (string)$amount);
                $normalized = str_replace(',', '.', $normalized);
                if (is_numeric($normalized)) {
                    $montantFloat = (float)$normalized;
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
                $mariage->etat = 'en_attente_de_livraison';
                $mariage->statut_livraison = 'en attente';
                $mariage->save();
    
                Log::info("Demande (Mariage) {$cinetpayTransactionId} mise à jour : en_attente_de_livraison");
    
                return response()->json(['success' => true, 'message' => 'Paiement accepté et traité'], 200);
            }
    
            // Si status différent
            $upper = strtoupper((string)$status);
            if ($upper === 'PENDING' || $upper === 'AWAITING') {
                $mariage->etat = 'en attente de paiement';
                $mariage->statut_livraison = 'en attente';
                $mariage->save();
                Log::info("Demande (Mariage) {$cinetpayTransactionId} marquée en attente (CinetPay status: {$status})");
                return response()->json(['success' => true, 'message' => 'Paiement en attente'], 200);
            }
    
            // Pour REFUSED ou autres
            $mariage->etat = 'paiement échoué';
            $mariage->statut_livraison = 'paiement échoué';
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
            $montant_total_timbres = (float)($mariage->montant_timbre ?? 0) * (int)($mariage->quantite ?? 1);
            $montant_total = $mariage->choix_option === 'livraison' 
                ? $montant_total_timbres + (float)($mariage->montant_livraison ?? 0) 
                : 0;

            // 3. Déterminer la date et l'heure
            $date_heure = $mariage->created_at->format('Y-m-d H:i:s');
            
            if ($mariage->etat === 'en_attente_de_livraison') {
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
            } elseif ($mariage->etat === 'paiement échoué') {
                $date_heure = $mariage->updated_at->format('Y-m-d H:i:s');
            }

            // 4. Construire le JSON de réponse
            $responseData = [
                'status' => $mariage->etat,
                'data' => [
                    'type_document' => 'Acte de mariage', // ✅ Type Mariage
                    'quantite' => (int)$mariage->quantite,
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
     * NOUVEAU (Adapté)
     * Supprimer une demande de mariage
     * DELETE /api/utilisateurs/demandes/mariage/{mariage}
     */
    public function destroy(Mariage $mariage): JsonResponse // ✅ Modèle Mariage
    {
        try {
            $user = Auth::user();
            
            if ($mariage->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à supprimer cette demande'
                ], 403);
            }
 
            // Optionnel: Supprimer les fichiers
            // Storage::disk('public')->delete([
            //     $mariage->pieceIdentite,
            //     $mariage->extraitMariage,
            // ]);
 
            $mariage->delete();
 
            return response()->json([
                'success' => true,
                'message' => 'Demande de mariage supprimée avec succès' // ✅ Message Mariage
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