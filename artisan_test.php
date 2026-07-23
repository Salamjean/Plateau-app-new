<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$solde = 0;
foreach (['Naissance','Deces','Mariage'] as $m) {
    $class = "App\\Models\\{$m}";
    $v = $class::paye()->where('choix_option','livraison')->sum('montant_livraison');
    echo $m.': '.$v."\n";
    $solde += $v;
}
echo 'TOTAL soldeDisponible: '.$solde."\n";

// Also check id=15
$r = \App\Models\Naissance::find(15);
echo "\nNaissance id=15: etat=".$r->etat.", montant_livraison=".$r->montant_livraison.", statut_livraison=".$r->statut_livraison."\n";
echo "In paye() scope: ".\App\Models\Naissance::paye()->where('id',15)->count()." records\n";
