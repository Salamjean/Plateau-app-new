@extends('user.layouts.template')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #1977cc;
            --primary-light: #eef5fc;
            --success: #28a745;
            --text-navy: #1a365d;
        }

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

        .form-section {
            margin-bottom: 2.5rem;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            color: var(--primary);
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
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
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }

        .form-control-custom {
            width: 100%;
            padding: 14px 20px 14px 50px;
            border-radius: 15px;
            border: 2px solid #f4f7fe;
            background: #f4f7fe;
            transition: 0.3s;
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            background: #fff;
            outline: none;
        }

        /* Stepper */
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
            top: 25px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 120px;
        }

        .step-circle {
            width: 50px;
            height: 50px;
            background: #fff;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-bottom: 10px;
            transition: 0.4s;
            color: #718096;
        }

        .step-item.active .step-circle {
            border-color: var(--primary);
            background: var(--primary);
            color: #fff;
            box-shadow: 0 0 0 5px var(--primary-light);
        }

        .step-item.completed .step-circle {
            background: var(--success);
            border-color: var(--success);
            color: #fff;
        }

        .step-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #718096;
            text-align: center;
        }

        .step-item.active .step-label { color: var(--primary); }

        .form-step { display: none; }
        .form-step.active { display: block; animation: slideIn 0.5s ease-out; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .stepper-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #f4f7fe;
        }

        .btn-step {
            padding: 14px 30px;
            border-radius: 15px;
            font-weight: 700;
            transition: 0.3s;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-prev { background: #f4f7fe; color: #718096; }
        .btn-prev:hover { background: #e2e8f0; }
        .btn-next { background: var(--primary); color: #fff; }
        .btn-next:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(25, 119, 204, 0.3); }

        /* Delivery Options */
        .delivery-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .delivery-card {
            position: relative;
            cursor: pointer;
        }

        .delivery-card input { position: absolute; opacity: 0; }

        .delivery-card-content {
            padding: 2rem;
            background: #fff;
            border-radius: 20px;
            border: 2px solid #f4f7fe;
            text-align: center;
            transition: 0.3s;
        }

        .delivery-card input:checked + .delivery-card-content {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-5px);
        }

        .delivery-card-content i { font-size: 2rem; color: var(--primary); margin-bottom: 1rem; }
        .delivery-title { display: block; font-weight: 800; color: var(--text-navy); }
        .delivery-price { font-size: 0.85rem; color: #718096; }

        .is-invalid { border-color: var(--danger) !important; }
        .error-message { color: var(--danger); font-size: 0.75rem; margin-top: 5px; font-weight: 600; }
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

            .step-item {
                width: auto;
                flex: 1;
            }

            .step-circle {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }

            .step-label {
                font-size: 0.6rem;
            }

            .delivery-grid { 
                grid-template-columns: 1fr; 
                gap: 1rem;
            }

            .delivery-card-content {
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
                <h2>Demande d'acte de mariage</h2>
                <p class="text-muted">Remplissez les informations ci-dessous pour commander votre acte de mariage.</p>
            </div>

            <!-- Stepper -->
            <div class="stepper-container">
                <div class="step-item active" id="step-item-1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Type d'acte</div>
                </div>
                <div class="step-item" id="step-item-2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Informations</div>
                </div>
                <div class="step-item" id="step-item-3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Retrait & Pièces</div>
                </div>
            </div>

            <form id="demandeForm" method="POST" enctype="multipart/form-data" action="{{ route('user.extrait.mariage.store') }}">
                @csrf

                <!-- ÉTAPE 1: Type de demande -->
                <div class="form-step active" id="step-1">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-file-signature"></i> Quel type d'acte souhaitez-vous ?
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group-custom">
                                    <label>Format de l'acte :</label>
                                    <div class="input-wrapper">
                                        <select id="typeDemande" name="typeDemande" class="form-control-custom">
                                            <option value="extraitSimple">Acte simple (Extrait)</option>
                                            <option value="copieIntegrale">Copie intégrale</option>
                                        </select>
                                        <i class="fas fa-list"></i>
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

                <!-- ÉTAPE 2: Informations -->
                <div class="form-step" id="step-2">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-book"></i> Détails du mariage
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="input-group-custom">
                                    <label>Commune de mariage :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="commune_mariage" name="commune_mariage" class="form-control-custom" placeholder="Ville ou commune">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="input-group-custom">
                                    <label>Quantité :</label>
                                    <div class="input-wrapper">
                                        <input type="number" id="quantite" name="quantite" class="form-control-custom" value="1" min="1" max="10">
                                        <i class="fas fa-copy"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="input-group-custom">
                                    <label>Numéro NNI (Optionnel) :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="CMU" name="CMU" class="form-control-custom" value="{{ Auth::user()->CMU }}" placeholder="Votre NNI">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bloc Conjoint (Si Copie Intégrale) -->
                        <div id="infoEpoux" style="display: none; margin-top: 2rem;">
                            <div class="form-section-title">
                                <i class="fas fa-user-friends"></i> Informations sur le conjoint(e)
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="input-group-custom">
                                        <label>Nom du conjoint(e) :</label>
                                        <div class="input-wrapper">
                                            <input type="text" id="nomEpoux" name="nomEpoux" class="form-control-custom" placeholder="Nom">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="input-group-custom">
                                        <label>Prénom du conjoint(e) :</label>
                                        <div class="input-wrapper">
                                            <input type="text" id="prenomEpoux" name="prenomEpoux" class="form-control-custom" placeholder="Prénom">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="input-group-custom">
                                        <label>Date de naissance :</label>
                                        <div class="input-wrapper">
                                            <input type="date" id="dateNaissanceEpoux" name="dateNaissanceEpoux" class="form-control-custom">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="input-group-custom">
                                        <label>Lieu de naissance :</label>
                                        <div class="input-wrapper">
                                            <input type="text" id="lieuNaissanceEpoux" name="lieuNaissanceEpoux" class="form-control-custom" placeholder="Ville">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
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

                <!-- ÉTAPE 3: Pièces & Retrait -->
                <div class="form-step" id="step-3">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-upload"></i> Justificatifs & Mode de retrait
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="input-group-custom">
                                    <label>Pièce d'identité (CNI/Pass) :</label>
                                    <input type="file" id="pieceIdentite" name="pieceIdentite" class="form-control-custom" accept=".pdf,.jpg,.jpeg,.png" style="padding: 10px;">
                                    <small class="text-muted">Document obligatoire (Max 1Mo)</small>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="input-group-custom">
                                    <label>Ancien acte de mariage (Optionnel) :</label>
                                    <input type="file" id="extraitMariage" name="extraitMariage" class="form-control-custom" accept=".pdf,.jpg,.jpeg,.png" style="padding: 10px;">
                                </div>
                            </div>
                        </div>

                        <div class="form-section-title mt-4">
                            <i class="fas fa-truck"></i> Comment souhaitez-vous récupérer l'acte ?
                        </div>
                        <div class="delivery-grid">
                            <label class="delivery-card">
                                <input type="radio" name="choix_option" id="option1" value="Retrait sur place" checked>
                                <div class="delivery-card-content">
                                    <i class="fas fa-university"></i>
                                    <span class="delivery-title">Retrait en Mairie</span>
                                    <span class="delivery-price">Gratuit</span>
                                </div>
                            </label>
                            <label class="delivery-card">
                                <input type="radio" name="choix_option" id="option2" value="livraison">
                                <div class="delivery-card-content">
                                    <i class="fas fa-motorcycle"></i>
                                    <span class="delivery-title">Livraison Express</span>
                                    <span class="delivery-price">+ 1 500 FCFA</span>
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

        document.getElementById('typeDemande').addEventListener('change', function () {
            const infoEpoux = document.getElementById('infoEpoux');
            infoEpoux.style.display = (this.value === 'copieIntegrale') ? 'block' : 'none';
        });

        function nextStep(step) {
            if (validateStep(step)) {
                document.getElementById('step-' + step).classList.remove('active');
                document.getElementById('step-' + (step + 1)).classList.add('active');
                
                document.getElementById('step-item-' + step).classList.remove('active');
                document.getElementById('step-item-' + step).classList.add('completed');
                document.getElementById('step-item-' + (step + 1)).classList.add('active');
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function prevStep(step) {
            document.getElementById('step-' + step).classList.remove('active');
            document.getElementById('step-' + (step - 1)).classList.add('active');
            
            document.getElementById('step-item-' + step).classList.remove('active');
            document.getElementById('step-item-' + (step - 1)).classList.remove('completed');
            document.getElementById('step-item-' + (step - 1)).classList.add('active');
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function validateStep(step) {
            let isValid = true;
            const container = document.getElementById('step-' + step);
            container.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            container.querySelectorAll('.error-message').forEach(el => el.remove());

            if (step === 2) {
                const commune = document.getElementById('commune_mariage');
                if (!commune.value.trim()) {
                    isValid = false;
                    displayError(commune, "La commune de mariage est obligatoire.");
                }

                if (document.getElementById('typeDemande').value === 'copieIntegrale') {
                    const nom = document.getElementById('nomEpoux');
                    const prenom = document.getElementById('prenomEpoux');
                    if (!nom.value.trim()) { isValid = false; displayError(nom, "Le nom du conjoint est obligatoire."); }
                    if (!prenom.value.trim()) { isValid = false; displayError(prenom, "Le prénom du conjoint est obligatoire."); }
                }
            } else if (step === 3) {
                const piece = document.getElementById('pieceIdentite');
                if (piece.files.length === 0) {
                    isValid = false;
                    displayError(piece, "Veuillez téléverser votre pièce d'identité.");
                }
            }

            return isValid;
        }

        function displayError(input, message) {
            input.classList.add('is-invalid');
            const err = document.createElement('div');
            err.className = 'error-message';
            err.innerText = message;
            input.closest('.input-group-custom').appendChild(err);
        }

        document.getElementById('demandeForm').addEventListener('submit', function(e) {
            if (formSubmitted) return;
            e.preventDefault();

            if (validateStep(3)) {
                if (document.getElementById('option2').checked) {
                    showLivraisonPopup();
                } else {
                    formSubmitted = true;
                    this.submit();
                }
            }
        });

        function showLivraisonPopup() {
            const quantite = parseInt(document.getElementById('quantite').value) || 1;
            const montantTimbreUnitaire = 500; 
            const montantLivraison = 1500; 
            
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
                showCancelButton: true,
                confirmButtonText: 'Payer & Valider',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#1977cc',
                preConfirm: () => {
                    const d = {
                        nom: document.getElementById('swal-nom_destinataire').value,
                        prenom: document.getElementById('swal-prenom_destinataire').value,
                        email: document.getElementById('swal-email_destinataire').value,
                        contact: document.getElementById('swal-contact_destinataire').value,
                        adresse: document.getElementById('swal-adresse_livraison').value,
                        ville: document.getElementById('swal-ville').value,
                        commune: document.getElementById('swal-commune_livraison').value,
                        quartier: document.getElementById('swal-quartier').value,
                        date: document.getElementById('swal-date_livraison').value,
                        heure: document.getElementById('swal-heure_livraison').value,
                        method: document.getElementById('swal-payment_method').value,
                        number: document.getElementById('swal-mtn_number') ? document.getElementById('swal-mtn_number').value : ''
                    };
                    
                    if(!d.nom || !d.prenom || !d.contact || !d.adresse || !d.ville || !d.commune || !d.quartier || !d.date || !d.heure) {
                        Swal.showValidationMessage('Veuillez remplir tous les champs obligatoires');
                        return false;
                    }
                    window.PaymentPopup = window.open('', 'PaymentPopup');
                    return d;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const d = result.value;
                    const form = document.getElementById('demandeForm');
                    const fields = {
                        'nom_destinataire': d.nom, 'prenom_destinataire': d.prenom,
                        'email_destinataire': d.email, 'contact_destinataire': d.contact,
                        'adresse_livraison': d.adresse, 'ville': d.ville,
                        'commune_livraison': d.commune, 'quartier': d.quartier,
                        'date_livraison': d.date, 'heure_livraison': d.heure,
                        'payment_method': d.method, 'mtn_number': d.number || d.contact,
                        'montant_livraison': montantLivraison, 'montant_timbre': montantTimbreTotal
                    };
                    for(let k in fields) {
                        let input = document.createElement('input');
                        input.type = 'hidden'; input.name = k; input.value = fields[k];
                        form.appendChild(input);
                    }
                    form.target = 'PaymentPopup';
                    formSubmitted = true;
                    form.submit();
                    
                    Swal.fire({
                        title: 'Paiement en cours',
                        html: 'Suivez les instructions dans le nouvel onglet.',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    
                    const check = setInterval(() => {
                        if (window.PaymentPopup && window.PaymentPopup.closed) {
                            clearInterval(check);
                            location.href = "{{ route('user.extrait.mariage.index') }}";
                        }
                    }, 1000);
                } else {
                    document.getElementById('option1').checked = true;
                }
            });
        }

        function selectPaymentMethod(method) {
            document.getElementById('swal-payment_method').value = method;
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.style.border = '1px solid #edf2f7';
                btn.style.backgroundColor = 'white';
            });
            const activeBtn = document.getElementById('btn-pay-' + method);
            if (method === 'wave') {
                activeBtn.style.border = '2px solid #1e3a8a';
                activeBtn.style.backgroundColor = '#eff6ff';
                document.getElementById('payment-phone-container').style.display = 'none';
            } else if (method === 'mtn') {
                activeBtn.style.border = '2px solid #fcb711';
                activeBtn.style.backgroundColor = '#fffbed';
                document.getElementById('payment-phone-container').style.display = 'block';
                document.getElementById('payment-phone-label').innerText = 'Numéro MTN Money';
            }
        }
    </script>
@endsection