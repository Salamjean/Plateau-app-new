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
     private function compterDemandesEnAttente($agentId)
    {
        return Naissance::where('agent_id', $agentId)->where('etat', '!=', 'terminé')->count() +
                Deces::where('agent_id', $agentId)->where('etat', '!=', 'terminé')->count() +
                Mariage::where('agent_id', $agentId)->where('etat', '!=', 'terminé')->count();
    }

    private function traiterDemandeGenerique($modelClass, $id, $successRoute, $modelName)
    {
        $agent = Auth::guard('agent')->user();
        $pendingRequestsCount = $this->compterDemandesEnAttente($agent->id);

            if ($pendingRequestsCount >= 2) {
                return redirect()->route('agent.dashboard')->with('error', 'Vous avez 2 demandes en attente. Veuillez terminer les demandes en attente.');
            }

            $demande = $modelClass::find($id);

            if (!$demande) {
                return redirect()->route($successRoute)->with('error', 'Demande introuvable.'); 
            }

            if ($demande->agent_id) {
                return redirect()->route('agent.dashboard')->with('error', "Cette demande de {$modelName} a déjà été récupérée par un autre agent.");
            }

            $demande->is_read = true;
            $demande->agent_id = $agent->id;
            $demande->etat = 'en attente';
            $demande->save();

            return redirect()->route($successRoute)->with('success', "Demande de {$modelName} récupérée avec succès.");
        }

        public function RecupererNaissance($id)
        {
            $naissance = Naissance::find($id);
            if ($naissance) {
                return $this->traiterDemandeGenerique(Naissance::class, $id, 'agent.demandes.naissance.index', 'naissance');

            return redirect()->route('agent.demandes.naissance.index')->with('error', 'Demande introuvable.'); 
        }
    }

    public function RecupererDeces($id)
    {
        $deces = Deces::find($id);
        if ($deces) {
            return $this->traiterDemandeGenerique(Deces::class, $id,'agent.demandes.deces.index' , 'décès');
        }

        return redirect()->route('agent.demandes.deces.index')->with('error', 'Demande introuvable.');
    }

    public function RecupererMariage($id)
    {
        return $this->traiterDemandeGenerique(Mariage::class, $id,'agent.demandes.wedding.index' , 'mariage');
    }
}
