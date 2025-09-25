<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrangeSmsService
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;
    protected $accessToken;
    protected $sslCertPath;

    public function __construct()
    {
        $this->clientId = config('services.orange.client_id');
        $this->clientSecret = config('services.orange.client_secret');
        $this->baseUrl = config('services.orange.base_url', 'https://api.orange.com');
        $this->sslCertPath = config('services.orange.ssl_cert_path', storage_path('app/certs/cacert.pem'));
        
        $this->authenticate();
    }

    protected function getHttpClient($options = [])
    {
        $defaultOptions = [
            'verify' => file_exists($this->sslCertPath) ? $this->sslCertPath : false,
            'timeout' => 30,
            'connect_timeout' => 10,
        ];

        return Http::withOptions(array_merge($defaultOptions, $options));
    }

    protected function authenticate()
    {
        try {
            Log::info('Tentative d\'authentification Orange SMS');
            Log::info('Client ID: ' . substr($this->clientId, 0, 10) . '...');
            Log::info('Base URL: ' . $this->baseUrl);

            // Construction manuelle du corps de la requête en format x-www-form-urlencoded
            $postData = http_build_query([
                'grant_type' => 'client_credentials'
            ]);

            $response = $this->getHttpClient()
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                ])
                ->withBody($postData, 'application/x-www-form-urlencoded')
                ->post($this->baseUrl . '/oauth/v3/token');

            Log::info('URL de la requête: ' . $this->baseUrl . '/oauth/v3/token');
            Log::info('Headers envoyés: Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret));

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                Log::info('Authentification Orange SMS réussie');
                Log::info('Token obtenu: ' . substr($this->accessToken, 0, 20) . '...');
                return true;
            } else {
                Log::error('Erreur d\'authentification Orange SMS - Status: ' . $response->status());
                Log::error('Body: ' . $response->body());
                Log::error('Headers de réponse: ' . json_encode($response->headers()));
                
                throw new \Exception('Échec de l\'authentification Orange SMS - Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception lors de l\'authentification Orange SMS: ' . $e->getMessage());
            
            // Tentative alternative avec une autre méthode
            if (strpos($e->getMessage(), '400') !== false || strpos($e->getMessage(), 'grant_type') !== false) {
                Log::warning('Tentative d\'authentification avec méthode alternative');
                return $this->authenticateAlternative();
            }
            
            // Tentative avec vérification SSL désactivée en dernier recours
            if (strpos($e->getMessage(), 'SSL') !== false) {
                Log::warning('Tentative d\'authentification sans vérification SSL');
                return $this->authenticateWithoutSSL();
            }
            
            throw $e;
        }
    }

    protected function authenticateAlternative()
    {
        try {
            Log::info('Tentative d\'authentification alternative');

            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->asForm() // Utilise asForm() pour encoder correctement les données
                ->post($this->baseUrl . '/oauth/v3/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                Log::info('Authentification Orange SMS réussie (méthode alternative)');
                return true;
            } else {
                Log::error('Erreur auth alternative - Status: ' . $response->status());
                Log::error('Body: ' . $response->body());
                throw new \Exception('Échec de l\'authentification alternative');
            }
        } catch (\Exception $e) {
            Log::error('Échec de l\'authentification alternative: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function authenticateWithoutSSL()
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->asForm()
                ->post($this->baseUrl . '/oauth/v3/token', [
                    'grant_type' => 'client_credentials'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                Log::info('Authentification Orange SMS réussie (sans vérification SSL)');
                return true;
            } else {
                throw new \Exception('Échec de l\'authentification sans SSL');
            }
        } catch (\Exception $e) {
            Log::error('Échec de l\'authentification sans SSL: ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendSms($to, $message)
    {
        Log::info('Tentative d\'envoi de SMS Orange à : ' . $to);
        Log::info('Message : ' . $message);

        // Nettoyer le numéro de téléphone
        $to = $this->cleanPhoneNumber($to);

        try {
            $response = $this->getHttpClient()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/smsmessaging/v1/outbound/tel%3A%2B2250000/requests', [
                    'outboundSMSMessageRequest' => [
                        'address' => 'tel:' . $to,
                        'senderAddress' => 'tel:+2250000',
                        'outboundSMSTextMessage' => [
                            'message' => $message
                        ]
                    ]
                ]);

            if ($response->successful()) {
                Log::info('SMS Orange envoyé avec succès');
                return $response->json();
            } else {
                Log::error('Erreur envoi SMS Orange - Status: ' . $response->status());
                Log::error('Body: ' . $response->body());
                
                // Tentative de ré-authentification en cas d'erreur 401
                if ($response->status() === 401) {
                    Log::info('Token expiré, tentative de ré-authentification');
                    $this->authenticate();
                    return $this->sendSms($to, $message);
                }
                
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception lors de l\'envoi du SMS Orange: ' . $e->getMessage());
            
            // Tentative sans SSL en cas d'erreur SSL
            if (strpos($e->getMessage(), 'SSL') !== false) {
                Log::warning('Tentative d\'envoi SMS sans vérification SSL');
                return $this->sendSmsWithoutSSL($to, $message);
            }
            
            return false;
        }
    }

    protected function sendSmsWithoutSSL($to, $message)
    {
        try {
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/smsmessaging/v1/outbound/tel%3A%2B2250000/requests', [
                    'outboundSMSMessageRequest' => [
                        'address' => 'tel:' . $to,
                        'senderAddress' => 'tel:+2250000',
                        'outboundSMSTextMessage' => [
                            'message' => $message
                        ]
                    ]
                ]);

            if ($response->successful()) {
                Log::info('SMS Orange envoyé avec succès (sans vérification SSL)');
                return $response->json();
            } else {
                Log::error('Erreur envoi SMS Orange sans SSL - Status: ' . $response->status());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception lors de l\'envoi du SMS Orange sans SSL: ' . $e->getMessage());
            return false;
        }
    }

    protected function cleanPhoneNumber($phoneNumber)
    {
        // Supprimer tous les caractères non numériques
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Si le numéro commence par 225 et a 13 chiffres, c'est bon
        if (strlen($cleaned) === 13 && strpos($cleaned, '225') === 0) {
            return $cleaned;
        }
        
        // Si le numéro commence par 0, le convertir en format international
        if (strlen($cleaned) === 10 && $cleaned[0] === '0') {
            return '225' . substr($cleaned, 1);
        }
        
        // Si le numéro a 10 chiffres sans 0 au début, ajouter 225
        if (strlen($cleaned) === 10 && $cleaned[0] !== '0') {
            return '225' . $cleaned;
        }
        
        // Si le numéro a 9 chiffres (sans indicatif), ajouter 225
        if (strlen($cleaned) === 9) {
            return '225' . $cleaned;
        }
        
        return $cleaned;
    }
}