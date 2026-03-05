import re

file_path = "app/Http/Controllers/Api/Utilisateurs/DemandeDecesController.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Replace generateCinetPayLink in store
store_old = """            // 6. Générer le lien de paiement en utilisant la méthode refactorisée
            $paymentLinkResult = $this->generateCinetPayLink($deces);

            // 7. Gérer l'échec de la génération de lien
            if (!$paymentLinkResult['success']) {
                // La demande est créée, mais le lien a échoué. L'utilisateur pourra réessayer.
                return response()->json([
                    'success' => false,
                    'message' => 'Demande créée, mais échec de la génération du lien de paiement. Veuillez réessayer.',
                    'error_details' => $paymentLinkResult['error_details']
                ], 500);
            }

            // 8. Succès ! Construire la réponse
            $cinetpayResponseData = $paymentLinkResult['cinetpay_data'];

            return response()->json([
                'success' => true,
                'message' => 'Demande créée. Utilisez le payment_url pour payer.',
                'requires_payment' => true,

                'payment_details' => [
                    'payment_url' => $cinetpayResponseData['payment_url'],
                    'payment_token' => $cinetpayResponseData['payment_token'],
                    'transaction_id' => $paymentLinkResult['generated_transaction_id'], // Utiliser l'ID généré
                    'mode' => 'PRODUCTION',
                    'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'],
                    'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'],
                    'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'],
                    'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'],
                ],"""

store_new = r"""            // 6. Générer le lien de paiement avec Wave
            $paymentLinkResult = $this->generateWaveLink($deces);

            // 7. Gérer l'échec de la génération de lien
            if (!$paymentLinkResult['success']) {
                // La demande est créée, mais le lien a échoué. L'utilisateur pourra réessayer.
                return response()->json([
                    'success' => false,
                    'message' => 'Demande créée, mais échec de la génération du lien de paiement. Veuillez réessayer.',
                    'error_details' => $paymentLinkResult['error_details']
                ], 500);
            }

            // 8. Succès ! Construire la réponse
            return response()->json([
                'success' => true,
                'message' => 'Demande créée. Utilisez le payment_url pour payer.',
                'requires_payment' => true,

                'payment_details' => [
                    'payment_url' => $paymentLinkResult['wave_launch_url'],
                    'transaction_id' => $paymentLinkResult['generated_transaction_id'], // Utiliser l'ID généré
                    'mode' => 'PRODUCTION',
                    'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'],
                    'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'],
                    'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'],
                    'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'],
                ],"""

content = content.replace(store_old, store_new)

# 2. Replace generateCinetPayLink definition
cinetpay_def_old_pattern = r"    /\*\*\n     \* Génère un nouveau lien de paiement CinetPay.*?(?=    // --------------------------------------------------------------------\n    // NOUVELLE MÉTHODE PUBLIQUE)"

wave_def_new = r"""    /**
     * Génère un nouveau lien de paiement Wave pour une demande de décès existante.
     */
    private function generateWaveLink(Deces $deces): array
    {
        try {
            // 1. Préparer les URLs
            $baseUrl = config('app.url');
            $returnUrl = "plateauapps://payment?wave=true&transactionId={$deces->reference}";
            $cancelUrl = "plateauapps://payment?wave=false&transactionId={$deces->reference}";
            $fallbackReturnUrl = $baseUrl . "/deces/paiement/redirect-to-app?transactionId=" . urlencode($deces->reference);
            $fallbackCancelUrl = $baseUrl . "/deces/paiement/redirect-to-app?cancel=1&transactionId=" . urlencode($deces->reference);

            // 2. Calculer le montant
            $cout_total_timbres = (float) $deces->montant_timbre * (int) $deces->quantite;
            $totalAmount = $cout_total_timbres + (float) $deces->montant_livraison;

            // 3. Infos Wave
            $waveApiKey = env('WAVE_API_KEY', 'wave_ci_prod_ztyw2awlmgOaaDtMK_U1w_zZ24qFXoDzH5ZpxUoJNP_-jR72suwolQfukDai0-ilUaorkOMWPmTA8Poi8mb2bq1AliY4USX4uw');

            // 4. GÉNÉRER UN NOUVEAU TRANSACTION_ID UNIQUE
            $waveTransactionId = $deces->reference . '_' . time();

            // 5. Data du paiement
            $paymentData = [
                'amount' => (string) $totalAmount,
                'currency' => 'XOF',
                'error_url' => $fallbackCancelUrl,
                'success_url' => $fallbackReturnUrl,
                'client_reference' => $waveTransactionId
            ];

            // 6. Appel API Wave
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$waveApiKey}",
                'Content-Type' => 'application/json'
            ])->withoutVerifying()->post('https://api.wave.com/v1/checkout/sessions', $paymentData);

            // 7. Gérer l'échec
            if ($response->failed()) {
                Log::error('Erreur Wave (Génération lien): ' . $response->body(), ['transaction_id' => $deces->reference]);
                return [
                    'success' => false,
                    'message' => 'Échec de la génération du lien de paiement.',
                    'error_details' => $response->json() ?? $response->body()
                ];
            }

            // 8. Succès
            $responseData = $response->json();
            return [
                'success' => true,
                'wave_launch_url' => $responseData['wave_launch_url'],
                'generated_transaction_id' => $waveTransactionId,
                'return_url_deep_link' => $returnUrl,
                'cancel_url_deep_link' => $cancelUrl,
                'return_url_web_fallback' => $fallbackReturnUrl,
                'cancel_url_web_fallback' => $fallbackCancelUrl,
            ];

        } catch (\Exception $e) {
            Log::error('Exception in generateWaveLink: ' . $e->getMessage(), ['reference' => $deces->reference]);
            return [
                'success' => false,
                'message' => 'Erreur interne lors de la génération du lien: ' . $e->getMessage(),
                'error_details' => null
            ];
        }
    }

"""

