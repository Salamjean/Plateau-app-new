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

        .form-control-custom:focus+i {
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

        /* Quantités intégrées dans step 1 */
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
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Carte unique : centrée */
        .quantity-cards-row.single-card {
            grid-template-columns: minmax(200px, 420px);
            justify-content: center;
        }

        .quantity-cards-row.single-card+*,
        .quantity-cards-row.single-card {
            margin-left: auto;
            margin-right: auto;
        }

        @media (max-width: 576px) {

            .quantity-cards-row,
            .quantity-cards-row.single-card {
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
            .quantity-cards-row.single-card,
            .type-cards-grid.grid-2,
            .type-cards-grid.grid-3 {
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

        /* Cards visuelles type/pour */
        .type-cards-grid {
            display: grid;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }

        .type-cards-grid.grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .type-cards-grid.grid-3 {
            grid-template-columns: repeat(3, 1fr);
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
                <h2>{{ isset($naissance) ? 'Modification de la demande de naissance' : "Demande d'acte de naissance" }}</h2>
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

            <form id="naissanceForm"
                action="{{ isset($naissance) ? route('user.naissances.modifier', $naissance->id) : route('user.extrait.store') }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($naissance))
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
                                        {{ old('pour', isset($naissance) ? $naissance->pour : 'Moi') !== 'une_autre_personne' ? 'checked' : '' }}
                                        onchange="updateFields()">
                                    <div class="pour-toggle-content">
                                        <span class="check-dot"></span>
                                        <i class="fas fa-user"></i>
                                        Moi-même
                                    </div>
                                </label>
                                <label class="pour-toggle-label">
                                    <input type="radio" name="pour" value="une_autre_personne"
                                        {{ old('pour', isset($naissance) ? $naissance->pour : '') === 'une_autre_personne' ? 'checked' : '' }}
                                        onchange="updateFields()">
                                    <div class="pour-toggle-content">
                                        <span class="check-dot"></span>
                                        <i class="fas fa-users"></i>
                                        Une autre personne
                                    </div>
                                </label>
                            </div>{{-- /pour-toggle-group --}}
                        </div>{{-- /pour-toggle-row --}}

                        <div class="form-section-title" style="margin-top:1.5rem;">
                            <i class="fas fa-file-alt"></i> Type de document
                        </div>
                        <div class="type-cards-grid grid-3">
                            <label class="type-option-card">
                                <input type="radio" name="type" value="simple"
                                    {{ old('type', isset($naissance) ? $naissance->type : 'simple') === 'simple' ? 'checked' : '' }}
                                    onchange="onTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-file-alt"></i>
                                    <h6>Acte simple</h6>
                                    <small>Extrait d'acte de naissance</small>
                                </div>
                            </label>
                            <label class="type-option-card">
                                <input type="radio" name="type" value="integrale"
                                    {{ old('type', isset($naissance) ? $naissance->type : '') === 'integrale' ? 'checked' : '' }}
                                    onchange="onTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-file-contract"></i>
                                    <h6>Acte intégral</h6>
                                    <small>Acte avec filiation complète</small>
                                </div>
                            </label>
                            <label class="type-option-card">
                                <input type="radio" name="type" value="groupee"
                                    {{ old('type', isset($naissance) ? $naissance->type : '') === 'groupee' ? 'checked' : '' }}
                                    onchange="onTypeChange(this)">
                                <div class="type-option-content">
                                    <i class="fas fa-copy"></i>
                                    <h6>Simple + Intégral</h6>
                                    <small>Les deux types d'acte</small>
                                </div>
                            </label>
                        </div>

                        <!-- Blocs dynamiques de relation et procuration -->
                        <div class="input-group-custom" id="relation-block" style="display: none; margin-top: 1.5rem;">
                            <label>Quel est votre lien avec le titulaire de l'acte ? <span
                                    class="text-danger">*</span></label>
                            <div class="input-wrapper">
                                <select id="relation" name="relation" class="form-control-custom"
                                    onchange="onRelationChange()">
                                    <option value="">-- Choisir le lien de parenté --</option>
                                    <option value="enfant">C'est mon enfant</option>
                                    <option value="parent">C'est mon parent</option>
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
                                <input type="file" id="document_autorisation" name="document_autorisation" class="d-none"
                                    onchange="updateAutorisationFileName(this)" accept=".jpg,.jpeg,.png,.pdf">
                            </label>
                        </div>

                        <!-- Section quantités (toujours visible) -->
                        <div id="qty-section" class="qty-section">
                            <div id="qty-section-label" class="qty-section-label" style="text-align:center;">
                                <i class="fas fa-copy" style="color:var(--primary);"></i> Combien de copies souhaitez-vous
                                ?
                            </div>
                            @php
                                $isDeliveryDisabled = false;
                            @endphp
                            <div class="quantity-cards-row single-card" id="qty-cards-row">
                                <div class="quantity-card has-value" id="sq-card-simple"
                                    @if ($isDeliveryDisabled) style="pointer-events: none; opacity: 0.6;" @endif>
                                    <div class="quantity-card-header">
                                        <div class="quantity-card-title">
                                            <i class="fas fa-file-alt"
                                                style="background:rgba(1,181,116,0.1); color:var(--success);"></i>
                                            Acte simple
                                        </div>
                                        <div class="qty-stepper">
                                            @php
                                                $orig_min_simple =
                                                    isset($naissance) && $naissance->qty_simple > 0
                                                        ? (int) $naissance->qty_simple
                                                        : 1;
                                                $current_val_simple = old(
                                                    'qty_simple',
                                                    isset($naissance) ? $naissance->qty_simple : 1,
                                                );
                                                if ($current_val_simple < $orig_min_simple) {
                                                    $current_val_simple = $orig_min_simple;
                                                }
                                            @endphp
                                            <button type="button" class="qty-btn"
                                                onclick="updateSQty('sq_simple',-1)">−</button>
                                            <input type="number" id="sq_simple" name="qty_simple" class="qty-input"
                                                min="{{ $orig_min_simple }}" data-original-min="{{ $orig_min_simple }}"
                                                max="20" value="{{ $current_val_simple }}" readonly>
                                            <button type="button" class="qty-btn"
                                                onclick="updateSQty('sq_simple',1)">+</button>
                                        </div>
                                    </div>
                                    <div class="quantity-card-meta">
                                        {{ \App\Http\Controllers\User\Extrait\Naissance\NaissanceGroupeController::TARIF_TIMBRE }}
                                        FCFA / copie</div>
                                </div>
                                <div class="quantity-card has-value" id="sq-card-integral"
                                    style="display:none;{{ $isDeliveryDisabled ? ' pointer-events: none; opacity: 0.6;' : '' }}">
                                    <div class="quantity-card-header">
                                        <div class="quantity-card-title">
                                            <i class="fas fa-file-contract"
                                                style="background:rgba(255,59,103,0.1); color:var(--pink);"></i>
                                            Acte intégral
                                        </div>
                                        <div class="qty-stepper">
                                            @php
                                                $orig_min_integral =
                                                    isset($naissance) && $naissance->qty_integral > 0
                                                        ? (int) $naissance->qty_integral
                                                        : 1;
                                                $current_val_integral = old(
                                                    'qty_integral',
                                                    isset($naissance) ? $naissance->qty_integral : 0,
                                                );
                                                if (
                                                    $current_val_integral < $orig_min_integral &&
                                                    ((isset($naissance) && $naissance->type === 'integrale') ||
                                                        (isset($naissance) && $naissance->type === 'groupee'))
                                                ) {
                                                    $current_val_integral = $orig_min_integral;
                                                }
                                            @endphp
                                            <button type="button" class="qty-btn"
                                                onclick="updateSQty('sq_integral',-1)">−</button>
                                            <input type="number" id="sq_integral" name="qty_integral" class="qty-input"
                                                min="{{ $orig_min_integral }}"
                                                data-original-min="{{ $orig_min_integral }}" max="20"
                                                value="{{ $current_val_integral }}" readonly disabled>
                                            <button type="button" class="qty-btn"
                                                onclick="updateSQty('sq_integral',1)">+</button>
                                        </div>
                                    </div>
                                    <div class="quantity-card-meta">
                                        {{ \App\Http\Controllers\User\Extrait\Naissance\NaissanceGroupeController::TARIF_TIMBRE }}
                                        FCFA / copie (avec filiation)</div>
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
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>Nom :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="name" name="name" class="form-control-custom"
                                            value="{{ old('name', isset($naissance) ? $naissance->name : $userName) }}"
                                            placeholder="Nom sur l'acte">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>Prénoms :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="prenom" name="prenom" class="form-control-custom"
                                            value="{{ old('prenom', isset($naissance) ? $naissance->prenom : $userPrenom) }}"
                                            placeholder="Prénoms sur l'acte">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>Date de naissance :</label>
                                    <div class="input-wrapper">
                                        <input type="date" id="date_naissance" name="date_naissance"
                                            class="form-control-custom"
                                            value="{{ old('date_naissance', isset($naissance) && $naissance->date_naissance ? \Carbon\Carbon::parse($naissance->date_naissance)->format('Y-m-d') : '') }}"
                                            onclick="this.showPicker()">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>Lieu de naissance :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="commune_naissance" name="commune_naissance"
                                            class="form-control-custom"
                                            value="{{ old('commune_naissance', isset($naissance) ? $naissance->commune_naissance : '') }}"
                                            placeholder="Commune">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ligne 2: Registre + Filiation -->
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>N° Registre :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="number" name="number" class="form-control-custom"
                                            value="{{ old('number', isset($naissance) ? $naissance->number : '') }}"
                                            placeholder="Ex: 123/2024">
                                        <i class="fas fa-hashtag"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>Date Registre :</label>
                                    <div class="input-wrapper">
                                        <input type="date" id="DateR" name="DateR" class="form-control-custom"
                                            value="{{ old('DateR', isset($naissance) && $naissance->DateR ? \Carbon\Carbon::parse($naissance->DateR)->format('Y-m-d') : '') }}"
                                            onclick="this.showPicker()">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>Nom et Prénoms du père :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="nom_prenoms_pere" name="nom_prenoms_pere"
                                            class="form-control-custom"
                                            value="{{ old('nom_prenoms_pere', isset($naissance) ? $naissance->nom_prenoms_pere : '') }}"
                                            placeholder="Nom complet du père">
                                        <i class="fas fa-male"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="input-group-custom">
                                    <label>Nom et Prénoms de la mère :</label>
                                    <div class="input-wrapper">
                                        <input type="text" id="nom_prenoms_mere" name="nom_prenoms_mere"
                                            class="form-control-custom"
                                            value="{{ old('nom_prenoms_mere', isset($naissance) ? $naissance->nom_prenoms_mere : '') }}"
                                            placeholder="Nom complet de la mère">
                                        <i class="fas fa-female"></i>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="quantite" name="quantite"
                                value="{{ old('quantite', isset($naissance) ? $naissance->quantite : 1) }}">
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
                                <h6 id="file-name" class="text-navy-bold mb-1">
                                    {{ isset($naissance) && $naissance->CNI ? 'Pièce d\'identité déjà téléversée (cliquez pour remplacer)' : 'Téléverser votre pièce' }}
                                </h6>
                                <p class="x-small text-grey mb-0">PDF, JPG ou PNG (Max 1Mo)</p>
                                <input type="file" id="CNI" name="CNI" class="d-none"
                                    onchange="updateFileName(this)" accept=".jpg,.jpeg,.png,.pdf">
                            </label>
                        </div>
                    </div>

                    <!-- Section: Mode de retrait -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-truck"></i> Mode de retrait
                        </div>
                        <div class="delivery-card-grid">
                            <label class="delivery-option-card"
                                style="{{ isset($naissance) && $naissance->choix_option === 'livraison' ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                                <input type="radio" id="option1" name="choix_option" value="Retrait sur place"
                                    {{ old('choix_option', isset($naissance) ? $naissance->choix_option : 'Retrait sur place') === 'Retrait sur place' ? 'checked' : '' }}
                                    {{ isset($naissance) && $naissance->choix_option === 'livraison' ? 'disabled' : '' }}>
                                <div class="delivery-option-content">
                                    <i class="fas fa-university"></i>
                                    <h5>Retrait en Mairie</h5>
                                    <p>Gratuit - Guichet</p>
                                </div>
                            </label>
                            <label class="delivery-option-card">
                                <input type="radio" id="option2" name="choix_option" value="livraison"
                                    {{ old('choix_option', isset($naissance) ? $naissance->choix_option : '') === 'livraison' ? 'checked' : '' }}>
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
                    relationSelect.value = '';

                    documentBlock.style.display = 'none';
                    document.getElementById('document_autorisation').value = '';
                    document.getElementById('autorisation-file-name').textContent =
                        'Téléverser le justificatif d\'autorisation';
                }
            }
        }

        function onRelationChange() {
            const relationSelect = document.getElementById('relation');
            const documentBlock = document.getElementById('document-autorisation-block');

            if (relationSelect.value === 'connaissance') {
                documentBlock.style.display = 'block';
            } else {
                documentBlock.style.display = 'none';
                document.getElementById('document_autorisation').value = '';
                document.getElementById('autorisation-file-name').textContent =
                    'Téléverser le justificatif d\'autorisation';
            }
        }

        function updateAutorisationFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : 'Aucun document sélectionné';
            document.getElementById('autorisation-file-name').textContent = fileName;
        }

        function updateFields() {
            const pourSelect = document.querySelector('input[name="pour"]:checked');
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
            toggleRelationFields();
        }

        function updateFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : 'Aucun fichier sélectionné';
            document.getElementById('file-name').textContent = fileName;
        }

        const ROUTE_SIMPLE_STORE = "{{ route('user.extrait.store') }}";
        const ROUTE_GROUPEE_STORE = "{{ route('user.extrait.groupee.store') }}";

        function onTypeChange(input) {
            const type = input.value;
            const isGroupee = type === 'groupee';
            const isSimple = type === 'simple';
            const isIntegral = type === 'integrale';

            const cardSimple = document.getElementById('sq-card-simple');
            const cardIntegral = document.getElementById('sq-card-integral');
            const sqSimple = document.getElementById('sq_simple');
            const sqIntegral = document.getElementById('sq_integral');
            const label = document.getElementById('qty-section-label');
            const row = document.getElementById('qty-cards-row');

            if (isGroupee) {
                cardSimple.style.display = '';
                cardIntegral.style.display = '';
                sqSimple.disabled = false;
                const origMinSimple = parseInt(sqSimple.getAttribute('data-original-min')) || 1;
                sqSimple.min = origMinSimple;
                if (parseInt(sqSimple.value) < origMinSimple) sqSimple.value = origMinSimple;

                sqIntegral.disabled = false;
                const origMinIntegral = parseInt(sqIntegral.getAttribute('data-original-min')) || 1;
                sqIntegral.min = origMinIntegral;
                if (parseInt(sqIntegral.value) < origMinIntegral) sqIntegral.value = origMinIntegral;

                if (label) {
                    label.style.textAlign = '';
                    label.innerHTML =
                        '<i class="fas fa-layer-group" style="color:var(--primary);"></i> Combien de copies de chaque type ?';
                }
                if (row) row.classList.remove('single-card');
            } else if (isSimple) {
                cardSimple.style.display = '';
                cardIntegral.style.display = 'none';
                sqSimple.disabled = false;
                const origMinSimple = parseInt(sqSimple.getAttribute('data-original-min')) || 1;
                sqSimple.min = origMinSimple;
                if (parseInt(sqSimple.value) < origMinSimple) sqSimple.value = origMinSimple;

                sqIntegral.disabled = true;
                sqIntegral.value = 0;
                if (label) {
                    label.style.textAlign = 'center';
                    label.innerHTML =
                        '<i class="fas fa-copy" style="color:var(--primary);"></i> Combien de copies souhaitez-vous ?';
                }
                if (row) row.classList.add('single-card');
            } else if (isIntegral) {
                cardSimple.style.display = 'none';
                cardIntegral.style.display = '';
                sqIntegral.disabled = false;
                const origMinIntegral = parseInt(sqIntegral.getAttribute('data-original-min')) || 1;
                sqIntegral.min = origMinIntegral;
                if (parseInt(sqIntegral.value) < origMinIntegral) sqIntegral.value = origMinIntegral;

                sqSimple.disabled = true;
                sqSimple.value = 0;
                if (label) {
                    label.style.textAlign = 'center';
                    label.innerHTML =
                        '<i class="fas fa-copy" style="color:var(--primary);"></i> Combien de copies souhaitez-vous ?';
                }
                if (row) row.classList.add('single-card');
            }

            syncQuantite();
            if (!{{ isset($naissance) ? 'true' : 'false' }}) {
                document.getElementById('naissanceForm').action = ROUTE_SIMPLE_STORE;
            }
            toggleRelationFields();
        }

        function syncQuantite() {
            const type = document.querySelector('input[name="type"]:checked').value;
            const qEl = document.getElementById('quantite');
            if (!qEl) return;
            if (type === 'simple') {
                qEl.value = parseInt(document.getElementById('sq_simple').value) || 1;
            } else if (type === 'integrale') {
                qEl.value = parseInt(document.getElementById('sq_integral').value) || 1;
            } else if (type === 'groupee') {
                const s = parseInt(document.getElementById('sq_simple').value) || 0;
                const i = parseInt(document.getElementById('sq_integral').value) || 0;
                qEl.value = s + i;
            }
        }

        function updateSQty(field, delta) {
            const input = document.getElementById(field);
            const min = parseInt(input.min) || 0;
            input.value = Math.max(min, Math.min(20, parseInt(input.value || 0) + delta));
            document.getElementById('sq-card-simple').classList.toggle('has-value',
                parseInt(document.getElementById('sq_simple').value) > 0);
            document.getElementById('sq-card-integral').classList.toggle('has-value',
                parseInt(document.getElementById('sq_integral').value) > 0);
            syncQuantite();
        }

        // Stepper Navigation
        function nextStep(step) {
            if (step === 1) {
                const type = document.querySelector('input[name="type"]:checked').value;
                const qS = parseInt(document.getElementById('sq_simple').value || 0);
                const qI = parseInt(document.getElementById('sq_integral').value || 0);
                if (type === 'groupee' && (qS < 1 || qI < 1)) {
                    alert('Pour le type "Simple + Intégral", veuillez choisir au moins 1 copie de chaque type.');
                    return;
                }
                if (type === 'simple' && qS < 1) {
                    alert('Veuillez choisir au moins 1 copie.');
                    return;
                }
                if (type === 'integrale' && qI < 1) {
                    alert('Veuillez choisir au moins 1 copie.');
                    return;
                }
            }

            if (validateStep(step)) {
                document.getElementById('step-' + step).classList.remove('active');
                document.getElementById('step-' + (step + 1)).classList.add('active');

                document.getElementById('step-' + step + '-indicator').classList.add('completed');
                document.getElementById('step-' + step + '-indicator').classList.remove('active');
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
            document.getElementById('step-' + (step - 1) + '-indicator').classList.add('active');
            document.getElementById('step-' + (step - 1) + '-indicator').classList.remove('completed');

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function validateStep(step) {
            const form = document.getElementById('naissanceForm');
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('.form-control-custom.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            let isValid = true;
            if (step === 1) {
                const pourSelect = document.querySelector('input[name="pour"]:checked').value;
                const typeSelect = document.querySelector('input[name="type"]:checked').value;

                if (pourSelect === 'une_autre_personne' && (typeSelect === 'integrale' || typeSelect === 'groupee')) {
                    const relation = document.getElementById('relation');
                    if (!relation.value) {
                        isValid = false;
                        displayClientError(relation, "Veuillez préciser votre lien de parenté.");
                    } else if (relation.value === 'connaissance') {
                        const docInput = document.getElementById('document_autorisation');
                        const hasExistingDoc =
                            {{ isset($naissance) && $naissance->document_autorisation ? 'true' : 'false' }};
                        if (docInput.files.length === 0 && !hasExistingDoc) {
                            isValid = false;
                            displayClientError(docInput.closest('.input-group-custom'),
                                "Veuillez téléverser le document d'autorisation.");
                        }
                    }
                }
            } else if (step === 2) {
                const name = document.getElementById('name');
                const prenom = document.getElementById('prenom');
                const commune = document.getElementById('commune_naissance');
                const number = document.getElementById('number');
                const dateR = document.getElementById('DateR');
                const pere = document.getElementById('nom_prenoms_pere');
                const mere = document.getElementById('nom_prenoms_mere');

                if (!name.value.trim()) {
                    isValid = false;
                    displayClientError(name, "Le nom est obligatoire.");
                }
                if (!prenom.value.trim()) {
                    isValid = false;
                    displayClientError(prenom, "Le prénom est obligatoire.");
                }
                if (!commune.value.trim()) {
                    isValid = false;
                    displayClientError(commune, "La commune de naissance est obligatoire.");
                }

                // Logic: Registry OR Parents, but if Simple + Intégral (groupee), parents are strictly required
                const typeVal = document.querySelector('input[name="type"]:checked').value;
                if (typeVal === 'groupee') {
                    if (!pere.value.trim()) {
                        isValid = false;
                        displayClientError(pere,
                            "Pour une copie intégrale (Simple + Intégral), les informations du père sont obligatoires.");
                    }
                    if (!mere.value.trim()) {
                        isValid = false;
                        displayClientError(mere,
                            "Pour une copie intégrale (Simple + Intégral), les informations de la mère sont obligatoires."
                        );
                    }
                    if (!number.value.trim() || !dateR.value.trim()) {
                        isValid = false;
                        displayClientError(number, "Le numéro et la date de registre sont obligatoires.");
                    }
                } else {
                    const hasRegistry = number.value.trim() && dateR.value.trim();
                    const hasParents = pere.value.trim() && mere.value.trim();

                    if (!hasRegistry && !hasParents) {
                        isValid = false;
                        displayClientError(number,
                            "Veuillez fournir soit le registre (N° et Date), soit les deux parents.");
                    }
                }
            } else if (step === 3) {
                const cni = document.getElementById('CNI');
                const hasExistingCNI = {{ isset($naissance) && $naissance->CNI ? 'true' : 'false' }};
                if (cni.files.length === 0 && !hasExistingCNI) {
                    isValid = false;
                    displayClientError(cni.closest('.input-group-custom'), "Veuillez téléverser votre pièce d'identité.");
                }
            }

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Champs manquants',
                    text: 'Veuillez remplir les informations obligatoires.',
                    confirmButtonColor: '#1f4083'
                });
            }
            return isValid;
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateFields();
            // Initialise l'affichage des cartes quantité selon le type sélectionné par défaut
            const typeEl = document.querySelector('input[name="type"]:checked');
            if (typeEl) onTypeChange(typeEl);

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

            // Retirer les messages d'erreur existants et les styles d'erreur en tout premier
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('.form-control-custom.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            form.querySelectorAll('.form-control-custom').forEach(el => el.style.animation = 'none');

            // Champs à valider (basés sur vos règles de validation Laravel)
            const fieldsToValidate = [{
                    id: 'type',
                    message: 'Le type d\'extrait est obligatoire.'
                },
                {
                    id: 'name',
                    message: 'Le nom est obligatoire.'
                },
                {
                    id: 'prenom',
                    message: 'Le prénom est obligatoire.'
                },
                {
                    id: 'commune_naissance',
                    message: 'La commune de naissance est obligatoire.'
                },
                {
                    id: 'quantite',
                    message: 'La quantité est obligatoire.'
                },
                {
                    id: 'CNI',
                    message: 'Une pièce d\'identité (CNI/Passeport) est obligatoire.'
                }
            ];

            // Validation conditionnelle : Registre OU Parents
            const number = document.getElementById('number') ? document.getElementById('number').value.trim() : '';
            const dateR = document.getElementById('DateR') ? document.getElementById('DateR').value.trim() : '';
            const nomPrenomsPere = document.getElementById('nom_prenoms_pere') ? document.getElementById('nom_prenoms_pere')
                .value.trim() : '';
            const nomPrenomsMere = document.getElementById('nom_prenoms_mere') ? document.getElementById('nom_prenoms_mere')
                .value.trim() : '';

            const typeEl = document.querySelector('input[name="type"]:checked');
            const typeVal = typeEl ? typeEl.value : 'simple';
            if (typeVal === 'groupee') {
                if (!nomPrenomsPere) {
                    isValid = false;
                    displayClientError(document.getElementById('nom_prenoms_pere'),
                        "Pour une copie intégrale (Simple + Intégral), les informations du père sont obligatoires.");
                }
                if (!nomPrenomsMere) {
                    isValid = false;
                    displayClientError(document.getElementById('nom_prenoms_mere'),
                        "Pour une copie intégrale (Simple + Intégral), les informations de la mère sont obligatoires.");
                }
                if (!number || !dateR) {
                    isValid = false;
                    displayClientError(document.getElementById('number'),
                        "Le numéro et la date de registre sont obligatoires.");
                }
            } else {
                const hasRegistryInfo = number && dateR;
                const hasParentInfo = nomPrenomsPere && nomPrenomsMere;

                if (!hasRegistryInfo && !hasParentInfo) {
                    isValid = false;
                    displayClientError(document.getElementById('number'),
                        "Veuillez fournir soit les infos de registre, soit les infos complètes des parents.");
                    if (document.getElementById('nom_prenoms_pere')) displayClientError(document.getElementById(
                            'nom_prenoms_pere'),
                        "Veuillez fournir soit les infos de registre, soit les infos complètes des parents.");
                }
            }


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
                        const hasExistingCNI = {{ isset($naissance) && $naissance->CNI ? 'true' : 'false' }};
                        if (inputElement.files.length === 0) {
                            if (!hasExistingCNI) {
                                isValid = false;
                                displayClientError(inputElement, fieldInfo.message);
                            }
                        } else {
                            // Optionnel: Ajouter ici une validation pour le type de fichier et la taille
                            const file = inputElement.files[0];
                            const allowedTypes = ['image/png', 'image/jpeg', 'application/pdf'];
                            const maxSize = 1000 * 1024; // 1 Mo

                            if (!allowedTypes.includes(file.type)) {
                                isValid = false;
                                displayClientError(inputElement,
                                    'Le format du fichier doit être PNG, JPG, JPEG ou PDF.');
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

            // Validation de la relation et procuration (si requis)
            const pourSelect = document.querySelector('input[name="pour"]:checked');
            const pourSelectVal = pourSelect ? pourSelect.value : 'Moi';
            if (pourSelectVal === 'une_autre_personne' && (typeVal === 'integrale' || typeVal === 'groupee')) {
                const relation = document.getElementById('relation');
                if (!relation.value) {
                    isValid = false;
                    displayClientError(relation, "Veuillez préciser votre lien de parenté.");
                } else if (relation.value === 'connaissance') {
                    const docInput = document.getElementById('document_autorisation');
                    const hasExistingDoc = {{ isset($naissance) && $naissance->document_autorisation ? 'true' : 'false' }};
                    if (docInput.files.length === 0 && !hasExistingDoc) {
                        isValid = false;
                        displayClientError(docInput.closest('.input-group-custom'),
                            "Veuillez téléverser le document d'autorisation.");
                    } else if (docInput.files.length > 0) {
                        const file = docInput.files[0];
                        const allowedTypes = ['image/png', 'image/jpeg', 'application/pdf'];
                        const maxSize = 2000 * 1024; // 2 Mo
                        if (!allowedTypes.includes(file.type)) {
                            isValid = false;
                            displayClientError(docInput.closest('.input-group-custom'),
                                'Le format du document doit être PNG, JPG, JPEG ou PDF.');
                        } else if (file.size > maxSize) {
                            isValid = false;
                            displayClientError(docInput.closest('.input-group-custom'),
                                'Le document ne doit pas dépasser 2Mo.');
                        }
                    }
                }
            }

            return isValid;
        }

        // Fonction pour afficher les erreurs côté client
        function displayClientError(inputElement, message) {
            inputElement.classList.add('is-invalid');
            const errorSpan = document.createElement('span');
            errorSpan.classList.add('error-message');
            errorSpan.textContent = message;

            const wrapper = inputElement.closest('.input-group-custom') || inputElement.closest('.form-section') ||
                inputElement.parentNode;
            wrapper.appendChild(errorSpan);
        }


        document.getElementById('naissanceForm').addEventListener('submit', function(event) {
            if (formSubmitted) {
                event.preventDefault();
                return;
            }

            event.preventDefault(); // On gère toujours manuellement

            if (validateFormClient()) {
                const livraisonCheckbox = document.getElementById('option2');

                // Calculer et ajouter montant_a_payer avant soumission
                const type = document.querySelector('input[name="type"]:checked').value;
                const montantTimbreUnitaire = 500;
                const montantLivraison = 1500;

                // Récupérer les quantités selon le type
                let totalTimbres = 0;
                if (type === 'groupee') {
                    const qtySimple = parseInt(document.getElementById('sq_simple').value) || 0;
                    const qtyIntegral = parseInt(document.getElementById('sq_integral').value) || 0;
                    totalTimbres = qtySimple + qtyIntegral;
                } else if (type === 'simple') {
                    totalTimbres = parseInt(document.getElementById('sq_simple').value) || 0;
                } else if (type === 'integrale') {
                    totalTimbres = parseInt(document.getElementById('sq_integral').value) || 0;
                }

                // Calcul des timbres gratuits (max 2 demandes gratuites)
                let freeTimbres = 0;
                const freeRequestsModeActive = @json($freeRequestsModeActive ?? false);
                const freeRequestsRemaining = @json($freeRequestsRemaining ?? 0);

                if (freeRequestsModeActive && freeRequestsRemaining > 0) {
                    freeTimbres = Math.min(totalTimbres, freeRequestsRemaining);
                }

                const paidTimbres = totalTimbres - freeTimbres;
                const montantTimbreTotal = paidTimbres * montantTimbreUnitaire;

                // Montant à payer: timbres + livraison si livraison est choisie
                const montantAPayer = livraisonCheckbox.checked ? montantTimbreTotal + montantLivraison :
                    montantTimbreTotal;

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
                    // Pour retrait sur place gratuit, ajouter aussi choix_option, montant_timbre et montant_livraison
                    let choixOptionInput = document.querySelector('input[name="choix_option"]');
                    if (!choixOptionInput) {
                        choixOptionInput = document.createElement('input');
                        choixOptionInput.type = 'hidden';
                        choixOptionInput.name = 'choix_option';
                        this.appendChild(choixOptionInput);
                    }
                    choixOptionInput.value = 'Retrait sur place';

                    let montantTimbreInput = document.querySelector('input[name="montant_timbre"]');
                    if (!montantTimbreInput) {
                        montantTimbreInput = document.createElement('input');
                        montantTimbreInput.type = 'hidden';
                        montantTimbreInput.name = 'montant_timbre';
                        this.appendChild(montantTimbreInput);
                    }
                    montantTimbreInput.value = 0;

                    let montantLivraisonInput = document.querySelector('input[name="montant_livraison"]');
                    if (!montantLivraisonInput) {
                        montantLivraisonInput = document.createElement('input');
                        montantLivraisonInput.type = 'hidden';
                        montantLivraisonInput.name = 'montant_livraison';
                        this.appendChild(montantLivraisonInput);
                    }
                    montantLivraisonInput.value = 0;

                    formSubmitted = true;
                    this.submit();
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur de Validation',
                    text: 'Veuillez corriger les erreurs dans le formulaire avant de continuer.',
                    confirmButtonColor: '#1f4083'
                });
            }
        });

        function showRetraitPaymentPopup() {
            // Récupérer les quantités selon le type
            const type = document.querySelector('input[name="type"]:checked').value;
            const montantTimbreUnitaire = 500;

            let qtySimple = 0;
            let qtyIntegral = 0;
            let totalTimbres = 0;

            if (type === 'groupee') {
                qtySimple = parseInt(document.getElementById('sq_simple').value) || 0;
                qtyIntegral = parseInt(document.getElementById('sq_integral').value) || 0;
                totalTimbres = qtySimple + qtyIntegral;
            } else if (type === 'simple') {
                qtySimple = parseInt(document.getElementById('sq_simple').value) || 0;
                totalTimbres = qtySimple;
            } else if (type === 'integrale') {
                qtyIntegral = parseInt(document.getElementById('sq_integral').value) || 0;
                totalTimbres = qtyIntegral;
            }

            // Calcul des timbres gratuits (max 2 demandes gratuites)
            let freeTimbres = 0;
            const originalFreeTimbresCount = @json(isset($naissance) ? (int) $naissance->free_timbres_count : 0);
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
            const isEditMode = @json(isset($naissance));
            const originalMontantTimbre = @json(isset($naissance) ? (float) $naissance->montant_timbre : 0);
            const originalMontantLivraison = @json(isset($naissance) ? (float) $naissance->montant_livraison : 0);
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
                    const form = document.getElementById('naissanceForm');

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
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = field.name;
                        input.value = field.value;
                        form.appendChild(input);
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
                                    startMtnPaymentPolling(data.reference, data.mtn_ref, 'naissance');
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
                                html: 'VSuite de paiement dans le <b>nouvel onglet</b> qui vient de s\'ouvrir.<br><br><span style="color:#555;font-size:0.9rem;">La page s\'actualisera automatiquement dès que le paiement sera confirmé.</span>',
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
                                        "{{ route('user.extrait.index') }}";
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
                                            "{{ route('user.extrait.index') }}";
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
                                window.location.href = "{{ route('user.extrait.index') }}";
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
            // Récupérer les quantités selon le type
            const type = document.querySelector('input[name="type"]:checked').value;
            const montantTimbreUnitaire = 500;
            const montantLivraison = 1500;

            let qtySimple = 0;
            let qtyIntegral = 0;
            let totalTimbres = 0;

            if (type === 'groupee') {
                qtySimple = parseInt(document.getElementById('sq_simple').value) || 0;
                qtyIntegral = parseInt(document.getElementById('sq_integral').value) || 0;
                totalTimbres = qtySimple + qtyIntegral;
            } else if (type === 'simple') {
                qtySimple = parseInt(document.getElementById('sq_simple').value) || 0;
                totalTimbres = qtySimple;
            } else if (type === 'integrale') {
                qtyIntegral = parseInt(document.getElementById('sq_integral').value) || 0;
                totalTimbres = qtyIntegral;
            }

            // Calcul des timbres gratuits (max 2 demandes gratuites)
            let freeTimbres = 0;
            const originalFreeTimbresCount = @json(isset($naissance) ? (int) $naissance->free_timbres_count : 0);
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
            const montantTotal = montantTimbreTotal + montantLivraison;

            // Déterminer s'il faut payer
            const isEditMode = @json(isset($naissance));
            const originalMontantTimbre = @json(isset($naissance) ? (float) $naissance->montant_timbre : 0);
            const originalMontantLivraison = @json(isset($naissance) ? (float) $naissance->montant_livraison : 0);
            const ancienMontantPaye = isEditMode ? (originalMontantTimbre + originalMontantLivraison) : 0;
            const resteAPayer = Math.max(0, montantTotal - ancienMontantPaye);
            const needsPayment = resteAPayer > 0;
            const finalTotalAmount = resteAPayer;

            // Stocker globalement pour accès dans preConfirm
            window.livraisonData = {
                montantTimbreTotal: montantTimbreTotal,
                montantLivraison: montantLivraison,
                totalTimbres: totalTimbres,
                needsPayment: needsPayment
            };

            // Élément d'affichage pour les timbres gratuits
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
                title: '<div class="flex items-center justify-center p-2"><i class="fas fa-truck text-primary mr-2"></i> <span style="font-size: 1.2rem; font-weight: 800; color: #1f4083;">DÉTAILS DE LIVRAISON</span></div>',
                width: '800px',
                html: `
            <div style="display: flex; flex-wrap: wrap; gap: 20px; text-align: left; max-height: 70vh; overflow-y: auto; padding: 10px;">

                <!-- Formulaire (Partie gauche) -->
                <div style="flex: 1 1 400px;">
                    <h4 style="font-size: 0.9rem; font-weight: bold; color: #1f4083; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">📍Informations de livraisons</h4>

                    <div style="margin-bottom: 15px; display: flex; align-items: center; gap: 15px; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <span style="font-size: 0.8rem; font-weight: 700; color: #555; text-transform: uppercase;">Réceptionnaire :</span>
                        <div style="display: flex; gap: 15px;">
                            <label style="font-size: 0.85rem; font-weight: 600; color: #1f4083; cursor: pointer; display: flex; align-items: center; gap: 5px; margin: 0;">
                                <input type="radio" name="swal-destinataire-type" value="moi" {{ !isset($naissance) || ($naissance->nom_destinataire === Auth::user()->name && $naissance->prenom_destinataire === Auth::user()->prenom) ? 'checked' : '' }} style="cursor: pointer; width: auto; margin: 0;"> Moi-même
                            </label>
                            <label style="font-size: 0.85rem; font-weight: 600; color: #555; cursor: pointer; display: flex; align-items: center; gap: 5px; margin: 0;">
                                <input type="radio" name="swal-destinataire-type" value="autre" {{ isset($naissance) && ($naissance->nom_destinataire !== Auth::user()->name || $naissance->prenom_destinataire !== Auth::user()->prenom) ? 'checked' : '' }} style="cursor: pointer; width: auto; margin: 0;"> Autre personne
                            </label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Nom</label>
                            <input id="swal-nom_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Nom" value="{{ isset($naissance) && $naissance->nom_destinataire ? $naissance->nom_destinataire : Auth::user()->name }}">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Prénom</label>
                            <input id="swal-prenom_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Prénom" value="{{ isset($naissance) && $naissance->prenom_destinataire ? $naissance->prenom_destinataire : Auth::user()->prenom }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Email</label>
                            <input id="swal-email_destinataire" type="email" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="mail@exemple.com" value="{{ isset($naissance) && $naissance->email_destinataire ? $naissance->email_destinataire : Auth::user()->email }}">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Téléphone</label>
                            <input id="swal-contact_destinataire" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="0123456789" value="{{ isset($naissance) && $naissance->contact_destinataire ? $naissance->contact_destinataire : Auth::user()->contact }}">
                        </div>
                    </div>

                    <div style="margin-top: 10px;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Adresse précise (Commune, Quartier, Rue, etc.)</label>
                        <input id="swal-adresse_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px;" placeholder="Ex: Cocody, Angré, Rue L12, Immeuble..." value="{{ isset($naissance) && $naissance->adresse_livraison ? $naissance->adresse_livraison : '' }}">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Date</label>
                            <input type="date" id="swal-date_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px; cursor: pointer;" {{ isset($naissance) ? '' : 'min="' . date('Y-m-d') . '"' }} onclick="this.showPicker()" value="{{ isset($naissance) && $naissance->date_livraison ? $naissance->date_livraison : '' }}">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #555; margin-bottom: 3px; text-transform: uppercase;">Heure</label>
                            <input type="time" id="swal-heure_livraison" class="swal2-input" style="width: 100%; margin: 0; padding: 6px 10px; height: 35px; font-size: 0.85rem; border-radius: 6px; cursor: pointer;" onclick="this.showPicker()" value="{{ isset($naissance) && $naissance->heure_livraison ? \Carbon\Carbon::parse($naissance->heure_livraison)->format('H:i') : '' }}">
                        </div>
                    </div>
                </div>

                <!-- Résumé et Paiement (Partie droite) -->
                <div style="flex: 1 1 250px; display: flex; flex-direction: column; gap: 15px;">
                    <div style="background: #f0f7ff; padding: 15px; border-radius: 12px; border: 1px solid #cce3f6;">
                        <h4 style="font-size: 0.9rem; font-weight: bold; color: #1f4083; margin-bottom: 10px; border-bottom: 1px solid #cce3f6; padding-bottom: 5px;">🧾 Résumé</h4>

                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem;">
                            <span style="color: #555;">Exemplaires:</span>
                            <span style="font-weight: 700">${exemplaireText}</span>
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
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: needsPayment ? (isEditMode ? 'Enregistrer & Payer' : 'Payer') :
                    'Enregistrer & Soumettre',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#1f4083',
                focusConfirm: false,
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
                    const nom_destinataire = document.getElementById('swal-nom_destinataire').value;
                    const prenom_destinataire = document.getElementById('swal-prenom_destinataire').value;
                    const email_destinataire = document.getElementById('swal-email_destinataire').value;
                    const contact_destinataire = document.getElementById('swal-contact_destinataire').value;
                    const adresse_livraison = document.getElementById('swal-adresse_livraison').value;
                    const ville = "";
                    const commune_livraison = "";
                    const quartier = "";
                    const date_livraison = document.getElementById('swal-date_livraison').value;
                    const heure_livraison = document.getElementById('swal-heure_livraison').value;

                    if (!nom_destinataire || !prenom_destinataire || !contact_destinataire || !
                        adresse_livraison) {
                        Swal.showValidationMessage(
                            "Veuillez remplir tous les champs obligatoires."
                        );
                        return false;
                    }

                    if (!{{ isset($naissance) ? 'true' : 'false' }} && date_livraison) {
                        const selectedDate = new Date(date_livraison);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0);
                        if (selectedDate < today) {
                            Swal.showValidationMessage("La date de livraison ne peut pas être dans le passé.");
                            return false;
                        }
                    }
                    // Validation d'email simple (si renseigné)
                    if (email_destinataire && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email_destinataire)) {
                        Swal.showValidationMessage("Veuillez entrer une adresse email valide.");
                        return false;
                    }
                    // Validation de numéro de téléphone (8 à 15 chiffres)
                    const cleanContact = contact_destinataire.replace(/\s+/g, '');
                    if (!/^\d{8,15}$/.test(cleanContact)) {
                        Swal.showValidationMessage(
                            "Veuillez entrer un numéro de téléphone de contact valide (8 à 15 chiffres).");
                        return false;
                    }

                    const payment_number = document.getElementById('swal-mtn_number') ? document.getElementById(
                        'swal-mtn_number').value.replace(/\s+/g, '') : cleanContact;
                    const payment_method = document.getElementById('swal-payment_method').value;

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
                        quantite: window.livraisonData.totalTimbres,
                        montant_timbre_unitaire: 500,
                        montant_timbre: needsPayment ? window.livraisonData.montantTimbreTotal : 0,
                        montant_livraison: needsPayment ? window.livraisonData.montantLivraison : 0,
                        payment_method: payment_method,
                        mtn_number: (payment_method === 'mtn' || payment_method === 'tresorpay') ?
                            payment_number : '',
                        wave_number: ''
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = result.value;

                    // Ajouter les données de livraison au formulaire
                    const form = document.getElementById('naissanceForm');

                    // Créer des champs cachés pour les données de livraison
                    const hiddenFields = [{
                            name: 'nom_destinataire',
                            value: formData.nom_destinataire
                        },
                        {
                            name: 'prenom_destinataire',
                            value: formData.prenom_destinataire
                        },
                        {
                            name: 'email_destinataire',
                            value: formData.email_destinataire
                        },
                        {
                            name: 'contact_destinataire',
                            value: formData.contact_destinataire
                        },
                        {
                            name: 'adresse_livraison',
                            value: formData.adresse_livraison
                        },
                        {
                            name: 'ville',
                            value: formData.ville
                        },
                        {
                            name: 'commune_livraison',
                            value: formData.commune_livraison
                        },
                        {
                            name: 'quartier',
                            value: formData.quartier
                        },
                        {
                            name: 'date_livraison',
                            value: formData.date_livraison
                        },
                        {
                            name: 'heure_livraison',
                            value: formData.heure_livraison
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
                            value: formData.montant_livraison
                        },
                        {
                            name: 'montant_a_payer',
                            value: formData.montant_timbre + formData.montant_livraison
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
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = field.name;
                        input.value = field.value;
                        form.appendChild(input);
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
                                    startMtnPaymentPolling(data.reference, data.mtn_ref, 'naissance');
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
                                html: 'Veuillez finaliser le paiement dans le <b>nouvel onglet</b> qui vient de s\'ouvrir.<br><br><span style="color:#555;font-size:0.9rem;">La page s\'actualisera automatiquement dès que le paiement sera confirmé.</span>',
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
                                        "{{ route('user.extrait.index') }}";
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
                                            "{{ route('user.extrait.index') }}";
                                        return;
                                    }
                                } catch (e) {}

                                if (window.PaymentPopup && window.PaymentPopup.closed) {
                                    clearInterval(timer);
                                    Swal.close();
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
                                confirmButtonColor: '#1f4083',
                                allowOutsideClick: false
                            }).then(() => {
                                window.location.href = "{{ route('user.extrait.index') }}";
                            });
                        }
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
            if (method !== 'wave' && method !== 'stripe' && method !== 'mtn' && method !== 'tresorpay') return;

            // Réinitialiser tous les boutons
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                if (!btn.classList.contains('opacity-50')) {
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
                activeBtn.style.border = '2px solid #fcb711'; // MTN Yellow
                activeBtn.style.backgroundColor = '#fffbed';
                document.getElementById('payment-phone-container').style.display = 'block';
                document.getElementById('payment-phone-label').innerText = 'Numéro MTN Money';
            }

            // Mettre à jour la valeur du champ caché
            document.getElementById('swal-payment_method').value = method;
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
                                    "{{ route('user.extrait.index') }}";
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
                                    "{{ route('user.extrait.index') }}";
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
