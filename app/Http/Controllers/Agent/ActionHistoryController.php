<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\ActionHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionHistoryController extends Controller
{
    /**
     * Afficher l'historique des actions de l'agent
     */
    public function index(Request $request)
    {
        $agent = Auth::guard('agent')->user();
        
        $query = ActionHistory::where('agent_id', $agent->id)
            ->orderBy('created_at', 'desc');
        
        // Filtres
        if ($request->filled('type')) {
            $query->where('demande_type', $request->type);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }
        
        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%' . $request->reference . '%');
        }
        
        $actions = $query->paginate(20);
        
        // Statistiques
        $stats = [
            'total' => ActionHistory::where('agent_id', $agent->id)->count(),
            'rejets' => ActionHistory::where('agent_id', $agent->id)->where('action', 'rejet')->count(),
            'changements' => ActionHistory::where('agent_id', $agent->id)->where('action', 'changement_etat')->count(),
            'recuperations' => ActionHistory::where('agent_id', $agent->id)->where('action', 'recuperation')->count(),
        ];
        
        return view('agent.history.actions.index', compact('actions', 'stats'));
    }
}
