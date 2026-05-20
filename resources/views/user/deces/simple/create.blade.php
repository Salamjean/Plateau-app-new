@extends('user.layouts.template')

@section('content')
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

        .delivery-option-card input:checked+.delivery-option-content {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(25, 119, 204, 0.1);
        }

        .delivery-option-card input:checked+.delivery-option-content i {
            color: var(--primary);
        }

        /* Options cards (état matrimonial / lieu décès) */
        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .option-card {
            position: relative;
            cursor: pointer;
        }

        .option-card input {
            position: absolute;
            opacity: 0;
        }

        .option-card-content {
            padding: 1.5rem;
            background: #fff;
            border-radius: 16px;
            border: 2px solid #f4f7fe;
            text-align: center;
            transition: 0.3s;
        }

        .option-card input:checked+.option-card-content {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-3px);
        }

        .option-title {
            display: block;
            font-weight: 700;
            color: var(--text-navy);
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
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
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

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
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

        @media (max-width: 768px) {
            .form-page-container {
                padding: 1rem 0;
            }

            .form-glass-card {
                padding: 1.5rem;
                border-radius: 20px;
                margin: 0;
                box-shadow: none;
                border: none;
                background: #fff;
            }

            .form-header-box h2 {
                font-size: 1.5rem;
            }

            .stepper-container {
                margin-bottom: 2rem;
                gap: 5px;
            }

            .stepper-container::before {
                top: 15px;
            }

            .step-label {
                font-size: 0.55rem;
                text-align: center;
                display: none;
                /* Hide labels on very small screens or keep them tiny */
            }

            .step-item.active .step-label {
                display: block;
                position: absolute;
                top: 45px;
                width: 80px;
            }

            .step-number {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }

            .form-section-title {
                font-size: 0.9rem;
            }

            .delivery-card-grid,
            .options-grid,
            .type-cards-grid,
            .quantity-cards-row,
            .quantity-cards-row.dual-card {
                grid-template-columns: 1fr !important;
                gap: 1rem;
            }

            .delivery-option-content {
                padding: 1.25rem;
            }

            .btn-step {
                padding: 0.7rem 1.2rem;
                font-size: 0.9rem;
                flex: 1;
                justify-content: center;
            }

            input[type="date"] {
                min-height: 48px;
            }

            .type-option-content {
                padding: 1rem;
            }
        }

        .type-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .type-option-card {
            position: relative;
            cursor: pointer;
        }

        .type-option-card input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .type-option-content {
            padding: 1.5rem 1rem;
            background: #fff;
            border-radius: 16px;
            border: 2px solid #f4f7fe;
            text-align: center;
            transition: all 0.25s ease;
            height: 100%;
        }

        .type-option-content i {
            font-size: 1.6rem;
            color: #a0aec0;
            margin-bottom: 0.6rem;
            display: block;
            transition: 0.25s;
        }

        .type-option-content h6 {
            font-weight: 700;
            color: var(--text-navy);
            margin-bottom: 0.2rem;
            font-size: 0.95rem;
        }

        .type-option-content small {
            color: #718096;
            font-size: 0.78rem;
        }

        .type-option-card input:checked+.type-option-content {
            border-color: var(--primary);
            background: var(--primary-light);
            box-shadow: 0 8px 20px rgba(25, 119, 204, 0.12);
            transform: translateY(-3px);
        }

        .type-option-card input:checked+.type-option-content i {
            color: var(--primary);
        }

        .qty-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f0f2f5;
            animation: fadeIn 0.3s ease-out;
        }

        .qty-section-label {
            font-weight: 600;
            color: #718096;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
        }

        .quantity-cards-row {
            display: grid;
            grid-template-columns: minmax(200px, 420px);
            justify-content: center;
        }

        .quantity-cards-row.dual-card {
            grid-template-columns: 1fr 1fr;
            max-width: 100%;
        }

        .quantity-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.25rem;
            border: 2px solid #f4f7fe;
            transition: 0.3s;
        }

        .quantity-card.has-value {
            border-color: var(--primary);
            background: #f8fbff;
        }

        .quantity-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .quantity-card-title {
            font-weight: 700;
            color: var(--text-navy);
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-card-meta {
            color: #718096;
            font-size: 0.82rem;
            margin-top: 0.5rem;
        }

        .qty-stepper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qty-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: none;
            background: #f4f7fe;
            color: var(--primary);
            font-weight: 800;
            font-size: 16px;
            cursor: pointer;
            transition: 0.2s;
        }

        .qty-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        .qty-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .qty-btn:disabled:hover {
            background: #f4f7fe;
            color: var(--primary);
        }

        .qty-input {
            width: 52px;
            text-align: center;
            padding: 6px;
            border-radius: 8px;
            border: 2px solid #f4f7fe;
            font-weight: 700;
            font-size: 1rem;
        }
    </style>

    <div class="form-page-container">
        <div class="form-glass-card">
            <div class="form-header-box">
                <h2>Demande d'acte de décès</h2>
                <p class="text-muted">Commandez un acte de décès en quelques étapes simples.</p>
            </div>

            <!-- Stepper -->
            <div class="stepper-container">
                <div class="step-item active" id="step-1-indicator">
                    <div class="step-number">1</div>
                    <div class="step-label">Type d'acte</div>
                </div>
                <div class="step-item" id="step-2-indicator">
                    <div class="step-number">2</div>
                    <div class="step-label">Identité défunt</div>
                </div>
                <div class="step-item" id="step-3-indicator">
                    <div class="step-number">3</div>
                    <div class="step-label">Documents</div>
                </div>
                <div class="step-item" id="step-4-indicator">
                    <div class="step-number">4</div>
                    <div class="step-label">Retrait</div>
                </div>
            </div>

            <form id="deathForm" method="POST" enctype="multipart/form-data"
                action="{{ route('user.extrait.deces.store') }}">
                @csrf
                <input type="hidden" name="commune" value="Plateau">

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert"
                        style="border-radius: 15px;">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert"
                        style="border-radius: 15px;">
                        <i class="fas fa-exclamation-circle me-2"></i> Veuillez corriger les erreurs dans le formulaire.
                        <ul class="mb-0 mt-2 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- ÉTAPE 1: Type de document -->
                <div class="form-step active" id="step-1">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-file-alt"></i> Quel type d'acte souhaitez-vous ?
                        </div>

                        <div class="type-cards-grid">
                            <label class="type-option-card">
                                <input type="radio" name="type" value="simple" checked
                                    onchange="onDecesTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-file-alt"></i>
                                    <h6>Copie simple</h6>
                                    <small>Extrait d'acte de décès</small>
                                </div>
                            </label>
                            <label class="type-option-card">
                                <input type="radio" name="type" value="integrale" onchange="onDecesTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-file-contract"></i>
                                    <h6>Copie intégrale</h6>
                                    <small>Acte intégral de décès</small>
                                </div>
                            </label>
                            <label class="type-option-card">
                                <input type="radio" name="type" value="groupee" onchange="onDecesTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-copy"></i>
                                    <h6>Simple + Intégrale</h6>
                                    <small>Les deux types d'acte</small>
                                </div>
                            </label>
                        </div>

                        <!-- Quantité -->
                        <div class="qty-section">
                            <div class="qty-section-label" id="qty-label-deces-section"><i class="fas fa-copy"
                                    style="color:var(--primary);"></i> Combien de copies souhaitez-vous ?</div>
                            <div class="quantity-cards-row single-card" id="qty-row-deces">
                                <div class="quantity-card has-value" id="qty-card-deces">
                                    <div class="quantity-card-header">
                                        <div class="quantity-card-title">
                                            <div
                                                style="width:36px;height:36px;border-radius:10px;background:#eef5fc;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-file-alt" style="color:var(--primary);"></i>
                                            </div>
                                            <span>Copie simple</span>
                                        </div>
                                        <div class="qty-stepper">
                                            <button type="button" class="qty-btn" onclick="updateDQty(-1)" id="dQtyMinus"
                                                disabled>-</button>
                                            <input type="number" name="qty_simple" id="qty_simple" class="qty-input"
                                                value="1" min="1" max="20" readonly>
                                            <button type="button" class="qty-btn" onclick="updateDQty(1)">+</button>
                                        </div>
                                    </div>
                                    <div class="quantity-card-meta">500 FCFA / exemplaire</div>
                                </div>
                                <div class="quantity-card" id="qty-card-deces-integral" style="display:none;">
                                    <div class="quantity-card-header">
                                        <div class="quantity-card-title">
                                            <div
                                                style="width:36px;height:36px;border-radius:10px;background:#eef5fc;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-file-contract" style="color:var(--primary);"></i>
                                            </div>
                                            <span>Copie intégrale</span>
                                        </div>
                                        <div class="qty-stepper">
                                            <button type="button" class="qty-btn" onclick="updateDIntQty(-1)"
                                                id="dIntQtyMinus" disabled>-</button>
                                            <input type="number" name="qty_integral" id="qty_integral"
                                                class="qty-input" value="1" min="1" max="20" readonly>
                                            <button type="button" class="qty-btn" onclick="updateDIntQty(1)">+</button>
                                        </div>
                                    </div>
                                    <div class="quantity-card-meta">500 FCFA / exemplaire</div>
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

                <!-- ÉTAPE 2: Identité du défunt -->
                <div class="form-step" id="step-2">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-user-ghost"></i> Identité du défunt
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="input-group-custom">
                                    <label>Nom et Prénoms du défunt :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="name" name="name"
                                            class="form-control-custom @error('name') is-invalid @enderror"
                                            placeholder="Nom complet" value="{{ old('name') }}">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    @error('name')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group-custom">
                                    <label>Commune de décès :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="commune_deces" name="commune_deces"
                                            class="form-control-custom @error('commune_deces') is-invalid @enderror"
                                            placeholder="Ville/Commune" value="{{ old('commune_deces') }}">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    @error('commune_deces')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>Numéro de Registre :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="numberR" name="numberR" class="form-control-custom"
                                            placeholder="Ex: 1234/2024">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>Date de Registre :</label>
                                    <div class="input-wrapper">
                                        <input type="date" id="dateR" name="dateR"
                                            class="form-control-custom @error('dateR') is-invalid @enderror"
                                            value="{{ old('dateR') }}">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    @error('dateR')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>Nom du Père :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="nom_prenoms_pere" name="nom_prenoms_pere"
                                            class="form-control-custom @error('nom_prenoms_pere') is-invalid @enderror"
                                            placeholder="Nom complet du père" value="{{ old('nom_prenoms_pere') }}">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    @error('nom_prenoms_pere')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>Nom de la Mère :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="nom_prenoms_mere" name="nom_prenoms_mere"
                                            class="form-control-custom @error('nom_prenoms_mere') is-invalid @enderror"
                                            placeholder="Nom complet de la mère" value="{{ old('nom_prenoms_mere') }}">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    @error('nom_prenoms_mere')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
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

                <!-- ÉTAPE 3: Documents -->
                <div class="form-step" id="step-3">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-paperclip"></i> Justificatifs obligatoires
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="input-group-custom">
                                    <label>CNI ou Acte de naissance du défunt : <span class="text-danger">*</span></label>
                                    <label class="file-upload-area" for="CNIdfnt">
                                        <i class="fas fa-id-card"></i>
                                        <div class="fw-bold">Cliquez pour sélectionner</div>
                                        <small class="text-muted" id="CNIdfnt-name">PDF, JPG ou PNG</small>
                                    </label>
                                    <input type="file" id="CNIdfnt" name="CNIdfnt"
                                        class="d-none @error('CNIdfnt') is-invalid @enderror"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="document.getElementById('CNIdfnt-name').textContent = this.files[0]?.name || 'PDF, JPG ou PNG'">
                                    @error('CNIdfnt')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="input-group-custom">
                                    <label>Certificat médical de décès : <span class="text-danger">*</span></label>
                                    <label class="file-upload-area" for="CNIdcl">
                                        <i class="fas fa-file-medical"></i>
                                        <div class="fw-bold">Cliquez pour sélectionner</div>
                                        <small class="text-muted" id="CNIdcl-name">PDF, JPG ou PNG</small>
                                    </label>
                                    <input type="file" id="CNIdcl" name="CNIdcl"
                                        class="d-none @error('CNIdcl') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="document.getElementById('CNIdcl-name').textContent = this.files[0]?.name || 'PDF, JPG ou PNG'">
                                    @error('CNIdcl')
                                        <div class="error-message">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="form-section-title">
                                    <i class="fas fa-ring"></i> État matrimonial du défunt
                                </div>
                                <div class="options-grid">
                                    <label class="option-card">
                                        <input type="radio" name="married" value="non"
                                            {{ old('married', 'non') == 'non' ? 'checked' : '' }}
                                            onclick="toggleMarriageDoc(false)">
                                        <div class="option-card-content">
                                            <span class="option-title">Célibataire</span>
                                        </div>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="married" value="oui"
                                            {{ old('married') == 'oui' ? 'checked' : '' }}
                                            onclick="toggleMarriageDoc(true)">
                                        <div class="option-card-content">
                                            <span class="option-title">Marié(e)</span>
                                        </div>
                                    </label>
                                </div>
                                <div id="marriageDocContainer"
                                    style="{{ old('married') == 'oui' ? 'display: block;' : 'display: none;' }} margin-top: 15px;">
                                    <div class="input-group-custom">
                                        <label>Acte de mariage du défunt :</label>
                                        <label class="file-upload-area" for="documentMariage">
                                            <i class="fas fa-file-alt"></i>
                                            <div class="fw-bold">Cliquez pour sélectionner</div>
                                            <small class="text-muted" id="documentMariage-name">PDF, JPG ou PNG</small>
                                        </label>
                                        <input type="file" id="documentMariage" name="documentMariage"
                                            class="d-none @error('documentMariage') is-invalid @enderror"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            onchange="document.getElementById('documentMariage-name').textContent = this.files[0]?.name || 'PDF, JPG ou PNG'">
                                        @error('documentMariage')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-section-title">
                                    <i class="fas fa-hospital-alt"></i> Lieu du décès
                                </div>
                                <div class="options-grid">
                                    <label class="option-card">
                                        <input type="radio" name="DecesHorsS" value="non"
                                            {{ old('DecesHorsS', 'non') == 'non' ? 'checked' : '' }}
                                            onclick="togglePoliceDoc(false)">
                                        <div class="option-card-content">
                                            <span class="option-title">En milieu médical</span>
                                        </div>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="DecesHorsS" value="oui"
                                            {{ old('DecesHorsS') == 'oui' ? 'checked' : '' }}
                                            onclick="togglePoliceDoc(true)">
                                        <div class="option-card-content">
                                            <span class="option-title">Hors milieu médical</span>
                                        </div>
                                    </label>
                                </div>
                                <div id="policeDocContainer"
                                    style="{{ old('DecesHorsS') == 'oui' ? 'display: block;' : 'display: none;' }} margin-top: 15px;">
                                    <div class="input-group-custom">
                                        <label>Réquisition de police :</label>
                                        <label class="file-upload-area" for="RequisPolice">
                                            <i class="fas fa-file-shield"></i>
                                            <div class="fw-bold">Cliquez pour sélectionner</div>
                                            <small class="text-muted" id="RequisPolice-name">PDF, JPG ou PNG</small>
                                        </label>
                                        <input type="file" id="RequisPolice" name="RequisPolice"
                                            class="d-none @error('RequisPolice') is-invalid @enderror"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            onchange="document.getElementById('RequisPolice-name').textContent = this.files[0]?.name || 'PDF, JPG ou PNG'">
                                        @error('RequisPolice')
                                            <div class="error-message">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="stepper-footer">
                        <button type="button" class="btn-step btn-prev" onclick="prevStep(3)">
                            <i class="fas fa-arrow-left"></i> Précédent
                        </button>
                        <button type="button" class="btn-step btn-next" onclick="nextStep(3)">
                            Continuer <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <!-- ÉTAPE 4: Retrait -->
                <div class="form-step" id="step-4">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-truck"></i> Mode de retrait
                        </div>
                        <div class="delivery-card-grid">
                            <label class="delivery-option-card">
                                <input type="radio" name="choix_option" id="option1" value="Retrait sur place"
                                    checked>
                                <div class="delivery-option-content">
                                    <i class="fas fa-university"></i>
                                    <h5>Mairie</h5>
                                    <p>Gratuit</p>
                                </div>
                            </label>
                            <label class="delivery-option-card">
                                <input type="radio" name="choix_option" id="option2" value="livraison">
                                <div class="delivery-option-content">
                                    <i class="fas fa-motorcycle"></i>
                                    <h5>Livraison</h5>
                                    <p>+ 1 500 FCFA</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="stepper-footer">
                        <button type="button" class="btn-step btn-prev" onclick="prevStep(4)">
                            <i class="fas fa-arrow-left"></i> Précédent
                        </button>
                        <button type="submit" id="btnValider" class="btn-step btn-next"
                            style="background: var(--success);">
                            <i class="fas fa-check-circle"></i> Confirmer la demande
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let formSubmitted = false;

        function toggleMarriageDoc(show) {
            document.getElementById('marriageDocContainer').style.display = show ? 'block' : 'none';
        }

        function togglePoliceDoc(show) {
            document.getElementById('policeDocContainer').style.display = show ? 'block' : 'none';
        }

        function onDecesTypeChange(input) {
            const rowDeces = document.getElementById('qty-row-deces');
            const cardSimple = document.getElementById('qty-card-deces');
            const cardInteg = document.getElementById('qty-card-deces-integral');
            const sectionLbl = document.getElementById('qty-label-deces-section');

            if (input.value === 'groupee') {
                cardSimple.style.display = '';
                cardInteg.style.display = '';
                cardSimple.classList.add('has-value');
                cardInteg.classList.add('has-value');
                rowDeces.className = 'quantity-cards-row dual-card';
                if (sectionLbl) sectionLbl.innerHTML =
                    '<i class="fas fa-copy" style="color:var(--primary);"></i> Combien de copies de chaque type ?';
            } else if (input.value === 'integrale') {
                cardSimple.style.display = 'none';
                cardSimple.classList.remove('has-value');
                cardInteg.style.display = '';
                cardInteg.classList.add('has-value');
                rowDeces.className = 'quantity-cards-row single-card';
                if (sectionLbl) sectionLbl.innerHTML =
                    '<i class="fas fa-copy" style="color:var(--primary);"></i> Combien de copies souhaitez-vous ?';
            } else {
                cardSimple.style.display = '';
                cardSimple.classList.add('has-value');
                cardInteg.style.display = 'none';
                cardInteg.classList.remove('has-value');
                rowDeces.className = 'quantity-cards-row single-card';
                if (sectionLbl) sectionLbl.innerHTML =
                    '<i class="fas fa-copy" style="color:var(--primary);"></i> Combien de copies souhaitez-vous ?';
            }
        }

        function updateDQty(delta) {
            const input = document.getElementById('qty_simple');
            let val = parseInt(input.value) || 1;
            val = Math.max(1, Math.min(20, val + delta));
            input.value = val;
            document.getElementById('dQtyMinus').disabled = val <= 1;
            const card = document.getElementById('qty-card-deces');
            if (card) card.classList.toggle('has-value', val >= 1);
        }

        function updateDIntQty(delta) {
            const input = document.getElementById('qty_integral');
            let val = parseInt(input.value) || 1;
            val = Math.max(1, Math.min(20, val + delta));
            input.value = val;
            document.getElementById('dIntQtyMinus').disabled = val <= 1;
            const card = document.getElementById('qty-card-deces-integral');
            if (card) card.classList.toggle('has-value', val >= 1);
        }

        function nextStep(step) {
            if (validateStep(step)) {
                document.getElementById('step-' + step).classList.remove('active');
                document.getElementById('step-' + (step + 1)).classList.add('active');

                document.getElementById('step-' + step + '-indicator').classList.remove('active');
                document.getElementById('step-' + step + '-indicator').classList.add('completed');
                document.getElementById('step-' + (step + 1) + '-indicator').classList.add('active');

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }

        function prevStep(step) {
            document.getElementById('step-' + step).classList.remove('active');
            document.getElementById('step-' + (step - 1)).classList.add('active');

            document.getElementById('step-' + step + '-indicator').classList.remove('active');
            document.getElementById('step-' + (step - 1) + '-indicator').classList.remove('completed');
            document.getElementById('step-' + (step - 1) + '-indicator').classList.add('active');

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function validateStep(step) {
            let isValid = true;
            const container = document.getElementById('step-' + step);
            container.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            container.querySelectorAll('.error-message').forEach(el => el.remove());

            if (step === 1) {
                // Type always selected via radio
                return true;
            } else if (step === 2) {
                const required = ['name', 'commune_deces'];
                required.forEach(id => {
                    const el = document.getElementById(id);
                    if (!el.value.trim()) {
                        isValid = false;
                        displayError(el, "Ce champ est obligatoire.");
                    }
                });

                // Registre ou Parents
                const numR = document.getElementById('numberR').value.trim();
                const dateR = document.getElementById('dateR').value.trim();
                const p1 = document.getElementById('nom_prenoms_pere').value.trim();
                const p2 = document.getElementById('nom_prenoms_mere').value.trim();

                if (!((numR && dateR) || (p1 && p2))) {
                    isValid = false;
                    displayError(document.getElementById('numberR'),
                        "Fournir soit le registre (num+date), soit les parents.");
                }
            } else if (step === 3) {
                const files = ['CNIdfnt', 'CNIdcl'];
                files.forEach(id => {
                    const el = document.getElementById(id);
                    if (el.files.length === 0) {
                        isValid = false;
                        displayError(el, "Fichier obligatoire.");
                    }
                });

                if (document.querySelector('input[name="married"]:checked').value === 'oui') {
                    const el = document.getElementById('documentMariage');
                    if (el.files.length === 0) {
                        isValid = false;
                        displayError(el, "Acte de mariage obligatoire.");
                    }
                }
                if (document.querySelector('input[name="DecesHorsS"]:checked').value === 'oui') {
                    const el = document.getElementById('RequisPolice');
                    if (el.files.length === 0) {
                        isValid = false;
                        displayError(el, "Réquisition obligatoire.");
                    }
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

        document.getElementById('deathForm').addEventListener('submit', function(e) {
            if (formSubmitted) return;
            e.preventDefault();

            if (validateStep(4)) {
                // Calculer et ajouter montant_a_payer avant soumission
                const montantTimbreUnitaire = 500;
                const montantLivraison = 1500;
                const livraisonCheckbox = document.getElementById('option2');

                const typeVal = document.querySelector('input[name="type"]:checked').value;
                const qtySimple = parseInt(document.getElementById('qty_simple').value) || 0;
                const qtyIntegal = parseInt(document.getElementById('qty_integral').value) || 0;
                const totalTimbres = typeVal === 'simpleIntegrale' ? qtySimple + qtyIntegal :
                    typeVal === 'integral' ? qtyIntegal : qtySimple;

                let freeTimbres = 0;
                const freeRequestsModeActive = @json($freeRequestsModeActive ?? false);
                const freeRequestsRemaining = @json($freeRequestsRemaining ?? 0);

                if (freeRequestsModeActive && freeRequestsRemaining > 0) {
                    freeTimbres = Math.min(totalTimbres, freeRequestsRemaining);
                }

                const paidTimbres = totalTimbres - freeTimbres;
                const montantTimbreTotal = paidTimbres * montantTimbreUnitaire;
                const montantAPayer = livraisonCheckbox.checked ? montantTimbreTotal + montantLivraison :
                    montantTimbreTotal;

                // Stocker globalement pour accès dans preConfirm
                window.livraisonData = {
                    montantTimbreTotal: montantTimbreTotal,
                    montantLivraison: montantLivraison,
                    montantAPayer: montantAPayer
                };

                if (livraisonCheckbox.checked) {
                    showLivraisonPopup();
                } else {
                    // Pour retrait sur place
                    const form = this;
                    const fields = [{
                            name: 'montant_timbre',
                            value: montantTimbreTotal
                        },
                        {
                            name: 'montant_livraison',
                            value: 0
                        },
                        {
                            name: 'montant_a_payer',
                            value: montantTimbreTotal
                        }
                    ];
                    fields.forEach(f => {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = f.name;
                        input.value = f.value;
                        form.appendChild(input);
                    });
                    formSubmitted = true;
                    form.submit();
                }
            }
        });

        function showLivraisonPopup() {
            const typeValP = document.querySelector('input[name="type"]:checked').value;
            const qtyS = parseInt(document.getElementById('qty_simple').value) || 0;
            const qtyI = parseInt(document.getElementById('qty_integral').value) || 0;
            const quantite = typeValP === 'simpleIntegrale' ? qtyS + qtyI :
                typeValP === 'integral' ? qtyI : qtyS;
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

            // Mise à jour des données globales
            window.livraisonData = {
                montantTimbreTotal: montantTimbreTotal,
                montantLivraison: montantLivraison,
                montantAPayer: montantTotal
            };

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
                title: '<div class="flex items-center justify-center p-2"><i class="fas fa-truck text-primary mr-2"></i> <span style="font-size: 1.2rem; font-weight: 800; color: #1f4083;">DÉTAILS DE LIVRAISON</span></div>',
                width: '800px',
                html: `
                    <div style="display: flex; flex-wrap: wrap; gap: 20px; text-align: left; max-height: 70vh; overflow-y: auto; padding: 10px;">

                        <div style="flex: 1 1 400px;">
                            <h4 style="font-size: 0.9rem; font-weight: bold; color: #1f4083; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">📍 Vos coordonnées</h4>

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
                                <h4 style="font-size: 0.9rem; font-weight: bold; color: #1f4083; margin-bottom: 10px; border-bottom: 1px solid #cce3f6; padding-bottom: 5px;">🧾 Résumé</h4>

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
                                    <span style="color: #1f4083; font-weight: 800; font-size: 0.9rem;">TOTAL:</span>
                                    <span style="color: #1f4083; font-weight: 800; font-size: 1.1rem;">${montantTotal} FCFA</span>
                                </div>
                            </div>

                            <div>
                                <h4 style="font-size: 0.9rem; font-weight: bold; color: #1f4083; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">💳 Paiement</h4>
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
                confirmButtonColor: '#1f4083',
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
                        number: document.getElementById('swal-mtn_number') ? document.getElementById(
                            'swal-mtn_number').value : ''
                    };

                    if (!d.nom || !d.prenom || !d.contact || !d.adresse || !d.ville || !d.commune || !d
                        .quartier || !d.date || !d.heure) {
                        Swal.showValidationMessage('Veuillez remplir tous les champs obligatoires');
                        return false;
                    }
                    window.PaymentPopup = window.open('', 'PaymentPopup');
                    return d;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const d = result.value;
                    const form = document.getElementById('deathForm');
                    const fields = {
                        'nom_destinataire': d.nom,
                        'prenom_destinataire': d.prenom,
                        'email_destinataire': d.email,
                        'contact_destinataire': d.contact,
                        'adresse_livraison': d.adresse,
                        'ville': d.ville,
                        'commune_livraison': d.commune,
                        'quartier': d.quartier,
                        'date_livraison': d.date,
                        'heure_livraison': d.heure,
                        'payment_method': d.method,
                        'mtn_number': d.number || d.contact,
                        'montant_livraison': montantLivraison,
                        'montant_timbre': montantTimbreTotal
                    };
                    for (let k in fields) {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = k;
                        input.value = fields[k];
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
                            location.href = "{{ route('user.extrait.deces.index') }}";
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
