<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
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

class DemandeNaissanceController extends Controller
{
    /**
     * Liste des demandes de naissance de l'utilisateur
     * GET /api/utilisateurs/demandes/naissance
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
                    // --- MODIFICATION --- : Utilisation de l'helper
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
     * POST /api/utilisateurs/demandes/naissance
     */
    public function store(Request $request): JsonResponse
    {
        // --- MODIFICATION --- : Ajout de 'quantite'
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|max:255',
            'pour' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'number' => 'required|string|max:255',
            'DateR' => 'required|date',
            'commune' => 'required|string|max:255',
            
            // --- AJOUT --- (Basé sur NaissanceController.php)
            'quantite' => 'required|integer|min:1|max:10',
            
            'CNI' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'choix_option' => 'required|in:retrait,livraison',
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

            // Upload du fichier CNI
            $uploadedPaths = [];
            if ($request->hasFile('CNI')) {
                $file = $request->file('CNI');
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                // Utilisation de storeAs pour un chemin propre
                $path = $file->storeAs("images/naissances/cni", $newFileName, 'public');
                $uploadedPaths['CNI'] = $path; // Stocke "images/naissances/cni/..."
            }

            // Génération de la référence
            $communeInitiale = strtoupper(substr($request->commune ?: 'X', 0, 1));
            $anneeCourante = Carbon::now()->year;
            // --- MODIFICATION --- : Utilisation de Naissance::max('id') pour l'API
            $nextId = Naissance::max('id') + 1;
            $reference = 'AN' . str_pad($nextId, 4, '0', STR_PAD_LEFT) . $communeInitiale . $anneeCourante;

            // Création de la demande
            $naissance = new Naissance();
            $naissance->pour = $request->pour;
            $naissance->type = $request->type;
            $naissance->name = $request->name;
            $naissance->prenom = $request->prenom;
            $naissance->number = $request->number;
            $naissance->DateR = $request->DateR;
            $naissance->commune = $request->commune;

            // --- AJOUT ---
            $naissance->quantite = $request->quantite;
            
            $naissance->CNI = $uploadedPaths['CNI'] ?? null;
            $naissance->choix_option = $request->choix_option;
            $naissance->user_id = $user->id;
            $naissance->reference = $reference;

            // Informations de livraison
            if ($request->choix_option === 'livraison') {
                $naissance->montant_timbre = $request->montant_timbre;
                $naissance->montant_livraison = $request->montant_livraison;
                $naissance->nom_destinataire = $request->nom_destinataire;
                $naissance->prenom_destinataire = $request->prenom_destinataire;
                $naissance->email_destinataire = $request->email_destinataire;
                $naissance->contact_destinataire = $request->contact_destinataire;
                $naissance->adresse_livraison = $request->adresse_livraison;
                $naissance->code_postal = $request->code_postal;
                $naissance->ville = $request->ville;
                $naissance->commune_livraison = $request->commune_livraison;
                $naissance->quartier = $request->quartier;
                // --- AJOUT --- : Logique d'état pour paiement
                $naissance->etat = 'en attente de paiement';
                $naissance->statut_livraison = 'en attente de paiement';
            } else {
                 $naissance->etat = 'en attente';
                 $naissance->statut_livraison = null;
            }

            $naissance->save();
            
            // --- DEBUT BLOC PAIEMENT (Porté de Mariage/Deces Controller) ---

            // 5. Réponse conditionnelle (Cas "Retrait")
            if ($naissance->choix_option === 'retrait') {
                return response()->json([
                    'success' => true,
                    'message' => 'Demande de naissance (retrait) créée avec succès',
                    'requires_payment' => false,
                    'data' => ['demande' => $this->formatDemandeResponse($naissance)]
                ], 201);
            }

            // 6. Préparer l'appel à CinetPay (Cas "Livraison")
            $returnUrl = "plateauapps://payment?cinetpay=true&transactionId={$naissance->reference}";
            $cancelUrl = "plateauapps://payment?cinetpay=false&transactionId={$naissance->reference}";
            $fallbackReturnUrl = "https://plateau-apps.com/naissance/paiement/redirect-to-app?transactionId={$naissance->reference}";
            $fallbackCancelUrl = "https://plateau-apps.com/naissance/paiement/redirect-to-app?cancel=1&transactionId={$naissance->reference}";
            
            // --- MODIFICATION --- : Calcul du montant total
            $cout_total_timbres = (float)$naissance->montant_timbre * (int)$naissance->quantite;
            $totalAmount = $cout_total_timbres + (float)$naissance->montant_livraison;
            // --- FIN MODIFICATION ---

            $cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548'); 
            $cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');
            
            $paymentData = [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $naissance->reference,
                'amount' => $totalAmount, // Montant total corrigé
                'currency' => 'XOF',
                'description' => "Paiement ({$naissance->quantite}x timbre + livr) Acte Naissance {$naissance->reference}", // Description adaptée
                
                // --- MODIFICATION --- : URL de notification spécifique à la naissance
                'notify_url' => 'https://plateau-apps.com/api/webhooks/cinetpay/notify/naissance',
                // 'notify_url' => 'https://sindy-overmeek-congruently.ngrok-free.dev/api/webhooks/cinetpay/notify/naissance', // Pour test
                
                'return_url' => $fallbackReturnUrl, 
                'cancel_url' => $fallbackCancelUrl, 
                'mode' => 'PRODUCTION',
                'channels' => 'ALL',

                // Customer info
                'customer_name' => $naissance->nom_destinataire,
                'customer_surname' => $naissance->prenom_destinataire,
                'customer_email' => $naissance->email_destinataire,
                'customer_phone_number' => $naissance->contact_destinataire,
                'customer_address' => $naissance->adresse_livraison,
                'customer_city' => $naissance->ville,
                'customer_country' => 'CI',
                'customer_zip_code' => $naissance->code_postal ?? '00225'
            ];

            // 7. Faire l'appel API à CinetPay
            $response = Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);

