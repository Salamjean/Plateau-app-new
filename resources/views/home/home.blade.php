@extends('home.layouts.main')

@section('content')
    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container-fluid hero-container px-10percent">
            <div class="row align-items-center">
                <!-- Colonne Gauche: Textes et Boutons -->
                <div class="col-lg-7" data-aos="fade-right">
                    {{-- <div class="badge-premium mb-4">
                        <span class="badge rounded-pill badge-maquette px-3 py-2 fw-600 shadow-sm">
                            <i class="bi bi-sparkles me-1 text-warning"></i> Portail officiel de la Mairie du Plateau
                        </span>
                    </div> --}}
                    <h1 class="display-3 fw-900 mb-4 hero-title" style="line-height: 1.1;">
                        La marie du Plateau,<br>
                        désormais dans<br>
                        <span class="text-green-highlight">votre poche.</span>
                    </h1>
                    <p class="lead mb-5 fs-5 hero-subtitle">
                        Actes civils, rendez-vous et services municipaux<br>
                        accécibles partout, à tout moment.
                    </p>

                    <!-- Barre de recherche maquette -->
                    {{-- <div class="search-box-maquette mb-5" data-aos="fade-up" data-aos-delay="200">
                        <form action="{{ route('recherche.demande') }}" method="POST"
                            class="d-flex align-items-center p-2 bg-white rounded-pill shadow-lg">
                            @csrf
                            <div class="d-flex align-items-center flex-grow-1 px-3">
                                <i class="bi bi-search text-muted fs-5 me-2"></i>
                                <input type="text" name="reference_naissance" class="form-control border-0 fw-600"
                                    placeholder="Quelle démarche souhaitez-vous effectuer ?" required style="box-shadow: none;">
                            </div>
                            <button type="submit" class="btn btn-search-maquette rounded-pill px-4 py-2 fw-700">Rechercher</button>
                        </form>
                    </div> --}}

                    <!-- Boutons raccourcis maquette -->
                    <div class="d-flex flex-wrap gap-2 gap-md-3 mb-4">
                        <a href="{{ route('home.birth') }}" class="btn btn-shortcut-maquette rounded-pill px-4 py-2 fw-700 transition d-flex align-items-center gap-2">
                            <i class="bi bi-person-circle fs-5"></i>
                            <span>Acte de naissance</span>
                        </a>
                        <a href="{{ route('home.wedding') }}" class="btn btn-shortcut-maquette rounded-pill px-4 py-2 fw-700 transition d-flex align-items-center gap-2">
                            <i class="bi bi-infinity fs-5"></i>
                            <span>Acte de mariage</span>
                        </a>
                        <a href="{{ route('home.death') }}" class="btn btn-shortcut-maquette rounded-pill px-4 py-2 fw-700 transition d-flex align-items-center gap-2">
                            <i class="bi bi-feather fs-5"></i>
                            <span>Acte de décès</span>
                        </a>
                    </div>
                </div>

                <!-- Colonne Droite: Cartes Flottantes -->
                <div class="col-lg-5 d-none d-lg-block" data-aos="fade-left" data-aos-delay="300" style="min-height: 480px; position: relative;">
                    <!-- Card 1: Acte de naissance -->
                    <div class="hero-floating-card card-1 shadow-lg">
                        <div class="card-body-custom">
                            <div class="card-header-section d-flex align-items-center gap-3">
                                <div class="icon-box icon-blue">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                </div>
                                <div>
                                    <h6 class="card-title-text mb-0">Acte de naissance</h6>
                                    <span class="card-status-text text-success-custom">Disponible</span>
                                </div>
                            </div>
                            <div class="card-footer-section">
                                <button class="btn btn-download-card w-100 d-flex align-items-center justify-content-center gap-2">
                                    <span>Télécharger</span>
                                    <i class="bi bi-download"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Rendez-vous confirmé -->
                    <div class="hero-floating-card card-2 shadow-lg">
                        <div class="card-body-custom d-flex align-items-center gap-3">
                            <div class="icon-box icon-darkblue">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="card-title-text mb-0">Rendez-vous confirmé</h6>
                                <span class="card-date-text text-muted">Jeu 23 mai 2024 à 10:00</span>
                            </div>
                            <div class="check-badge">
                                <i class="bi bi-check"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Acte de mariage -->
                    <div class="hero-floating-card card-3 shadow-lg">
                        <div class="card-body-custom">
                            <div class="card-header-section d-flex align-items-center gap-3">
                                <div class="icon-box icon-green">
                                    <i class="bi bi-heart-fill"></i>
                                </div>
                                <div>
                                    <h6 class="card-title-text mb-0">Acte de mariage</h6>
                                    <span class="card-status-text text-success-custom">Disponible</span>
                                </div>
                            </div>
                            <div class="card-footer-section">
                                <button class="btn btn-download-card w-100 d-flex align-items-center justify-content-center gap-2">
                                    <span>Télécharger</span>
                                    <i class="bi bi-check text-success-custom"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION SUIVRE & DEMARCHES URGENTES -->
    <section class="tracking-urgent-section py-5" style="background-color: #ffffff; margin-top: -20px; position: relative; z-index: 10;">
        <div class="container-fluid px-10percent">
            <div class="row g-4">
                <!-- Gauche: Suivre ma demande -->
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="tracking-card p-4 p-md-5 rounded-5" style="background-color: #f1f5f9;">
                        <h3 class="fw-800 mb-3" style="color: #0f2c59; font-size: 1.8rem; font-family: 'Plus Jakarta Sans', sans-serif;">Suivre ma demande</h3>
                        <p class="text-muted mb-5" style="font-size: 0.95rem; line-height: 1.6;">
                            Consultez l'état d'avancement de votre dossier en temps réel avec votre numéro de référence.
                        </p>

                        <form action="{{ route('recherche.demande') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-800 text-uppercase small tracking-wider mb-2" style="color: #1e293b; font-size: 0.75rem;">Numéro de référence</label>
                                <input type="text" name="reference_naissance" class="form-control rounded-4 border-0 py-3 px-4" 
                                    placeholder="ex: AN012345P8956" required 
                                    style="background-color: #e2e8f0; font-weight: 600; color: #1e293b; font-size: 0.95rem; box-shadow: none;">
                            </div>
                            <button type="submit" class="btn w-100 py-3 rounded-4 fw-700 d-flex align-items-center justify-content-center gap-2" 
                                style="background-color: #103a83; color: #ffffff; border: none; font-size: 0.95rem; transition: all 0.3s ease;">
                                <span>Suivre mon dossier</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Droite: Démarches urgentes -->
                <div class="col-lg-7" data-aos="fade-left" data-aos-delay="200">
                    <h3 class="fw-800 mb-4" style="color: #0f2c59; font-size: 1.8rem; font-family: 'Plus Jakarta Sans', sans-serif;">Démarches urgentes</h3>

                    <div class="d-flex flex-column gap-3">
                        <!-- Demande d'extrait de naissance -->
                        <a href="{{ route('home.birth') }}" class="urgent-demarche-card d-flex align-items-center justify-content-between p-3 rounded-4 border bg-white text-decoration-none transition">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-container-urgent blue-bg">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div>
                                    <h6 class="fw-800 mb-1" style="color: #0f2c59; font-size: 0.98rem; font-family: 'Plus Jakarta Sans', sans-serif;">Demande d'extrait de naissance</h6>
                                    <small class="text-muted d-flex align-items-center gap-2" style="font-size: 0.8rem; font-weight: 500;">
                                        <span class="dot-status green-dot"></span> Délai de traitement: 3h
                                    </small>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted fs-5"></i>
                        </a>

                        <!-- Célébration de mariage civil -->
                        <a href="{{ route('home.wedding') }}" class="urgent-demarche-card d-flex align-items-center justify-content-between p-3 rounded-4 border bg-white text-decoration-none transition">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-container-urgent orange-bg">
                                    <i class="bi bi-heart"></i>
                                </div>
                                <div>
                                    <h6 class="fw-800 mb-1" style="color: #0f2c59; font-size: 0.98rem; font-family: 'Plus Jakarta Sans', sans-serif;">Célébration de mariage civil</h6>
                                    <small class="text-muted d-flex align-items-center gap-2" style="font-size: 0.8rem; font-weight: 500;">
                                        <span class="dot-status orange-dot"></span> Sur rendez-vous uniquement
                                    </small>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted fs-5"></i>
                        </a>

                        <!-- Légalisation de documents -->
                        <a href="{{ route('service.demande') }}" class="urgent-demarche-card d-flex align-items-center justify-content-between p-3 rounded-4 border bg-white text-decoration-none transition">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-container-urgent purple-bg">
                                    <i class="fa-solid fa-gavel"></i>
                                </div>
                                <div>
                                    <h6 class="fw-800 mb-1" style="color: #0f2c59; font-size: 0.98rem; font-family: 'Plus Jakarta Sans', sans-serif;">Légalisation de documents</h6>
                                    <small class="text-muted d-flex align-items-center gap-2" style="font-size: 0.8rem; font-weight: 500;">
                                        <span class="dot-status green-dot"></span> Sur rendez-vous uniquement
                                    </small>
                                </div>
                            </div>
                            <i class="bi bi-chevron-right text-muted fs-5"></i>
                        </a>
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
            padding: 200px 0 140px;
            background-image: url('{{ asset("assets/assets/img/backhero.png") }}') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            position: relative;
            overflow: hidden;
        }

        .badge-maquette {
            background-color: rgba(15, 23, 42, 0.45) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            font-size: 0.85rem !important;
            backdrop-filter: blur(4px);
        }

        .hero-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -1.5px;
            font-size: 3.8rem !important;
            color: #0f2c59 !important; /* Couleur marine */
        }

        .hero-subtitle {
            color: #475569 !important; /* Couleur ardoise sombre */
            font-weight: 500;
        }

        .text-green-highlight {
            color: #22c55e !important; /* Vert moderne et vif */
        }

        .search-box-maquette {
            max-width: 600px;
        }

        .search-box-maquette form {
            background-color: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
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
            background-color: rgba(15, 23, 42, 0.4) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            font-size: 0.9rem !important;
            backdrop-filter: blur(4px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            text-decoration: none !important;
        }

        .btn-shortcut-maquette:hover {
            background-color: #103a83 !important;
            border-color: #103a83 !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 58, 131, 0.25) !important;
        }

        /* --- FLOATING CARDS --- */
        .hero-floating-card {
            position: absolute;
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            padding: 18px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .hero-floating-card:hover {
            transform: scale(1.03) translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15) !important;
        }

        /* Positions et animations des cartes */
        .card-1 {
            width: 260px;
            left: 20px;
            top: 140px;
            z-index: 10;
            animation: float-1 6s ease-in-out infinite;
        }

        .card-2 {
            width: 290px;
            right: 0px;
            top: 70px;
            z-index: 11;
            animation: float-2 7s ease-in-out infinite;
        }

        .card-3 {
            width: 240px;
            right: 40px;
            bottom: 60px;
            z-index: 9;
            animation: float-3 8s ease-in-out infinite;
        }

        @keyframes float-1 {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }

        @keyframes float-2 {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes float-3 {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }

        /* Structure interne des cartes */
        .card-body-custom {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .card-2 .card-body-custom {
            flex-direction: row;
            position: relative;
        }

        .card-header-section {
            display: flex;
            align-items: center;
        }

        .icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .icon-blue {
            background-color: rgba(31, 64, 131, 0.1);
            color: #1f4083;
        }

        .icon-darkblue {
            background-color: rgba(16, 58, 131, 0.1);
            color: #103a83;
        }

        .icon-green {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .card-title-text {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: #0d2c67;
        }

        .card-status-text {
            font-size: 0.8rem;
            font-weight: 600;
        }

        .card-date-text {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .check-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #2ecc71;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.8rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border: 2px solid #ffffff;
        }

        .btn-download-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #475569;
            transition: all 0.2s ease;
        }

        .btn-download-card:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }

        .text-success-custom {
            color: #28a745 !important;
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
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.8) 0%, rgba(255, 255, 255, 0.45) 100%), url('{{ asset("assets/assets/img/backhero.png") }}') !important;
                background-size: cover !important;
                background-position: 78% center !important; /* Centre l'image sur l'homme */
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

        /* --- TRACKING & URGENT DEMARCHES --- */
        .tracking-card {
            border: 1px solid rgba(0, 0, 0, 0.03) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02) !important;
            border-radius: 28px !important;
        }

        .tracking-card .btn:hover {
            background-color: #0d2e69 !important;
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(16, 58, 131, 0.2);
        }

        .urgent-demarche-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .urgent-demarche-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(16, 58, 131, 0.06) !important;
            border-color: rgba(16, 58, 131, 0.15) !important;
        }

        .icon-container-urgent {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .icon-container-urgent.blue-bg {
            background-color: rgba(31, 64, 131, 0.08);
            color: #1f4083;
        }

        .icon-container-urgent.orange-bg {
            background-color: rgba(249, 115, 22, 0.08);
            color: #ea580c;
        }

        .icon-container-urgent.purple-bg {
            background-color: rgba(168, 85, 247, 0.08);
            color: #9333ea;
        }

        .dot-status {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .green-dot {
            background-color: #2ecc71;
        }

        .orange-dot {
            background-color: #f97316;
        }
    </style>
@endpush
