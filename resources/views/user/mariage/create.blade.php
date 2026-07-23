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

        .type-default-badge {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            white-space: nowrap;
            pointer-events: none;
        }

        @media (max-width: 576px) {
            .type-cards-grid {
                grid-template-columns: 1fr;
            }
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

        .quantity-card-title i {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
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
                <h2>{{ isset($mariage) ? 'Modification de la demande de mariage' : "Demande d'acte de mariage" }}</h2>
                <p class="text-muted">Remplissez les informations ci-dessous pour commander votre acte de mariage.</p>
            </div>

            <!-- Stepper -->
            <div class="stepper-container">
                <div class="step-item active" id="step-1-indicator">
                    <div class="step-number">1</div>
                    <div class="step-label">Type d'acte</div>
                </div>
                <div class="step-item" id="step-2-indicator">
                    <div class="step-number">2</div>
                    <div class="step-label">Informations</div>
                </div>
                <div class="step-item" id="step-3-indicator">
                    <div class="step-number">3</div>
                    <div class="step-label">Retrait & Pièces</div>
                </div>
            </div>

            <form id="demandeForm" method="POST" enctype="multipart/form-data"
                action="{{ isset($mariage) ? route('user.extrait.mariage.modifier', $mariage->id) : route('user.extrait.mariage.store') }}">
                @csrf
                @if (isset($mariage))
                    @method('PUT')
                @endif
                <input type="hidden" name="commune" value="Plateau">

                <!-- ÉTAPE 1: Type de demande -->
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
                                        Mon propre mariage
                                    </div>
                                </label>
                                <label class="pour-toggle-label">
                                    <input type="radio" name="pour" value="une_autre_personne"
                                        {{ old('pour') === 'une_autre_personne' ? 'checked' : '' }}
                                        onchange="updateFields()">
                                    <div class="pour-toggle-content">
                                        <span class="check-dot"></span>
                                        <i class="fas fa-users"></i>
                                        Un autre couple
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-section-title" style="margin-top:1.5rem;">
                            <i class="fas fa-file-signature"></i> Quel type d'acte souhaitez-vous ?
                        </div>

                        <div class="type-cards-grid">
                            <label class="type-option-card">
                                <input type="radio" name="typeDemande" value="simple"
                                    {{ old('typeDemande', isset($mariage) ? $mariage->type : 'simple') === 'simple' ? 'checked' : '' }}
                                    onchange="onMariageTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-file-alt"></i>
                                    <h6>Simple</h6>
                                    <small>Extrait d'acte</small>
                                </div>
                            </label>
                            <label class="type-option-card">
                                <input type="radio" name="typeDemande" value="integrale"
                                    {{ old('typeDemande', isset($mariage) ? $mariage->type : '') === 'integrale' ? 'checked' : '' }}
                                    onchange="onMariageTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-file-contract"></i>
                                    <h6>Intégrale</h6>
                                    <small>Copie intégrale</small>
                                </div>
                            </label>
                            <label class="type-option-card">
                                <input type="radio" name="typeDemande" value="groupee"
                                    {{ old('typeDemande', isset($mariage) ? $mariage->type : '') === 'groupee' ? 'checked' : '' }}
                                    onchange="onMariageTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-copy"></i>
                                    <h6>Simple + Intégrale</h6>
                                    <small>Les deux types</small>
                                </div>
                            </label>
                        </div>

                        <!-- Blocs dynamiques de relation et procuration -->
                        <!-- Blocs dynamiques de relation et procuration -->
                        <div class="input-group-custom" id="relation-block"
                            style="display: {{ old('pour', isset($mariage) && $mariage->pour ? $mariage->pour : '') === 'une_autre_personne' ? 'block' : 'none' }}; margin-top: 1.5rem;">
                            <label>Quel est votre lien avec les époux ? <span class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <select id="relation" name="relation" class="form-control-custom"
                                    onchange="onRelationChange()">
                                    <option value="">-- Choisir le lien de parenté --</option>
                                    <option value="enfant"
                                        {{ old('relation', isset($mariage) ? $mariage->relation : '') === 'enfant' ? 'selected' : '' }}>
                                        Je suis leur enfant</option>
                                    <option value="parent"
                                        {{ old('relation', isset($mariage) ? $mariage->relation : '') === 'parent' ? 'selected' : '' }}>
                                        Je suis leur parent</option>
                                    <option value="connaissance"
                                        {{ old('relation', isset($mariage) ? $mariage->relation : '') === 'connaissance' ? 'selected' : '' }}>
                                        Autre / Mandataire</option>
                                </select>
                                <i class="fas fa-link"></i>
                            </div>
                        </div>

                        <div class="input-group-custom" id="document-autorisation-block"
                            style="display: {{ old('relation', isset($mariage) ? $mariage->relation : '') === 'connaissance' ? 'block' : 'none' }}; margin-top: 1.5rem;">
                            <label>Document d'autorisation / Procuration <span class="text-danger">*</span></label>
                            <label class="file-upload-area">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <h6 id="autorisation-file-name" class="text-navy-bold mb-1">
                                    {{ isset($mariage) && $mariage->document_autorisation ? 'Procuration déjà téléversée (cliquez pour remplacer)' : "Téléverser le justificatif d'autorisation" }}
                                </h6>
                                <p class="x-small text-grey mb-0">PDF, JPG ou PNG (Max 2Mo)</p>
                                <input type="file" id="document_autorisation" name="document_autorisation"
                                    class="d-none" onchange="updateAutorisationFileName(this)"
                                    accept=".jpg,.jpeg,.png,.pdf">
                            </label>
                        </div>

                        <!-- Quantité -->
                        <div class="qty-section">
                            <div class="qty-section-label" id="qty-label-mariage"><i class="fas fa-copy"
                                    style="color:var(--primary);"></i> Combien de copies souhaitez-vous ?</div>
                            @php
                                $isDeliveryDisabled = false;
                            @endphp
                            <div class="quantity-cards-row single-card" id="qty-row-mariage">
                                <!-- Card simple -->
                                <div class="quantity-card has-value" id="qty-card-simple"
                                    @if ($isDeliveryDisabled) style="pointer-events: none; opacity: 0.6;" @endif>
                                    <div class="quantity-card-header">
                                        <div class="quantity-card-title">
                                            <div
                                                style="width:36px;height:36px;border-radius:10px;background:#eef5fc;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-file-alt" style="color:var(--primary);"></i>
                                            </div>
                                            <span>Acte simple</span>
                                        </div>
                                        <div class="qty-stepper">
                                            @php
                                                $orig_min_simple =
                                                    isset($mariage) && $mariage->qty_simple > 0
                                                        ? (int) $mariage->qty_simple
                                                        : 1;
                                                $current_val_simple = old(
                                                    'qty_simple',
                                                    isset($mariage) ? $mariage->qty_simple : 1,
                                                );
                                                if ($current_val_simple < $orig_min_simple) {
                                                    $current_val_simple = $orig_min_simple;
                                                }
                                            @endphp
                                            <button type="button" class="qty-btn" onclick="updateMQtySimple(-1)"
                                                id="mQtySimpleMinus"
                                                {{ $current_val_simple > $orig_min_simple ? '' : 'disabled' }}>-</button>
                                            <input type="number" name="qty_simple" id="qty_simple" class="qty-input"
                                                value="{{ $current_val_simple }}" min="{{ $orig_min_simple }}"
                                                data-original-min="{{ $orig_min_simple }}" max="20" readonly>
                                            <button type="button" class="qty-btn"
                                                onclick="updateMQtySimple(1)">+</button>
                                        </div>
                                    </div>
                                    <div class="quantity-card-meta">500 FCFA / exemplaire</div>
                                </div>
                                <!-- Card intégrale (affichée seulement pour simple+intégrale) -->
                                <div class="quantity-card" id="qty-card-integral"
                                    style="display:none;{{ $isDeliveryDisabled ? ' pointer-events: none; opacity: 0.6;' : '' }}">
                                    <div class="quantity-card-header">
                                        <div class="quantity-card-title">
                                            <div
                                                style="width:36px;height:36px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;">
                                                <i class="fas fa-file-contract" style="color:#28a745;"></i>
                                            </div>
                                            <span>Copie intégrale</span>
                                        </div>
                                        <div class="qty-stepper">
                                            @php
                                                $orig_min_integral =
                                                    isset($mariage) && $mariage->qty_integral > 0
                                                        ? (int) $mariage->qty_integral
                                                        : 1;
                                                $current_val_integral = old(
                                                    'qty_integral',
                                                    isset($mariage) ? $mariage->qty_integral : 0,
                                                );
                                                // If we are in edit mode and they activate integral, we start with either the original or 1
                                                if (
                                                    $current_val_integral < $orig_min_integral &&
                                                    ((isset($mariage) && $mariage->type === 'integrale') ||
                                                        (isset($mariage) && $mariage->type === 'groupee'))
                                                ) {
                                                    $current_val_integral = $orig_min_integral;
                                                }
                                            @endphp
                                            <button type="button" class="qty-btn" onclick="updateMQtyIntegral(-1)"
                                                id="mQtyIntegralMinus"
                                                {{ $current_val_integral > $orig_min_integral ? '' : 'disabled' }}>-</button>
                                            <input type="number" name="qty_integral" id="qty_integral"
                                                class="qty-input" value="{{ $current_val_integral }}"
                                                min="{{ $orig_min_integral }}"
                                                data-original-min="{{ $orig_min_integral }}" max="20" readonly>
                                            <button type="button" class="qty-btn"
                                                onclick="updateMQtyIntegral(1)">+</button>
                                        </div>
                                    </div>
                                    <div class="quantity-card-meta">500 FCFA / exemplaire</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="stepper-footer">
                        <div></div>
                        <button type="button" class="btn-step btn-next" onclick="onMariageContinue()">
                            Continuer <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>

                <script>
                    function onMariageTypeChange(input) {
                        const val = input.value;
                        const row = document.getElementById('qty-row-mariage');
                        const cardSimple = document.getElementById('qty-card-simple');
                        const cardIntegral = document.getElementById('qty-card-integral');
                        const qtySimple = document.getElementById('qty_simple');
                        const qtyIntegral = document.getElementById('qty_integral');
                        const label = document.getElementById('qty-label-mariage');

                        if (val === 'groupee') {
                            cardSimple.style.display = '';
                            cardIntegral.style.display = '';
                            cardSimple.classList.add('has-value');
                            cardIntegral.classList.add('has-value');
                            qtySimple.disabled = false;

                            const origMinSimple = parseInt(qtySimple.getAttribute('data-original-min')) || 1;
                            qtySimple.min = origMinSimple;
                            if (parseInt(qtySimple.value) < origMinSimple) qtySimple.value = origMinSimple;

                            qtyIntegral.disabled = false;
                            const origMinIntegral = parseInt(qtyIntegral.getAttribute('data-original-min')) || 1;
                            qtyIntegral.min = origMinIntegral;
                            if (parseInt(qtyIntegral.value) < origMinIntegral) qtyIntegral.value = origMinIntegral;

                            document.getElementById('mQtySimpleMinus').disabled = (parseInt(qtySimple.value) <= origMinSimple);
                            document.getElementById('mQtyIntegralMinus').disabled = (parseInt(qtyIntegral.value) <= origMinIntegral);
                            row.classList.add('dual-card');
                            row.classList.remove('single-card');
                            if (label) label.innerHTML =
                                '<i class="fas fa-layer-group" style="color:var(--primary);"></i> Combien de copies de chaque type ?';
                        } else if (val === 'simple') {
                            cardSimple.style.display = '';
                            cardIntegral.style.display = 'none';
                            cardSimple.classList.add('has-value');
                            cardIntegral.classList.remove('has-value');
                            qtySimple.disabled = false;

                            const origMinSimple = parseInt(qtySimple.getAttribute('data-original-min')) || 1;
                            qtySimple.min = origMinSimple;
                            if (parseInt(qtySimple.value) < origMinSimple) qtySimple.value = origMinSimple;

                            qtyIntegral.disabled = true;
                            qtyIntegral.value = 0;
                            document.getElementById('mQtySimpleMinus').disabled = (parseInt(qtySimple.value) <= origMinSimple);
                            row.classList.remove('dual-card');
                            row.classList.add('single-card');
                            if (label) label.innerHTML =
                                '<i class="fas fa-copy" style="color:var(--primary);"></i> Combien de copies souhaitez-vous ?';
                        } else if (val === 'integrale') {
                            cardSimple.style.display = 'none';
                            cardIntegral.style.display = '';
                            cardSimple.classList.remove('has-value');
                            cardIntegral.classList.add('has-value');
                            qtyIntegral.disabled = false;

                            const origMinIntegral = parseInt(qtyIntegral.getAttribute('data-original-min')) || 1;
                            qtyIntegral.min = origMinIntegral;
                            if (parseInt(qtyIntegral.value) < origMinIntegral) qtyIntegral.value = origMinIntegral;

                            qtySimple.disabled = true;
                            qtySimple.value = 0;
                            document.getElementById('mQtyIntegralMinus').disabled = (parseInt(qtyIntegral.value) <= origMinIntegral);
                            row.classList.remove('dual-card');
                            row.classList.add('single-card');
                            if (label) label.innerHTML =
                                '<i class="fas fa-copy" style="color:var(--primary);"></i> Combien de copies souhaitez-vous ?';
                        }
                        if (typeof toggleRelationFields === 'function') {
                            toggleRelationFields();
                        }
                    }

                    function updateMQtySimple(delta) {
                        const input = document.getElementById('qty_simple');
                        const typeVal = document.querySelector('input[name="typeDemande"]:checked').value;
                        const origMin = parseInt(input.getAttribute('data-original-min')) || 1;
                        const minVal = (typeVal === 'simple' || typeVal === 'groupee') ? origMin : 0;
                        let val = Math.max(minVal, Math.min(20, (parseInt(input.value) || 0) + delta));
                        input.value = val;
                        document.getElementById('mQtySimpleMinus').disabled = val <= minVal;
                        document.getElementById('qty-card-simple').classList.toggle('has-value', val > 0);
                    }

                    function updateMQtyIntegral(delta) {
                        const input = document.getElementById('qty_integral');
                        const typeVal = document.querySelector('input[name="typeDemande"]:checked').value;
                        const origMin = parseInt(input.getAttribute('data-original-min')) || 1;
                        const minVal = (typeVal === 'integrale' || typeVal === 'groupee') ? origMin : 0;
                        let val = Math.max(minVal, Math.min(20, (parseInt(input.value) || 0) + delta));
                        input.value = val;
                        document.getElementById('mQtyIntegralMinus').disabled = val <= minVal;
                        document.getElementById('qty-card-integral').classList.toggle('has-value', val > 0);
                    }

                    function onMariageContinue() {
                        if (typeof nextStep === 'function') nextStep(1);
                    }
                </script>

                <!-- ÉTAPE 2: Informations -->
                <div class="form-step" id="step-2">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-book"></i> Détails du mariage
                        </div>
                        <div class="form-section-content">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="input-group-custom">
                                        <label>Commune de mariage :</label>
                                        <div class="input-wrapper">
                                            <input type="text" id="commune_mariage" name="commune_mariage"
                                                class="form-control-custom" placeholder="Ville ou commune"
                                                value="{{ old('commune_mariage', isset($mariage) ? $mariage->commune_mariage : '') }}">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="input-group-custom">
                                        <label>Numéro NNI (Optionnel) :</label>
                                        <div class="input-wrapper">
                                            <input type="text" id="CMU" name="CMU"
                                                class="form-control-custom"
                                                value="{{ old('CMU', isset($mariage) ? $mariage->CMU : Auth::user()->CMU) }}"
                                                placeholder="Votre NNI">
                                            <i class="fas fa-id-card"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="input-group-custom">
                                        <label>Numéro de registre : <span class="text-danger">*</span></label>
                                        <div class="input-wrapper">
                                            <input type="text" id="numero_registre" name="numero_registre"
                                                class="form-control-custom" placeholder="Numéro de registre"
                                                value="{{ old('numero_registre', isset($mariage) ? $mariage->numero_registre : '') }}"
                                                required>
                                            <i class="fas fa-hashtag"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="input-group-custom">
                                        <label>Date de registre : <span class="text-danger">*</span></label>
                                        <div class="input-wrapper">
                                            <input type="date" id="date_registre" name="date_registre"
                                                class="form-control-custom"
                                                value="{{ old('date_registre', isset($mariage) ? $mariage->date_registre : '') }}"
                                                required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bloc Conjoint (Si Copie Intégrale) -->
                        <div id="infoEpoux"
                            style="display: {{ old('typeDemande', isset($mariage) ? $mariage->type : '') === 'integrale' || old('typeDemande', isset($mariage) ? $mariage->type : '') === 'groupee' ? 'block' : 'none' }}; margin-top: 2rem;">
                            <div class="form-section-title">
                                <i class="fas fa-user-friends"></i> Informations sur le conjoint(e)
                            </div>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <div class="input-group-custom">
                                        <label>Nom du conjoint(e) :</label>
                                        <div class="input-wrapper">
                                            <input type="text" id="nomEpoux" name="nomEpoux"
                                                class="form-control-custom" placeholder="Nom"
                                                value="{{ old('nomEpoux', isset($mariage) ? $mariage->nomEpoux : '') }}">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="input-group-custom">
                                        <label>Prénom du conjoint(e) :</label>
                                        <div class="input-wrapper">
                                            <input type="text" id="prenomEpoux" name="prenomEpoux"
                                                class="form-control-custom" placeholder="Prénom"
                                                value="{{ old('prenomEpoux', isset($mariage) ? $mariage->prenomEpoux : '') }}">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="input-group-custom">
                                        <label>Date de naissance :</label>
                                        <div class="input-wrapper">
                                            <input type="date" id="dateNaissanceEpoux" name="dateNaissanceEpoux"
                                                class="form-control-custom"
                                                value="{{ old('dateNaissanceEpoux', isset($mariage) ? $mariage->dateNaissanceEpoux : '') }}">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <div class="input-group-custom">
                                        <label>Lieu de naissance :</label>
                                        <div class="input-wrapper">
                                            <input type="text" id="lieuNaissanceEpoux" name="lieuNaissanceEpoux"
                                                class="form-control-custom" placeholder="Ville"
                                                value="{{ old('lieuNaissanceEpoux', isset($mariage) ? $mariage->lieuNaissanceEpoux : '') }}">
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
                                    <label>Pièce d'identité (CNI/Pass) : <span class="text-danger">*</span></label>
                                    <label class="file-upload-area" for="pieceIdentite">
                                        <i class="fas fa-id-card"></i>
                                        <div class="fw-bold">Cliquez pour sélectionner</div>
                                        <small class="text-muted"
                                            id="pieceIdentite-name">{{ isset($mariage) && $mariage->pieceIdentite ? 'Pièce d\'identité déjà téléversée (cliquez pour remplacer)' : 'CNI ou Passeport (Max 1Mo)' }}</small>
                                    </label>
                                    <input type="file" id="pieceIdentite" name="pieceIdentite" class="d-none"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="document.getElementById('pieceIdentite-name').textContent = this.files[0]?.name || 'CNI ou Passeport (Max 1Mo)'">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="input-group-custom">
                                    <label>Ancien acte de mariage (Optionnel) :</label>
                                    <label class="file-upload-area" for="extraitMariage">
                                        <i class="fas fa-file-alt"></i>
                                        <div class="fw-bold">Cliquez pour sélectionner</div>
                                        <small class="text-muted"
                                            id="extraitMariage-name">{{ isset($mariage) && $mariage->extraitMariage ? 'Acte déjà téléversé (cliquez pour remplacer)' : 'PDF, JPG ou PNG' }}</small>
                                    </label>
                                    <input type="file" id="extraitMariage" name="extraitMariage" class="d-none"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        onchange="document.getElementById('extraitMariage-name').textContent = this.files[0]?.name || 'PDF, JPG ou PNG'">
                                </div>
                            </div>
                        </div>

                        <div class="form-section-title mt-4">
                            <i class="fas fa-truck"></i> Comment souhaitez-vous récupérer l'acte ?
                        </div>
                        <div class="delivery-card-grid">
                            <label class="delivery-option-card"
                                style="{{ isset($mariage) && $mariage->choix_option === 'livraison' ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                                <input type="radio" name="choix_option" id="option1" value="Retrait sur place"
                                    {{ old('choix_option', isset($mariage) ? $mariage->choix_option : 'Retrait sur place') === 'Retrait sur place' ? 'checked' : '' }}
                                    {{ isset($mariage) && $mariage->choix_option === 'livraison' ? 'disabled' : '' }}>
                                <div class="delivery-option-content">
                                    <i class="fas fa-university"></i>
                                    <h5>Retrait en Mairie</h5>
                                    <p>Gratuit</p>
                                </div>
                            </label>
                            <label class="delivery-option-card">
                                <input type="radio" name="choix_option" id="option2" value="livraison"
                                    {{ old('choix_option', isset($mariage) ? $mariage->choix_option : '') === 'livraison' ? 'checked' : '' }}>
                                <div class="delivery-option-content">
                                    <i class="fas fa-motorcycle"></i>
                                    <h5>Livraison Express</h5>
                                    <p>+ 1 500 FCFA</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="stepper-footer">
                        <button type="button" class="btn-step btn-prev" onclick="prevStep(3)">
                            <i class="fas fa-arrow-left"></i> Précédent
                        </button>
                        <button type="submit" id="btnValider" class="btn-step btn-next"
                            style="background: var(--success);">
                            <i class="fas fa-check-circle"></i> Valider ma demande
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
            const typeSelect = document.querySelector('input[name="typeDemande"]:checked');

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

                    if (documentBlock) documentBlock.style.display = 'none';
                    const docAuto = document.getElementById('document_autorisation');
                    if (docAuto) docAuto.value = '';
                    const autoFileName = document.getElementById('autorisation-file-name');
                    if (autoFileName) autoFileName.textContent = 'Téléverser le justificatif d\'autorisation';
                }
            }
        }

        function onRelationChange() {
            const relationSelect = document.getElementById('relation');
            const documentBlock = document.getElementById('document-autorisation-block');

            if (relationSelect && relationSelect.value === 'connaissance') {
                if (documentBlock) documentBlock.style.display = 'block';
            } else {
                if (documentBlock) documentBlock.style.display = 'none';
                const docAuto = document.getElementById('document_autorisation');
                if (docAuto) docAuto.value = '';
                const autoFileName = document.getElementById('autorisation-file-name');
                if (autoFileName) autoFileName.textContent = 'Téléverser le justificatif d\'autorisation';
            }
        }

        function updateAutorisationFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : 'Aucun document sélectionné';
            const autoFileName = document.getElementById('autorisation-file-name');
            if (autoFileName) autoFileName.textContent = fileName;
        }

        function updateFields() {
            toggleRelationFields();
            const initialTypeInput = document.querySelector('input[name="typeDemande"]:checked');
            if (initialTypeInput) {
                onMariageTypeChange(initialTypeInput);
            }
        }

        document.querySelectorAll('input[name="typeDemande"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const infoEpoux = document.getElementById('infoEpoux');
                infoEpoux.style.display = (this.value === 'integrale' || this.value ===
                    'groupee') ? 'block' : 'none';
                toggleRelationFields();
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            toggleRelationFields();
            updateFields();
        });

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
                const pourSelect = document.querySelector('input[name="pour"]:checked');
                const typeSelect = document.querySelector('input[name="typeDemande"]:checked');
                if (pourSelect && typeSelect) {
                    if (pourSelect.value === 'une_autre_personne' && (typeSelect.value === 'integrale' || typeSelect
                            .value === 'groupee')) {
                        const relation = document.getElementById('relation');
                        if (!relation.value) {
                            isValid = false;
                            displayError(relation, "Veuillez préciser votre lien de parenté.");
                        } else if (relation.value === 'connaissance') {
                            const docInput = document.getElementById('document_autorisation');
                            const hasExistingDoc =
                                {{ isset($mariage) && $mariage->document_autorisation ? 'true' : 'false' }};
                            if (docInput.files.length === 0 && !hasExistingDoc) {
                                isValid = false;
                                displayError(docInput, "Veuillez téléverser le document d'autorisation.");
                            }
                        }
                    }
                }
            } else if (step === 2) {
                const commune = document.getElementById('commune_mariage');
                if (!commune.value.trim()) {
                    isValid = false;
                    displayError(commune, "La commune de mariage est obligatoire.");
                }

                const typeVal = document.querySelector('input[name="typeDemande"]:checked').value;
                if (typeVal === 'integrale' || typeVal === 'groupee') {
                    const nom = document.getElementById('nomEpoux');
                    const prenom = document.getElementById('prenomEpoux');
                    const dateNaissance = document.getElementById('dateNaissanceEpoux');

                    if (!nom.value.trim()) {
                        isValid = false;
                        displayError(nom, "Le nom du conjoint est obligatoire.");
                    }
                    if (!prenom.value.trim()) {
                        isValid = false;
                        displayError(prenom, "Le prénom du conjoint est obligatoire.");
                    }

                    if (dateNaissance && dateNaissance.value) {
                        const birthDate = new Date(dateNaissance.value);
                        const today = new Date();
                        let age = today.getFullYear() - birthDate.getFullYear();
                        const m = today.getMonth() - birthDate.getMonth();
                        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                            age--;
                        }

                        if (age < 18) {
                            isValid = false;
                            displayError(dateNaissance, "Le conjoint doit avoir au moins 18 ans.");
                            Swal.fire({
                                title: 'Âge non requis',
                                text: "L'âge légal pour le mariage en Côte d'Ivoire est de 18 ans au minimum.",
                                icon: 'error',
                                confirmButtonColor: '#1f4083'
                            });
                        }
                    } else if (!dateNaissance.value) {
                        isValid = false;
                        displayError(dateNaissance, "La date de naissance est obligatoire.");
                    }
                }
            } else if (step === 3) {
                const piece = document.getElementById('pieceIdentite');
                const hasExistingPiece = {{ isset($mariage) && $mariage->pieceIdentite ? 'true' : 'false' }};
                if (piece.files.length === 0 && !hasExistingPiece) {
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
                // Calculer et ajouter montant_a_payer avant soumission
                const typeVal = document.querySelector('input[name="typeDemande"]:checked').value;
                const qtySimple = parseInt(document.getElementById('qty_simple').value) || 0;
                const qtyIntegral = parseInt(document.getElementById('qty_integral').value) || 0;
                const totalTimbres = typeVal === 'groupee' ? qtySimple + qtyIntegral :
                    typeVal === 'integrale' ? qtyIntegral :
                    qtySimple;
                const montantTimbreUnitaire = 500;
                const montantLivraison = 1500;
                const livraisonCheckbox = document.getElementById('option2');

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
            const type = document.querySelector('input[name="typeDemande"]:checked').value;
            const montantTimbreUnitaire = 500;

            let qtySimple = parseInt(document.getElementById('qty_simple').value) || 0;
            let qtyIntegral = parseInt(document.getElementById('qty_integral').value) || 0;
            let totalTimbres = type === 'groupee' ? qtySimple + qtyIntegral :
                type === 'integrale' ? qtyIntegral :
                qtySimple;

            // Calcul des timbres gratuits (max 2 demandes gratuites)
            let freeTimbres = 0;
            const originalFreeTimbresCount = @json(isset($mariage) ? (int) $mariage->free_timbres_count : 0);
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
            const isEditMode = @json(isset($mariage));
            const originalMontantTimbre = @json(isset($mariage) ? (float) $mariage->montant_timbre : 0);
            const originalMontantLivraison = @json(isset($mariage) ? (float) $mariage->montant_livraison : 0);
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
                                                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 8px;">
                                                                <button type="button" id="btn-pay-wave" class="payment-method-btn active-payment" style="background: #eff6ff; border: 2px solid #1e3a8a; border-radius: 8px; padding: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="selectPaymentMethod('wave')">
                                                        <img src="{{ asset('assets/assets/img/Wave.png') }}" alt="Wave" style="height: 30px; object-fit: contain;">
                                                    </button>
                                                    <button type="button" id="btn-pay-stripe" class="payment-method-btn" style="background: white; border: 1px solid #edf2f7; border-radius: 8px; padding: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="selectPaymentMethod('stripe')">
                                                        <span style="font-size: 0.78rem; font-weight: 700; color: #635bff;">STRIPE</span>
                                                    </button>
                                                    <button type="button" id="btn-pay-tresorpay" class="payment-method-btn" style="background: white; border: 1px solid #edf2f7; border-radius: 8px; padding: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="selectPaymentMethod('tresorpay')">
                                                        <img src="{{ asset('assets/assets/img/tresormoney.png') }}" alt="TrésorMoney" style="height: 30px; object-fit: contain;">
                                                    </button>
                                                                <button type="button" id="btn-pay-mtn" class="payment-method-btn opacity-50" style="background: white; border: 1px solid #edf2f7; border-radius: 8px; padding: 8px; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 5px;" disabled title="Indisponible">
                                                                    <img src="{{ asset('assets/assets/img/MTN.png') }}" alt="MTN" style="height: 30px; object-fit: contain;">
                                                                </button>
                                                            </div>
                                                            <input type="hidden" id="swal-payment_method" value="wave">
                                                            <div id="payment-phone-container" style="display: none; margin-top: 10px;">
                                                                <label id="payment-phone-label" style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Numéro MTN à débiter</label>
                                                                <input id="swal-mtn_number" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="05XXXXXXXX (10 chiffres)" value="" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
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
                        if (!/^05\d{8}$/.test(payment_number)) {
                            Swal.showValidationMessage(
                                'Le numéro MTN Money doit comporter 10 chiffres et commencer par 05.');
                            return false;
                        }
                    }

                    if (payment_method === 'tresorpay') {
                        if (!payment_number || payment_number.length !== 10) {
                            Swal.showValidationMessage(
                                'Le numéro TrésorPay est obligatoire et doit comporter exactement 10 chiffres.'
                            );
                            return false;
                        }
                    }

                    if (needsPayment) {
                        if (payment_method === 'wave' || payment_method === 'stripe') {
                            window.PaymentPopup = window.open('', 'PaymentPopup');
                        }
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
                        mtn_number: (payment_method === 'mtn' || payment_method === 'tresorpay') ?
                            payment_number : '',
                        wave_number: ''
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = result.value;
                    const form = document.getElementById('demandeForm');

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
                        },
                        {
                            name: 'wave_number',
                            value: formData.wave_number
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

                    if (needsPayment && formData.payment_method === 'mtn') {
                        Swal.fire({
                            title: 'Paiement MTN Money',
                            html: `<div class="text-center">
                                    <div class="mtn-spinner" style="margin: 20px auto; width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #fcb711; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                    <p style="font-weight: 600; color: #1f4083;">Requête push envoyée au ${formData.mtn_number}</p>
                                    <p style="font-size: 0.9rem; color: #555;">Veuillez valider le paiement sur votre téléphone en saisissant votre code secret.<br><br>
                                    <span style="font-size: 0.8rem; color: #777;">En attente de validation... (Ne fermez pas cette page)</span></p>
                                </div>`,
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                if (!document.getElementById('mtn-spin-style')) {
                                    const style = document.createElement('style');
                                    style.id = 'mtn-spin-style';
                                    style.innerHTML =
                                        `@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }`;
                                    document.head.appendChild(style);
                                }
                            }
                        });

                        formSubmitted = true;
                        const formPayload = new FormData(form);
                        fetch(form.action, {
                                method: 'POST',
                                body: formPayload,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(err => {
                                        throw err;
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success && data.reference && data.mtn_ref) {
                                    startMtnPaymentPolling(data.reference, data.mtn_ref, 'mariage');
                                } else {
                                    throw new Error(data.message ||
                                        'Erreur lors de l\'initialisation du paiement.');
                                }
                            })
                            .catch(error => {
                                console.error('Erreur MTN:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: error.message ||
                                        'Une erreur est survenue lors de l\'initialisation du paiement MTN. Veuillez réessayer.',
                                    confirmButtonColor: '#1f4083'
                                });
                            });
                        return;
                    }

                    if (needsPayment) {
                        if (window.PaymentPopup) {
                            form.target = 'PaymentPopup';
                        } else {
                            form.target = '_blank';
                        }

                        if (formData.payment_method === 'wave' || formData.payment_method === 'stripe') {
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
                                if (window.paymentSuccess) {
                                    clearInterval(timer);
                                    Swal.close();
                                    window.location.href = window.paymentSuccessUrl ||
                                        "{{ route('user.extrait.mariage.index') }}";
                                    return;
                                }

                                try {
                                    var result = JSON.parse(localStorage.getItem('plateauPaymentResult') ||
                                        '{}');
                                    var age = Date.now() - (result.timestamp || 0);
                                    if (result.status === 'success' && age < 120000) {
                                        clearInterval(timer);
                                        Swal.close();
                                        localStorage.removeItem('plateauPaymentResult');
                                        window.location.href = result.listUrl ||
                                            "{{ route('user.extrait.mariage.index') }}";
                                        return;
                                    }
                                } catch (e) {}

                                if (window.PaymentPopup && window.PaymentPopup.closed) {
                                    clearInterval(timer);
                                    Swal.close();
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
                                window.location.href = "{{ route('user.extrait.mariage.index') }}";
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
            const typeVal = document.querySelector('input[name="typeDemande"]:checked').value;
            const qtySimple = parseInt(document.getElementById('qty_simple').value) || 0;
            const qtyIntegral = parseInt(document.getElementById('qty_integral').value) || 0;
            const quantite = typeVal === 'groupee' ? qtySimple + qtyIntegral :
                typeVal === 'integrale' ? qtyIntegral :
                qtySimple;
            const montantTimbreUnitaire = 500;
            const montantLivraison = 1500;

            let freeTimbres = 0;
            const originalFreeTimbresCount = @json(isset($mariage) ? (int) $mariage->free_timbres_count : 0);
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
            const isEditMode = @json(isset($mariage));
            const originalMontantTimbre = @json(isset($mariage) ? (float) $mariage->montant_timbre : 0);
            const originalMontantLivraison = @json(isset($mariage) ? (float) $mariage->montant_livraison : 0);
            const ancienMontantPaye = isEditMode ? (originalMontantTimbre + originalMontantLivraison) : 0;
            const resteAPayer = Math.max(0, montantTotal - ancienMontantPaye);
            const needsPayment = resteAPayer > 0;
            const finalTotalAmount = resteAPayer;

            // Stocker globalement pour accès dans preConfirm
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
                                            <input type="radio" name="swal-destinataire-type" value="moi" {{ !isset($mariage) || ($mariage->nom_destinataire === Auth::user()->name && $mariage->prenom_destinataire === Auth::user()->prenom) ? 'checked' : '' }} style="cursor: pointer; width: auto; margin: 0;"> Moi-même
                                        </label>
                                        <label style="font-size: 0.85rem; font-weight: 600; color: #555; cursor: pointer; display: flex; align-items: center; gap: 5px; margin: 0;">
                                            <input type="radio" name="swal-destinataire-type" value="autre" {{ isset($mariage) && ($mariage->nom_destinataire !== Auth::user()->name || $mariage->prenom_destinataire !== Auth::user()->prenom) ? 'checked' : '' }} style="cursor: pointer; width: auto; margin: 0;"> Autre personne
                                        </label>
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    <div>
                                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Nom</label>
                                        <input id="swal-nom_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Nom" value="{{ isset($mariage) && $mariage->nom_destinataire ? $mariage->nom_destinataire : Auth::user()->name }}">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Prénom</label>
                                        <input id="swal-prenom_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Prénom" value="{{ isset($mariage) && $mariage->prenom_destinataire ? $mariage->prenom_destinataire : Auth::user()->prenom }}">
                                    </div>
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                                    <div>
                                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Email</label>
                                        <input id="swal-email_destinataire" type="email" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="mail@exemple.com" value="{{ isset($mariage) && $mariage->email_destinataire ? $mariage->email_destinataire : Auth::user()->email }}">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Téléphone</label>
                                        <input id="swal-contact_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="0123456789" value="{{ isset($mariage) && $mariage->contact_destinataire ? $mariage->contact_destinataire : Auth::user()->contact }}">
                                    </div>
                                </div>

                                <div style="margin-top: 10px;">
                                    <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Adresse précise (Commune, Quartier, Rue, etc.)</label>
                                    <input id="swal-adresse_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Ex: Cocody, Angré, Rue L12, Immeuble..." value="{{ isset($mariage) && $mariage->adresse_livraison ? $mariage->adresse_livraison : '' }}">
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                                    <div>
                                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Date</label>
                                        <input type="date" id="swal-date_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px; cursor: pointer;" {{ isset($mariage) ? '' : 'min="' . date('Y-m-d') . '"' }} onclick="this.showPicker()" value="{{ isset($mariage) && $mariage->date_livraison ? $mariage->date_livraison : '' }}">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Heure</label>
                                        <input type="time" id="swal-heure_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px; cursor: pointer;" onclick="this.showPicker()" value="{{ isset($mariage) && $mariage->heure_livraison ? \Carbon\Carbon::parse($mariage->heure_livraison)->format('H:i') : '' }}">
                                    </div>
                                </div>
                            </div>

                            <div style="flex: 1 1 250px; display: flex; flex-direction: column; gap: 15px;">
                                <div style="background: #f0f7ff; padding: 15px; border-radius: 12px; border: 1px solid #cce3f6;">
                                    <h4 style="font-size: 0.9rem; font-weight: bold; color: #1f4083; margin-bottom: 10px; border-bottom: 1px solid #cce3f6; padding-bottom: 5px;">🧾 Résumé</h4>

                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem;">
                                        <span style="color: #555;">Exemplaires:</span>
                                        <span style="font-weight: 700">${typeVal === 'groupee' ? qtySimple + ' simple + ' + qtyIntegral + ' intégrale' : quantite}</span>
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
                                                                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 8px;">
                                                                            <button type="button" id="btn-pay-wave" class="payment-method-btn active-payment" style="background: #eff6ff; border: 2px solid #1e3a8a; border-radius: 8px; padding: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="selectPaymentMethod('wave')">
                                                        <img src="{{ asset('assets/assets/img/Wave.png') }}" alt="Wave" style="height: 30px; object-fit: contain;">
                                                    </button>
                                                    <button type="button" id="btn-pay-stripe" class="payment-method-btn" style="background: white; border: 1px solid #edf2f7; border-radius: 8px; padding: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="selectPaymentMethod('stripe')">
                                                        <span style="font-size: 0.78rem; font-weight: 700; color: #635bff;">STRIPE</span>
                                                    </button>
                                                    <button type="button" id="btn-pay-tresorpay" class="payment-method-btn" style="background: white; border: 1px solid #edf2f7; border-radius: 8px; padding: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;" onclick="selectPaymentMethod('tresorpay')">
                                                        <img src="{{ asset('assets/assets/img/tresormoney.png') }}" alt="TrésorMoney" style="height: 30px; object-fit: contain;">
                                                    </button>
                                                                            <button type="button" id="btn-pay-mtn" class="payment-method-btn opacity-50" style="background: white; border: 1px solid #edf2f7; border-radius: 8px; padding: 8px; cursor: not-allowed; display: flex; align-items: center; justify-content: center; gap: 5px;" disabled title="Indisponible">
                                                                                <img src="{{ asset('assets/assets/img/MTN.png') }}" alt="MTN" style="height: 30px; object-fit: contain;">
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
                    if (d.method === 'mtn') {
                        if (!/^05\d{8}$/.test(d.number.replace(/\s+/g, ''))) {
                            Swal.showValidationMessage(
                                'Le numéro MTN Money doit comporter 10 chiffres et commencer par 05.');
                            return false;
                        }
                    }
                    if (d.method === 'tresorpay') {
                        if (!d.number || d.number.replace(/\s+/g, '').length !== 10) {
                            Swal.showValidationMessage(
                                'Le numéro TrésorPay est obligatoire et doit comporter exactement 10 chiffres.'
                            );
                            return false;
                        }
                    }
                    if (needsPayment) {
                        if (d.method === 'wave' || d.method === 'stripe') {
                            window.PaymentPopup = window.open('', 'PaymentPopup');
                        }
                    }
                    return d;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const d = result.value;
                    const form = document.getElementById('demandeForm');
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
                        'montant_timbre': needsPayment ? montantTimbreTotal : 0,
                        'montant_a_payer': needsPayment ? finalTotalAmount : 0
                    };
                    for (let k in fields) {
                        let input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = k;
                        input.value = fields[k];
                        form.appendChild(input);
                    }

                    if (needsPayment && d.method === 'mtn') {
                        Swal.fire({
                            title: 'Paiement MTN Money',
                            html: `<div class="text-center">
                                    <div class="mtn-spinner" style="margin: 20px auto; width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #fcb711; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                    <p style="font-weight: 600; color: #1f4083;">Requête push envoyée au ${fields.mtn_number}</p>
                                    <p style="font-size: 0.9rem; color: #555;">Veuillez valider le paiement sur votre téléphone en saisissant votre code secret.<br><br>
                                    <span style="font-size: 0.8rem; color: #777;">En attente de validation... (Ne fermez pas cette page)</span></p>
                                </div>`,
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                if (!document.getElementById('mtn-spin-style')) {
                                    const style = document.createElement('style');
                                    style.id = 'mtn-spin-style';
                                    style.innerHTML =
                                        `@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }`;
                                    document.head.appendChild(style);
                                }
                            }
                        });

                        formSubmitted = true;
                        const formPayload = new FormData(form);
                        fetch(form.action, {
                                method: 'POST',
                                body: formPayload,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(err => {
                                        throw err;
                                    });
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success && data.reference && data.mtn_ref) {
                                    startMtnPaymentPolling(data.reference, data.mtn_ref, 'mariage');
                                } else {
                                    throw new Error(data.message ||
                                        'Erreur lors de l\'initialisation du paiement.');
                                }
                            })
                            .catch(error => {
                                console.error('Erreur MTN:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: error.message ||
                                        'Une erreur est survenue lors de l\'initialisation du paiement MTN. Veuillez réessayer.',
                                    confirmButtonColor: '#1f4083'
                                });
                            });
                        return;
                    }

                    if (needsPayment) {
                        if (window.PaymentPopup) {
                            form.target = 'PaymentPopup';
                        } else {
                            form.target = '_blank';
                        }
                    }
                    formSubmitted = true;
                    form.submit();

                    if (needsPayment) {
                        Swal.fire({
                            title: 'Paiement en cours',
                            html: 'Suivez les instructions dans le nouvel onglet.',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        const check = setInterval(() => {
                            if (window.paymentSuccess) {
                                clearInterval(check);
                                Swal.close();
                                window.location.href = window.paymentSuccessUrl ||
                                    "{{ route('user.extrait.mariage.index') }}";
                                return;
                            }

                            try {
                                var result = JSON.parse(localStorage.getItem('plateauPaymentResult') ||
                                    '{}');
                                var age = Date.now() - (result.timestamp || 0);
                                if (result.status === 'success' && age < 120000) {
                                    clearInterval(check);
                                    Swal.close();
                                    localStorage.removeItem('plateauPaymentResult');
                                    window.location.href = result.listUrl ||
                                        "{{ route('user.extrait.mariage.index') }}";
                                    return;
                                }
                            } catch (e) {}

                            if (window.PaymentPopup && window.PaymentPopup.closed) {
                                clearInterval(check);
                                Swal.close();
                                location.href = "{{ route('user.extrait.mariage.index') }}";
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
                btn.classList.remove('active-payment');
                btn.classList.add('opacity-50');
            });
            const activeBtn = document.getElementById('btn-pay-' + method);
            activeBtn.classList.add('active-payment');
            activeBtn.classList.remove('opacity-50');

            if (method === 'wave') {
                activeBtn.style.border = '2px solid #1e3a8a';
                activeBtn.style.backgroundColor = '#eff6ff';
                document.getElementById('payment-phone-container').style.display = 'none';
            } else if (method === 'stripe') {
                activeBtn.style.border = '2px solid #635bff';
                activeBtn.style.backgroundColor = '#f5f3ff';
                document.getElementById('payment-phone-container').style.display = 'none';
            } else if (method === 'tresorpay') {
                activeBtn.style.border = '2px solid #e35205';
                activeBtn.style.backgroundColor = '#fff5f0';
                document.getElementById('payment-phone-container').style.display = 'block';
                document.getElementById('payment-phone-label').innerText = 'Numéro TrésorMoney (ex: 0767664010)';
            } else if (method === 'mtn') {
                activeBtn.style.border = '2px solid #fcb711';
                activeBtn.style.backgroundColor = '#fffbed';
                document.getElementById('payment-phone-container').style.display = 'block';
                document.getElementById('payment-phone-label').innerText = 'Numéro MTN Money';
            }
        }

        // Fonction de polling pour paiement MTN
        function startMtnPaymentPolling(reference, mtnRef, type) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ?
                document.querySelector('meta[name="csrf-token"]').getAttribute('content') :
                (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value :
                    '');

            const checkStatus = () => {
                fetch('{{ route('user.payment.mtn.check') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            reference: reference,
                            type: type,
                            mtn_ref: mtnRef
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'SUCCESSFUL') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Paiement Réussi',
                                text: 'Votre paiement a été validé avec succès.',
                                confirmButtonColor: '#1f4083',
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.href = data.redirect ||
                                    "{{ route('user.extrait.mariage.index') }}";
                            });
                        } else if (data.status === 'FAILED') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Échec du paiement',
                                text: data.message || 'Le paiement a échoué ou a été annulé.',
                                confirmButtonColor: '#1f4083',
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.href = data.redirect ||
                                    "{{ route('user.extrait.mariage.index') }}";
                            });
                        } else {
                            // Si toujours PENDING, on continue le polling
                            setTimeout(checkStatus, 4000);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur de vérification:', error);
                        setTimeout(checkStatus, 4000);
                    });
            };

            // Démarrer la vérification dans 4 secondes
            setTimeout(checkStatus, 4000);
        }
    </script>
@endsection
