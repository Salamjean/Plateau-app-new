<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    
    'cinetpay' => [
        'api_key' => env('CINETPAY_API_KEY'),
        'site_id' => env('CINETPAY_SITE_ID'),
        'mode' => env('CINETPAY_MODE', 'TEST')
    ],

    'infobip' => [
        'api_key' => env('INFOBIP_API_KEY'),
        'base_url' => env('INFOBIP_BASE_URL'),
    ],

    'orange' => [
        'client_id' => env('ORANGE_CLIENT_ID'),
        'client_secret' => env('ORANGE_CLIENT_SECRET'),
        'base_url' => env('ORANGE_BASE_URL', 'https://api.orange.com'),
        'ssl_cert_path' => env('SSL_CERT_PATH', storage_path('app/certs/cacert.pem')),
    ],

];
