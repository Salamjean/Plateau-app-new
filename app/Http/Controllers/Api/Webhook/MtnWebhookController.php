<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Paiement;
use App\Models\Naissance;
use App\Models\Mariage;
use App\Models\Deces;

class MtnWebhookController extends Controller
{
    /**
     * Handle MTN Collection Webhook for RequestToPay.
     * MTN will send a PUT or POST request here with the transaction details.
     */
    public function handle(Request $request)
    {
        Log::info('MTN Webhook received: ' . json_encode($request->all()));

        // MTN sends the reference ID, status, and other transaction details
        $statusInfo = $request->all();
        
        $mtnRef = $statusInfo['referenceId'] ?? null;
        $status = $statusInfo['status'] ?? null;
        $externalId = $statusInfo['externalId'] ?? null; // This is our internal reference (e.g. AN...)

        if (!$mtnRef || !$status || !$externalId) {
            Log::warning('MTN Webhook missing essential data');
            return response()->json(['message' => 'Missing data'], 400);
        }

        if ($status === 'SUCCESSFUL') {
            Log::info("MTN Webhook: Transaction $externalId was successful");

            // Process payment completion
            $this->processSuccessfulPayment($externalId, $statusInfo);

        } elseif ($status === 'FAILED') {
            Log::warning("MTN Webhook: Transaction $externalId failed");
            // Optionally handle failed transaction logic here based on externalId
        }

        // Always return 200 OK so MTN knows we received the webhook
        return response()->json(['message' => 'Processed'], 200);
    }

    private function processSuccessfulPayment($reference, $statusInfo)
    {
        // Prevent duplicate processing
        $paiement = Paiement::where('transaction_id', $reference)->where('operator_id', 'MTN')->first();
        if ($paiement) {
            Log::info("MTN Webhook: Payment for $reference already processed");
            return;
        }

        // Determine request type
        $type = null;
        $demande = null;
        if (str_starts_with($reference, 'AN')) {
            $type = 'naissance';
            $demande = Naissance::where('reference', $reference)->first();
        } elseif (str_starts_with($reference, 'AM')) {
            $type = 'mariage';
            $demande = Mariage::where('reference', $reference)->first();
        } elseif (str_starts_with($reference, 'AD')) {
            $type = 'deces';
            $demande = Deces::where('reference', $reference)->first();
        }

        if ($demande) {
            // MTN returns amount and currency
            $amount = isset($statusInfo['amount']) ? (float)$statusInfo['amount'] : 0;
            
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

            Log::info("MTN Webhook: Successfully processed payment for $reference ($type)");
        } else {
            Log::error("MTN Webhook: Could not find model for reference $reference");
        }
    }
}
