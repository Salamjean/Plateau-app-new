function initializeCinetPay(formData) {
    // Configuration CinetPay
    CinetPay.setConfig({
        apikey: '521006956621e4e7a6a3d16.70681548', // Remplacez par votre APIKEY
        site_id: '935132', // Remplacez par votre SITE ID
        notify_url: 'http://mondomaine.com/notify/', // URL de notification
        mode: 'PRODUCTION' // ou 'TEST' pour le mode test
    });

    // Générer un ID de transaction unique
    const transactionId = Math.floor(Math.random() * 100000000).toString();

    // Lancer le paiement
    CinetPay.getCheckout({
        transaction_id: transactionId,
        amount: formData.montant_timbre + formData.montant_livraison, // Montant total
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

    // Gérer la réponse de CinetPay
    CinetPay.waitResponse(function(data) {
        if (data.status === "ACCEPTED") {
            // Ajouter les données de livraison au formulaire
            const form = document.getElementById('naissanceForm');
            for (const key in formData) {
                if (formData.hasOwnProperty(key)) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = key;
                    hiddenInput.value = formData[key];
                    form.appendChild(hiddenInput);
                }
            }
            // Soumettre le formulaire
            form.submit();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Le paiement a échoué. Veuillez réessayer.',
            });
        }
    });
}