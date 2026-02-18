@extends('user.layouts.template')

@section('content')

    <!-- Inclure SweetAlert2 et CinetPay -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.cinetpay.com/seamless/main.js"></script>

    <!-- Inclure le fichier JavaScript externe pour CinetPay -->
    <script src="{{ asset('js/cinetpay_deces.js') }}"></script>

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

        .form-container {
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

        .form-title {
            text-align: center;
            color: var(--primary);
            margin-bottom: 2.5rem;
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .form-title::after {
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

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-col {
            margin-bottom: 0;
        }

        .form-col.full-width {
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

        .radio-card i {
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

        .btn-submit {
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

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(25, 119, 204, 0.4);
        }

        .error-message {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 5px;
            font-weight: 500;
        }

        /* Custom File Upload */
        .file-input-container {
            position: relative;
            width: 100%;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.8rem 1rem;
            background: #f8fafc;
            border: 2px dashed #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: var(--transition);
            color: #64748b;
        }

        .file-input-label:hover {
            border-color: var(--primary);
            background: var(--primary-soft);
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
                margin: 1rem;
            }

            .form-row,
            .radio-grid {
                grid-template-columns: 1fr;
            }

            .form-title {
                font-size: 1.8rem;
            }
        }
    </style>

    <div class="form-container">
        <h2 class="form-title">Demande d'acte de naissance</h2>

        <form id="naissanceForm" action="{{route('user.extrait.store')}}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Section: Type de demande -->
            <div class="section-card">
                <h3 class="section-title"><i class="fas fa-info-circle"></i> Type de demande</h3>
                <div class="form-row">
                    <div class="form-col">
                        <label for="pour" class="form-label">Cette demande est pour :</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-users"></i>
                            <select id="pour" name="pour" class="form-control" onchange="updateFields()">
                                <option value="Moi" {{ old('pour') == 'Moi' ? 'selected' : '' }}>Moi-même</option>
                                <option value="une_autre_personne" {{ old('pour') == 'une_autre_personne' ? 'selected' : '' }}>Une autre personne</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-col">
                        <label for="type" class="form-label">Type de document :</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-file-alt"></i>
                            <select id="type" name="type" class="form-control">
                                <option value="simple" {{ old('type') == 'simple' ? 'selected' : '' }}>Acte simple</option>
                                <option value="extrait_integral" {{ old('type') == 'extrait_integral' ? 'selected' : '' }}>
                                    Acte intégral</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Informations du titulaire -->
            <div class="section-card">
                <h3 class="section-title"><i class="fas fa-user"></i> Informations sur l'acte</h3>
                <div class="form-row">
                    <div class="form-col">
                        <label for="name" class="form-label">Nom :</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-id-card"></i>
                            <input type="text" id="name" name="name" class="form-control"
                                value="{{ old('name', $userName) }}" placeholder="Nom tel qu'indiqué sur l'acte">
                        </div>
                        @error('name') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-col">
                        <label for="prenom" class="form-label">Prénoms :</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-id-card"></i>
                            <input type="text" id="prenom" name="prenom" class="form-control"
                                value="{{ old('prenom', $userPrenom) }}" placeholder="Prénoms tels qu'indiqués sur l'acte">
                        </div>
                        @error('prenom') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label for="number" class="form-label">Numéro de registre :</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-hashtag"></i>
                            <input type="text" id="number" name="number" class="form-control" value="{{ old('number') }}"
                                placeholder="Ex: 123/2024">
                        </div>
                        @error('number') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-col">
                        <label for="DateR" class="form-label">Date de registre :</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-calendar-check"></i>
                            <input type="date" id="DateR" name="DateR" class="form-control" value="{{ old('DateR') }}">
                        </div>
                        @error('DateR') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <label for="commune" class="form-label">Commune de naissance :</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" id="commune" name="commune" class="form-control" value="Plateau" readonly>
                        </div>
                    </div>

                    <div class="form-col">
                        <label for="quantite" class="form-label">Quantité d'exemplaires :</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-copy"></i>
                            <input type="number" id="quantite" name="quantite" class="form-control"
                                value="{{ old('quantite', 1) }}" min="1" max="10">
                        </div>
                        @error('quantite') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Pièce jointe -->
            <div class="section-card">
                <h3 class="section-title"><i class="fas fa-paperclip"></i> Justificatif d'identité</h3>
                <div class="form-group">
                    <label for="CNI" class="form-label">Pièce d'identité (CNI, Passeport ou Page du livret de famille)
                        :</label>
                    <div class="file-input-container">
                        <label class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span id="file-name">Cliquez pour choisir un fichier</span>
                            <input type="file" id="CNI" name="CNI" class="form-control d-none"
                                onchange="updateFileName(this)" accept=".jpg,.jpeg,.png,.pdf">
                        </label>
                    </div>
                    @error('CNI') <span class="error-message">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Section: Mode de retrait -->
            <div class="section-card">
                <h3 class="section-title"><i class="fas fa-shipping-fast"></i> Mode de retrait</h3>
                <div class="radio-grid">
                    <div class="radio-card">
                        <input type="radio" id="option1" name="choix_option" value="Retrait sur place" checked>
                        <label for="option1">
                            <i class="fas fa-building"></i>
                            <span>Retrait sur place</span>
                            <small>À la mairie (Gratuit)</small>
                        </label>
                    </div>
                    <div class="radio-card">
                        <input type="radio" id="option2" name="choix_option" value="livraison">
                        <label for="option2">
                            <i class="fas fa-truck"></i>
                            <span>Livraison à domicile</span>
                            <small>Frais : 1500 FCFA</small>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" id="btnValider" class="btn-submit">
                <i class="fas fa-check-circle"></i> Soumettre ma demande
            </button>
        </form>
    </div>

    <script>
        let formSubmitted = false;

        function updateFields() {
            const pourSelect = document.getElementById('pour');
            const nameInput = document.getElementById('name');
            const prenomInput = document.getElementById('prenom');
            // const communeSelect = document.getElementById('commune'); // La commune est en readonly 'plateau'
            const CMUInput = document.getElementById('CMU');

            if (pourSelect.value === 'Moi') {
                nameInput.value = '{{ $userName }}';
                prenomInput.value = '{{ $userPrenom }}';
                // Étant donné que la commune est en lecture seule et fixée à 'plateau', cette partie est moins critique ici.
                // Si vous aviez une liste de communes, vous devriez faire une logique pour sélectionner celle de l'utilisateur.
                // const userCommune = '{{ $userCommune }}';
                // if (userCommune) {
                //     for (let i = 0; i < communeSelect.options.length; i++) {
                //         if (communeSelect.options[i].value === userCommune) {
                //             communeSelect.selectedIndex = i;
                //             break;
                //         }
                //     }
                // }
                // CMUInput.value = '{{ $userCMU }}'; // CMU est optionnel, pas besoin de le réinitialiser si vide
            } else {
                nameInput.value = '';
                prenomInput.value = '';
                // communeSelect.selectedIndex = 0; // Pas nécessaire si 'plateau' est toujours sélectionné
                // CMUInput.value = ''; // Pas nécessaire si CMU est optionnel
            }
        }

        function updateFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : 'Aucun fichier sélectionné';
            document.getElementById('file-name').textContent = fileName;
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateFields();

            // Animation pour les champs
            // Note: Ici, je commente la partie qui ajoute l'animation 'pulse' à tous les champs 'required'.
            // Nous allons plutôt l'appliquer uniquement aux champs qui échouent la validation pour une meilleure UX.
            // const requiredFields = document.querySelectorAll('input[required], select[required]');
            // requiredFields.forEach(field => {
            //     field.addEventListener('focus', function() {
            //         this.style.animation = 'pulse 1.5s infinite';
            //     });
            //     field.addEventListener('blur', function() {
            //         this.style.animation = 'none';
            //     });
            // });
        });

        // Nouvelle fonction de validation côté client
        function validateFormClient() {
            let isValid = true;
            const form = document.getElementById('naissanceForm');

            // Champs à valider (basés sur vos règles de validation Laravel)
            const fieldsToValidate = [
                { id: 'type', message: 'Le type d\'extrait est obligatoire.' },
                { id: 'name', message: 'Le nom est obligatoire.' },
                { id: 'prenom', message: 'Le prénom est obligatoire.' },
                { id: 'number', message: 'Le numéro de registre est obligatoire.' },
                { id: 'DateR', message: 'La date de registre est obligatoire.' },
                { id: 'commune', message: 'La commune est obligatoire.' },
                { id: 'quantite', message: 'La quantité est obligatoire.' },
                { id: 'CNI', message: 'Une pièce d\'identité (CNI/Passeport) est obligatoire.' }
            ];

            // Retirer les messages d'erreur existants et les styles d'erreur
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('.form-control.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.form-control').forEach(el => el.style.animation = 'none');


            fieldsToValidate.forEach(fieldInfo => {
                const inputElement = document.getElementById(fieldInfo.id);
                if (inputElement) {
                    if (fieldInfo.id === 'quantite') {
                        const quantite = parseInt(inputElement.value);
                        if (!quantite || quantite < 1 || quantite > 10) {
                            isValid = false;
                            displayClientError(inputElement, 'La quantité doit être entre 1 et 10.');
                        }
                    }
                    // Pour les fichiers, nous vérifions si un fichier a été sélectionné
                    else if (fieldInfo.id === 'CNI') {
                        if (inputElement.files.length === 0) {
                            isValid = false;
                            displayClientError(inputElement, fieldInfo.message);
                        } else {
                            // Optionnel: Ajouter ici une validation pour le type de fichier et la taille
                            const file = inputElement.files[0];
                            const allowedTypes = ['image/png', 'image/jpeg', 'application/pdf'];
                            const maxSize = 1000 * 1024; // 1 Mo

                            if (!allowedTypes.includes(file.type)) {
                                isValid = false;
                                displayClientError(inputElement, 'Le format du fichier doit être PNG, JPG, JPEG ou PDF.');
                            } else if (file.size > maxSize) {
                                isValid = false;
                                displayClientError(inputElement, 'Le fichier ne doit pas dépasser 1Mo.');
                            }
                        }
                    } else if (!inputElement.value.trim()) { // Pour les autres champs, vérifier si vide
                        isValid = false;
                        displayClientError(inputElement, fieldInfo.message);
                    }
                }
            });
            return isValid;
        }

        // Fonction pour afficher les erreurs côté client
        function displayClientError(inputElement, message) {
            inputElement.classList.add('is-invalid'); // Pour un style visuel d'erreur
            inputElement.style.animation = 'pulse 1.5s infinite'; // Appliquer l'animation si erreur
            const errorSpan = document.createElement('span');
            errorSpan.classList.add('error-message');
            errorSpan.textContent = message;
            // Insérer le message d'erreur après le champ ou son conteneur parent
            if (inputElement.closest('.form-col')) {
                inputElement.closest('.form-col').appendChild(errorSpan);
            } else {
                inputElement.parentNode.insertBefore(errorSpan, inputElement.nextSibling);
            }
        }


        document.getElementById('naissanceForm').addEventListener('submit', function (event) {
            if (formSubmitted) {
                event.preventDefault();
                return;
            }

            const livraisonCheckbox = document.getElementById('option2');
            if (livraisonCheckbox.checked) {
                event.preventDefault(); // Empêche la soumission initiale

                if (validateFormClient()) { // Valider le formulaire côté client
                    showPaymentPopup(); // Si validation réussie, afficher la popup de paiement
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur de Validation',
                        text: 'Veuillez corriger les erreurs dans le formulaire avant de continuer.',
                        confirmButtonColor: '#e74c3c'
                    });
                }
            } else {
                formSubmitted = true;
                this.submit(); // Soumettre le formulaire directement si 'Retrait sur place'
            }
        });

        function showPaymentPopup() {
            // Récupérer la quantité depuis le formulaire
            const quantite = parseInt(document.getElementById('quantite').value) || 1;
            const montantTimbreUnitaire = 50; // 50 FCFA par timbre
            const montantTimbreTotal = montantTimbreUnitaire * quantite;
            const montantLivraison = 50; // 1500 FCFA pour la livraison

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
                            <!-- Colonne 1: Destinataire -->
                            <div>
                                <h4 style="margin-bottom: 1.2rem; color: #1e293b; font-size: 1rem; border-left: 4px solid #1977cc; padding-left: 10px; border-radius: 2px;">
                                    <i class="fas fa-user-check" style="color: #1977cc; margin-right: 8px;"></i>Coordonnées du Destinataire
                                </h4>
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">NOM DU DESTINATAIRE</label>
                                    <div style="position: relative;">
                                        <i class="fas fa-user" style="position: absolute; left: 12px; top: 38px; color: #94a3b8;"></i>
                                        <input id="swal-nom_destinataire" class="swal2-input" placeholder="Ex: Kouassi" style="width: 100%; margin: 0; padding-left: 35px; height: 45px; font-size: 0.95rem;">
                                    </div>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">PRÉNOM DU DESTINATAIRE</label>
                                    <div style="position: relative;">
                                        <i class="fas fa-user" style="position: absolute; left: 12px; top: 38px; color: #94a3b8;"></i>
                                        <input id="swal-prenom_destinataire" class="swal2-input" placeholder="Ex: Amenan" style="width: 100%; margin: 0; padding-left: 35px; height: 45px; font-size: 0.95rem;">
                                    </div>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">EMAIL</label>
                                    <div style="position: relative;">
                                        <i class="fas fa-envelope" style="position: absolute; left: 12px; top: 38px; color: #94a3b8;"></i>
                                        <input id="swal-email_destinataire" type="email" class="swal2-input" placeholder="Ex: contact@email.com" style="width: 100%; margin: 0; padding-left: 35px; height: 45px; font-size: 0.95rem;">
                                    </div>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">TÉLÉPHONE</label>
                                    <div style="position: relative;">
                                        <i class="fas fa-phone-alt" style="position: absolute; left: 12px; top: 38px; color: #94a3b8;"></i>
                                        <input id="swal-contact_destinataire" type="tel" class="swal2-input" placeholder="Ex: 0708091011" style="width: 100%; margin: 0; padding-left: 35px; height: 45px; font-size: 0.95rem;">
                                    </div>
                                </div>
                            </div>

                            <!-- Colonne 2: Lieu de livraison -->
                            <div>
                                <h4 style="margin-bottom: 1.2rem; color: #1e293b; font-size: 1rem; border-left: 4px solid #2c7873; padding-left: 10px; border-radius: 2px;">
                                    <i class="fas fa-map-marked-alt" style="color: #2c7873; margin-right: 8px;"></i>Lieu de Livraison
                                </h4>
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">VILLE</label>
                                    <div style="position: relative;">
                                        <i class="fas fa-city" style="position: absolute; left: 12px; top: 38px; color: #94a3b8;"></i>
                                        <input id="swal-ville" class="swal2-input" placeholder="Ex: Abidjan" style="width: 100%; margin: 0; padding-left: 35px; height: 45px; font-size: 0.95rem;">
                                    </div>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">COMMUNE</label>
                                    <div style="position: relative;">
                                        <i class="fas fa-map-marker-alt" style="position: absolute; left: 12px; top: 38px; color: #94a3b8;"></i>
                                        <input id="swal-commune_livraison" class="swal2-input" placeholder="Ex: Plateau" style="width: 100%; margin: 0; padding-left: 35px; height: 45px; font-size: 0.95rem;">
                                    </div>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">QUARTIER / PRÉCISIONS</label>
                                    <div style="position: relative;">
                                        <i class="fas fa-location-arrow" style="position: absolute; left: 12px; top: 38px; color: #94a3b8;"></i>
                                        <input id="swal-quartier" class="swal2-input" placeholder="Ex: Cité des arts" style="width: 100%; margin: 0; padding-left: 35px; height: 45px; font-size: 0.95rem;">
                                    </div>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #64748b; margin-bottom: 5px;">ADRESSE EXACTE</label>
                                    <div style="position: relative;">
                                        <i class="fas fa-home" style="position: absolute; left: 12px; top: 38px; color: #94a3b8;"></i>
                                        <input id="swal-adresse_livraison" class="swal2-input" placeholder="Ex: Rue 12, Près de l'église" style="width: 100%; margin: 0; padding-left: 35px; height: 45px; font-size: 0.95rem;">
                                    </div>
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
                    const nom_destinataire = document.getElementById('swal-nom_destinataire').value;
                    const prenom_destinataire = document.getElementById('swal-prenom_destinataire').value;
                    const email_destinataire = document.getElementById('swal-email_destinataire').value;
                    const contact_destinataire = document.getElementById('swal-contact_destinataire').value;
                    const adresse_livraison = document.getElementById('swal-adresse_livraison').value;
                    const ville = document.getElementById('swal-ville').value;
                    const commune_livraison = document.getElementById('swal-commune_livraison').value;
                    const quartier = document.getElementById('swal-quartier').value;

                    if (!nom_destinataire || !prenom_destinataire || !email_destinataire || !contact_destinataire || !adresse_livraison || !ville || !commune_livraison || !quartier) {
                        Swal.showValidationMessage("Veuillez remplir tous les champs obligatoires");
                        return false;
                    }
                    // Validation d'email simple
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email_destinataire)) {
                        Swal.showValidationMessage("Veuillez entrer une adresse email valide.");
                        return false;
                    }
                    // Validation de numéro de téléphone (simple, peut être plus complexe si nécessaire)
                    if (!/^\d{8,15}$/.test(contact_destinataire)) {
                        Swal.showValidationMessage("Veuillez entrer un numéro de téléphone valide (8 à 15 chiffres).");
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
                        quantite: quantite,
                        montant_timbre_unitaire: montantTimbreUnitaire,
                        montant_timbre: montantTimbreTotal,
                        montant_livraison: montantLivraison,
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = result.value;
                    initializeCinetPay(formData);
                } else if (result.dismiss === Swal.DismissReason.cancel) {
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

            // Montant total (timbre total + livraison)
            const totalAmount = formData.montant_timbre + formData.montant_livraison;

            // Chargement
            Swal.fire({
                title: 'Redirection en cours',
                html: `Préparation du paiement de ${totalAmount} FCFA...`,
                allowOutsideClick: true,
                didOpen: () => Swal.showLoading()
            });

            // Données client
            const customer = {
                name: '{{ Auth::user()->name ?? "Client" }}',
                email: '{{ Auth::user()->email ?? "contact@client.com" }}',
                phone: '{{ Auth::user()->telephone ?? "00000000" }}'
            };

            // Description détaillée avec la quantité
            const description = `Paiement pour ${formData.quantite} exemplaire(s) d'extrait de naissance (Timbre: ${formData.montant_timbre} FCFA + Livraison: ${formData.montant_livraison} FCFA)`;

            // Paiement
            CinetPay.getCheckout({
                transaction_id: transactionId,
                amount: totalAmount,
                currency: 'XOF',
                channels: 'ALL',
                description: description,
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
            CinetPay.waitResponse(function (data) {
                Swal.close();
                if (data.status === "ACCEPTED") {
                    // Ajouter les données de livraison au formulaire
                    const form = document.getElementById('naissanceForm');

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
                        { name: 'montant_timbre_unitaire', value: formData.montant_timbre_unitaire },
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
                        text: data.message || 'Une erreur est survenue lors du traitement de votre paiement. Veuillez réessayer.'
                    });
                }
            });

            // Gestion erreurs
            CinetPay.onError(function (error) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur de connexion',
                    html: `Une erreur est survenue lors de la connexion à CinetPay.<br><small>${error.message || 'Veuillez vérifier votre connexion et réessayer.'}</small>`
                });
            });
        }
    </script>
@endsection