<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'deces/paiement/redirect-to-app',
        'mariage/paiement/redirect-to-app',
        'naissance/paiement/redirect-to-app', // ✅ AJOUTÉ
        
        // --- Route d'authentification Google (token Firebase vérifié côté serveur) ---
        'user/auth/google',
        
        // --- Routes de Webhook API ---
        'api/webhooks/cinetpay/notify/deces',
        'api/webhooks/cinetpay/notify/mariage',
        'api/webhooks/cinetpay/notify/naissance', // ✅ AJOUTÉ
    ];
}