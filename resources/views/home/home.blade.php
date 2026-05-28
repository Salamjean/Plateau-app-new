@extends('home.layouts.main')

@section('content')
    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="container hero-container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="badge-premium mb-3">
                        <span class="badge rounded-pill bg-white text-primary px-3 py-2 fw-800 shadow-sm">
                            <i class="bi bi-patch-check-fill me-1"></i> PORTAIL OFFICIEL
                        </span>
                    </div>
                    <h1 class="display-1 fw-900 mb-4" style="line-height: 1.1; color: #1f4083;">
                        Vos Actes Civils<br>
                        <span class="text-secondary">Simplifiés.</span>
                    </h1>
                    <p class="lead text-muted mb-5 fs-5">
                        La Mairie du Plateau se digitalise pour vous. Commandez, suivez et recevez vos documents
                        administratifs sans vous déplacer.
                    </p>

                    <div class="search-box-wrapper mb-5" data-aos="fade-up" data-aos-delay="200">
                        <form action="{{ route('recherche.demande') }}" method="POST"
                            class="d-flex p-2 bg-white rounded-4 shadow-lg border">
                            @csrf
                            <input type="text" name="reference_naissance" class="form-control border-0 px-4 py-3 fw-600"
                                placeholder="Entrez votre numéro de suivi..." required>
                            <button type="submit" class="btn btn-primary rounded-3 px-5 fw-800">Suivre</button>
                        </form>
                    </div>

                    <div class="d-flex flex-wrap gap-4 align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-circle-sm bg-success text-white">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <span class="fw-700 text-dark small">Service 24h/7j</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-circle-sm bg-primary text-white">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <span class="fw-700 text-dark small">Paiement Sécurisé</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6" data-aos="zoom-in" data-aos-delay="300">
                    <div class="hero-image-stack">
                        <img src="{{ asset('assets/assets/img/Plateau-immeuble.jpg') }}"
                            class="img-fluid rounded-5 main-img" alt="Mairie">
                        <div class="floating-card p-4 rounded-4 shadow-lg bg-white bg-opacity-75 backdrop-blur">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="stats-icon bg-warning text-white rounded-3 p-2">
                                    <i class="bi bi-people-fill fs-3"></i>
                                </div>
                                <div>
                                    <h4 class="fw-900 mb-0">+50,000</h4>
                                    <p class="text-muted small mb-0">Demandes traitées</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES QUICK START -->
    <section class="services-section py-5">
        <div class="container">
            <div class="row g-4">
                <!-- Naissance -->
                <div class="col-md-4" data-aos="fade-up">
                    <div class="service-card-new p-5 text-center transition">
                        <div class="icon-wrapper mb-4 mx-auto">
                            <i class="bi bi-journal-plus fs-1" style="color: #1f4083;"></i>
                        </div>
                        <h4 class="fw-900 mb-3" style="color: #1f4083;">Naissance</h4>
                        <p class="text-muted mb-4">Commandez vos extraits d'acte de naissance ou copies intégrales en ligne.
                        </p>
                        <a href="{{ route('home.birth') }}" class="btn px-4 rounded-pill fw-800"
                            style="border-color: #1f4083; color: white; background-color: #1f4083;">FAIRE UNE DEMANDE</a>
                    </div>
                </div>

                <!-- Mariage -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="service-card-new p-5 text-center featured transition" style="background-color: #1f4083;">
                        <div class="icon-wrapper mb-4 mx-auto">
                            <i class="bi bi-heart-fill text-white fs-1"></i>
                        </div>
                        <h4 class="fw-900 mb-3 text-white">Mariage</h4>
                        <p class="text-white text-opacity-75 mb-4">Réservez votre date de célébration et demandez vos
                            certificats.</p>
                        <a href="{{ route('home.wedding') }}" class="btn btn-light px-4 rounded-pill fw-800"
                            style="color: #1f4083;">FAIRE UNE DEMANDE</a>
                    </div>
                </div>

                <!-- Décès -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="service-card-new p-5 text-center transition">
                        <div class="icon-wrapper mb-4 mx-auto">
                            <i class="bi bi-file-earmark-diff fs-1" style="color: #1f4083;"></i>
                        </div>
                        <h4 class="fw-900 mb-3" style="color: #1f4083;">Décès</h4>
                        <p class="text-muted mb-4">Démarches simplifiées pour l'obtention des actes de décès officiels.</p>
                        <a href="{{ route('home.death') }}" class="btn px-4 rounded-pill fw-800"
                            style="border-color: #1f4083; color: white; background-color: #1f4083;">FAIRE UNE DEMANDE</a>
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

        .service-card-new {
            background: white;
            border-radius: 30px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .service-card-new.featured {
            background: var(--primary);
            box-shadow: 0 30px 60px rgba(31, 64, 131, 0.2);
        }

        .service-card-new.featured .icon-wrapper {
            background: rgba(255, 255, 255, 0.1);
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: #f1f5f9;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .transition {
            transition: all 0.4s ease;
        }

        .service-card-new:hover {
            transform: translateY(-15px);
        }

        .service-card-new.featured:hover {
            box-shadow: 0 40px 80px rgba(31, 64, 131, 0.3);
        }

        @media (max-width: 991px) {
            .hero-section {
                text-align: center;
                padding-top: 120px;
                padding-bottom: 60px;
            }

            .display-1 {
                font-size: 3.2rem !important;
            }

            .hero-image-stack {
                margin-top: 50px;
                padding-right: 0;
            }

            .floating-card {
                right: 50% !important;
                transform: translateX(50%) !important;
                bottom: -20px !important;
                min-width: 200px !important;
                display: block !important;
            }

            .d-flex.flex-wrap.gap-4 {
                justify-content: center;
            }

            .search-box-wrapper form {
                flex-direction: column;
                gap: 10px;
                background: transparent !important;
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }

            .search-box-wrapper input {
                border-radius: 15px !important;
                text-align: center;
                border: 1px solid #e2e8f0 !important;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            }

            .search-box-wrapper button {
                width: 100%;
                padding: 15px !important;
            }

            .services-section {
                margin-top: 20px;
            }
        }

        @media (max-width: 768px) {
            .display-1 {
                font-size: 2.5rem !important;
            }

            .service-card-new {
                padding: 30px !important;
                border-radius: 25px;
            }

            .icon-wrapper {
                width: 60px;
                height: 60px;
            }

            .icon-wrapper i {
                font-size: 1.5rem !important;
            }
        }
    </style>
@endpush
