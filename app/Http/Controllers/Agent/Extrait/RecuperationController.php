<?php

namespace App\Http\Controllers\Agent\Extrait;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // <-- AJOUT
use Illuminate\Support\Facades\Log;  // <-- AJOUT

class RecuperationController extends Controller
{
    /**
     * Compte les demandes "actives" (ni terminées, ni rejetées) de l'agent.
     */
    private function compterDemandesEnAttente($agentId)
    {
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

        // Sauvegarder l'ancien état
        $ancienEtat = $demande->etat ?? 'en attente';
        
        $demande->is_read = true;
        $demande->agent_id = $agent->id;
        $demande->etat = 'réçu';
        $demande->save();

        // =================================================================
        // CRÉATION DE NOTIFICATION WEB POUR L'UTILISATEUR
        // =================================================================
        $demande->load('user');
        $user = $demande->user;
        
        // Mapper le nom du modèle vers le type de notification
        $typeMapping = [
            'naissance' => 'naissance',
            'décès' => 'deces',
            'mariage' => 'mariage',
        ];
        $notificationType = $typeMapping[$modelName] ?? $modelName;
        
        if ($user) {
            UserNotification::notifyStatusChange(
                $user->id,
                $notificationType,
                $demande->id,
                $demande->reference,
                $ancienEtat,
                'réçu'
            );
        }

        // =================================================================
        // <-- NOUVEAU : BLOC D'ENVOI DE NOTIFICATION PUSH
        // =================================================================
        if ($user && !empty($user->push_notification)) {
            $title = 'Demande reçue';
            $body = "Votre demande de {$modelName} est bien reçue par la mairie et est en cours de traitement.";
            $data = ['url' => 'plateauapps://demande?reference=' . $demande->reference];
            
            $this->sendPushNotification($user->push_notification, $title, $body, $data);
        }
        // =================================================================
        // FIN DU BLOC DE NOTIFICATION
        // =================================================================

        return redirect()->route($successRoute)->with('success', "Demande de {$modelName} récupérée avec succès.");
    }

    public function RecupererNaissance($id)
    {
        return $this->traiterDemandeGenerique(Naissance::class, $id, 'agent.demandes.naissance.index', 'naissance');
    }

    public function RecupererDeces($id)
    {
        return $this->traiterDemandeGenerique(Deces::class, $id,'agent.demandes.deces.index' , 'décès');
    }

    public function RecupererMariage($id)
    {
        return $this->traiterDemandeGenerique(Mariage::class, $id,'agent.demandes.wedding.index' , 'mariage');
    }

    // =================================================================
    // <-- NOUVEAU : FONCTION PRIVÉE POUR ENVOYER LA NOTIFICATION
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
            $response = Http::withoutVerifying() // Utilise la solution pour localhost
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