<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Paiement;

$allPayments = Paiement::where('status', 'ACCEPTED')->get();

$rawN = 0; $stampN = 0;
$rawM = 0; $stampM = 0;
$rawD = 0; $stampD = 0;

foreach ($allPayments as $p) {
    // Calcul de secours simulé
    $pt = getPaymentPartTimbreSimulatedLocal($p);
    $raw = (float) $p->montant;
    
    if ($p->naissance_id) {
        $rawN += $raw;
        $stampN += $pt;
    } elseif ($p->mariage_id) {
        $rawM += $raw;
        $stampM += $pt;
    } elseif ($p->deces_id) {
        $rawD += $raw;
        $stampD += $pt;
    }
}

function getPaymentPartTimbreSimulatedLocal($p) {
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

echo "COMPARAISON DES TOTAUX BRUTS vs TIMBRES :\n";
echo sprintf("Naissance : Brut = %s | Timbres = %s (Diff: %s)\n", $rawN, $stampN, $rawN - $stampN);
echo sprintf("Mariage   : Brut = %s | Timbres = %s (Diff: %s)\n", $rawM, $stampM, $rawM - $stampM);
echo sprintf("Décès     : Brut = %s | Timbres = %s (Diff: %s)\n", $rawD, $stampD, $rawD - $stampD);
echo sprintf("Total     : Brut = %s | Timbres = %s (Diff: %s)\n", ($rawN+$rawM+$rawD), ($stampN+$stampM+$stampD), ($rawN+$rawM+$rawD) - ($stampN+$stampM+$stampD));
