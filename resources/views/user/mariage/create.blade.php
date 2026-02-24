@extends('user.layouts.template')
@section('content')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.cinetpay.com/seamless/main.js"></script>
    <script src="{{ asset('js/cinetpayM.js') }}"></script>

    <style>
        :root {
            --primary: #1977cc;
            --primary-soft: rgba(25, 119, 204, 0.1);
            --secondary: #2c7873;
            --accent: #ff7e5f;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --bg-glass: rgba(255, 255, 255, 0.9);
            --border-radius: 16px;
            --shadow-sm: 0 4px 6px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 10px 25px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .marriage-certificate-container {
            background: var(--bg-glass);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            backdrop-filter: blur(12px);
            padding: 3rem;
            width: 70%;
            margin: 2rem auto;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .marriage-certificate-title {
            text-align: center;
            color: var(--primary);
            margin-bottom: 2.5rem;
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .marriage-certificate-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            margin: 12px auto 0;
            border-radius: 2px;
        }

        .section-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #edf2f7;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .section-card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--primary-soft);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 600;
            color: #4a5568;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            border: 2px solid #eef2f7;
            border-radius: 10px;
            background-color: #fcfdfe;
            transition: var(--transition);
            font-size: 0.95rem;
            color: #2d3748;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(25, 119, 204, 0.1);
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .input-icon-wrapper .form-control {
            padding-left: 40px;
        }

        /* Radio Cards */
        .radio-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.2rem;
            margin-top: 1rem;
        }

        .radio-card {
            position: relative;
        }

        .radio-card input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .radio-card label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: #fff;
            border: 2px solid #edf2f7;
            border-radius: 12px;
            cursor: pointer;
            transition: var(--transition);
            height: 100%;
        }

        .radio-card label:hover {
            border-color: var(--primary-soft);
            background: #fafbff;
        }

        .radio-card input[type="radio"]:checked+label {
            border-color: var(--primary);
            background: var(--primary-soft);
            box-shadow: var(--shadow-sm);
        }

        .radio-card i,
        .radio-card .icon-fs {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--primary);
        }

        .radio-card span {
            font-weight: 700;
            color: #2d3748;
            font-size: 0.95rem;
        }

        .radio-card small {
            color: #718096;
            margin-top: 4px;
        }

        .submit-btn {
            width: 100%;
            padding: 1.2rem;
            background: linear-gradient(135deg, var(--primary), #1565b8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 4px 15px rgba(25, 119, 204, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(25, 119, 204, 0.4);
        }

        .error-message {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 5px;
            font-weight: 500;
        }

        .hidden-section {
            display: none;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 992px) {
            .marriage-certificate-container {
                width: 90%;
            }
        }

        @media (max-width: 768px) {
            .marriage-certificate-container {
                padding: 1.5rem;
                width: 95%;
            }

            .form-grid,
            .radio-grid {
                grid-template-columns: 1fr;
            }

            .marriage-certificate-title {
                font-size: 1.8rem;
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

    <div class="marriage-certificate-container">
        <h1 class="marriage-certificate-title">Demande d'acte de mariage</h1>

        <form id="demandeForm" method="POST" enctype="multipart/form-data"
            action="{{ route('user.extrait.mariage.store') }}">
            @csrf

            <!-- Section: Type de demande -->
            <div class="section-card">
                <h3 class="section-title"><i class="fas fa-file-signature"></i> Type de demande</h3>
                <div class="form-group full-width">
                    <label for="typeDemande" class="form-label">Type d'acte souhaité</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-list"></i>
                        <select id="typeDemande" name="typeDemande" class="form-control">
                            <option value="extraitSimple">Acte simple (Extrait)</option>
                            <option value="copieIntegrale">Copie intégrale</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section: Informations Conjoint (Conditionnelle) -->
            <div id="infoEpoux" class="section-card hidden-section">
                <h3 class="section-title"><i class="fas fa-user-friends"></i> Informations sur le conjoint(e)</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nomEpoux" class="form-label">Nom du conjoint(e)</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="nomEpoux" name="nomEpoux" class="form-control"
                                placeholder="Nom de l'époux(se)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="prenomEpoux" class="form-label">Prénom du conjoint(e)</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="prenomEpoux" name="prenomEpoux" class="form-control"
                                placeholder="Prénom de l'époux(se)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="dateNaissanceEpoux" class="form-label">Date de naissance</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="date" id="dateNaissanceEpoux" name="dateNaissanceEpoux" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lieuNaissanceEpoux" class="form-label">Lieu de naissance</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" id="lieuNaissanceEpoux" name="lieuNaissanceEpoux" class="form-control"
                                placeholder="Ville de naissance">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Détails de l'acte -->
            <div class="section-card">
                <h3 class="section-title"><i class="fas fa-book"></i> Détails de l'acte et Pièces</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="commune" class="form-label">Commune de l'acte</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-city"></i>
                            <input type="text" id="commune" value="Plateau" name="commune" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="quantite" class="form-label">Quantité souhaitée</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-copy"></i>
                            <input type="number" id="quantite" name="quantite" class="form-control"
                                value="{{ old('quantite', 1) }}" min="1" max="10">
                        </div>
                        @error('quantite') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="pieceIdentite" class="form-label">Pièce d'identité (CNI/Pass)</label>
                        <input type="file" id="pieceIdentite" name="pieceIdentite" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png">
                        @error('pieceIdentite') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="extraitMariage" class="form-label">Ancien acte de mariage</label>
                        <input type="file" id="extraitMariage" name="extraitMariage" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png">
                        @error('extraitMariage') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="CMU" class="form-label">Numéro NNI (Optionnel)</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-id-card"></i>
                            <input type="text" id="CMU" value="{{ Auth::user()->CMU }}" name="CMU"
                                placeholder="Votre numéro NNI" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Mode de retrait -->
            <div class="section-card">
                <h3 class="section-title"><i class="fas fa-shipping-fast"></i> Option de retrait</h3>
                <div class="radio-grid">
                    <div class="radio-card">
                        <input type="radio" id="option1" name="choix_option" value="Retrait sur place" checked>
                        <label for="option1">
                            <i class="fas fa-building icon-fs"></i>
                            <span>Retrait sur place</span>
                            <small>À la mairie (Gratuit)</small>
                        </label>
                    </div>
                    <div class="radio-card">
                        <input type="radio" id="option2" name="choix_option" value="livraison">
                        <label for="option2">
                            <i class="fas fa-truck icon-fs"></i>
                            <span>Livraison à domicile</span>
                            <small>Frais : 1500 FCFA</small>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fas fa-check-circle"></i> Soumettre la demande
            </button>
        </form>
    </div>

    <script>
        let formSubmitted = false;
        let submitAfterPopup = false;

        document.getElementById('typeDemande').addEventListener('change', function () {
            const infoEpoux = document.getElementById('infoEpoux');
            if (this.value === 'copieIntegrale') {
                infoEpoux.classList.remove('hidden-section');
            } else {
                infoEpoux.classList.add('hidden-section');
            }
        });

        function showLivraisonPopup() {
            const quantite = parseInt(document.getElementById('quantite').value) || 1;
            const montantTimbreUnitaire = 50;
            const montantTimbreTotal = montantTimbreUnitaire * quantite;
            const montantLivraison = 1500; // 1500 FCFA pour la livraison

            Swal.fire({
                title: '<i class="fas fa-shipping-fast" style="color: #1977cc;"></i> Informations de Livraison',
                width: '800px',
                html: `
                        <div class="swal-custom-container" style="text-align: left; padding: 10px;">
                            <!-- Résumé de la commande -->
                            <div style="background: #f8fafc; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; border: 1px solid #e2e8f0;">
                                 <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px dashed #cbd5e1; padding-bottom: 10px;">
                                    <span style="font-weight: 600; color: #64748b;">Quantité demandée</span>
                                    <span style="font-weight: 800; color: #1e293b; font-size: 1.1rem;">${quantite} Exemplaire(s)</span>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                    <div style="background: white; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        <small style="display: block; color: #94a3b8; font-weight: 600;">TIMBRE (${quantite})</small>
                                        <span style="font-weight: 700; color: #1e293b;">${montantTimbreTotal} FCFA</span>
                                    </div>
                                    <div style="background: white; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                        <small style="display: block; color: #94a3b8; font-weight: 600;">LIVRAISON</small>
                                        <span style="font-weight: 700; color: #1e293b;">${montantLivraison} FCFA</span>
                                    </div>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; background: #1977cc; color: white; padding: 15px; border-radius: 10px; box-shadow: 0 4px 10px rgba(25, 119, 204, 0.2);">
                                    <span style="font-weight: 600;">TOTAL À PAYER</span>
                                    <span style="font-weight: 800; font-size: 1.3rem;">${montantTimbreTotal + montantLivraison} FCFA</span>
                                </div>
                            </div>

                            <!-- Formulaire Détaillé -->
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                <div>
                                    <h4 style="margin-bottom: 1.2rem; color: #1e293b; font-size: 1rem; border-left: 4px solid #1977cc; padding-left: 10px;">
                                        <i class="fas fa-user-check" style="color: #1977cc; margin-right: 8px;"></i>Destinataire
                                    </h4>
                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">NOM</label>
                                        <input id="swal-nom_destinataire" class="swal2-input" style="width: 100%; margin: 0; height: 45px;">
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">PRÉNOM</label>
                                        <input id="swal-prenom_destinataire" class="swal2-input" style="width: 100%; margin: 0; height: 45px;">
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">EMAIL</label>
                                        <input id="swal-email_destinataire" type="email" class="swal2-input" style="width: 100%; margin: 0; height: 45px;">
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">TÉLÉPHONE</label>
                                        <input id="swal-contact_destinataire" type="tel" class="swal2-input" style="width: 100%; margin: 0; height: 45px;">
                                    </div>
                                </div>

                                <div>
                                    <h4 style="margin-bottom: 1.2rem; color: #1e293b; font-size: 1rem; border-left: 4px solid #2c7873; padding-left: 10px;">
                                        <i class="fas fa-map-marked-alt" style="color: #2c7873; margin-right: 8px;"></i>Adresse
                                    </h4>
                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">VILLE</label>
                                        <input id="swal-ville" class="swal2-input" style="width: 100%; margin: 0; height: 45px;">
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">COMMUNE</label>
                                        <input id="swal-commune_livraison" class="swal2-input" style="width: 100%; margin: 0; height: 45px;">
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">QUARTIER</label>
                                        <input id="swal-quartier" class="swal2-input" style="width: 100%; margin: 0; height: 45px;">
                                    </div>
                                    <div style="margin-bottom: 15px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                        <div>
                                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">DATE DE LIVRAISON *</label>
                                            <div style="position: relative;">
                                                <i class="fas fa-calendar-day" style="position: absolute; left: 12px; top: 15px; color: #94a3b8; z-index: 10;"></i>
                                                <input id="swal-date_livraison" type="date" class="swal2-input" style="width: 100%; margin: 0; padding-left: 35px; height: 45px; font-size: 0.95rem;" min="${new Date(Date.now() + 86400000).toISOString().split('T')[0]}">
                                            </div>
                                        </div>
                                        <div>
                                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">HEURE (OPTIONNEL)</label>
                                            <div style="position: relative;">
                                                <i class="fas fa-clock" style="position: absolute; left: 12px; top: 15px; color: #94a3b8; z-index: 10;"></i>
                                                <input id="swal-heure_livraison" type="time" class="swal2-input" style="width: 100%; margin: 0; padding-left: 35px; height: 45px; font-size: 0.95rem;">
                                            </div>
                                        </div>
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">ADRESSE EXACTE</label>
                                        <input id="swal-adresse_livraison" class="swal2-input" style="width: 100%; margin: 0; height: 45px;">
                                    </div>
                                </div>
                            </div>
                        </div>`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: `<i class="fas fa-credit-card"></i> Payer ${montantTimbreTotal + montantLivraison} FCFA`,
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#1977cc',
                focusConfirm: false,
                preConfirm: () => {
                    const nom = document.getElementById('swal-nom_destinataire').value;
                    const prenom = document.getElementById('swal-prenom_destinataire').value;
                    const email = document.getElementById('swal-email_destinataire').value;
                    const tel = document.getElementById('swal-contact_destinataire').value;
                    const ville = document.getElementById('swal-ville').value;
                    const commune = document.getElementById('swal-commune_livraison').value;
                    const quartier = document.getElementById('swal-quartier').value;
                    const adresse = document.getElementById('swal-adresse_livraison').value;
                    const date_livraison = document.getElementById('swal-date_livraison').value;
                    const heure_livraison = document.getElementById('swal-heure_livraison').value;

                    if (!nom || !prenom || !email || !tel || !ville || !commune || !quartier || !adresse || !date_livraison) {
                        Swal.showValidationMessage("Remplissez tous les champs obligatoires (incluant la date)");
                        return false;
                    }
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                        Swal.showValidationMessage("Email invalide");
                        return false;
                    }
                    return { nom, prenom, email, tel, ville, commune, quartier, adresse, date_livraison, heure_livraison, quantite, montantTimbreTotal, montantLivraison };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    initializeCinetPay(result.value);
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    document.getElementById('option1').checked = true;
                }
            });
        }

        function initializeCinetPay(formData) {
            CinetPay.setConfig({
                apikey: '{{ config("services.cinetpay.api_key") }}',
                site_id: '{{ config("services.cinetpay.site_id") }}',
                mode: 'PRODUCTION'
            });

            const transactionId = 'MAR-' + Date.now();
            const totalAmount = formData.montantTimbreTotal + formData.montantLivraison;

            Swal.fire({
                title: 'Redirection...',
                html: `Paiement de ${totalAmount} FCFA en cours...`,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            CinetPay.getCheckout({
                transaction_id: transactionId,
                amount: totalAmount,
                currency: 'XOF',
                channels: 'ALL',
                description: `Acte Mariage x${formData.quantite}`,
                customer_name: '{{ Auth::user()->name }}',
                customer_email: '{{ Auth::user()->email }}',
                customer_phone_number: '{{ Auth::user()->telephone }}',
                customer_address: formData.adresse,
                customer_city: formData.ville,
                customer_country: 'CI'
            });

            CinetPay.waitResponse(function (data) {
                Swal.close();
                if (data.status === "ACCEPTED") {
                    const form = document.getElementById('demandeForm');
                    const fields = [
                        { n: 'nom_destinataire', v: formData.nom },
                        { n: 'prenom_destinataire', v: formData.prenom },
                        { n: 'email_destinataire', v: formData.email },
                        { n: 'contact_destinataire', v: formData.tel },
                        { n: 'adresse_livraison', v: formData.adresse },
                        { n: 'ville', v: formData.ville },
                        { n: 'commune_livraison', v: formData.commune },
                        { n: 'quartier', v: formData.quartier },
                        { n: 'date_livraison', v: formData.date_livraison },
                        { n: 'heure_livraison', v: formData.heure_livraison },
                        { n: 'montant_timbre_unitaire', v: 50 }, // Assuming 50 FCFA per unit
                        { n: 'montant_timbre', v: formData.montantTimbreTotal },
                        { n: 'montant_livraison', v: formData.montantLivraison },
                        { n: 'transaction_id', v: transactionId }
                    ];

                    fields.forEach(f => {
                        const i = document.createElement('input');
                        i.type = 'hidden'; i.name = f.n; i.value = f.v;
                        form.appendChild(i);
                    });

                    formSubmitted = true;
                    form.submit();
                } else {
                    Swal.fire('Erreur', data.message || 'Échec du paiement', 'error');
                }
            });
        }

        document.getElementById('demandeForm').addEventListener('submit', function (event) {
            if (formSubmitted) return;
            const livraison = document.getElementById('option2').checked;
            if (livraison && !submitAfterPopup) {
                event.preventDefault();
                showLivraisonPopup();
            } else {
                formSubmitted = true;
            }
        });
    </script>

@endsection