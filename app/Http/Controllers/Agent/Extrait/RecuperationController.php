<?php

namespace App\Http\Controllers\Agent\Extrait;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecuperationController extends Controller
{
    /**
     * Compte les demandes "actives" (ni terminées, ni rejetées) de l'agent.
     */
    private function compterDemandesEnAttente($agentId)
    {
        // --- MODIFICATION ---
        // Définir les états qui sont considérés comme "finaux"
        $etatsFinaux = ['terminé', 'rejetée'];

        // Compter uniquement les demandes qui NE SONT PAS dans les états finaux
        return Naissance::where('agent_id', $agentId)->whereNotIn('etat', $etatsFinaux)->count() +
               Deces::where('agent_id', $agentId)->whereNotIn('etat', $etatsFinaux)->count() +
               Mariage::where('agent_id', $agentId)->whereNotIn('etat', $etatsFinaux)->count();
    }

    private function traiterDemandeGenerique($modelClass, $id, $successRoute, $modelName)
    {
        $agent = Auth::guard('agent')->user();
        
        // Ce compteur est maintenant correct grâce à la modification ci-dessus
        $pendingRequestsCount = $this->compterDemandesEnAttente($agent->id);

        if ($pendingRequestsCount >= 2) {
            return redirect()->route('agent.dashboard')->with('error', 'Vous avez 2 demandes en attente. Veuillez terminer les demandes en attente.');
        }

        $demande = $modelClass::find($id);

        if (!$demande) {
            // Redirige vers la route d'index (ex: .../deces) en cas d'erreur
            return redirect()->route($successRoute)->with('error', 'Demande introuvable.'); 
        }

        if ($demande->agent_id) {
            return redirect()->route('agent.dashboard')->with('error', "Cette demande de {$modelName} a déjà été récupérée par un autre agent.");
        }

        $demande->is_read = true;
        $demande->agent_id = $agent->id;
        $demande->etat = 'réçu';
        $demande->save();

        return redirect()->route($successRoute)->with('success', "Demande de {$modelName} récupérée avec succès.");
    }

    public function RecupererNaissance($id)
    {
        // --- SIMPLIFICATION ---
        // La vérification de l'existence se fait dans traiterDemandeGenerique
        return $this->traiterDemandeGenerique(Naissance::class, $id, 'agent.demandes.naissance.index', 'naissance');
    }

    public function RecupererDeces($id)
    {
        // --- SIMPLIFICATION ---
        return $this->traiterDemandeGenerique(Deces::class, $id,'agent.demandes.deces.index' , 'décès');
    }

    public function RecupererMariage($id)
    {
        return $this->traiterDemandeGenerique(Mariage::class, $id,'agent.demandes.wedding.index' , 'mariage');
    }
}