content = re.sub(cinetpay_def_old_pattern, lambda m: wave_def_new, content, flags=re.DOTALL)

# 3. Replace in retryPayment
retry_old = """            // 5. Générer le nouveau lien de paiement
            $paymentLinkResult = $this->generateCinetPayLink($deces);

            // 6. Gérer l'échec de la génération
            if (!$paymentLinkResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de la génération du nouveau lien de paiement.',
                    'error_details' => $paymentLinkResult['error_details']
                ], 500);
            }

            // 7. Succès ! Construire la réponse (similaire à 'store')
            $cinetpayResponseData = $paymentLinkResult['cinetpay_data'];

            return response()->json([
                'success' => true,
                'message' => 'Nouveau lien de paiement généré. Utilisez le payment_url pour payer.',
                'requires_payment' => true,

                'payment_details' => [
                    'payment_url' => $cinetpayResponseData['payment_url'],
                    'payment_token' => $cinetpayResponseData['payment_token'],
                    'transaction_id' => $paymentLinkResult['generated_transaction_id'],
                    'mode' => 'PRODUCTION',
                    'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'],
                    'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'],
                    'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'],
                    'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'],
                ],"""

retry_new = r"""            // 5. Générer le nouveau lien de paiement avec Wave
            $paymentLinkResult = $this->generateWaveLink($deces);

            // 6. Gérer l'échec de la génération
            if (!$paymentLinkResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Échec de la génération du nouveau lien de paiement.',
                    'error_details' => $paymentLinkResult['error_details']
                ], 500);
            }

            // 7. Succès ! Construire la réponse (similaire à 'store')
            return response()->json([
                'success' => true,
                'message' => 'Nouveau lien de paiement généré. Utilisez le payment_url pour payer.',
                'requires_payment' => true,

                'payment_details' => [
                    'payment_url' => $paymentLinkResult['wave_launch_url'],
                    'transaction_id' => $paymentLinkResult['generated_transaction_id'],
                    'mode' => 'PRODUCTION',
                    'return_url_deep_link' => $paymentLinkResult['return_url_deep_link'],
                    'cancel_url_deep_link' => $paymentLinkResult['cancel_url_deep_link'],
                    'return_url_web_fallback' => $paymentLinkResult['return_url_web_fallback'],
                    'cancel_url_web_fallback' => $paymentLinkResult['cancel_url_web_fallback'],
                ],"""

content = content.replace(retry_old, retry_new)

