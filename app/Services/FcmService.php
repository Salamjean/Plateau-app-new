<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected $projectId;
    protected $credentialsPath;

    public function __construct()
    {
        $this->credentialsPath = base_path(
            env('FIREBASE_CREDENTIALS', 'storage/plateau-apps-user-firebase-adminsdk-fbsvc-7ec6f5846b.json')
        );

        if (file_exists($this->credentialsPath)) {
            $credentials = json_decode(file_get_contents($this->credentialsPath), true);
            $this->projectId = $credentials['project_id'] ?? null;
        }
    }

    /**
     * Obtenir un token d'accès OAuth2 pour FCM v1 API
     */
    protected function getAccessToken()
    {
        if (!file_exists($this->credentialsPath)) {
            Log::error('FCM: Fichier de credentials Firebase non trouvé: ' . $this->credentialsPath);
            return null;
        }

        try {
            // Créer un client HTTP avec SSL désactivé pour Windows (dev uniquement)
            $httpClient = new \GuzzleHttp\Client([
                'verify' => false,
            ]);

            // Créer un handler callable à partir du client Guzzle
            $httpHandler = \Google\Auth\HttpHandler\HttpHandlerFactory::build($httpClient);

            $credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/firebase.messaging',
                json_decode(file_get_contents($this->credentialsPath), true)
            );

            $token = $credentials->fetchAuthToken($httpHandler);
            return $token['access_token'] ?? null;

        } catch (\Exception $e) {
            Log::error('FCM: Erreur lors de l\'obtention du token OAuth2: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Envoyer une notification push à un appareil via FCM v1 API
     */
    public function sendNotification($fcmToken, $title, $body, $data = [])
    {
        if (!$this->projectId) {
            Log::error('FCM: Project ID non configuré. Vérifiez le fichier firebase-credentials.json');
            return [
                'success' => false,
                'message' => 'FCM non configuré. Fichier firebase-credentials.json manquant.',
            ];
        }

        if (!$fcmToken) {
            return [
                'success' => false,
                'message' => 'Token FCM manquant',
            ];
        }

        $accessToken = trim($this->getAccessToken() ?? '');
        
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Impossible d\'obtenir le token d\'accès OAuth2',
            ];
        }

        Log::info('FCM Debug Token', ['length' => strlen($accessToken), 'starts_with' => substr($accessToken, 0, 15)]);

        // Construire le payload pour FCM v1 API (Android + iOS)
        $payload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                // Config Android
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound'        => 'default',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
                // Config iOS (APNs via FCM)
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10', // 10 = immédiat, 5 = économie batterie
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title,
                                'body'  => $body,
                            ],
                            'sound'             => 'default',
                            'badge'             => 1,
                            'content-available' => 1,
                        ],
                    ],
                ],
            ],
        ];

        // Ajouter les données personnalisées si fournies
        if (!empty($data)) {
            $stringData = [];
            foreach ($data as $key => $value) {
                $stringData[$key] = is_string($value) ? $value : json_encode($value);
            }
            $payload['message']['data'] = $stringData;
        }

        try {
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            // Utilisation d'un tableau d'en-tête brut pour être certain
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Authorization' => "Bearer " . $accessToken,
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                ])
                ->post($url, $payload);



            $result = $response->json();

            Log::info('FCM v1 Response:', ['status' => $response->status(), 'body' => $result]);

            if ($response->successful()) {
                return [
                    'success'  => true,
                    'message'  => 'Notification envoyée avec succès',
                    'response' => $result,
                ];
            }

            return [
                'success' => false,
                'message' => 'Échec d\'envoi de la notification',
                'error'   => $result['error']['message'] ?? 'Erreur inconnue',
            ];

        } catch (\Exception $e) {
            Log::error('FCM v1 Erreur: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Envoyer une notification à plusieurs appareils
     */
    public function sendToMultiple(array $fcmTokens, $title, $body, $data = [])
    {
        if (!$this->projectId) {
            return ['success' => false, 'message' => 'FCM non configuré'];
        }

        $results = [];
        $successCount = 0;
        $failureCount = 0;

        foreach ($fcmTokens as $token) {
            $result = $this->sendNotification($token, $title, $body, $data);
            $results[] = $result;
            $result['success'] ? $successCount++ : $failureCount++;
        }

        return [
            'success'       => $successCount > 0,
            'message'       => "Envoyé: {$successCount}, Échoué: {$failureCount}",
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'results'       => $results,
        ];
    }

    /**
     * Vérifier si FCM est correctement configuré
     */
    public function isConfigured()
    {
        return file_exists($this->credentialsPath) && $this->projectId !== null;
    }
}
