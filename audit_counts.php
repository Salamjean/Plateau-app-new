<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Naissance;
use Illuminate\Support\Facades\DB;

$userId = 17;
$results = Naissance::where('user_id', $userId)
    ->select('etat', 'statut_livraison', DB::raw('count(*) as count'))
    ->groupBy('etat', 'statut_livraison')
    ->get();

echo "AUDIT:\n";
foreach ($results as $r) {
    echo "{$r->etat} | " . ($r->statut_livraison ?? 'NULL') . " : {$r->count}\n";
}
