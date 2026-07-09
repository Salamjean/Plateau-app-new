<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TresorPayService
{
    protected $key;
    protected $secret;
    protected $credentialId;
    protected $baseUrl;

    public function __construct()
    {
        $this->key = env('TRESORPAY_KEY');
        $this->secret = env('TRESORPAY_SECRET');
        $this->credentialId = env('TRESORPAY_CREDENTIAL_ID');
        $this->baseUrl = env('TRESORPAY_BASE_URL');
    }

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
            //Log::error('TrésorPay - Échec de récupération du Token: Status ' . $response->status());
            return null;
        } catch (\Exception $e) {
            Log::error('TrésorPay Exception Token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Alias pour initierPaiementDirect (utilisé par les contrôleurs)
     */
    public function initierPaiementDirect($telephone, $montant, $codePaiement, $nom = 'Client', $prenom = 'Plateau')
    {
        return $this->initierReversementDirect($telephone, $montant, $codePaiement, $nom, $prenom);
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
        
        if (empty($telephoneNormalise) || strlen($telephoneNormalise) !== 10) {
            return [
                'success' => false,
                'message' => 'Le numéro de téléphone est obligatoire et doit être valide (10 chiffres) pour TrésorPay.'
            ];
        }

        $montantNormalise = max(1, (int) round((float) $montant));
        $codePaiementNormalise = $this->normalizeCodePaiement($codePaiement);
        $callbackBaseUrl = rtrim((string) env('TRESORPAY_CALLBACK_URL', 'https://carol-peritectic-bentley.ngrok-free.dev/'), '/');

        $payload = [
            'Token' => $token,
            'Nom_usager' => substr($nom, 0, 30),
            'Prenom_usager' => substr($prenom, 0, 30),
            'Email' => 'contact@plateau.ci',
            'Telephone' => $telephoneNormalise,
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
                'telephone' => $telephoneNormalise,
                'telephone_user_input' => $telephoneNormalise,
                'montant' => $montantNormalise,
                'url_retour' => $callbackBaseUrl,
            ]);

            $response = Http::withoutVerifying()->asJson()->post("{$this->baseUrl}/InitierPaiement_V1", $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['code']) && $data['code'] == 200) {
                    $messageRetour = $data['cleretour'] ?? 'Paiement initié.';
                    
                    // TrésorPay renvoie parfois "sur le numéro  en composant" (avec un espace vide).
                    // On injecte le numéro pour que l'utilisateur sache sur quel numéro valider.
                    if (strpos($messageRetour, 'le numéro  en composant') !== false) {
                        $messageRetour = str_replace('le numéro  en composant', "le numéro {$telephoneNormalise} en composant", $messageRetour);
                    } elseif (strpos($messageRetour, 'sur le numéro en composant') !== false) {
                        $messageRetour = str_replace('sur le numéro en composant', "sur le numéro {$telephoneNormalise} en composant", $messageRetour);
                    }

                    Log::info("TrésorPay - Paiement Direct initié avec succès pour {$codePaiementNormalise}. Message: " . $messageRetour);
                    return [
                        'success' => true,
                        'message' => $messageRetour
                    ];
                }
            }

            Log::error("TrésorPay - Échec initialisation paiement pour {$codePaiementNormalise}: Status " . $response->status());
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
            throw new \Exception("Le numéro TrésorPay est invalide ou absent.");
        }

        return $telephone;
    }

    private function normalizeCodePaiement($codePaiement): string
    {
        // On conserve les lettres et les chiffres (alphanumérique) pour que le Webhook puisse identifier la demande (ex: AN5058...)
        $code = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $codePaiement);

        if ($code === '') {
            $code = (string) now()->format('YmdHis');
        }

        return substr($code, 0, 50);
    }
}
