@extends('user.layouts.template')

@section('content')
<style>
    .form-page-container { padding: 2rem 0; animation: fadeIn 0.6s ease-out; }
    .form-glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(15px);
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        padding: 3rem;
        max-width: 1400px;
        margin: 0 auto;
    }
    .form-header-box { text-align: center; margin-bottom: 2.5rem; }
    .form-header-box h2 {
        color: var(--text-navy);
        font-weight: 800;
        font-size: 2rem;
        letter-spacing: -1px;
        margin-bottom: 0.5rem;
    }
    .form-header-box p { color: #718096; font-size: 1rem; }

    .stepper-container { display: flex; justify-content: space-between; align-items: center; margin: 2rem auto; max-width: 700px; }
    .step-item { display: flex; flex-direction: column; align-items: center; flex: 1; position: relative; }
    .step-item:not(:last-child)::after {
        content: ''; position: absolute; top: 18px; right: -50%; width: 100%; height: 3px; background: #e9ecef; z-index: 0;
    }
    .step-item.completed:not(:last-child)::after { background: var(--success); }
    .step-number {
        width: 38px; height: 38px; border-radius: 50%; background: #e9ecef; color: #a3aed0;
        display: flex; align-items: center; justify-content: center; font-weight: 700;
        z-index: 1; transition: 0.3s;
    }
    .step-item.active .step-number { background: var(--primary); color: #fff; box-shadow: 0 4px 14px rgba(25,119,204,0.3); }
    .step-item.completed .step-number { background: var(--success); color: #fff; }
    .step-label { margin-top: 8px; font-size: 0.8rem; font-weight: 600; color: #a3aed0; }
    .step-item.active .step-label { color: var(--primary); }
    .step-item.completed .step-label { color: var(--success); }

    .form-step { display: none; animation: fadeIn 0.4s ease-out; }
    .form-step.active { display: block; }

    .form-section { margin-bottom: 2rem; }
    .form-section-title {
        display: flex; align-items: center; margin-bottom: 1.25rem;
        color: var(--primary); font-weight: 700; font-size: 1.05rem;
        text-transform: uppercase; letter-spacing: 1px;
    }
    .form-section-title i {
        width: 35px; height: 35px; background: var(--primary-light);
        display: flex; align-items: center; justify-content: center;
        border-radius: 10px; margin-right: 12px; font-size: 14px;
    }

    .input-group-custom { margin-bottom: 1.25rem; }
    .input-group-custom label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-navy); font-size: 0.9rem; padding-left: 5px; }
    .input-wrapper { position: relative; }
    .input-wrapper i {
        position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
        color: #a0aec0; font-size: 14px;
    }
    .form-control-custom {
        width: 100%; padding: 14px 20px 14px 50px; border-radius: 15px;
        border: 2px solid #f4f7fe; background: #f4f7fe;
        color: var(--text-navy); font-weight: 500; font-size: 0.95rem;
        transition: 0.3s;
    }
    .form-control-custom:focus { border-color: var(--primary); background: #fff; outline: none; }

    /* Sélecteur de quantité */
    .quantity-card {
        background: #fff; border-radius: 20px; padding: 1.5rem;
        border: 2px solid #f4f7fe; transition: 0.3s; margin-bottom: 1rem;
    }
    .quantity-card.has-value { border-color: var(--primary); background: #f8fbff; }
    .quantity-card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; }
    .quantity-card-title { font-weight: 700; color: var(--text-navy); font-size: 1rem; display: flex; align-items: center; gap: 12px; }
    .quantity-card-title i {
        width: 40px; height: 40px; border-radius: 12px; display: flex;
        align-items: center; justify-content: center; font-size: 18px;
    }
    .qty-stepper { display: flex; align-items: center; gap: 10px; }
    .qty-btn {
        width: 38px; height: 38px; border-radius: 12px; border: none;
        background: #f4f7fe; color: var(--primary); font-weight: 800; font-size: 18px;
        cursor: pointer; transition: 0.2s;
    }
    .qty-btn:hover { background: var(--primary); color: #fff; }
    .qty-btn:disabled { opacity: 0.4; cursor: not-allowed; }
    .qty-btn:disabled:hover { background: #f4f7fe; color: var(--primary); }
    .qty-input {
        width: 60px; text-align: center; padding: 8px; border-radius: 10px;
        border: 2px solid #f4f7fe; font-weight: 700; font-size: 1.1rem;
    }
    .quantity-card-meta { color: #718096; font-size: 0.85rem; }

    /* Récap total */
    .total-recap {
        background: linear-gradient(135deg, var(--primary) 0%, #4facfe 100%);
        color: #fff; padding: 1.5rem 2rem; border-radius: 20px;
        margin-top: 1.5rem; box-shadow: 0 10px 30px rgba(25,119,204,0.25);
    }
    .total-recap-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.95rem; }
    .total-recap-row.total {
        border-top: 1px solid rgba(255,255,255,0.3); margin-top: 8px; padding-top: 14px;
        font-weight: 800; font-size: 1.2rem;
    }
    .free-badge {
        display: inline-block; background: var(--success); color: #fff; padding: 3px 10px;
        border-radius: 50px; font-size: 0.7rem; font-weight: 700; margin-left: 6px;
    }

    /* Sous-formulaires (lignes) */
    .ligne-card {
        background: #fff; border-radius: 20px; padding: 1.5rem;
        border: 2px solid #f4f7fe; margin-bottom: 1.5rem; position: relative;
    }
    .ligne-card.simple { border-left: 5px solid var(--success); }
    .ligne-card.integral { border-left: 5px solid var(--pink); }
    .ligne-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding-bottom: 1rem; margin-bottom: 1rem; border-bottom: 1px solid #f0f2f5;
    }
    .ligne-card-title { font-weight: 700; color: var(--text-navy); font-size: 1.05rem; }
    .ligne-badge {
        padding: 5px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 700;
    }
    .ligne-badge.simple { background: rgba(1,181,116,0.1); color: var(--success); }
    .ligne-badge.integral { background: rgba(255,59,103,0.1); color: var(--pink); }

    .file-upload-area {
        border: 2px dashed #cbd5e0; border-radius: 15px; padding: 1.5rem;
        text-align: center; cursor: pointer; transition: 0.3s; display: block;
    }
    .file-upload-area:hover { border-color: var(--primary); background: #f8fbff; }
    .file-upload-area i { font-size: 1.8rem; color: var(--primary); margin-bottom: 0.5rem; }

    /* Mode de retrait */
    .delivery-card-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
    .delivery-option-card { position: relative; cursor: pointer; }
    .delivery-option-card input { position: absolute; opacity: 0; }
    .delivery-option-content {
        padding: 2rem; background: #fff; border-radius: 20px; border: 2px solid #f4f7fe;
        text-align: center; transition: 0.3s; height: 100%;
    }
    .delivery-option-content i { font-size: 2rem; margin-bottom: 1rem; color: var(--primary); }
    .delivery-option-card input:checked + .delivery-option-content {
        border-color: var(--primary); background: #f8fbff;
        box-shadow: 0 8px 25px rgba(25,119,204,0.15);
    }

    .stepper-footer {
        display: flex; justify-content: space-between; margin-top: 2rem;
        padding-top: 2rem; border-top: 1px solid #f4f7fe;
    }
    .btn-step {
        padding: 0.8rem 2rem; border-radius: 12px; font-weight: 700;
        transition: 0.3s; display: flex; align-items: center; gap: 10px;
    }
    .btn-prev { background: #f4f7fe; color: #718096; border: none; }
    .btn-prev:hover { background: #edf2f7; }
    .btn-next {
        background: var(--primary); color: #fff; border: none;
        box-shadow: 0 5px 15px rgba(25,119,204,0.1);
    }
    .btn-next:hover { background: #0d4a85; transform: translateX(3px); }

    /* Badges actes commandés */
    .acte-pill {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        border-radius: 14px;
        background: #fff;
        border: 2px solid #f4f7fe;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        min-width: 180px;
        transition: 0.2s;
    }
    .acte-pill.simple { border-left: 4px solid var(--success); }
    .acte-pill.integral { border-left: 4px solid var(--pink); }
    .acte-pill-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
    }
    .acte-pill.simple .acte-pill-num { background: rgba(1,181,116,0.1); color: var(--success); }
    .acte-pill.integral .acte-pill-num { background: rgba(255,59,103,0.1); color: var(--pink); }
    .acte-pill-title { font-weight: 700; color: var(--text-navy); font-size: 0.9rem; line-height: 1.2; }
    .acte-pill-type { font-size: 0.75rem; color: #718096; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
    .acte-pill > i {
        margin-left: auto;
        font-size: 1.1rem;
        color: #a0aec0;
    }
    .acte-pill.simple > i { color: var(--success); }
    .acte-pill.integral > i { color: var(--pink); }

    /* Modes de paiement */
    .payment-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }
    .payment-option { position: relative; cursor: pointer; }
    .payment-option input { position: absolute; opacity: 0; }
    .payment-option-content {
        padding: 1.25rem; background: #fff; border-radius: 16px;
        border: 2px solid #f4f7fe; text-align: center; transition: 0.3s;
    }
    .payment-option-content img { max-height: 40px; max-width: 80px; margin-bottom: 8px; }
    .payment-option-content h6 { font-weight: 700; color: var(--text-navy); margin-bottom: 4px; }
    .payment-option-content small { color: #718096; font-size: 0.8rem; }
    .payment-option input:checked + .payment-option-content {
        border-color: var(--primary); background: #f8fbff;
        box-shadow: 0 4px 15px rgba(25,119,204,0.15);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .form-glass-card { padding: 1.5rem; border-radius: 20px; margin: 0 -5px; }
        .form-header-box h2 { font-size: 1.5rem; }
        .delivery-card-grid { grid-template-columns: 1fr; }
        .quantity-card-header { flex-direction: column; gap: 1rem; align-items: flex-start; }
    }
</style>

<div class="form-page-container">
    <div class="form-glass-card">
        <div class="form-header-box">
            <h2><i class="fas fa-shopping-basket" style="color: var(--primary); margin-right: 10px;"></i> Demande groupée</h2>
            <p>Commandez plusieurs actes en une seule démarche</p>
            @if($freeRequestsModeActive && $freeRequestsRemaining > 0)
                <div style="margin-top: 12px;">
                    <span class="free-badge"><i class="fas fa-gift"></i> {{ $freeRequestsRemaining }} timbre(s) offert(s) restant(s)</span>
                </div>
            @endif
        </div>

        <!-- Stepper -->
        <div class="stepper-container">
            <div class="step-item active" id="step-1-indicator">
                <div class="step-number">1</div>
                <div class="step-label">Quantités</div>
            </div>
            <div class="step-item" id="step-2-indicator">
                <div class="step-number">2</div>
                <div class="step-label">Informations</div>
            </div>
            <div class="step-item" id="step-3-indicator">
                <div class="step-number">3</div>
                <div class="step-label">Retrait & Paiement</div>
            </div>
        </div>

        <form id="naissanceGroupeeForm" action="{{ route('user.extrait.groupee.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="commune" value="{{ Auth::user()->commune ?? 'plateau' }}">

            <!-- ÉTAPE 1 : Quantités -->
            <div class="form-step active" id="step-1">
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-list-ol"></i> Combien d'actes voulez-vous ?
                    </div>

                    <div class="quantity-card" id="card-simple">
                        <div class="quantity-card-header">
                            <div class="quantity-card-title">
                                <i class="fas fa-file-alt" style="background: rgba(1,181,116,0.1); color: var(--success);"></i>
                                Acte simple
                            </div>
                            <div class="qty-stepper">
                                <button type="button" class="qty-btn" onclick="updateQty('qty_simple', -1)">−</button>
                                <input type="number" id="qty_simple" name="qty_simple" class="qty-input" min="0" max="10" value="0" readonly>
                                <button type="button" class="qty-btn" onclick="updateQty('qty_simple', 1)">+</button>
                            </div>
                        </div>
                        <div class="quantity-card-meta">{{ \App\Http\Controllers\User\Extrait\Naissance\NaissanceGroupeController::TARIF_TIMBRE }} FCFA / copie</div>
                    </div>

                    <div class="quantity-card" id="card-integral">
                        <div class="quantity-card-header">
                            <div class="quantity-card-title">
                                <i class="fas fa-file-contract" style="background: rgba(255,59,103,0.1); color: var(--pink);"></i>
                                Acte intégral
                            </div>
                            <div class="qty-stepper">
                                <button type="button" class="qty-btn" onclick="updateQty('qty_integral', -1)">−</button>
                                <input type="number" id="qty_integral" name="qty_integral" class="qty-input" min="0" max="10" value="0" readonly>
                                <button type="button" class="qty-btn" onclick="updateQty('qty_integral', 1)">+</button>
                            </div>
                        </div>
                        <div class="quantity-card-meta">{{ \App\Http\Controllers\User\Extrait\Naissance\NaissanceGroupeController::TARIF_TIMBRE }} FCFA / copie (avec filiation)</div>
                    </div>

                    <!-- Récap dynamique -->
                    <div class="total-recap" id="recap-step1" style="display:none;">
                        <div class="total-recap-row">
                            <span><span id="recap-simple-qty">0</span> acte(s) simple(s)</span>
                            <span id="recap-simple-amount">0 FCFA</span>
                        </div>
                        <div class="total-recap-row">
                            <span><span id="recap-integral-qty">0</span> acte(s) intégral(s)</span>
                            <span id="recap-integral-amount">0 FCFA</span>
                        </div>
                        <div class="total-recap-row" id="recap-free-row" style="display:none;">
                            <span><i class="fas fa-gift"></i> Timbres offerts (<span id="recap-free-qty">0</span>)</span>
                            <span id="recap-free-amount">−0 FCFA</span>
                        </div>
                        <div class="total-recap-row total">
                            <span>Sous-total timbres</span>
                            <span id="recap-total">0 FCFA</span>
                        </div>
                        <div style="opacity: 0.85; font-size: 0.8rem; margin-top: 8px;">
                            <i class="fas fa-info-circle"></i> Frais de livraison ajoutés ultérieurement si applicable.
                        </div>
                    </div>
                </div>

                <div class="stepper-footer">
                    <a href="{{ route('user.extrait.create') }}" class="btn-step btn-prev" style="text-decoration: none;">
                        <i class="fas fa-arrow-left"></i> Annuler
                    </a>
                    <button type="button" class="btn-step btn-next" onclick="goToStep2()">
                        Continuer <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- ÉTAPE 2 : 1 SEUL titre + N cards générées dynamiquement (1 par acte/personne) -->
            <div class="form-step" id="step-2">
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-user-edit"></i> Informations sur l'acte
                    </div>
                    <p style="color:#718096; margin-bottom:1.5rem;">
                        Renseignez les informations pour <strong>chaque personne</strong> concernée.
                        Les actes <span style="color:var(--success); font-weight:700;">simples</span> et
                        <span style="color:var(--pink); font-weight:700;">intégraux</span> sont distingués par leur badge.
                    </p>

                    <div id="lignes-container">
                        <!-- N cards générées dynamiquement par JS -->
                    </div>
                </div>

                <div class="stepper-footer">
                    <button type="button" class="btn-step btn-prev" onclick="goToStep(1)">
                        <i class="fas fa-arrow-left"></i> Précédent
                    </button>
                    <button type="button" class="btn-step btn-next" onclick="goToStep3()">
                        Continuer <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- ÉTAPE 3 : Mode de retrait + Paiement -->
            <div class="form-step" id="step-3">
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-truck"></i> Mode de retrait
                    </div>
                    <div class="delivery-card-grid">
                        <label class="delivery-option-card">
                            <input type="radio" name="choix_option" value="Retrait sur place" checked onchange="onDeliveryChange()">
                            <div class="delivery-option-content">
                                <i class="fas fa-university"></i>
                                <h5>Retrait en Mairie</h5>
                                <p>Gratuit - Guichet</p>
                            </div>
                        </label>
                        <label class="delivery-option-card">
                            <input type="radio" name="choix_option" value="livraison" onchange="onDeliveryChange()">
                            <div class="delivery-option-content">
                                <i class="fas fa-motorcycle"></i>
                                <h5>Livraison Express</h5>
                                <p>{{ \App\Http\Controllers\User\Extrait\Naissance\NaissanceGroupeController::TARIF_LIVRAISON }} FCFA - Domicile</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Champs livraison (cachés par défaut) -->
                <div class="form-section" id="livraison-fields" style="display:none;">
                    <div class="form-section-title">
                        <i class="fas fa-map-marker-alt"></i> Informations de livraison
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group-custom">
                                <label>Nom destinataire :</label>
                                <div class="input-wrapper">
                                    <input type="text" name="nom_destinataire" class="form-control-custom" placeholder="Nom">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group-custom">
                                <label>Prénom destinataire :</label>
                                <div class="input-wrapper">
                                    <input type="text" name="prenom_destinataire" class="form-control-custom" placeholder="Prénom">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group-custom">
                                <label>Téléphone :</label>
                                <div class="input-wrapper">
                                    <input type="tel" name="contact_destinataire" class="form-control-custom" placeholder="07xxxxxxxx">
                                    <i class="fas fa-phone"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group-custom">
                                <label>Email :</label>
                                <div class="input-wrapper">
                                    <input type="email" name="email_destinataire" class="form-control-custom" placeholder="email@exemple.com">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group-custom">
                        <label>Adresse complète :</label>
                        <div class="input-wrapper">
                            <input type="text" name="adresse_livraison" class="form-control-custom" placeholder="Adresse de livraison">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="input-group-custom">
                                <label>Commune :</label>
                                <div class="input-wrapper">
                                    <input type="text" name="commune_livraison" class="form-control-custom" placeholder="Ex: Plateau">
                                    <i class="fas fa-city"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group-custom">
                                <label>Quartier :</label>
                                <div class="input-wrapper">
                                    <input type="text" name="quartier" class="form-control-custom" placeholder="Quartier">
                                    <i class="fas fa-map"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="input-group-custom">
                                <label>Ville :</label>
                                <div class="input-wrapper">
                                    <input type="text" name="ville" class="form-control-custom" placeholder="Ville">
                                    <i class="fas fa-globe"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="input-group-custom">
                                <label>Date de livraison souhaitée :</label>
                                <div class="input-wrapper">
                                    <input type="date" name="date_livraison" class="form-control-custom" min="{{ now()->addDay()->toDateString() }}">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group-custom">
                                <label>Heure de livraison souhaitée :</label>
                                <div class="input-wrapper">
                                    <input type="time" name="heure_livraison" class="form-control-custom">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mode de paiement (visible uniquement si livraison) -->
                <div class="form-section" id="payment-method-section" style="display:none;">
                    <div class="form-section-title">
                        <i class="fas fa-credit-card"></i> Mode de paiement
                    </div>
                    <div class="payment-grid">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="wave" checked onchange="onPaymentMethodChange()">
                            <div class="payment-option-content">
                                <img src="{{ asset('assets/assets/img/wave.png') }}" alt="Wave" onerror="this.style.display='none'">
                                <h6>Wave</h6>
                                <small>Paiement instantané</small>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="mtn" onchange="onPaymentMethodChange()">
                            <div class="payment-option-content">
                                <img src="{{ asset('assets/assets/img/mtn.png') }}" alt="MTN" onerror="this.style.display='none'">
                                <h6>MTN Money</h6>
                                <small>Mobile Money</small>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="orange" onchange="onPaymentMethodChange()">
                            <div class="payment-option-content">
                                <img src="{{ asset('assets/assets/img/orange.png') }}" alt="Orange" onerror="this.style.display='none'">
                                <h6>Orange Money</h6>
                                <small>Via CinetPay</small>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="moov" onchange="onPaymentMethodChange()">
                            <div class="payment-option-content">
                                <img src="{{ asset('assets/assets/img/moov.png') }}" alt="Moov" onerror="this.style.display='none'">
                                <h6>Moov Money</h6>
                                <small>Via CinetPay</small>
                            </div>
                        </label>
                    </div>

                    <!-- Champ MTN (visible uniquement si MTN sélectionné) -->
                    <div id="mtn-phone-block" style="display:none; margin-top: 1rem;">
                        <div class="input-group-custom">
                            <label>Numéro MTN à débiter :</label>
                            <div class="input-wrapper">
                                <input type="tel" name="mtn_number" class="form-control-custom" placeholder="07XXXXXXXX (10 chiffres)" maxlength="10">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <small class="text-muted" style="padding-left: 5px;">
                                <i class="fas fa-info-circle"></i> Vous recevrez une notification pour valider le paiement sur votre téléphone.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Récap final -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-receipt"></i> Récapitulatif
                    </div>
                    <div class="total-recap" id="recap-final">
                        <div class="total-recap-row">
                            <span><span id="final-simple-qty">0</span> acte(s) simple(s)</span>
                            <span id="final-simple-amount">0 FCFA</span>
                        </div>
                        <div class="total-recap-row">
                            <span><span id="final-integral-qty">0</span> acte(s) intégral(s)</span>
                            <span id="final-integral-amount">0 FCFA</span>
                        </div>
                        <div class="total-recap-row" id="final-free-row" style="display:none;">
                            <span><i class="fas fa-gift"></i> Timbres offerts (<span id="final-free-qty">0</span>)</span>
                            <span id="final-free-amount">−0 FCFA</span>
                        </div>
                        <div class="total-recap-row" id="final-livraison-row" style="display:none;">
                            <span><i class="fas fa-motorcycle"></i> Livraison</span>
                            <span>{{ \App\Http\Controllers\User\Extrait\Naissance\NaissanceGroupeController::TARIF_LIVRAISON }} FCFA</span>
                        </div>
                        <div class="total-recap-row total">
                            <span>Total à payer</span>
                            <span id="final-total">0 FCFA</span>
                        </div>
                    </div>
                </div>

                <div class="stepper-footer">
                    <button type="button" class="btn-step btn-prev" onclick="goToStep(2)">
                        <i class="fas fa-arrow-left"></i> Précédent
                    </button>
                    <button type="submit" class="btn-step btn-next" style="background: var(--success);">
                        <i class="fas fa-check-circle"></i> Valider la demande
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Constantes serveur
    const TARIF_TIMBRE = {{ \App\Http\Controllers\User\Extrait\Naissance\NaissanceGroupeController::TARIF_TIMBRE }};
    const TARIF_LIVRAISON = {{ \App\Http\Controllers\User\Extrait\Naissance\NaissanceGroupeController::TARIF_LIVRAISON }};
    const FREE_REQUESTS_REMAINING = {{ (int) $freeRequestsRemaining }};
    const FREE_REQUESTS_MODE_ACTIVE = {{ $freeRequestsModeActive ? 'true' : 'false' }};

    // ─────────────── Stepper Navigation ───────────────
    function goToStep(step) {
        document.querySelectorAll('.form-step').forEach(el => el.classList.remove('active'));
        document.getElementById('step-' + step).classList.add('active');

        document.querySelectorAll('.step-item').forEach(el => {
            el.classList.remove('active', 'completed');
        });
        for (let i = 1; i < step; i++) {
            document.getElementById('step-' + i + '-indicator').classList.add('completed');
        }
        document.getElementById('step-' + step + '-indicator').classList.add('active');

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ─────────────── Étape 1 : Quantités ───────────────
    function updateQty(field, delta) {
        const input = document.getElementById(field);
        const newVal = Math.max(0, Math.min(10, parseInt(input.value || 0) + delta));
        input.value = newVal;
        refreshRecapStep1();
    }

    function calculateFreeBreakdown(qtyTotal) {
        if (!FREE_REQUESTS_MODE_ACTIVE || FREE_REQUESTS_REMAINING <= 0) {
            return { freeTimbres: 0, paidTimbres: qtyTotal };
        }
        const freeTimbres = Math.min(FREE_REQUESTS_REMAINING, qtyTotal);
        return { freeTimbres, paidTimbres: qtyTotal - freeTimbres };
    }

    function refreshRecapStep1() {
        const qS = parseInt(document.getElementById('qty_simple').value || 0);
        const qI = parseInt(document.getElementById('qty_integral').value || 0);
        const qT = qS + qI;

        document.getElementById('card-simple').classList.toggle('has-value', qS > 0);
        document.getElementById('card-integral').classList.toggle('has-value', qI > 0);

        if (qT === 0) {
            document.getElementById('recap-step1').style.display = 'none';
            return;
        }

        const { freeTimbres, paidTimbres } = calculateFreeBreakdown(qT);
        const subTotal = paidTimbres * TARIF_TIMBRE;
        const freeAmount = freeTimbres * TARIF_TIMBRE;

        document.getElementById('recap-simple-qty').textContent = qS;
        document.getElementById('recap-integral-qty').textContent = qI;
        document.getElementById('recap-simple-amount').textContent = (qS * TARIF_TIMBRE).toLocaleString() + ' FCFA';
        document.getElementById('recap-integral-amount').textContent = (qI * TARIF_TIMBRE).toLocaleString() + ' FCFA';

        if (freeTimbres > 0) {
            document.getElementById('recap-free-row').style.display = 'flex';
            document.getElementById('recap-free-qty').textContent = freeTimbres;
            document.getElementById('recap-free-amount').textContent = '−' + freeAmount.toLocaleString() + ' FCFA';
        } else {
            document.getElementById('recap-free-row').style.display = 'none';
        }

        document.getElementById('recap-total').textContent = subTotal.toLocaleString() + ' FCFA';
        document.getElementById('recap-step1').style.display = 'block';
    }

    function goToStep2() {
        const qS = parseInt(document.getElementById('qty_simple').value || 0);
        const qI = parseInt(document.getElementById('qty_integral').value || 0);
        if (qS + qI === 0) {
            alert('Veuillez choisir au moins un acte (simple ou intégral).');
            return;
        }
        generateLignes(qS, qI);
        goToStep(2);
    }

    // Génère N cards : une par acte commandé (simples puis intégraux)
    function generateLignes(qS, qI) {
        const container = document.getElementById('lignes-container');
        container.innerHTML = '';
        let position = 0;

        for (let i = 0; i < qS; i++) {
            container.appendChild(buildLigne(position, 'simple'));
            position++;
        }
        for (let i = 0; i < qI; i++) {
            container.appendChild(buildLigne(position, 'extrait_integral'));
            position++;
        }
    }

    // Construit le formulaire d'une ligne : champs identité + filiation (si intégral) + CNI
    function buildLigne(position, type) {
        const isIntegral = type === 'extrait_integral';
        const card = document.createElement('div');
        card.className = 'ligne-card ' + (isIntegral ? 'integral' : 'simple');
        card.innerHTML = `
            <div class="ligne-card-header">
                <div class="ligne-card-title">
                    <i class="fas ${isIntegral ? 'fa-file-contract' : 'fa-file-alt'}"></i>
                    Acte n°${position + 1}
                </div>
                <span class="ligne-badge ${isIntegral ? 'integral' : 'simple'}">${isIntegral ? 'Intégral' : 'Simple'}</span>
            </div>

            <input type="hidden" name="lignes[${position}][type_document]" value="${type}">

            <div class="row">
                <div class="col-md-4 mb-3"><div class="input-group-custom"><label>Nom : *</label><div class="input-wrapper"><input type="text" name="lignes[${position}][name]" class="form-control-custom" placeholder="Nom" required><i class="fas fa-id-card"></i></div></div></div>
                <div class="col-md-4 mb-3"><div class="input-group-custom"><label>Prénoms : *</label><div class="input-wrapper"><input type="text" name="lignes[${position}][prenom]" class="form-control-custom" placeholder="Prénoms" required><i class="fas fa-id-card"></i></div></div></div>
                <div class="col-md-4 mb-3"><div class="input-group-custom"><label>Lieu de naissance : *</label><div class="input-wrapper"><input type="text" name="lignes[${position}][commune_naissance]" class="form-control-custom" placeholder="Commune" required><i class="fas fa-map-marker-alt"></i></div></div></div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3"><div class="input-group-custom"><label>N° Registre :</label><div class="input-wrapper"><input type="text" name="lignes[${position}][number]" class="form-control-custom" placeholder="Ex: 123/2024"><i class="fas fa-hashtag"></i></div></div></div>
                <div class="col-md-6 mb-3"><div class="input-group-custom"><label>Date Registre :</label><div class="input-wrapper"><input type="date" name="lignes[${position}][DateR]" class="form-control-custom"><i class="fas fa-calendar-alt"></i></div></div></div>
            </div>

            <!-- Filiation : présente dans tous les cas (comme dans la demande simple existante)
                 - Optionnelle pour les actes simples
                 - Obligatoire pour les actes intégraux -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="input-group-custom">
                        <label>Nom et Prénoms du père :${isIntegral ? ' *' : ''}</label>
                        <div class="input-wrapper">
                            <input type="text" name="lignes[${position}][nom_prenoms_pere]" class="form-control-custom" placeholder="Nom complet du père" ${isIntegral ? 'required' : ''}>
                            <i class="fas fa-male"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="input-group-custom">
                        <label>Nom et Prénoms de la mère :${isIntegral ? ' *' : ''}</label>
                        <div class="input-wrapper">
                            <input type="text" name="lignes[${position}][nom_prenoms_mere]" class="form-control-custom" placeholder="Nom complet de la mère" ${isIntegral ? 'required' : ''}>
                            <i class="fas fa-female"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="input-group-custom">
                <label>Pièce d'identité (CNI/Passeport) : *</label>
                <label class="file-upload-area">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <h6 id="file-name-${position}" style="margin: 5px 0; color: var(--text-navy); font-weight: 700;">Téléverser</h6>
                    <p style="font-size: 0.8rem; color: #718096; margin: 0;">PDF, JPG ou PNG (max 5 Mo)</p>
                    <input type="file" name="lignes[${position}][CNI]" class="d-none" required accept=".jpg,.jpeg,.png,.pdf"
                           onchange="document.getElementById('file-name-${position}').textContent = this.files[0]?.name || 'Téléverser'">
                </label>
            </div>
        `;
        return card;
    }

    // ─────────────── Étape 3 : Mode de retrait + récap final ───────────────
    function goToStep3() {
        // Validation simple : tous les champs requis remplis
        const requiredInputs = document.querySelectorAll('#step-2 input[required]');
        for (const input of requiredInputs) {
            if (!input.value || (input.type === 'file' && !input.files[0])) {
                alert('Veuillez remplir tous les champs obligatoires (marqués d\'un *).');
                input.focus();
                return;
            }
        }
        refreshRecapFinal();
        goToStep(3);
    }

    function onDeliveryChange() {
        const selected = document.querySelector('input[name="choix_option"]:checked').value;
        const isLivraison = selected === 'livraison';
        document.getElementById('livraison-fields').style.display = isLivraison ? 'block' : 'none';
        document.getElementById('final-livraison-row').style.display = isLivraison ? 'flex' : 'none';
        document.getElementById('payment-method-section').style.display = isLivraison ? 'block' : 'none';
        refreshRecapFinal();
    }

    function onPaymentMethodChange() {
        const method = document.querySelector('input[name="payment_method"]:checked')?.value;
        document.getElementById('mtn-phone-block').style.display = method === 'mtn' ? 'block' : 'none';
    }

    // ─────────────── Soumission AJAX du formulaire ───────────────
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('naissanceGroupeeForm');
        if (!form) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalLabel = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement en cours...';

            try {
                const formData = new FormData(form);

                // ⚠ Important : ouvrir le nouvel onglet AVANT le fetch (sinon les navigateurs
                // bloquent window.open hors d'un événement utilisateur synchrone).
                // Si pas livraison : pas besoin de nouvel onglet.
                const isLivraison = document.querySelector('input[name="choix_option"]:checked').value === 'livraison';
                let paymentTab = null;
                if (isLivraison) {
                    paymentTab = window.open('about:blank', '_blank');
                }

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token')
                    }
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    if (paymentTab) paymentTab.close();
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalLabel;
                    alert(data.message || 'Une erreur est survenue. Veuillez vérifier vos informations et réessayer.');
                    return;
                }

                // Cas 1 : Paiement requis → ouvrir l'URL de paiement dans le nouvel onglet
                if (data.payment_url && paymentTab) {
                    paymentTab.location.href = data.payment_url;
                    // Rediriger l'onglet courant vers la liste des demandes
                    window.location.href = data.redirect_url || '{{ route("user.extrait.index") }}';
                }
                // Cas 2 : Pas de paiement (retrait OU livraison gratuite)
                else {
                    if (paymentTab) paymentTab.close();
                    window.location.href = data.redirect_url || '{{ route("user.extrait.index") }}';
                }

            } catch (error) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalLabel;
                console.error('Erreur soumission :', error);
                alert('Erreur de communication avec le serveur. Réessayez.');
            }
        });
    });

    function refreshRecapFinal() {
        const qS = parseInt(document.getElementById('qty_simple').value || 0);
        const qI = parseInt(document.getElementById('qty_integral').value || 0);
        const qT = qS + qI;
        const { freeTimbres, paidTimbres } = calculateFreeBreakdown(qT);
        const subTotal = paidTimbres * TARIF_TIMBRE;
        const freeAmount = freeTimbres * TARIF_TIMBRE;

        const choix = document.querySelector('input[name="choix_option"]:checked').value;
        const livraison = choix === 'livraison' ? TARIF_LIVRAISON : 0;

        document.getElementById('final-simple-qty').textContent = qS;
        document.getElementById('final-integral-qty').textContent = qI;
        document.getElementById('final-simple-amount').textContent = (qS * TARIF_TIMBRE).toLocaleString() + ' FCFA';
        document.getElementById('final-integral-amount').textContent = (qI * TARIF_TIMBRE).toLocaleString() + ' FCFA';

        if (freeTimbres > 0) {
            document.getElementById('final-free-row').style.display = 'flex';
            document.getElementById('final-free-qty').textContent = freeTimbres;
            document.getElementById('final-free-amount').textContent = '−' + freeAmount.toLocaleString() + ' FCFA';
        } else {
            document.getElementById('final-free-row').style.display = 'none';
        }

        document.getElementById('final-total').textContent = (subTotal + livraison).toLocaleString() + ' FCFA';
    }
</script>
@endsection
