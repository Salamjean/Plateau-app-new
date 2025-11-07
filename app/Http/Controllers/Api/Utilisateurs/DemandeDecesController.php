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

use Illuminate\Support\Facades\Http; // Pour faire l'appel de vérification

class DemandeDecesController extends Controller

{

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

     * @return JsonResponse

     */

     public function store(Request $request): JsonResponse
     {
         // 1. Validation (Mise à jour)
         $validator = Validator::make($request->all(), [
             'name' => 'required|string|max:255',
             'numberR' => 'required|string|max:255',
             'dateR' => 'required|date',
             
             // --- AJOUT --- (Basé sur DecesController.php)
             'quantite' => 'required|integer|min:1|max:10', 
             
             'CNIdfnt' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
             'CNIdcl' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
             'documentMariage' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:1000',
             'RequisPolice' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:1000',
             'choix_option' => 'required|in:retrait,livraison',
             'communeD' => 'nullable|string|max:255',
             
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
         ]);
 
         if ($validator->fails()) {
             return response()->json(['success' => false, 'message' => 'Erreur de validation', 'errors' => $validator->errors()], 422);
         }
 
         try {
             $user = Auth::user();
 
             // 2. Upload des fichiers (inchangé)
             $filesToUpload = [
                 'CNIdfnt' => 'cnid', 'CNIdcl' => 'cnid',
                 'documentMariage' => 'mariage', 'RequisPolice' => 'police',
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
 
             // 3. Génération de référence (inchangée)
             $communeInitiale = strtoupper(substr($request->communeD ?: $user->commune ?: 'X', 0, 1));
             $anneeCourante = Carbon::now()->year;
             $nextId = Deces::max('id') + 1;
             $reference = 'AD' . str_pad($nextId, 4, '0', STR_PAD_LEFT) . $communeInitiale . $anneeCourante;
 
             // 4. Création de la demande (Mise à jour)
             $deces = new Deces();
             $deces->name = $request->name;
             $deces->numberR = $request->numberR;
             $deces->dateR = $request->dateR;
 
             // --- AJOUT ---
             $deces->quantite = $request->quantite; 
             
             $deces->CNIdfnt = $uploadedPaths['CNIdfnt'] ?? null;
             $deces->CNIdcl = $uploadedPaths['CNIdcl'] ?? null;
             $deces->documentMariage = $uploadedPaths['documentMariage'] ?? null;
             $deces->RequisPolice = $uploadedPaths['RequisPolice'] ?? null;
             $deces->choix_option = $request->choix_option;
             $deces->commune = $request->communeD ?: $user->commune;
             $deces->user_id = $user->id;
             $deces->reference = $reference;
 
             if ($request->choix_option === 'livraison') {
                 $deces->montant_timbre = $request->montant_timbre; // Prix unitaire
                 $deces->montant_livraison = $request->montant_livraison;
                 $deces->nom_destinataire = $request->nom_destinataire;
                 $deces->prenom_destinataire = $request->prenom_destinataire;
                 $deces->email_destinataire = $request->email_destinataire;
                 $deces->contact_destinataire = $request->contact_destinataire;
                 $deces->adresse_livraison = $request->adresse_livraison;
                 $deces->code_postal = $request->code_postal;
                 $deces->ville = $request->ville;
                 $deces->commune_livraison = $request->commune_livraison;
                 $deces->quartier = $request->quartier;
                 $deces->etat = 'en attente de paiement';
                 $deces->statut_livraison = 'en attente de paiement';
             } else {
                 $deces->etat = 'en attente';
                 $deces->statut_livraison = null;
             }
 
             $deces->save();
 
             // 5. Réponse conditionnelle (Cas "Retrait" - inchangé)
             if ($deces->choix_option === 'retrait') {
                 return response()->json([
                     'success' => true,
                     'message' => 'Demande de décès (retrait) créée avec succès',
                     'requires_payment' => false,
                     'data' => ['demande' => $this->formatDemandeResponse($deces)]
                 ], 201);
             }
 
             // --- DEBUT DE LA LOGIQUE MISE A JOUR (Cas "Livraison") ---
             
             // 6. Préparer l'appel à CinetPay pour générer le lien
            
             // --- préparation des URLs de retour (inchangé) ---
             $returnUrl = "plateauapps://payment?cinetpay=true&transactionId={$deces->reference}";
             $cancelUrl = "plateauapps://payment?cinetpay=false&transactionId={$deces->reference}";
             $fallbackReturnUrl = "https://www.plateau-apps.com/deces/paiement/redirect-to-app?transactionId={$deces->reference}";
             $fallbackCancelUrl = "https://www.plateau-apps.com/deces/paiement/redirect-to-app?cancel=1&transactionId={$deces->reference}";
            //$fallbackReturnUrl = "https://sindy-overmeek-congruently.ngrok-free.dev/deces/paiement/redirect-to-app?transactionId={$deces->reference}";
           // $fallbackCancelUrl = "https://sindy-overmeek-congruently.ngrok-free.dev/deces/paiement/redirect-to-app?cancel=1&transactionId={$deces->reference}";
 
             
             // --- MODIFICATION --- : Calcul du montant total
           // --- Calcul du montant total ---
$cout_total_timbres = (float)$deces->montant_timbre * (int)$deces->quantite;
$totalAmount = $cout_total_timbres + (float)$deces->montant_livraison;

$cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548'); 
$cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');

// GÉNÉRER UN TRANSACTION_ID UNIQUE
$cinetpayTransactionId = $deces->reference . '_' . time(); // ou Str::random(8)

$paymentData = [
    'apikey' => $cinetpayApiKey,
    'site_id' => $cinetpaySiteId,
    'transaction_id' => $cinetpayTransactionId, // ← Utilisez l'ID unique
    'amount' => $totalAmount,
    'currency' => 'XOF',
    'description' => "Paiement ({$deces->quantite}x timbre + livr) Acte Décès {$deces->reference}",
   'notify_url' => 'https://www.plateau-apps.com/api/webhooks/cinetpay/notify/deces',
    // 'notify_url' => 'https://sindy-overmeek-congruently.ngrok-free.dev/api/webhooks/cinetpay/notify/deces',
    
    'return_url' => $fallbackReturnUrl, 
    'cancel_url' => $fallbackCancelUrl, 

    'mode' => 'PRODUCTION',
    'channels' => 'ALL',

    // Customer info
    'customer_name' => $deces->nom_destinataire,
    'customer_surname' => $deces->prenom_destinataire,
    'customer_email' => $deces->email_destinataire,
    'customer_phone_number' => $deces->contact_destinataire,
    'customer_address' => $deces->adresse_livraison,
    'customer_city' => $deces->ville,
    'customer_country' => 'CI',
    'customer_zip_code' => $deces->code_postal ?? '00225'
];
             // 7. Faire l'appel API à CinetPay (inchangé)
             $response = Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);
             // $response = Http::post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);
 
             // 8. Gérer l'échec de l'appel à CinetPay (inchangé)
             if ($response->failed() || $response->json('code') !== '201') {
                 Log::error('Erreur CinetPay (Génération lien): ' . $response->body(), ['transaction_id' => $deces->reference]);
                 return response()->json([
                     'success' => false,
                     'message' => 'Demande créée, mais échec de la génération du lien de paiement. Veuillez réessayer.',
                     'error_details' => $response->json() ?? $response->body()
                 ], 500);
             }
 
             // 9. Succès ! Extraire le lien de paiement (inchangé)
             $cinetpayResponseData = $response->json('data');
 
             // 10. Renvoyer la réponse JSON avec le payment_url (inchangé)
             return response()->json([
                 'success' => true,
                 'message' => 'Demande créée. Utilisez le payment_url pour payer.',
                 'requires_payment' => true,
 
                 'payment_details' => [
                     'payment_url' => $cinetpayResponseData['payment_url'],
                     'payment_token' => $cinetpayResponseData['payment_token'],
                    'transaction_id' => $deces->reference . '_' . time(), // ou utilisez un UUID
                     'mode' => 'PRODUCTION',
                     'return_url_deep_link' => $returnUrl,
                     'cancel_url_deep_link' => $cancelUrl,
                     'return_url_web_fallback' => $fallbackReturnUrl,
                     'cancel_url_web_fallback' => $fallbackCancelUrl,
                 ],
 
                 'data' => [
                     // La réponse formatée inclura maintenant la quantité et le montant total correct
                     'demande' => $this->formatDemandeResponse($deces) 
                 ]
             ], 201);
             
             // --- FIN DE LA NOUVELLE LOGIQUE ---
 
         } catch (\Exception $e) {
             Log::error('Erreur DemandeDecesController@store: ' . $e->getMessage() . ' Ligne: ' . $e->getLine());
             return response()->json([
                 'success' => false,
                 'message' => 'Erreur lors de la création de la demande: ' . $e->getMessage()
             ], 500);
         }
     }

