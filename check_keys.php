<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Paiement;

$payments = Paiement::where('status', 'ACCEPTED')->get();

echo "ANALYSE DE TOUS LES PAIEMENTS ACCEPTES :\n";
foreach ($payments as $p) {
    $relation = null;
    if ($p->naissance_id) $relation = 'naissance';
    elseif ($p->mariage_id) $relation = 'mariage';
    elseif ($p->deces_id) $relation = 'deces';
    
    $choix = 'N/A';
    if ($relation) {
        $demande = $p->$relation;
        if ($demande) {
            $choix = $demande->choix_option ?? 'NULL';
        } else {
            $choix = 'DEMANDE_MANQUANTE';
        }
    }

    $rawResponsePartTimbre = isset($p->raw_response['part_timbre']) ? $p->raw_response['part_timbre'] : 'NON_DEFINI';
    $rawResponsePartLivraison = isset($p->raw_response['part_livraison']) ? $p->raw_response['part_livraison'] : 'NON_DEFINI';

    echo sprintf(
        "ID: %d | Ref: %s | Montant: %s | Type: %s | Option: %s | PartTimbre raw: %s | PartLivraison raw: %s\n",
        $p->id,
        $p->transaction_id,
        $p->montant,
        $relation ? strtoupper($relation) : 'GROUPE/AUTRE',
        $choix,
        $rawResponsePartTimbre,
        $rawResponsePartLivraison
    );
}
