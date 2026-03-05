@extends('user.layouts.template')
@section('content')

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        .death-certificate-container {
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

        .death-certificate-title {
            text-align: center;
            color: var(--primary);
            margin-bottom: 2.5rem;
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .death-certificate-title::after {
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
    /* Nouveau style pour les options de retrait cards premium */
    .delivery-options-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .delivery-card {
        position: relative;
        cursor: pointer;
    }

    .delivery-card input {
        position: absolute;
        opacity: 0;
    }

    .delivery-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s;
        height: 100%;
        text-align: center;
    }

    .delivery-label .icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: #1977cc;
    }

    .delivery-name {
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 0.25rem;
        color: #2c3e50;
    }

    .delivery-info {
        font-size: 0.8rem;
        color: #718096;
    }

    .delivery-card input:checked + .delivery-label {
        border-color: #1977cc;
        background-color: #f0f7ff;
        box-shadow: 0 0 0 1px #1977cc;
    }

    .section-header {
        display: flex;
        align-items: center;
        margin: 2rem 0 1rem;
        border-bottom: 1px solid #edf2f7;
        padding-bottom: 0.5rem;
    }

    .section-header i {
        color: #1977cc;
        margin-right: 0.75rem;
    }

    .section-header h3 {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1977cc;
        text-transform: uppercase;
        margin: 0;
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

        .submit-btn i {
            font-size: 1.2rem;
        }

        .error-message {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 5px;
            font-weight: 500;
        }

        /* Custom File Upload */
        .file-upload-wrapper {
            position: relative;
            width: 100%;
        }

        .file-upload-label {
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

        .file-upload-label:hover {
            border-color: var(--primary);
            background: var(--primary-soft);
            color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .death-certificate-container {
                padding: 1.5rem;
                margin: 1rem;
            }

            .form-grid,
            .radio-grid {
                grid-template-columns: 1fr;
            }

            .death-certificate-title {
                font-size: 1.8rem;
            }
        }
    </style>

    <div class="death-certificate-container">
        <h1 class="death-certificate-title">Demande d'acte de décès</h1>

        <form id="deathForm" method="POST" enctype="multipart/form-data"
            action="{{route('user.extrait.deces.store')}}">
            @csrf

            <!-- Section: Informations du défunt -->
            <div class="section-card">
                <h3 class="section-title"><i class="fas fa-user-ghost"></i> Informations du défunt</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="type" class="form-label">Type de document</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-file-alt"></i>
                            <select id="type" name="type" class="form-control">
                                <option value="" disabled selected>Choisir le type de document</option>
                                <option value="simple" {{ old('type') == 'simple' ? 'selected' : '' }}>Copie simple</option>
                                <option value="integral" {{ old('type') == 'integral' ? 'selected' : '' }}>Copie intégrale
                                </option>
                            </select>
                        </div>
                        @error('type') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="name" class="form-label">Nom et Prénoms du Défunt</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="name" name="name" class="form-control" value="{{old('name')}}"
                                placeholder="Ex : Jean Philippe Kouassi">
                        </div>
                        @error('name') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="numberR" class="form-label">Numéro de Registre</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-file-invoice"></i>
                            <input type="text" id="numberR" name="numberR" class="form-control" value="{{old('numberR')}}"
                                placeholder="Ex : 1234/2024">
                        </div>
                        @error('numberR') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="dateR" class="form-label">Date de Registre</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-calendar-alt"></i>
                            <input type="date" id="dateR" name="dateR" class="form-control" value="{{old('dateR')}}">
                        </div>
                        @error('dateR') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <!-- Informations Parentales -->
                    <div class="form-group">
                        <label for="nom_prenoms_pere" class="form-label">Nom et Prénoms du père</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="nom_prenoms_pere" name="nom_prenoms_pere" class="form-control"
                                value="{{old('nom_prenoms_pere')}}" placeholder="Nom et Prénoms du père">
                        </div>
                        @error('nom_prenoms_pere') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="nom_prenoms_mere" class="form-label">Nom et Prénoms de la mère</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" id="nom_prenoms_mere" name="nom_prenoms_mere" class="form-control"
                                value="{{old('nom_prenoms_mere')}}" placeholder="Nom et Prénoms de la mère">
                        </div>
                        @error('nom_prenoms_mere') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="communeD" class="form-label">Commune</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" id="communeD" name="communeD" class="form-control" value="Plateau" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="quantite" class="form-label">Quantité d'exemplaires</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-copy"></i>
                            <input type="number" id="quantite" name="quantite" class="form-control"
                                value="{{ old('quantite', 1) }}" min="1" max="10">
                        </div>
                        @error('quantite') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Pièces jointes -->
            <div class="section-card">
                <h3 class="section-title"><i class="fas fa-paperclip"></i> Documents justificatifs</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="CNIdfnt" class="form-label">CNI ou Acte de naissance du défunt</label>
                        <input type="file" name="CNIdfnt" id="CNIdfnt" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        @error('CNIdfnt') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label for="CNIdcl" class="form-label">Certificat médical de décès</label>
                        <input type="file" name="CNIdcl" id="CNIdcl" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        @error('CNIdcl') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Mariage & Infos Spécifiques -->
            <div class="section-card">
                <div class="row">
                    <div class="col-md-6 border-right">
                        <h5 class="form-label mb-3"><i class="fas fa-ring me-2"></i>Le défunt était-il marié ?</h5>
                        <div class="radio-grid" style="grid-template-columns: repeat(2, 1fr);">
                            <div class="radio-card">
                                <input type="radio" id="non" name="married" value="non" checked>
                                <label for="non"><span>Non</span></label>
                            </div>
                            <div class="radio-card">
                                <input type="radio" id="oui" name="married" value="oui">
                                <label for="oui"><span>Oui</span></label>
                            </div>
                        </div>
                        <div id="married-file-inputs-container" class="mt-3"></div>
                    </div>
                    <div class="col-md-6">
                        <h5 class="form-label mb-3"><i class="fas fa-hospital-user me-2"></i>Décès hors centre de santé ?
                        </h5>
                        <div class="radio-grid" style="grid-template-columns: repeat(2, 1fr);">
                            <div class="radio-card">
                                <input type="radio" id="nonHorsS" name="DecesHorsS" value="non" checked>
                                <label for="nonHorsS"><span>Non</span></label>
                            </div>
                            <div class="radio-card">
                                <input type="radio" id="ouiHorsS" name="DecesHorsS" value="oui">
                                <label for="ouiHorsS"><span>Oui</span></label>
                            </div>
                        </div>
                        <div id="deces-file-inputs-container" class="mt-3"></div>
                    </div>
                </div>
            </div>

            <!-- Section: Mode de retrait -->
            <div class="delivery-options" id="optionsSection">
                <div class="section-header">
                    <i class="fas fa-truck text-primary"></i>
                    <h3 class="section-title">Mode de retrait</h3>
                </div>
                <div class="delivery-options-cards">
                    <label class="delivery-card">
                        <input type="radio" id="option1" name="choix_option" value="Retrait sur place" checked>
                        <div class="delivery-label">
                            <i class="fas fa-building icon"></i>
                            <span class="delivery-name">Retrait sur place</span>
                            <span class="delivery-info">À la mairie (Gratuit)</span>
                        </div>
                    </label>
                    <label class="delivery-card">
                        <input type="radio" id="option2" name="choix_option" value="livraison">
                        <div class="delivery-label">
                            <i class="fas fa-truck icon"></i>
                            <span class="delivery-name">Livraison à domicile</span>
                            <span class="delivery-info">Frais : 1500 FCFA</span>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-check-circle"></i> Valider ma demande
            </button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let formSubmitted = false;
        let submitAfterPopup = false;

        // Function to display client-side validation errors
        function displayClientError(inputElement, message) {
            // Remove existing error messages for this input
            $(inputElement).removeClass('is-invalid').next('.error-message').remove();
            $(inputElement).css('animation', 'none'); // Remove previous animation

            if (message) {
                $(inputElement).addClass('is-invalid'); // Add class for visual styling if needed
                $(inputElement).css('animation', 'pulse 1.5s infinite'); // Apply animation
                const errorSpan = `<span class="error-message">${message}</span>`;
                $(inputElement).after(errorSpan);
            }
        }

        // Client-side validation function
        function validateFormClient() {
            let isValid = true;
            const form = $("#deathForm");

            // Clear all previous error messages and styles
            form.find('.error-message').remove();
            form.find('.form-control').removeClass('is-invalid').css('animation', 'none');

            // Fields to validate based on your Laravel validation rules
            const fieldsToValidate = [
                { id: 'type', message: 'Le type de document est obligatoire.' },
                { id: 'name', message: 'Le nom du défunt est obligatoire.' },
                { id: 'quantite', message: 'La quantité est obligatoire.' },
                { id: 'CNIdfnt', message: 'La CNI/extrait de naissance du défunt(e) est obligatoire.' },
                { id: 'CNIdcl', message: 'Le certificat médical de décès est obligatoire.' }
            ];

            // Validation conditionnelle : Registre OU Parents
            const numberR = document.getElementById('numberR').value.trim();
            const dateR = document.getElementById('dateR').value.trim();
            const nomPrenomsPere = document.getElementById('nom_prenoms_pere').value.trim();
            const nomPrenomsMere = document.getElementById('nom_prenoms_mere').value.trim();

            const hasRegistryInfo = numberR && dateR;
            const hasParentInfo = nomPrenomsPere && nomPrenomsMere;

            if (!hasRegistryInfo && !hasParentInfo) {
                isValid = false;
                displayClientError(document.getElementById('numberR'), "Veuillez fournir soit les infos de registre (Numéro Et Date), soit les infos complètes des parents.");
                if (document.getElementById('nom_prenoms_pere')) displayClientError(document.getElementById('nom_prenoms_pere'), "Veuillez fournir soit les infos de registre, soit les infos complètes des parents.");
            }

            fieldsToValidate.forEach(fieldInfo => {
                const inputElement = document.getElementById(fieldInfo.id);
                if (!inputElement) return; // Skip if element not found
                if (fieldInfo.id === 'quantite') {
                    const quantite = parseInt(inputElement.value);
                    if (!quantite || quantite < 1 || quantite > 10) {
                        isValid = false;
                        displayClientError(inputElement, 'La quantité doit être entre 1 et 10.');
                    }
                }
                else if (inputElement.type === 'file') {
                    if (inputElement.files.length === 0) {
                        isValid = false;
                        displayClientError(inputElement, fieldInfo.message);
                    } else {
                        const file = inputElement.files[0];
                        const allowedTypes = ['image/png', 'image/jpeg', 'application/pdf'];
                        const maxSize = 1000 * 1024; // 1 MB

                        if (!allowedTypes.includes(file.type)) {
                            isValid = false;
                            displayClientError(inputElement, `Le format du fichier "${file.name}" doit être PNG, JPG, JPEG ou PDF.`);
                        } else if (file.size > maxSize) {
                            isValid = false;
                            displayClientError(inputElement, `Le fichier "${file.name}" ne doit pas dépasser 1Mo.`);
                        }
                    }
                } else if (!inputElement.value.trim()) {
                    isValid = false;
                    displayClientError(inputElement, fieldInfo.message);
                }
            });

            // Conditional validation for 'married' field
            const marriedRadio = $('input[name="married"]:checked').val();
            if (marriedRadio === 'oui') {
                const documentMariageInput = document.getElementById('documentMariage');
                if (documentMariageInput) { // Ensure the field exists if 'oui' is selected
                    if (documentMariageInput.files.length === 0) {
                        isValid = false;
                        displayClientError(documentMariageInput, 'Le document de mariage est obligatoire.');
                    } else {
                        const file = documentMariageInput.files[0];
                        const allowedTypes = ['image/png', 'image/jpeg', 'application/pdf'];
                        const maxSize = 1000 * 1024; // 1 MB
                        if (!allowedTypes.includes(file.type)) {
                            isValid = false;
                            displayClientError(documentMariageInput, `Le format du fichier "${file.name}" doit être PNG, JPG, JPEG ou PDF.`);
                        } else if (file.size > maxSize) {
                            isValid = false;
                            displayClientError(documentMariageInput, `Le fichier "${file.name}" ne doit pas dépasser 1Mo.`);
                        }
                    }
                } else {
                    isValid = false; // If 'oui' is selected but input not rendered, it's an error
                    // This case should ideally not happen if toggleMarriedFields works correctly
                    console.error("documentMariage input not found when 'married' is 'oui'.");
                }
            }

            // Conditional validation for 'DecesHorsS' field
            const decesHorsSRadio = $('input[name="DecesHorsS"]:checked').val();
            if (decesHorsSRadio === 'oui') {
                const requisPoliceInput = document.getElementById('RequisPolice');
                if (requisPoliceInput) { // Ensure the field exists if 'oui' is selected
                    if (requisPoliceInput.files.length === 0) {
                        isValid = false;
                        displayClientError(requisPoliceInput, 'La réquisition de la police est obligatoire.');
                    } else {
                        const file = requisPoliceInput.files[0];
                        const allowedTypes = ['image/png', 'image/jpeg', 'application/pdf'];
                        const maxSize = 1000 * 1024; // 1 MB
                        if (!allowedTypes.includes(file.type)) {
                            isValid = false;
                            displayClientError(requisPoliceInput, `Le format du fichier "${file.name}" doit être PNG, JPG, JPEG ou PDF.`);
                        } else if (file.size > maxSize) {
                            isValid = false;
                            displayClientError(requisPoliceInput, `Le fichier "${file.name}" ne doit pas dépasser 1Mo.`);
                        }
                    }
                } else {
                    isValid = false; // If 'oui' is selected but input not rendered, it's an error
                    console.error("RequisPolice input not found when 'DecesHorsS' is 'oui'.");
                }
            }

            return isValid;
        }


        $(document).ready(function () {
            // Section de retrait visible par défaut
            $("#optionsSection").show();

            // ==============================
            // MARIAGE
            // ==============================
            const marriedFileInputsContainer = $("#married-file-inputs-container");
            // Added `required` attribute here
            const marriageFields = `
                                            <div class="form-group">
                                                <label for="documentMariage" class="form-label">Photocopie de document de mariage pour le défunt(e)</label>
                                                <input type="file" id="documentMariage" name="documentMariage" class="form-control" required>
                                            </div>
                                        `;

            function toggleMarriedFields() {
                if ($('input[name="married"]:checked').val() === 'oui') {
                    marriedFileInputsContainer.html(marriageFields);
                } else {
                    marriedFileInputsContainer.empty();
                }
            }

            $(document).on('change', 'input[name="married"]', toggleMarriedFields);
            toggleMarriedFields(); // Vérifie au chargement

            // ==============================
            // DÉCÈS HORS CENTRE
            // ==============================
            const decesFileInputsContainer = $("#deces-file-inputs-container");
            // Added `required` attribute here
            const decesFields = `
                                            <div class="form-group">
                                                <label for="RequisPolice" class="form-label">Photocopie de la réquisition de la police</label>
                                                <input type="file" id="RequisPolice" name="RequisPolice" class="form-control" required>
                                            </div>
                                        `;

            function toggleDecesFields() {
                if ($('input[name="DecesHorsS"]:checked').val() === 'oui') {
                    decesFileInputsContainer.html(decesFields);
                } else {
                    decesFileInputsContainer.empty();
                }
            }

            $(document).on('change', 'input[name="DecesHorsS"]', toggleDecesFields);
            toggleDecesFields(); // Vérifie au chargement

            // ==============================
            // SOUMISSION FORMULAIRE
            // ==============================
            $("#deathForm").submit(function (event) {
                if (formSubmitted) {
                    event.preventDefault();
                    return;
                }

                const livraisonCheckbox = $("#option2");
                if (livraisonCheckbox.is(":checked") && !submitAfterPopup) {
                    event.preventDefault(); // Prevent default submission initially

                    if (validateFormClient()) { // Perform client-side validation
                        showLivraisonPopup(); // If valid, show payment popup
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur de Validation',
                            text: 'Veuillez corriger les erreurs dans le formulaire avant de continuer.',
                            confirmButtonColor: '#e74c3c'
                        });
                    }
                } else {
                    // If "Retrait sur place" or form is being submitted after successful payment
                    formSubmitted = true;
                    // Form will naturally submit if preventDefault was not called,
                    // or will be explicitly submitted by initializeCinetPay
                }
            });
        });

        // ==============================
        // POPUP LIVRAISON
        // ==============================
        function showLivraisonPopup() {
            const quantite = parseInt(document.getElementById('quantite').value) || 1;
            const montantTimbreUnitaire = 500; 
            const montantTimbreTotal = montantTimbreUnitaire * quantite;
            const montantLivraison = 1500; 

            Swal.fire({
                title: '<div class="flex items-center justify-center p-2"><i class="fas fa-truck text-primary mr-2"></i> <span style="font-size: 1.2rem; font-weight: 800; color: #1977cc;">DÉTAILS DE LIVRAISON</span></div>',
                html: `
                    <div class="text-left" style="max-height: 70vh; overflow-y: auto; padding: 10px;">
                        <p class="text-center text-muted mb-3" style="font-size: 0.9rem;">Veuillez confirmer vos informations pour la livraison sécurisée de votre acte de décès.</p>
                        
                        <div style="background: #f0f7ff; padding: 15px; border-radius: 12px; border: 1px solid #cce3f6; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                <span style="color: #555; font-size: 0.85rem;">Nombre d'exemplaires:</span>
                                <span style="font-weight: 700;">${quantite}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                <span style="color: #555; font-size: 0.85rem;">Frais de timbre:</span>
                                <span style="font-weight: 700;">${montantTimbreTotal} FCFA</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="color: #555; font-size: 0.85rem;">Frais de livraison:</span>
                                <span style="font-weight: 700;">${montantLivraison} FCFA</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 2px dashed #b8d4ed; pt-2; margin-top: 10px;">
                                <span style="color: #1977cc; font-weight: 800;">TOTAL À PAYER:</span>
                                <span style="color: #1977cc; font-weight: 800; font-size: 1.2rem;">${montantTimbreTotal + montantLivraison} FCFA</span>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div style="text-align: left;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase;">Nom</label>
                                <input id="swal-nom_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 10px; height: 40px; font-size: 0.9rem; border-radius: 8px;" placeholder="Nom" value="{{ Auth::user()->name }}">
                            </div>
                            <div style="text-align: left;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase;">Prénom</label>
                                <input id="swal-prenom_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 10px; height: 40px; font-size: 0.9rem; border-radius: 8px;" placeholder="Prénom" value="{{ Auth::user()->prenom }}">
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                            <div style="text-align: left;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase;">Email</label>
                                <input id="swal-email_destinataire" type="email" class="swal2-input" style="width: 100%; margin: 0; padding: 10px; height: 40px; font-size: 0.9rem; border-radius: 8px;" placeholder="mail@exemple.com" value="{{ Auth::user()->email }}">
                            </div>
                            <div style="text-align: left;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase;">Téléphone</label>
                                <input id="swal-contact_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 10px; height: 40px; font-size: 0.9rem; border-radius: 8px;" placeholder="0123456789" value="{{ Auth::user()->contact }}">
                            </div>
                        </div>

                        <div style="margin-top: 15px;">
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase;">Adresse précise</label>
                            <input id="swal-adresse_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 10px; height: 40px; font-size: 0.9rem; border-radius: 8px;" placeholder="N° de rue, immuable, appartement...">
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-top: 15px;">
                            <div style="text-align: left;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase;">Ville</label>
                                <input id="swal-ville" class="swal2-input" style="width: 100%; margin: 0; padding: 8px; height: 35px; font-size: 0.85rem; border-radius: 8px;" placeholder="Ville" value="Abidjan">
                            </div>
                            <div style="text-align: left;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase;">Commune</label>
                                <input id="swal-commune_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 8px; height: 35px; font-size: 0.85rem; border-radius: 8px;" placeholder="Commune">
                            </div>
                            <div style="text-align: left;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #555; margin-bottom: 5px; text-transform: uppercase;">Quartier</label>
                                <input id="swal-quartier" class="swal2-input" style="width: 100%; margin: 0; padding: 8px; height: 35px; font-size: 0.85rem; border-radius: 8px;" placeholder="Quartier">
                            </div>
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: `Payer via Wave`,
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
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email_destinataire)) {
                        Swal.showValidationMessage("Veuillez entrer une adresse email valide.");
                        return false;
                    }
                    if (!/^\d{8,15}$/.test(contact_destinataire)) {
                        Swal.showValidationMessage("Veuillez entrer un numéro de téléphone valide (8 à 15 chiffres).");
                        return false;
                    }

                    return {
                        nom_destinataire, prenom_destinataire, email_destinataire, contact_destinataire, adresse_livraison, ville, commune_livraison, quartier,
                        quantite, montantTimbreTotal, montantLivraison
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = result.value;
                    const form = document.getElementById('deathForm');
                    const hiddenFields = [
                        { name: 'nom_destinataire', value: formData.nom_destinataire },
                        { name: 'prenom_destinataire', value: formData.prenom_destinataire },
                        { name: 'email_destinataire', value: formData.email_destinataire },
                        { name: 'contact_destinataire', value: formData.contact_destinataire },
                        { name: 'adresse_livraison', value: formData.adresse_livraison },
                        { name: 'ville', value: formData.ville },
                        { name: 'commune_livraison', value: formData.commune_livraison },
                        { name: 'quartier', value: formData.quartier },
                        { name: 'montant_timbre_unitaire', value: 500 },
                        { name: 'montant_timbre', value: formData.montantTimbreTotal },
                        { name: 'montant_livraison', value: formData.montantLivraison }
                    ];

                    hiddenFields.forEach(field => {
                        const input = document.createElement('input');
                        input.type = 'hidden'; input.name = field.name; input.value = field.value;
                        form.appendChild(input);
                    });

                    Swal.fire({
                        title: 'Redirection vers Wave',
                        html: `Préparation du paiement de ${formData.montantTimbreTotal + formData.montantLivraison} FCFA...`,
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    formSubmitted = true;
                    form.submit();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    document.getElementById('option1').checked = true;
                }
            });
        }
    </script>
@endsection