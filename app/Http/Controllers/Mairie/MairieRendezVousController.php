<?php

namespace App\Http\Controllers\Mairie;

use App\Http\Controllers\Controller;
use App\Models\Rendezvous;
use Illuminate\Http\Request;

class MairieRendezVousController extends Controller
{
    public function index(){
        $rendezvous = Rendezvous::get();
        return view('mairie.rendezvous.index', compact('rendezvous'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date_mariage_souhaitee' => 'required|date',
            'heure_souhaitee' => 'required',
        ]);

        $rendezvous = Rendezvous::findOrFail($id);
        $rendezvous->date_mariage_souhaitee = $request->date_mariage_souhaitee;
        $rendezvous->heure_souhaitee = $request->heure_souhaitee;
        $rendezvous->statut = 'confirmé';
        $rendezvous->save();

        return redirect()->route('mairie.rendezvous.index')->with('success', 'Rendez-vous modifié avec succès');
    }

    public function confirmation($id)
    {
        $rendezvous = RendezVous::findOrFail($id);
        return view('mairie.rendezvous.confirmation', compact('rendezvous'));
    }

    public function confirm(Request $request, $id)
    {
        try {
            $rendezvous = RendezVous::findOrFail($id);
            
            // Mettre à jour le statut du rendez-vous
            $rendezvous->statut = 'confirmé';
            $rendezvous->save();
            
            return response()->json(['success' => true, 'message' => 'Rendez-vous confirmé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la confirmation du rendez-vous']);
        }
        }

}
