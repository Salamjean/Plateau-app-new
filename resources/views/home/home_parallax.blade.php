<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateau Smart City - État Civil Simplifié</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" type="image/x-icon">

    <!-- CSS Vendors -->
    <link href="{{ asset('assets/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* DESIGN STRICTEMENT IDENTIQUE AU MODÈLE FIGMA */
        html, body {
            height: 100% !important;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #e5eef9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #0b2545;
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
            padding: 0px 64px;
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
            margin-top: -220px; /* Aligné dynamiquement sur la même ligne que le contenu textuel de gauche */
            width: 1000px;
            height: 1000px;
            pointer-events: none;
            z-index: 1;
        }

        .hero-circle-bg {
            position: absolute;
            width: 920px; /* AGRANDI: Pour donner plus de marge autour du téléphone */
            height: 920px; /* AGRANDI */
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
            width: 1140px; /* AGRANDI: Pour suivre la taille augmentée du cercle principal */
            height: 1140px; /* AGRANDI */
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
            left: 28%; /* Légèrement ajusté pour un cadrage parfait par rapport au nouveau grand cercle */
            top: 28%; 
            transform: translate(-50%, -50%);
            width: 1500px; 
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
            html, body {
                overflow-y: auto !important; /* Autorise le scroll uniquement sur mobile */
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
                    <img src="{{ asset('assets/assets/img/telephone.png') }}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1601784551146-12c4eaf7bfe5?q=80&w=800&auto=format&fit=crop';" alt="Plateau App UI" class="hero-phone-img">
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
                                Ne perdez plus des heures en déplacementss inutiles. Obtenez vos actes d'état civil, prenez rendez-vous et suivez vos demandes en quelques clics.
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
                                    <img src="https://i.pravatar.cc/100?img=12" alt="User" class="avatar-img border border-white">
                                    <img src="https://i.pravatar.cc/100?img=22" alt="User" class="avatar-img border border-white">
                                    <img src="https://i.pravatar.cc/100?img=32" alt="User" class="avatar-img border border-white">
                                    <img src="https://i.pravatar.cc/100?img=42" alt="User" class="avatar-img border border-white">
                                </div>
                                <div class="proof-text d-flex flex-column">
                                    <span class="proof-title fw-700">+12 000 habitants<br>utilisent déjà Plateau Apps</span>
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
            <div class="scroll-down-arrow-btn">
                <i class="bi bi-chevron-down scroll-arrow-icon"></i>
            </div>
        </div>

    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="{{ asset('assets/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
