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
        // ... (Ton code index reste inchangé)
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
        // Le validateur reste le même, il vérifie déjà les champs de livraison
        // si choix_option=livraison
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
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();

            // Upload des fichiers (inchangé)
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
                    // !! Important : Correction du chemin stocké
                    $uploadedPaths[$fileKey] = $path; 
                }
            }

            // Génération de la référence (inchangé)
            $communeInitiale = strtoupper(substr($request->communeD ?: $user->commune ?: 'X', 0, 1));
            $anneeCourante = Carbon::now()->year;
            // NOTE: Assurez-vous que la méthode 'getNextId()' existe sur votre modèle Deces
            $nextId = Deces::max('id') + 1; // Solution simple si getNextId() n'existe pas
            $reference = 'AD' . str_pad($nextId, 4, '0', STR_PAD_LEFT) . $communeInitiale . $anneeCourante;

            // Création de la demande
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

            // --- DEBUT DE LA NOUVELLE LOGIQUE ---

            if ($request->choix_option === 'livraison') {
                // Enregistrer les informations de livraison
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

                // Définir l'état pour refléter l'attente de paiement
                // NOTE: Vous devriez ajouter une colonne 'payment_status' (ex: 'pending', 'paid')
                // pour une meilleure gestion. Ici, on utilise 'etat'.
                $deces->etat = 'en attente de paiement';
                $deces->statut_livraison = 'en attente de paiement';

            } else {
                // Option "retrait"
                $deces->etat = 'en attente';
                $deces->statut_livraison = null; // Pas de livraison
            }

            $deces->save();

            // --- NOUVELLE RÉPONSE CONDITIONNELLE ---

            // Si c'est un simple retrait, retourner la réponse standard
            if ($deces->choix_option === 'retrait') {
                return response()->json([
                    'success' => true,
                    'message' => 'Demande de décès (retrait) créée avec succès',
                    'requires_payment' => false, // Indiquer qu'aucun paiement n'est requis
                    'data' => [
                        'demande' => $this->formatDemandeResponse($deces)
                    ]
                ], 201);
            }

            // Si c'est une LIVRAISON, renvoyer les infos pour initier le paiement
            // Le client (mobile/postman) recevra ceci et saura qu'il doit payer.
            
            // Calculer le montant total (basé sur ton JS)
            $totalAmount = (float)$deces->montant_timbre + (float)$deces->montant_livraison;

            // Récupérer les infos CinetPay (basé sur ton JS)
            // !! ATTENTION : Stockez-les dans votre .env et config/services.php !!
            // $cinetpayApiKey = config('services.cinetpay.api_key');
            // $cinetpaySiteId = config('services.cinetpay.site_id');
            $cinetpayApiKey = '521006956621e4e7a6a3d16.70681548'; // PRIS DE TON JS
            $cinetpaySiteId = '935132'; // PRIS DE TON JS

            return response()->json([
                'success' => true,
                'message' => 'Demande créée, en attente de paiement.',
                'requires_payment' => true, // Indique au client qu'un paiement est requis
                
                // Détails pour le SDK CinetPay (côté mobile)
                'payment_details' => [
                    'transaction_id' => $deces->reference, // Utiliser la référence unique
                    'amount' => $totalAmount,
                    'currency' => 'XOF',
                    'description' => 'Paiement livraison Acte de Décès ' . $deces->reference,
                    
                    // Config pour le SDK Mobile CinetPay
                    'cinetpay_config' => [
                        'apikey' => $cinetpayApiKey,
                        'site_id' => (int)$cinetpaySiteId,
                        // !! IMPORTANT : Vous devez créer cette route de webhook !!
                        // 'notify_url' => route('api.cinetpay.notify.deces'), 
                      'notify_url' => 'https://plateau-apps.com/api/webhooks/cinetpay/notify/deces',
                        'mode' => 'PRODUCTION' // ou 'TEST'
                    ],
                    
                    // Infos client pour pré-remplir le SDK
                    'customer_info' => [
                        'customer_name' => $deces->nom_destinataire,
                        'customer_surname' => $deces->prenom_destinataire,
                        'customer_email' => $deces->email_destinataire,
                        'customer_phone_number' => $deces->contact_destinataire,
                        'customer_address' => $deces->adresse_livraison,
                        'customer_city' => $deces->ville,
                        'customer_country' => 'CI',
                        'customer_zip_code' => $deces->code_postal ?? '00225'
                    ]
                ],
                
                // Données de la demande créée
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
        // 1. Logguer TOUJOURS la requête brute pour le débogage
        Log::info('Webhook CinetPay Reçu (Deces):', $request->all());

        // 'cpm_transaction_id' est l'ID que NOUS avons envoyé (la référence de la demande)
        $transaction_id = $request->input('cpm_transaction_id');
        
        // 'cpm_trans_id' est l'ID unique de CinetPay
        $cinetpay_trans_id = $request->input('cpm_trans_id'); 

        if (empty($transaction_id) || empty($cinetpay_trans_id)) {
            Log::warning('Webhook CinetPay (Deces): ID de transaction manquant.');
            // On renvoie 200 pour que CinetPay arrête de notifier
            return response()->json(['success' => false, 'message' => 'Transaction ID manquant'], 200);
        }

        try {
            // 2. Trouver la demande correspondante
            $deces = Deces::where('reference', $transaction_id)
                          ->where('etat', 'en attente de paiement') // S'assurer qu'on ne la traite pas 2 fois
                          ->first();

            if (!$deces) {
                Log::warning("Webhook CinetPay (Deces): Demande $transaction_id non trouvée ou déjà traitée.");
                // Demande non trouvée ou déjà payée. Répondre 200 OK.
                return response()->json(['success' => true, 'message' => 'Déjà traité ou non trouvé'], 200);
            }

            // 3. !! SÉCURITÉ : VÉRIFIER LE STATUT AUPRÈS DE CINETPAY !!
            // Ne jamais faire confiance aveuglément au webhook.
            // On utilise l'API de CinetPay pour revérifier le statut réel de la transaction.
            
            // !! ATTENTION : NE PAS HARDCODER LES CLÉS EN PRODUCTION !!
            // Utilisez config('services.cinetpay.api_key') et config('services.cinetpay.site_id')
            $cinetpayApiKey = '521006956621e4e7a6a3d16.70681548'; // PRIS DE TON JS
            $cinetpaySiteId = '935132'; // PRIS DE TON JS
            $cinetpayUrl = 'https://api-checkout.cinetpay.com/v2/payment/check';

            $response = Http::post($cinetpayUrl, [
                'apikey' => $cinetpayApiKey,
                'site_id' => $cinetpaySiteId,
                'transaction_id' => $transaction_id
            ]);

            if ($response->failed()) {
                Log::error("Webhook CinetPay (Deces) $transaction_id: Échec de la vérification API.", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                // Erreur serveur. CinetPay réessaiera.
                return response()->json(['success' => false, 'message' => 'Vérification échouée'], 500);
            }

            $verificationData = $response->json();
            Log::info("Webhook CinetPay (Deces) $transaction_id: Réponse de vérification:", $verificationData);

            // 4. Traiter la réponse de VÉRIFICATION
            // On se base sur la réponse de l'API, PAS sur le webhook initial
            if (isset($verificationData['data']['status']) && $verificationData['data']['status'] === 'ACCEPTED') {
                
                // --- PAIEMENT CONFIRMÉ ---
                $deces->etat = 'en attente de livraison';
                $deces->statut_livraison = 'en attente';
                
                // Optionnel : stocker l'ID de CinetPay pour référence
                // $deces->cinetpay_ref = $cinetpay_trans_id; 
                
                // Optionnel : si tu as une colonne 'payment_status'
                // $deces->payment_status = 'paid';

                $deces->save();

                Log::info("Webhook CinetPay (Deces) $transaction_id: Paiement ACCEPTÉ et demande mise à jour.");

                // (Optionnel : Envoyer un email de confirmation au client ici)

            } else {
                // --- PAIEMENT ÉCHOUÉ OU EN ATTENTE ---
                $deces->etat = 'paiement échoué';
                $deces->statut_livraison = 'paiement échoué';
                $deces->save();
                
                Log::warning("Webhook CinetPay (Deces) $transaction_id: Paiement NON ACCEPTÉ (Statut: " . ($verificationData['data']['status'] ?? 'INCONNU') . ")");
            }

            // 5. Répondre 200 OK à CinetPay pour dire "Notification reçue et traitée"
            return response()->json(['success' => true, 'message' => 'Webhook traité'], 200);

        } catch (\Exception $e) {
            Log::error("Webhook CinetPay (Deces) $transaction_id: Erreur critique.", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // Erreur de notre côté (ex: BDD). Répondre 500 pour que CinetPay réessaie.
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