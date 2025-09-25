@extends('user.layouts.template')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.cinetpay.com/seamless/main.js"></script>
<script src="{{ asset('js/cinetpayM.js') }}"></script>

<style>
    .demande-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .form-title {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 2rem;
        font-weight: 600;
        font-size: 2rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .form-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background-color: #ff7e5f;
        border-radius: 2px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: #34495e;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .form-control {
        width: 100%;
        padding: 0.60rem 1rem;
        border: 1px solid #dfe6e9;
        border-radius: 8px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-control:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        background-color: white;
    }

    .radio-options {
        display: flex;
        gap: 1.5rem;
        margin: 1.5rem 0;
        justify-content: center;
    }

    .radio-option {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .radio-input {
        margin-right: 0.5rem;
        accent-color: #3498db;
        transform: scale(1.2);
    }

    .submit-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #1977cc, #1977cc);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .hidden-section {
        display: none;
        animation: fadeIn 0.5s ease-out;
    }

    .section-title {
        color: #2c3e50;
        margin: 1.5rem 0 1rem;
        font-size: 1.2rem;
        font-weight: 500;
        border-bottom: 1px solid #ecf0f1;
        padding-bottom: 0.5rem;
    }

    .error-message {
        color: #e74c3c;
        font-size: 0.8rem;
        margin-top: 0.3rem;
        display: block;
    }

    .radio-options {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin: 1.5rem 0;
        justify-content: center;
    }

    .radio-card {
        position: relative;
        flex: 1 1 200px;
        max-width: 250px;
    }

    .radio-card input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .radio-card label {
        display: block;
        padding: 1.5rem 1rem;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: var(--transition);
    }

    .radio-card label:hover {
        border-color: var(--secondary-color);
    }

    .radio-card input[type="radio"]:checked + label {
        border-color: var(--primary-color);
        background-color: rgba(44, 120, 115, 0.05);
        box-shadow: 0 0 0 1px var(--primary-color);
    }

    .radio-card .icon {
        font-size: 2rem;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
        display: inline-block;
    }

    
        /* Style pour la popup de livraison */
    .swal-delivery-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    @media (max-width: 600px) {
        .swal-delivery-grid {
            grid-template-columns: 1fr;
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .demande-container {
            padding: 1.5rem;
            margin: 1rem;
        }
        
        .form-title {
            font-size: 1.5rem;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .radio-options {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>

@if (Session::get('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: '{{ Session::get('success') }}',
            timer: 3000,
            showConfirmButton: false,
        });
    </script>
@endif

@if (Session::get('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: '{{ Session::get('error') }}',
            timer: 3000,
            showConfirmButton: false,
        });
    </script>
@endif

<div class="demande-container">
    <h1 class="form-title">Demande d'extrait de mariage</h1>
    
    <form id="demandeForm" method="POST" enctype="multipart/form-data" action="#">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label for="typeDemande" class="form-label">Type de demande</label>
                <select id="typeDemande" name="typeDemande" class="form-control">
                    <option value="extraitSimple">Extrait simple</option>
                    <option value="copieIntegrale">Copie intégrale</option>
                </select>
            </div>
        </div>

        <div id="infoEpoux" class="hidden-section">
            <h3 class="section-title">Informations sur le conjoint(e)</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label for="nomEpoux" class="form-label">Nom du conjoint(e)</label>
                    <input type="text" id="nomEpoux" name="nomEpoux" class="form-control" placeholder="Entrez le nom de l'époux">
                </div>
                <div class="form-group">
                    <label for="prenomEpoux" class="form-label">Prénom du conjoint(e)</label>
                    <input type="text" id="prenomEpoux" name="prenomEpoux" class="form-control" placeholder="Entrez le prénom de l'époux">
                </div>
                <div class="form-group">
                    <label for="dateNaissanceEpoux" class="form-label">Date de naissance du conjoint(e)</label>
                    <input type="date" id="dateNaissanceEpoux" name="dateNaissanceEpoux" class="form-control">
                </div>
                <div class="form-group">
                    <label for="lieuNaissanceEpoux" class="form-label">Lieu de naissance du conjoint(e)</label>
                    <input type="text" id="lieuNaissanceEpoux" name="lieuNaissanceEpoux" class="form-control" placeholder="Entrez le lieu de naissance">
                </div>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="commune" class="form-label">Commune</label>
                <input type="text" id="commune" value="plateau" name="commune" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="pieceIdentite" class="form-label">Pièce d'identité</label>
                <input type="file" id="pieceIdentite" name="pieceIdentite" class="form-control">
                @error('pieceIdentite')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="extraitMariage" class="form-label">Extrait de mariage</label>
                <input type="file" id="extraitMariage" name="extraitMariage" class="form-control">
                @error('extraitMariage')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="CMU" class="form-label">Numéro CMU</label>
                <input type="text" id="CMU" value="{{ Auth::user()->CMU }}" name="CMU" placeholder="Entrez votre numéro CMU" class="form-control">
            </div>
        </div>

        <div class="delivery-options">
            <p class="form-label text-center" style="margin-bottom: 1rem;">Options de retrait :</p>
            <div class="radio-options">
                <div class="radio-card">
                    <input type="radio" id="option1" name="choix_option" value="Retrait sur place" checked>
                    <label for="option1">
                        <div class="icon">🏢</div>
                        <div>Retrait sur place</div>
                        <small>Gratuit</small>
                    </label>
                </div>
                <div class="radio-card">
                    <input type="radio" id="option2" name="choix_option" value="livraison">
                    <label for="option2">
                        <div class="icon">🚚</div>
                        <div>Livraison à domicile</div>
                        <small>Frais: 1500 FCFA</small>
                    </label>
                </div>
            </div>
        </div>

        <button type="submit" class="submit-btn">Soumettre la demande</button>
    </form>
</div>

<script>
    let formSubmitted = false;
    let submitAfterPopup = false;
    const optionsSection = document.getElementById('optionsSection');
    
    document.getElementById('typeDemande').addEventListener('change', function() {
        const infoEpoux = document.getElementById('infoEpoux');
        if (this.value === 'copieIntegrale') {
            infoEpoux.classList.remove('hidden-section');
        } else {
            infoEpoux.classList.add('hidden-section');
        }
    });

    function showLivraisonPopup() {
       Swal.fire({
            title: 'Informations de Livraison',
            width: '700px',
            html: `
                <div class="swal-delivery-grid">
                    <div>
                        <label for="swal-montant_timbre" style="font-weight: bold">Timbre</label>
                        <input id="swal-montant_timbre" class="swal2-input text-center" value="50" readonly>
                        <small style="color:#666">Frais couverts par Kks-technologies</small>
                    </div>
                    <div>
                        <label for="swal-montant_livraison" style="font-weight: bold">Frais Livraison</label>
                        <input id="swal-montant_livraison" class="swal2-input text-center" value="50" readonly>
                        <small style="color:#666">Frais fixes pour la livraison</small>
                    </div>
                    <div>
                        <label for="swal-nom_destinataire" style="font-weight: bold">Nom</label>
                        <input id="swal-nom_destinataire" class="swal2-input" placeholder="Nom du destinataire">
                    </div>
                    <div>
                        <label for="swal-prenom_destinataire" style="font-weight: bold">Prénom</label>
                        <input id="swal-prenom_destinataire" class="swal2-input" placeholder="Prénom du destinataire">
                    </div>
                    <div>
                        <label for="swal-email_destinataire" style="font-weight: bold">Email</label>
                        <input id="swal-email_destinataire" class="swal2-input" placeholder="Email du destinataire" type="email">
                    </div>
                    <div>
                        <label for="swal-contact_destinataire" style="font-weight: bold">Téléphone</label>
                        <input id="swal-contact_destinataire" class="swal2-input" placeholder="Contact du destinataire" type="tel">
                    </div>
                    <div>
                        <label for="swal-adresse_livraison" style="font-weight: bold">Adresse</label>
                        <input id="swal-adresse_livraison" class="swal2-input" placeholder="Adresse complète">
                    </div>
                    <div>
                        <label for="swal-ville" style="font-weight: bold">Ville</label>
                        <input id="swal-ville" class="swal2-input" placeholder="Ville de livraison">
                    </div>
                    <div>
                        <label for="swal-commune_livraison" style="font-weight: bold">Commune</label>
                        <input id="swal-commune_livraison" class="swal2-input" placeholder="Commune">
                    </div>
                    <div>
                        <label for="swal-quartier" style="font-weight: bold">Quartier</label>
                        <input id="swal-quartier" class="swal2-input" placeholder="Quartier">
                    </div>
                </div>`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Payer maintenant',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#1977cc',
            focusConfirm: false,
            preConfirm: () => {
                const nom_destinataire = document.getElementById('swal-nom_destinataire').value;
                const prenom_destinataire = document.getElementById('swal-prenom_destinataire').value;
                const email_destinataire = document.getElementById('swal-email_destinataire').value;
                const contact_destinataire = document.getElementById('swal-contact_destinataire').value;
                const adresse_livraison = document.getElementById('swal-adresse_livraison').value;
                const ville = document.getElementById('swal-ville').value;
                const commune_livraison = document.getElementById('swal-commune_livraison').value;
                const quartier = document.getElementById('swal-quartier').value;
                const montant_timbre = document.getElementById('swal-montant_timbre').value;
                const montant_livraison = document.getElementById('swal-montant_livraison').value;

                if (!nom_destinataire || !prenom_destinataire || !email_destinataire || !contact_destinataire || !adresse_livraison || !ville || !commune_livraison || !quartier || !montant_timbre || !montant_livraison) {
                    Swal.showValidationMessage("Veuillez remplir tous les champs obligatoires");
                    return false;
                }
                return {
                    nom_destinataire: nom_destinataire,
                    prenom_destinataire: prenom_destinataire,
                    email_destinataire: email_destinataire,
                    contact_destinataire: contact_destinataire,
                    adresse_livraison: adresse_livraison,
                    ville: ville,
                    commune_livraison: commune_livraison,
                    quartier: quartier,
                    montant_timbre: parseFloat(montant_timbre),
                    montant_livraison: parseFloat(montant_livraison),
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = result.value;
                initializeCinetPay(formData); // Appel de la fonction CinetPay
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Si l'utilisateur clique sur annuler, sélectionner l'option 1
                document.getElementById('option1').checked = true;
            }
        });
    }

   function initializeCinetPay(formData) {
    // Configuration CinetPay
    CinetPay.setConfig({
        apikey: '{{ config("services.cinetpay.api_key") }}',
        site_id: '{{ config("services.cinetpay.site_id") }}',
        mode: 'PRODUCTION'
    });

    // ID de transaction
    const transactionId = 'EXT-' + Date.now();
    
    // Montant total
    const totalAmount = formData.montant_timbre + formData.montant_livraison;

    // Chargement
    Swal.fire({
        title: 'Redirection en cours',
        html: 'Préparation du paiement...',
        allowOutsideClick: true,
        didOpen: () => Swal.showLoading()
    });

    // Données client
    const customer = {
        name: '{{ Auth::user()->name ?? "Client" }}',
        email: '{{ Auth::user()->email ?? "contact@client.com" }}',
        phone: '{{ Auth::user()->telephone ?? "00000000" }}'
    };

    // Paiement
    CinetPay.getCheckout({
        transaction_id: transactionId,
        amount: totalAmount,
        currency: 'XOF',
        channels: 'ALL',
        description: `Paiement pour livraison d'extrait de naissance`,
        customer_name: customer.name,
        customer_email: customer.email,
        customer_phone_number: customer.phone,
        customer_address: formData.adresse_livraison,
        customer_city: formData.ville,
        customer_country: 'CI',
        customer_state: 'CI',
        customer_zip_code: '00225'
    });

    // Gestion réponse
    CinetPay.waitResponse(function(data) {
        Swal.close();
        if (data.status === "ACCEPTED") {
            // Ajouter les données de livraison au formulaire
            const form = document.getElementById('demandeForm');
            
            // Créer des champs cachés pour les données de livraison
            const hiddenFields = [
                { name: 'nom_destinataire', value: formData.nom_destinataire },
                { name: 'prenom_destinataire', value: formData.prenom_destinataire },
                { name: 'email_destinataire', value: formData.email_destinataire },
                { name: 'contact_destinataire', value: formData.contact_destinataire },
                { name: 'adresse_livraison', value: formData.adresse_livraison },
                { name: 'ville', value: formData.ville },
                { name: 'commune_livraison', value: formData.commune_livraison },
                { name: 'quartier', value: formData.quartier },
                { name: 'montant_timbre', value: formData.montant_timbre },
                { name: 'montant_livraison', value: formData.montant_livraison },
                { name: 'transaction_id', value: transactionId }
            ];

            hiddenFields.forEach(field => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = field.name;
                input.value = field.value;
                form.appendChild(input);
            });

            // Soumettre le formulaire
            formSubmitted = true;
            form.submit();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Échec du paiement',
                text: data.message || 'Erreur lors du traitement'
            });
        }
    });

    // Gestion erreurs
    CinetPay.onError(function(error) {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            html: `Une erreur est survenue<br><small>${error.message || 'Veuillez réessayer'}</small>`
        });
    });
}

    document.getElementById('demandeForm').addEventListener('submit', function(event) {
        if (formSubmitted) {
            event.preventDefault();
            return;
        }
        const livraisonCheckbox = document.getElementById('option2');
        if (livraisonCheckbox.checked && !submitAfterPopup) {
            event.preventDefault();
            showLivraisonPopup();
        } else {
            formSubmitted = true;
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const isFilled = Array.from(document.querySelectorAll("#demandeForm input[required], #demandeForm select"))
            .every(input => input.value.trim() !== "");
        if (isFilled) {
            optionsSection.style.display = 'block';
        }
    });
</script>

@endsection