# 4. Handle Webhook
webhook_def_old_pattern = r"    public function handlePaymentNotification\(Request \$request\): JsonResponse\n    \{.*?\}\n\n\n    /\*\*"
webhook_new = r"""    public function handlePaymentNotification(Request $request): JsonResponse
    {
        Log::info('Webhook Wave Reçu (Deces)', ['headers' => $request->headers->all(), 'body' => $request->all()]);

        // Optionnel: Validation de la signature Wave (fortement recommandé en production)
        $waveSignature = $request->header('wave-signature');
        $waveWebhookSecret = env('WAVE_WEBHOOK_SECRET');
        
        if ($waveWebhookSecret && $waveSignature) {
            // Format du header: "t=1639081943, v1=942119ae..."
            $timestamp = null;
            $signatures = [];
            
            $parts = explode(',', (string) $waveSignature);
            foreach ($parts as $part) {
                list($prefix, $value) = array_pad(explode('=', trim($part), 2), 2, null);
                if ($prefix === 't') {
                    $timestamp = $value;
                } elseif ($prefix === 'v1') {
                    $signatures[] = $value;
                }
            }

            if ($timestamp) {
                $payload = $timestamp . $request->getContent();
                $expectedSignature = hash_hmac('sha256', $payload, $waveWebhookSecret);
                
                if (!in_array($expectedSignature, $signatures)) {
                    Log::warning('Webhook Wave: Signature invalide');
                    return response()->json(['success' => false, 'message' => 'Signature invalide'], 401);
                }
            }
        }

        $eventType = $request->input('type');
        
        if ($eventType !== 'checkout.session.completed' && $eventType !== 'checkout.session.payment_failed') {
            Log::info("Webhook Wave: Evénement ignoré ({$eventType})");
            return response()->json(['success' => true, 'message' => 'Evénement ignoré'], 200);
        }

        $checkoutData = $request->input('data');
        if (!$checkoutData) {
            return response()->json(['success' => false, 'message' => 'Données manquantes'], 400);
        }

        $waveTransactionId = $checkoutData['client_reference'] ?? null; // C'est ici qu'on avait passé notre reference
        $checkoutId = $checkoutData['id'] ?? null;
        
        if (!$waveTransactionId) {
            Log::warning('Webhook Wave (Deces): client_reference manquant.', $checkoutData);
            return response()->json(['success' => false, 'message' => 'Client reference manquant'], 200);
        }

        // Extraire la référence originale (enlever le suffixe unique ajouté à la génération)
        $reference = $waveTransactionId;
        if (strpos($waveTransactionId, '_') !== false) {
            $parts = explode('_', $waveTransactionId);
            $reference = $parts[0];
        }

        try {
            $deces = Deces::where('reference', $reference)->first();

            if (!$deces) {
                Log::warning("Webhook Wave (Deces): Aucune demande trouvée pour reference {$reference}.");
                return response()->json(['success' => true, 'message' => 'Demande non trouvée'], 200);
            }

            if ($eventType === 'checkout.session.completed') {
                $amount = $checkoutData['amount'] ?? null;
                $paymentStatus = $checkoutData['payment_status'] ?? null;
                $whenCompleted = $checkoutData['when_completed'] ?? null;

                try {
                    $paiement = Paiement::create([
                        'deces_id' => $deces->id,
                        'user_id' => $deces->user_id ?? null,
                        'transaction_id' => $waveTransactionId, // L'ID généré par nous
                        'operator_id' => $checkoutId, // L'ID session checkout de Wave
                        'payment_token' => null, // Non applicable avec Wave
                        'payer_name' => 'Wave Customer', // Wave ne donne pas le nom par défaut ici
                        'montant' => $amount,
                        'currency' => $checkoutData['currency'] ?? 'XOF',
                        'status' => 'ACCEPTED', // Pour matcher notre existant
                        'paid_at' => $whenCompleted ? Carbon::parse($whenCompleted) : now(),
                        'raw_response' => $request->all(),
                    ]);
                    Log::info("Paiement enregistré pour {$waveTransactionId}, id paiement: {$paiement->id}");
                } catch (\Exception $e) {
                    Log::error("Erreur enregistrement paiement pour {$waveTransactionId}: " . $e->getMessage());
                }

                // Mettre à jour le Deces
                $deces->etat = 'en attente';
                $deces->statut_livraison = 'en attente';
                $deces->save();

                Log::info("Demande {$waveTransactionId} mise à jour : en attente");

                // Envoi des notifications
                try {
                    $user = $deces->user;
                    if ($user) {
                        $yellikaSmsService = app(YellikaSmsService::class);
                        $phoneNumber = $user->indicatif . $user->contact;
                        $message = "Bonjour {$user->name}, votre paiement pour la demande d'extrait de décès a été confirmé. Référence : {$deces->reference}.
Votre demande est maintenant en attente de traitement.";
                        $yellikaSmsService->sendSms($phoneNumber, $message);

                        Notification::send($user, new DemandeDecesConfirmationNotification($user, $deces));
                    }
                } catch (\Exception $e) {
                    Log::error("Erreur notifications Webhook Deces: " . $e->getMessage());
                }

                return response()->json(['success' => true, 'message' => 'Paiement accepté et traité'], 200);

            } elseif ($eventType === 'checkout.session.payment_failed') {
                // Echec de paiement
                $deces->etat = 'paiement_echoue';
                $deces->statut_livraison = 'paiement_echoue';
                $deces->save();

                Log::warning("Demande {$waveTransactionId} paiement non accepté (Wave).");
                return response()->json(['success' => true, 'message' => 'Echec paiement traité'], 200);
            }

        } catch (\Exception $e) {
            Log::error("Webhook Wave (Deces) {$waveTransactionId}: Exception critique : " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Erreur interne'], 500);
        }

        return response()->json(['success' => true], 200);
    }


    /**"""

content = re.sub(webhook_def_old_pattern, lambda m: webhook_new, content, flags=re.DOTALL)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Replacement script complete.")
