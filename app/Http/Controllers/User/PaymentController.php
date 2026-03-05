<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\Paiement;
use App\Models\User;
use App\Services\WaveService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $waveService;

    public function __construct(WaveService $waveService)
    {
        $this->waveService = $waveService;
    }

    public function success(Request $request)
    {
        $reference = $request->query('reference');
        $id = $request->query('id'); // Session ID from Wave

        // Identifier le type de demande
        $demande = null;
        $type = null;

        if (str_starts_with($reference, 'AN')) {
            $demande = Naissance::where('reference', $reference)->first();
            $type = 'naissance';
        } elseif (str_starts_with($reference, 'AM')) {
            $demande = Mariage::where('reference', $reference)->first();
            $type = 'mariage';
        } elseif (str_starts_with($reference, 'AD')) {
            $demande = Deces::where('reference', $reference)->first();
            $type = 'deces';
        }

        if (!$demande) {
            return redirect()->route('user.dashboard')->with('error', 'Référence de demande invalide.');
        }

        // Vérifier si le paiement est déjà enregistré (par le webhook)
        $paiement = Paiement::where("{$type}_id", $demande->id)->first();

        // Fallback: Si pas encore enregistré, on le crée manuellement
        // Puisqu'on est sur la page de succès, Wave a validé le paiement
        if (!$paiement) {
            Log::info("Succès paiement: Webhook non encore reçu pour {$reference}. Enregistrement manuel.");
            
            $totalAmount = ($demande->montant_timbre * $demande->quantite) + ($demande->montant_livraison ?? 0);

            Paiement::create([
                'user_id' => $demande->user_id,
                'transaction_id' => $demande->reference,
                'operator_id' => 'WAVE_MANUAL_CHECK',
                'montant' => $totalAmount,
                'currency' => 'XOF',
                'status' => 'ACCEPTED',
                'paid_at' => now(),
                "{$type}_id" => $demande->id,
            ]);

            // Mettre à jour l'état de la demande
            $demande->etat = 'en attente';
            $demande->save();

            // Envoyer les notifications si nécessaire (optionnel car le webhook pourrait aussi arriver)
            // Mais pour une meilleure UX locale, on le fait ici.
            try {
                $user = User::find($demande->user_id);
                if ($user) {
                    $this->sendManualNotifications($user, $demande, $type);
                }
            } catch (\Exception $e) {
                Log::error("Erreur notifications manuelles: " . $e->getMessage());
            }
        }

        return view('user.payment.success', [
            'demande' => $demande,
            'type' => $type,
            'reference' => $reference
        ]);
    }

    private function sendManualNotifications($user, $demande, $type)
    {
        // On réutilise la même logique que le webhook
        // (Copie simplifiée ici ou appel à une méthode partagée)
        $typeLabel = ucfirst($type);
        $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de {$type} a été confirmé. Référence : {$demande->reference}.";
        
        try {
            $yellikaSmsService = app(\App\Services\YellikaSmsService::class);
            $yellikaSmsService->sendSms($user->indicatif . $user->contact, $message);
        } catch (\Exception $e) {}

        $notificationClass = match($type) {
            'naissance' => \App\Notifications\DemandeNaissanceConfirmationNotification::class,
            'mariage' => \App\Notifications\DemandeMariageConfirmationNotification::class,
            'deces' => \App\Notifications\DemandeDecesConfirmationNotification::class,
            default => null,
        };

        if ($notificationClass) {
            \Illuminate\Support\Facades\Notification::send($user, new $notificationClass($user, $demande));
        }
    }

    public function cancel(Request $request)
    {
        $reference = $request->query('reference');
        
        return view('user.payment.cancel', [
            'reference' => $reference
        ]);
    }
}
