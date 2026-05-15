@extends('user.layouts.template')

@section('content')

    <!-- Inclure SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .form-page-container {
            padding: 2rem 0;
            animation: fadeIn 0.8s ease-out;
        }

        .form-glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            padding: 3rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .form-header-box {
            text-align: center;
            margin-bottom: 3rem;
        }

        .form-header-box h2 {
            color: var(--text-navy);
            font-weight: 800;
            font-size: 2.2rem;
            letter-spacing: -1px;
            margin-bottom: 0.5rem;
        }

        .form-header-box p {
            color: #718096;
            font-size: 1rem;
        }

        .form-section {
            margin-bottom: 2.5rem;
            position: relative;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            color: var(--primary);
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-section-title i {
            width: 35px;
            height: 35px;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            margin-right: 12px;
            font-size: 14px;
        }

        .input-group-custom {
            margin-bottom: 1.5rem;
        }

        .input-group-custom label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-navy);
            font-size: 0.9rem;
            padding-left: 5px;
        }

        .input-wrapper {
            position: relative;
            transition: 0.3s;
        }

        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-control-custom {
            width: 100%;
            padding: 14px 20px 14px 50px;
            border-radius: 15px;
            border: 2px solid #f4f7fe;
            background: #f4f7fe;
            color: var(--text-navy);
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 10px 20px rgba(25, 119, 204, 0.05);
            outline: none;
        }

        .form-control-custom:focus + i {
            color: var(--primary);
        }

        .delivery-card-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .delivery-option-card {
            position: relative;
            cursor: pointer;
        }

        .delivery-option-card input {
            position: absolute;
            opacity: 0;
        }

        .delivery-option-content {
            padding: 2rem;
            background: #fff;
            border-radius: 20px;
            border: 2px solid #f4f7fe;
            text-align: center;
            transition: 0.3s;
            height: 100%;
        }

        .delivery-option-content i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #a0aec0;
            transition: 0.3s;
        }

        .delivery-option-content h5 {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: var(--text-navy);
        }

        .delivery-option-content p {
            font-size: 0.8rem;
            color: #718096;
            margin-bottom: 0;
        }

        .delivery-option-card input:checked + .delivery-option-content {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(25, 119, 204, 0.1);
        }

        .delivery-option-card input:checked + .delivery-option-content i {
            color: var(--primary);
        }

        .file-upload-area {
            border: 2px dashed #cbd5e0;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            background: #f8faff;
        }

        .file-upload-area:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .file-upload-area i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .btn-submit-premium {
            background: linear-gradient(135deg, var(--primary), #0d4a85);
            color: #fff;
            border: none;
            padding: 1.2rem;
            border-radius: 18px;
            font-weight: 700;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 10px 20px rgba(25, 119, 204, 0.2);
            transition: 0.3s;
        }

        .btn-submit-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(25, 119, 204, 0.3);
        }

        /* Stepper Styles */
        .stepper-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3rem;
            position: relative;
            max-width: 1000px;
            margin-left: auto;
            margin-right: auto;
        }

        .stepper-container::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #f4f7fe;
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            border: 2px solid #f4f7fe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #a0aec0;
            transition: 0.3s;
        }

        .step-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #a0aec0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: 0.3s;
        }

        .step-item.active .step-number {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
            box-shadow: 0 5px 15px rgba(25, 119, 204, 0.2);
        }

        .step-item.active .step-label {
            color: var(--primary);
        }

        .step-item.completed .step-number {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary);
        }

        .form-step {
            display: none;
            animation: slideInRight 0.5s ease-out;
        }

        .form-step.active {
            display: block;
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .stepper-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #f4f7fe;
        }

        .btn-step {
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-prev {
            background: #f4f7fe;
            color: #718096;
            border: none;
        }

        .btn-prev:hover {
            background: #edf2f7;
            transform: translateX(-3px);
        }

        .btn-next {
            background: var(--primary);
            color: #fff;
            border: none;
            box-shadow: 0 5px 15px rgba(25, 119, 204, 0.1);
        }

        .btn-next:hover {
            background: #0d4a85;
            transform: translateX(3px);
            box-shadow: 0 8px 20px rgba(25, 119, 204, 0.2);
        }

        .form-control-custom.is-invalid {
            border-color: #EE5D50 !important;
            background: #fffafa !important;
        }

        .error-message {
            color: #EE5D50;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 5px;
            display: block;
            padding-left: 5px;
            animation: shake 0.4s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .form-page-container {
                padding: 1rem 0;
            }

            .form-glass-card { 
                padding: 1.5rem; 
                border-radius: 20px;
                margin: 0 -5px;
            }

            .form-header-box h2 {
                font-size: 1.5rem;
            }

            .stepper-container {
                margin-bottom: 2rem;
            }

            .step-label {
                font-size: 0.6rem;
                text-align: center;
            }

            .step-number {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }

            .form-section-title {
                font-size: 0.9rem;
            }

            .delivery-card-grid { 
                grid-template-columns: 1fr; 
                gap: 1rem;
            }

            .delivery-option-content {
                padding: 1.25rem;
            }

            .btn-step {
                padding: 0.7rem 1.2rem;
                font-size: 0.9rem;
            }

            input[type="date"] {
                min-height: 48px;
            }
        }
    </style>

<div class="form-page-container">
    <div class="form-glass-card">
        <div class="form-header-box">
            <h2>Demande d'acte de naissance2</h2>
            <p>Processus simplifié en 3 étapes</p>
        </div>

        <!-- Stepper Indicator -->
        <div class="stepper-container">
            <div class="step-item active" id="step-1-indicator">
                <div class="step-number">1</div>
                <div class="step-label">Type</div>
            </div>
            <div class="step-item" id="step-2-indicator">
                <div class="step-number">2</div>
                <div class="step-label">Informations</div>
            </div>
            <div class="step-item" id="step-3-indicator">
                <div class="step-number">3</div>
                <div class="step-label">Mode & Pièce</div>
            </div>
        </div>

        <form id="naissanceForm" action="{{route('user.extrait.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="commune" value="Plateau">

            <!-- ÉTAPE 1: Type de demande -->
            <div class="form-step active" id="step-1">
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i> Type de demande
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group-custom">
                                <label>Cette demande est pour :</label>
                                <div class="input-wrapper">
                                    <select id="pour" name="pour" class="form-control-custom" onchange="updateFields()">
                                        <option value="Moi" {{ old('pour') == 'Moi' ? 'selected' : '' }}>Moi-même</option>
                                        <option value="une_autre_personne" {{ old('pour') == 'une_autre_personne' ? 'selected' : '' }}>Une autre personne</option>
                                    </select>
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group-custom">
                                <label>Type de document :</label>
                                <div class="input-wrapper">
                                    <select id="type" name="type" class="form-control-custom">
                                        <option value="simple" {{ old('type') == 'simple' ? 'selected' : '' }}>Acte simple</option>
                                        <option value="extrait_integral" {{ old('type') == 'extrait_integral' ? 'selected' : '' }}>Acte intégral</option>
                                    </select>
                                    <i class="fas fa-file-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stepper-footer">
                    <div></div>
                    <button type="button" class="btn-step btn-next" onclick="nextStep(1)">
                        Continuer <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- ÉTAPE 2: Informations sur l'acte -->
            <div class="form-step" id="step-2">
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-user"></i> Informations sur l'acte
                    </div>
                    
                    <!-- Ligne 1: Identité et Lieu -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="input-group-custom">
                                <label>Nom :</label>
                                <div class="input-wrapper">
                                    <input type="text" id="name" name="name" class="form-control-custom"
                                        value="{{ old('name', $userName) }}" placeholder="Nom sur l'acte">
                                    <i class="fas fa-id-card"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group-custom">
                                <label>Prénoms :</label>
                                <div class="input-wrapper">
                                    <input type="text" id="prenom" name="prenom" class="form-control-custom"
                                        value="{{ old('prenom', $userPrenom) }}" placeholder="Prénoms sur l'acte">
                                    <i class="fas fa-id-card"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group-custom">
                                <label>Lieu de naissance :</label>
                                <div class="input-wrapper">
                                    <input type="text" id="commune_naissance" name="commune_naissance" class="form-control-custom" 
                                        value="{{ old('commune_naissance') }}" placeholder="Commune">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ligne 2: Registre et Quantité -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="input-group-custom">
                                <label>N° Registre :</label>
                                <div class="input-wrapper">
                                    <input type="text" id="number" name="number" class="form-control-custom" 
                                        value="{{ old('number') }}" placeholder="Ex: 123/2024">
                                    <i class="fas fa-hashtag"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group-custom">
                                <label>Date Registre :</label>
                                <div class="input-wrapper">
                                    <input type="date" id="DateR" name="DateR" class="form-control-custom" 
                                        value="{{ old('DateR') }}" onclick="this.showPicker()">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group-custom">
                                <label>Quantité :</label>
                                <div class="input-wrapper">
                                    <input type="number" id="quantite" name="quantite" class="form-control-custom"
                                        value="{{ old('quantite', 1) }}" min="1" max="10">
                                    <i class="fas fa-copy"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ligne 3: Filiation -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group-custom">
                                <label>Nom et Prénoms du père :</label>
                                <div class="input-wrapper">
                                    <input type="text" id="nom_prenoms_pere" name="nom_prenoms_pere" class="form-control-custom"
                                        value="{{ old('nom_prenoms_pere') }}" placeholder="Nom complet du père">
                                    <i class="fas fa-male"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group-custom">
                                <label>Nom et Prénoms de la mère :</label>
                                <div class="input-wrapper">
                                    <input type="text" id="nom_prenoms_mere" name="nom_prenoms_mere" class="form-control-custom"
                                        value="{{ old('nom_prenoms_mere') }}" placeholder="Nom complet de la mère">
                                    <i class="fas fa-female"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stepper-footer">
                    <button type="button" class="btn-step btn-prev" onclick="prevStep(2)">
                        <i class="fas fa-arrow-left"></i> Précédent
                    </button>
                    <button type="button" class="btn-step btn-next" onclick="nextStep(2)">
                        Continuer <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- ÉTAPE 3: Justificatif & Mode de retrait -->
            <div class="form-step" id="step-3">
                <!-- Section: Justificatif -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-paperclip"></i> Justificatif d'identité
                    </div>
                    <div class="input-group-custom">
                        <label>Pièce d'identité (CNI, Passeport, Livret) :</label>
                        <label class="file-upload-area">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h6 id="file-name" class="text-navy-bold mb-1">Téléverser votre pièce</h6>
                            <p class="x-small text-grey mb-0">PDF, JPG ou PNG (Max 1Mo)</p>
                            <input type="file" id="CNI" name="CNI" class="d-none" onchange="updateFileName(this)" accept=".jpg,.jpeg,.png,.pdf">
                        </label>
                    </div>
                </div>

                <!-- Section: Mode de retrait -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-truck"></i> Mode de retrait
                    </div>
                    <div class="delivery-card-grid">
                        <label class="delivery-option-card">
                            <input type="radio" id="option1" name="choix_option" value="Retrait sur place" checked>
                            <div class="delivery-option-content">
                                <i class="fas fa-university"></i>
                                <h5>Retrait en Mairie</h5>
                                <p>Gratuit - Guichet</p>
                            </div>
                        </label>
                        <label class="delivery-option-card">
                            <input type="radio" id="option2" name="choix_option" value="livraison">
                            <div class="delivery-option-content">
                                <i class="fas fa-motorcycle"></i>
                                <h5>Livraison Express</h5>
                                <p>1500 FCFA - Domicile</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="stepper-footer">
                    <button type="button" class="btn-step btn-prev" onclick="prevStep(3)">
                        <i class="fas fa-arrow-left"></i> Précédent
                    </button>
                    <button type="submit" id="btnValider" class="btn-step btn-next" style="background: var(--success);">
                        <i class="fas fa-check-circle"></i> Valider ma demande
                    </button>
                </div>
            </div>
        </form>
    </div>
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

        // Stepper Navigation
        function nextStep(step) {
            if (validateStep(step)) {
                document.getElementById('step-' + step).classList.remove('active');
                document.getElementById('step-' + (step + 1)).classList.add('active');
                
                document.getElementById('step-' + step + '-indicator').classList.add('completed');
                document.getElementById('step-' + step + '-indicator').classList.remove('active');
                document.getElementById('step-' + (step + 1) + '-indicator').classList.add('active');
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function prevStep(step) {
            document.getElementById('step-' + step).classList.remove('active');
            document.getElementById('step-' + (step - 1)).classList.add('active');
            
            document.getElementById('step-' + step + '-indicator').classList.remove('active');
            document.getElementById('step-' + (step - 1) + '-indicator').classList.add('active');
            document.getElementById('step-' + (step - 1) + '-indicator').classList.remove('completed');
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function validateStep(step) {
            const form = document.getElementById('naissanceForm');
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('.form-control-custom.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            let isValid = true;
            if (step === 1) {
                // Étape 1 toujours valide (selects avec valeurs par défaut)
            } else if (step === 2) {
                const name = document.getElementById('name');
                const prenom = document.getElementById('prenom');
                const commune = document.getElementById('commune_naissance');
                const number = document.getElementById('number');
                const dateR = document.getElementById('DateR');
                const pere = document.getElementById('nom_prenoms_pere');
                const mere = document.getElementById('nom_prenoms_mere');

                if (!name.value.trim()) { isValid = false; displayClientError(name, "Le nom est obligatoire."); }
                if (!prenom.value.trim()) { isValid = false; displayClientError(prenom, "Le prénom est obligatoire."); }
                if (!commune.value.trim()) { isValid = false; displayClientError(commune, "La commune de naissance est obligatoire."); }
                
                // Logic: Registry OR Parents
                const hasRegistry = number.value.trim() && dateR.value.trim();
                const hasParents = pere.value.trim() && mere.value.trim();
                
                if (!hasRegistry && !hasParents) {
                    isValid = false;
                    displayClientError(number, "Veuillez fournir soit le registre (N° et Date), soit les deux parents.");
                }
            } else if (step === 3) {
                const cni = document.getElementById('CNI');
                if (cni.files.length === 0) {
                    isValid = false;
                    displayClientError(cni.closest('.input-group-custom'), "Veuillez téléverser votre pièce d'identité.");
                }
            }
            
            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Champs manquants',
                    text: 'Veuillez remplir les informations obligatoires.',
                    confirmButtonColor: '#1977cc'
                });
            }
            return isValid;
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
                { id: 'commune_naissance', message: 'La commune de naissance est obligatoire.' },
                { id: 'quantite', message: 'La quantité est obligatoire.' },
                { id: 'CNI', message: 'Une pièce d\'identité (CNI/Passeport) est obligatoire.' }
            ];

            // Validation conditionnelle : Registre OU Parents
            const number = document.getElementById('number').value.trim();
            const dateR = document.getElementById('DateR').value.trim();
            const nomPrenomsPere = document.getElementById('nom_prenoms_pere').value.trim();
            const nomPrenomsMere = document.getElementById('nom_prenoms_mere').value.trim();

            const hasRegistryInfo = number && dateR;
            const hasParentInfo = nomPrenomsPere && nomPrenomsMere;

            if (!hasRegistryInfo && !hasParentInfo) {
                isValid = false;
                displayClientError(document.getElementById('number'), "Veuillez fournir soit les infos de registre, soit les infos complètes des parents.");
                if (document.getElementById('nom_prenoms_pere')) displayClientError(document.getElementById('nom_prenoms_pere'), "Veuillez fournir soit les infos de registre, soit les infos complètes des parents.");
            }

            // Retirer les messages d'erreur existants et les styles d'erreur
            form.querySelectorAll('.error-message').forEach(el => el.remove());

            form.querySelectorAll('.form-control-custom.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.form-control-custom').forEach(el => el.style.animation = 'none');


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
            inputElement.classList.add('is-invalid');
            const errorSpan = document.createElement('span');
            errorSpan.classList.add('error-message');
            errorSpan.textContent = message;
            
            const wrapper = inputElement.closest('.input-group-custom') || inputElement.closest('.form-section') || inputElement.parentNode;
            wrapper.appendChild(errorSpan);
        }


        document.getElementById('naissanceForm').addEventListener('submit', function (event) {
            if (formSubmitted) {
                event.preventDefault();
                return;
            }

            event.preventDefault(); // On gère toujours manuellement

            if (validateFormClient()) {
                const livraisonCheckbox = document.getElementById('option2');
                if (livraisonCheckbox.checked) {
                    showLivraisonPopup();
                } else {
                    formSubmitted = true;
                    this.submit();
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur de Validation',
                    text: 'Veuillez corriger les erreurs dans le formulaire avant de continuer.',
                    confirmButtonColor: '#1977cc'
                });
            }
        });

        function showLivraisonPopup() {
    // Récupérer la quantité depuis le formulaire
    const quantite = parseInt(document.getElementById('quantite').value) || 1;
    const montantTimbreUnitaire = 500; 
    const montantLivraison = 1500; 
    
    // Calcul des timbres gratuits
    let freeTimbres = 0;
    let freeAmount = 0;
    const freeRequestsModeActive = @json($freeRequestsModeActive ?? false);
    const freeRequestsRemaining = @json($freeRequestsRemaining ?? 0);

    if (freeRequestsModeActive && freeRequestsRemaining > 0) {
        freeTimbres = Math.min(quantite, freeRequestsRemaining);
        freeAmount = freeTimbres * montantTimbreUnitaire;
    }

    const paidTimbres = quantite - freeTimbres;
    const montantTimbreTotal = paidTimbres * montantTimbreUnitaire;
    const montantTotal = montantTimbreTotal + montantLivraison;

    // Élément d'affichage pour les timbres gratuits
    let freeTimbresHtml = '';
    let originalTimbreHtml = '';
    
    if (freeTimbres > 0) {
        originalTimbreHtml = `
            <div style="display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 0.85rem; text-decoration: line-through; color: #a0aec0;">
                <span>Timbres (x${quantite}):</span>
                <span>${quantite * montantTimbreUnitaire} FCFA</span>
            </div>
        `;
        freeTimbresHtml = `
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem; color: #28a745; font-weight: bold;">
                <span><i class="fas fa-gift mr-1"></i> Timbres offerts (x${freeTimbres}):</span>
                <span>- ${freeAmount} FCFA</span>
            </div>
        `;
    }

    let finalTimbreHtml = `
        <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem;">
            <span style="color: #555;">Timbres payants (x${paidTimbres}):</span>
            <span style="font-weight: 700">${montantTimbreTotal} FCFA</span>
        </div>
    `;

    Swal.fire({
        title: '<div class="flex items-center justify-center p-2"><i class="fas fa-truck text-primary mr-2"></i> <span style="font-size: 1.2rem; font-weight: 800; color: #1977cc;">DÉTAILS DE LIVRAISON</span></div>',
        width: '800px',
        html: `
            <div style="display: flex; flex-wrap: wrap; gap: 20px; text-align: left; max-height: 70vh; overflow-y: auto; padding: 10px;">
                
                <!-- Formulaire (Partie gauche) -->
                <div style="flex: 1 1 400px;">
                    <h4 style="font-size: 0.9rem; font-weight: bold; color: #1977cc; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">📍 Vos coordonnées</h4>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Nom</label>
                            <input id="swal-nom_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Nom" value="{{ Auth::user()->name }}">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Prénom</label>
                            <input id="swal-prenom_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Prénom" value="{{ Auth::user()->prenom }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Email</label>
                            <input id="swal-email_destinataire" type="email" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="mail@exemple.com" value="{{ Auth::user()->email }}">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Téléphone</label>
                            <input id="swal-contact_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="0123456789" value="{{ Auth::user()->contact }}">
                        </div>
                    </div>

                    <div style="margin-top: 10px;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Adresse précise</label>
                        <input id="swal-adresse_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="N° de rue, immeuble...">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-top: 10px;">
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Ville</label>
                            <input id="swal-ville" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Ville" value="Abidjan">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Commune</label>
                            <input id="swal-commune_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Commune">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Quartier</label>
                            <input id="swal-quartier" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Quartier">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Date</label>
                            <input type="date" id="swal-date_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px; cursor: pointer;" min="{{ date('Y-m-d') }}" onclick="this.showPicker()">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Heure</label>
                            <input type="time" id="swal-heure_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px; cursor: pointer;" onclick="this.showPicker()">
                        </div>
                    </div>
                </div>

                <!-- Résumé et Paiement (Partie droite) -->
                <div style="flex: 1 1 250px; display: flex; flex-direction: column; gap: 15px;">
                    <div style="background: #f0f7ff; padding: 15px; border-radius: 12px; border: 1px solid #cce3f6;">
                        <h4 style="font-size: 0.9rem; font-weight: bold; color: #1977cc; margin-bottom: 10px; border-bottom: 1px solid #cce3f6; padding-bottom: 5px;">🧾 Résumé</h4>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem;">
                            <span style="color: #555;">Exemplaires:</span>
                            <span style="font-weight: 700">${quantite}</span>
                        </div>
                        ${originalTimbreHtml}
                        ${freeTimbresHtml}
                        ${finalTimbreHtml}
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.85rem;">
                            <span style="color: #555;">Livraison:</span>
                            <span style="font-weight: 700">${montantLivraison} FCFA</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-top: 2px dashed #b8d4ed; padding-top: 8px; margin-top: 8px;">
                            <span style="color: #1977cc; font-weight: 800; font-size: 0.9rem;">TOTAL:</span>
                            <span style="color: #1977cc; font-weight: 800; font-size: 1.1rem;">${montantTotal} FCFA</span>
                        </div>
                    </div>

                    <div>
                        <h4 style="font-size: 0.9rem; font-weight: bold; color: #1977cc; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">💳 Paiement</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <button type="button" id="btn-pay-wave" class="payment-method-btn active-payment" style="background: #eff6ff; border: 2px solid #1e3a8a; border-radius: 8px; padding: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="selectPaymentMethod('wave')">
                                <img src="{{ asset('assets/assets/img/Wave.png') }}" alt="Wave" style="height: 30px; object-fit: contain;">
                            </button>
                            <button type="button" id="btn-pay-orange" class="payment-method-btn opacity-50" style="background: white; border: 1px solid #edf2f7; border-radius: 8px; padding: 8px; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 5px;" disabled title="Bientôt disponible">
                                <img src="{{ asset('assets/assets/img/Orange.png') }}" alt="Orange Money" style="height: 30px; object-fit: contain;">
                            </button>
                            <button type="button" id="btn-pay-mtn" class="payment-method-btn" style="background: white; border: 1px solid #edf2f7; border-radius: 8px; padding: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="selectPaymentMethod('mtn')">
                                <img src="{{ asset('assets/assets/img/MTN.png') }}" alt="MTN" style="height: 30px; object-fit: contain;">
                            </button>
                            <button type="button" id="btn-pay-moov" class="payment-method-btn opacity-50" style="background: white; border: 1px solid #edf2f7; border-radius: 8px; padding: 8px; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 5px;" disabled title="Bientôt disponible">
                                <img src="{{ asset('assets/assets/img/Moov.png') }}" alt="Moov" style="height: 30px; object-fit: contain;">
                            </button>
                        </div>
                        <input type="hidden" id="swal-payment_method" value="wave">
                        <div id="payment-phone-container" style="display: none; margin-top: 10px;">
                            <label id="payment-phone-label" style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Numéro Wave</label>
                            <input id="swal-mtn_number" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Entrez votre numéro" value="" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: `Payer`,
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
            const date_livraison = document.getElementById('swal-date_livraison').value;
            const heure_livraison = document.getElementById('swal-heure_livraison').value;

            if (!nom_destinataire || !prenom_destinataire || !contact_destinataire || !adresse_livraison || !ville || !commune_livraison || !quartier || !date_livraison || !heure_livraison) {
                Swal.showValidationMessage("Veuillez remplir tous les champs obligatoires, y compris la date et l'heure de livraison.");
                return false;
            }

            const selectedDate = new Date(date_livraison);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            if (selectedDate < today) {
                Swal.showValidationMessage("La date de livraison ne peut pas être dans le passé.");
                return false;
            }
            // Validation d'email simple (si renseigné)
            if (email_destinataire && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email_destinataire)) {
                Swal.showValidationMessage("Veuillez entrer une adresse email valide.");
                return false;
            }
            // Validation de numéro de téléphone (8 à 15 chiffres)
            const cleanContact = contact_destinataire.replace(/\s+/g, '');
            if (!/^\d{8,15}$/.test(cleanContact)) {
                Swal.showValidationMessage("Veuillez entrer un numéro de téléphone de contact valide (8 à 15 chiffres).");
                return false;
            }

            const payment_number = document.getElementById('swal-mtn_number') ? document.getElementById('swal-mtn_number').value.replace(/\s+/g, '') : cleanContact;
            const payment_method = document.getElementById('swal-payment_method').value;

                    if (payment_method === 'mtn') {
                        if (!/^\d{10}$/.test(payment_number)) {
                            Swal.showValidationMessage('Veuillez entrer un numéro MTN Money valide à 10 chiffres.');
                            return false;
                        }
                    }

                    // Ouvrir la fenêtre de paiement de manière synchrone pour contourner le bloqueur
                    window.PaymentPopup = window.open('', 'PaymentPopup');

                    return {
                        nom_destinataire: nom_destinataire,
                        prenom_destinataire: prenom_destinataire,
                        email_destinataire: email_destinataire,
                        contact_destinataire: contact_destinataire,
                        adresse_livraison: adresse_livraison,
                        ville: ville,
                        commune_livraison: commune_livraison,
                        quartier: quartier,
                        date_livraison: date_livraison,
                        heure_livraison: heure_livraison,
                        quantite: quantite,
                        montant_timbre_unitaire: montantTimbreUnitaire,
                        montant_timbre: montantTimbreTotal,
                        montant_livraison: montantLivraison,
                        payment_method: payment_method,
                        mtn_number: payment_number
                    };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = result.value;
            
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
                { name: 'date_livraison', value: formData.date_livraison },
                { name: 'heure_livraison', value: formData.heure_livraison },
                { name: 'montant_timbre_unitaire', value: formData.montant_timbre_unitaire },
                { name: 'montant_timbre', value: formData.montant_timbre },
                { name: 'montant_livraison', value: formData.montant_livraison },
                { name: 'payment_method', value: formData.payment_method },
                { name: 'mtn_number', value: formData.mtn_number }
            ];

            hiddenFields.forEach(field => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = field.name;
                input.value = field.value;
                form.appendChild(input);
            });

            window.paymentSuccess = false;
            
            if (window.PaymentPopup) {
                form.target = 'PaymentPopup';
            } else {
                form.target = '_blank';
            }
            
            if (formData.payment_method === 'wave') {
                Swal.fire({
                    title: 'Paiement en cours',
                    html: 'Veuillez finaliser le paiement dans le <b>nouvel onglet</b> qui vient de s\'ouvrir.<br><br><span style="color:#555;font-size:0.9rem;">La page s\'actualisera automatiquement dès que le paiement sera confirmé.</span>',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const timer = setInterval(() => {
                    if (window.PaymentPopup && window.PaymentPopup.closed) {
                        clearInterval(timer);
                        Swal.close();
                        // Vérifier d'abord window.paymentSuccess (opener accessible)
                        if (window.paymentSuccess) {
                            window.location.href = window.paymentSuccessUrl || "{{ route('user.extrait.index') }}";
                            return;
                        }
                        // Fallback : lire localStorage (cas où COOP a rompu window.opener)
                        try {
                            var result = JSON.parse(localStorage.getItem('plateauPaymentResult') || '{}');
                            var age = Date.now() - (result.timestamp || 0);
                            if (result.status === 'success' && age < 120000) {
                                localStorage.removeItem('plateauPaymentResult');
                                window.location.href = result.listUrl || "{{ route('user.extrait.index') }}";
                                return;
                            }
                        } catch (e) {}
                        // Paiement annulé ou inconnu : recharger le formulaire
                        window.location.reload();
                    }
                }, 1000);
            } else {
                // Pour les autres paiements (MTN, etc.)
                Swal.fire({
                    title: 'Redirection en cours',
                    html: `Un nouvel onglet s'est ouvert pour le suivi du paiement...`,
                    icon: 'info',
                    confirmButtonText: 'Fermer',
                    confirmButtonColor: '#1977cc',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = "{{ route('user.extrait.index') }}";
                });
            }

            // Soumettre le formulaire
            formSubmitted = true;
            form.submit();
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            document.getElementById('option1').checked = true;
        }
    });
}

        // Fonction pour sélectionner la méthode de paiement
        function selectPaymentMethod(method) {
            if (method !== 'wave' && method !== 'mtn') return;
            
            // Réinitialiser tous les boutons
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                if(!btn.classList.contains('opacity-50')) {
                    btn.style.border = '1px solid #edf2f7';
                    btn.style.backgroundColor = 'white';
                }
            });
            
            // Appliquer le style actif au bouton sélectionné
            const activeBtn = document.getElementById('btn-pay-' + method);
            if (method === 'wave') {
                activeBtn.style.border = '2px solid #1e3a8a';
                activeBtn.style.backgroundColor = '#eff6ff';
                document.getElementById('payment-phone-container').style.display = 'none';
            } else if (method === 'mtn') {
                activeBtn.style.border = '2px solid #fcb711'; // MTN Yellow
                activeBtn.style.backgroundColor = '#fffbed';
                document.getElementById('payment-phone-container').style.display = 'block';
                document.getElementById('payment-phone-label').innerText = 'Numéro MTN Money';
            }
            
            // Mettre à jour la valeur du champ caché
            document.getElementById('swal-payment_method').value = method;
        }
    </script>
@endsection