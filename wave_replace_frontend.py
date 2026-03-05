import re

# 1. Update DecesController.php

controller_path = "app/Http/Controllers/User/Extrait/Deces/DecesController.php"
with open(controller_path, "r", encoding="utf-8") as f:
    controller_content = f.read()

# Add Http dependency if missing
if "use Illuminate\\Support\\Facades\\Http;" not in controller_content:
    controller_content = controller_content.replace(
        "use Illuminate\\Support\\Facades\\Storage;",
        "use Illuminate\\Support\\Facades\\Storage;\nuse Illuminate\\Support\\Facades\\Http;"
    )

store_pattern = r"        // Envoi de l'email de confirmation\n        Notification::send\(\$user, new DemandeDecesConfirmationNotification\(\$user, \$deces\)\);\n\n        return redirect\(\)->route\('user\.extrait\.deces\.index'\)->with\('success', 'Demande envoyée avec succès\.'\);\n    \}"

# The new logic for store()
store_new = r"""        // Envoi de l'email de confirmation (Seulement pour retrait)
        if ($deces->choix_option !== 'livraison') {
            Notification::send($user, new DemandeDecesConfirmationNotification($user, $deces));
            return redirect()->route('user.extrait.deces.index')->with('success', 'Demande envoyée avec succès.');
        }

        // --- Logique Paiement Wave pour Livraison ---
        try {
            $baseUrl = config('app.url');
            $returnUrl = $baseUrl . "/user/extrait/deces?payment=success&transactionId=" . urlencode($deces->reference); // Or appropriate redirect
            $cancelUrl = $baseUrl . "/user/extrait/deces/create?payment=cancelled&transactionId=" . urlencode($deces->reference);

            $cout_total_timbres = (float) $deces->montant_timbre * (int) $deces->quantite;
            $totalAmount = $cout_total_timbres + (float) $deces->montant_livraison;

            $waveApiKey = env('WAVE_API_KEY', 'wave_ci_prod_ztyw2awlmgOaaDtMK_U1w_zZ24qFXoDzH5ZpxUoJNP_-jR72suwolQfukDai0-ilUaorkOMWPmTA8Poi8mb2bq1AliY4USX4uw');
            $waveTransactionId = $deces->reference . '_' . time(); // Unique ID

            $paymentData = [
                'amount' => (string) $totalAmount,
                'currency' => 'XOF',
                'error_url' => $cancelUrl,
                'success_url' => $returnUrl,
                'client_reference' => $waveTransactionId
            ];

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$waveApiKey}",
                'Content-Type' => 'application/json'
            ])->withoutVerifying()->post('https://api.wave.com/v1/checkout/sessions', $paymentData);

            if ($response->failed()) {
                Log::error('Erreur Wave (Web Frontend): ' . $response->body(), ['transaction_id' => $deces->reference]);
                return redirect()->route('user.extrait.deces.index')->with('error', 'Demande créée, mais échec du lien de paiement.');
            }

            $responseData = $response->json();
            
            // Redirect user to the Wave payment page
            return redirect()->away($responseData['wave_launch_url']);

        } catch (\Exception $e) {
            Log::error('Exception in Wave Web generate: ' . $e->getMessage(), ['reference' => $deces->reference]);
            return redirect()->route('user.extrait.deces.index')->with('error', 'Erreur interne lors du paiement.');
        }
    }"""

controller_content = re.sub(store_pattern, lambda m: store_new, controller_content, flags=re.DOTALL)

with open(controller_path, "w", encoding="utf-8") as f:
    f.write(controller_content)


# 2. Update routes
api_routes_path = "routes/api.php"
with open(api_routes_path, "r", encoding="utf-8") as f:
    api_routes = f.read()

api_routes = api_routes.replace("Route::post('/cinetpay/notify/deces',", "Route::post('/wave/notify/deces',")
api_routes = api_routes.replace("->name('api.cinetpay.notify.deces')", "->name('api.wave.notify.deces')")

with open(api_routes_path, "w", encoding="utf-8") as f:
    f.write(api_routes)


# 3. Modify create.blade.php
blade_path = "resources/views/user/deces/simple/create.blade.php"
with open(blade_path, "r", encoding="utf-8") as f:
    blade_content = f.read()

# Remove cinetpay JS imports
blade_content = blade_content.replace('<script src="https://cdn.cinetpay.com/seamless/main.js"></script>', "")
blade_content = blade_content.replace('<script src="{{ asset(\'js/cinetpay_deces_deja.js\') }}"></script>', "")

