<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Paiement;

$payments = Paiement::where('status', 'ACCEPTED')->get();

echo "AUDIT DES DETAILED PAYMENTS & DEMANDS:\n";
foreach ($payments as $p) {
    $relation = null;
    if ($p->naissance_id) $relation = 'naissance';
    elseif ($p->mariage_id) $relation = 'mariage';
    elseif ($p->deces_id) $relation = 'deces';

    if (!$relation) {
        echo sprintf("Paiement ID: %d | Ref: %s | Montant: %s | Pas de relation d'acte individuelle\n", $p->id, $p->transaction_id, $p->montant);
        continue;
    }

    $demande = $p->$relation;
    if (!$demande) {
        echo sprintf("Paiement ID: %d | Ref: %s | Montant: %s | Relation: %s | DEMANDE INTROUVABLE\n", $p->id, $p->transaction_id, $p->montant, strtoupper($relation));
        continue;
    }

    $computedPartTimbre = getPaymentPartTimbreSimulated($p);
    $computedPartLivraison = (float)$p->montant - $computedPartTimbre;

    echo sprintf(
        "Paiement ID: %d | Ref: %s | PayMontant: %s | Acte: %s ID: %d | ActeRef: %s | Option: %s | Quantite: %s | DB_Timbre: %s | DB_Livraison: %s | ComputedTimbre: %s | ComputedLivraison: %s\n",
        $p->id,
        $p->transaction_id,
        $p->montant,
        strtoupper($relation),
        $demande->id,
        $demande->reference,
        $demande->choix_option ?? 'NULL',
        $demande->quantite ?? 'NULL',
        $demande->montant_timbre ?? 'NULL',
        $demande->montant_livraison ?? 'NULL',
        $computedPartTimbre,
        $computedPartLivraison
    );
}

function getPaymentPartTimbreSimulated($p) {
    if (isset($p->raw_response['part_timbre'])) {
        return (float) $p->raw_response['part_timbre'];
    }

    $isModification = str_contains((string) $p->transaction_id, '-MOD-');
    $montant = (float) $p->montant;

    $relation = null;
    if ($p->naissance_id) $relation = 'naissance';
    elseif ($p->mariage_id) $relation = 'mariage';
    elseif ($p->deces_id) $relation = 'deces';
    else return $montant;

    $demande = $p->$relation;
    if (!$demande) return $montant;

    // ATTENTION: Version sensible à la casse
    if (strtolower((string)$demande->choix_option) !== 'livraison') {
        return $montant;
    }

    $allPayments = Paiement::where("{$relation}_id", $demande->id)
        ->where('status', 'ACCEPTED')
        ->orderBy('id', 'asc')
        ->get();

    if ($allPayments->isEmpty()) {
        return $isModification ? $montant : max(0.0, $montant - 1500.0);
    }

    $deliveryPaymentId = null;
    foreach ($allPayments as $pay) {
        if (isset($pay->raw_response['part_livraison']) && (float)$pay->raw_response['part_livraison'] > 0) {
            $deliveryPaymentId = $pay->id;
            break;
        }
    }

    if ($deliveryPaymentId !== null) {
        if ($p->id === $deliveryPaymentId) {
            $partLivraison = (float)$p->raw_response['part_livraison'];
            return max(0.0, $montant - $partLivraison);
        }
        return $montant;
    }

    if ($allPayments->count() === 1) {
        return max(0.0, $montant - 1500.0);
    }

    if ($p->id === $allPayments->first()->id) {
        return max(0.0, $montant - 1500.0);
    }

    return $montant;
}