    /**

     * Helper pour formater la réponse de la demande

     */

     private function formatDemandeResponse(Deces $deces, bool $includeFiles = false)
     {
         // --- MODIFICATION ---
         // Calcul correct du montant total en incluant la quantité
         // Utilise (?? 1) pour la quantité au cas où elle serait nulle (ancien enregistrement)
         // Utilise (?? 0) pour les montants au cas où ils seraient nuls
         $montant_total_timbres = (float)($deces->montant_timbre ?? 0) * (int)($deces->quantite ?? 1);
         $montant_total = $deces->choix_option === 'livraison' 
             ? $montant_total_timbres + (float)($deces->montant_livraison ?? 0) 
             : 0;
         // --- FIN MODIFICATION ---
 
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
             'quantite' => (int)$deces->quantite, 
             
             'montant_timbre_unitaire' => (float)$deces->montant_timbre, // Renommé pour plus de clarté
             'montant_livraison' => (float)$deces->montant_livraison,
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
     
             $response = Http::withoutVerifying()->post($cinetpayUrl, [
                 'apikey' => $cinetpayApiKey,
                 'site_id' => $cinetpaySiteId,
                 'transaction_id' => $transaction_id,
             ]);
     
             Log::info("CinetPay check response status: {$response->status()} for transaction {$transaction_id}");
     
             if ($response->failed()) {
                 Log::error("Webhook CinetPay (Deces) {$transaction_id}: échec check API.", [
                     'status' => $response->status(),
                     'body' => $response->body()
                 ]);
                 return response()->json(['success' => false, 'message' => 'Vérification CinetPay échouée'], 500);
             }
     
             $verificationData = $response->json();
             Log::info("CinetPay check body for {$transaction_id}:", $verificationData);
     
             // 4. Extraire info avec tolérance (plusieurs clés possibles)
             $data = $verificationData['data'] ?? $verificationData ?? [];
     
             $status = $data['status'] ?? $data['payment_status'] ?? null; // ACCEPTED / REFUSED / PENDING
     
             // Montant : essayer plusieurs clés
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
                     Log::info("Paiement enregistré pour {$transaction_id}, id paiement: {$paiement->id}");
                 } catch (\Exception $e) {
                     Log::error("Erreur enregistrement paiement pour {$transaction_id}: " . $e->getMessage(), [
                         'exception' => $e
                     ]);
                     // Ne pas aborter la suite : on continue à mettre à jour la demande
                 }
     