# Modify the sumbit interceptor logic
submit_logic_old = r"""        // ==============================
        // SOUMISSION FORMULAIRE
        // ==============================
        \$\("\#declarationForm"\)\.submit\(function\(event\) \{
            if \(formSubmitted\) \{
                event\.preventDefault\(\);
                return;
            \}

            const livraisonCheckbox = \$\("\#option2"\);
            if \(livraisonCheckbox\.is\(":checked"\) && !submitAfterPopup\) \{
                event\.preventDefault\(\); // Prevent default submission initially

                if \(validateFormClient\(\)\) \{ // Perform client-side validation
                    showLivraisonPopup\(\); // If valid, show payment popup
                \} else \{
                    Swal\.fire\(\{
                        icon: 'error',
                        title: 'Erreur de Validation',
                        text: 'Veuillez corriger les erreurs dans le formulaire avant de continuer\.',
                        confirmButtonColor: '\#e74c3c'
                    \}\);
                \}
            \} else \{
                // If "Retrait sur place" or form is being submitted after successful payment
                formSubmitted = true;
                // Form will naturally submit if preventDefault was not called,
                // or will be explicitly submitted by initializeCinetPay
            \}
        \}\);"""

submit_logic_new = r"""        // ==============================
        // SOUMISSION FORMULAIRE
        // ==============================
        $("#declarationForm").submit(function(event) {
            if (formSubmitted) {
                event.preventDefault();
                return;
            }

            if (!validateFormClient()) {
                event.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur de Validation',
                    text: 'Veuillez corriger les erreurs dans le formulaire avant de continuer.',
                    confirmButtonColor: '#e74c3c'
                });
                return;
            }

            const livraisonCheckbox = $("#option2");
            if (livraisonCheckbox.is(":checked")) {
                event.preventDefault(); // On intercepte pour préparer les champs cachés avant la soumission
                prepareLivraisonFieldsAndSubmit();
            } else {
                formSubmitted = true;
            }
        });"""

blade_content = re.sub(submit_logic_old, lambda m: submit_logic_new, blade_content, flags=re.DOTALL)

# Modify popup logic to just submit instead of initializeCinetPay
popup_logic_old = r"""        \}\)\.then\(\(result\) => \{
            if \(result\.isConfirmed\) \{
                const formData = result\.value;
                initializeCinetPay\(formData\); // Appel de la fonction CinetPay
            \} else if \(result\.dismiss === Swal\.DismissReason\.cancel\) \{
                // Si l'utilisateur clique sur annuler, sélectionner l'option "Retrait sur place"
                document\.getElementById\('option1'\)\.checked = true;
            \}
        \}\);
    \}

   function initializeCinetPay\(formData\) \{
.*?</script>"""

popup_logic_new = r"""        }).then((result) => {
            if (result.isConfirmed) {
                const formData = result.value;
                
                // Add delivery data to the form as hidden inputs
                const form = document.getElementById('declarationForm');
                
                // Vider les anciens hidden inputs s'il y en a eu
                $('.dynamic-hidden-field').remove();

                const hiddenFields = [
                    { name: 'nom_destinataire', value: formData.nom_destinataire },
                    { name: 'prenom_destinataire', value: formData.prenom_destinataire },
                    { name: 'email_destinataire', value: formData.email_destinataire },
                    { name: 'contact_destinataire', value: formData.contact_destinataire },
                    { name: 'adresse_livraison', value: formData.adresse_livraison },
                    { name: 'ville', value: formData.ville },
                    { name: 'commune_livraison', value: formData.commune_livraison },
                    { name: 'quartier', value: formData.quartier },
                    { name: 'montant_timbre_unitaire', value: formData.montant_timbre_unitaire },
                    { name: 'montant_timbre', value: formData.montant_timbre },
                    { name: 'montant_livraison', value: formData.montant_livraison }
                ];

                hiddenFields.forEach(field => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = field.name;
                    input.value = field.value;
                    input.className = 'dynamic-hidden-field';
                    form.appendChild(input);
                });

                // Set flag and submit the form (which will redirect to Wave Checkout server-side)
                formSubmitted = true;
                
                // Afficher un loader pendant la redirection
                Swal.fire({
                    title: 'Redirection vers Wave',
                    html: `Préparation du paiement de ${formData.montant_timbre + formData.montant_livraison} FCFA...`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                
                form.submit();
                
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                document.getElementById('option1').checked = true;
            }
        });
    }
</script>"""

blade_content = re.sub(popup_logic_old, lambda m: popup_logic_new, blade_content, flags=re.DOTALL)
blade_content = blade_content.replace("function showLivraisonPopup()", "function prepareLivraisonFieldsAndSubmit()")

with open(blade_path, "w", encoding="utf-8") as f:
    f.write(blade_content)

print("Frontend replacement and web controller update complete.")
