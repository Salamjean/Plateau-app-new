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

            // SMS notification
            if ($rendezvous->user) {
                $user = $rendezvous->user;
                $phoneNumber = $user->indicatif . $user->contact;
                $message = "Bonjour {$user->name}, votre rendez-vous de mariage (Réf: RDV-{$rendezvous->id}) a été annulé par la mairie.";
                try {
                    $yellikaSmsService = app(YellikaSmsService::class);
                    $yellikaSmsService->sendSms($phoneNumber, $message);
                } catch (\Exception $e) {
                    Log::error("Erreur lors de l'envoi du SMS d'annulation (rendezvous) : " . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'message' => 'Rendez-vous annulé avec succès']);
        } catch (\Exception $e) {
            Log::error("Erreur annulation RDV: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de l\'annulation du rendez-vous']);
        }
    }

    /**
     * Helper pour préparer et envoyer la notification push + DB
     */
    private function triggerNotification($rendezvous, string $title, string $body): void
    {
        try {
            $rendezvous->load('user');
            $user = $rendezvous->user;

            if ($user) {
                $user->notify(new GeneralPushNotification(
                    $title,
                    $body,
                    ['type' => 'rendezvous', 'id' => (string) $rendezvous->id, 'url' => 'plateauapps://rendez-vous-details?id=' . $rendezvous->id]
                ));
            }
        } catch (\Exception $e) {
            Log::error("Erreur notification RDV #{$rendezvous->id}: " . $e->getMessage());
        }
    }
}
