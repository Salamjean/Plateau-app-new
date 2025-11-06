<?php

namespace App\Http\Controllers\Api\Utilisateurs;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use Illuminate\Http\JsonResponse;
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
                        return [
                            'id' => $demande->id,
                            'name' => $demande->name,
                            'numberR' => $demande->numberR,
                            'dateR' => $demande->dateR,
                            'reference' => $demande->reference,
                            'commune' => $demande->commune,
                            'etat' => $demande->etat,
                            'statut_livraison' => $demande->statut_livraison,
                            'choix_option' => $demande->choix_option,
                            'created_at' => $demande->created_at->format('Y-m-d H:i:s'),
                            'updated_at' => $demande->updated_at->format('Y-m-d H:i:s'),
                            'documents' => [
                                'CNIdfnt' => $demande->CNIdfnt ? Storage::url($demande->CNIdfnt) : null,
                                'CNIdcl' => $demande->CNIdcl ? Storage::url($demande->CNIdcl) : null,
                                'documentMariage' => $demande->documentMariage ? Storage::url($demande->documentMariage) : null,
                                'RequisPolice' => $demande->RequisPolice ? Storage::url($demande->RequisPolice) : null,
                            ]
                        ];
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
        // 1. Validation (inchangée)
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'numberR' => 'required|string|max:255',
            'dateR' => 'required|date',
            'CNIdfnt' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'CNIdcl' => 'required|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'documentMariage' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'RequisPolice' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:1000',
            'choix_option' => 'required|in:retrait,livraison',
            'communeD' => 'nullable|string|max:255',
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

            // 4. Création de la demande (inchangée)
            $deces = new Deces();
            $deces->name = $request->name;
            $deces->numberR = $request->numberR;
            $deces->dateR = $request->dateR;
            $deces->CNIdfnt = $uploadedPaths['CNIdfnt'] ?? null;
            $deces->CNIdcl = $uploadedPaths['CNIdcl'] ?? null;
            $deces->documentMariage = $uploadedPaths['documentMariage'] ?? null;
            $deces->RequisPolice = $uploadedPaths['RequisPolice'] ?? null;
            $deces->choix_option = $request->choix_option;
            $deces->commune = $request->communeD ?: $user->commune;
            $deces->user_id = $user->id;
            $deces->reference = $reference;

            if ($request->choix_option === 'livraison') {
                $deces->montant_timbre = $request->montant_timbre;
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

            // --- DEBUT DE LA NOUVELLE LOGIQUE (Cas "Livraison") ---
            
            // 6. Préparer l'appel à CinetPay pour générer le lien
            $totalAmount = (float)$deces->montant_timbre + (float)$deces->montant_livraison;
            $cinetpayApiKey = '521006956621e4e7a6a3d16.70681548'; // !! A METTRE DANS .env
            $cinetpaySiteId = '935132'; // !! A METTRE DANS .env
            
            $paymentData = [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $deces->reference,
                'amount' => $totalAmount,
                'currency' => 'XOF',
                'description' => 'Paiement livraison Acte de Décès ' . $deces->reference,
                // 'notify_url' => 'https://sindy-overmeek-congruently.ngrok-free.dev/api/webhooks/cinetpay/notify/deces',
               'notify_url' => 'https://plateau-apps.com/api/webhooks/cinetpay/notify/deces',
                'mode' => 'PRODUCTION',
                'channels' => 'ALL', // Important
                
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

            // 7. Faire l'appel API à CinetPay
            // $response = Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);
                $response = Http::post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);

            // 8. Gérer l'échec de l'appel à CinetPay
            if ($response->failed() || $response->json('code') !== '201') {
                Log::error('Erreur CinetPay (Génération lien): ' . $response->body(), ['transaction_id' => $deces->reference]);
                // La demande est créée, mais le lien a échoué.
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
                    'transaction_id' => $deces->reference, // Notre référence
                    'mode' => 'PRODUCTION'
                ],
                
                'data' => [
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
    private function formatDemandeResponse(Deces $deces)
    {
        return [
            'id' => $deces->id,
            'reference' => $deces->reference,
            'name' => $deces->name,
            'numberR' => $deces->numberR,
            'dateR' => $deces->dateR,
            'commune' => $deces->commune,
            'etat' => $deces->etat,
            'choix_option' => $deces->choix_option,
            'created_at' => $deces->created_at->format('Y-m-d H:i:s'),
            'montant_total' => $deces->choix_option === 'livraison' ? (float)$deces->montant_timbre + (float)$deces->montant_livraison : 0,
        ];
    }

    public function handlePaymentNotification(Request $request): JsonResponse
    {
        // 1. Logguer la requête
        Log::info('Webhook CinetPay Reçu (Deces):', $request->all());

        // --- CORRECTION ---
        // CinetPay renvoie NOTRE référence (ex: AD0012P2025) 
        // dans LEUR champ 'cpm_trans_id'.
        
        $transaction_id = $request->input('cpm_trans_id'); 

        if (empty($transaction_id)) {
            Log::warning('Webhook CinetPay (Deces): ID de transaction (cpm_trans_id) manquant.');
            return response()->json(['success' => false, 'message' => 'Transaction ID manquant'], 200);
        }
        // --- FIN CORRECTION ---

        try {
            // 2. Trouver la demande (Ce code est maintenant correct)
            $deces = Deces::where('reference', $transaction_id)
                          ->where('etat', 'en attente de paiement')
                          ->first();

            if (!$deces) {
                Log::warning("Webhook CinetPay (Deces): Demande $transaction_id non trouvée ou déjà traitée.");
                return response()->json(['success' => true, 'message' => 'Déjà traité ou non trouvé'], 200);
            }

            // 3. VÉRIFIER LE STATUT AUPRÈS DE CINETPAY
            $cinetpayApiKey = '521006956621e4e7a6a3d16.70681548';
            $cinetpaySiteId = '935132';
            $cinetpayUrl = 'https://api-checkout.cinetpay.com/v2/payment/check';

            // N'oubliez pas le withoutVerifying() pour votre test local !
            // $response = Http::withoutVerifying()->post($cinetpayUrl, [
            //     'apikey' => $cinetpayApiKey,
            //     'site_id' => $cinetpaySiteId,
            //     'transaction_id' => $transaction_id // On utilise notre $transaction_id
            // ]);
            $response = Http::post($cinetpayUrl, [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $transaction_id // On utilise notre $transaction_id
            ]);
            if ($response->failed()) {
                Log::error("Webhook CinetPay (Deces) $transaction_id: Échec de la vérification API.", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json(['success' => false, 'message' => 'Vérification échouée'], 500);
            }

            $verificationData = $response->json();
            Log::info("Webhook CinetPay (Deces) $transaction_id: Réponse de vérification:", $verificationData);

            // 4. Traiter la réponse de VÉRIFICATION
            if (isset($verificationData['data']['status']) && $verificationData['data']['status'] === 'ACCEPTED') {
                
                // --- PAIEMENT CONFIRMÉ ---
                $deces->etat = 'en attente de livraison';
                $deces->statut_livraison = 'en attente';
                $deces->save();

                Log::info("Webhook CinetPay (Deces) $transaction_id: Paiement ACCEPTÉ et demande mise à jour.");

            } else {
                // --- PAIEMENT ÉCHOUÉ ---
                $deces->etat = 'paiement échoué';
                $deces->statut_livraison = 'paiement échoué';
                $deces->save();
                
                Log::warning("Webhook CinetPay (Deces) $transaction_id: Paiement NON ACCEPTÉ (Statut: " . ($verificationData['data']['status'] ?? 'INCONNU') . ")");
            }

            // 5. Répondre 200 OK
            return response()->json(['success' => true, 'message' => 'Webhook traité'], 200);

        } catch (\Exception $e) {
            Log::error("Webhook CinetPay (Deces) $transaction_id: Erreur critique.", [
                'message' => $e->getMessage(),
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