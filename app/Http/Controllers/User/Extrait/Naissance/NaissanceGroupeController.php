<?php

namespace App\Http\Controllers\User\Extrait\Naissance;

use App\Http\Controllers\Controller;
use App\Models\Naissance;
use App\Models\NaissanceGroupe;
use App\Models\MaintenanceSetting;
use App\Notifications\DemandeNaissanceConfirmationNotification;
use App\Services\YellikaSmsService;
use App\Services\WaveService;
use App\Traits\HandlesFreeRequests;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * Gère les "demandes groupées" d'actes de naissance — un panier contenant
 * plusieurs actes (simples et/ou intégraux), chacun pour une personne
 * potentiellement différente.
 *
 * - 1 NaissanceGroupe = la "tête" du panier (livraison, paiement, état global)
 * - N Naissance = les sous-actes individuels, liés via `groupe_id`
 */
class NaissanceGroupeController extends Controller
{
    use HandlesFreeRequests;

    /**
     * Constante : tarif unitaire d'un timbre (simple OU intégral).
     */
    public const TARIF_TIMBRE = 500;

    /**
     * Constante : tarif livraison (à externaliser plus tard si variable par zone).
     */
    public const TARIF_LIVRAISON = 1500;

    /**
     * Affiche le formulaire de création d'une demande groupée.
     */
    public function create()
    {
        $user = Auth::user();
        $userName = $user->name ?? '';
        $userPrenom = $user->prenom ?? '';

        // Infos free requests pour affichage
        $freeRequestsModeActive = MaintenanceSetting::isFreeRequestsModeActive();
        $freeRequestsRemaining  = $this->getRemainingFreeRequests($user);

        return view('user.naissance.groupee.create', compact(
            'userName',
            'userPrenom',
            'freeRequestsModeActive',
            'freeRequestsRemaining'
        ));
    }

