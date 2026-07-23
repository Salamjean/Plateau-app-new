<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::table('naissances')
    ->whereNotIn('etat', ['non_paye','paiement_en_attente','en attente de paiement','paiement_echoue'])
    ->where('choix_option','livraison')
    ->select('id','created_at','montant_livraison','statut_livraison')
    ->get();

echo "=== Toutes les naissances avec livraison payées ===\n";
foreach($rows as $r) {
    echo "id={$r->id} | created_at={$r->created_at} | montant={$r->montant_livraison} | statut={$r->statut_livraison}\n";
}

// Deces aussi
$rows2 = DB::table('deces')
    ->whereNotIn('etat', ['non_paye','paiement_en_attente','en attente de paiement','paiement_echoue'])
    ->where('choix_option','livraison')
    ->select('id','created_at','montant_livraison','statut_livraison')
    ->get();
echo "\n=== Deces avec livraison payés ===\n";
foreach($rows2 as $r) {
    echo "id={$r->id} | created_at={$r->created_at} | montant={$r->montant_livraison} | statut={$r->statut_livraison}\n";
}

// soldeMoisEnCours juillet 2025
$currentMonth = 7;
$currentYear = 2026;
$sumMois = DB::table('naissances')
    ->whereNotIn('etat', ['non_paye','paiement_en_attente','en attente de paiement','paiement_echoue'])
    ->where('choix_option','livraison')
    ->whereYear('created_at', $currentYear)
    ->whereMonth('created_at', $currentMonth)
    ->sum('montant_livraison');
echo "\nsoldeMoisEnCours (Naissance, {$currentMonth}/{$currentYear}): {$sumMois}\n";
