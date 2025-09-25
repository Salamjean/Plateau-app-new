// cinetpay.js
function initiateCinetPayPayment(formData, form) {
    const totalAmount = parseFloat(formData.montant_timbre) + parseFloat(formData.montant_livraison);

    CinetPay.setConfig({
        apikey: '521006956621e4e7a6a3d16.70681548', // Remplacez par votre APIKEY
        site_id: '935132', // Remplacez par votre SITE ID
        notify_url: 'http://mondomaine.com/notify/', // URL de notification (à modifier)
        mode: 'PRODUCTION' // ou 'TEST' pour le mode test
    });

    const transactionId = Math.floor(Math.random() * 100000000).toString();

    CinetPay.getCheckout({
        transaction_id: transactionId,
        amount: totalAmount,
        currency: 'XOF',
        channels: 'ALL',
        description: 'Paiement pour la livraison de l\'acte de mariage',
        customer_name: formData.nom_destinataire,
        customer_surname: formData.prenom_destinataire,
        customer_email: formData.email_destinataire,
        customer_phone_number: formData.contact_destinataire,
        customer_address: formData.adresse_livraison,
        customer_city: formData.ville,
        customer_country: 'CI',
        customer_state: 'CI',
        customer_zip_code: formData.code_postal,
    });

    CinetPay.waitResponse(function(data) {
        if (data.status === "ACCEPTED") {
            // Payment successful, add delivery data to the form and submit
            for (const key in formData) {
                if (formData.hasOwnProperty(key)) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = key;
                    hiddenInput.value = formData[key];
                    form.appendChild(hiddenInput);
                }
            }
            form.submit();
        } else if (data.status === "REFUSED") {
            // Payment failed
            Swal.fire({
                icon: 'error',
                title: 'Erreur de Paiement',
                text: 'Le paiement a été refusé. Veuillez réessayer.',
            });
        } else {
            // Payment pending or other status
            Swal.fire({
                icon: 'warning',
                title: 'Paiement en attente',
                text: 'Votre paiement est en cours de traitement.',
            });
        }
    });

    CinetPay.onError(function(data) {
        // Handle error during payment process
        console.error("CinetPay error:", data);
        Swal.fire({
            icon: 'error',
            title: 'Erreur Inattendue',
            text: 'Une erreur s\'est produite lors du paiement. Veuillez réessayer plus tard.',
        });
    });
}