    /**
     * Reçoit les N sous-formulaires + infos livraison et crée :
     *   - 1 ligne dans `naissance_groupes`
     *   - N lignes dans `naissances` (liées par groupe_id)
     */
    public function store(Request $request, YellikaSmsService $yellikaSmsService, WaveService $waveService)
    {
        $request->validate([
            'qty_simple'                  => 'required|integer|min:0|max:10',
            'qty_integral'                => 'required|integer|min:0|max:10',
            // Chaque ligne = un acte pour une personne potentiellement différente
            'lignes'                      => 'required|array|min:1|max:20',
            'lignes.*.type_document'      => 'required|in:simple,extrait_integral',
            'lignes.*.name'               => 'required|string|max:255',
            'lignes.*.prenom'             => 'required|string|max:255',
            'lignes.*.commune_naissance'  => 'required|string|max:255',
            'lignes.*.number'             => 'nullable|string|max:50',
            'lignes.*.DateR'              => 'nullable|date',
            'lignes.*.nom_prenoms_pere'   => 'nullable|string|max:255',
            'lignes.*.nom_prenoms_mere'   => 'nullable|string|max:255',
            'lignes.*.CNI'                => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'choix_option'                => 'required|in:Retrait sur place,livraison',
            // Livraison (requis si livraison)
            'nom_destinataire'        => 'required_if:choix_option,livraison|string|max:255',
            'prenom_destinataire'     => 'required_if:choix_option,livraison|string|max:255',
            'contact_destinataire'    => 'required_if:choix_option,livraison|string|max:20',
            'adresse_livraison'       => 'required_if:choix_option,livraison|string|max:500',
            'date_livraison'          => 'nullable|date|after_or_equal:today',
            'heure_livraison'         => 'nullable',
            // Paiement (requis si livraison)
            'payment_method'          => 'required_if:choix_option,livraison|in:wave,mtn,orange,moov',
            'mtn_number'              => 'required_if:payment_method,mtn|nullable|string|max:20',
        ]);

        $user = Auth::user();

        $qtySimple   = (int) $request->qty_simple;
        $qtyIntegral = (int) $request->qty_integral;
        $qtyTotal    = $qtySimple + $qtyIntegral;

        if ($qtyTotal === 0) {
            return $this->respondError($request, 'Veuillez choisir au moins un acte (simple ou intégral).');
        }

        // Calcul des free requests sur le total
        $freeCalc = $this->calculateFreeRequestsDiscount($user, $qtyTotal);

        $montantTimbreTotal = $freeCalc['montant_timbre_total'];
        $montantLivraison   = $request->choix_option === 'livraison' ? self::TARIF_LIVRAISON : 0;
        $montantTotal       = $montantTimbreTotal + $montantLivraison;

        try {
            $groupeReference = NaissanceGroupe::generateReference($user->commune ?? null);

            DB::beginTransaction();

            // 1. Créer le groupe (la "tête" du panier)
            $groupe = NaissanceGroupe::create([
                'reference'            => $groupeReference,
                'user_id'              => $user->id,
                'commune'              => $user->commune ?? 'plateau',
                'etat'                 => $request->choix_option === 'livraison' && $montantTotal > 0
                                            ? 'en attente de paiement'
                                            : 'en attente',
                'choix_option'         => $request->choix_option,
                'montant_timbre_total' => $montantTimbreTotal,
                'montant_livraison'    => $montantLivraison,
                'montant_total'        => $montantTotal,
                'is_free_request'      => $freeCalc['free_timbres'] > 0,
                'free_timbres_count'   => $freeCalc['free_timbres'],
                'qty_simple'           => $qtySimple,
                'qty_integral'         => $qtyIntegral,
                // Champs livraison (uniquement si livraison)
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

            // 2. Vérifier que le nombre de lignes correspond aux quantités annoncées
            $lignes = $request->input('lignes', []);
            if (count($lignes) !== $qtyTotal) {
                throw new \Exception("Le nombre de formulaires remplis ({$qtyTotal} attendu) ne correspond pas.");
            }

            // 3. Créer les N sous-actes — chaque ligne avec ses propres infos + son propre CNI
            foreach ($lignes as $position => $ligneData) {
                $position1Based = $position + 1;

                // Upload du CNI individuel (par ligne — accès via $request->file)
                $cniFile = $request->file("lignes.{$position}.CNI");
                if (!$cniFile || !$cniFile->isValid()) {
                    throw new \Exception("La pièce d'identité (CNI) est manquante pour l'acte n°{$position1Based}.");
                }
                $newFileName = (string) Str::uuid() . '.' . $cniFile->getClientOriginalExtension();
                $cniFile->storeAs('images/naissances/cni/', $newFileName, 'public');
                $cniPath = 'images/naissances/cni/' . $newFileName;

                Naissance::create([
                    'groupe_id'          => $groupe->id,
                    'position_in_groupe' => $position1Based,
                    'type_document'      => $ligneData['type_document'],
                    'type'               => $ligneData['type_document'], // compat
                    'pour'               => 'une_autre_personne',
                    'name'               => $ligneData['name'],
                    'prenom'             => $ligneData['prenom'],
                    'commune'            => $user->commune ?? 'plateau',
                    'commune_naissance'  => $ligneData['commune_naissance'] ?? null,
                    'number'             => $ligneData['number'] ?? null,
                    'DateR'              => $ligneData['DateR'] ?? null,
                    'nom_prenoms_pere'   => $ligneData['nom_prenoms_pere'] ?? null,
                    'nom_prenoms_mere'   => $ligneData['nom_prenoms_mere'] ?? null,
                    'CNI'                => $cniPath,
                    'reference'          => $groupeReference . '-' . $position1Based,
                    'quantite'           => 1,
                    'choix_option'       => $request->choix_option,
                    'montant_timbre'     => self::TARIF_TIMBRE,
                    'montant_livraison'  => 0,
                    'user_id'            => $user->id,
                    'etat'               => $groupe->etat,
                ]);
            }

            DB::commit();

            Log::info("Demande groupée créée [{$groupe->reference}] : {$qtySimple} simples + {$qtyIntegral} intégraux, total {$montantTotal} FCFA");

            // 3. Si paiement requis (livraison + montant > 0), générer l'URL de paiement
            if ($request->choix_option === 'livraison' && $montantTotal > 0) {
                $paymentUrl = $this->generatePaymentUrl($groupe, $request->input('payment_method', 'wave'), $waveService, $request);

                if (!$paymentUrl) {
                    return $this->respondError($request, 'Erreur lors de la préparation du paiement. Veuillez réessayer.');
                }

                return $this->respondSuccess($request, [
                    'message'      => 'Demande créée. Redirection vers le paiement.',
                    'payment_url'  => $paymentUrl,
                    'redirect_url' => route('user.extrait.index'),
                    'reference'    => $groupe->reference,
                ]);
            }

            // 4. Sinon (retrait OU livraison gratuite) : on peut incrémenter le compteur tout de suite
            if ($freeCalc['free_timbres'] > 0) {
                $this->incrementFreeRequestsUsed($user, $freeCalc['free_timbres']);
            }

            // Notification SMS + email
            $phoneNumber = $user->indicatif . $user->contact;
            $message = "Bonjour {$user->name}, votre demande groupée d'actes de naissance ({$qtyTotal} actes) a bien été transmise à la mairie du Plateau. Référence : {$groupe->reference}.";
            try {
                $yellikaSmsService->sendSms($phoneNumber, $message);
            } catch (\Throwable $e) {
                Log::warning("SMS échoué pour groupe {$groupe->reference} : " . $e->getMessage());
            }

            return $this->respondSuccess($request, [
                'message'      => "Votre demande groupée ({$qtyTotal} actes) a été transmise avec succès. Référence : {$groupe->reference}",
                'payment_url'  => null,
                'redirect_url' => route('user.extrait.index'),
                'reference'    => $groupe->reference,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur création demande groupée : ' . $e->getMessage());
            return $this->respondError($request, "Une erreur est survenue : " . $e->getMessage());
        }
    }

    /**
     * Réponse de succès — JSON pour AJAX, redirect classique sinon.
     */
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

    /**
     * Réponse d'erreur — JSON pour AJAX, redirect avec error sinon.
     */
    private function respondError(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }
        return back()->withInput()->with('error', $message);
    }

    /**
     * Génère l'URL de paiement selon le mode choisi.
     * Retourne l'URL de paiement ou null en cas d'échec.
     */
    private function generatePaymentUrl(NaissanceGroupe $groupe, string $paymentMethod, WaveService $waveService, Request $request): ?string
    {
        $baseUrl    = config('app.url');
        $successUrl = $baseUrl . '/user/payment/success?reference=' . urlencode($groupe->reference) . '&type=naissance_groupe';
        $errorUrl   = $baseUrl . '/user/payment/cancel?reference=' . urlencode($groupe->reference) . '&type=naissance_groupe';

        $method = strtolower($paymentMethod);

        // ─── WAVE ───
        if ($method === 'wave') {
            $session = $waveService->createCheckoutSession(
                $groupe->montant_total,
                'XOF',
                $successUrl,
                $errorUrl,
                $groupe->reference
            );

            if ($session && isset($session['wave_launch_url'])) {
                return $session['wave_launch_url'];
            }

            Log::error("Échec création session Wave pour groupe {$groupe->reference}");
            return null;
        }

        // ─── MTN ───
        if ($method === 'mtn') {
            $mtnPhone = preg_replace('/[^0-9]/', '', (string) $request->input('mtn_number'));
            if (!$mtnPhone) {
                Log::error("MTN : numéro manquant pour groupe {$groupe->reference}");
                return null;
            }
            if (!str_starts_with($mtnPhone, '225') && strlen($mtnPhone) === 10) {
                $mtnPhone = '225' . $mtnPhone;
            }

            try {
                $mtnService = new \App\Services\MtnService();
                $response = $mtnService->requestToPay(
                    $groupe->montant_total,
                    $mtnPhone,
                    $groupe->reference,
                    'Extrait Naissance Groupé',
                    'Mairie Plateau'
                );

                if ($response && $response['status'] === 'PENDING') {
                    session(['mtn_ref_' . $groupe->reference => $response['referenceId']]);
                    // Pour MTN, on redirige vers une page d'attente
                    return route('user.payment.mtn.waiting', [
                        'reference' => $groupe->reference,
                        'type'      => 'naissance_groupe',
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error("Erreur MTN groupe {$groupe->reference} : " . $e->getMessage());
            }
            return null;
        }

        // ─── CINETPAY (Orange / Moov) ───
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
                    'description'    => "Paiement demande groupée " . $groupe->reference,
                    'return_url'     => $successUrl,
                    'notify_url'     => $baseUrl . '/api/webhook/cinetpay',
                    'channels'       => 'MOBILE_MONEY',
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['data']['payment_url'])) {
                        return $data['data']['payment_url'];
                    }
                }
                Log::error('Échec CinetPay pour groupe ' . $groupe->reference . ' : ' . $response->body());
            } catch (\Throwable $e) {
                Log::error("Erreur CinetPay groupe {$groupe->reference} : " . $e->getMessage());
            }
            return null;
        }

        Log::warning("Mode de paiement inconnu : {$paymentMethod}");
        return null;
    }
}