            // 8. Gérer l'échec de l'appel à CinetPay
            if ($response->failed() || $response->json('code') !== '201') {
                Log::error('Erreur CinetPay (Génération lien Naissance): ' . $response->body(), ['transaction_id' => $naissance->reference]);
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
                    'transaction_id' => $naissance->reference,
                    'mode' => 'PRODUCTION',
                    'return_url_deep_link' => $returnUrl,
                    'cancel_url_deep_link' => $cancelUrl,
                    'return_url_web_fallback' => $fallbackReturnUrl,
                    'cancel_url_web_fallback' => $fallbackCancelUrl,
                ],

                'data' => [
                    'demande' => $this->formatDemandeResponse($naissance)
                ]
            ], 201);
            
            // --- FIN BLOC PAIEMENT ---

        } catch (\Exception $e) {
            Log::error('Erreur DemandeNaissanceController@store: ' . $e->getMessage() . ' Ligne: ' . $e->getLine());
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
    private function formatDemandeResponse(Naissance $naissance, bool $includeFiles = false)
    {
        // Calcul correct du montant total en incluant la quantité
        $montant_total_timbres = (float)($naissance->montant_timbre ?? 0) * (int)($naissance->quantite ?? 1);
        $montant_total = $naissance->choix_option === 'livraison' 
            ? $montant_total_timbres + (float)($naissance->montant_livraison ?? 0) 
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
            
            'quantite' => (int)($naissance->quantite ?? 1), // Assure que la quantité est un int
            
            'montant_timbre_unitaire' => (float)$naissance->montant_timbre,
            'montant_livraison' => (float)$naissance->montant_livraison,
            'montant_total' => $montant_total, // Montant total calculé
            
            'created_at' => $naissance->created_at->format('Y-m-d H:i:s'),

            // Infos de livraison
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

        // Ajout conditionnel des documents (pour la méthode index)
        if ($includeFiles) {
             $data['documents'] = [
                'CNI' => $naissance->CNI ? Storage::url($naissance->CNI) : null,
            ];
            $data['updated_at'] = $naissance->updated_at->format('Y-m-d H:i:s');
        }

        return $data;
    }

    /**
     * --- AJOUT ---
     * Gère la notification de webhook de CinetPay pour les naissances
     */
    public function handlePaymentNotification(Request $request): JsonResponse
    {
        // 1. Logguer la requête
        Log::info('Webhook CinetPay Reçu (Naissance) - request:', $request->all());
    
        $transaction_id = $request->input('cpm_trans_id') 
                            ?? $request->input('transaction_id') 
                            ?? $request->input('data.cpm_trans_id') 
                            ?? null;
    
        if (empty($transaction_id)) {
            Log::warning('Webhook CinetPay (Naissance): transaction_id manquant.', $request->all());
            return response()->json(['success' => false, 'message' => 'Transaction ID manquant'], 200);
        }
    
        try {
            // 2. Trouver la demande Naissance
            $naissance = Naissance::where('reference', $transaction_id)->first();
    
            if (!$naissance) {
                Log::warning("Webhook CinetPay (Naissance): Aucune demande trouvée pour reference {$transaction_id}.");
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
    
            Log::info("CinetPay (Naissance) check response status: {$response->status()} for {$transaction_id}");
    
            if ($response->failed()) {
                Log::error("Webhook CinetPay (Naissance) {$transaction_id}: échec check API.", ['body' => $response->body()]);
                return response()->json(['success' => false, 'message' => 'Vérification CinetPay échouée'], 500);
            }
    
            $verificationData = $response->json();
            Log::info("CinetPay (Naissance) check body for {$transaction_id}:", $verificationData);
    
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
                    // --- MODIFICATION --- : Création du paiement lié au naissance_id
                    $paiement = Paiement::create([
                        'naissance_id' => $naissance->id, // <- Champ adapté
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
                    Log::info("Paiement (Naissance) enregistré pour {$transaction_id}, id: {$paiement->id}");
                } catch (\Exception $e) {
                    Log::error("Erreur enregistrement paiement (Naissance) {$transaction_id}: " . $e->getMessage(), ['exception' => $e]);
                }
    
                // Mettre à jour la Naissance
                $naissance->etat = 'en attente de livraison';
                $naissance->statut_livraison = 'en attente';
                $naissance->save();
    
                Log::info("Demande Naissance {$transaction_id} mise à jour : en attente de livraison");
                return response()->json(['success' => true, 'message' => 'Paiement accepté et traité'], 200);
            }
    
            // 6. Si status différent
            $upper = strtoupper((string)$status);
            if ($upper === 'PENDING' || $upper === 'AWAITING') {
                $naissance->etat = 'en attente de paiement';
                $naissance->statut_livraison = 'en attente';
                $naissance->save();
                Log::info("Demande Naissance {$transaction_id} marquée en attente (Status: {$status})");
                return response()->json(['success' => true, 'message' => 'Paiement en attente'], 200);
            }
    
            // Pour REFUSED ou autres
            $naissance->etat = 'paiement échoué';
            $naissance->statut_livraison = 'paiement échoué';
            $naissance->save();
            Log::warning("Demande Naissance {$transaction_id} paiement non accepté (status: {$status}).");
            return response()->json(['success' => true, 'message' => 'Paiement non accepté traité'], 200);
    
        } catch (\Exception $e) {
            Log::error("Webhook CinetPay (Naissance) {$transaction_id}: Exception critique : " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Erreur interne'], 500);
        }
    }
}