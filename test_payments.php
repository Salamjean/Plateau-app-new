<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$demande = \App\Models\Naissance::where('reference', 'AN3122102P2026')->first();
if ($demande) {
    echo json_encode(\App\Models\Paiement::where('naissance_id', $demande->id)->get()->toArray(), JSON_PRETTY_PRINT);
} else {
    echo "Demande not found.";
}
