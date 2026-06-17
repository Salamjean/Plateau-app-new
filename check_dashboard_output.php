<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Paiement;
use App\Models\Naissance;
use App\Models\Mariage;
use App\Models\Deces;

// Simuler getPaymentPartTimbre comme défini dans FinanceDashboard / MairieDashboard
function getPaymentPartTimbreSimulated($p) {
    if (isset($p->raw_response['part_timbre'])) {
        return (float) $p->raw_response['part_timbre'];
    }

    $isModification = str_contains((string) $p->transaction_id, '-MOD-');
    $montant = (float) $p->montant;

    $relation = null;
    if ($p->naissance_id) {
        $relation = 'naissance';
    } elseif ($p->mariage_id) {
        $relation = 'mariage';
    } elseif ($p->deces_id) {
        $relation = 'deces';
    } elseif ($p->naissance_groupe_id || $p->mariage_groupe_id || $p->deces_groupe_id) {
        return $montant;
    }

    if (!$relation) {
        return $montant;
    }

    $demande = $p->$relation;
    if (!$demande) {
        return $montant;
    }

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

    $modificationPayment = $allPayments->first(function ($pay) {
        return str_contains((string) $pay->transaction_id, '-MOD-');
    });

    $initialPayment = $allPayments->first(function ($pay) {
        return !str_contains((string) $pay->transaction_id, '-MOD-');
    });

    if ($modificationPayment && $initialPayment) {
        if ((float)$initialPayment->montant < 1500.0) {
            if ($p->id === $modificationPayment->id) {
                return max(0.0, $montant - 1500.0);
            }
            return $montant;
        }
        if ($p->id === $initialPayment->id) {
            return max(0.0, $montant - 1500.0);
        }
        return $montant;
    }

    if ($p->id === $allPayments->first()->id) {
        return max(0.0, $montant - 1500.0);
    }

    return $montant;
}

// Simulons pour toutes les communes
$allPayments = Paiement::where('status', 'ACCEPTED')->get();
echo "Calculs globaux de tous les paiements ACCEPTED :\n";

$totalN = 0;
$totalM = 0;
$totalD = 0;

foreach ($allPayments as $p) {
    $pt = getPaymentPartTimbreSimulated($p);
    
    if ($p->naissance_id) $totalN += $pt;
    elseif ($p->mariage_id) $totalM += $pt;
    elseif ($p->deces_id) $totalD += $pt;
    
    // Si c'est livraison, on affiche les détails
    $relation = $p->naissance_id ? 'naissance' : ($p->mariage_id ? 'mariage' : ($p->deces_id ? 'deces' : null));
    $demande = $relation ? $p->$relation : null;
    if ($demande && strtolower((string)$demande->choix_option) === 'livraison') {
        echo sprintf(
            "Paiement ID %d (Ref %s) | Montant total payé: %s | Part Timbre calculée: %s | Livraison: %s\n",
            $p->id,
            $p->transaction_id,
            $p->montant,
            $pt,
            $p->montant - $pt
        );
    }
}

echo "\nTOTAUX :\n";
echo "Total Naissance: " . $totalN . " XOF\n";
echo "Total Mariage: " . $totalM . " XOF\n";
echo "Total Décès: " . $totalD . " XOF\n";
echo "Solde Portefeuille Global: " . ($totalN + $totalM + $totalD) . " XOF\n";
