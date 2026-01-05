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

        // Récupération des identifiants Yéllika depuis le .env (avec trim pour éviter les espaces invisibles)
        $this->apiKey = trim(env('YELLIKA_API_KEY'));
        $this->senderId = env('YELLIKA_SENDER_ID', 'Notify');
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
            'sender_id' => $this->senderId, // Doit correspondre à un Sender ID validé
            'message' => $this->message,
            'type' => 'plain'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Important: Authentification Bearer Token
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->apiKey,
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
            return ['success' => true, 'response' => $response];
        }
    }
}
