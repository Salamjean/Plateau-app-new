<?php

namespace App\Http\Controllers\User\Extrait\Mariage;

use App\Http\Controllers\Controller;
use App\Models\Mariage;
use App\Models\MariageGroupe;
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
 * Demande groupée d'actes de mariage : panier de N actes (simple/intégral).
 * Chaque ligne = un mariage différent.
 */
class MariageGroupeController extends Controller
{
    use HandlesFreeRequests;

    public const TARIF_TIMBRE = 500;
    public const TARIF_LIVRAISON = 1500;

    public function index()
    {
        $user   = Auth::user();
        $groupes = MariageGroupe::where('user_id', $user->id)->latest()->get();
        return view('user.mariage.groupee.index', compact('groupes'));
    }

    public function create()
    {
        $user = Auth::user();
        $userName = $user->name ?? '';
        $userPrenom = $user->prenom ?? '';

        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining  = $this->getRemainingFreeRequests($user);

        return view('user.mariage.groupee.create', compact(
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
            'qty_simple'                     => 'required|integer|min:0|max:10',
            'qty_integral'                   => 'required|integer|min:0|max:10',
            'lignes'                         => 'required|array|min:1|max:20',
            'lignes.*.type_document'         => 'required|in:simple,extrait_integral',
            // Détails du mariage (commun simple + intégral)
            'lignes.*.commune_mariage'       => 'required|string|max:255',
            'lignes.*.CMU'                   => 'nullable|string|max:50',
            // Conjoint (présent uniquement dans intégral, optionnel)
            'lignes.*.nomEpoux'              => 'nullable|string|max:255',
            'lignes.*.prenomEpoux'           => 'nullable|string|max:255',
            'lignes.*.dateNaissanceEpoux'    => 'nullable|date',
            'lignes.*.lieuNaissanceEpoux'    => 'nullable|string|max:255',
            // Justificatifs
            'lignes.*.pieceIdentite'         => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'lignes.*.extraitMariage'        => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'choix_option'                   => 'required|in:Retrait sur place,livraison',
            'nom_destinataire'               => 'required_if:choix_option,livraison|string|max:255',
            'prenom_destinataire'            => 'required_if:choix_option,livraison|string|max:255',
            'contact_destinataire'           => 'required_if:choix_option,livraison|string|max:20',
            'adresse_livraison'              => 'required_if:choix_option,livraison|string|max:500',
            'date_livraison'                 => 'nullable|date|after_or_equal:today',
            'heure_livraison'                => 'nullable',
            // Paiement (requis si paiement requis)
            'payment_method'                 => $paymentRequired ? 'required|in:wave,mtn,orange,moov' : 'nullable|in:wave,mtn,orange,moov',
            'mtn_number'                     => ($paymentRequired && $request->input('payment_method') === 'mtn') ? 'required|string|max:20' : 'nullable|string|max:20',
        ]);

        $geminiService = app(\App\Services\GeminiValidationService::class);

        // Validation IA Gemini de chaque pieceIdentite dans les lignes
        if ($request->has('lignes') && is_array($request->lignes)) {
            foreach ($request->lignes as $index => $ligne) {
                if ($request->hasFile("lignes.{$index}.pieceIdentite")) {
                    $validation = $geminiService->validateIdentityDocument($request->file("lignes.{$index}.pieceIdentite"));
                    if (!$validation['isValid']) {
                        return $this->respondError($request, "La pièce d'identité de l'acte n°" . ($index + 1) . " a été rejetée par l'IA : " . $validation['reason']);
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
            $groupeReference = MariageGroupe::generateReference($user->commune ?? null);

            DB::beginTransaction();

            $groupe = MariageGroupe::create([
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

                // Upload pieceIdentite
                $pieceFile = $request->file("lignes.{$position}.pieceIdentite");
                $piecePath = null;
                if ($pieceFile && $pieceFile->isValid()) {
                    $newName = (string) Str::uuid() . '.' . $pieceFile->getClientOriginalExtension();
                    $pieceFile->storeAs('images/mariages/pieces/', $newName, 'public');
                    $piecePath = 'images/mariages/pieces/' . $newName;
                }
                if (!$piecePath) {
                    throw new \Exception("Pièce d'identité manquante pour l'acte n°{$position1Based}.");
                }

                // Upload extraitMariage (optionnel)
                $extraitFile = $request->file("lignes.{$position}.extraitMariage");
                $extraitPath = null;
                if ($extraitFile && $extraitFile->isValid()) {
                    $newName = (string) Str::uuid() . '.' . $extraitFile->getClientOriginalExtension();
                    $extraitFile->storeAs('images/mariages/extraits/', $newName, 'public');
                    $extraitPath = 'images/mariages/extraits/' . $newName;
                }

                Mariage::create([
                    'groupe_id'           => $groupe->id,
                    'position_in_groupe'  => $position1Based,
                    'type_document'       => $ligneData['type_document'],
                    'nomEpoux'            => $ligneData['nomEpoux'] ?? null,
                    'prenomEpoux'         => $ligneData['prenomEpoux'] ?? null,
                    'dateNaissanceEpoux'  => $ligneData['dateNaissanceEpoux'] ?? null,
                    'lieuNaissanceEpoux'  => $ligneData['lieuNaissanceEpoux'] ?? null,
                    'commune_mariage'     => $ligneData['commune_mariage'] ?? null,
                    'CMU'                 => $ligneData['CMU'] ?? null,
                    'pieceIdentite'       => $piecePath,
                    'extraitMariage'      => $extraitPath ?? '',
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
            Log::info("Demande groupée mariage créée [{$groupe->reference}] : {$qtySimple} simples + {$qtyIntegral} intégraux, total {$montantTotal} FCFA");

            // Paiement requis ?
            if ($montantTotal > 0) {
                $paymentUrl = $this->generatePaymentUrl($groupe, $request->input('payment_method', 'wave'), $waveService, $request);
                if (!$paymentUrl) {
                    return $this->respondError($request, 'Erreur lors de la préparation du paiement. Veuillez réessayer.');
                }
                return $this->respondSuccess($request, [
                    'message'      => 'Demande créée. Redirection vers le paiement.',
                    'payment_url'  => $paymentUrl,
                    'redirect_url' => route('user.extrait.mariage.index'),
                    'reference'    => $groupe->reference,
                ]);
            }

            // Pas de paiement
            if ($freeCalc['free_timbres'] > 0) {
                $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
            }

            try {
                $phoneNumber = $user->indicatif . $user->contact;
                $message = "Bonjour {$user->name}, votre demande groupée d'actes de mariage ({$qtyTotal} actes) a bien été transmise. Référence : {$groupe->reference}.";
                $yellikaSmsService->sendSms($phoneNumber, $message);
            } catch (\Throwable $e) {
                Log::warning("SMS échoué pour groupe mariage {$groupe->reference} : " . $e->getMessage());
            }

            return $this->respondSuccess($request, [
                'message'      => "Votre demande groupée ({$qtyTotal} actes) a été transmise. Référence : {$groupe->reference}",
                'payment_url'  => null,
                'redirect_url' => route('user.extrait.mariage.index'),
                'reference'    => $groupe->reference,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur création demande groupée mariage : ' . $e->getMessage());
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

    private function generatePaymentUrl(MariageGroupe $groupe, string $paymentMethod, WaveService $waveService, Request $request): ?string
    {
        $baseUrl    = config('app.url');
        $successUrl = $baseUrl . '/user/payment/success?reference=' . urlencode($groupe->reference) . '&type=mariage_groupe';
        $errorUrl   = $baseUrl . '/user/payment/cancel?reference=' . urlencode($groupe->reference) . '&type=mariage_groupe';

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
            $mtnPhone = preg_replace('/[^0-9]/', '', (string) $request->input('mtn_number'));
            if (!$mtnPhone) return null;
            if (!str_starts_with($mtnPhone, '225') && strlen($mtnPhone) === 10) {
                $mtnPhone = '225' . $mtnPhone;
            }
            try {
                $mtnService = new \App\Services\MtnService();
                $response = $mtnService->requestToPay(
                    $groupe->montant_total,
                    $mtnPhone,
                    $groupe->reference,
                    'Extrait Mariage Groupé',
                    'Mairie Plateau'
                );
                if ($response && $response['status'] === 'PENDING') {
                    session(['mtn_ref_' . $groupe->reference => $response['referenceId']]);
                    return route('user.payment.mtn.waiting', [
                        'reference' => $groupe->reference,
                        'type' => 'mariage_groupe',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error("MTN groupe mariage : " . $e->getMessage());
            }
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
                    'description'    => "Paiement groupé mariage " . $groupe->reference,
                    'return_url'     => $successUrl,
                    'notify_url'     => $baseUrl . '/api/webhook/cinetpay',
                    'channels'       => 'MOBILE_MONEY',
                ]);
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['data']['payment_url'] ?? null;
                }
            } catch (\Throwable $e) {
                Log::error("CinetPay groupe mariage : " . $e->getMessage());
            }
        }

        return null;
    }
}
