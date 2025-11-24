<?php

namespace App\Http\Controllers\Mairie;

use App\Http\Controllers\Controller;
use App\Models\Rendezvous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // <-- AJOUT IMPORTANT
use Illuminate\Support\Facades\Log;  // <-- AJOUT IMPORTANT
use Carbon\Carbon;
class MairieRendezVousController extends Controller
{
    public function index(){
        $rendezvous = Rendezvous::with('user')->get(); // Optimisation: eager loading
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
 // =================================================================
        // <-- NOTIFICATION PUSH : MODIFICATION AVEC DATE FORMATÉE
        // =================================================================
        
        // 1. Configurer Carbon en français
        Carbon::setLocale('fr');
        
        // 2. Formater la date (ex: lundi 24 novembre 2025)
        $dateLitterale = Carbon::parse($rendezvous->date_mariage_souhaitee)->translatedFormat('l d F Y');

        $this->triggerNotification(
            $rendezvous, 
            'Rendez-vous modifié', 
            "La date de votre rendez-vous a été modifiée au " . $dateLitterale . " à " . $rendezvous->heure_souhaitee
        );

        return redirect()->route('mairie.rendezvous.index')->with('success', 'Rendez-vous modifié avec succès');
    }


    public function confirmation($id)
    {
        $rendezvous = Rendezvous::findOrFail($id);
        return view('mairie.rendezvous.confirmation', compact('rendezvous'));
    }

    public function confirm(Request $request, $id)
    {
        try {
            $rendezvous = Rendezvous::findOrFail($id);
            
            // Mettre à jour le statut du rendez-vous
            $rendezvous->statut = 'confirmé';
            $rendezvous->save();
            
            // =================================================================
            // <-- NOTIFICATION PUSH : CONFIRMATION
            // =================================================================
            $this->triggerNotification(
                $rendezvous, 
                'Rendez-vous confirmé', 
                "Votre demande de rendez-vous a été acceptée par la mairie."
            );
            
            return response()->json(['success' => true, 'message' => 'Rendez-vous confirmé avec succès']);
        } catch (\Exception $e) {
            Log::error("Erreur confirmation RDV: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la confirmation du rendez-vous']);
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $rendezvous = Rendezvous::findOrFail($id);
            
            // Mettre à jour le statut du rendez-vous
            $rendezvous->statut = 'annulé';
            $rendezvous->save();
            
            // =================================================================
            // <-- NOTIFICATION PUSH : ANNULATION
            // =================================================================
            $this->triggerNotification(
                $rendezvous, 
                'Rendez-vous annulé', 
                "Votre rendez-vous de mariage a été annulé."
            );
            
            return response()->json(['success' => true, 'message' => 'Rendez-vous annulé avec succès']);
        } catch (\Exception $e) {
            Log::error("Erreur annulation RDV: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'annulation du rendez-vous']);
        }
    }

    /**
     * Helper pour préparer et envoyer la notification
     */
    private function triggerNotification($rendezvous, $title, $body)
    {
        try {
            // Charger la relation user si elle n'est pas déjà chargée
            $rendezvous->load('user');
            $user = $rendezvous->user;

            if ($user && !empty($user->push_notification)) {
                // Adapter les data selon vos besoins (ici lien vers mes RDV)
                $data = ['url' => 'plateauapps://rendez-vous-details?id=' . $rendezvous->id]; 
                
                $this->sendPushNotification($user->push_notification, $title, $body, $data);
            }
        } catch (\Exception $e) {
            Log::error("Erreur préparation notif pour RDV #{$rendezvous->id}: " . $e->getMessage());
        }
    }

    // =================================================================
    // <-- FONCTION D'ENVOI (RECUPERATION CONTROLLER)
    // =================================================================
    private function sendPushNotification(string $userToken, string $title, string $body, array $data = [])
    {
        if (empty($userToken)) {
            Log::warning('Tentative d\'envoi de notif push sans token utilisateur.');
            return;
        }

        $payload = [
            'to' => $userToken,
            'sound' => 'default',
            'title' => $title,
            'body' => $body,
            'data' => (object) $data,
        ];

        try {
            $response = Http::withoutVerifying()
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate',
            ])->post('https://exp.host/--/api/v2/push/send', $payload);

            if ($response->failed()) {
                Log::error('Échec envoi notification Expo: ' . $response->body());
            } else {
                Log::info('Notification Expo envoyée: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Exception lors de lenvoi de notification Expo: ' . $e->getMessage());
        }
    }
}