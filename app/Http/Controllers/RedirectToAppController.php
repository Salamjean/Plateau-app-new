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
            'headers' => $request->headers->all()
        ]);

        // Si c'est un POST (venant de CinetPay), traiter et rediriger en GET
        if ($request->isMethod('post')) {
            $transactionId = $request->input('cpm_trans_id') 
                          ?? $request->input('transaction_id') 
                          ?? $request->input('transactionId');
            
            Log::info('POST reçu de CinetPay, redirection vers GET', [
                'transactionId' => $transactionId
            ]);

            // Rediriger vers la même URL en GET
            return redirect()->route('deces.redirect_to_app', [
                'transactionId' => $transactionId,
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