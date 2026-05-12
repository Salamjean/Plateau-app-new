<?php

namespace App\Repositories;

class SmsRepository
{
    private $apiKey;
    private $senderId;
    private $baseUrl;

    protected $message;
    protected $phone;

    public function __construct($phone, $message)
    {
        $this->phone = $phone;
        $this->message = $message;

        // Récupération des identifiants Yéllika depuis le .env (avec trim pour éviter les espaces invisibles et guillemets)
        $this->apiKey = trim(env('YELLIKA_API_KEY'));
        $this->senderId = trim(env('YELLIKA_SENDER_ID', 'Plateau app'), ' "\'');
        // On récupère la base URL (ex: https://app.1smsafrica.com/api/v3)
        $this->baseUrl = rtrim(env('YELLIKA_API_URL', 'https://app.1smsafrica.com/api/v3'), '/');
    }

    public function send()
    {
        // Nettoyer le numéro (garder chiffres)
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);

        // Formatage pour 1smsafrica (souvent 225xxxxxxxxx)
        if (!str_starts_with($cleanPhone, '225')) {
            if (str_starts_with($cleanPhone, '00225')) {
                $cleanPhone = substr($cleanPhone, 2);
            } else {
                $cleanPhone = '225' . $cleanPhone;
            }
        }

        // Endpoint V3 standard pour l'envoi de SMS
        $url = $this->baseUrl . "/sms/send";

        // Paramètres pour l'API V3 (POST JSON)
        $data = [
            'recipient' => $cleanPhone,
            'sender_id' => trim($this->senderId), // Nettoyer les guillemets
            'message' => $this->message,
            'type' => 'plain'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Debug: Log la clé API utilisée
        \Illuminate\Support\Facades\Log::info("Debug 1smsafrica - API Key used: " . substr($this->apiKey, 0, 10) . "...");
        \Illuminate\Support\Facades\Log::info("Debug 1smsafrica - Sender ID: " . trim($this->senderId));
        \Illuminate\Support\Facades\Log::info("Debug 1smsafrica - Request data: " . json_encode($data));

        // Extraire la clé réelle si elle contient le format 811|clé
        $apiKeyToUse = $this->apiKey;
        \Illuminate\Support\Facades\Log::info("Debug 1smsafrica - Full API Key (trimmed): " . $this->apiKey);
        \Illuminate\Support\Facades\Log::info("Debug 1smsafrica - Checking for pipe character: " . (strpos($this->apiKey, '|') !== false ? 'YES' : 'NO'));

        if (strpos($this->apiKey, '|') !== false) {
            $parts = explode('|', $this->apiKey);
            $apiKeyToUse = end($parts); // Prendre la dernière partie après |
            \Illuminate\Support\Facades\Log::info("Debug 1smsafrica - Extracting API key from format 'ID|KEY': " . substr($apiKeyToUse, 0, 10) . "...");
        } else {
            \Illuminate\Support\Facades\Log::info("Debug 1smsafrica - Using API key as-is: " . substr($apiKeyToUse, 0, 10) . "...");
        }

        // Essayer le format d'authentification avec Bearer Token
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $apiKeyToUse,
            "Content-Type: application/json",
            "Accept: application/json"
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

        curl_close($ch);

        if ($error) {
            \Illuminate\Support\Facades\Log::error("1smsafrica Curl Error: $error");
            return ['success' => false, 'error' => $error];
        } else {
            \Illuminate\Support\Facades\Log::info("1smsafrica Response ($httpCode) at $effectiveUrl (Redirect: $redirectUrl): $response");

            // Vérifier la réponse JSON pour s'assurer du succès
            $decodedResponse = json_decode($response, true);
            if ($decodedResponse && isset($decodedResponse['status'])) {
                if ($decodedResponse['status'] === 'success') {
                    return ['success' => true, 'response' => $response];
                } else {
                    $errorMsg = $decodedResponse['message'] ?? 'Unknown error';
                    \Illuminate\Support\Facades\Log::error("1smsafrica API Error: $errorMsg");
                    return ['success' => false, 'error' => $errorMsg, 'response' => $response];
                }
            }

            return ['success' => true, 'response' => $response];
        }
    }
}
