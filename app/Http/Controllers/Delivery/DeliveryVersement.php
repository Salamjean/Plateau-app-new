<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Livreur;
use App\Models\VersementLivreur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryVersement extends Controller
{
    public function versement(Livreur $livreur)
{
    // Calculer le solde disponible du livreur (total des livraisons)
    $models = ['Deces', 'Mariage', 'Decesdeja', 'NaissanceD', 'Naissance'];
    $soldeDisponible = 0;
    
    foreach ($models as $model) {
        $classe = "App\\Models\\$model";
        $soldeDisponible += $classe::where('livreur_id', $livreur->id)
            ->where('statut_livraison', 'livré')
            ->sum('montant_livraison');
    }
    
    // Calculer le total des versements déjà effectués
    $totalVersements = VersementLivreur::where('livreur_id', $livreur->id)
        ->sum('montant');
    
    // Calculer le solde réellement disponible
    $soldeReel = $soldeDisponible - $totalVersements;
    
    // Récupérer l'historique des versements
    $versements = VersementLivreur::where('livreur_id', $livreur->id)
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('poste.livreur.versement', compact('livreur', 'soldeDisponible', 'soldeReel', 'totalVersements', 'versements'));
}

    public function processVersement(Request $request, Livreur $livreur)
{
    $request->validate([
        'montant' => 'required|numeric|min:1',
        'methode_paiement' => 'required|string|max:255',
        'reference' => 'nullable|string|max:255'
    ]);
    
    // Calculer le solde disponible
    $models = ['Deces', 'Mariage', 'Decesdeja', 'NaissanceD', 'Naissance'];
    $soldeDisponible = 0;
    
    foreach ($models as $model) {
        $classe = "App\\Models\\$model";
        $soldeDisponible += $classe::where('livreur_id', $livreur->id)
            ->where('statut_livraison', 'livré')
            ->sum('montant_livraison');
    }
    
    // Calculer le total des versements déjà effectués
    $totalVersements = VersementLivreur::where('livreur_id', $livreur->id)
        ->sum('montant');
    
    // Calculer le solde réellement disponible (total livraisons - versements déjà effectués)
    $soldeReel = $soldeDisponible - $totalVersements;
    
    // Vérifier si le montant demandé est disponible
    if ($request->montant > $soldeReel) {
        return redirect()->back()->with('error', 'Le montant demandé dépasse le solde disponible. Solde disponible: ' . number_format($soldeReel, 0, ',', ' ') . ' FCFA');
    }
    
    // Créer le versement
    $versement = VersementLivreur::create([
        'livreur_id' => $livreur->id,
        'montant' => $request->montant,
        'methode_paiement' => $request->methode_paiement,
        'reference' => $request->reference,
        'statut' => 'completé',
        'effectue_par' => Auth::guard('poste')->user()->id
    ]);
    
    return redirect()->back()->with('success', 'Versement de ' . number_format($request->montant, 0, ',', ' ') . ' FCFA effectué avec succès. Nouveau solde disponible: ' . number_format(($soldeReel - $request->montant), 0, ',', ' ') . ' FCFA');
}
}
