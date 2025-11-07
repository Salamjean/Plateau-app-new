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
        '/deces/paiement/redirect-to-app',
        '/api/webhooks/cinetpay/notify/deces',
        // Ajoutez d'autres routes webhook si nécessaire
    ];
}