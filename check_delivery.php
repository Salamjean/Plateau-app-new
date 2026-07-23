<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::table('naissances')
    ->where('choix_option','livraison')
    ->select('id','nom_destinataire','prenom_destinataire','contact_destinataire','adresse_livraison','ville','commune_livraison','quartier','etat','statut_livraison')
    ->get();

echo "=== Champs livraison dans naissances ===\n";
foreach($rows as $r) {
    echo "\nid={$r->id}:\n";
    echo "  nom_destinataire: ".($r->nom_destinataire ?? 'NULL')."\n";
    echo "  prenom_destinataire: ".($r->prenom_destinataire ?? 'NULL')."\n";
    echo "  contact_destinataire: ".($r->contact_destinataire ?? 'NULL')."\n";
    echo "  adresse_livraison: ".($r->adresse_livraison ?? 'NULL')."\n";
    echo "  ville: ".($r->ville ?? 'NULL')."\n";
    echo "  commune_livraison: ".($r->commune_livraison ?? 'NULL')."\n";
    echo "  quartier: ".($r->quartier ?? 'NULL')."\n";
    echo "  etat: ".($r->etat ?? 'NULL')."\n";
    echo "  statut_livraison: ".($r->statut_livraison ?? 'NULL')."\n";
}
