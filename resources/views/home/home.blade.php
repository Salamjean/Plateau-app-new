@extends('home.layouts.main')

@section('content')
    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container-fluid hero-container px-10percent">
            <div class="row align-items-center">
                <!-- Colonne Gauche: Textes et Boutons -->
                <div class="col-lg-7" data-aos="fade-right">
                    <div class="badge-premium mb-4">
                        <span class="badge rounded-pill badge-maquette px-3 py-2 fw-600 shadow-sm">
                            <i class="bi bi-sparkles me-1 text-warning"></i> Portail officiel de la Mairie du Plateau
                        </span>
                    </div>
                    <h1 class="display-3 fw-900 text-white mb-4 hero-title" style="line-height: 1.1;">
                        Vos démarches<br>
                        administratives, depuis chez<br>
                        vous
                    </h1>
                    <p class="lead text-white text-opacity-75 mb-5 fs-5">
                        Actes civils, rendez-vous et documents officiels disponibles en ligne<br>
                        24h/24 — sans file d'attente.
                    </p>

                    <!-- Barre de recherche maquette -->
                    <div class="search-box-maquette mb-5" data-aos="fade-up" data-aos-delay="200">
                        <form action="{{ route('recherche.demande') }}" method="POST"
                            class="d-flex align-items-center p-2 bg-white rounded-pill shadow-lg">
                            @csrf
                            <div class="d-flex align-items-center flex-grow-1 px-3">
                                <i class="bi bi-search text-muted fs-5 me-2"></i>
                                <input type="text" name="reference_naissance" class="form-control border-0 fw-600"
                                    placeholder="Rechercher un service..." required style="box-shadow: none;">
                            </div>
                            <button type="submit" class="btn btn-search-maquette rounded-pill px-4 py-2 fw-700">Rechercher</button>
                        </form>
                    </div>

                    <!-- Boutons raccourcis maquette -->
                    <div class="d-flex flex-wrap gap-2 gap-md-3 mb-4">
                        <a href="{{ route('home.birth') }}" class="btn btn-shortcut-maquette rounded-pill px-4 py-2 fw-700 transition d-flex align-items-center gap-2">
                            <i class="bi bi-person-plus fs-5"></i>
                            <span>Acte de naissance</span>
                        </a>
                        <a href="{{ route('home.wedding') }}" class="btn btn-shortcut-maquette rounded-pill px-4 py-2 fw-700 transition d-flex align-items-center gap-2">
                            <i class="bi bi-heart fs-5"></i>
                            <span>Acte de mariage</span>
                        </a>
                        <a href="{{ route('home.death') }}" class="btn btn-shortcut-maquette rounded-pill px-4 py-2 fw-700 transition d-flex align-items-center gap-2">
                            <i class="bi bi-person-dash fs-5"></i>
                            <span>Acte de décès</span>
                        </a>
                    </div>
                </div>

                <!-- Colonne Droite: Image du Maire -->
                <div class="col-lg-5 text-center text-lg-end d-none d-lg-block" data-aos="fade-left" data-aos-delay="300">
                    <div class="hero-mayor-wrapper">
                        <img src="{{ asset('assets/assets/img/maire.avif') }}"
                            onerror="this.onerror=null; this.src='{{ asset('assets/assets/img/maire.jpeg') }}';"
                            class="img-fluid hero-mayor-img" alt="Maire du Plateau">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STEPS SECTION -->
    <section class="services-section py-5">
        <div class="container py-4">
            <div class="text-center mb-5" data-aos="fade-up">
                <h6 class="text-primary fw-800 text-uppercase tracking-wider mb-2">Simplicité & Rapidité</h6>
                <h2 class="display-5 fw-900 mb-3" style="color: #1f4083;">Comment faire votre demande ?</h2>
                <p class="lead text-muted mx-auto mb-0" style="max-width: 600px;">
                    Obtenez vos actes civils officiels en suivant ces 4 étapes simples sans quitter votre domicile.
                </p>
            </div>
            
            <div class="row g-4">
                <!-- Étape 1 -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-card p-4 rounded-5 border bg-white shadow-sm h-100 transition relative">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="step-icon-wrapper text-primary bg-primary bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-file-earmark-text fs-2"></i>
                            </div>
                            <div class="step-number fw-900 text-secondary mb-0">01</div>
                        </div>
                        <h4 class="fw-800 mb-3" style="color: #1f4083;">Choix de l'Acte</h4>
                        <p class="text-muted small mb-0">
                            Sélectionnez le document d'état civil dont vous avez besoin : Naissance, Mariage ou Décès en cliquant sur le bouton correspondant ci-dessus.
                        </p>
                    </div>
                </div>

                <!-- Étape 2 -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-card p-4 rounded-5 border bg-white shadow-sm h-100 transition relative">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="step-icon-wrapper text-primary bg-primary bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-pencil-square fs-2"></i>
                            </div>
                            <div class="step-number fw-900 text-secondary mb-0">02</div>
                        </div>
                        <h4 class="fw-800 mb-3" style="color: #1f4083;">Saisie des Infos</h4>
                        <p class="text-muted small mb-0">
                            Remplissez le formulaire en ligne de manière confidentielle avec les détails requis de l'acte et vos pièces justificatives.
                        </p>
                    </div>
                </div>

                <!-- Étape 3 -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-card p-4 rounded-5 border bg-white shadow-sm h-100 transition relative">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="step-icon-wrapper text-primary bg-primary bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-credit-card-2-front fs-2"></i>
                            </div>
                            <div class="step-number fw-900 text-secondary mb-0">03</div>
                        </div>
                        <h4 class="fw-800 mb-3" style="color: #1f4083;">Paiement en ligne</h4>
                        <p class="text-muted small mb-0">
                            Réglez de façon sécurisée par Mobile Money (Wave, Orange Money, MTN, Moov) ou par carte bancaire.
                        </p>
                    </div>
                </div>

                <!-- Étape 4 -->
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="step-card p-4 rounded-5 border bg-white shadow-sm h-100 transition relative">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="step-icon-wrapper text-primary bg-primary bg-opacity-10 rounded-4 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="bi bi-truck fs-2"></i>
                            </div>
                            <div class="step-number fw-900 text-secondary mb-0">04</div>
                        </div>
                        <h4 class="fw-800 mb-3" style="color: #1f4083;">Livraison rapide</h4>
                        <p class="text-muted small mb-0">
                            Suivez en temps réel et recevez votre extrait d'acte à domicile ou retirez-le directement en mairie.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* --- FONT WEIGHTS --- */
        .fw-600 {
            font-weight: 600;
        }

        .fw-700 {
            font-weight: 700;
        }

        .fw-800 {
            font-weight: 800;
        }

        .fw-900 {
            font-weight: 900;
        }

        .btn-primary {
            background-color: #1f4083;
            border-color: #1f4083;
        }

        .btn-primary:hover {
            background-color: #162e5f;
            border-color: #162e5f;
        }

        .text-primary {
            color: #1f4083 !important;
        }

        .bg-primary {
            background-color: #1f4083 !important;
        }

        /* --- HERO SECTION --- */
        .hero-section {
            padding: 160px 0 100px;
            background: #f8fafc;
            overflow: hidden;
        }

        .hero-container {
            position: relative;
        }

        .icon-circle-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }

        .hero-image-stack {
            position: relative;
            padding-right: 40px;
        }

        .main-img {
            box-shadow: 0 50px 100px -20px rgba(15, 23, 42, 0.15);
            border: 10px solid white;
        }

        .floating-card {
            position: absolute;
            bottom: -30px;
            right: 0;
            min-width: 240px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .backdrop-blur {
            backdrop-filter: blur(10px);
        }

        /* --- SERVICE CARDS --- */
        .services-section {
            background: white;
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }

        /* --- SCROLLBAR HIDING --- */
        .scrollbar-hidden::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hidden {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* --- HERO SECTION MAQUETTE --- */
        .hero-section {
            padding: 170px 0 100px;
            background: radial-gradient(circle at 85% 50%, #1e4ea5 0%, #0d2c67 100%) !important; /* Dégradé radial avec halo lumineux derrière le maire */
            position: relative;
            overflow: hidden;
        }

        .badge-maquette {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
            font-size: 0.9rem !important;
        }

        .hero-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -1px;
            font-size: 3.8rem !important;
        }

        .search-box-maquette {
            max-width: 520px;
        }

        .search-box-maquette form {
            background-color: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .btn-search-maquette {
            background-color: #103a83 !important;
            color: #ffffff !important;
            border: none !important;
            transition: all 0.3s ease;
        }

        .btn-search-maquette:hover {
            background-color: #0d2e69 !important;
            transform: scale(1.02);
        }

        .btn-shortcut-maquette {
            background-color: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-decoration: none !important;
        }

        .btn-shortcut-maquette:hover {
            background-color: rgba(255, 255, 255, 0.2) !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
        }

        .hero-mayor-wrapper {
            position: relative;
            margin-bottom: 0px; /* Aligne l'image encore plus haut, entièrement dans le hero */
        }

        .hero-mayor-img {
            max-height: 540px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 15px 30px rgba(0, 0, 0, 0.25));
        }

        /* --- STEP CARDS --- */
        .step-card {
            position: relative;
            background: white;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.02) !important;
            border-radius: 24px !important;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .step-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 45px rgba(31, 64, 131, 0.08) !important;
            border-color: rgba(31, 64, 131, 0.15) !important;
        }

        .step-number {
            font-size: 2.2rem;
            line-height: 1;
            font-weight: 900;
            color: #1f4083 !important;
            opacity: 0.12;
            transition: all 0.3s ease;
        }

        .step-card:hover .step-number {
            opacity: 0.3;
            transform: scale(1.05);
        }

        .step-icon-wrapper {
            transition: all 0.3s ease !important;
            background-color: rgba(16, 58, 131, 0.1) !important; /* Fond bleu clair transparent */
            color: #103a83 !important; /* Force la couleur bleu roi hors survol */
            opacity: 1 !important;
            visibility: visible !important;
            display: flex !important;
        }

        .step-icon-wrapper i {
            color: #103a83 !important; /* Force l'icône en bleu roi hors survol */
            opacity: 1 !important;
            visibility: visible !important;
            display: inline-block !important;
        }

        .step-card:hover .step-icon-wrapper {
            background-color: #103a83 !important;
            color: #ffffff !important;
            transform: rotate(5deg) !important;
        }

        .step-card:hover .step-icon-wrapper i {
            color: #ffffff !important; /* Force l'icône en blanc au survol */
        }

        .transition {
            transition: all 0.4s ease;
        }

        @media (max-width: 991px) {
            .hero-section {
                text-align: center;
                padding-top: 140px;
                padding-bottom: 60px;
            }

            .hero-title {
                font-size: 2.5rem !important;
            }

            .search-box-maquette {
                margin: 0 auto 30px !important;
            }

            .btn-shortcut-maquette {
                font-size: 0.85rem !important;
                padding: 8px 16px !important;
                justify-content: center;
                width: 100%;
            }

            .badge-premium {
                display: flex;
                justify-content: center;
            }

            .services-section {
                margin-top: 20px;
            }
        }

        @media (max-width: 768px) {
            .step-card {
                padding: 24px !important;
                border-radius: 20px !important;
            }

            .step-icon-wrapper {
                width: 50px !important;
                height: 50px !important;
            }

            .step-icon-wrapper i {
                font-size: 1.5rem !important;
            }
        }
    </style>
@endpush
