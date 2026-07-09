<?php

namespace App\Http\Controllers\User\Extrait\Deces;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\MaintenanceSetting;
use App\Notifications\DemandeDecesConfirmationNotification;
use App\Services\YellikaSmsService;
use App\Traits\HandlesFreeRequests;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DecesController extends Controller
{
    use HandlesFreeRequests;
    public function index()
    {
        // Récupérer les utilisateurs connecté
        $user = Auth::user();

        $deces = Deces::where('user_id', $user->id)->paye()->where('etat', '!=', 'terminé')->paginate(10);

        return view('user.deces.index', compact('deces'));
    }

    public function create()
    {
        $user = Auth::user();
        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining = $this->getRemainingFreeRequests($user);

        return view('user.deces.simple.create', [
            'user' => $user,
            'freeRequestsModeActive' => $freeRequestsModeActive,
            'freeRequestsRemaining' => $freeRequestsRemaining,
        ]);
    }

    public function store(Request $request, YellikaSmsService $yellikaSmsService, \App\Services\WaveService $waveService)
    {
        $request->validate([
            'type' => 'required',
            'pour' => 'nullable|string',
            'relation' => 'nullable|string|in:enfant,parent,connaissance',
            'document_autorisation' => 'required_if:relation,connaissance|nullable|file|mimes:jpeg,png,jpg,pdf|max:25600',
            'name' => 'required',
            'numberR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',
            'dateR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',
            'nom_prenoms_pere' => 'nullable|string|max:255',
            'nom_prenoms_mere' => 'nullable|string|max:255',
            'CNIdfnt' => 'required',
            'qty_simple' => 'nullable|integer|min:0|max:10',
            'qty_integral' => 'nullable|integer|min:0|max:10',
            'CNIdcl' => 'required',
            'commune' => 'required',
            'commune_deces' => 'required|string|max:255',
        ], [
            'name.required' => 'Le nom du défunt est obligatoire.',
            'numberR.required_without_all' => 'Le numéro de registre est obligatoire si les informations parentales ne sont pas fournies.',
            'dateR.required_without_all' => 'La date de registre est obligatoire si les informations parentales ne sont pas fournies.',
            'CNIdfnt.required' => 'Cet document est obligatoire.',
            'CNIdcl.required' => 'Cet document est obligatoire.',
            'qty_simple.integer' => 'La quantité doit être un nombre entier',
            'qty_integral.integer' => 'La quantité intégrale doit être un nombre entier',
            'documentMariage.required' => 'Cet document de mariage est obligatoire.',
            'RequisPolice.required' => 'Cet document requis de police est obligatoire.',
            'pActe.mimes' => 'Cet document d\'extrait de décès doit être un format de fichier valide (png, jpg, jpeg, pdf).',
            'CNIdfnt.mimes' => 'Cet document doit être un format de fichier valide (png, jpg, jpeg, pdf).',
            'document_autorisation.required_if' => 'Le document d\'autorisation est obligatoire pour une connaissance.',
        ]);

        $geminiService = app(\App\Services\GeminiValidationService::class);

        // Validation IA Gemini de la pièce d'identité du défunt (CNIdfnt)
        if ($request->hasFile('CNIdfnt')) {
            $validation = $geminiService->validateIdentityDocument($request->file('CNIdfnt'));
            if (!$validation['isValid']) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité du défunt a été rejetée par l'IA de la mairie : " . $validation['reason']
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['CNIdfnt' => "La pièce d'identité du défunt a été rejetée par l'IA : " . $validation['reason']])
                    ->withInput();
            }
        }

        // Validation IA Gemini de la pièce d'identité du déclarant (CNIdcl)
        if ($request->hasFile('CNIdcl')) {
            $validation = $geminiService->validateIdentityDocument($request->file('CNIdcl'));
            if (!$validation['isValid']) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité du déclarant a été rejetée par l'IA de la mairie : " . $validation['reason']
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['CNIdcl' => "La pièce d'identité du déclarant a été rejetée par l'IA : " . $validation['reason']])
                    ->withInput();
            }
        }

        $filesToUpload = [
            'pActe' => '', // Pas de sous-dossier
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

                // Stockage avec le disque 'public'
                $path = $file->storeAs("images/deces/$subDir", $newFileName, 'public');

                // Même format que naissance/mariage
                $uploadedPaths[$fileKey] = "images/deces/$subDir/$newFileName";
            }
        }

        // Vérifier si l'utilisateur est authentifié
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Vous devez être connecté pour effectuer cette action.');
        }

        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Générer la référence ici dans le contrôleur
        $communeInitiale = strtoupper(substr($request->commune ?: $user->commune ?: 'X', 0, 1)); // 'X' si commune est null ou vide
        $anneeCourante = Carbon::now()->year;
        $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $increment = Deces::getNextId();
        $reference = 'AD' . $randomDigits . $increment . $communeInitiale . $anneeCourante; // AD pour Acte de Decès

        // Récupérer les quantités selon le type de demande
        $qtySimple = (int) $request->input('qty_simple', 0);
        $qtyIntegral = (int) $request->input('qty_integral', 0);

        if ($request->type === 'simple') {
            $qtyIntegral = 0;
            if ($qtySimple <= 0) {
                $qtySimple = 1;
            }
        } elseif ($request->type === 'integrale') {
            $qtySimple = 0;
            if ($qtyIntegral <= 0) {
                $qtyIntegral = 1;
            }
        } else {
            // groupee (simple + integrale)
            if ($qtySimple <= 0) {
                $qtySimple = 1;
            }
            if ($qtyIntegral <= 0) {
                $qtyIntegral = 1;
            }
        }
        $totalQuantity = $qtySimple + $qtyIntegral;

        // Enregistrement de l'objet deces
        $deces = new Deces();
        $deces->type = $request->type;
        $deces->pour = $request->pour;
        $deces->relation = $request->relation;
        $deces->document_autorisation = $uploadedPaths['document_autorisation'] ?? null;
        $deces->name = $request->name;
        $deces->nom_prenoms_pere = $request->nom_prenoms_pere;
        $deces->nom_prenoms_mere = $request->nom_prenoms_mere;
        $deces->numberR = $request->numberR;
        $deces->dateR = $request->dateR;
        $deces->CNIdfnt = $uploadedPaths['CNIdfnt'] ?? null;
        $deces->CNIdcl = $uploadedPaths['CNIdcl'] ?? null;
        $deces->documentMariage = $uploadedPaths['documentMariage'] ?? null;
        $deces->RequisPolice = $uploadedPaths['RequisPolice'] ?? null;
        $deces->choix_option = $request->choix_option;
        $deces->qty_simple = $qtySimple;
        $deces->qty_integral = $qtyIntegral;
        $deces->quantite = $totalQuantity;
        $deces->commune = $request->commune ?: $user->commune; // Déterminer la commune
        $deces->commune_deces = $request->commune_deces;
        $deces->etat = 'non_paye';
        $deces->user_id = $user->id; // Lier la demande à l'utilisateur connecté
        $deces->reference = $reference; // Assignez la référence générée

        // === GESTION DES DEMANDES GRATUITES (MODE TEST) & CALCULS ===
        $user->refresh();
        $freeCalc = $this->calculateFreeRequestsDiscount($user, $totalQuantity);

        $montantTimbreTotal = $freeCalc['montant_timbre_total'];
        $montantLivraison = $request->input('choix_option') === 'livraison' ? (float) $request->input('montant_livraison') : 0;
        $totalAmount = $montantTimbreTotal + $montantLivraison;

        $deces->montant_timbre = $montantTimbreTotal;
        $deces->montant_livraison = $montantLivraison;
        $deces->is_free_request = $freeCalc['free_timbres'] > 0;
        $deces->free_timbres_count = $freeCalc['free_timbres'];

        if ($request->input('choix_option') === 'livraison') {
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

        $deces->save();

        Log::info("Demandes gratuites - Deces {$deces->reference}: {$freeCalc['free_timbres']} timbres gratuits, {$freeCalc['paid_timbres']} timbres payants. Total à payer: {$totalAmount}");

        if ($totalAmount > 0) {
            // Paiement requis → le compteur sera incrémenté APRÈS confirmation du paiement
            $paymentMethod = $request->input('payment_method', 'wave');
            if (strtolower($paymentMethod) === 'mtn') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Le paiement par MTN est actuellement indisponible. Veuillez utiliser un autre moyen de paiement.']);
                }
                return redirect()->back()->with('error', 'Le paiement par MTN est actuellement indisponible. Veuillez utiliser un autre moyen de paiement.')->withInput();
            }

            // Préparer les URLs de retour
            $baseUrl = config('app.url');
            $successUrl = $baseUrl . '/user/payment/success?reference=' . urlencode($deces->reference) . "&type=deces";
            $errorUrl = $baseUrl . '/user/payment/cancel?reference=' . urlencode($deces->reference) . "&type=deces";

            if (strtolower($paymentMethod) === 'wave') {
                // Créer une session de paiement Wave
                $checkoutSession = $waveService->createCheckoutSession(
                    $totalAmount,
                    'XOF',
                    $successUrl,
                    $errorUrl,
                    $deces->reference
                );

                if ($checkoutSession && isset($checkoutSession['wave_launch_url'])) {
                    return redirect($checkoutSession['wave_launch_url']);
                }

                Log::error('Échec de la création de la session Wave pour ' . $deces->reference);
                return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur lors de la préparation du paiement Wave. Veuillez réessayer.');
            } elseif (strtolower($paymentMethod) === 'mtn') {
                $mtnPhoneNumber = $request->input('mtn_number');
                // Format number to international format (starting with 225)
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
                    // Stocker le ReferenceId en session pour la vérification
                    session(['mtn_ref_' . $deces->reference => $response['referenceId']]);

                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'redirect_url' => route('user.payment.mtn.waiting', [
                                'reference' => $deces->reference,
                                'type' => 'deces'
                            ]),
                            'reference' => $deces->reference,
                            'mtn_ref' => $response['referenceId']
                        ]);
                    }

                    return redirect()->route('user.payment.mtn.waiting', [
                        'reference' => $deces->reference,
                        'type' => 'deces'
                    ]);
                }

                Log::error('Échec de la création de la session MTN pour ' . $deces->reference);
                return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur lors de la préparation du paiement MTN. Veuillez réessayer.');
            } elseif (strtolower($paymentMethod) === 'tresorpay') {
                $tresorPhone = $request->input('mtn_number');
                $tresorPhone = preg_replace('/[^0-9]/', '', $tresorPhone);

                $tresorService = app(\App\Services\TresorPayService::class);
                $response = $tresorService->initierPaiementDirect($tresorPhone, $totalAmount, $deces->reference, $user->name ?? 'Client', $user->prenoms ?? 'Plateau');

                if ($response && ($response['success'] ?? false)) {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => true,
                            'redirect_url' => route('user.payment.tresorpay.waiting', [
                                'reference' => $deces->reference,
                                'type' => 'deces'
                            ]),
                            'reference' => $deces->reference,
                        ]);
                    }

                    return redirect()->route('user.payment.tresorpay.waiting', [
                        'reference' => $deces->reference,
                        'type' => 'deces'
                    ]);
                }

                Log::error('Échec de la création de la session TrésorPay pour ' . $deces->reference);
                return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur TrésorPay: ' . ($response['message'] ?? 'Erreur inconnue.'));
            } else {
                // Générer la session CinetPay
                $channels = 'ALL';
                if (in_array(strtolower($paymentMethod), ['orange', 'mtn', 'moov'])) {
                    $channels = 'MOBILE_MONEY';
                }

                $cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548');
                $cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');

                try {
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', [
                        'apikey' => $cinetpayApiKey,
                        'site_id' => $cinetpaySiteId,
                        'transaction_id' => $deces->reference,
                        'amount' => $totalAmount,
                        'currency' => 'XOF',
                        'description' => "Paiement pour " . $deces->reference,
                        'return_url' => $successUrl,
                        'notify_url' => $baseUrl . '/api/webhook/cinetpay',
                        'channels' => $channels,
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data['data']['payment_url'])) {
                            return redirect($data['data']['payment_url']);
                        }
                    }

                    Log::error('Échec CinetPay: ' . $response->body());
                    return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur de génération du lien CinetPay.');
                } catch (\Exception $e) {
                    Log::error('Erreur Exception CinetPay: ' . $e->getMessage());
                    return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur interne de paiement.');
                }
            }
        } else {
            // Pas de montant à payer (0 à payer) → incrémenter maintenant
            $deces->etat = 'en attente';
            $deces->save();
            if ($freeCalc['free_timbres'] > 0) {
                $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
            }
        }

        $phoneNumber = $user->indicatif . $user->contact;
        $message = "Bonjour {$user->name}, votre demande d'extrait de décès a bien été transmise à la mairie du plateau. Référence: {$deces->reference}
Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com";
        $yellikaSmsService->sendSms($phoneNumber, $message);

        // Envoi de l'email de confirmation
        Notification::send($user, new DemandeDecesConfirmationNotification($user, $deces));

        return redirect()->route('user.extrait.deces.index')->with('success', 'Demande envoyée avec succès.');
    }

    /**
     * Modifier une demande rejetée ou non encore attribuée à un agent
     */
    public function modifierDemande(Request $request, $id)
    {
        $demande = Deces::where('user_id', Auth::id())
            ->where('id', $id)
            ->where(function ($query) {
                $query->where('peut_modifier', true)
                    ->orWhereNull('agent_id');
            })
            ->firstOrFail();

        $originalChoixOption = $demande->choix_option;

        $rules = [
            'type' => 'required',
            'name' => 'required|string|max:255',
            'numberR' => 'required|string|max:50',
            'dateR' => 'required|date',
            'commune' => 'required|string',
            'qty_simple' => 'nullable|integer|min:0|max:10',
            'qty_integral' => 'nullable|integer|min:0|max:10',
            'CNIdfnt' => $demande->CNIdfnt ? 'nullable' : 'required',
            'CNIdcl' => $demande->CNIdcl ? 'nullable' : 'required',
            'documentMariage' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:25600',
            'RequisPolice' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:25600',
        ];

        $validated = $request->validate($rules);

        // Initialiser les tableaux de modification en attente
        $pendingAttributes = [];
        $oldFilesToDelete = [];

        // Upload files
        foreach (['CNIdfnt', 'CNIdcl', 'documentMariage', 'RequisPolice'] as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $subDir = match ($fileKey) {
                    'CNIdfnt' => 'cnid',
                    'CNIdcl' => 'cnid',
                    'documentMariage' => 'mariage',
                    'RequisPolice' => 'police',
                    default => ''
                };
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;
                $file->storeAs("images/deces/$subDir", $newFileName, 'public');

                $pendingAttributes[$fileKey] = "images/deces/$subDir/$newFileName";
                if ($demande->$fileKey) {
                    $oldFilesToDelete[] = $demande->$fileKey;
                }
            }
        }

        // Quantities
        $qty_simple = (int)$request->input('qty_simple', 0);
        $qty_integral = (int)$request->input('qty_integral', 0);
        if ($request->type === 'simple') {
            $qty_integral = 0;
            if ($qty_simple <= 0) $qty_simple = 1;
        } elseif ($request->type === 'integrale') {
            $qty_simple = 0;
            if ($qty_integral <= 0) $qty_integral = 1;
        } else {
            if ($qty_simple <= 0) $qty_simple = 1;
            if ($qty_integral <= 0) $qty_integral = 1;
        }

        // Remplir les attributs modifiés
        $pendingAttributes['type'] = $request->input('type');
        $pendingAttributes['name'] = $request->input('name');
        $pendingAttributes['numberR'] = $request->input('numberR');
        $pendingAttributes['dateR'] = $request->input('dateR');
        $pendingAttributes['commune'] = $request->input('commune');
        $pendingAttributes['qty_simple'] = $qty_simple;
        $pendingAttributes['qty_integral'] = $qty_integral;
        $pendingAttributes['quantite'] = $qty_simple + $qty_integral;

        // Verification des paiements requis
        $user = Auth::user();
        $user->refresh();

        // Créditer temporairement les timbres gratuits déjà accordés à cette demande pour le calcul
        $anciensTimbresGratuits = (int) $demande->free_timbres_count;
        if ($anciensTimbresGratuits > 0) {
            $user->free_requests_used = max(0, $user->free_requests_used - $anciensTimbresGratuits);
            $user->save();
        }

        $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) ($qty_simple + $qty_integral));
        $montantTimbreTotal = $freeCalc['montant_timbre_total'];

        $newChoixOption = $request->input('choix_option');
        $montantLivraisonCible = $newChoixOption === 'livraison' ? (float) $request->input('montant_livraison', 0) : 0;

        $nouveauMontantTotal = $montantTimbreTotal + $montantLivraisonCible;

        // Calcul du montant déjà payé s'il a déjà effectué un paiement
        $demandeDejaPayee = !in_array($demande->etat, ['non_paye', 'paiement_en_attente', 'en attente de paiement']);
        $ancienMontantPaye = $demandeDejaPayee ? ((float)$demande->montant_timbre + (float)$demande->montant_livraison) : 0;

        $resteAPayer = $nouveauMontantTotal - $ancienMontantPaye;
        $needsPayment = $resteAPayer > 0;

        $pendingDeliveryData = null;

        if ($needsPayment) {
            // Ajouter les modifications d'informations aux pendingAttributes
            $pendingAttributes['montant_timbre'] = $montantTimbreTotal;
            $pendingAttributes['is_free_request'] = $freeCalc['free_timbres'] > 0;
            $pendingAttributes['free_timbres_count'] = $freeCalc['free_timbres'];

            // Mettre en cache les modifications d'informations
            \Illuminate\Support\Facades\Cache::put('pending_modification_update_' . $demande->reference, [
                'attributes' => $pendingAttributes,
                'old_files_to_delete' => $oldFilesToDelete
            ], now()->addDays(7));

            if ($newChoixOption === 'livraison') {
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
            } else {
                $pendingDeliveryData = [
                    'choix_option' => 'Retrait sur place',
                    'montant_timbre' => $montantTimbreTotal,
                    'montant_livraison' => 0,
                ];
            }
        } else {
            // Pas de paiement requis -> on enregistre directement les modifications d'informations
            foreach ($pendingAttributes as $key => $value) {
                $demande->$key = $value;
            }
            // Supprimer physiquement les anciens fichiers remplacés maintenant
            foreach ($oldFilesToDelete as $filePath) {
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            $demande->choix_option = $newChoixOption;
            if ($newChoixOption === 'livraison') {
                $demande->montant_timbre = $montantTimbreTotal;
                $demande->montant_livraison = $montantLivraisonCible;
                $demande->nom_destinataire = $request->input('nom_destinataire');
                $demande->prenom_destinataire = $request->input('prenom_destinataire');
                $demande->email_destinataire = $request->input('email_destinataire');
                $demande->contact_destinataire = $request->input('contact_destinataire');
                $demande->adresse_livraison = $request->input('adresse_livraison');
                $demande->code_postal = $request->input('code_postal');
                $demande->ville = $request->input('ville');
                $demande->commune_livraison = $request->input('commune_livraison');
                $demande->quartier = $request->input('quartier');
                $demande->date_livraison = $request->input('date_livraison');
                $demande->heure_livraison = $request->input('heure_livraison');
            } else {
                $demande->montant_timbre = $montantTimbreTotal;
                $demande->montant_livraison = 0;
                // Vider les infos de livraison
                $demande->nom_destinataire = null;
                $demande->prenom_destinataire = null;
                $demande->email_destinataire = null;
                $demande->contact_destinataire = null;
                $demande->adresse_livraison = null;
                $demande->code_postal = null;
                $demande->ville = null;
                $demande->commune_livraison = null;
                $demande->quartier = null;
                $demande->date_livraison = null;
                $demande->heure_livraison = null;
            }

            $demande->is_free_request = $freeCalc['free_timbres'] > 0;
            $demande->free_timbres_count = $freeCalc['free_timbres'];

            // Réinitialiser l'état et désactiver la modification
            $demande->etat = 'en attente';
            $demande->peut_modifier = false;
            $demande->champs_a_modifier = null;
            $demande->motif_de_rejet = null;
            $demande->save();

            if ($freeCalc['free_timbres'] > 0) {
                $user->free_requests_used = min(2, $user->free_requests_used + $freeCalc['free_timbres']);
                $user->save();
            }
        }

        if ($needsPayment && $pendingDeliveryData) {
            $totalAmount = $resteAPayer; // C'est le reste à payer qui sera envoyé à la passerelle

            // Mettre en cache les données de livraison
            \Illuminate\Support\Facades\Cache::put('pending_delivery_update_' . $demande->reference, $pendingDeliveryData, now()->addDays(7));

            if ($totalAmount > 0) {
                $transactionReference = $demande->reference . '-MOD-' . time();
                $paymentMethod = $request->input('payment_method', 'wave');
                if (strtolower($paymentMethod) === 'mtn') {
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Le paiement par MTN est actuellement indisponible. Veuillez utiliser un autre moyen de paiement.']);
                    }
                    return redirect()->back()->with('error', 'Le paiement par MTN est actuellement indisponible. Veuillez utiliser un autre moyen de paiement.')->withInput();
                }
                $baseUrl = config('app.url');
                $successUrl = $baseUrl . '/user/payment/success?reference=' . urlencode($transactionReference) . "&type=deces";
                $errorUrl = $baseUrl . '/user/payment/cancel?reference=' . urlencode($transactionReference) . "&type=deces";

                if (strtolower($paymentMethod) === 'wave') {
                    $waveService = app(\App\Services\WaveService::class);
                    $checkoutSession = $waveService->createCheckoutSession(
                        $totalAmount,
                        'XOF',
                        $successUrl,
                        $errorUrl,
                        $transactionReference
                    );

                    if ($checkoutSession && isset($checkoutSession['wave_launch_url'])) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => true,
                                'redirect_url' => $checkoutSession['wave_launch_url']
                            ]);
                        }
                        return redirect($checkoutSession['wave_launch_url']);
                    }
                    return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur lors de la préparation du paiement Wave. Veuillez réessayer.');
                } elseif (strtolower($paymentMethod) === 'mtn') {
                    $mtnPhoneNumber = $request->input('mtn_number');
                    $mtnPhoneNumber = preg_replace('/[^0-9]/', '', $mtnPhoneNumber);
                    if (!str_starts_with($mtnPhoneNumber, '225') && strlen($mtnPhoneNumber) == 10) {
                        $mtnPhoneNumber = '225' . $mtnPhoneNumber;
                    }

                    $mtnService = new \App\Services\MtnService();
                    $response = $mtnService->requestToPay(
                        $totalAmount,
                        $mtnPhoneNumber,
                        $transactionReference,
                        'Extrait Décès',
                        'Mairie Plateau'
                    );

                    if ($response && $response['status'] === 'PENDING') {
                        session(['mtn_ref_' . $transactionReference => $response['referenceId']]);
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => true,
                                'redirect_url' => route('user.payment.mtn.waiting', [
                                    'reference' => $transactionReference,
                                    'type' => 'deces'
                                ]),
                                'reference' => $transactionReference,
                                'mtn_ref' => $response['referenceId']
                            ]);
                        }
                        return redirect()->route('user.payment.mtn.waiting', [
                            'reference' => $transactionReference,
                            'type' => 'deces'
                        ]);
                    }
                    return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur lors de la préparation du paiement MTN. Veuillez réessayer.');
                } elseif (strtolower($paymentMethod) === 'tresorpay') {
                    $tresorPhone = $request->input('mtn_number');
                    $tresorPhone = preg_replace('/[^0-9]/', '', $tresorPhone);

                    $tresorService = app(\App\Services\TresorPayService::class);
                    $response = $tresorService->initierPaiementDirect($tresorPhone, $totalAmount, $transactionReference, $user->name ?? 'Client', $user->prenoms ?? 'Plateau');

                    if ($response && ($response['success'] ?? false)) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => true,
                                'redirect_url' => route('user.payment.tresorpay.waiting', [
                                    'reference' => $transactionReference,
                                    'type' => 'deces'
                                ]),
                                'reference' => $transactionReference,
                            ]);
                        }
                        return redirect()->route('user.payment.tresorpay.waiting', [
                            'reference' => $transactionReference,
                            'type' => 'deces'
                        ]);
                    }
                    return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur TrésorPay: ' . ($response['message'] ?? 'Erreur inconnue.'));
                } else {
                    $cinetpayApiKey = env('CINETPAY_APIKEY', '521006956621e4e7a6a3d16.70681548');
                    $cinetpaySiteId = env('CINETPAY_SITE_ID', '935132');
                    try {
                        $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', [
                            'apikey' => $cinetpayApiKey,
                            'site_id' => $cinetpaySiteId,
                            'transaction_id' => $demande->reference,
                            'amount' => $totalAmount,
                            'currency' => 'XOF',
                            'description' => "Paiement pour " . $demande->reference,
                            'return_url' => $successUrl,
                            'notify_url' => $baseUrl . '/api/webhook/cinetpay',
                            'channels' => 'ALL',
                        ]);

                        if ($response->successful()) {
                            $data = $response->json();
                            if (isset($data['data']['payment_url'])) {
                                if ($request->expectsJson()) {
                                    return response()->json([
                                        'success' => true,
                                        'redirect_url' => $data['data']['payment_url']
                                    ]);
                                }
                                return redirect($data['data']['payment_url']);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Exception CinetPay: ' . $e->getMessage());
                    }
                }
            } else {
                $demande->etat = 'en attente';
                $demande->save();
                if ($freeCalc['free_timbres'] > 0) {
                    $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Demande modifiée avec succès et soumise à nouveau.',
                'demande' => $demande
            ]);
        }

        return redirect()->route('user.extrait.deces.index')->with('success', 'Votre demande a été modifiée avec succès.');
    }

    public function edit($id)
    {
        $demande = Deces::where('user_id', Auth::id())
            ->where('id', $id)
            ->where(function ($query) {
                $query->where('peut_modifier', true)
                    ->orWhereNull('agent_id');
            })
            ->firstOrFail();

        $user = Auth::user();
        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining = $this->getRemainingFreeRequests($user);

        return view('user.deces.simple.create', [
            'deces' => $demande,
            'user' => $user,
            'freeRequestsModeActive' => $freeRequestsModeActive,
            'freeRequestsRemaining' => $freeRequestsRemaining,
        ]);
    }

    public function delete(Deces $dece)
    {
        try {
            $dece->delete();
            return redirect()->route('user.extrait.deces.index')->with('success', 'La demande a été supprimée avec succès.');
        } catch (Exception $e) {
            // Log l'erreur pour le débogage
            Log::error('Erreur lors de la suppression de la demande : ' . $e->getMessage());
            return redirect()->route('user.extrait.deces.index')->with('error', 'Une erreur est survenue lors de la suppression de la demande.');
        }
    }
}
