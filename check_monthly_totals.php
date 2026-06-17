<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Paiement;
use App\Models\Mairie;
use Carbon\Carbon;

// Simuler la mairie de Plateau
$mairie = Mairie::first(); // ou Mairie::where('name', 'like', '%Plateau%')->first();
if (!$mairie) {
    echo "Aucune mairie trouvée !\n";
    exit;
}

echo "Mairie sélectionnée : " . $mairie->name . "\n";

$startOfMonth = Carbon::now()->startOfMonth();
$endOfMonth = Carbon::now()->endOfMonth();

echo "Période : " . $startOfMonth->toDateTimeString() . " à " . $endOfMonth->toDateTimeString() . "\n";

$naissancesPaiementsMonth = Paiement::whereHas('naissance', function ($query) use ($mairie) {
    $query->where('commune', $mairie->name);
})
->where('status', 'ACCEPTED')
->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
->get();

$totalNaissanceMonth = $naissancesPaiementsMonth->sum(function ($p) {
    return getPaymentPartTimbreSimulated($p);
});

echo "Naissance Month count: " . $naissancesPaiementsMonth->count() . "\n";
foreach ($naissancesPaiementsMonth as $p) {
    echo sprintf("  Paiement ID: %d | Montant: %s | Part Timbre: %s\n", $p->id, $p->montant, getPaymentPartTimbreSimulated($p));
}
echo "Total Naissance Month: " . $totalNaissanceMonth . "\n\n";

$mariagesPaiementsMonth = Paiement::whereHas('mariage', function ($query) use ($mairie) {
    $query->where('commune', $mairie->name);
})
->where('status', 'ACCEPTED')
->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
->get();

$totalMariageMonth = $mariagesPaiementsMonth->sum(function ($p) {
    return getPaymentPartTimbreSimulated($p);
});

echo "Mariage Month count: " . $mariagesPaiementsMonth->count() . "\n";
foreach ($mariagesPaiementsMonth as $p) {
    echo sprintf("  Paiement ID: %d | Montant: %s | Part Timbre: %s\n", $p->id, $p->montant, getPaymentPartTimbreSimulated($p));
}
echo "Total Mariage Month: " . $totalMariageMonth . "\n\n";

$decesPaiementsMonth = Paiement::whereHas('deces', function ($query) use ($mairie) {
    $query->where('commune', $mairie->name);
})
->where('status', 'ACCEPTED')
->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
->get();

$totalDecesMonth = $decesPaiementsMonth->sum(function ($p) {
    return getPaymentPartTimbreSimulated($p);
});

echo "Deces Month count: " . $decesPaiementsMonth->count() . "\n";
foreach ($decesPaiementsMonth as $p) {
    echo sprintf("  Paiement ID: %d | Montant: %s | Part Timbre: %s\n", $p->id, $p->montant, getPaymentPartTimbreSimulated($p));
}
echo "Total Deces Month: " . $totalDecesMonth . "\n\n";

$soldePortefeuille = $totalNaissanceMonth + $totalMariageMonth + $totalDecesMonth;
echo "Solde Portefeuille Total du mois : " . $soldePortefeuille . " FCFA\n";

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
    if ($demande->choix_option !== 'livraison') {
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
