<?php

namespace App\Http\Controllers\Mairie;

use App\Http\Controllers\Controller;
use App\Models\Rendezvous;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use App\Notifications\GeneralPushNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\YellikaSmsService;

class MairieRendezVousController extends Controller
{
    public function index()
    {
        $rendezvous = Rendezvous::with('user')->get(); // Optimisation: eager loading
        return view('mairie.rendezvous.index', compact('rendezvous'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'date_mariage_souhaitee' => 'required|date',
            'heure_souhaitee' => 'required',
        ]);

        $rendezvous = Rendezvous::with('user')->findOrFail($id);

        // Sauvegarder l'ancien statut
        $ancienStatut = $rendezvous->statut ?? 'en attente';

        $rendezvous->date_mariage_souhaitee = $request->date_mariage_souhaitee;
        $rendezvous->heure_souhaitee = $request->heure_souhaitee;
        $rendezvous->statut = 'confirmé';
        $rendezvous->save();

        // =================================================================
        // CRÉATION DE NOTIFICATION WEB POUR L'UTILISATEUR
        // =================================================================
        // 1. Configurer Carbon en français
        Carbon::setLocale('fr');

        // 2. Formater la date (ex: lundi 24 novembre 2025)
        $dateLitterale = Carbon::parse($rendezvous->date_mariage_souhaitee)->translatedFormat('l d F Y');

        if ($rendezvous->user) {
            // Créer un message personnalisé pour la modification
            $message = "Votre rendez-vous de mariage (Réf: RDV-{$rendezvous->id}) a été reprogrammé au {$dateLitterale} à {$rendezvous->heure_souhaitee}.";

            UserNotification::create([
                'user_id' => $rendezvous->user->id,
                'type' => 'rendezvous',
                'demande_id' => $rendezvous->id,
                'reference' => 'RDV-' . $rendezvous->id,
                'ancien_statut' => $ancienStatut,
                'nouveau_statut' => 'modifié',
                'message' => $message,
                'is_read' => false,
            ]);
        }

        // =================================================================
        // <-- NOTIFICATION PUSH : MODIFICATION AVEC DATE FORMATÉE
        // =================================================================
        $this->triggerNotification(
            $rendezvous,
            'Rendez-vous modifié',
            "La date de votre rendez-vous a été modifiée au " . $dateLitterale . " à " . $rendezvous->heure_souhaitee
        );

        // ✅ SMS de notification — fallback fiable même si le push FCM échoue
        $this->sendSms(
            $rendezvous,
            "Bonjour {nom}, votre rendez-vous de mariage (Réf: RDV-{$rendezvous->id}) a été reprogrammé au {$dateLitterale} à {$rendezvous->heure_souhaitee} par la mairie du plateau."
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
            $ancienStatut = $rendezvous->statut ?? 'en attente';
            $rendezvous->statut = 'confirmé';
            $rendezvous->save();

            // =================================================================
            // CRÉATION DE NOTIFICATION WEB POUR L'UTILISATEUR
            // =================================================================
            if ($rendezvous->user) {
                UserNotification::notifyStatusChange(
                    $rendezvous->user->id,
                    'rendezvous',
                    $rendezvous->id,
                    'RDV-' . $rendezvous->id,
                    $ancienStatut,
                    'confirmé'
                );
            }

            // =================================================================
            // <-- NOTIFICATION PUSH : CONFIRMATION
            // =================================================================
            $this->triggerNotification(
                $rendezvous,
                'Rendez-vous confirmé',
                "Votre demande de rendez-vous a été acceptée par la mairie."
            );

            // ✅ SMS de notification — fallback fiable même si le push FCM échoue
            $this->sendSms(
                $rendezvous,
                "Bonjour {nom}, votre rendez-vous de mariage (Réf: RDV-{$rendezvous->id}) a été confirmé par la mairie du plateau."
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
            $ancienStatut = $rendezvous->statut ?? 'en attente';
            $rendezvous->statut = 'annulé';
            $rendezvous->save();

            // =================================================================
            // CRÉATION DE NOTIFICATION WEB POUR L'UTILISATEUR
            // =================================================================
            if ($rendezvous->user) {
                UserNotification::notifyStatusChange(
                    $rendezvous->user->id,
                    'rendezvous',
                    $rendezvous->id,
                    'RDV-' . $rendezvous->id,
                    $ancienStatut,
                    'annulé'
                );
            }

            // =================================================================
            // <-- NOTIFICATION PUSH : ANNULATION
            // =================================================================
            $this->triggerNotification(
                $rendezvous,
                'Rendez-vous annulé',
                "Votre rendez-vous de mariage a été annulé."
            );

            // ✅ SMS de notification
            $this->sendSms(
                $rendezvous,
                "Bonjour {nom}, votre rendez-vous de mariage (Réf: RDV-{$rendezvous->id}) a été annulé par la mairie du plateau."
            );

            return response()->json(['success' => true, 'message' => 'Rendez-vous annulé avec succès']);
        } catch (\Exception $e) {
            Log::error("Erreur annulation RDV: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'annulation du rendez-vous']);
        }
    }

    /**
     * Helper pour préparer et envoyer la notification push + DB.
     * Logs détaillés pour diagnostiquer pourquoi le mobile ne reçoit pas la notif.
     */
    private function triggerNotification($rendezvous, string $title, string $body): void
    {
        try {
            $rendezvous->load('user');
            $user = $rendezvous->user;

            if (!$user) {
                Log::warning("RDV #{$rendezvous->id} : aucun user associé — push impossible.");
                return;
            }

            // Diagnostic : pourquoi la push notification pourrait ne pas arriver
            Log::info("RDV #{$rendezvous->id} push notify", [
                'user_id'                => $user->id,
                'has_push_token'         => !empty($user->push_notification),
                'push_token_preview'     => $user->push_notification ? substr($user->push_notification, 0, 20) . '...' : null,
                'title'                  => $title,
            ]);

            $user->notify(new GeneralPushNotification(
                $title,
                $body,
                ['type' => 'rendezvous', 'id' => (string) $rendezvous->id, 'url' => 'plateauapps://rendez-vous-details?id=' . $rendezvous->id]
            ));

            if (empty($user->push_notification)) {
                Log::warning("RDV #{$rendezvous->id} : push notification skipped — token FCM manquant pour user #{$user->id}. Seul le SMS arrivera.");
            }

        } catch (\Exception $e) {
            Log::error("Erreur notification RDV #{$rendezvous->id}: " . $e->getMessage());
        }
    }

    /**
     * Envoie un SMS au user du rendez-vous.
     * Le placeholder {nom} dans $messageTemplate est remplacé par le nom de l'utilisateur.
     */
    private function sendSms($rendezvous, string $messageTemplate): void
    {
        try {
            if (!$rendezvous->user) {
                $rendezvous->load('user');
            }
            $user = $rendezvous->user;
            if (!$user) {
                Log::warning("RDV #{$rendezvous->id} : aucun user — SMS impossible.");
                return;
            }

            $phoneNumber = $user->indicatif . $user->contact;
            $message = str_replace('{nom}', $user->name ?? 'Madame/Monsieur', $messageTemplate);

            $yellikaSmsService = app(YellikaSmsService::class);
            $yellikaSmsService->sendSms($phoneNumber, $message);

            Log::info("RDV #{$rendezvous->id} : SMS envoyé à {$phoneNumber}");

        } catch (\Exception $e) {
            Log::error("Erreur SMS RDV #{$rendezvous->id}: " . $e->getMessage());
        }
    }
}
