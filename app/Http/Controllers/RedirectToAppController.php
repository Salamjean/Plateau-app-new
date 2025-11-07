<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RedirectToAppController extends Controller
{
    public function show(Request $request)
    {
        // Log pour debug
        Log::info('RedirectToAppController - Méthode: ' . $request->method(), [
            'all_inputs' => $request->all(),
            'query_params' => $request->query(),
            'headers' => $request->headers->all(),
            'full_url' => $request->fullUrl()
        ]);

        // Si c'est un POST (venant de CinetPay), traiter et rediriger en GET
        if ($request->isMethod('post')) {
            $transactionId = $request->input('cpm_trans_id') 
                          ?? $request->input('transaction_id') 
                          ?? $request->input('transactionId');
            
            Log::info('POST reçu de CinetPay, redirection vers GET', [
                'transactionId' => $transactionId
            ]);

            // Extraire la référence originale si transactionId contient un suffixe
            $originalTransactionId = $transactionId;
            if (strpos($transactionId, '_') !== false) {
                $parts = explode('_', $transactionId);
                $originalTransactionId = $parts[0];
            }

            // Déterminer le type de demande basé sur l'URL
            $currentUrl = $request->fullUrl();
            $routeName = 'deces.redirect_to_app'; // par défaut
            
            if (strpos($currentUrl, '/mariage/') !== false) {
                $routeName = 'mariage.redirect_to_app';
            } elseif (strpos($currentUrl, '/naissance/') !== false) {
                $routeName = 'naissance.redirect_to_app';
            }

            // Rediriger vers la même URL en GET
            return redirect()->route($routeName, [
                'transactionId' => $originalTransactionId,
                'cinetpay' => 'true'
            ]);
        }

        // Si c'est un GET, afficher la vue normale
        $transactionId = $request->query('transactionId') 
                      ?? $request->query('transaction_id');
        
        $isCancel = $request->query('cancel') === '1';
        $cinetpayStatus = $request->query('cinetpay') ?? 'true';

        return view('payments.redirect_to_app', [
            'transactionId' => $transactionId,
            'isCancel' => $isCancel,
            'cinetpayStatus' => $cinetpayStatus
        ]);
    }
}