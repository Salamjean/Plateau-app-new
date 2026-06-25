<?php

namespace App\Http\Controllers\User\Extrait\Deces;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\DecesGroupe;
use App\Models\MaintenanceSetting;
use App\Services\YellikaSmsService;
use App\Services\WaveService;
use App\Traits\HandlesFreeRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Demande groupée d'actes de décès : panier de N actes (simple/intégral).
 */
class DecesGroupeController extends Controller
{
    use HandlesFreeRequests;

    public const TARIF_TIMBRE = 500;
    public const TARIF_LIVRAISON = 1500;

    public function create()
    {
        $user = Auth::user();
        $userName = $user->name ?? '';
        $userPrenom = $user->prenom ?? '';

        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining  = $this->getRemainingFreeRequests($user);

        return view('user.deces.groupee.create', compact(
            'userName',
            'userPrenom',
            'freeRequestsModeActive',
            'freeRequestsRemaining'
        ));
    }

    public function store(Request $request, YellikaSmsService $yellikaSmsService, WaveService $waveService)
    {
        $user = Auth::user();
        $qtySimple   = (int) $request->qty_simple;
        $qtyIntegral = (int) $request->qty_integral;
        $qtyTotal    = $qtySimple + $qtyIntegral;

        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining = $freeRequestsModeActive ? $this->getRemainingFreeRequests($user) : 0;
        $paidTimbres = $freeRequestsModeActive ? max(0, $qtyTotal - $freeRequestsRemaining) : $qtyTotal;
        $montantTimbreTotal = $paidTimbres * self::TARIF_TIMBRE;
        $montantLivraison = $request->input('choix_option') === 'livraison' ? self::TARIF_LIVRAISON : 0;
        $montantTotal = $montantTimbreTotal + $montantLivraison;

        $paymentRequired = $montantTotal > 0;

        $request->validate([
            'qty_simple'                  => 'required|integer|min:0|max:10',
            'qty_integral'                => 'required|integer|min:0|max:10',
            'lignes'                      => 'required|array|min:1|max:20',
            'lignes.*.type_document'      => 'required|in:simple,extrait_integral',
            // Identité du défunt
            'lignes.*.name'               => 'required|string|max:255',
            'lignes.*.numberR'            => 'nullable|string|max:50',
            'lignes.*.dateR'              => 'nullable|date',
            'lignes.*.commune_deces'      => 'required|string|max:255',
            'lignes.*.nom_prenoms_pere'   => 'nullable|string|max:255',
            'lignes.*.nom_prenoms_mere'   => 'nullable|string|max:255',
            // Justificatifs obligatoires
            'lignes.*.CNIdfnt'            => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // CNI/Acte naissance défunt
            'lignes.*.CNIdcl'             => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120', // Certificat médical
            // État matrimonial du défunt
            'lignes.*.married'            => 'required|in:oui,non',
            'lignes.*.documentMariage'    => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            // Lieu du décès
            'lignes.*.DecesHorsS'         => 'required|in:oui,non',
            'lignes.*.RequisPolice'       => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'choix_option'                => 'required|in:Retrait sur place,livraison',
            'nom_destinataire'        => 'required_if:choix_option,livraison|string|max:255',
            'prenom_destinataire'     => 'required_if:choix_option,livraison|string|max:255',
            'contact_destinataire'    => 'required_if:choix_option,livraison|string|max:20',
            'adresse_livraison'       => 'required_if:choix_option,livraison|string|max:500',
            'date_livraison'          => 'nullable|date|after_or_equal:today',
            'heure_livraison'         => 'nullable',
            // Paiement (requis si paiement requis)
            'payment_method'          => $paymentRequired ? 'required|in:wave' : 'nullable|in:wave',
            'mtn_number'              => 'nullable',
        ]);

        $geminiService = app(\App\Services\GeminiValidationService::class);

        // Validation IA Gemini de chaque justificatif d'identité dans les lignes du groupe décès
        if ($request->has('lignes') && is_array($request->lignes)) {
            foreach ($request->lignes as $index => $ligne) {
                // Pièce d'identité du défunt (CNIdfnt)
                if ($request->hasFile("lignes.{$index}.CNIdfnt")) {
                    $validation = $geminiService->validateIdentityDocument($request->file("lignes.{$index}.CNIdfnt"));
                    if (!$validation['isValid']) {
                        return $this->respondError($request, "La pièce d'identité du défunt de la ligne n°" . ($index + 1) . " a été rejetée par l'IA : " . $validation['reason']);
                    }
                }

                // Pièce d'identité du déclarant (CNIdcl)
                if ($request->hasFile("lignes.{$index}.CNIdcl")) {
                    $validation = $geminiService->validateIdentityDocument($request->file("lignes.{$index}.CNIdcl"));
                    if (!$validation['isValid']) {
                        return $this->respondError($request, "La pièce d'identité du déclarant de la ligne n°" . ($index + 1) . " a été rejetée par l'IA : " . $validation['reason']);
                    }
                }
            }
        }

        if ($qtyTotal === 0) {
            return $this->respondError($request, 'Veuillez choisir au moins un acte.');
        }

        $lignes = $request->input('lignes');
        if (count($lignes) !== $qtyTotal) {
            return $this->respondError($request, 'Le nombre de formulaires ne correspond pas aux quantités.');
        }

        // Calcul des free requests sur le total (déjà fait au début)
        $freeCalc = $this->calculateFreeRequestsDiscount($user, $qtyTotal);

        try {
            $groupeReference = DecesGroupe::generateReference($user->commune ?? null);
            DB::beginTransaction();

            $groupe = DecesGroupe::create([
                'reference'            => $groupeReference,
                'user_id'              => $user->id,
                'commune'              => $user->commune ?? 'plateau',
                'etat'                 => $montantTotal > 0 ? 'en attente de paiement' : 'en attente',
                'choix_option'         => $request->choix_option,
                'montant_timbre_total' => $montantTimbreTotal,
                'montant_livraison'    => $montantLivraison,
                'montant_total'        => $montantTotal,
                'is_free_request'      => $freeCalc['free_timbres'] > 0,
                'free_timbres_count'   => $freeCalc['free_timbres'],
                'qty_simple'           => $qtySimple,
                'qty_integral'         => $qtyIntegral,
                'nom_destinataire'     => $request->choix_option === 'livraison' ? $request->nom_destinataire : null,
                'prenom_destinataire'  => $request->choix_option === 'livraison' ? $request->prenom_destinataire : null,
                'email_destinataire'   => $request->choix_option === 'livraison' ? $request->email_destinataire : null,
                'contact_destinataire' => $request->choix_option === 'livraison' ? $request->contact_destinataire : null,
                'adresse_livraison'    => $request->choix_option === 'livraison' ? $request->adresse_livraison : null,
                'code_postal'          => $request->choix_option === 'livraison' ? $request->code_postal : null,
                'ville'                => $request->choix_option === 'livraison' ? $request->ville : null,
                'commune_livraison'    => $request->choix_option === 'livraison' ? $request->commune_livraison : null,
                'quartier'             => $request->choix_option === 'livraison' ? $request->quartier : null,
                'date_livraison'       => $request->choix_option === 'livraison' ? $request->date_livraison : null,
                'heure_livraison'      => $request->choix_option === 'livraison' ? $request->heure_livraison : null,
            ]);

            foreach ($lignes as $position => $ligneData) {
                $position1Based = $position + 1;

                $cniDfntFile = $request->file("lignes.{$position}.CNIdfnt");
                $cniDfntPath = null;
                if ($cniDfntFile && $cniDfntFile->isValid()) {
                    $newName = (string) Str::uuid() . '.' . $cniDfntFile->getClientOriginalExtension();
                    $cniDfntFile->storeAs('images/deces/cni-dfnt/', $newName, 'public');
                    $cniDfntPath = 'images/deces/cni-dfnt/' . $newName;
                }

                $cniDclFile = $request->file("lignes.{$position}.CNIdcl");
                $cniDclPath = null;
                if ($cniDclFile && $cniDclFile->isValid()) {
                    $newName = (string) Str::uuid() . '.' . $cniDclFile->getClientOriginalExtension();
                    $cniDclFile->storeAs('images/deces/cert-medical/', $newName, 'public');
                    $cniDclPath = 'images/deces/cert-medical/' . $newName;
                }

                if (!$cniDfntPath || !$cniDclPath) {
                    throw new \Exception("Justificatifs obligatoires manquants pour l'acte n°{$position1Based} (CNI/acte naissance défunt + certificat médical).");
                }

                // Upload conditionnel : acte de mariage si défunt marié
                $documentMariagePath = null;
                $isMarried = ($ligneData['married'] ?? 'non') === 'oui';
                if ($isMarried) {
                    $marFile = $request->file("lignes.{$position}.documentMariage");
                    if ($marFile && $marFile->isValid()) {
                        $newName = (string) Str::uuid() . '.' . $marFile->getClientOriginalExtension();
                        $marFile->storeAs('images/deces/mariage/', $newName, 'public');
                        $documentMariagePath = 'images/deces/mariage/' . $newName;
                    }
                }

                // Upload conditionnel : réquisition police si décès hors milieu médical
                $requisPolicePath = null;
                $isHorsMilieu = ($ligneData['DecesHorsS'] ?? 'non') === 'oui';
                if ($isHorsMilieu) {
                    $polFile = $request->file("lignes.{$position}.RequisPolice");
                    if ($polFile && $polFile->isValid()) {
                        $newName = (string) Str::uuid() . '.' . $polFile->getClientOriginalExtension();
                        $polFile->storeAs('images/deces/police/', $newName, 'public');
                        $requisPolicePath = 'images/deces/police/' . $newName;
                    }
                }

                Deces::create([
                    'groupe_id'           => $groupe->id,
                    'position_in_groupe'  => $position1Based,
                    'type_document'       => $ligneData['type_document'],
                    'type'                => $ligneData['type_document'],
                    'name'                => $ligneData['name'],
                    'numberR'             => $ligneData['numberR'] ?? '',
                    'dateR'               => $ligneData['dateR'] ?? null,
                    'commune_deces'       => $ligneData['commune_deces'] ?? null,
                    'nom_prenoms_pere'    => $ligneData['nom_prenoms_pere'] ?? null,
                    'nom_prenoms_mere'    => $ligneData['nom_prenoms_mere'] ?? null,
                    'CNIdfnt'             => $cniDfntPath,
                    'CNIdcl'              => $cniDclPath,
                    'documentMariage'     => $documentMariagePath,
                    'RequisPolice'        => $requisPolicePath,
                    'commune'             => $user->commune ?? 'plateau',
                    'reference'           => $groupeReference . '-' . $position1Based,
                    'qty_simple'          => $ligneData['type_document'] === 'simple' ? 1 : 0,
                    'qty_integral'        => $ligneData['type_document'] === 'extrait_integral' ? 1 : 0,
                    'quantite'            => 1,
                    'choix_option'        => $request->choix_option,
                    'montant_timbre'      => self::TARIF_TIMBRE,
                    'montant_livraison'   => 0,
                    'user_id'             => $user->id,
                    'etat'                => $groupe->etat,
                ]);
            }

            DB::commit();
            Log::info("Demande groupée décès créée [{$groupe->reference}] : {$qtySimple} simples + {$qtyIntegral} intégraux, total {$montantTotal} FCFA");

            if ($montantTotal > 0) {
                $paymentUrl = $this->generatePaymentUrl($groupe, $request->input('payment_method', 'wave'), $waveService, $request);
                if (!$paymentUrl) {
                    return $this->respondError($request, 'Erreur lors de la préparation du paiement.');
                }
                return $this->respondSuccess($request, [
                    'message'      => 'Demande créée. Redirection vers le paiement.',
                    'payment_url'  => $paymentUrl,
                    'redirect_url' => route('user.extrait.deces.index'),
                    'reference'    => $groupe->reference,
                ]);
            }

            if ($freeCalc['free_timbres'] > 0) {
                $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
            }

            try {
                $phoneNumber = $user->indicatif . $user->contact;
                $message = "Bonjour {$user->name}, votre demande groupée d'actes de décès ({$qtyTotal} actes) a bien été transmise. Référence : {$groupe->reference}.";
                $yellikaSmsService->sendSms($phoneNumber, $message);
            } catch (\Throwable $e) {
                Log::warning("SMS échoué pour groupe deces {$groupe->reference} : " . $e->getMessage());
            }

            return $this->respondSuccess($request, [
                'message'      => "Votre demande groupée ({$qtyTotal} actes) a été transmise. Référence : {$groupe->reference}",
                'payment_url'  => null,
                'redirect_url' => route('user.extrait.deces.index'),
                'reference'    => $groupe->reference,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur création demande groupée décès : ' . $e->getMessage());
            return $this->respondError($request, "Une erreur est survenue : " . $e->getMessage());
        }
    }

    private function respondSuccess(Request $request, array $data)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(array_merge(['success' => true], $data), 200);
        }
        if (!empty($data['payment_url'])) {
            return redirect($data['payment_url']);
        }
        return redirect($data['redirect_url'])->with('success', $data['message'] ?? 'Demande créée');
    }

