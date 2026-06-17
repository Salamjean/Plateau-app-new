<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Paiement;
use App\Models\Naissance;

echo "Vérification des correspondances par Référence :\n";
foreach (Paiement::where('status', 'ACCEPTED')->latest()->take(20)->get() as $p) {
    $ref = $p->transaction_id;
    // Extraire la base
    $baseRef = $ref;
    if (str_contains($ref, '-MOD-')) {
        $baseRef = explode('-MOD-', $ref)[0];
    }
    
    // Rechercher dans naissance
    $naisByRef = Naissance::where('reference', $baseRef)->first();
    $naisById = $p->naissance_id ? Naissance::find($p->naissance_id) : null;
    
    echo sprintf(
        "Paiement ID: %d | Ref: %s | nais_id dans pay: %s | Trouvé par ID: %s | Trouvé par Ref (ID): %s\n",
        $p->id,
        $p->transaction_id,
        var_export($p->naissance_id, true),
        $naisById ? 'OUI (Ref: ' . $naisById->reference . ', Option: ' . $naisById->choix_option . ')' : 'NON',
        $naisByRef ? 'OUI (ID: ' . $naisByRef->id . ', Option: ' . $naisByRef->choix_option . ')' : 'NON'
    );
}
