<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaveService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.wave.api_key');
        $this->baseUrl = config('services.wave.base_url');
    }

    /**
     * Create a checkout session on Wave CI
     *
     * @param float $amount
     * @param string $currency
     * @param string $successUrl
     * @param string $errorUrl
     * @param string $clientReference
     * @return array|null
     */
    public function createCheckoutSession($amount, $currency, $successUrl, $errorUrl, $clientReference)
    {
        // Détecter dynamiquement si la requête provient d'un tunnel ngrok.
        // Si c'est le cas, on remplace l'hôte localhost/lvh.me par l'URL ngrok active
        // pour que l'utilisateur soit correctement redirigé vers son tunnel après le paiement.
        if (!app()->runningInConsole() && request()->getHost() && str_contains(request()->getHost(), 'ngrok')) {
            $currentSchemeAndHost = request()->getSchemeAndHttpHost();
            $configuredAppUrl = rtrim(config('app.url'), '/');
            
            if ($configuredAppUrl) {
                $successUrl = str_replace($configuredAppUrl, $currentSchemeAndHost, $successUrl);
                $errorUrl = str_replace($configuredAppUrl, $currentSchemeAndHost, $errorUrl);
            }
            
            // Sécurité additionnelle si localhost est présent
            $successUrl = str_replace('://localhost', '://' . request()->getHost(), $successUrl);
            $errorUrl = str_replace('://localhost', '://' . request()->getHost(), $errorUrl);
        }

        // Force HTTPS unconditionally — Wave API strictly requires https:// URLs
        // even for local development environments.
        $successUrl = str_replace('http://', 'https://', $successUrl);
        $errorUrl = str_replace('http://', 'https://', $errorUrl);

        // Wave also requires a host with a Top Level Domain (TLD). 
        // 'localhost' is rejected. We replace it with 'lvh.me' (which points to 127.0.0.1)
        // or '127.0.0.1.nip.io' to pass validation during local tests.
        $successUrl = str_replace('://localhost', '://lvh.me', $successUrl);
        $errorUrl = str_replace('://localhost', '://lvh.me', $errorUrl);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer " . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->withoutVerifying()->post($this->baseUrl, [
                'amount' => (string) $amount,
                'currency' => $currency,
                'success_url' => $successUrl,
                'error_url' => $errorUrl,
                'client_reference' => $clientReference
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Wave API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Wave Service Exception: ' . $e->getMessage());
            return null;
        }
    }
    public function getCheckoutSession($id)
    {
        try {
            // Wave API URL for retrieving a session usually involves the ID
            // For Wave CI, it might be slightly different depending on their exact spec, 
            // but usually it's a GET request to the same endpoint with the ID.
            $response = Http::withHeaders([
                'Authorization' => "Bearer " . $this->apiKey,
                'Content-Type' => 'application/json'
            ])->withoutVerifying()->get($this->baseUrl . '/' . $id);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Wave API Error (Get Session): ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Wave Service Exception (Get Session): ' . $e->getMessage());
            return null;
        }
    }
}
