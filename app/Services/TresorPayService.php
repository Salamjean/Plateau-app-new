<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TresorPayService
{
    protected $key = 'VAU34I';
    protected $secret = '8A03R3';
    protected $credentialId = 'gtvB04rzE_wkvb4S2';
    protected $baseUrl = 'https://test.tresormoney.ci'; // Version sandbox/test

    /**
     * Récupère le Token d'accès (30 minutes de validité)
     */
    public function getToken()
    {
        try {
            $response = Http::withoutVerifying()->asJson()->post("{$this->baseUrl}/Authentification_V1", [
                'Key' => $this->key,
                'Secret' => $this->secret,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] == 200) {
                    return $data['Token'];
                }
            }
            Log::error('TrésorPay - Échec de récupération du Token: Status ' . $response->status() . ' - Body: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('TrésorPay Exception Token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Initie le paiement direct automatique (débit) en arrière-plan
     */
    public function initierReversementDirect($telephone, $montant, $codePaiement, $nom = 'Client', $prenom = 'Plateau')
    {
        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Impossible d\'obtenir le token d\'authentification TrésorPay.'
            ];
        }

        // Normalisation des champs sensibles validés côté TrésorPay
        $telephoneNormalise = $this->normalizeTelephone($telephone);
        $telephoneTresor = '0767664010';
        $montantNormalise = max(1, (int) round((float) $montant));
        $codePaiementNormalise = $this->normalizeCodePaiement($codePaiement);
        $callbackBaseUrl = rtrim((string) env('TRESORPAY_CALLBACK_URL', 'https://carol-peritectic-bentley.ngrok-free.dev/'), '/');

        $payload = [
            'Token' => $token,
            'Nom_usager' => substr($nom, 0, 30),
            'Prenom_usager' => substr($prenom, 0, 30),
            'Email' => 'contact@plateau.ci',
            'Telephone' => $telephoneTresor,
            'Additif' => 'Transfert auto timbre',
            'code_paiement' => $codePaiementNormalise,
            'Url_Retour' => $callbackBaseUrl,
            'Url_Callback' => $callbackBaseUrl,
            'TypeOperation' => '1', // 1 = Paiement Direct (débit)
            'TCredentiel' => [
                [
                    'Credentiel' => $this->credentialId,
                    'Produits' => [
                        [
                            'LibelleProduit' => 'RECETTE NON LISTEE',
                            'Montant' => (string) $montantNormalise,
                            'IdProduit' => '0',
                            'nEstUnServicePrive' => '0',
                            'TypeProduit' => '1',
                            'Reference_code_Produit' => '',
                            'Quantite' => '1'
                        ]
                    ]
                ]
            ]
        ];

        try {
            Log::info('TrésorPay - Init payload (sanitized)', [
                'code_paiement' => $codePaiementNormalise,
                'telephone' => $telephoneTresor,
                'telephone_user_input' => $telephoneNormalise,
                'montant' => $montantNormalise,
                'url_retour' => $callbackBaseUrl,
            ]);

            $response = Http::withoutVerifying()->asJson()->post("{$this->baseUrl}/InitierPaiement_V1", $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] == 200) {
                    Log::info("TrésorPay - Paiement Direct initié avec succès pour {$codePaiementNormalise}. Message: " . ($data['cleretour'] ?? ''));
                    return [
                        'success' => true,
                        'message' => $data['cleretour'] ?? 'Paiement initié.'
                    ];
                }
            }

            Log::error("TrésorPay - Échec initialisation paiement pour {$codePaiementNormalise}: Status " . $response->status() . ' - Body: ' . $response->body());
            return [
                'success' => false,
                'message' => 'L\'initialisation TrésorPay a retourné une erreur.'
            ];
        } catch (\Exception $e) {
            Log::error("TrésorPay Exception Paiement Direct pour {$codePaiementNormalise}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function normalizeTelephone($telephone): string
    {
        $telephone = preg_replace('/[^0-9]/', '', (string) $telephone);

        // Convertit 225XXXXXXXXXX vers 0XXXXXXXXX
        if (str_starts_with($telephone, '225') && strlen($telephone) === 13) {
            $telephone = substr($telephone, 3);
        }

        // Certains numéros sont saisis au format 8 chiffres
        if (strlen($telephone) === 8) {
            $telephone = '07' . $telephone;
        }

        if (strlen($telephone) !== 10) {
            return '0767664010';
        }

        return $telephone;
    }

    private function normalizeCodePaiement($codePaiement): string
    {
        // TrésorPay accepte un identifiant partenaire en chaîne, mais le format numérique est le plus robuste en pratique.
        $digitsOnly = preg_replace('/\D/', '', (string) $codePaiement);

        if ($digitsOnly === '') {
            $digitsOnly = (string) now()->format('ymdHis');
        }

        return substr($digitsOnly, 0, 20);
    }
}
