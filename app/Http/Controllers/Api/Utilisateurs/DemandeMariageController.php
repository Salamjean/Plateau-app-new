<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Mariage;
use App\Models\Paiement; // --- AJOUT ---
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http; // --- AJOUT ---

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
                    // --- MODIFICATION --- : Utilisation de l'helper
                    'demandes' => $mariages->map(function ($demande) {
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
        // --- MODIFICATION --- : Ajout de 'quantite'
        $validator = Validator::make($request->all(), [
            'nomEpoux' => 'required|string|max:255',
            'prenomEpoux' => 'required|string|max:255',
            'dateNaissanceEpoux' => 'required|date',
            'lieuNaissanceEpoux' => 'required|string|max:255',
            
            // --- AJOUT --- (Basé sur MariageController.php)
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
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();

            // Upload des fichiers (Logique de chemin modifiée pour correspondre à Deces)
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
                    // --- MODIFICATION --- : Utilisation de storeAs pour un chemin relatif propre
                    $path = $file->storeAs("images/mariages/$subDir", $newFileName, 'public');
                    $uploadedPaths[$fileKey] = $path; // Stocke 'images/mariages/...'
                }
            }

            // Génération de la référence
            $commune = $request->commune ?: $user->commune;
            $communeInitiale = strtoupper(substr($commune ?: 'X', 0, 1));
            $anneeCourante = Carbon::now()->year;
            // --- MODIFICATION --- : Utilisation de Mariage::max('id') pour l'API
            $nextId = Mariage::max('id') + 1; 
            $reference = 'AM' . str_pad($nextId, 4, '0', STR_PAD_LEFT) . $communeInitiale . $anneeCourante;

            // Création de la demande
            $mariage = new Mariage();
            $mariage->nomEpoux = $request->nomEpoux;
            $mariage->prenomEpoux = $request->prenomEpoux;
            $mariage->dateNaissanceEpoux = $request->dateNaissanceEpoux;
            $mariage->lieuNaissanceEpoux = $request->lieuNaissanceEpoux;
            
            // --- AJOUT ---
            $mariage->quantite = $request->quantite;
            
            $mariage->pieceIdentite = $uploadedPaths['pieceIdentite'] ?? null;
            $mariage->extraitMariage = $uploadedPaths['extraitMariage'] ?? null;
            $mariage->commune = $commune;
            $mariage->choix_option = $request->choix_option;
            $mariage->user_id = $user->id;
            $mariage->reference = $reference;

            // Informations de livraison
            if ($request->choix_option === 'livraison') {
                $mariage->montant_timbre = $request->montant_timbre;
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
                // --- AJOUT --- : Logique d'état pour paiement
                $mariage->etat = 'en attente de paiement';
                $mariage->statut_livraison = 'en attente de paiement';
            } else {
                 $mariage->etat = 'en attente';
                 $mariage->statut_livraison = null;
            }

            $mariage->save();
            
            // --- DEBUT BLOC PAIEMENT (Porté de DecesController) ---

            // 5. Réponse conditionnelle (Cas "Retrait")
            if ($mariage->choix_option === 'retrait') {
                return response()->json([
                    'success' => true,
                    'message' => 'Demande de mariage (retrait) créée avec succès',
                    'requires_payment' => false,
                    'data' => ['demande' => $this->formatDemandeResponse($mariage)]
                ], 201);
            }

            // 6. Préparer l'appel à CinetPay (Cas "Livraison")
            $returnUrl = "plateauapps://payment?cinetpay=true&transactionId={$mariage->reference}";
            $cancelUrl = "plateauapps://payment?cinetpay=false&transactionId={$mariage->reference}";
            $fallbackReturnUrl = "https://plateau-apps.com/mariage/paiement/redirect-to-app?transactionId={$mariage->reference}";
            $fallbackCancelUrl = "https://plateau-apps.com/mariage/paiement/redirect-to-app?cancel=1&transactionId={$mariage->reference}";
            
            // --- MODIFICATION --- : Calcul du montant total
            $cout_total_timbres = (float)$mariage->montant_timbre * (int)$mariage->quantite;
            $totalAmount = $cout_total_timbres + (float)$mariage->montant_livraison;
            // --- FIN MODIFICATION ---

            $cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548'); 
            $cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');
            
            $paymentData = [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $mariage->reference,
                'amount' => $totalAmount, // Montant total corrigé
                'currency' => 'XOF',
                'description' => "Paiement ({$mariage->quantite}x timbre + livr) Acte Mariage {$mariage->reference}", // Description adaptée
                
                // --- MODIFICATION --- : URL de notification spécifique au mariage
                // 'notify_url' => 'https://plateau-apps.com/api/webhooks/cinetpay/notify/mariage',
                 'notify_url' => 'https://sindy-overmeek-congruently.ngrok-free.dev/api/webhooks/cinetpay/notify/mariage', // Pour test
                
                'return_url' => $fallbackReturnUrl, 
                'cancel_url' => $fallbackCancelUrl, 
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

            // 7. Faire l'appel API à CinetPay
            $response = Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);

            // 8. Gérer l'échec de l'appel à CinetPay
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

            // 10. Renvoyer la réponse JSON avec le payment_url
            return response()->json([
                'success' => true,
                'message' => 'Demande créée. Utilisez le payment_url pour payer.',
                'requires_payment' => true,

                'payment_details' => [
                    'payment_url' => $cinetpayResponseData['payment_url'],
                    'payment_token' => $cinetpayResponseData['payment_token'],
                    'transaction_id' => $mariage->reference,
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
            
            // --- FIN BLOC PAIEMENT ---

        } catch (\Exception $e) {
            Log::error('Erreur DemandeMariageController@store: ' . $e->getMessage() . ' Ligne: ' . $e->getLine());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la demande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * --- AJOUT ---
     * Helper pour formater la réponse de la demande
     */
    private function formatDemandeResponse(Mariage $mariage, bool $includeFiles = false)
    {
        // Calcul correct du montant total en incluant la quantité
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
            
            'quantite' => (int)($mariage->quantite ?? 1), // Assure que la quantité est un int
            
            'montant_timbre_unitaire' => (float)$mariage->montant_timbre,
            'montant_livraison' => (float)$mariage->montant_livraison,
            'montant_total' => $montant_total, // Montant total calculé
            
            'created_at' => $mariage->created_at->format('Y-m-d H:i:s'),

            // Infos de livraison
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

        // Ajout conditionnel des documents (pour la méthode index)
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
     * --- AJOUT ---
     * Gère la notification de webhook de CinetPay pour les mariages
     */
    public function handlePaymentNotification(Request $request): JsonResponse
    {
        // 1. Logguer la requête
        Log::info('Webhook CinetPay Reçu (Mariage) - request:', $request->all());
    
        $transaction_id = $request->input('cpm_trans_id') 
                            ?? $request->input('transaction_id') 
                            ?? $request->input('data.cpm_trans_id') 
                            ?? null;
    
        if (empty($transaction_id)) {
            Log::warning('Webhook CinetPay (Mariage): transaction_id manquant.', $request->all());
            return response()->json(['success' => false, 'message' => 'Transaction ID manquant'], 200);
        }
    
        try {
            // 2. Trouver la demande Mariage
            $mariage = Mariage::where('reference', $transaction_id)->first();
    
            if (!$mariage) {
                Log::warning("Webhook CinetPay (Mariage): Aucune demande trouvée pour reference {$transaction_id}.");
                return response()->json(['success' => true, 'message' => 'Demande non trouvée'], 200);
            }
    
            // 3. Vérifier le statut auprès de CinetPay
            $cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548');
            $cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');
            $cinetpayUrl = 'https://api-checkout.cinetpay.com/v2/payment/check';
    
            $response = Http::withoutVerifying()->post($cinetpayUrl, [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $transaction_id,
            ]);
    
            Log::info("CinetPay (Mariage) check response status: {$response->status()} for {$transaction_id}");
    
            if ($response->failed()) {
                Log::error("Webhook CinetPay (Mariage) {$transaction_id}: échec check API.", ['body' => $response->body()]);
                return response()->json(['success' => false, 'message' => 'Vérification CinetPay échouée'], 500);
            }
    
            $verificationData = $response->json();
            Log::info("CinetPay (Mariage) check body for {$transaction_id}:", $verificationData);
    
            // 4. Extraire info
            $data = $verificationData['data'] ?? $verificationData ?? [];
            $status = $data['status'] ?? $data['payment_status'] ?? null;
            $amount = $data['amount'] ?? $data['cpm_amount'] ?? $data['amount_paid'] ?? null;
            $operatorId = $data['cpm_payid'] ?? $data['payid'] ?? $data['pay_id'] ?? null;
            $paymentToken = $data['payment_token'] ?? $data['cpm_token'] ?? null;
            $paymentDate = $data['payment_date'] ?? $data['cpm_trans_date'] ?? $data['created_at'] ?? null;
            $operatorTransId = $data['cpm_trans_id'] ?? $data['transaction_id'] ?? $transaction_id;
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
    
            // 5. Si status ACCEPTED
            if (strtoupper($status) === 'ACCEPTED') {
    
                try {
                    // --- MODIFICATION --- : Création du paiement lié au mariage_id
                    $paiement = Paiement::create([
                        'mariage_id' => $mariage->id, // <- Champ adapté
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
                    Log::info("Paiement (Mariage) enregistré pour {$transaction_id}, id: {$paiement->id}");
                } catch (\Exception $e) {
                    Log::error("Erreur enregistrement paiement (Mariage) {$transaction_id}: " . $e->getMessage(), ['exception' => $e]);
                }
    
                // Mettre à jour le Mariage
                $mariage->etat = 'en attente de livraison';
                $mariage->statut_livraison = 'en attente';
                $mariage->save();
    
                Log::info("Demande Mariage {$transaction_id} mise à jour : en attente de livraison");
                return response()->json(['success' => true, 'message' => 'Paiement accepté et traité'], 200);
            }
    
            // 6. Si status différent
            $upper = strtoupper((string)$status);
            if ($upper === 'PENDING' || $upper === 'AWAITING') {
                $mariage->etat = 'en attente de paiement';
                $mariage->statut_livraison = 'en attente';
                $mariage->save();
                Log::info("Demande Mariage {$transaction_id} marquée en attente (Status: {$status})");
                return response()->json(['success' => true, 'message' => 'Paiement en attente'], 200);
            }
    
            // Pour REFUSED ou autres
            $mariage->etat = 'paiement échoué';
            $mariage->statut_livraison = 'paiement échoué';
            $mariage->save();
            Log::warning("Demande Mariage {$transaction_id} paiement non accepté (status: {$status}).");
            return response()->json(['success' => true, 'message' => 'Paiement non accepté traité'], 200);
    
        } catch (\Exception $e) {
            Log::error("Webhook CinetPay (Mariage) {$transaction_id}: Exception critique : " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Erreur interne'], 500);
        }
    }
}