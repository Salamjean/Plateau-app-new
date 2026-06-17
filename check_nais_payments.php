<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Paiement;

// Simuler getPaymentPartTimbre
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

$naisPayments = Paiement::whereNotNull('naissance_id')->where('status', 'ACCEPTED')->get();
echo "DÉTAILS DES PAIEMENTS NAISSANCE :\n";
foreach ($naisPayments as $p) {
    $demande = $p->naissance;
    $option = $demande ? $demande->choix_option : 'N/A';
    $pt = getPaymentPartTimbreSimulated($p);
    echo sprintf(
        "ID: %d | Ref: %s | Montant: %s | Option: %s | Part Timbre: %s\n",
        $p->id,
        $p->transaction_id,
        $p->montant,
        $option,
        $pt
    );
}
