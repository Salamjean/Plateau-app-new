<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateau Smart City - État Civil Simplifié</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" type="image/x-icon">

    <!-- CSS Vendors -->
    <link href="{{ asset('assets/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* DESIGN STRICTEMENT IDENTIQUE AU MODÈLE FIGMA */
        html,
        body {
            height: 100% !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #e5eef9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0b2545;
            scroll-behavior: smooth;
        }

        /* --- BACKGROUND GRADIENT GENERAL FIGMA --- */
        .page-container {
            height: 100vh;
            width: 100vw;
            background: radial-gradient(circle at 10% 20%, #f0f6ff 0%, #e1ecf9 60%, #d8e6f7 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* --- HEADER FIGMA ET SES BOUTONS --- */
        .figma-header {
            position: absolute;
            top: -5px;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: transparent;
            padding: 0px 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-img {
            height: 180px;
            width: auto;
            object-fit: contain;
            margin-top: -45px;
            margin-bottom: -45px;
        }

        .logo-main {
            color: #0b2545;
            font-size: 1.35rem;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .logo-sub {
            color: #3b82f6;
            font-size: 0.78rem;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .btn-figma-primary {
            background-color: #10b981;
            color: white !important;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 12px 28px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-figma-primary:hover {
            background-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
        }

        .btn-figma-outline {
            background-color: transparent;
            color: #10b981 !important;
            border: 2px solid #10b981;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 10px 28px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-figma-outline:hover {
            background-color: #10b981;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
        }

        /* --- CONTENU DE LA PAGE --- */
        .main-content {
            flex-grow: 1;
            display: flex;
            align-items: center;
            padding: 120px 80px 0px 80px;
            position: relative;
        }

        /* COLONNE GAUCHE CONFIGURÉE POUR RESTER PROCHE DU TÉLÉPHONE */
        .hero-left-col {
            position: relative;
            z-index: 10;
        }

        /* TYPOGRAPHIE MAQUETTE FIGMA */
        .hero-title-main {
            font-size: 5.6rem;
            line-height: 0.95;
            font-weight: 900;
            color: #0b2545;
            letter-spacing: -2px;
            margin: 0;
        }

        .hero-title-sub {
            font-size: 7.2rem;
            line-height: 0.95;
            font-weight: 900;
            color: #10b981;
            letter-spacing: -3px;
            margin: 0 0 25px 0;
        }

        .hero-desc {
            font-size: 1.15rem;
            line-height: 1.65;
            max-width: 480px;
            color: #475569 !important;
            margin-bottom: 40px !important;
        }

        /* AVANTAGES LIST INLINE Horizontal */
        .advantages-row {
            margin-bottom: 40px !important;
        }

        .advantage-item {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .advantage-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 1px solid #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #10b981;
            font-size: 1.4rem;
            background-color: transparent;
        }

        .advantage-text {
            font-weight: 700;
            color: #0b2545;
            font-size: 0.95rem;
            line-height: 1.3;
        }

        /* DOWNLOAD BADGES */
        .download-buttons {
            margin-bottom: 40px !important;
        }

        .store-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background-color: #111827;
            color: white !important;
            padding: 10px 22px;
            border-radius: 12px;
            text-decoration: none !important;
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .store-btn:hover {
            transform: translateY(-2px);
            background-color: #1f2937;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .store-logo {
            font-size: 1.8rem;
        }

        .store-text {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .store-sub {
            font-size: 0.65rem;
            font-weight: 600;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .store-main {
            font-size: 0.95rem;
            font-weight: 800;
            line-height: 1;
        }

        /* AVATARS PROOF */
        .avatar-img {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: 3px solid #e1ecf9;
            margin-right: -15px;
            object-fit: cover;
        }

        .proof-text {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .proof-title {
            line-height: 1.35;
            font-size: 0.98rem;
            font-weight: 700;
            color: #0b2545;
        }

        .rating-stars {
            font-size: 0.85rem;
        }

        .text-green {
            color: #10b981 !important;
        }

        /* CONTENEUR UNIQUE D'ALIGNEMENT POUR LE DISQUE ET LE TÉLÉPHONE (AJUSTEMENTS FINAUX) */
        .hero-right-container {
            position: absolute;
            right: -200px;
            top: 50%;
            margin-top: -220px;
            /* Aligné dynamiquement sur la même ligne que le contenu textuel de gauche */
            width: 1000px;
            height: 1000px;
            pointer-events: none;
            z-index: 1;
        }

        .hero-circle-bg {
            position: absolute;
            width: 780px;
            height: 780px;
            background-color: #0d2c67;
            border-radius: 50%;
            z-index: 1;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 25px 75px rgba(13, 44, 103, 0.25);
        }

        .hero-circle-outline {
            position: absolute;
            width: 880px;
            height: 880px;
            border: 2px solid rgba(13, 44, 103, 0.12);
            border-radius: 50%;
            z-index: 0;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        /* IMAGE TÉLÉPHONE (4000x3000) ET AFFICHAGE FIGMA EXPORT (AGRANDI ET CENTRÉ) */
        .phone-mockup-wrapper {
            position: absolute;
            z-index: 2;
            left: 27%;
            top: 27%;
            transform: translate(-50%, -50%);
            width: 1250px;
            height: auto;
        }

        .hero-phone-img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        /* --- VISUAL CHEVRON DOWN --- */
        .scroll-down-arrow-container {
            width: 100%;
            display: flex;
            justify-content: center;
            padding-bottom: 24px;
            z-index: 10;
        }

        .scroll-down-arrow-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #0b2545;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 12px rgba(11, 37, 69, 0.25);
        }

        .scroll-arrow-icon {
            font-size: 1.4rem;
        }

        /* --- RESPONSIVE MOBILE --- */
        @media (max-width: 991px) {

            html,
            body {
                overflow-y: auto !important;
                /* Autorise le scroll uniquement sur mobile */
            }

            .page-container {
                height: auto;
                min-height: 100vh;
                justify-content: flex-start;
            }

            .figma-header {
                padding: 20px 24px;
            }

            .main-content {
                padding: 40px 24px;
                flex-direction: column;
            }

            .hero-left-col {
                align-items: center;
                text-align: center;
                width: 100% !important;
            }

            .hero-title-main {
                font-size: 3.5rem;
                letter-spacing: -1.5px;
            }

            .hero-title-sub {
                font-size: 4.2rem;
                letter-spacing: -2px;
            }

            .hero-desc {
                text-align: center;
                margin: 0 auto 30px !important;
            }

            .advantages-row {
                justify-content: center;
            }

            .download-buttons {
                justify-content: center;
                width: 100%;
            }

            .store-btn {
                flex-grow: 1;
                max-width: 220px;
                justify-content: center;
            }

            .user-proof {
                justify-content: center;
                flex-direction: column;
                text-align: center;
            }

            .avatar-group {
                margin-right: 14px;
            }
        }

        /* --- NEW VISION & ABOUT SECTION --- */
        .vision-about-section {
            background: radial-gradient(circle at bottom center, rgba(34, 197, 94, 0.22) 0%, rgba(16, 58, 131, 0) 55%),
                linear-gradient(180deg, #092552 0%, #103a83 50%, #092552 100%),
                url('{{ asset("assets/assets/img/footerback.png") }}') no-repeat bottom center !important;
            background-size: 100% 100%, 100% 100%, cover !important;
            color: #ffffff;
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 60px 0;
            z-index: 1;
        }

        .section-label-wrapper {
            display: inline-block;
            margin-bottom: 12px;
        }

        .section-label {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.7);
        }

        .label-accent-line {
            width: 32px;
            height: 3px;
            background-color: #22c55e;
            margin-top: 6px;
            border-radius: 2px;
        }

        .vision-about-section .section-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 2.25rem;
            line-height: 1.25;
            color: #ffffff !important;
        }

        .vision-about-section .section-desc {
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 0.95rem;
            line-height: 1.6;
            font-weight: 400;
        }

        .vision-about-section .text-green-highlight {
            color: #22c55e !important;
        }

        /* Card grid vision */
        .vision-about-section .vision-card {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            padding: 15px 8px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .vision-about-section .vision-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .vision-about-section .vision-card-icon {
            font-size: 1.4rem;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .vision-about-section .vision-card-icon i {
            color: rgba(255, 255, 255, 0.95) !important;
        }

        .vision-about-section .vision-card-text {
            font-size: 0.72rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            line-height: 1.3;
        }

        /* Phone Mockup */
        .vision-about-section .phone-mockup-container {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            margin-left: -120px;
            margin-right: -120px;
            margin-bottom: -100px;
            /* Fait descendre le téléphone pour qu'il rentre en bas */
            z-index: 5;
        }

        .vision-about-section .phone-mockup-img {
            max-height: 85vh;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 30px 60px rgba(0, 0, 0, 0.4));
            animation: phone-float-vision 6s ease-in-out infinite;
        }

        @keyframes phone-float-vision {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-12px) rotate(0.3deg);
            }
        }

        /* Security Card */
        .vision-about-section .security-card {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 18px;
            padding: 18px 22px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
            transition: all 0.3s ease;
        }

        .vision-about-section .security-card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.22);
        }

        .vision-about-section .security-icon-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #103a83 !important;
            font-size: 1.3rem;
            border: 2px solid rgba(16, 58, 131, 0.15);
        }

        .vision-about-section .security-icon-circle i {
            color: #103a83 !important;
        }

        .vision-about-section .security-text {
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.85rem;
            margin: 0;
            line-height: 1.5;
            font-weight: 500;
        }

        /* Discover Button */
        .vision-about-section .btn-discover {
            background-color: #ffffff;
            color: #0f2c59;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 700;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            text-decoration: none !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
        }

        .vision-about-section .btn-discover:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.28);
            background-color: #f8fafc;
            color: #103a83 !important;
        }

        .vision-about-section .btn-discover i {
            font-size: 1rem;
            transition: transform 0.3s ease;
        }

        .vision-about-section .btn-discover:hover i {
            transform: translate(2px, -2px);
        }

        /* Bottom glow */
        .vision-bottom-glow {
            position: absolute;
            bottom: -150px;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 300px;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.3) 0%, rgba(34, 197, 94, 0) 70%);
            border-radius: 50%;
            filter: blur(50px);
            pointer-events: none;
            z-index: 0;
        }

        /* Custom spacing to look exactly like mockup layout */
        .px-custom-align {
            padding-left: 80px !important;
            padding-right: 80px !important;
        }

        @media (max-width: 991px) {
            .px-custom-align {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }
        }

        /* Responsiveness */
        @media (max-width: 991px) {
            .vision-about-section {
                padding: 60px 0;
                background-size: auto 100%, auto 100%, cover !important;
            }

            .vision-about-section .phone-mockup-container {
                margin: 30px 0;
                margin-left: 0;
                margin-right: 0;
            }

            .vision-about-section .phone-mockup-img {
                max-height: 480px;
            }

            .vision-about-section .section-title {
                font-size: 1.85rem;
            }
        }

        /* --- SERVICES ESSENTIELS SECTION --- */
        .services-essentiels-section {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #ffffff;
            position: relative;
            z-index: 1;
        }

        .services-blue-part {
            background: linear-gradient(135deg, #092552 0%, #103a83 100%);
            color: #ffffff;
            padding-top: 100px !important;
            padding-bottom: 100px !important;
            min-height: 480px;
            flex-grow: 1;
            display: flex;
            align-items: center;
            position: relative;
        }

        .services-essentiels-section .section-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 2.5rem;
            line-height: 1.25;
            color: #ffffff !important;
        }

        .services-essentiels-section .section-desc {
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 1rem;
            line-height: 1.6;
            font-weight: 400;
        }

        .services-blue-part .text-cyan-highlight {
            color: #3b82f6 !important;
        }

        .services-phone-container {
            position: absolute;
            right: 0px;
            top: 20px;
            bottom: -150px;
            /* Bleed down into the wave and white section */
            width: 45%;
            z-index: 10;
            pointer-events: none;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .services-phone-img {
            height: 100%;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 25px 50px rgba(0, 0, 0, 0.35));
        }

        /* Wave Separator */
        .services-wave-divider {
            width: 100%;
            height: 120px;
            background-color: #103a83;
            /* Matches the bottom of blue gradient */
            margin-bottom: -2px;
            /* Prevent gaps */
            position: relative;
            z-index: 2;
        }

        .services-wave-divider svg {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /* White Part & Essential Cards */
        .services-white-part {
            position: relative;
            z-index: 3;
            padding-top: 50px !important;
            padding-bottom: 80px !important;
        }

        .essential-service-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
        }

        .essential-icon-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 2px solid #1f4083;
            /* Fine blue border */
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1f4083;
            box-shadow: 0 12px 30px rgba(31, 64, 131, 0.08);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
        }

        .essential-service-item:hover .essential-icon-circle {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(31, 64, 131, 0.18);
            border-color: #22c55e;
            /* Green border on hover */
            color: #22c55e;
            background-color: #e6fdf5;
            /* Subtle green highlight background */
        }

        .essential-icon-circle svg {
            width: 42px;
            height: 42px;
            transition: transform 0.4s ease;
        }

        .essential-service-item:hover .essential-icon-circle svg {
            transform: scale(1.1);
        }

        .essential-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 0.95rem;
            color: #0b2545;
            line-height: 1.35;
        }

        .essential-title .text-secondary-highlight {
            color: #1f4083 !important;
        }

        .essential-line {
            width: 24px;
            height: 3px;
            background-color: #22c55e;
            margin: 10px 0;
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .essential-service-item:hover .essential-line {
            width: 40px;
        }

        .essential-desc {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
            margin: 0;
            max-width: 170px;
            line-height: 1.4;
        }

        /* 5 columns layout helper */
        @media (min-width: 992px) {
            .col-lg-2-4 {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }

        /* Spacing wrapper max-width */
        .max-width-550 {
            max-width: 550px;
        }

        /* Responsiveness for Services Section */
        @media (max-width: 991px) {
            .services-essentiels-section {
                min-height: auto;
            }

            .services-blue-part {
                padding-top: 60px !important;
                padding-bottom: 60px !important;
                text-align: center;
            }

            .services-blue-part .section-label-wrapper {
                justify-content: center;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .services-blue-part h2 {
                font-size: 2.2rem;
            }

            .services-blue-part p {
                margin: 0 auto !important;
            }

            .services-phone-container {
                position: relative;
                width: 100%;
                max-width: 320px;
                height: auto;
                margin: 40px auto 0 !important;
                margin-bottom: 0 !important;
                right: auto;
                bottom: auto;
            }

            .services-wave-divider {
                height: 60px;
            }

            .services-white-part {
                padding-top: 30px !important;
                padding-bottom: 50px !important;
            }

            .essential-icon-circle {
                width: 95px;
                height: 95px;
            }

            .essential-icon-circle svg {
                width: 32px;
                height: 32px;
            }

            .essential-title {
                font-size: 0.88rem;
            }

            .essential-desc {
                font-size: 0.76rem;
            }
        }
    </style>
</head>

<body>

    <div class="page-container">

        <!-- HEADER FIGMA -->
        <header class="figma-header d-flex justify-content-between align-items-center">
            <div class="header-logo d-flex align-items-center gap-2">
                <img src="{{ asset('assets/assets/img/plateau.png') }}" alt="Logo" class="logo-img">
            </div>
            <div class="header-buttons d-flex gap-3">
                <a href="{{ route('recherche.demande') }}" class="btn-figma-outline btn-figma-md">
                    <i class="bi bi-search"></i>
                    <span>Suivre ma demande</span>
                </a>
                <a href="{{ route('login') }}" class="btn-figma-primary btn-figma-md">
                    <i class="bi bi-person-fill"></i>
                    <span>Mon espace</span>
                </a>
            </div>
        </header>

        <!-- CONTENU PRINCIPAL -->
        <main class="main-content">
            <!-- Conteneur graphique unifié de droite (Cercle agrandi + Téléphone couplés) -->
            <div class="hero-right-container d-none d-lg-block">
                <div class="hero-circle-outline"></div>
                <div class="hero-circle-bg"></div>

                <div class="phone-mockup-wrapper">
                    <img src="{{ asset('assets/assets/img/telephone.png') }}"
                        onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1601784551146-12c4eaf7bfe5?q=80&w=800&auto=format&fit=crop';"
                        alt="Plateau App UI" class="hero-phone-img">
                </div>
            </div>

            <!-- Contenu Texte de gauche -->
            <div class="container-fluid px-0">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-12 d-flex flex-column justify-content-center hero-left-col">
                        <div class="content-wrapper">
                            <h1 class="hero-title-main">Etat civil</h1>
                            <h1 class="hero-title-sub">SIMPLIFIE</h1>

                            <p class="lead text-muted mb-4 hero-desc">
                                Ne perdez plus des heures en déplacementss inutiles. Obtenez vos actes d'état civil,
                                prenez rendez-vous et suivez vos demandes en quelques clics.
                            </p>

                            <!-- Puces avantages alignées horizontalement -->
                            <div class="d-flex flex-row flex-wrap gap-4 mb-5 advantages-row">
                                <div class="d-flex align-items-center gap-3 advantage-item">
                                    <div class="advantage-icon-box">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <span class="advantage-text">Sans<br>file d'attente</span>
                                </div>
                                <div class="d-flex align-items-center gap-3 advantage-item">
                                    <div class="advantage-icon-box">
                                        <i class="bi bi-clock"></i>
                                    </div>
                                    <span class="advantage-text">Disponible<br>24/24</span>
                                </div>
                                <div class="d-flex align-items-center gap-3 advantage-item">
                                    <div class="advantage-icon-box">
                                        <i class="bi bi-lightning-charge"></i>
                                    </div>
                                    <span class="advantage-text">En quelques<br>minutes</span>
                                </div>
                            </div>

                            <!-- Boutons de téléchargement style pilule premium -->
                            <div class="d-flex flex-row gap-3 mb-5 download-buttons">
                                <a href="#" class="store-btn">
                                    <i class="fa-brands fa-apple store-logo"></i>
                                    <div class="store-text">
                                        <span class="store-sub">Télécharger sur l'</span>
                                        <span class="store-main">App Store</span>
                                    </div>
                                </a>
                                <a href="#" class="store-btn">
                                    <i class="fa-brands fa-google-play store-logo"></i>
                                    <div class="store-text">
                                        <span class="store-sub">DISPONIBLE SUR</span>
                                        <span class="store-main">Google Play</span>
                                    </div>
                                </a>
                            </div>

                            <!-- Éléments de preuve sociale -->
                            <div class="d-flex align-items-center gap-4 user-proof mt-2">
                                <div class="avatar-group d-flex">
                                    <img src="https://i.pravatar.cc/100?img=12" alt="User"
                                        class="avatar-img border border-white">
                                    <img src="https://i.pravatar.cc/100?img=22" alt="User"
                                        class="avatar-img border border-white">
                                    <img src="https://i.pravatar.cc/100?img=32" alt="User"
                                        class="avatar-img border border-white">
                                    <img src="https://i.pravatar.cc/100?img=42" alt="User"
                                        class="avatar-img border border-white">
                                </div>
                                <div class="proof-text d-flex flex-column">
                                    <span class="proof-title fw-700">+12 000 habitants<br>utilisent déjà Plateau
                                        Apps</span>
                                    <div class="rating-stars text-xs d-flex align-items-center gap-1 mt-1">
                                        <i class="bi bi-star-fill text-green"></i>
                                        <i class="bi bi-star-fill text-green"></i>
                                        <i class="bi bi-star-fill text-green"></i>
                                        <i class="bi bi-star-fill text-green"></i>
                                        <i class="bi bi-star-fill text-green"></i>
                                        <span class="text-dark fw-850 ms-1">4.8/5</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Flèche de défilement vers le bas (Visual Anchor uniquement) -->
        <div class="scroll-down-arrow-container">
            <a href="#vision-about" class="scroll-down-arrow-btn text-decoration-none">
                <i class="bi bi-chevron-down scroll-arrow-icon"></i>
            </a>
        </div>

    </div>

    <!-- VISION & A PROPOS SECTION -->
    <section class="vision-about-section py-5 py-lg-6 position-relative overflow-hidden" id="vision-about">
        <div class="container-fluid px-custom-align py-4">
            <div class="row align-items-center g-5">
                <!-- Colonne Gauche: Vision -->
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="section-label-wrapper">
                        <span class="section-label text-uppercase">Notre vision</span>
                        <div class="label-accent-line"></div>
                    </div>
                    <h2 class="section-title mb-4">
                        Une administration <span class="text-green-highlight">humaine,</span> moderne et transparente.
                    </h2>
                    <p class="section-desc mb-3">
                        Au cœur du centre des affaires d'Abidjan, nous croyons que la technologie doit simplifier la
                        vie, pas la complexifier.
                    </p>
                    <p class="section-desc mb-3">
                        Notre vision est de redéfinir la relation entre la Mairie du Plateau et ses citoyens.
                    </p>
                    <p class="section-desc mb-4">
                        Finies les tracasseries administratives : nous bâtissons une administration transparente, rapide
                        et <span class="text-green-highlight">accessible à tous,</span> à chaque instant de votre vie.
                    </p>

                    <!-- Grille des piliers en ligne -->
                    <div class="row g-2 g-xl-3 mt-4">
                        <div class="col-lg-3 col-6">
                            <div class="vision-card">
                                <div class="vision-card-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                                <p class="vision-card-text">Proche de vous</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="vision-card">
                                <div class="vision-card-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>
                                <p class="vision-card-text">Transparente et sécurisée</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="vision-card">
                                <div class="vision-card-icon">
                                    <i class="bi bi-lightning-charge"></i>
                                </div>
                                <p class="vision-card-text">Rapide et efficace</p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="vision-card">
                                <div class="vision-card-icon">
                                    <i class="bi bi-heart"></i>
                                </div>
                                <p class="vision-card-text">A l'écoute de vos besoins</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne Centrale: Téléphone Mockup -->
                <div class="col-lg-3 text-center d-flex justify-content-center align-items-center" data-aos="fade-up"
                    data-aos-delay="200">
                    <div class="phone-mockup-container">
                        <img src="{{ asset('assets/assets/img/telephone2.png') }}" class="img-fluid phone-mockup-img"
                            alt="Plateau Apps Mobile Dashboard">
                    </div>
                </div>

                <!-- Colonne Droite: À Propos -->
                <div class="col-lg-4" data-aos="fade-left" data-aos-delay="300">
                    <div class="section-label-wrapper">
                        <span class="section-label text-uppercase">A propos</span>
                        <div class="label-accent-line"></div>
                    </div>
                    <h2 class="section-title mb-4">
                        Plateau Apps Votre mairie <span class="text-green-highlight">dans la poche.</span>
                    </h2>
                    <p class="section-desc mb-3">
                        Plateau Apps est le portail officiel conçu pour dématérialiser vos démarches citoyennes les plus
                        essentielles.
                    </p>
                    <p class="section-desc mb-4">
                        Qu'il s'agisse de célébrer une nouvelle vie (actes de naissance), de sceller une union (actes de
                        mariage) ou de planifier un rendez-vous crucial en mairie, notre plateforme sécurise et accélère
                        vos demandes pour vous faire gagner un temps précieux.
                    </p>

                    <!-- Carte Sécurité -->
                    <div class="security-card">
                        <div class="security-icon-circle">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <p class="security-text">
                            Vos données sont protégées à <span class="text-green-highlight fw-700">100%</span> et
                            traitées avec le plus haut niveau de sécurité.
                        </p>
                    </div>

                    <!-- Bouton Découvrir -->
                    <div class="text-start">
                        <a href="{{ route('service.demande') }}" class="btn-discover">
                            <span>Découvrir Plateau Apps</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Effet lumineux au bas center -->
        <div class="vision-bottom-glow"></div>
    </section>

    <!-- SERVICES ESSENTIELS SECTION -->
    <section
        class="services-essentiels-section d-flex flex-column justify-content-between position-relative overflow-hidden"
        id="services-essentiels">
        <!-- Partie supérieure bleue -->
        <div class="services-blue-part container-fluid px-custom-align pt-5 pb-5">
            <div class="row align-items-center g-5">
                <!-- Gauche: Textes -->
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="section-label-wrapper">
                        <span class="section-label text-uppercase text-white-50">Plateau Apps</span>
                        <div class="label-accent-line"></div>
                    </div>
                    <h2 class="section-title mb-4">
                        Des services pensés<br>
                        Pour vous <span class="text-cyan-highlight">simplifier la vie.</span>
                    </h2>
                    <p class="section-desc mb-0" style="max-width: 550px;">
                        Plateau Apps centralise tous vos services essentiels pour vous offrir une expérience rapide,
                        sécurisée et accessible à tous.
                    </p>
                </div>
                <!-- Droite: Phone mockup -->
                <div class="col-lg-6 text-end" data-aos="fade-left" data-aos-delay="200">
                    <div class="services-phone-container">
                        <img src="{{ asset('assets/assets/img/telephone2.png') }}" class="img-fluid services-phone-img"
                            alt="Plateau Apps Mobile UI">
                    </div>
                </div>
            </div>
        </div>

        <!-- Wave divider SVG -->
        <div class="services-wave-divider">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M0 0C240 80 480 120 720 120C960 120 1200 80 1440 0V120H0V0Z" fill="#ffffff" />
            </svg>
        </div>

        <!-- Partie inférieure blanche -->
        <div class="services-white-part container-fluid px-custom-align bg-white pt-4 pb-5">
            <div class="row g-4 justify-content-center text-center">
                <!-- Item 1: Traitement Express -->
                <div class="col-lg-2-4 col-md-4 col-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="essential-service-item">
                        <div class="essential-icon-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                            </svg>
                        </div>
                        <h5 class="essential-title mt-3">Traitement<br><span
                                class="text-secondary-highlight">Express</span></h5>
                        <div class="essential-line"></div>
                        <p class="essential-desc">Vos demandes traitées en un temps record.</p>
                    </div>
                </div>
                <!-- Item 2: Zéro Déplacement -->
                <div class="col-lg-2-4 col-md-4 col-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="essential-service-item">
                        <div class="essential-icon-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                        </div>
                        <h5 class="essential-title mt-3">Zéro<br><span
                                class="text-secondary-highlight">Déplacement</span></h5>
                        <div class="essential-line"></div>
                        <p class="essential-desc">Faites vos démarches en ligne sans bouger de chez vous.</p>
                    </div>
                </div>
                <!-- Item 3: Actes Certifiés -->
                <div class="col-lg-2-4 col-md-4 col-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="essential-service-item">
                        <div class="essential-icon-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <circle cx="12" cy="14" r="3"></circle>
                                <path d="M12 17v4l2-1 2 1v-4"></path>
                            </svg>
                        </div>
                        <h5 class="essential-title mt-3">Actes<br><span
                                class="text-secondary-highlight">Certifiés</span></h5>
                        <div class="essential-line"></div>
                        <p class="essential-desc">Des documents officiels signés et certifiés conformes.</p>
                    </div>
                </div>
                <!-- Item 4: Paiement Mobile -->
                <div class="col-lg-2-4 col-md-4 col-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="essential-service-item">
                        <div class="essential-icon-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                <line x1="12" y1="18" x2="12.01" y2="18"></line>
                                <path d="M10 11l2 2 4-4"></path>
                            </svg>
                        </div>
                        <h5 class="essential-title mt-3">Paiement<br><span
                                class="text-secondary-highlight">Mobile</span></h5>
                        <div class="essential-line"></div>
                        <p class="essential-desc">Payez facilement et en toute sécurité via Mobile Money.</p>
                    </div>
                </div>
                <!-- Item 5: Éco-Citoyen -->
                <div class="col-lg-2-4 col-md-4 col-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="essential-service-item">
                        <div class="essential-icon-circle">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="10" r="8"></circle>
                                <path d="M12 2v2"></path>
                                <path d="M12 18v2"></path>
                                <path
                                    d="M12 10a15.3 15.3 0 0 1 2 4 15.3 15.3 0 0 1-2 4 15.3 15.3 0 0 1-2-4 15.3 15.3 0 0 1 2-4z">
                                </path>
                                <path d="M8 20h8"></path>
                            </svg>
                        </div>
                        <h5 class="essential-title mt-3">Éco-<br><span class="text-secondary-highlight">Citoyen</span>
                        </h5>
                        <div class="essential-line"></div>
                        <p class="essential-desc">Agissons ensemble pour préserver l'environnement en évitant le papier.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap Bundle JS -->
    <script src="{{ asset('assets/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.querySelector('.scroll-down-arrow-btn').addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector('#vision-about').scrollIntoView({
                behavior: 'smooth'
            });
        });
    </script>
</body>

</html>