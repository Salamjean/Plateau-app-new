@extends('poste.layouts.template')

@section('content')
<style>
    /* ========== MODERN REDESIGN DESIGN SYSTEM ========== */
    :root {
        --primary-glow: rgba(31, 64, 131, 0.15);
        --primary-solid: #1f4083;
        --secondary-solid: #ea8c51;
        --secondary-glow: rgba(234, 140, 81, 0.15);
        --bg-glass: rgba(255, 255, 255, 0.85);
        --border-glass: rgba(255, 255, 255, 0.4);
        --success-green: #10b981;
        --error-red: #ef4444;
        --text-dark: #1e293b;
        --text-secondary: #64748b;
        --shadow-premium: 0 20px 40px -15px rgba(0, 0, 0, 0.05), 0 1px 3px 0 rgba(0, 0, 0, 0.03);
        --shadow-active: 0 30px 60px -15px rgba(31, 64, 131, 0.12);
        --font-poppins: 'Poppins', sans-serif;
    }

    body {
        background-color: #f6f9fc;
        font-family: var(--font-poppins);
    }

    .premium-page-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1.5rem 0.5rem;
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Page Header */
    .page-header-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        background: linear-gradient(135deg, var(--primary-solid), #2a52be);
        padding: 2rem;
        border-radius: 24px;
        color: white;
        box-shadow: 0 10px 30px rgba(31, 64, 131, 0.15);
        position: relative;
        overflow: hidden;
    }

    .page-header-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        pointer-events: none;
    }

    .page-header-banner::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -10%;
        width: 250px;
        height: 250px;
        background: var(--secondary-glow);
        border-radius: 50%;
        pointer-events: none;
        filter: blur(40px);
    }

    .header-info h1 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .header-info p {
        margin: 0.5rem 0 0 0;
        opacity: 0.85;
        font-size: 0.95rem;
    }

    /* Status Badge Pulsing */
    .scanner-status-indicator {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.15);
        padding: 10px 20px;
        border-radius: 30px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .scanner-status-indicator.focus-lost {
        background: rgba(239, 68, 68, 0.2);
        border-color: rgba(239, 68, 68, 0.4);
        color: #fecaca;
        animation: pulseRed 2s infinite;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: var(--success-green);
        box-shadow: 0 0 8px var(--success-green);
    }

    .scanner-status-indicator.focus-lost .status-dot {
        background-color: var(--error-red);
        box-shadow: 0 0 8px var(--error-red);
        animation: blink 1s infinite;
    }

    /* Dual Column Layout */
    .main-interactive-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2.5rem;
    }

    @media (max-width: 991px) {
        .main-interactive-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Premium Cards */
    .interactive-card {
        background: var(--bg-glass);
        border-radius: 24px;
        border: 1px solid var(--border-glass);
        box-shadow: var(--shadow-premium);
        padding: 2.5rem;
        backdrop-filter: blur(15px);
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }

    .interactive-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-active);
    }

    .card-title-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--primary-glow);
        color: var(--primary-solid);
        padding: 6px 14px;
        border-radius: 100px;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        margin-bottom: 1.5rem;
    }

    .card-title-badge.orange {
        background: var(--secondary-glow);
        color: var(--secondary-solid);
    }

    .interactive-card h2 {
        margin: 0 0 0.5rem 0;
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .interactive-card .card-subtitle {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-bottom: 2rem;
        line-height: 1.5;
    }

    /* ==================== LEFT COLUMN: SCANNER SIMULATOR ==================== */
    .scanner-simulation-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        padding: 2.5rem;
        background: #f8fafc;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
        min-height: 280px;
    }

    .scanner-simulation-container.focused {
        border-color: var(--primary-solid);
        background: rgba(31, 64, 131, 0.02);
        box-shadow: inset 0 0 20px rgba(31, 64, 131, 0.05);
    }

    .scanner-simulation-container.unfocused {
        border-color: var(--error-red);
        background: rgba(239, 68, 68, 0.02);
        animation: pulseBorderRed 2s infinite;
    }

    /* Laser Scanning Effect */
    .scanner-visual-box {
        position: relative;
        width: 140px;
        height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .scanner-visual-box svg {
        width: 100px;
        height: 100px;
        color: var(--text-dark);
        opacity: 0.85;
        transition: all 0.3s ease;
    }

    .scanner-simulation-container.focused .scanner-visual-box svg {
        color: var(--primary-solid);
        filter: drop-shadow(0 0 10px rgba(31, 64, 131, 0.2));
    }

    .scanner-laser-line {
        position: absolute;
        left: -10px;
        right: -10px;
        height: 3px;
        background: linear-gradient(90deg, transparent, var(--secondary-solid), transparent);
        box-shadow: 0 0 12px 2px var(--secondary-solid);
        opacity: 0;
        pointer-events: none;
    }

    .scanner-simulation-container.focused .scanner-laser-line {
        opacity: 1;
        animation: laserScan 2.5s ease-in-out infinite;
    }

    .scanner-simulation-container.unfocused .scanner-laser-line {
        background: linear-gradient(90deg, transparent, var(--error-red), transparent);
        box-shadow: 0 0 12px 2px var(--error-red);
        opacity: 0.5;
        top: 50%;
    }

    /* Target Corners */
    .corner {
        position: absolute;
        width: 20px;
        height: 20px;
        border: 3px solid #cbd5e1;
        transition: all 0.3s ease;
    }

    .scanner-simulation-container.focused .corner {
        border-color: var(--primary-solid);
    }
    
    .scanner-simulation-container.unfocused .corner {
        border-color: var(--error-red);
    }

    .top-left { top: 15px; left: 15px; border-right: none; border-bottom: none; border-top-left-radius: 8px; }
    .top-right { top: 15px; right: 15px; border-left: none; border-bottom: none; border-top-right-radius: 8px; }
    .bottom-left { bottom: 15px; left: 15px; border-right: none; border-top: none; border-bottom-left-radius: 8px; }
    .bottom-right { bottom: 15px; right: 15px; border-left: none; border-top: none; border-bottom-right-radius: 8px; }

    .scanner-instruction {
        font-size: 0.85rem;
        font-weight: 600;
        text-align: center;
        margin: 0;
        transition: all 0.3s ease;
    }

    .scanner-simulation-container.focused .scanner-instruction {
        color: var(--primary-solid);
    }

    .scanner-simulation-container.unfocused .scanner-instruction {
        color: var(--error-red);
        animation: shake 0.5s ease;
    }

    /* ==================== RIGHT COLUMN: FORM & VALIDATION ==================== */
    .form-group-floating {
        position: relative;
        margin-bottom: 2rem;
    }

    .form-label-floating {
        position: absolute;
        left: 50px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.95rem;
        color: var(--text-secondary);
        font-weight: 500;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        background: transparent;
        padding: 0 4px;
    }

    .input-icon-left {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        font-size: 22px;
        pointer-events: none;
        transition: color 0.3s ease;
    }

    .premium-form-control {
        width: 100%;
        padding: 18px 20px 18px 52px;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        background: white;
        color: var(--text-dark);
        font-size: 1.15rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-sizing: border-box;
        text-transform: uppercase;
    }

    .premium-form-control:focus, 
    .premium-form-control:not(:placeholder-shown) {
        border-color: var(--primary-solid);
        outline: none;
        box-shadow: 0 0 0 4px rgba(31, 64, 131, 0.08);
    }

    /* Floating Label lift effect */
    .premium-form-control:focus ~ .form-label-floating,
    .premium-form-control:not(:placeholder-shown) ~ .form-label-floating {
        top: 0;
        transform: translateY(-50%) scale(0.85);
        background: #f6f9fc; /* Match main layout bg */
        color: var(--primary-solid);
        font-weight: 700;
        left: 45px;
    }

    .premium-form-control:focus ~ .input-icon-left {
        color: var(--primary-solid);
    }

    /* Quick Fill Helpers */
    .quick-prefixes-row {
        display: flex;
        gap: 8px;
        margin-top: -1.25rem;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }

    .prefix-badge {
        font-size: 0.75rem;
        font-weight: 700;
        background: #e2e8f0;
        color: var(--text-secondary);
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .prefix-badge:hover {
        background: var(--primary-glow);
        color: var(--primary-solid);
        border-color: rgba(31, 64, 131, 0.2);
    }

    /* Submit Button Magnetic Style */
    .btn-submit-premium {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, var(--primary-solid), #2a52be);
        color: white;
        border: none;
        border-radius: 16px;
        font-size: 1.05rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 12px rgba(31, 64, 131, 0.2);
    }

    .btn-submit-premium:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(31, 64, 131, 0.3);
        background: linear-gradient(135deg, #1b3975, #22439c);
    }

    .btn-submit-premium:active:not(:disabled) {
        transform: translateY(1px);
    }

    .btn-submit-premium:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Loading Spinner */
    .loading-spinner-circle {
        display: none;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s linear infinite;
    }

    /* ==================== ALERT MESSAGES ==================== */
    .alert-premium-box {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 1.25rem 1.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        border: 1px solid;
        animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .alert-premium-box.success {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #065f46;
    }

    .alert-premium-box.error {
        background: #fef2f2;
        border-color: #fecaca;
        color: #991b1b;
    }

    .alert-premium-box i {
        font-size: 24px;
        flex-shrink: 0;
    }

    .alert-premium-content {
        flex-grow: 1;
        font-size: 0.92rem;
        font-weight: 600;
        line-height: 1.5;
    }

    .alert-close-btn {
        background: none;
        border: none;
        color: inherit;
        opacity: 0.6;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        transition: opacity 0.2s;
    }

    .alert-close-btn:hover {
        opacity: 1;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes laserScan {
        0%, 100% { top: 15px; }
        50% { top: 115px; }
    }

    @keyframes pulseRed {
        0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
    }

    @keyframes pulseBorderRed {
        0%, 100% { border-color: rgba(239, 68, 68, 0.3); }
        50% { border-color: rgba(239, 68, 68, 0.7); }
    }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }
</style>

<div class="premium-page-container">
    
    <!-- Top banner with scanning indicator -->
    <div class="page-header-banner">
        <div class="header-info">
            <h1>Réception & Enregistrement</h1>
            <p>Scannez ou saisissez la référence unique d'un colis pour l'enregistrer dans votre poste.</p>
        </div>
        <div class="scanner-status-indicator" id="scannerStatusWidget" onclick="forceScannerFocus()">
            <div class="status-dot"></div>
            <span id="scannerStatusText">Veille active</span>
        </div>
    </div>

    @if(session('success') || session('error'))
        <!-- Dynamic Alert Panel -->
        <div class="alert-premium-box {{ session('success') ? 'success' : 'error' }}" id="sessionAlert">
            <i class="material-icons">{{ session('success') ? 'check_circle' : 'error_outline' }}</i>
            <div class="alert-premium-content">
                {{ session('success') ?? session('error') }}
            </div>
            <button class="alert-close-btn" onclick="dismissAlert()">
                <i class="material-icons">close</i>
            </button>
        </div>
    @endif

    <!-- Interactive Grid Content -->
    <div class="main-interactive-grid">
        
        <!-- Left Side: Scanner Zone -->
        <div class="interactive-card">
            <span class="card-title-badge">
                <i class="material-icons" style="font-size: 16px;">qr_code_scanner</i>
                Interface Scanner
            </span>
            <h2>Lecteur Automatique</h2>
            <p class="card-subtitle">
                Le système attend un signal de scan. Utilisez votre douchette physique ou cliquez sur la zone ci-dessous pour activer la réception automatique.
            </p>

            <div class="scanner-simulation-container focused" id="scannerFrame" onclick="forceScannerFocus()">
                <!-- Target corners -->
                <div class="corner top-left"></div>
                <div class="corner top-right"></div>
                <div class="corner bottom-left"></div>
                <div class="corner bottom-right"></div>

                <div class="scanner-visual-box">
                    <div class="scanner-laser-line"></div>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                </div>

                <p class="scanner-instruction" id="scannerInstructionText">
                    Scanner prêt à recevoir le colis...
                </p>
            </div>
        </div>

        <!-- Right Side: Manual Form Entry -->
        <div class="interactive-card">
            <span class="card-title-badge orange">
                <i class="material-icons" style="font-size: 16px;">edit</i>
                Formulaire
            </span>
            <h2>Saisie Manuelle</h2>
            <p class="card-subtitle">
                Saisissez manuellement le numéro de référence du récépissé ci-dessous si le code QR n'est pas lisible.
            </p>

            <form action="{{ route('poste.attribuer-demande') }}" method="POST" id="deliveryForm">
                @csrf
                
                <div class="form-group-floating">
                    <i class="material-icons input-icon-left">label</i>
                    <input type="text" 
                           name="reference" 
                           id="referenceInput"
                           class="premium-form-control" 
                           placeholder=" " 
                           required 
                           autofocus
                           autocomplete="off">
                    <label class="form-label-floating">Numéro de Référence du Colis (Ex: AN...)</label>
                </div>

                <!-- Quick Prefixes badges -->
                <div class="quick-prefixes-row">
                    <span class="prefix-badge" onclick="fillPrefix('AN')">
                        <i class="material-icons" style="font-size: 13px;">baby_changing_station</i> AN
                    </span>
                    <span class="prefix-badge" onclick="fillPrefix('AD')">
                        <i class="material-icons" style="font-size: 13px;">mood_bad</i> AD
                    </span>
                    <span class="prefix-badge" onclick="fillPrefix('AM')">
                        <i class="material-icons" style="font-size: 13px;">favorite</i> AM
                    </span>
                </div>

                <button type="submit" class="btn-submit-premium" id="btnSubmit">
                    <span class="loading-spinner-circle" id="btnSpinner"></span>
                    <i class="material-icons" id="btnIcon">check_circle</i>
                    <span id="btnText">Enregistrer en courrier</span>
                </button>
            </form>
        </div>
    </div>

</div>

<!-- ========== CLIENT SIDE JAVASCRIPT LOGIC ========== -->
<script>
    // Autofocus enforcement & Scan Status Management
    const refInput = document.getElementById('referenceInput');
    const scannerFrame = document.getElementById('scannerFrame');
    const statusWidget = document.getElementById('scannerStatusWidget');
    const statusText = document.getElementById('scannerStatusText');
    const instructionText = document.getElementById('scannerInstructionText');
    const deliveryForm = document.getElementById('deliveryForm');

    // Forces focus on the main input field
    function forceScannerFocus() {
        refInput.focus();
    }

    // Input events
    refInput.addEventListener('focus', () => {
        scannerFrame.classList.remove('unfocused');
        scannerFrame.classList.add('focused');
        statusWidget.classList.remove('focus-lost');
        statusText.textContent = "Veille active";
        instructionText.textContent = "Scanner prêt à recevoir le colis...";
    });

    refInput.addEventListener('blur', () => {
        // We use a slight timeout to check if focus went elsewhere
        setTimeout(() => {
            if (document.activeElement !== refInput) {
                scannerFrame.classList.remove('focused');
                scannerFrame.classList.add('unfocused');
                statusWidget.classList.add('focus-lost');
                statusText.textContent = "Perte de focus";
                instructionText.textContent = "Cliquez sur l'écran pour réactiver le scanner";
            }
        }, 100);
    });

    // Make input automatically uppercase in real-time
    refInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Prefill helper prefixes
    function fillPrefix(prefix) {
        refInput.value = prefix;
        refInput.focus();
    }

    // Auto-dismiss Alert helper
    function dismissAlert() {
        const alertBox = document.getElementById('sessionAlert');
        if (alertBox) {
            alertBox.style.opacity = '0';
            alertBox.style.transform = 'translateY(-10px)';
            alertBox.style.transition = 'all 0.3s ease';
            setTimeout(() => alertBox.remove(), 300);
        }
    }

    // Auto-close alert after 7 seconds
    document.addEventListener('DOMContentLoaded', () => {
        const alertBox = document.getElementById('sessionAlert');
        if (alertBox) {
            setTimeout(dismissAlert, 7000);
        }
    });

    // Save reference on submit to show beautiful loading state
    deliveryForm.addEventListener('submit', function() {
        // Show loading state
        const btn = document.getElementById('btnSubmit');
        const spinner = document.getElementById('btnSpinner');
        const icon = document.getElementById('btnIcon');
        const text = document.getElementById('btnText');
        
        btn.disabled = true;
        spinner.style.display = 'inline-block';
        icon.style.display = 'none';
        text.textContent = "Traitement...";
    });

    // Keep input focused automatically on page load
    document.addEventListener('DOMContentLoaded', () => {
        forceScannerFocus();
    });
</script>
@endsection
