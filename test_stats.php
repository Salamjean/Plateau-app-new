<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
$request = Illuminate\Http\Request::create('/api/utilisateurs/demandes/toutes', 'GET');
$request->setUserResolver(function() use ($user) { return $user; });
$controller = new App\Http\Controllers\Api\Utilisateurs\StatistiqueController();

$response = $controller->listeToutesDemandes($request);
echo "LISTE TOUTES:\n";
echo json_encode($response->getData(), JSON_PRETTY_PRINT) . "\n";

$response2 = $controller->statistiquesParStatut($request);
echo "STATISTIQUES:\n";
echo json_encode($response2->getData(), JSON_PRETTY_PRINT) . "\n";
