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

    public function mtnWaiting(Request $request)
    {
        $reference = $request->query('reference');
        $type = $request->query('type');
        
        $mtnRef = session('mtn_ref_' . $reference);

        if (!$mtnRef) {
            return redirect()->route('user.extrait.index')->with('error', 'Référence MTN introuvable ou session expirée.');
        }

        return view('user.payment.mtn-waiting', [
            'reference' => $reference,
            'type' => $type,
            'mtnRef' => $mtnRef
        ]);
    }

    public function mtnCheck(Request $request)
    {
        $reference = $request->input('reference');
        $type = $request->input('type');
        $mtnRef = $request->input('mtn_ref');

        if (!$mtnRef) {
            return response()->json(['status' => 'ERROR', 'message' => 'No MTN ID provided']);
        }

        $mtnService = new \App\Services\MtnService();
        $statusInfo = $mtnService->getTransactionStatus($mtnRef);

        if (!$statusInfo) {
            return response()->json(['status' => 'PENDING', 'message' => 'Pending or API disconnected.']);
        }

        $status = $statusInfo['status'] ?? 'PENDING';

        if ($status === 'SUCCESSFUL') {
            // Check if already processed
            $paiement = Paiement::where('transaction_id', $reference)->where('operator_id', 'MTN')->first();
            if (!$paiement) {
                // Find demande 
                $demande = null;
                if ($type === 'naissance') {
                    $demande = Naissance::where('reference', $reference)->first();
                } elseif ($type === 'mariage') {
                    $demande = Mariage::where('reference', $reference)->first();
                } elseif ($type === 'deces') {
                    $demande = Deces::where('reference', $reference)->first();
                }

                if ($demande) {
                    $montantTimbre = (float) ($demande->montant_timbre ?? 0);
                    $quantite = (int) ($demande->quantite ?? 1);
                    $montantLivraison = (float) ($demande->montant_livraison ?? 0);
                    // The timbre total is already stored in montant_timbre if logic is correctly updated
                    // However, we just use the API's amount if available from $statusInfo['amount']
                    $amount = isset($statusInfo['amount']) ? (float)$statusInfo['amount'] : (($montantTimbre * $quantite) + $montantLivraison);

                    Paiement::create([
                        'user_id' => $demande->user_id,
                        'transaction_id' => $reference,
                        'operator_id' => 'MTN',
                        'montant' => $amount,
                        'currency' => $statusInfo['currency'] ?? 'XOF',
                        'status' => 'ACCEPTED',
                        'paid_at' => now(),
                        "{$type}_id" => $demande->id,
                    ]);

                    $demande->etat = 'en attente';
                    if ($demande->choix_option === 'livraison') {
                        $demande->statut_livraison = 'en attente';
                    }
                    $demande->save();
                }
            }
            return response()->json(['status' => 'SUCCESSFUL', 'redirect' => route('payment.success', ['reference' => $reference, 'type' => $type])]);
        } elseif ($status === 'FAILED') {
            return response()->json(['status' => 'FAILED', 'redirect' => route('payment.cancel', ['reference' => $reference, 'type' => $type])]);
        }

        return response()->json(['status' => $status]); // PENDING
    }
}
