<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', 
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'mairie' => \App\Http\Middleware\MairieMiddleware::class,
            'etatCivil' => \App\Http\Middleware\EtatCivilMiddleware::class,
            'agent' => \App\Http\Middleware\AgentMiddleware::class,
            'finance' => \App\Http\Middleware\FinanceMiddleware::class,
            'comptable' => \App\Http\Middleware\ComptableMiddleware::class,
            'poste' => \App\Http\Middleware\PosteMiddleware::class,
            'livreur' => \App\Http\Middleware\LivreurMiddleware::class,
            'dhl' => \App\Http\Middleware\DhlMiddleware::class,
            'agency' => \App\Http\Middleware\AgencyMiddleware::class,
        ]);

        // --- AJOUTEZ CETTE SECTION ---
        // C'est la nouvelle façon (Laravel 11+) de définir
        // les exceptions CSRF (remplace $except dans VerifyCsrfToken.php)
        $middleware->validateCsrfTokens(except: [
            'deces/paiement/redirect-to-app',       // Pour le retour CinetPay (POST)
            'api/webhooks/cinetpay/notify/deces', // Pour le webhook CinetPay (POST)
            
            // Si vous avez d'autres routes, ajoutez-les aussi :
            // 'mariage/paiement/redirect-to-app',
            // 'naissance/paiement/redirect-to-app',
        ]);
        // --- FIN DE L'AJOUT ---

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();