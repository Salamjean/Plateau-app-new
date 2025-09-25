<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
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
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
