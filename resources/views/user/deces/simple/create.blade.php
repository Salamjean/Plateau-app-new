@extends('user.layouts.template')
@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.cinetpay.com/seamless/main.js"></script>
<script src="{{ asset('js/cinetpay_deces_deja.js') }}"></script>

<style>
    .death-certificate-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(8px);
        padding: 2.5rem;
        max-width: 1000px;
        margin: 2rem auto;
        animation: fadeIn 0.6s ease-out;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .death-certificate-title {
        text-align: center;
        color: #1977cc;
        margin-bottom: 2rem;
        font-size: 2rem;
        font-weight: 600;
        position: relative;
        padding-bottom: 1rem;
    }

    .death-certificate-title::after {
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
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group.full-width {
        grid-column: span 2;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #1977cc;
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background-color: #f8fafc;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .form-control:focus {
        outline: none;
        border-color: #1977cc;
        box-shadow: 0 0 0 3px rgba(26, 92, 88, 0.1);
        background-color: #fff;
    }

    .radio-group {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .radio-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-weight: 500;
        color: #4a5568;
    }

    .radio-input {
        width: 18px;
        height: 18px;
        accent-color: #1977cc;
    }

    .submit-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #1977cc, #1977cc);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .submit-btn:hover {
        background: linear-gradient(135deg, #1569b2, #1569b2);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .error-message {
        color: #e53e3e;
        font-size: 0.85rem;
        margin-top: 0.25rem;
        display: block;
    }

    .conditional-section {
        background: #f7fafc;
        padding: 1.5rem;
        border-radius: 10px;
        margin: 1rem 0;
        border-left: 4px solid #1977cc;
    }

    .conditional-title {
        font-weight: 600;
        color: #1977cc;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .delivery-options {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
    }

    .options-title {
        text-align: center;
        font-weight: 600;
        color: #1977cc;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .options-title::before {
        content: '';
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, #1977cc, #3e8e41);
    }

    .options-grid {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-bottom: 1.5rem;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: span 1;
        }

        .death-certificate-container {
            padding: 1.5rem;
            margin: 1rem;
        }

        .death-certificate-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .options-grid {
            flex-direction: column;
            gap: 1rem;
        }
    }

    /* SweetAlert custom styles */
    .swal-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .swal2-input {
        width: 90% !important;
        margin: 0.5rem auto !important;
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
    .form-control:focus {
        animation: pulse 1.5s infinite;
    }

    /* Style pour la popup de livraison */
    .swal-delivery-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    @media (max-width: 640px) {
        .swal-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="death-certificate-container">
    <h1 class="death-certificate-title">Demande d'extrait de décès</h1>
    
    <form id="declarationForm" method="POST" enctype="multipart/form-data" action="{{route('user.extrait.deces.store')}}">
        @csrf
        
        <div class="form-grid">
            <div class="form-group">
                <label for="name" class="form-label">Nom et Prénoms du Défunt</label>
                <input type="text" id="name" name="name" class="form-control" value="{{old('name')}}" placeholder="Exemple : Jean Dupont">
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="numberR" class="form-label">Numéro de Registre</label>
                <input type="text" id="numberR" name="numberR" class="form-control" value="{{old('numberR')}}"  placeholder="Exemple : 123456">
                @error('numberR')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="dateR" class="form-label">Date de Registre</label>
                <input type="date" id="dateR" name="dateR" class="form-control" value="{{old('dateR')}}" >
                @error('dateR')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="communeD" class="form-label">Commune</label>
                <input type="text" id="communeD" name="communeD" class="form-control" value="plateau" readonly>
                @error('communeD')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="CNIdfnt" class="form-label">CNI/extrait de naissance du défunt(e)</label>
                <input type="file" name="CNIdfnt" id="CNIdfnt" class="form-control">
                @error('CNIdfnt')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="CNIdcl" class="form-label">Certificat médical de décès</label>
                <input type="file" name="CNIdcl" id="CNIdcl" class="form-control">
                @error('CNIdcl')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>
        
        <!-- Marriage conditional section -->
        <div class="conditional-section">
            <h3 class="conditional-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                </svg>
                Le défunt était-il marié ?
            </h3>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" id="oui" name="married" value="oui" class="radio-input">
                    Oui
                </label>
                <label class="radio-label">
                    <input type="radio" id="non" name="married" value="non" checked class="radio-input">
                    Non
                </label>
            </div>
            <div id="married-file-inputs-container"></div>
        </div>
        
        <!-- Death location conditional section -->
        <div class="conditional-section">
            <h3 class="conditional-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                </svg>
                Le défunt est-il décédé hors d'un centre de santé ?
            </h3>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" id="ouiHorsS" name="DecesHorsS" value="oui" class="radio-input">
                    Oui
                </label>
                <label class="radio-label">
                    <input type="radio" id="nonHorsS" name="DecesHorsS" value="non" checked class="radio-input">
                    Non
                </label>
            </div>
            <div id="deces-file-inputs-container"></div>
        </div>
        
        <!-- Delivery options -->
        <div class="delivery-options" id="optionsSection">
            <h3 class="options-title">Choisissez le mode de retrait</h3>
            <div class="options-grid">
                <label class="radio-label">
                    <input type="radio" id="option1" name="choix_option" value="Retrait sur place" checked class="radio-input">
                    Retrait sur place
                </label>
                <label class="radio-label">
                    <input type="radio" id="option2" name="choix_option" value="livraison" class="radio-input">
                    Livraison
                </label>
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
        
        <button type="submit" class="submit-btn">Valider la demande</button>
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
        const form = $("#declarationForm");

        // Clear all previous error messages and styles
        form.find('.error-message').remove();
        form.find('.form-control').removeClass('is-invalid').css('animation', 'none');

        // Fields to validate based on your Laravel validation rules
        const fieldsToValidate = [
            { id: 'name', message: 'Le nom du défunt est obligatoire.' },
            { id: 'numberR', message: 'Le numéro de registre est obligatoire.' },
            { id: 'dateR', message: 'La date de registre est obligatoire.' },
            { id: 'CNIdfnt', message: 'La CNI/extrait de naissance du défunt(e) est obligatoire.' },
            { id: 'CNIdcl', message: 'Le certificat médical de décès est obligatoire.' }
        ];

        fieldsToValidate.forEach(fieldInfo => {
            const inputElement = document.getElementById(fieldInfo.id);
            if (!inputElement) return; // Skip if element not found

            if (inputElement.type === 'file') {
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
        // Cache la section de retrait au départ
        $("#optionsSection").hide();

        // Check if all *currently visible and required* fields are filled
        function checkFieldsForOptionSection() {
            try {
                // Select all inputs that have a 'name' attribute, are currently visible, and not disabled.
                // We filter out radio buttons for 'married' and 'DecesHorsS' here because their 'required'
                // state is managed by their presence in the DOM based on radio selection, not a direct attribute.
                const relevantInputs = $("#declarationForm").find("input:not([type='radio']):not(:hidden):not(:disabled), select:not(:hidden):not(:disabled), input[type='file']:not(:hidden):not(:disabled)");

                let isFilled = true;

                relevantInputs.each(function() {
                    if ($(this).attr('required') && $(this).val().trim() === "") {
                        isFilled = false;
                        return false; // Break out of the loop early
                    }
                    if ($(this).attr('type') === 'file' && $(this).attr('required') && $(this)[0].files.length === 0) {
                         isFilled = false;
                         return false;
                    }
                });

                if (isFilled) {
                    $("#optionsSection").fadeIn();
                } else {
                    $("#optionsSection").hide();
                }
            } catch (error) {
                console.error("Error in checkFieldsForOptionSection:", error);
            }
        }

        // Listen for changes on all form inputs to trigger the check
        $("#declarationForm").on("input change", "input, select", checkFieldsForOptionSection);
        checkFieldsForOptionSection(); // Initial check on page load

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
                $('#documentMariage').on("input change", checkFieldsForOptionSection); // Add listener to new input
            } else {
                marriedFileInputsContainer.empty();
            }
            checkFieldsForOptionSection(); // Re-check visibility after toggling
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
                $('#RequisPolice').on("input change", checkFieldsForOptionSection); // Add listener to new input
            } else {
                decesFileInputsContainer.empty();
            }
            checkFieldsForOptionSection(); // Re-check visibility after toggling
        }

        $(document).on('change', 'input[name="DecesHorsS"]', toggleDecesFields);
        toggleDecesFields(); // Vérifie au chargement

        // ==============================
        // SOUMISSION FORMULAIRE
        // ==============================
        $("#declarationForm").submit(function(event) {
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
                // Basic email validation
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email_destinataire)) {
                    Swal.showValidationMessage("Veuillez entrer une adresse email valide.");
                    return false;
                }
                // Basic phone number validation (8 to 15 digits)
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
                    montant_timbre: parseFloat(montant_timbre),
                    montant_livraison: parseFloat(montant_livraison),
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = result.value;
                initializeCinetPay(formData); // Appel de la fonction CinetPay
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Si l'utilisateur clique sur annuler, sélectionner l'option "Retrait sur place"
                document.getElementById('option1').checked = true;
            }
        });
    }

   function initializeCinetPay(formData) {
    // Configuration CinetPay
    CinetPay.setConfig({
        apikey: '{{ config("services.cinetpay.api_key") }}',
        site_id: '{{ config("services.cinetpay.site_id") }}',
        mode: 'PRODUCTION' // Use 'PRODUCTION' for live, 'TEST' for testing
    });

    // ID de transaction
    const transactionId = 'EXT-' + Date.now();

    // Montant total (50 FCFA timbre + 1500 FCFA livraison)
    const totalAmount = formData.montant_timbre + formData.montant_livraison;

    // Chargement SweetAlert
    Swal.fire({
        title: 'Redirection en cours',
        html: 'Préparation du paiement...',
        allowOutsideClick: true, // Prevent closing accidentally
        didOpen: () => Swal.showLoading()
    });

    // Données client
    const customer = {
        name: '{{ Auth::user()->name ?? "Client" }}', // Use authenticated user's name if available
        email: '{{ Auth::user()->email ?? "contact@client.com" }}', // Use authenticated user's email if available
        phone: '{{ Auth::user()->telephone ?? "00000000" }}' // Use authenticated user's phone if available
    };

    // Paiement CinetPay
    CinetPay.getCheckout({
        transaction_id: transactionId,
        amount: totalAmount,
        currency: 'XOF',
        channels: 'ALL',
        description: `Paiement pour livraison d'extrait de décès`,
        customer_name: customer.name,
        customer_email: customer.email,
        customer_phone_number: customer.phone,
        customer_address: formData.adresse_livraison,
        customer_city: formData.ville,
        customer_country: 'CI',
        customer_state: 'CI',
        customer_zip_code: '00225'
    });

    // Gestion réponse CinetPay
    CinetPay.waitResponse(function(data) {
        Swal.close(); // Close loading popup
        if (data.status === "ACCEPTED") {
            // Add delivery data to the form as hidden inputs
            const form = document.getElementById('declarationForm');

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

            // Set flag and submit the form
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

    // Gestion erreurs CinetPay (e.g., connection issues)
    CinetPay.onError(function(error) {
        Swal.close(); // Close loading popup
        Swal.fire({
            icon: 'error',
            title: 'Erreur de connexion',
            html: `Une erreur est survenue lors de la connexion à CinetPay.<br><small>${error.message || 'Veuillez vérifier votre connexion et réessayer.'}</small>`
        });
    });
}
</script>


@endsection