                 // Mettre à jour le Deces (quelque soit l'état antérieur)
                 $deces->etat = 'en attente de livraison';
                 $deces->statut_livraison = 'en attente';
                 $deces->save();
     
                 Log::info("Demande {$transaction_id} mise à jour : en attente de livraison");
     
                 return response()->json(['success' => true, 'message' => 'Paiement accepté et traité'], 200);
             }
     
             // 6. Si status différent -> marquer selon le cas
             $upper = strtoupper((string)$status);
             if ($upper === 'PENDING' || $upper === 'AWAITING') {
                 $deces->etat = 'en attente de paiement';
                 $deces->statut_livraison = 'en attente';
                 $deces->save();
     
                 Log::info("Demande {$transaction_id} marquée en attente (CinetPay status: {$status})");
                 return response()->json(['success' => true, 'message' => 'Paiement en attente'], 200);
             }
     
             // Pour REFUSED ou autres
             $deces->etat = 'paiement échoué';
             $deces->statut_livraison = 'paiement échoué';
             $deces->save();
     
             Log::warning("Demande {$transaction_id} paiement non accepté (status: {$status}).");
     
             return response()->json(['success' => true, 'message' => 'Paiement non accepté traité'], 200);
     
         } catch (\Exception $e) {
             Log::error("Webhook CinetPay (Deces) {$transaction_id}: Exception critique : " . $e->getMessage(), [
                 'trace' => $e->getTraceAsString()
             ]);
             return response()->json(['success' => false, 'message' => 'Erreur interne'], 500);
         }
     }
    

    

    /**

     * Supprimer une demande de décès

     * DELETE /api/utilisateurs/demandes/deces/{deces}

     */

     public function destroy(Deces $deces): JsonResponse
     {
         // ... (Ton code destroy reste inchangé)
         try {
             $user = Auth::user();
             
             // Vérifier que l'utilisateur est propriétaire de la demande
             if ($deces->user_id !== $user->id) {
                 return response()->json([
                     'success' => false,
                     'message' => 'Non autorisé à supprimer cette demande'
                 ], 403);
             }
 
             // Optionnel: Supprimer les fichiers associés
             // Storage::disk('public')->delete([
             //     $deces->CNIdfnt,
             //     $deces->CNIdcl,
             //     $deces->documentMariage,
             //     $deces->RequisPolice
             // ]);
 
             $deces->delete();
 
             return response()->json([
                 'success' => true,
                 'message' => 'Demande de décès supprimée avec succès'
             ]);
 
         } catch (\Exception $e) {
             Log::error('Erreur DemandeDecesController@destroy: ' . $e->getMessage());
             return response()->json([
                 'success' => false,
                 'message' => 'Erreur lors de la suppression de la demande'
             ], 500);
         }
     }

} 