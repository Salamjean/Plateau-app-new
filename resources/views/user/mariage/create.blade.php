@extends('user.layouts.template')
@section('content')

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
                    <div class="form-group" style="display: none;">
                        <label for="commune" class="form-label">Commune d'enregistrement</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-city"></i>
                            <input type="text" id="commune" value="Plateau" name="commune" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="commune_mariage" class="form-label">Commune de mariage</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" id="commune_mariage" name="commune_mariage" class="form-control" value="{{ old('commune_mariage') }}">
                        </div>
                        @error('commune_mariage') <span class="error-message">{{ $message }}</span> @enderror
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
                        <div id="payment-phone-container" style="display: block; margin-top: 10px;">
                            <label id="payment-phone-label" style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Numéro Wave</label>
                            <input id="swal-mtn_number" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Ex: 05... (sans indicatif)" value="">
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

                    if (!nom_destinataire || !prenom_destinataire || !email_destinataire || !contact_destinataire || !adresse_livraison || !ville || !commune_livraison || !quartier || !date_livraison || !heure_livraison) {
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
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email_destinataire)) {
                        Swal.showValidationMessage("Veuillez entrer une adresse email valide.");
                        return false;
                    }
                    const cleanContact = contact_destinataire.replace(/\s+/g, '');
                    if (!/^\d{8,15}$/.test(cleanContact)) {
                        Swal.showValidationMessage("Veuillez entrer un numéro de téléphone de contact valide (8 à 15 chiffres).");
                        return false;
                    }
                    
                    const payment_number = document.getElementById('swal-mtn_number') ? document.getElementById('swal-mtn_number').value.replace(/\s+/g, '') : cleanContact;
                    const payment_method = document.getElementById('swal-payment_method').value;

                    if (payment_method === 'mtn' || payment_method === 'wave') {
                        if (!/^\d{10}$/.test(payment_number)) {
                            Swal.showValidationMessage('Veuillez entrer un numéro ' + (payment_method === 'mtn' ? 'MTN Money' : 'Wave') + ' valide à 10 chiffres.');
                            return false;
                        }
                    }

                    return {
                        nom_destinataire, prenom_destinataire, email_destinataire, contact_destinataire, adresse_livraison, ville, commune_livraison, quartier,
                        date_livraison, heure_livraison,
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
                    
                    if (formData.payment_method === 'wave') {
                        // Afficher un loader pendant la redirection Wave
                        const width = 500;
                        const height = 500;
                        const left = (screen.width - width) / 2;
                        const top = (screen.height - height) / 2;
                        const popup = window.open('', 'WavePaymentPopup', `width=${width},height=${height},top=${top},left=${left},scrollbars=yes,resizable=yes`);
                        
                        if (popup) {
                            form.target = 'WavePaymentPopup';
                            
                            Swal.fire({
                                title: 'Paiement en cours',
                                html: 'Veuillez finaliser le paiement et scanner le QR code dans <b>la nouvelle fenêtre</b> qui vient de s\'ouvrir.<br><br><span style="color:#555;font-size:0.9rem;">La page s\'actualisera automatiquement à la fin de la transaction.</span>',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const timer = setInterval(() => {
                                if (popup.closed) {
                                    clearInterval(timer);
                                    if (window.paymentSuccess) {
                                        window.location.href = window.paymentSuccessUrl || "{{ route('user.extrait.mariage.index') }}";
                                    } else {
                                        window.location.reload();
                                    }
                                }
                            }, 1000);
                        } else {
                            form.target = '_self';
                            Swal.fire({
                                title: 'Redirection vers Wave',
                                html: `Le bloqueur de popup est activé. Redirection dans la page actuelle...`,
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                        }
                    } else {
                        // Paiement MTN MoMo (Push)
                        form.target = '_self';
                        Swal.fire({
                            title: 'Initiation du paiement MTN',
                            html: `Veuillez patienter... Un message push apparaîtra sur le téléphone <b>${formData.mtn_number}</b> pour confirmer le paiement.`,
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
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

        // Fonction pour sélectionner la méthode de paiement
        function selectPaymentMethod(method) {
            if (method !== 'wave' && method !== 'mtn') return;
            
            // Réinitialiser tous les boutons
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                if(!btn.classList.contains('opacity-50')) { // Reset style only for non-disabled ones
                    btn.style.border = '1px solid #edf2f7';
                    btn.style.backgroundColor = 'white';
                }
            });
            
            // Appliquer le style actif au bouton sélectionné
            const activeBtn = document.getElementById('btn-pay-' + method);
            if (method === 'wave') {
                activeBtn.style.border = '2px solid #1e3a8a';
                activeBtn.style.backgroundColor = '#eff6ff';
                document.getElementById('payment-phone-container').style.display = 'block';
                document.getElementById('payment-phone-label').innerText = 'Numéro Wave';
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