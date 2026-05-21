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

        // Nettoyage et formatage du numéro de téléphone
        $telephone = preg_replace('/[^0-9]/', '', $telephone);
        if (strlen($telephone) == 8) {
            $telephone = '07' . $telephone; // Formatage rapide si besoin
        }

        $payload = [
            'Token' => $token,
            'Nom_usager' => substr($nom, 0, 30),
            'Prenom_usager' => substr($prenom, 0, 30),
            'Email' => 'contact@plateau.ci',
            'Telephone' => '0767664010',
            'Additif' => 'Transfert auto timbre',
            'code_paiement' => $codePaiement,
            'Url_Retour' => 'https://localhost',
            'Url_Callback' => 'https://localhost',
            'TypeOperation' => '1', // 1 = Paiement Direct (débit)
            'TCredentiel' => [
                [
                    'Credentiel' => $this->credentialId,
                    'Produits' => [
                        [
                            'LibelleProduit' => 'TIMBRE ETAT CIVIL',
                            'Montant' => (string) $montant,
                            'IdProduit' => '0',
                            'nEstUnServicePrive' => '0',
                            'TypeProduit' => '0',
                            'Reference_code_Produit' => '',
                            'Reference_Code_Produit' => '',
                            'Quantite' => '1'
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = Http::withoutVerifying()->asJson()->post("{$this->baseUrl}/InitierPaiement_V1", $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] == 200) {
                    Log::info("TrésorPay - Paiement Direct initié avec succès pour {$codePaiement}. Message: " . ($data['cleretour'] ?? ''));
                    return [
                        'success' => true,
                        'message' => $data['cleretour'] ?? 'Paiement initié.'
                    ];
                }
            }

            Log::error("TrésorPay - Échec initialisation paiement pour {$codePaiement}: Status " . $response->status() . ' - Body: ' . $response->body());
            return [
                'success' => false,
                'message' => 'L\'initialisation TrésorPay a retourné une erreur.'
            ];
        } catch (\Exception $e) {
            Log::error("TrésorPay Exception Paiement Direct pour {$codePaiement}: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
