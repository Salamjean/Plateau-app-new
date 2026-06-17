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

        /* Pills compactes pour "Cette demande est pour" */
        .pour-toggle-group {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
            margin-bottom: 0.5rem;
        }

        .pour-toggle-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .pour-toggle-row-label {
            font-weight: 700;
            color: var(--text-navy);
            font-size: 0.92rem;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pour-toggle-row-label i {
            width: 28px;
            height: 28px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 12px;
        }

        .pour-toggle-label {
            position: relative;
            cursor: pointer;
        }

        .pour-toggle-label input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .pour-toggle-content {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 1.1rem;
            background: #fff;
            border: 2px solid #e8edf5;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #718096;
            transition: all 0.2s ease;
            white-space: nowrap;
            user-select: none;
        }

        .pour-toggle-content i {
            font-size: 0.8rem;
            color: #a0aec0;
            transition: 0.2s;
        }

        .pour-toggle-content .check-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid #cbd5e0;
            flex-shrink: 0;
            transition: 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pour-toggle-label input:checked+.pour-toggle-content {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary);
        }

        .pour-toggle-label input:checked+.pour-toggle-content i {
            color: var(--primary);
        }

        .pour-toggle-label input:checked+.pour-toggle-content .check-dot {
            background: var(--primary);
            border-color: var(--primary);
        }
    </style>

    <div class="form-page-container">
        <div class="form-glass-card">
            <div class="form-header-box">
                <h2>{{ isset($deces) ? 'Modification de la demande de décès' : "Demande d'acte de décès" }}</h2>
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
                action="{{ isset($deces) ? route('user.extrait.deces.modifier', $deces->id) : route('user.extrait.deces.store') }}">
                @csrf
                @if (isset($deces))
                    @method('PUT')
                @endif
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
                        <div class="pour-toggle-row">
                            <span class="pour-toggle-row-label">
                                <i class="fas fa-user-circle"></i> Cette demande est pour
                            </span>
                            <div class="pour-toggle-group" style="margin-bottom:0;">
                                <label class="pour-toggle-label">
                                    <input type="radio" name="pour" value="Moi"
                                        {{ old('pour', 'Moi') !== 'une_autre_personne' ? 'checked' : '' }}
                                        onchange="updateFields()">
                                    <div class="pour-toggle-content">
                                        <span class="check-dot"></span>
                                        <i class="fas fa-user"></i>
                                        Ma famille directe
                                    </div>
                                </label>
                                <label class="pour-toggle-label">
                                    <input type="radio" name="pour" value="une_autre_personne"
                                        {{ old('pour') === 'une_autre_personne' ? 'checked' : '' }}
                                        onchange="updateFields()">
                                    <div class="pour-toggle-content">
                                        <span class="check-dot"></span>
                                        <i class="fas fa-users"></i>
                                        Une autre personne
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-section-title" style="margin-top:1.5rem;">
                            <i class="fas fa-file-alt"></i> Quel type d'acte souhaitez-vous ?
                        </div>

                        <div class="type-cards-grid">
                            <label class="type-option-card">
                                <input type="radio" name="type" value="simple"
                                    {{ old('type', isset($deces) ? $deces->type : 'simple') === 'simple' ? 'checked' : '' }}
                                    onchange="onDecesTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-file-alt"></i>
                                    <h6>Copie simple</h6>
                                    <small>Extrait d'acte de décès</small>
                                </div>
                            </label>
                            <label class="type-option-card">
                                <input type="radio" name="type" value="integrale"
                                    {{ old('type', isset($deces) ? $deces->type : '') === 'integrale' ? 'checked' : '' }}
                                    onchange="onDecesTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-file-contract"></i>
                                    <h6>Copie intégrale</h6>
                                    <small>Acte intégral de décès</small>
                                </div>
                            </label>
                            <label class="type-option-card">
                                <input type="radio" name="type" value="groupee"
                                    {{ old('type', isset($deces) ? $deces->type : '') === 'groupee' ? 'checked' : '' }}
                                    onchange="onDecesTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-copy"></i>
                                    <h6>Simple + Intégrale</h6>
                                    <small>Les deux types d'acte</small>
                                </div>
                            </label>
                        </div>

                        <!-- Blocs dynamiques de relation et procuration -->
                        <div class="input-group-custom" id="relation-block" style="display: none; margin-top: 1.5rem;">
                            <label>Quel est votre lien avec le défunt ? <span class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <select id="relation" name="relation" class="form-control-custom"
                                    onchange="onRelationChange()">
                                    <option value="">-- Choisir le lien de parenté --</option>
                                    <option value="enfant">C'est mon enfant</option>
                                    <option value="parent">C'est mon parent / ascendant</option>
                                    <option value="connaissance">C'est une connaissance / Autre</option>
                                </select>
                                <i class="fas fa-link"></i>
                            </div>
                        </div>

                        <div class="input-group-custom" id="document-autorisation-block"
                            style="display: none; margin-top: 1.5rem;">
                            <label>Document d'autorisation / Procuration <span class="text-danger">*</span></label>
                            <label class="file-upload-area">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h6 id="autorisation-file-name" class="text-navy-bold mb-1">Téléverser le justificatif
                                    d'autorisation</h6>
                                <p class="x-small text-grey mb-0">PDF, JPG ou PNG (Max 2Mo)</p>
                                <input type="file" id="document_autorisation" name="document_autorisation"
                                    class="d-none" onchange="updateAutorisationFileName(this)"
                                    accept=".jpg,.jpeg,.png,.pdf">
                            </label>
                        </div>

                        <!-- Quantité -->
                        <div class="qty-section">
                            <div class="qty-section-label" id="qty-label-deces-section"><i class="fas fa-copy"
                                    style="color:var(--primary);"></i> Combien de copies souhaitez-vous ?</div>
                            @php
                                $isDeliveryDisabled = false;
                            @endphp
                            <div class="quantity-cards-row single-card" id="qty-row-deces">
                                <div class="quantity-card has-value" id="qty-card-deces"
                                    @if ($isDeliveryDisabled) style="pointer-events: none; opacity: 0.6;" @endif>
                                    <div class="quantity-card-header">
                                        <div class="quantity-card-title">
                                            <div
                                                style="width:36px;height:36px;border-radius:10px;background:#eef5fc;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-file-alt" style="color:var(--primary);"></i>
                                            </div>
                                            <span>Copie simple</span>
                                        </div>
                                        <div class="qty-stepper">
                                            @php
                                                $orig_min_simple =
                                                    isset($deces) && $deces->qty_simple > 0
                                                        ? (int) $deces->qty_simple
                                                        : 1;
                                                $current_val_simple = old(
                                                    'qty_simple',
                                                    isset($deces) ? $deces->qty_simple : 1,
                                                );
                                                if ($current_val_simple < $orig_min_simple) {
                                                    $current_val_simple = $orig_min_simple;
                                                }
                                            @endphp
                                            <button type="button" class="qty-btn" onclick="updateDQty(-1)"
                                                id="dQtyMinus"
                                                {{ $current_val_simple > $orig_min_simple ? '' : 'disabled' }}>-</button>
                                            <input type="number" name="qty_simple" id="qty_simple" class="qty-input"
                                                value="{{ $current_val_simple }}" min="{{ $orig_min_simple }}"
                                                data-original-min="{{ $orig_min_simple }}" max="20" readonly>
                                            <button type="button" class="qty-btn" onclick="updateDQty(1)">+</button>
                                        </div>
                                    </div>
                                    <div class="quantity-card-meta">500 FCFA / exemplaire</div>
                                </div>
                                <div class="quantity-card" id="qty-card-deces-integral"
                                    style="display:none;{{ $isDeliveryDisabled ? ' pointer-events: none; opacity: 0.6;' : '' }}">
                                    <div class="quantity-card-header">
                                        <div class="quantity-card-title">
                                            <div
                                                style="width:36px;height:36px;border-radius:10px;background:#eef5fc;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-file-contract" style="color:var(--primary);"></i>
                                            </div>
                                            <span>Copie intégrale</span>
                                        </div>
                                        <div class="qty-stepper">
                                            @php
                                                $orig_min_integral =
                                                    isset($deces) && $deces->qty_integral > 0
                                                        ? (int) $deces->qty_integral
                                                        : 1;
                                                $current_val_integral = old(
                                                    'qty_integral',
                                                    isset($deces) ? $deces->qty_integral : 1,
                                                );
                                                if ($current_val_integral < $orig_min_integral) {
                                                    $current_val_integral = $orig_min_integral;
                                                }
                                            @endphp
                                            <button type="button" class="qty-btn" onclick="updateDIntQty(-1)"
                                                id="dIntQtyMinus"
                                                {{ $current_val_integral > $orig_min_integral ? '' : 'disabled' }}>-</button>
                                            <input type="number" name="qty_integral" id="qty_integral"
                                                class="qty-input" value="{{ $current_val_integral }}"
                                                min="{{ $orig_min_integral }}"
                                                data-original-min="{{ $orig_min_integral }}" max="20" readonly>
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
                                            placeholder="Nom complet"
                                            value="{{ old('name', isset($deces) ? $deces->name : '') }}">
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
                                            placeholder="Ville/Commune"
                                            value="{{ old('commune_deces', isset($deces) ? $deces->commune_deces : '') }}">
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
                                            placeholder="Ex: 1234/2024"
                                            value="{{ old('numberR', isset($deces) ? $deces->numberR : '') }}">
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
                                            value="{{ old('dateR', isset($deces) && $deces->dateR ? \Carbon\Carbon::parse($deces->dateR)->format('Y-m-d') : '') }}">
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
                                            placeholder="Nom complet du père"
                                            value="{{ old('nom_prenoms_pere', isset($deces) ? $deces->nom_prenoms_pere : '') }}">
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
                                            placeholder="Nom complet de la mère"
                                            value="{{ old('nom_prenoms_mere', isset($deces) ? $deces->nom_prenoms_mere : '') }}">
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
                                        <small class="text-muted"
                                            id="CNIdfnt-name">{{ isset($deces) && $deces->CNIdfnt ? "Pièce d'identité déjà téléversée (cliquez pour remplacer)" : 'PDF, JPG ou PNG' }}</small>
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
                                        <small class="text-muted"
                                            id="CNIdcl-name">{{ isset($deces) && $deces->CNIdcl ? 'Certificat médical déjà téléversé (cliquez pour remplacer)' : 'PDF, JPG ou PNG' }}</small>
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
                                            {{ old('married', isset($deces) && $deces->documentMariage ? 'oui' : 'non') == 'non' ? 'checked' : '' }}
                                            onclick="toggleMarriageDoc(false)">
                                        <div class="option-card-content">
                                            <span class="option-title">Célibataire</span>
                                        </div>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="married" value="oui"
                                            {{ old('married', isset($deces) && $deces->documentMariage ? 'oui' : 'non') == 'oui' ? 'checked' : '' }}
                                            onclick="toggleMarriageDoc(true)">
                                        <div class="option-card-content">
                                            <span class="option-title">Marié(e)</span>
                                        </div>
                                    </label>
                                </div>
                                <div id="marriageDocContainer"
                                    style="{{ old('married', isset($deces) && $deces->documentMariage ? 'oui' : 'non') == 'oui' ? 'display: block;' : 'display: none;' }} margin-top: 15px;">
                                    <div class="input-group-custom">
                                        <label>Acte de mariage du défunt :</label>
                                        <label class="file-upload-area" for="documentMariage">
                                            <i class="fas fa-file-alt"></i>
                                            <div class="fw-bold">Cliquez pour sélectionner</div>
                                            <small class="text-muted"
                                                id="documentMariage-name">{{ isset($deces) && $deces->documentMariage ? 'Acte de mariage déjà téléversé (cliquez pour remplacer)' : 'PDF, JPG ou PNG' }}</small>
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
                                            {{ old('DecesHorsS', isset($deces) && $deces->RequisPolice ? 'oui' : 'non') == 'non' ? 'checked' : '' }}
                                            onclick="togglePoliceDoc(false)">
                                        <div class="option-card-content">
                                            <span class="option-title">En milieu médical</span>
                                        </div>
                                    </label>
                                    <label class="option-card">
                                        <input type="radio" name="DecesHorsS" value="oui"
                                            {{ old('DecesHorsS', isset($deces) && $deces->RequisPolice ? 'oui' : 'non') == 'oui' ? 'checked' : '' }}
                                            onclick="togglePoliceDoc(true)">
                                        <div class="option-card-content">
                                            <span class="option-title">Hors milieu médical</span>
                                        </div>
                                    </label>
                                </div>
                                <div id="policeDocContainer"
                                    style="{{ old('DecesHorsS', isset($deces) && $deces->RequisPolice ? 'oui' : 'non') == 'oui' ? 'display: block;' : 'display: none;' }} margin-top: 15px;">
                                    <div class="input-group-custom">
                                        <label>Réquisition de police :</label>
                                        <label class="file-upload-area" for="RequisPolice">
                                            <i class="fas fa-file-shield"></i>
                                            <div class="fw-bold">Cliquez pour sélectionner</div>
                                            <small class="text-muted"
                                                id="RequisPolice-name">{{ isset($deces) && $deces->RequisPolice ? 'Réquisition de police déjà téléversée (cliquez pour remplacer)' : 'PDF, JPG ou PNG' }}</small>
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
                            <label class="delivery-option-card"
                                style="{{ isset($deces) && $deces->choix_option === 'livraison' ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                                <input type="radio" name="choix_option" id="option1" value="Retrait sur place"
                                    {{ old('choix_option', isset($deces) ? $deces->choix_option : 'Retrait sur place') === 'Retrait sur place' ? 'checked' : '' }}
                                    {{ isset($deces) && $deces->choix_option === 'livraison' ? 'disabled' : '' }}>
                                <div class="delivery-option-content">
                                    <i class="fas fa-university"></i>
                                    <h5>Mairie</h5>
                                    <p>Gratuit</p>
                                </div>
                            </label>
                            <label class="delivery-option-card">
                                <input type="radio" name="choix_option" id="option2" value="livraison"
                                    {{ old('choix_option', isset($deces) ? $deces->choix_option : '') === 'livraison' ? 'checked' : '' }}>
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

        function toggleRelationFields() {
            const pourSelect = document.querySelector('input[name="pour"]:checked');
            const typeSelect = document.querySelector('input[name="type"]:checked');

            const relationBlock = document.getElementById('relation-block');
            const relationSelect = document.getElementById('relation');
            const documentBlock = document.getElementById('document-autorisation-block');

            if (pourSelect && typeSelect) {
                const isAutrePersonne = pourSelect.value === 'une_autre_personne';
                const isIntegralOrGroupee = typeSelect.value === 'integrale' || typeSelect.value === 'groupee';

                if (isAutrePersonne && isIntegralOrGroupee) {
                    relationBlock.style.display = 'block';
                } else {
                    relationBlock.style.display = 'none';
                    if (relationSelect) relationSelect.value = '';

                    if (documentBlock) {
                        documentBlock.style.display = 'none';
                        document.getElementById('document_autorisation').value = '';
                        document.getElementById('autorisation-file-name').textContent =
                            'Téléverser le justificatif d\'autorisation';
                    }
                }
            }
        }

        function onRelationChange() {
            const relationSelect = document.getElementById('relation');
            const documentBlock = document.getElementById('document-autorisation-block');

            if (relationSelect && relationSelect.value === 'connaissance') {
                documentBlock.style.display = 'block';
            } else {
                if (documentBlock) {
                    documentBlock.style.display = 'none';
                    document.getElementById('document_autorisation').value = '';
                    document.getElementById('autorisation-file-name').textContent =
                        'Téléverser le justificatif d\'autorisation';
                }
            }
        }

        function updateAutorisationFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : 'Aucun document sélectionné';
            document.getElementById('autorisation-file-name').textContent = fileName;
        }

        function updateFields() {
            toggleRelationFields();
            const initialTypeInput = document.querySelector('input[name="type"]:checked');
            if (initialTypeInput) {
                onDecesTypeChange(initialTypeInput);
            }
        }

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
            const qtySimple = document.getElementById('qty_simple');
            const qtyInteg = document.getElementById('qty_integral');

            if (input.value === 'groupee') {
                cardSimple.style.display = '';
                cardInteg.style.display = '';
                cardSimple.classList.add('has-value');
                cardInteg.classList.add('has-value');
                rowDeces.className = 'quantity-cards-row dual-card';
                if (sectionLbl) sectionLbl.innerHTML =
                    '<i class="fas fa-copy" style="color:var(--primary);"></i> Combien de copies de chaque type ?';

                if (qtySimple) {
                    qtySimple.disabled = false;
                    const origMin = parseInt(qtySimple.getAttribute('data-original-min')) || 1;
                    qtySimple.min = origMin;
                    if (parseInt(qtySimple.value) < origMin) qtySimple.value = origMin;
                }
                if (qtyInteg) {
                    qtyInteg.disabled = false;
                    const origMin = parseInt(qtyInteg.getAttribute('data-original-min')) || 1;
                    qtyInteg.min = origMin;
                    if (parseInt(qtyInteg.value) < origMin) qtyInteg.value = origMin;
                }
                if (document.getElementById('dQtyMinus') && qtySimple) {
                    const origMin = parseInt(qtySimple.getAttribute('data-original-min')) || 1;
                    document.getElementById('dQtyMinus').disabled = (parseInt(qtySimple.value) <= origMin);
                }
                if (document.getElementById('dIntQtyMinus') && qtyInteg) {
                    const origMin = parseInt(qtyInteg.getAttribute('data-original-min')) || 1;
                    document.getElementById('dIntQtyMinus').disabled = (parseInt(qtyInteg.value) <= origMin);
                }
            } else if (input.value === 'integrale') {
                cardSimple.style.display = 'none';
                cardSimple.classList.remove('has-value');
                cardInteg.style.display = '';
                cardInteg.classList.add('has-value');
                rowDeces.className = 'quantity-cards-row single-card';
                if (sectionLbl) sectionLbl.innerHTML =
                    '<i class="fas fa-copy" style="color:var(--primary);"></i> Combien de copies souhaitez-vous ?';

                if (qtySimple) {
                    qtySimple.disabled = true;
                    qtySimple.value = 0;
                }
                if (qtyInteg) {
                    qtyInteg.disabled = false;
                    const origMin = parseInt(qtyInteg.getAttribute('data-original-min')) || 1;
                    qtyInteg.min = origMin;
                    if (parseInt(qtyInteg.value) < origMin) qtyInteg.value = origMin;
                }
                if (document.getElementById('dIntQtyMinus') && qtyInteg) {
                    const origMin = parseInt(qtyInteg.getAttribute('data-original-min')) || 1;
                    document.getElementById('dIntQtyMinus').disabled = (parseInt(qtyInteg.value) <= origMin);
                }
            } else {
                cardSimple.style.display = '';
                cardSimple.classList.add('has-value');
                cardInteg.style.display = 'none';
                cardInteg.classList.remove('has-value');
                rowDeces.className = 'quantity-cards-row single-card';
                if (sectionLbl) sectionLbl.innerHTML =
                    '<i class="fas fa-copy" style="color:var(--primary);"></i> Combien de copies souhaitez-vous ?';

                if (qtyInteg) {
                    qtyInteg.disabled = true;
                    qtyInteg.value = 0;
                }
                if (qtySimple) {
                    qtySimple.disabled = false;
                    const origMin = parseInt(qtySimple.getAttribute('data-original-min')) || 1;
                    qtySimple.min = origMin;
                    if (parseInt(qtySimple.value) < origMin) qtySimple.value = origMin;
                }
                if (document.getElementById('dQtyMinus') && qtySimple) {
                    const origMin = parseInt(qtySimple.getAttribute('data-original-min')) || 1;
                    document.getElementById('dQtyMinus').disabled = (parseInt(qtySimple.value) <= origMin);
                }
            }
            toggleRelationFields();
        }

        function updateDQty(delta) {
            const input = document.getElementById('qty_simple');
            const minVal = parseInt(input.getAttribute('min')) || 1;
            let val = parseInt(input.value) || minVal;
            val = Math.max(minVal, Math.min(20, val + delta));
            input.value = val;
            document.getElementById('dQtyMinus').disabled = val <= minVal;
            const card = document.getElementById('qty-card-deces');
            if (card) card.classList.toggle('has-value', val >= 1);
        }

        function updateDIntQty(delta) {
            const input = document.getElementById('qty_integral');
            const minVal = parseInt(input.getAttribute('min')) || 1;
            let val = parseInt(input.value) || minVal;
            val = Math.max(minVal, Math.min(20, val + delta));
            input.value = val;
            document.getElementById('dIntQtyMinus').disabled = val <= minVal;
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
                    if (el && !el.value.trim()) {
                        isValid = false;
                        displayError(el, "Ce champ est obligatoire.");
                    }
                });

                // Registre ou Parents
                const numR = document.getElementById('numberR') ? document.getElementById('numberR').value.trim() : '';
                const dateR = document.getElementById('dateR') ? document.getElementById('dateR').value.trim() : '';
                const p1 = document.getElementById('nom_prenoms_pere') ? document.getElementById('nom_prenoms_pere').value
                    .trim() : '';
                const p2 = document.getElementById('nom_prenoms_mere') ? document.getElementById('nom_prenoms_mere').value
                    .trim() : '';

                if (!((numR && dateR) || (p1 && p2))) {
                    isValid = false;
                    const numREl = document.getElementById('numberR');
                    if (numREl) {
                        displayError(numREl, "Fournir soit le registre (num+date), soit les parents.");
                    }
                }
            } else if (step === 3) {
                const files = ['CNIdfnt', 'CNIdcl'];
                const hasExistingCNIdfnt = {{ isset($deces) && $deces->CNIdfnt ? 'true' : 'false' }};
                const hasExistingCNIdcl = {{ isset($deces) && $deces->CNIdcl ? 'true' : 'false' }};
                files.forEach(id => {
                    const el = document.getElementById(id);
                    if (el.files.length === 0) {
                        if (id === 'CNIdfnt' && hasExistingCNIdfnt) return;
                        if (id === 'CNIdcl' && hasExistingCNIdcl) return;
                        isValid = false;
                        displayError(el, "Fichier obligatoire.");
                    }
                });

                const marriedEl = document.querySelector('input[name="married"]:checked');
                if (marriedEl && marriedEl.value === 'oui') {
                    const el = document.getElementById('documentMariage');
                    const hasExistingMariage = {{ isset($deces) && $deces->documentMariage ? 'true' : 'false' }};
                    if (el.files.length === 0 && !hasExistingMariage) {
                        isValid = false;
                        displayError(el, "Acte de mariage obligatoire.");
                    }
                }
                const decesHorsSEl = document.querySelector('input[name="DecesHorsS"]:checked');
                if (decesHorsSEl && decesHorsSEl.value === 'oui') {
                    const el = document.getElementById('RequisPolice');
                    const hasExistingPolice = {{ isset($deces) && $deces->RequisPolice ? 'true' : 'false' }};
                    if (el.files.length === 0 && !hasExistingPolice) {
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
                const totalTimbres = typeVal === 'groupee' ? qtySimple + qtyIntegal :
                    typeVal === 'integrale' ? qtyIntegal : qtySimple;

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

                // Ajouter le champ montant_a_payer au formulaire
                let montantAPayerInput = document.querySelector('input[name="montant_a_payer"]');
                if (!montantAPayerInput) {
                    montantAPayerInput = document.createElement('input');
                    montantAPayerInput.type = 'hidden';
                    montantAPayerInput.name = 'montant_a_payer';
                    this.appendChild(montantAPayerInput);
                }
                montantAPayerInput.value = montantAPayer;

                if (livraisonCheckbox.checked) {
                    showLivraisonPopup();
                } else if (montantTimbreTotal > 0) {
                    showRetraitPaymentPopup();
                } else {
                    // Pour retrait sur place gratuit
                    const form = this;
                    const fields = [{
                            name: 'choix_option',
                            value: 'Retrait sur place'
                        },
                        {
                            name: 'montant_timbre',
                            value: 0
                        },
                        {
                            name: 'montant_livraison',
                            value: 0
                        }
                    ];
                    fields.forEach(f => {
                        let input = document.querySelector(`input[name="${f.name}"]`);
                        if (!input) {
                            input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = f.name;
                            form.appendChild(input);
                        }
                        input.value = f.value;
                    });
                    formSubmitted = true;
                    form.submit();
                }
            }
        });

        function showRetraitPaymentPopup() {
            // Récupérer les quantités selon le type
            const type = document.querySelector('input[name="type"]:checked').value;
            const montantTimbreUnitaire = 500;

            let qtySimple = parseInt(document.getElementById('qty_simple').value) || 0;
            let qtyIntegral = parseInt(document.getElementById('qty_integral').value) || 0;
            let totalTimbres = type === 'groupee' ? qtySimple + qtyIntegral :
                type === 'integrale' ? qtyIntegral : qtySimple;

            // Calcul des timbres gratuits (max 2 demandes gratuites)
            let freeTimbres = 0;
            const originalFreeTimbresCount = @json(isset($deces) ? (int) $deces->free_timbres_count : 0);
            const freeRequestsModeActive = @json($freeRequestsModeActive ?? false);
            const freeRequestsRemaining = @json($freeRequestsRemaining ?? 0);

            // Les timbres gratuits déjà accordés restent acquis pour cette demande
            freeTimbres = Math.min(totalTimbres, originalFreeTimbresCount);

            // Si la nouvelle quantité dépasse ce qui était accordé, on complète avec le quota restant si disponible
            if (totalTimbres > originalFreeTimbresCount && freeRequestsModeActive && freeRequestsRemaining > 0) {
                const extraTimbres = totalTimbres - originalFreeTimbresCount;
                freeTimbres += Math.min(extraTimbres, freeRequestsRemaining);
            }
            const freeAmount = freeTimbres * montantTimbreUnitaire;

            const paidTimbres = totalTimbres - freeTimbres;
            const montantTimbreTotal = paidTimbres * montantTimbreUnitaire;

            // Déterminer s'il faut payer
            const isEditMode = @json(isset($deces));
            const originalMontantTimbre = @json(isset($deces) ? (float) $deces->montant_timbre : 0);
            const originalMontantLivraison = @json(isset($deces) ? (float) $deces->montant_livraison : 0);
            const ancienMontantPaye = isEditMode ? (originalMontantTimbre + originalMontantLivraison) : 0;
            const resteAPayer = Math.max(0, montantTimbreTotal - ancienMontantPaye);
            const needsPayment = resteAPayer > 0;
            const finalTotalAmount = resteAPayer;

            // Stocker globalement pour accès dans preConfirm
            window.retraitData = {
                montantTimbreTotal: montantTimbreTotal,
                totalTimbres: totalTimbres,
                needsPayment: needsPayment
            };

            let freeTimbresHtml = '';
            let originalTimbreHtml = '';
            let exemplaireText = '';

            if (type === 'groupee') {
                exemplaireText = `Simple: ${qtySimple}, Intégral: ${qtyIntegral}`;
            } else if (type === 'simple') {
                exemplaireText = `Simple: ${qtySimple}`;
            } else if (type === 'integrale') {
                exemplaireText = `Intégral: ${qtyIntegral}`;
            }

            if (freeTimbres > 0) {
                originalTimbreHtml = `
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2px; font-size: 0.85rem; text-decoration: line-through; color: #a0aec0;">
                        <span>Timbres (x${totalTimbres}):</span>
                        <span>${totalTimbres * montantTimbreUnitaire} FCFA</span>
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
                title: '<div class="flex items-center justify-center p-2"><i class="fas fa-receipt text-primary mr-2"></i> <span style="font-size: 1.2rem; font-weight: 800; color: #1f4083;">RÉSUMÉ DU PAIEMENT</span></div>',
                width: '450px',
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <div style="background: #f0f7ff; padding: 15px; border-radius: 12px; border: 1px solid #cce3f6; margin-bottom: 15px;">
                            <h4 style="font-size: 0.9rem; font-weight: bold; color: #1f4083; margin-bottom: 10px; border-bottom: 1px solid #cce3f6; padding-bottom: 5px;">🧾 Détails de la demande</h4>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem;">
                                <span style="color: #555;">Option:</span>
                                <span style="font-weight: 700; color: #2b6cb0;">Retrait sur place (Mairie)</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem;">
                                <span style="color: #555;">Exemplaires:</span>
                                <span style="font-weight: 700">${exemplaireText}</span>
                            </div>
                            ${originalTimbreHtml}
                            ${freeTimbresHtml}
                            ${finalTimbreHtml}
                            <div style="display: flex; justify-content: space-between; border-top: 2px dashed #b8d4ed; padding-top: 8px; margin-top: 8px;">
                                <span style="color: #1f4083; font-weight: 800; font-size: 0.9rem;">TOTAL A PAYER:</span>
                                <span style="color: #1f4083; font-weight: 800; font-size: 1.1rem;">${finalTotalAmount} FCFA</span>
                            </div>
                        </div>

                        <div id="payment-section-container">
                            ${!needsPayment ? `
                                                <div style="background: #e6fffa; border: 1px solid #b2f5ea; padding: 15px; border-radius: 12px; color: #234e52; text-align: center; font-size: 0.85rem; font-weight: 600;">
                                                    <i class="fas fa-check-circle" style="color: #319795; margin-right: 5px; font-size: 1.2rem;"></i><br>
                                                    <span style="font-size: 0.95rem; display: block; margin-top: 5px;">Modification Gratuite</span>
                                                    La demande a déjà été réglée lors de la soumission initiale. Aucun frais supplémentaire n'est requis.
                                                </div>
                                                <input type="hidden" id="swal-payment_method" value="deja_paye">
                                                <input type="hidden" id="swal-mtn_number" value="">
                                            ` : `
                                                <h4 style="font-size: 0.9rem; font-weight: bold; color: #1f4083; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">💳 Moyen de paiement</h4>
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
                                            `}
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: needsPayment ? (isEditMode ? 'Enregistrer & Payer' : 'Payer') :
                    'Enregistrer & Soumettre',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#1f4083',
                focusConfirm: false,
                preConfirm: () => {
                    const payment_method = document.getElementById('swal-payment_method').value;
                    const cleanContact = "{{ Auth::user()->contact }}".replace(/\s+/g, '');
                    const payment_number = document.getElementById('swal-mtn_number') ? document.getElementById(
                        'swal-mtn_number').value.replace(/\s+/g, '') : cleanContact;

                    if (payment_method === 'mtn') {
                        if (!/^\d{10}$/.test(payment_number)) {
                            Swal.showValidationMessage(
                                'Veuillez entrer un numéro MTN Money valide à 10 chiffres.');
                            return false;
                        }
                    }

                    if (needsPayment) {
                        window.PaymentPopup = window.open('', 'PaymentPopup');
                    }

                    return {
                        nom_destinataire: null,
                        prenom_destinataire: null,
                        email_destinataire: null,
                        contact_destinataire: null,
                        adresse_livraison: null,
                        ville: null,
                        commune_livraison: null,
                        quartier: null,
                        date_livraison: null,
                        heure_livraison: null,
                        quantite: window.retraitData.totalTimbres,
                        montant_timbre_unitaire: 500,
                        montant_timbre: needsPayment ? window.retraitData.montantTimbreTotal : 0,
                        montant_livraison: 0,
                        payment_method: payment_method,
                        mtn_number: payment_number
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = result.value;
                    const form = document.getElementById('deathForm');

                    // Créer des champs cachés
                    const hiddenFields = [{
                            name: 'choix_option',
                            value: 'Retrait sur place'
                        },
                        {
                            name: 'montant_timbre_unitaire',
                            value: formData.montant_timbre_unitaire
                        },
                        {
                            name: 'montant_timbre',
                            value: formData.montant_timbre
                        },
                        {
                            name: 'montant_livraison',
                            value: 0
                        },
                        {
                            name: 'montant_a_payer',
                            value: formData.montant_timbre
                        },
                        {
                            name: 'payment_method',
                            value: formData.payment_method
                        },
                        {
                            name: 'mtn_number',
                            value: formData.mtn_number
                        }
                    ];

                    hiddenFields.forEach(field => {
                        let input = document.querySelector(`input[name="${field.name}"]`);
                        if (!input) {
                            input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = field.name;
                            form.appendChild(input);
                        }
                        input.value = field.value;
                    });

                    window.paymentSuccess = false;

                    if (needsPayment) {
                        if (window.PaymentPopup) {
                            form.target = 'PaymentPopup';
                        } else {
                            form.target = '_blank';
                        }

                        if (formData.payment_method === 'wave') {
                            Swal.fire({
                                title: 'Paiement en cours',
                                html: 'Suite de paiement dans le <b>nouvel onglet</b> qui vient de s\'ouvrir.<br><br><span style="color:#555;font-size:0.9rem;">La page s\'actualisera automatiquement dès que le paiement sera confirmé.</span>',
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
                                    if (window.paymentSuccess) {
                                        window.location.href = window.paymentSuccessUrl ||
                                            "{{ route('user.extrait.deces.index') }}";
                                        return;
                                    }
                                    try {
                                        var result = JSON.parse(localStorage.getItem(
                                            'plateauPaymentResult') || '{}');
                                        var age = Date.now() - (result.timestamp || 0);
                                        if (result.status === 'success' && age < 120000) {
                                            localStorage.removeItem('plateauPaymentResult');
                                            window.location.href = result.listUrl ||
                                                "{{ route('user.extrait.deces.index') }}";
                                            return;
                                        }
                                    } catch (e) {}
                                    window.location.reload();
                                }
                            }, 1000);
                        } else {
                            Swal.fire({
                                title: 'Redirection en cours',
                                html: `Un nouvel onglet s'est ouvert pour le suivi du paiement...`,
                                icon: 'info',
                                confirmButtonText: 'Fermer',
                                confirmButtonColor: '#1f4083',
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.href = "{{ route('user.extrait.deces.index') }}";
                            });
                        }
                    } else {
                        Swal.fire({
                            title: 'Enregistrement de la modification...',
                            html: 'Veuillez patienter pendant la mise à jour de votre demande.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    }

                    formSubmitted = true;
                    form.submit();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    document.getElementById('option1').checked = true;
                }
            });
        }

        function showLivraisonPopup() {
            const typeValP = document.querySelector('input[name="type"]:checked').value;
            const qtyS = parseInt(document.getElementById('qty_simple').value) || 0;
            const qtyI = parseInt(document.getElementById('qty_integral').value) || 0;
            const quantite = typeValP === 'groupee' ? qtyS + qtyI :
                typeValP === 'integrale' ? qtyI : qtyS;
            const montantTimbreUnitaire = 500;
            const montantLivraison = 1500;

            let freeTimbres = 0;
            const originalFreeTimbresCount = @json(isset($deces) ? (int) $deces->free_timbres_count : 0);
            const freeRequestsModeActive = @json($freeRequestsModeActive ?? false);
            const freeRequestsRemaining = @json($freeRequestsRemaining ?? 0);

            // Les timbres gratuits déjà accordés restent acquis pour cette demande
            freeTimbres = Math.min(quantite, originalFreeTimbresCount);

            // Si la nouvelle quantité dépasse ce qui était accordé, on complète avec le quota restant si disponible
            if (quantite > originalFreeTimbresCount && freeRequestsModeActive && freeRequestsRemaining > 0) {
                const extraTimbres = quantite - originalFreeTimbresCount;
                freeTimbres += Math.min(extraTimbres, freeRequestsRemaining);
            }
            const freeAmount = freeTimbres * montantTimbreUnitaire;

            const paidTimbres = quantite - freeTimbres;
            const montantTimbreTotal = paidTimbres * montantTimbreUnitaire;
            const montantTotal = montantTimbreTotal + montantLivraison;

            // Déterminer s'il faut payer
            const isEditMode = @json(isset($deces));
            const originalMontantTimbre = @json(isset($deces) ? (float) $deces->montant_timbre : 0);
            const originalMontantLivraison = @json(isset($deces) ? (float) $deces->montant_livraison : 0);
            const ancienMontantPaye = isEditMode ? (originalMontantTimbre + originalMontantLivraison) : 0;
            const resteAPayer = Math.max(0, montantTotal - ancienMontantPaye);
            const needsPayment = resteAPayer > 0;
            const finalTotalAmount = resteAPayer;

            // Mise à jour des données globales
            window.livraisonData = {
                montantTimbreTotal: montantTimbreTotal,
                montantLivraison: montantLivraison,
                montantAPayer: finalTotalAmount,
                totalTimbres: quantite,
                needsPayment: needsPayment
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
                            <h4 style="font-size: 0.9rem; font-weight: bold; color: #1f4083; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">📍Informations de livraisons</h4>

                            <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 15px; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <span style="font-size: 0.8rem; font-weight: 700; color: #555; text-transform: uppercase;">Réceptionnaire :</span>
                                <div style="display: flex; gap: 15px;">
                                    <label style="font-size: 0.85rem; font-weight: 600; color: #1f4083; cursor: pointer; display: flex; align-items: center; gap: 5px; margin: 0;">
                                        <input type="radio" name="swal-destinataire-type" value="moi" {{ !isset($deces) || ($deces->nom_destinataire === Auth::user()->name && $deces->prenom_destinataire === Auth::user()->prenom) ? 'checked' : '' }} style="cursor: pointer; width: auto; margin: 0;"> Moi-même
                                    </label>
                                    <label style="font-size: 0.85rem; font-weight: 600; color: #555; cursor: pointer; display: flex; align-items: center; gap: 5px; margin: 0;">
                                        <input type="radio" name="swal-destinataire-type" value="autre" {{ isset($deces) && ($deces->nom_destinataire !== Auth::user()->name || $deces->prenom_destinataire !== Auth::user()->prenom) ? 'checked' : '' }} style="cursor: pointer; width: auto; margin: 0;"> Autre personne
                                    </label>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Nom</label>
                                    <input id="swal-nom_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Nom" value="{{ isset($deces) && $deces->nom_destinataire ? $deces->nom_destinataire : Auth::user()->name }}">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Prénom</label>
                                    <input id="swal-prenom_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Prénom" value="{{ isset($deces) && $deces->prenom_destinataire ? $deces->prenom_destinataire : Auth::user()->prenom }}">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Email</label>
                                    <input id="swal-email_destinataire" type="email" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="mail@exemple.com" value="{{ isset($deces) && $deces->email_destinataire ? $deces->email_destinataire : Auth::user()->email }}">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Téléphone</label>
                                    <input id="swal-contact_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="0123456789" value="{{ isset($deces) && $deces->contact_destinataire ? $deces->contact_destinataire : Auth::user()->contact }}">
                                </div>
                            </div>

                            <div style="margin-top: 10px;">
                                <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Adresse précise (Commune, Quartier, Rue, etc.)</label>
                                <input id="swal-adresse_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Ex: Cocody, Angré, Rue L12, Immeuble..." value="{{ isset($deces) && $deces->adresse_livraison ? $deces->adresse_livraison : '' }}">
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Date</label>
                                    <input type="date" id="swal-date_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px; cursor: pointer;" {{ isset($deces) ? '' : 'min="' . date('Y-m-d') . '"' }} onclick="this.showPicker()" value="{{ isset($deces) && $deces->date_livraison ? $deces->date_livraison : '' }}">
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Heure</label>
                                    <input type="time" id="swal-heure_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px; cursor: pointer;" onclick="this.showPicker()" value="{{ isset($deces) && $deces->heure_livraison ? \Carbon\Carbon::parse($deces->heure_livraison)->format('H:i') : '' }}">
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
                                    <span style="color: #1f4083; font-weight: 800; font-size: 1.1rem;">${finalTotalAmount} FCFA</span>
                                </div>
                            </div>

                            <div id="payment-section-container">
                                ${!needsPayment ? `
                                                            <div style="background: #e6fffa; border: 1px solid #b2f5ea; padding: 15px; border-radius: 12px; color: #234e52; text-align: center; font-size: 0.85rem; font-weight: 600;">
                                                                <i class="fas fa-check-circle" style="color: #319795; margin-right: 5px; font-size: 1.2rem;"></i><br>
                                                                <span style="font-size: 0.95rem; display: block; margin-top: 5px;">Modification Gratuite</span>
                                                                La demande a déjà été réglée lors de la soumission initiale. Aucun frais supplémentaire n'est requis.
                                                            </div>
                                                            <input type="hidden" id="swal-payment_method" value="deja_paye">
                                                            <input type="hidden" id="swal-mtn_number" value="">
                                                        ` : `
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
                                                        `}
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: needsPayment ? (isEditMode ? 'Enregistrer & Payer' : 'Payer & Valider') :
                    'Enregistrer & Soumettre',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#1f4083',
                didOpen: () => {
                    const radios = document.getElementsByName('swal-destinataire-type');
                    radios.forEach(radio => {
                        radio.addEventListener('change', (e) => {
                            const type = e.target.value;
                            const inputNom = document.getElementById('swal-nom_destinataire');
                            const inputPrenom = document.getElementById(
                                'swal-prenom_destinataire');
                            const inputEmail = document.getElementById(
                                'swal-email_destinataire');
                            const inputContact = document.getElementById(
                                'swal-contact_destinataire');

                            if (type === 'moi') {
                                inputNom.value = "{{ Auth::user()->name }}";
                                inputPrenom.value = "{{ Auth::user()->prenom }}";
                                inputEmail.value = "{{ Auth::user()->email }}";
                                inputContact.value = "{{ Auth::user()->contact }}";
                            } else {
                                inputNom.value = "";
                                inputPrenom.value = "";
                                inputEmail.value = "";
                                inputContact.value = "";
                            }
                        });
                    });

                    // Google Maps Autocomplete
                    const addressInput = document.getElementById('swal-adresse_livraison');
                    if (addressInput && typeof google !== 'undefined' && google.maps && google.maps.places) {
                        new google.maps.places.Autocomplete(addressInput, {
                            types: ['geocode', 'establishment']
                        });
                    }
                },
                preConfirm: () => {
                    const d = {
                        nom: document.getElementById('swal-nom_destinataire').value,
                        prenom: document.getElementById('swal-prenom_destinataire').value,
                        email: document.getElementById('swal-email_destinataire').value,
                        contact: document.getElementById('swal-contact_destinataire').value,
                        adresse: document.getElementById('swal-adresse_livraison').value,
                        ville: "",
                        commune: "",
                        quartier: "",
                        date: document.getElementById('swal-date_livraison').value,
                        heure: document.getElementById('swal-heure_livraison').value,
                        method: document.getElementById('swal-payment_method').value,
                        number: document.getElementById('swal-mtn_number') ? document.getElementById(
                            'swal-mtn_number').value : ''
                    };

                    if (!d.nom || !d.prenom || !d.contact || !d.adresse) {
                        Swal.showValidationMessage('Veuillez remplir tous les champs obligatoires');
                        return false;
                    }
                    if (needsPayment) {
                        window.PaymentPopup = window.open('', 'PaymentPopup');
                    }
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
                        'montant_livraison': needsPayment ? montantLivraison : 0,
                        'montant_timbre': needsPayment ? montantTimbreTotal : 0
                    };
                    for (let k in fields) {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = k;
                        input.value = fields[k];
                        form.appendChild(input);
                    }

                    if (needsPayment) {
                        if (window.PaymentPopup) {
                            form.target = 'PaymentPopup';
                        } else {
                            form.target = '_blank';
                        }
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
                        // Modification: afficher un spinner d'enregistrement simple
                        Swal.fire({
                            title: 'Enregistrement de la modification...',
                            html: 'Veuillez patienter pendant la mise à jour de votre demande.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        formSubmitted = true;
                        form.submit();
                    }
                } else if (result.dismiss === Swal.DismissReason.cancel) {
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

        updateFields();
    </script>
@endsection
