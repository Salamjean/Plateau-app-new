<?php

namespace App\Http\Controllers\Mairie\Extrait;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MairieDecesController extends Controller
{
    public function deathRequest(Request $request)
    {
        $mairie = Auth::guard('mairie')->user();

        // Valider les paramètres de filtrage
        $validated = $request->validate([
            'etat' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'livraison' => 'nullable|string|max:255',
        ]);

        $etat = $validated['etat'] ?? null;
        $type = $validated['type'] ?? null;
        $livraison = $validated['livraison'] ?? null;

        // Construire la requête avec les filtres
        $query = Deces::where('commune', $mairie->name);

        if ($etat) {
            $query->where('etat', $etat);
        } else {
            $query->paye();
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($livraison) {
            $query->where('statut_livraison', $livraison);
        }

        $deces = $query->with('agent')->paginate(10);

        return view('mairie.extraits.deces.index', compact('mairie', 'deces', 'etat', 'type', 'livraison'));
    }
}
