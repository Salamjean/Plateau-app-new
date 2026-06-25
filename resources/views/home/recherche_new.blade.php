<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivre ma demande — Plateau Smart City</title>

    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-blue: #d7e5fa;
            --primary-dark: #1f4083;
            --primary: #1977cc;
            --primary-light: #4facfe;
            --success: #01b574;
            --success-dark: #019560;
            --text-navy: #1a2e5c;
            --text-grey: #6b7280;
            --white: #ffffff;
            --circle-blue: #b8d3f0;
            --circle-deep: #7ba9d6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #0d2a54 0%, #051429 100%);
            color: #ffffff;
            min-height: 100vh;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            padding: 32px 64px;
        }

        .background-skyline {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 450px;
            background: url('{{ asset("assets/landing/about-background.png") }}') center bottom / cover no-repeat;
            opacity: 0.08;
            z-index: 1;
            pointer-events: none;
        }

        /* ─────────────── HEADER ─────────────── */
        .top-bar {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-shrink: 0;
            width: 100%;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-img {
            height: 52px;
            width: auto;
            object-fit: contain;
            border-radius: 6px;
        }

        .nav-buttons {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .btn-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            cursor: pointer;
        }

        .btn-pill.outline {
            background: transparent;
            color: var(--success);
            border-color: var(--success);
        }

        .btn-pill.outline:hover {
            background: var(--success);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(1, 181, 116, 0.25);
        }

        .btn-pill.solid {
            background: var(--success);
            color: white;
        }

        .btn-pill.solid:hover {
            background: var(--success-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(1, 181, 116, 0.3);
        }

        /* ─────────────── BREADCRUMB ─────────────── */
        .breadcrumb {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            width: 100%;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .breadcrumb a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            transition: color 0.3s;
            display: inline-flex;
            align-items: center;
        }

        .breadcrumb a:hover {
            color: var(--success);
        }

        .breadcrumb .separator {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.4);
        }

        .breadcrumb .active {
            color: #ffffff;
            font-weight: 500;
        }

        /* ─────────────── MAIN CONTENT ─────────────── */
        .main-content {
            flex: 1;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 60px;
            align-items: start;
            position: relative;
            z-index: 5;
        }

        /* ─────────────── LEFT COLUMN ─────────────── */
        .left-column {
            display: flex;
            flex-direction: column;
        }

        .section-label {
            color: var(--success);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 12px;
            display: block;
            text-transform: uppercase;
        }

        .main-title {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
            color: #ffffff;
            letter-spacing: -1px;
        }

        .main-title span {
            color: var(--success);
        }

        .description {
            font-size: 15px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 35px;
            max-width: 520px;
        }

        /* Form Card */
        .search-form-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 30px;
            max-width: 520px;
            margin-bottom: 40px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .search-input-wrapper {
            position: relative;
            width: 100%;
        }

        .search-input-wrapper .search-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.4);
            font-size: 16px;
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 16px 20px 16px 52px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: #ffffff;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .search-input-wrapper input:focus {
            outline: none;
            border-color: var(--success);
            background: rgba(0, 0, 0, 0.3);
            box-shadow: 0 0 0 4px rgba(1, 181, 116, 0.15);
        }

        .help-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: color 0.3s;
            margin-top: 10px;
            cursor: pointer;
        }

        .help-link:hover {
            color: var(--success);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: var(--success);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background: var(--success-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(1, 181, 116, 0.3);
        }

        .security-notice {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.4);
            font-size: 12px;
            line-height: 1.5;
        }

        .security-notice i {
            font-size: 16px;
            color: var(--success);
            margin-top: 2px;
        }

        .security-notice strong {
            color: rgba(255, 255, 255, 0.7);
            display: block;
            margin-bottom: 2px;
        }

        /* Alert styling */
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 14px 16px;
            color: #fca5a5;
            font-size: 14px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .alert-error i {
            font-size: 16px;
            color: #ef4444;
        }

        /* Status Grid Cards */
        .status-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            max-width: 100%;
            margin-top: 20px;
        }

        .status-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s;
        }

        .status-card:hover {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-4px);
        }

        .status-card .icon-box {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(25, 119, 204, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-light);
            font-size: 16px;
            margin-bottom: 12px;
        }

        .status-card h4 {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .status-card p {
            font-size: 12px;
            line-height: 1.4;
            color: rgba(255, 255, 255, 0.5);
        }

        /* ─────────────── RIGHT COLUMN (IPHONE MOCKUP) ─────────────── */
        .phone-column {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            width: 100%;
        }

        .phone-circle-bg {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(79, 172, 254, 0.12) 0%, rgba(79, 172, 254, 0) 70%);
            z-index: 1;
            pointer-events: none;
        }

        .iphone-frame {
            width: 320px;
            height: 640px;
            background: #000000;
            border: 10px solid #1f2937;
            border-radius: 44px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 2;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .iphone-island {
            width: 100px;
            height: 24px;
            background: #000000;
            border-radius: 15px;
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 100;
        }

        .iphone-screen {
            flex: 1;
            background: #f4f6f9;
            color: #0f172a;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            scrollbar-width: none;
            position: relative;
            padding: 0 0 20px 0;
        }

        .iphone-screen::-webkit-scrollbar {
            display: none;
        }

        .screen-blue-header {
            background: linear-gradient(180deg, #103370 0%, #0d2859 100%);
            padding: 45px 18px 45px;
            color: #ffffff;
            border-bottom-left-radius: 28px;
            border-bottom-right-radius: 28px;
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .screen-status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 12px;
            padding: 0 4px;
        }

        .screen-status-bar .icons {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .screen-blue-header .screen-header-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 8px;
        }

        .screen-blue-header .screen-header-nav h3 {
            font-size: 15px;
            font-weight: 700;
            color: #ffffff;
        }

        .screen-blue-header .screen-header-nav .back-btn,
        .screen-blue-header .screen-header-nav .more-btn {
            color: #ffffff;
            opacity: 0.8;
            font-size: 15px;
            cursor: pointer;
        }

        .tracking-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            margin: -25px 14px 16px;
            position: relative;
            z-index: 2;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .tracking-card .ref-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 12px;
        }

        .tracking-card .ref-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tracking-card .ref-value {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 10px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-badge.pending {
            background: #fff7ed;
            color: #ea580c;
        }

        .status-badge.validated {
            background: #f0fdf4;
            color: #16a34a;
        }

        .status-badge.rejected {
            background: #fef2f2;
            color: #dc2626;
        }

        .status-badge.processing {
            background: #f0f9ff;
            color: #0284c7;
        }

        .details-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-item.justify-between {
            justify-content: space-between;
        }

        .detail-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(16, 51, 112, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #103370;
            font-size: 13px;
            flex-shrink: 0;
        }

        .detail-text {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 9px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .detail-value {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .total-qty-badge {
            background: #eef5fc;
            border-radius: 8px;
            padding: 4px 10px;
            text-align: center;
            border: 1.5px solid #d0e3f7;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 48px;
        }

        .total-qty-badge .num {
            font-size: 14px;
            font-weight: 800;
            color: #103370;
            line-height: 1;
        }

        .total-qty-badge .lbl {
            font-size: 8px;
            font-weight: 700;
            color: #5b75a6;
            text-transform: uppercase;
            margin-top: 1px;
        }

        /* Steps */
        .steps-container {
            background: #ffffff;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            margin: 0 14px 16px;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .steps-container h4 {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .steps-list {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .steps-list::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 10px;
            bottom: 10px;
            width: 2px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step-item {
            display: flex;
            gap: 16px;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .step-item:last-child {
            margin-bottom: 0;
        }

        .step-marker {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #94a3b8;
            font-weight: 700;
            flex-shrink: 0;
            transition: all 0.3s;
        }

        .step-item.active .step-marker {
            background: #103370;
            border-color: #103370;
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(16, 51, 112, 0.15);
        }

        .step-item.completed .step-marker {
            background: var(--success);
            border-color: var(--success);
            color: #ffffff;
        }

        .step-content {
            display: flex;
            flex-direction: column;
        }

        .step-title {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 2px;
        }

        .step-item.active .step-title,
        .step-item.completed .step-title {
            color: #0f172a;
        }

        .step-desc {
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.4;
        }

        .step-item.active .step-desc {
            color: #64748b;
        }

        /* Mockup containers for user's static image */
        .phone-image-container {
            width: 320px;
            height: 640px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
            z-index: 2;
            border-radius: 44px;
            filter: drop-shadow(0 25px 50px rgba(0, 0, 0, 0.4));
        }

        .iphone-image-mockup {
            height: 125%;
            width: auto;
            object-fit: contain;
            display: block;
        }

        /* Demo badge overlay */
        .demo-badge-overlay {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 16px;
            font-size: 12px;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .demo-badge-overlay i {
            color: var(--primary);
        }

        /* ─────────────── MODAL ─────────────── */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.open {
            display: flex;
        }

        .modal-content {
            background: #0f1c30;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            max-width: 450px;
            width: 100%;
            padding: 24px;
            color: #ffffff;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
        }

        .close-modal {
            font-size: 24px;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-modal:hover {
            color: #ffffff;
        }

        .modal-body p {
            font-size: 14px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 12px;
        }

        .modal-body strong {
            color: var(--success);
        }

        /* ─────────────── RESPONSIVE ─────────────── */
        @media (max-width: 991px) {
            body {
                padding: 15px 20px;
            }

            .top-bar {
                flex-direction: column;
                gap: 15px;
                margin-bottom: 30px;
            }

            .breadcrumb {
                justify-content: center;
                margin-bottom: 30px;
            }

            .main-content {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }

            .main-title {
                font-size: 34px;
            }

            .description {
                margin: 0 auto 30px auto;
            }

            .search-form-card {
                margin: 0 auto 30px auto;
            }

            .status-cards-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .phone-column {
                margin-top: 20px;
            }
        }

        @media (max-width: 575px) {
            .status-cards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="background-skyline"></div>

    <!-- HEADER : Logo + Boutons -->
    <header class="top-bar">
        <div class="logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/assets/img/plateau-mart1.png') }}" alt="Plateau Smart City" class="logo-img">
            </a>
        </div>

        <nav class="nav-buttons">
            <a href="{{ route('recherche.demande') }}" class="btn-pill outline">
                <i class="fas fa-search"></i> Suivre ma demande
            </a>
            <a href="{{ route('login') }}" class="btn-pill solid">
                <i class="fas fa-user"></i> Mon espace
            </a>
        </nav>
    </header>

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="{{ route('home') }}"><i class="fas fa-home"></i></a>
        <span class="separator"><i class="fas fa-chevron-right"></i></span>
        <span class="active">Suivre ma demande</span>
    </div>

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- COLUMN LEFT -->
        <div class="left-column">
            <span class="section-label">Suivi de demande</span>
            <h1 class="main-title">Suivez l'avancement<br><span>de votre demande</span></h1>
            <p class="description">
                Renseignez votre numéro de référence pour consulter l'état d'avancement de votre dossier en temps réel.
            </p>

            <div class="search-form-card">
                <!-- Message d'erreur si référence introuvable -->
                @if ($etatDemande === false)
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Aucune demande trouvée pour la référence <strong>{{ $reference }}</strong>.</span>
                    </div>
                @endif

                <form action="{{ route('recherche.demande') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="reference_naissance">Saisissez le numéro de référence figurant sur votre récépissé</label>
                        <div class="search-input-wrapper">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="reference_naissance" id="reference_naissance"
                                placeholder="Ex : PLA-2026-123456" value="{{ $reference ?? '' }}" required>
                        </div>
                        <a class="help-link" id="helpLink">
                            <i class="far fa-question-circle"></i> Où trouver mon numéro de référence ?
                        </a>
                    </div>

                    <button type="submit" class="btn-submit">
                        Suivre ma demande <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

                <div class="security-notice">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>Vos données sont sécurisées</strong>
                        Plateau Apps garantit la confidentialité et la protection de toutes vos informations.
                    </div>
                </div>
            </div>

            <!-- CARDS GRID -->
            <div class="status-cards-grid">
                <div class="status-card">
                    <div class="icon-box"><i class="far fa-clock"></i></div>
                    <h4>Suivi en temps réel</h4>
                    <p>Consultez l'état d'avancement à tout moment.</p>
                </div>
                <div class="status-card">
                    <div class="icon-box"><i class="far fa-bell"></i></div>
                    <h4>Notifications</h4>
                    <p>Soyez informé à chaque étape clé.</p>
                </div>
                <div class="status-card">
                    <div class="icon-box"><i class="fas fa-shield-alt"></i></div>
                    <h4>Sécurisé</h4>
                    <p>Vos informations sont chiffrées et protégées.</p>
                </div>
                <div class="status-card">
                    <div class="icon-box"><i class="fas fa-headset"></i></div>
                    <h4>Besoin d'aide ?</h4>
                    <p>Contactez notre support pour toute assistance.</p>
                </div>
            </div>
        </div>

        <!-- COLUMN RIGHT (IPHONE MOCKUP) -->
        <div class="phone-column">
            <div class="phone-circle-bg"></div>

            @if ($demande)
                <div class="iphone-frame">
                    <div class="iphone-island"></div>
                    <div class="iphone-screen">
                        
                        <!-- Header with blue background, status bar, and nav -->
                        <div class="screen-blue-header">
                            <div class="screen-status-bar">
                                <span>9:41</span>
                                <div class="icons">
                                    <i class="fas fa-signal"></i>
                                    <i class="fas fa-wifi"></i>
                                    <i class="fas fa-battery-three-quarters"></i>
                                </div>
                            </div>
                            <div class="screen-header-nav">
                                <i class="fas fa-chevron-left back-btn"></i>
                                <h3>Détails du suivi</h3>
                                <i class="fas fa-ellipsis-h more-btn"></i>
                            </div>
                        </div>

                        <!-- Card displaying tracking details -->
                        <div class="tracking-card">
                            <div class="ref-line">
                                <div>
                                    <span class="ref-label">Référence</span>
                                    <div class="ref-value">{{ $demande->reference }}</div>
                                </div>
                                @if ($demande->etat === 'valide' || $demande->etat === 'termine')
                                    <span class="status-badge validated"><i class="fas fa-circle" style="font-size: 6px; margin-right: 2px;"></i> Validée</span>
                                @elseif($demande->etat === 'rejete' || $demande->etat === 'rejeter')
                                    <span class="status-badge rejected"><i class="fas fa-circle" style="font-size: 6px; margin-right: 2px;"></i> Rejetée</span>
                                @elseif($demande->etat === 'encours')
                                    <span class="status-badge processing"><i class="fas fa-circle" style="font-size: 6px; margin-right: 2px;"></i> En cours</span>
                                @else
                                    <span class="status-badge pending"><i class="fas fa-circle" style="font-size: 6px; margin-right: 2px;"></i> En attente</span>
                                @endif
                            </div>

                            <div class="details-list">
                                <div class="detail-item">
                                    <div class="detail-icon"><i class="far fa-file-alt"></i></div>
                                    <div class="detail-text">
                                        <span class="detail-label">Type de document</span>
                                        <span class="detail-value">
                                            @if (isset($demande->nom_prenoms_pere))
                                                Extrait de Naissance
                                            @elseif(isset($demande->defunt_nom))
                                                Acte de Décès
                                            @else
                                                Acte de Mariage
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="detail-item justify-between">
                                    <div style="display: flex; gap: 12px; align-items: center;">
                                        <div class="detail-icon"><i class="fas fa-copy"></i></div>
                                        <div class="detail-text">
                                            <span class="detail-label">Nature du document</span>
                                            <span class="detail-value">
                                                @if ($demande->qty_integral > 0 && $demande->qty_simple > 0)
                                                    Copie Intégrale ({{ $demande->qty_integral }}) & Simple ({{ $demande->qty_simple }})
                                                @elseif($demande->qty_integral > 0)
                                                    Copie Intégrale ({{ $demande->qty_integral }})
                                                @else
                                                    Copie Simple ({{ $demande->qty_simple ?? 1 }})
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    @php
                                        $totalQty = ($demande->qty_simple ?? 0) + ($demande->qty_integral ?? 0);
                                        $totalQty = $totalQty > 0 ? $totalQty : ($demande->quantite ?? 1);
                                    @endphp
                                    <div class="total-qty-badge">
                                        <span class="num">{{ $totalQty }}</span>
                                        <span class="lbl">Total</span>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon"><i class="fas fa-university"></i></div>
                                    <div class="detail-text">
                                        <span class="detail-label">Mode de retrait</span>
                                        <span class="detail-value">{{ $statutDemande ?? 'Retrait en Mairie' }}</span>
                                    </div>
                                </div>
                                
                                <div class="detail-item">
                                    <div class="detail-icon"><i class="far fa-calendar-alt"></i></div>
                                    <div class="detail-text">
                                        <span class="detail-label">Date de la demande</span>
                                        <span class="detail-value">{{ $demande->created_at ? $demande->created_at->format('d/m/Y à H:i') : 'Récemment' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Steps -->
                        <div class="steps-container">
                            <h4>Étapes d'avancement</h4>
                            <div class="steps-list">
                                <!-- Étape 1 : Réception -->
                                <div class="step-item {{ in_array($demande->etat, ['attente', 'en attente', 'encours', 'valide', 'termine']) ? 'completed' : '' }}">
                                    <div class="step-marker">
                                        @if(in_array($demande->etat, ['attente', 'en attente', 'encours', 'valide', 'termine']))
                                            <i class="fas fa-check"></i>
                                        @else
                                            1
                                        @endif
                                    </div>
                                    <div class="step-content">
                                        <div class="step-title">Réception</div>
                                        <div class="step-desc">Votre demande a été reçue et enregistrée.</div>
                                    </div>
                                </div>

                                <!-- Étape 2 : Vérification -->
                                <div class="step-item @if($demande->etat === 'encours') active @elseif(in_array($demande->etat, ['valide', 'termine'])) completed @endif">
                                    <div class="step-marker">
                                        @if(in_array($demande->etat, ['valide', 'termine']))
                                            <i class="fas fa-check"></i>
                                        @else
                                            2
                                        @endif
                                    </div>
                                    <div class="step-content">
                                        <div class="step-title">Vérification</div>
                                        <div class="step-desc">Examen des pièces justificatives par nos officiers.</div>
                                    </div>
                                </div>

                                <!-- Étape 3 : Retrait / Livraison -->
                                <div class="step-item @if(in_array($demande->etat, ['valide', 'termine'])) completed @endif">
                                    <div class="step-marker">
                                        @if(in_array($demande->etat, ['valide', 'termine']))
                                            <i class="fas fa-check"></i>
                                        @else
                                            3
                                        @endif
                                    </div>
                                    <div class="step-content">
                                        <div class="step-title">Mise à disposition</div>
                                        <div class="step-desc">
                                            @if($statutDemande === 'DHL' || $statutDemande === 'Livreur')
                                                Document expédié à l'adresse de livraison.
                                            @else
                                                Document disponible pour retrait en Mairie.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="phone-image-container">
                    <img src="{{ asset('assets/assets/img/FaceTEL.png') }}" class="iphone-image-mockup" alt="Suivi de demande">
                </div>
            @endif
        </div>
    </main>

    <!-- MODAL -->
    <div class="modal" id="helpModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Où trouver la référence ?</h3>
                <span class="close-modal" id="closeModal">&times;</span>
            </div>
            <div class="modal-body">
                <p>Votre numéro de référence figure en haut à droite de votre reçu de paiement imprimé ou dans l'e-mail de confirmation qui vous a été envoyé après la soumission de votre demande en ligne.</p>
                <p>Il est composé de lettres et de chiffres, par exemple : <strong>PLA-2026-123456</strong>.</p>
                <div style="text-align: center; margin-top: 20px;">
                    <i class="fas fa-file-invoice" style="font-size: 48px; color: var(--success); opacity: 0.8;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT -->
    <script>
        const helpLink = document.getElementById('helpLink');
        const helpModal = document.getElementById('helpModal');
        const closeModal = document.getElementById('closeModal');

        if (helpLink && helpModal && closeModal) {
            helpLink.addEventListener('click', (e) => {
                e.preventDefault();
                helpModal.classList.add('open');
            });
            closeModal.addEventListener('click', () => {
                helpModal.classList.remove('open');
            });
            helpModal.addEventListener('click', (e) => {
                if (e.target === helpModal) {
                    helpModal.classList.remove('open');
                }
            });
        }
    </script>
</body>

</html>
