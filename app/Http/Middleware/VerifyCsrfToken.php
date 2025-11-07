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
        // Soyons très spécifique, sans le joker
        'deces/paiement/redirect-to-app',
        
        // Si vous avez les mêmes routes pour d'autres services,
        // ajoutez-les aussi explicitement.
        // 'mariage/paiement/redirect-to-app',
        // 'naissance/paiement/redirect-to-app',

        // Gardez l'exception pour le webhook de notification
        'api/webhooks/cinetpay/notify/deces',
    ];
}