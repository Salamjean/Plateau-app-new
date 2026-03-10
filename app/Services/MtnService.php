<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MtnService
{
    protected $apiUserId;
    protected $apiKey;
    protected $subscriptionKey;
    protected $targetEnvironment;
    protected $baseUrl;
    protected $currency;

    public function __construct()
    {
        $this->apiUserId = config('services.mtn.api_user_id');
        $this->apiKey = config('services.mtn.api_key');
        $this->subscriptionKey = config('services.mtn.subscription_key');
        $this->targetEnvironment = config('services.mtn.target_environment');
        $this->baseUrl = config('services.mtn.base_url');
        $this->currency = config('services.mtn.currency', 'EUR');
    }

    /**
     * Get the OAuth2.0 access token
     *
     * @return string|null
     */
    public function getAccessToken()
    {
        // Token is typically valid for 3600 seconds, cache it to avoid requesting a new one every time
        return Cache::remember('mtn_access_token', 3500, function () {
            try {
                $credentials = base64_encode($this->apiUserId . ':' . $this->apiKey);

                $response = Http::withHeaders([
                    'Authorization' => "Basic " . $credentials,
                    'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                ])->withoutVerifying()->post($this->baseUrl . '/collection/token/');

                if ($response->successful()) {
                    return $response->json()['access_token'] ?? null;
                }

                Log::error('MTN API Token Error: ' . $response->body());
                return null;
            } catch (\Exception $e) {
                Log::error('MTN Service Token Exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Request a payment (push USSD to user)
     *
     * @param float $amount
     * @param string $phoneNumber
     * @param string $externalId (e.g. your application order ID)
     * @param string $payerMessage
     * @param string $payeeNote
     * @return array|null Returns the generated reference ID and status
     */
    public function requestToPay($amount, $phoneNumber, $externalId, $payerMessage = 'Paiement', $payeeNote = 'Paiement')
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            $referenceId = Str::uuid()->toString();

            $response = Http::withHeaders([
                'Authorization' => "Bearer " . $token,
                'X-Reference-Id' => $referenceId,
                'X-Target-Environment' => $this->targetEnvironment,
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                // 'X-Callback-Url' => config('app.url') . '/api/webhooks/mtn/notify', 
                'Content-Type' => 'application/json',
            ])->withoutVerifying()->post($this->baseUrl . '/collection/v1_0/requesttopay', [
                'amount' => (string) $amount,
                'currency' => $this->currency,
                'externalId' => (string) $externalId,
                'payer' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => $phoneNumber
                ],
                'payerMessage' => $payerMessage,
                'payeeNote' => $payeeNote
            ]);

            if ($response->successful() && $response->status() === 202) {
                // Return the X-Reference-Id so the application can look it up later
                return [
                    'referenceId' => $referenceId,
                    'status' => 'PENDING'
                ];
            }

            Log::error('MTN API RequestToPay Error: ' . $response->body() . ' Status: ' . $response->status());
            return null;
        } catch (\Exception $e) {
            Log::error('MTN Service RequestToPay Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get transaction status
     *
     * @param string $referenceId
     * @return array|null
     */
    public function getTransactionStatus($referenceId)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            $response = Http::withHeaders([
                'Authorization' => "Bearer " . $token,
                'X-Target-Environment' => $this->targetEnvironment,
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            ])->withoutVerifying()->get($this->baseUrl . '/collection/v1_0/requesttopay/' . $referenceId);

            if ($response->successful()) {
                // typical response includes { "status": "PENDING|SUCCESSFUL|FAILED", "reason": "...", ... }
                return $response->json();
            }

            Log::error('MTN API GetTransactionStatus Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('MTN Service GetTransactionStatus Exception: ' . $e->getMessage());
            return null;
        }
    }
}