    private function respondError(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }
        return back()->withInput()->with('error', $message);
    }

    private function generatePaymentUrl(DecesGroupe $groupe, string $paymentMethod, WaveService $waveService, Request $request): ?string
    {
        $baseUrl    = config('app.url');
        $successUrl = $baseUrl . '/user/payment/success?reference=' . urlencode($groupe->reference) . '&type=deces_groupe';
        $errorUrl   = $baseUrl . '/user/payment/cancel?reference=' . urlencode($groupe->reference) . '&type=deces_groupe';

        $method = strtolower($paymentMethod);

        if ($method === 'wave') {
            $session = $waveService->createCheckoutSession(
                $groupe->montant_total,
                'XOF',
                $successUrl,
                $errorUrl,
                $groupe->reference
            );
            return $session['wave_launch_url'] ?? null;
        }

        if ($method === 'mtn') {
            Log::error("Tentative de paiement MTN bloquée pour le groupe {$groupe->reference}");
            return null;
        }

        if (in_array($method, ['orange', 'moov'])) {
            try {
                $cinetpayApiKey = env('CINETPAY_APIKEY', env('CINETPAY_API_KEY'));
                $cinetpaySiteId = env('CINETPAY_SITE_ID');
                $response = \Illuminate\Support\Facades\Http::withoutVerifying()->post('https://api-checkout.cinetpay.com/v2/payment', [
                    'apikey'         => $cinetpayApiKey,
                    'site_id'        => $cinetpaySiteId,
                    'transaction_id' => $groupe->reference,
                    'amount'         => $groupe->montant_total,
                    'currency'       => 'XOF',
                    'description'    => "Paiement groupé décès " . $groupe->reference,
                    'return_url'     => $successUrl,
                    'notify_url'     => $baseUrl . '/api/webhook/cinetpay',
                    'channels'       => 'MOBILE_MONEY',
                ]);
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data']['payment_url'] ?? null;
                }
            } catch (\Throwable $e) {
                Log::error("CinetPay groupe deces : " . $e->getMessage());
            }
        }

        return null;
    }
}
