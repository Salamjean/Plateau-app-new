<?php

namespace App\Http\Controllers\User\Extrait\Naissance;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use App\Models\MaintenanceSetting;
use App\Notifications\DemandeNaissanceConfirmationNotification;
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

class NaissanceController extends Controller
{
    use HandlesFreeRequests;
    public function index()
    {

        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        $naissances = Naissance::where('user_id', $user->id)->paye()->where('etat', '!=', 'terminé')->paginate(20);

        return view('user.naissance.index', compact('naissances'));
    }

    /**
     * Modifier une demande rejetée ou non encore attribuée à un agent
     */
    public function modifierDemande(Request $request, $id)
    {
        $demande = Naissance::where('user_id', Auth::id())
            ->where('id', $id)
            ->where(function ($query) {
                $query->where('peut_modifier', true)
                    ->orWhereNull('agent_id');
            })
            ->firstOrFail();

        $originalChoixOption = $demande->choix_option;

        $rules = [
            'type' => 'required',
            'name' => 'required',
            'prenom' => 'required',
            'number' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',
            'DateR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',
            'nom_prenoms_pere' => 'nullable|string|max:255',
            'nom_prenoms_mere' => 'nullable|string|max:255',
            'commune' => 'required',
            'commune_naissance' => 'required|string|max:255',
            'qty_simple' => 'nullable|integer|min:0|max:10',
            'qty_integral' => 'nullable|integer|min:0|max:10',
            'CNI' => $demande->CNI ? 'nullable' : 'required',
            'relation' => [
                \Illuminate\Validation\Rule::requiredIf(function () use ($request) {
                    return $request->pour === 'une_autre_personne' && in_array($request->type, ['integrale', 'groupee']);
                }),
                'nullable',
                'string',
                'in:enfant,parent,connaissance'
            ],
            'document_autorisation' => [
                \Illuminate\Validation\Rule::requiredIf(function () use ($request, $demande) {
                    return $request->pour === 'une_autre_personne' && $request->relation === 'connaissance' && !$demande->document_autorisation;
                }),
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:2048'
            ],
        ];

        $validated = $request->validate($rules);

        // Validation IA Gemini de la pièce d'identité CNI
        if ($request->hasFile('CNI')) {
            $geminiService = app(\App\Services\GeminiValidationService::class);
            $validation = $geminiService->validateIdentityDocument($request->file('CNI'));
            if (!$validation['isValid']) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "La pièce d'identité (CNI) a été rejetée par l'IA : " . $validation['reason']
                    ], 422);
                }
                return redirect()->back()
                    ->withErrors(['CNI' => "La pièce d'identité (CNI) a été rejetée par l'IA de la mairie : " . $validation['reason']])
                    ->withInput();
            }
        }

        // Initialiser les tableaux de modification en attente
        $pendingAttributes = [];
        $oldFilesToDelete = [];

        // Upload files
        if ($request->hasFile('CNI')) {
            $file = $request->file('CNI');
            $extension = $file->getClientOriginalExtension();
            $newFileName = (string) Str::uuid() . '.' . $extension;
            $file->storeAs("images/naissances/cni/", $newFileName, 'public');

            $pendingAttributes['CNI'] = "images/naissances/cni/$newFileName";
            if ($demande->CNI) {
                $oldFilesToDelete[] = $demande->CNI;
            }
        }

        if ($request->hasFile('document_autorisation')) {
            $file = $request->file('document_autorisation');
            $extension = $file->getClientOriginalExtension();
            $newFileName = (string) Str::uuid() . '.' . $extension;
            $file->storeAs("images/naissances/autorisations/", $newFileName, 'public');

            $pendingAttributes['document_autorisation'] = "images/naissances/autorisations/$newFileName";
            if ($demande->document_autorisation) {
                $oldFilesToDelete[] = $demande->document_autorisation;
            }
        }

        // Quantities
        $qtySimple = (int) $request->input('qty_simple', 0);
        $qtyIntegral = (int) $request->input('qty_integral', 0);

        if ($request->type === 'simple') {
            $qtyIntegral = 0;
            if ($qtySimple <= 0) $qtySimple = 1;
        } elseif ($request->type === 'integrale' || $request->type === 'extrait_integral') {
            $qtySimple = 0;
            if ($qtyIntegral <= 0) $qtyIntegral = 1;
        } else {
            if ($qtySimple <= 0) $qtySimple = 1;
            if ($qtyIntegral <= 0) $qtyIntegral = 1;
        }

        // Remplir les attributs modifiés
        $pendingAttributes['pour'] = $request->pour;
        $pendingAttributes['type'] = $request->type;
        $pendingAttributes['name'] = $request->name;
        $pendingAttributes['prenom'] = $request->prenom;
        $pendingAttributes['commune'] = $request->commune;
        $pendingAttributes['commune_naissance'] = $request->commune_naissance;
        $pendingAttributes['number'] = $request->number;
        $pendingAttributes['DateR'] = $request->DateR;
        $pendingAttributes['nom_prenoms_pere'] = $request->nom_prenoms_pere;
        $pendingAttributes['nom_prenoms_mere'] = $request->nom_prenoms_mere;
        $pendingAttributes['relation'] = $request->relation;
        $pendingAttributes['qty_simple'] = $qtySimple;
        $pendingAttributes['qty_integral'] = $qtyIntegral;
        $pendingAttributes['quantite'] = $qtySimple + $qtyIntegral;

        // Verification des paiements requis
        $user = Auth::user();
        $user->refresh();

        // Créditer temporairement les timbres gratuits déjà accordés à cette demande pour le calcul
        $anciensTimbresGratuits = (int) $demande->free_timbres_count;
        if ($anciensTimbresGratuits > 0) {
            $user->free_requests_used = max(0, $user->free_requests_used - $anciensTimbresGratuits);
            $user->save();
        }

        $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) ($qtySimple + $qtyIntegral));
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
                $baseUrl = config('app.url');
                $successUrl = $baseUrl . '/user/payment/success?reference=' . urlencode($transactionReference) . "&type=naissance";
                $errorUrl = $baseUrl . '/user/payment/cancel?reference=' . urlencode($transactionReference) . "&type=naissance";

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
                    return redirect()->route('user.extrait.index')->with('error', 'Erreur lors de la préparation du paiement Wave. Veuillez réessayer.');
                } elseif (strtolower($paymentMethod) === 'mtn') {
                    $mtnPhoneNumber = $request->input('mtn_number') ?: $demande->contact_destinataire;
                    $mtnPhoneNumber = preg_replace('/[^0-9]/', '', $mtnPhoneNumber);
                    if (!str_starts_with($mtnPhoneNumber, '225') && strlen($mtnPhoneNumber) == 10) {
                        $mtnPhoneNumber = '225' . $mtnPhoneNumber;
                    }

                    $mtnService = new \App\Services\MtnService();
                    $response = $mtnService->requestToPay(
                        $totalAmount,
                        $mtnPhoneNumber,
                        $transactionReference,
                        'Extrait Naissance',
                        'Mairie Plateau'
                    );

                    if ($response && $response['status'] === 'PENDING') {
                        session(['mtn_ref_' . $transactionReference => $response['referenceId']]);
                        if ($request->expectsJson()) {
                            return response()->json([
                                'success' => true,
                                'redirect_url' => route('user.payment.mtn.waiting', [
                                    'reference' => $transactionReference,
                                    'type' => 'naissance'
                                ])
                            ]);
                        }
                        return redirect()->route('user.payment.mtn.waiting', [
                            'reference' => $transactionReference,
                            'type' => 'naissance'
                        ]);
                    }
                    return redirect()->route('user.extrait.index')->with('error', 'Erreur lors de la préparation du paiement MTN. Veuillez réessayer.');
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

        return redirect()->route('user.extrait.index')->with('success', 'Votre demande a été modifiée avec succès.');
    }

    public function edit($id)
    {
        $demande = Naissance::where('user_id', Auth::id())
            ->where('id', $id)
            ->where(function ($query) {
                $query->where('peut_modifier', true)
                    ->orWhereNull('agent_id');
            })
            ->firstOrFail();

        $user = Auth::user();
        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining = $this->getRemainingFreeRequests($user);

        return view('user.naissance.simple.create', [
            'naissance' => $demande,
            'user' => $user,
            'userName' => $user ? $user->name : '',
            'userPrenom' => $user ? $user->prenom : '',
            'userEmail' => $user ? $user->email : '',
            'userContact' => $user ? $user->contact : '',
            'userCommune' => $user ? $user->commune : '',
            'userCMU' => $user ? $user->CMU : '',
            'freeRequestsModeActive' => $freeRequestsModeActive,
            'freeRequestsRemaining' => $freeRequestsRemaining,
        ]);
    }

    public function create()
    {
        $user = Auth::user();
        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining = $this->getRemainingFreeRequests($user);

        return view('user.naissance.simple.create', [
            'user' => $user,
            'userName' => $user ? $user->name : '',
            'userPrenom' => $user ? $user->prenom : '',
            'userEmail' => $user ? $user->email : '',
            'userContact' => $user ? $user->contact : '',
            'userCommune' => $user ? $user->commune : '',
            'userCMU' => $user ? $user->CMU : '',
            'freeRequestsModeActive' => $freeRequestsModeActive,
            'freeRequestsRemaining' => $freeRequestsRemaining,
        ]);
    }

    public function store(Request $request, YellikaSmsService $yellikaSmsService, \App\Services\WaveService $waveService)
    {

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'type' => 'required',
            'name' => 'required',
            'prenom' => 'required',
            'number' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',
            'DateR' => 'required_without_all:nom_prenoms_pere,nom_prenoms_mere',
            'nom_prenoms_pere' => 'nullable|string|max:255',
            'nom_prenoms_mere' => 'nullable|string|max:255',
            'commune' => 'required',
            'commune_naissance' => 'required|string|max:255',
            'qty_simple' => 'nullable|integer|min:0|max:10',
            'qty_integral' => 'nullable|integer|min:0|max:10',
            'CNI' => 'required',
            'relation' => [
                \Illuminate\Validation\Rule::requiredIf(function () use ($request) {
                    return $request->pour === 'une_autre_personne' && in_array($request->type, ['integrale', 'groupee']);
                }),
                'nullable',
                'string',
                'in:enfant,parent,connaissance'
            ],
            'document_autorisation' => [
                'required_if:relation,connaissance',
                'nullable',
                'file',
                'mimes:jpeg,png,jpg,pdf',
                'max:2048'
            ],
        ], [
            'type.required' => 'le type d\'extrait que vous-voulez demander est obligatoire',
            'name.required' => 'Le nom est obligatoire',
            'prenom.required' => 'Le prénom est obligatoire',
            'number.required_without_all' => 'Le numéro de registre est obligatoire si les informations parentales ne sont pas fournies',
            'DateR.required_without_all' => 'La date de registre est obligatoire si les informations parentales ne sont pas fournies',
            'commune.required' => 'La commune est obligatoire',
            'commune_naissance.required' => 'La commune de naissance est obligatoire',
            'CNI.required' => 'Le champ CNI est obligatoire',
            'qty_simple.integer' => 'La quantité simple doit être un nombre entier',
            'qty_integral.integer' => 'La quantité intégrale doit être un nombre entier',
            'CNI.mimes' => 'Le format du fichier doit être PNG, JPG, JPEG ou PDF',
            'CNI.max' => 'Le fichier ne doit pas dépasser 1Mo',
            'relation.required_if' => 'Le lien de parenté est obligatoire.',
            'relation.in' => 'Le lien de parenté sélectionné est invalide.',
            'document_autorisation.required_if' => 'Le document d\'autorisation est obligatoire pour une connaissance.',
            'document_autorisation.mimes' => 'Le format du document d\'autorisation doit être PNG, JPG, JPEG ou PDF.',
            'document_autorisation.max' => 'Le document d\'autorisation ne doit pas dépasser 2Mo.',
        ]);

        if ($validator->fails()) {
            Log::error('Validation échouée pour la demande de naissance : ', [
                'errors' => $validator->errors()->toArray(),
                'input' => $request->all(),
            ]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        // Validation IA Gemini de la pièce d'identité CNI
        if ($request->hasFile('CNI')) {
            $geminiService = app(\App\Services\GeminiValidationService::class);
            $validation = $geminiService->validateIdentityDocument($request->file('CNI'));
            if (!$validation['isValid']) {
                return redirect()->back()
                    ->withErrors(['CNI' => "La pièce d'identité (CNI) a été rejetée par l'IA de la mairie : " . $validation['reason']])
                    ->withInput();
            }
        }

        // Log des données de la requête
        Log::info('Store method called', $request->all());

        // Configuration des chemins pour le stockage des fichiers
        $filesToUpload = [
            'CNI' => 'cni/',
            'document_autorisation' => 'autorisations/',
        ];
        $uploadedPaths = [];

        foreach ($filesToUpload as $fileKey => $subDir) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $extension = $file->getClientOriginalExtension();
                $newFileName = (string) Str::uuid() . '.' . $extension;

                // Stockage avec le disque 'public' explicitement
                $file->storeAs("images/naissances/$subDir", $newFileName, 'public');

                // Même format que mariage (sans 'storage/' au début)
                $uploadedPaths[$fileKey] = "images/naissances/$subDir$newFileName";
            }
        }

        // Récupération de l'utilisateur connecté
        $user = Auth::user();

        // Génération de la référence
        $communeInitiale = strtoupper(substr($request->communeD ?: $user->commune ?: 'X', 0, 1));
        $anneeCourante = Carbon::now()->year;
        $randomDigits = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $increment = Naissance::getNextId();
        $reference = 'AN' . $randomDigits . $increment . $communeInitiale . $anneeCourante;

        // Récupération des quantités selon le type de demande
        $qtySimple = (int) $request->input('qty_simple', 0);
        $qtyIntegral = (int) $request->input('qty_integral', 0);

        if ($request->type === 'simple') {
            $qtyIntegral = 0;
            if ($qtySimple <= 0) {
                $qtySimple = 1;
            }
        } elseif ($request->type === 'integrale' || $request->type === 'extrait_integral') {
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

        // Création de la demande d'extrait de naissance
        $naissance = new Naissance();
        $naissance->pour = $request->pour;
        $naissance->type = $request->type;
        $naissance->name = $request->name;
        $naissance->qty_simple = $qtySimple;
        $naissance->qty_integral = $qtyIntegral;
        $naissance->quantite = $totalQuantity;
        $naissance->prenom = $request->prenom;
        $naissance->nom_prenoms_pere = $request->nom_prenoms_pere;
        $naissance->nom_prenoms_mere = $request->nom_prenoms_mere;
        $naissance->number = $request->number;
        $naissance->DateR = $request->DateR;
        $naissance->commune = $request->commune;
        $naissance->commune_naissance = $request->commune_naissance;
        $naissance->CNI = $uploadedPaths['CNI'] ?? null;
        $naissance->relation = $request->relation;
        $naissance->document_autorisation = $uploadedPaths['document_autorisation'] ?? null;
        $naissance->choix_option = $request->choix_option;
        $naissance->user_id = $user->id;
        $naissance->etat = 'non_paye';
        $naissance->reference = $reference;



        // === GESTION DES DEMANDES GRATUITES (Calcul préliminaire pour prix du timbre) ===
        $user->refresh();
        $freeCalc = $this->calculateFreeRequestsDiscount($user, (int) ($qtySimple + $qtyIntegral));

        // Ajout des informations de livraison si l'option "livraison" est choisie
        if ($request->input('choix_option') === 'livraison') {
            $naissance->montant_timbre = $freeCalc['montant_timbre_total'];
            $naissance->montant_livraison = $request->input('montant_livraison');
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
            $naissance->montant_timbre = $freeCalc['montant_timbre_total'];
            $naissance->montant_livraison = 0;
        }

        $naissance->save();

        $montantTimbreTotal = $freeCalc['montant_timbre_total'];
        $montantLivraison = $request->input('choix_option') === 'livraison' ? (float) $naissance->montant_livraison : 0;
        $totalAmount = $montantTimbreTotal + $montantLivraison;

        $naissance->montant_timbre = $montantTimbreTotal;
        $naissance->is_free_request = $freeCalc['free_timbres'] > 0;
        $naissance->free_timbres_count = $freeCalc['free_timbres'];
        $naissance->save();

        Log::info("Demandes gratuites - Naissance {$naissance->reference}: {$freeCalc['free_timbres']} timbres gratuits, {$freeCalc['paid_timbres']} timbres payants. Total à payer: {$totalAmount}");

        if ($totalAmount > 0) {
            // Paiement requis → le compteur sera incrémenté APRÈS confirmation du paiement
            $paymentMethod = $request->input('payment_method', 'wave');

            // Préparer les URLs de retour
            $baseUrl = config('app.url');
            $successUrl = $baseUrl . '/user/payment/success?reference=' . urlencode($naissance->reference) . "&type=naissance";
            $errorUrl = $baseUrl . '/user/payment/cancel?reference=' . urlencode($naissance->reference) . "&type=naissance";

            if (strtolower($paymentMethod) === 'wave') {
                // Créer une session de paiement Wave
                $checkoutSession = $waveService->createCheckoutSession(
                    $totalAmount,
                    'XOF',
                    $successUrl,
                    $errorUrl,
                    $naissance->reference
                );

                if ($checkoutSession && isset($checkoutSession['wave_launch_url'])) {
                    return redirect($checkoutSession['wave_launch_url']);
                }

                Log::error('Échec de la création de la session Wave pour ' . $naissance->reference);
                return redirect()->route('user.extrait.index')->with('error', 'Erreur lors de la préparation du paiement Wave. Veuillez réessayer.');
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
                    $naissance->reference,
                    'Extrait Naissance',
                    'Mairie Plateau'
                );

                if ($response && $response['status'] === 'PENDING') {
                    // Stocker le ReferenceId en session pour la vérification
                    session(['mtn_ref_' . $naissance->reference => $response['referenceId']]);

                    return redirect()->route('user.payment.mtn.waiting', [
                        'reference' => $naissance->reference,
                        'type' => 'naissance'
                    ]);
                }

                Log::error('Échec de la création de la session MTN pour ' . $naissance->reference);
                return redirect()->route('user.extrait.index')->with('error', 'Erreur lors de la préparation du paiement MTN. Veuillez réessayer.');
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
                        'transaction_id' => $naissance->reference,
                        'amount' => $totalAmount,
                        'currency' => 'XOF',
                        'description' => "Paiement pour " . $naissance->reference,
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
                    return redirect()->route('user.extrait.index')->with('error', 'Erreur de génération du lien CinetPay.');
                } catch (\Exception $e) {
                    Log::error('Erreur Exception CinetPay: ' . $e->getMessage());
                    return redirect()->route('user.extrait.index')->with('error', 'Erreur interne de paiement.');
                }
            }
        } else {
            // Pas de montant à payer (0 à payer) → incrémenter maintenant
            $naissance->etat = 'en attente';
            $naissance->save();
            if ($freeCalc['free_timbres'] > 0) {
                $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
            }
        }

        $phoneNumber = $user->indicatif . $user->contact;
        Log::info('Numéro de téléphone construit : ' . $phoneNumber);
        $message = "Bonjour {$user->name}, votre demande d'extrait de naissance a bien été transmise à la mairie du plateau. Référence : {$naissance->reference}.
Vous pouvez suivre l'état de votre demande en cliquant sur ce lien : https://plateau-apps.com";
        $smsResult = $yellikaSmsService->sendSms($phoneNumber, $message);

        // Envoi de l'email de confirmation
        Notification::send($user, new DemandeNaissanceConfirmationNotification($user, $naissance));

        return redirect()->route('user.extrait.index')->with('success', 'Votre demande a été traitée avec succès.');
    }

    public function delete(Naissance $naissance)
    {
        try {
            $naissance->delete();
            return redirect()->route('user.extrait.index')->with('success', 'La demande a été supprimée avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur lors de la suppression de la demande : ' . $e->getMessage());
            return redirect()->route('user.extrait.index')->with('error1', 'Une erreur est survenue lors de la suppression de la demande.');
        }
    }
}
