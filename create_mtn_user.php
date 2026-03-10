<?php
require __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

$client = new Client(['base_uri' => 'https://sandbox.momodeveloper.mtn.com']);
$subscriptionKey = '3e5597197dcb4304af06f52725757f43';
$uuid = Uuid::uuid4()->toString();

$output = "UUID (API USER ID): " . $uuid . "\n";

try {
    // 1. Create API User
    $client->post('/v1_0/apiuser', [
        'headers' => [
            'X-Reference-Id' => $uuid,
            'Ocp-Apim-Subscription-Key' => $subscriptionKey,
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'providerCallbackHost' => 'plateau-app-new.com'
        ]
    ]);
    
    // 2. Create API Key
    $response3 = $client->post('/v1_0/apiuser/' . $uuid . '/apikey', [
        'headers' => [
            'Ocp-Apim-Subscription-Key' => $subscriptionKey,
        ]
    ]);
    
    $apiKeyData = json_decode($response3->getBody(), true);
    
    $envContent = "MTN_API_USER_ID=" . $uuid . "\n" .
                  "MTN_API_KEY=" . $apiKeyData['apiKey'] . "\n" .
                  "MTN_SUBSCRIPTION_KEY=" . $subscriptionKey . "\n" .
                  "MTN_TARGET_ENVIRONMENT=sandbox\n";
    
    file_put_contents('mtn_env.txt', $envContent);
    echo "Done.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
