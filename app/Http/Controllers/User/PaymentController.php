<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Deces;
use App\Models\Mariage;
use App\Models\Naissance;
use App\Models\Paiement;
use App\Models\User;
use App\Services\WaveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        $reference = $request->query('reference');

        Log::info("Page de succès paiement atteinte. Référence: {$reference}");

        if (!$reference) {
            return redirect()->route('user.login')->with('error', 'Référence de paiement manquante.');
        }

        // Identifier le type de demande à partir du préfixe de la référence
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
            Log::error("Demande introuvable pour la référence: {$reference}");
            return redirect()->route('user.login')->with('error', 'Référence de demande invalide.');
        }

        Log::info("Demande trouvée: {$type} ID={$demande->id}, user_id={$demande->user_id}");

        // Vérifier si le paiement est déjà enregistré (par le webhook)
        $paiement = Paiement::where("{$type}_id", $demande->id)->first();

        // Fallback: Si pas encore enregistré, on le crée manuellement
        if (!$paiement) {
            Log::info("Webhook non encore reçu pour {$reference}. Enregistrement manuel du paiement.");
            
            $montantTimbre = (float) ($demande->montant_timbre ?? 0);
            $quantite = (int) ($demande->quantite ?? 1);
            $montantLivraison = (float) ($demande->montant_livraison ?? 0);
            $totalAmount = ($montantTimbre * $quantite) + $montantLivraison;

            try {
                $paiement = Paiement::create([
                    'user_id' => $demande->user_id,
                    'transaction_id' => $demande->reference,
                    'operator_id' => 'WAVE',
                    'montant' => $totalAmount,
                    'currency' => 'XOF',
                    'status' => 'ACCEPTED',
                    'paid_at' => now(),
                    "{$type}_id" => $demande->id,
                ]);
                Log::info("Paiement enregistré avec succès. ID: {$paiement->id}");
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'enregistrement du paiement: " . $e->getMessage());
            }

            // Mettre à jour l'état de la demande
            $demande->etat = 'en attente';
            if ($demande->choix_option === 'livraison') {
                $demande->statut_livraison = 'en attente';
            }
            $demande->save();
        } else {
            Log::info("Paiement déjà enregistré (webhook reçu). Paiement ID: {$paiement->id}");
        }

        return view('user.payment.success', [
            'demande' => $demande,
            'type' => $type,
            'reference' => $reference
        ]);
    }

    public function cancel(Request $request)
    {
        $reference = $request->query('reference');
        
        Log::info("Page d'annulation paiement atteinte. Référence: {$reference}");

        return view('user.payment.cancel', [
            'reference' => $reference
        ]);
    }
}
