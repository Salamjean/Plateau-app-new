@extends('home.layouts.main')

@section('content')
    <div class="services-list-page">
        <!-- Hero Section -->
        <section class="service-hero">
            <div class="container text-center">
                <div class="badge-service mb-3" data-aos="fade-down">
                    <span class="badge rounded-pill bg-white bg-opacity-25 text-white px-3 py-2 fw-700">
                        <i class="bi bi-grid me-1"></i> CATALOGUE
                    </span>
                </div>
                <h1 class="display-3 fw-900 text-white mb-4" data-aos="fade-up">Nos Services Municipaux</h1>
                <p class="lead text-white-50 mx-auto mb-5" style="max-width: 700px;" data-aos="fade-up" data-aos-delay="100">
                    Trouvez facilement le service dont vous avez besoin parmi notre catalogue administratif complet.
                </p>

                <!-- Search Bar -->
                <div class="col-lg-8 mx-auto" data-aos="fade-up" data-aos-delay="200">
                    <div class="search-wrapper p-2 bg-white rounded-pill shadow-lg d-flex align-items-center">
                        <i class="bi bi-search text-primary ms-3"></i>
                        <input type="text" class="form-control border-0 shadow-none ps-3"
                            placeholder="Quel service recherchez-vous ? (ex: Acte de naissance, Mariage...)"
                            id="serviceSearch">
                        <button class="btn btn-primary rounded-pill px-4 py-2 fw-700 ms-2">Rechercher</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Grid -->
        <section class="py-5 bg-white">
            <div class="container py-5">
                <div class="row g-4" id="servicesGrid">
                    <!-- État Civil -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up">
                        <div class="service-card-new p-4 rounded-4 border-0 shadow-sm h-100 transition">
                            <div class="icon-box mb-4 text-primary bg-primary bg-opacity-10 rounded-4 d-inline-flex align-items-center justify-content-center"
                                style="width: 70px; height: 70px;">
                                <i class="bi bi-card-checklist fs-2"></i>
                            </div>
                            <h4 class="fw-800 text-primary mb-3 text-uppercase">État Civil</h4>
                            <p class="text-muted small mb-4">Gérez vos actes de naissance, mariage et décès en quelques
                                clics.</p>
                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><a href="{{ route('home.birth') }}"
                                        class="text-decoration-none text-muted"><i
                                            class="bi bi-chevron-right me-2 text-primary"></i>Naissance</a></li>
                                <li class="mb-2"><a href="{{ route('home.wedding') }}"
                                        class="text-decoration-none text-muted"><i
                                            class="bi bi-chevron-right me-2 text-primary"></i>Mariage</a></li>
                                <li class="mb-2"><a href="{{ route('home.death') }}"
                                        class="text-decoration-none text-muted"><i
                                            class="bi bi-chevron-right me-2 text-primary"></i>Décès</a></li>
                            </ul>
                            <a href="{{ route('user.dashboard') }}"
                                class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-700">Accéder au service</a>
                        </div>
                    </div>

                    <!-- Urbanisme -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-card-new p-4 rounded-4 border-0 shadow-sm h-100 transition">
                            <div class="icon-box mb-4 text-primary bg-primary bg-opacity-10 rounded-4 d-inline-flex align-items-center justify-content-center"
                                style="width: 70px; height: 70px;">
                                <i class="bi bi-houses fs-2"></i>
                            </div>
                            <h4 class="fw-800 text-primary mb-3 text-uppercase">Urbanisme</h4>
                            <p class="text-muted small mb-4">Permis de construire et autorisations de travaux pour vos
                                projets.</p>
                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Permis de construire
                                </li>
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Certificat
                                    d'urbanisme</li>
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Audit foncier</li>
                            </ul>
                            <a href="{{ route('user.dashboard') }}"
                                class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-700">Accéder au service</a>
                        </div>
                    </div>

                    <!-- Citoyenneté -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-card-new p-4 rounded-4 border-0 shadow-sm h-100 transition">
                            <div class="icon-box mb-4 text-primary bg-primary bg-opacity-10 rounded-4 d-inline-flex align-items-center justify-content-center"
                                style="width: 70px; height: 70px;">
                                <i class="bi bi-person-badge fs-2"></i>
                            </div>
                            <h4 class="fw-800 text-primary mb-3 text-uppercase">Citoyenneté</h4>
                            <p class="text-muted small mb-4">Identité, résidence et documents administratifs courants.</p>
                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Certificat de
                                    résidence</li>
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Carte d'identité
                                </li>
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Recensement</li>
                            </ul>
                            <a href="{{ route('user.dashboard') }}"
                                class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-700">Accéder au service</a>
                        </div>
                    </div>

                    <!-- Économie -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-card-new p-4 rounded-4 border-0 shadow-sm h-100 transition">
                            <div class="icon-box mb-4 text-primary bg-primary bg-opacity-10 rounded-4 d-inline-flex align-items-center justify-content-center"
                                style="width: 70px; height: 70px;">
                                <i class="bi bi-briefcase fs-2"></i>
                            </div>
                            <h4 class="fw-800 text-primary mb-3 text-uppercase">Économie</h4>
                            <p class="text-muted small mb-4">Services aux entreprises et commerçants du Plateau.</p>
                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Licences
                                    commerciales</li>
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Patentes</li>
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Marchés publics
                                </li>
                            </ul>
                            <a href="{{ route('user.dashboard') }}"
                                class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-700">Accéder au service</a>
                        </div>
                    </div>

                    <!-- Social -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="service-card-new p-4 rounded-4 border-0 shadow-sm h-100 transition">
                            <div class="icon-box mb-4 text-primary bg-primary bg-opacity-10 rounded-4 d-inline-flex align-items-center justify-content-center"
                                style="width: 70px; height: 70px;">
                                <i class="bi bi-heart fs-2"></i>
                            </div>
                            <h4 class="fw-800 text-primary mb-3 text-uppercase">Social</h4>
                            <p class="text-muted small mb-4">Aide sociale et accompagnement des familles.</p>
                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Bourses d'études
                                </li>
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Logement social
                                </li>
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Aide alimentaire
                                </li>
                            </ul>
                            <a href="{{ route('user.dashboard') }}"
                                class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-700">Accéder au service</a>
                        </div>
                    </div>

                    <!-- Culture -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                        <div class="service-card-new p-4 rounded-4 border-0 shadow-sm h-100 transition">
                            <div class="icon-box mb-4 text-primary bg-primary bg-opacity-10 rounded-4 d-inline-flex align-items-center justify-content-center"
                                style="width: 70px; height: 70px;">
                                <i class="bi bi-music-note-beamed fs-2"></i>
                            </div>
                            <h4 class="fw-800 text-primary mb-3 text-uppercase">Culture & Sport</h4>
                            <p class="text-muted small mb-4">Activités culturelles, sportives et de loisirs.</p>
                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Médiathèque</li>
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Stades & Gymnaises
                                </li>
                                <li class="mb-2"><i class="bi bi-chevron-right me-2 text-primary"></i>Événements</li>
                            </ul>
                            <a href="{{ route('user.dashboard') }}"
                                class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-700">Accéder au service</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .service-hero {
            background: linear-gradient(rgba(31, 64, 131, 0.95), rgba(31, 64, 131, 0.85)), url('{{ asset('assets/assets/img/Plateau-immeuble.jpg') }}');
            background-size: cover;
            background-position: center;
            padding: 180px 0 100px;
            border-radius: 0 0 80px 80px;
        }

        .text-primary {
            color: #1f4083 !important;
        }

        .bg-primary {
            background-color: #1f4083 !important;
        }

        .btn-primary {
            background-color: #1f4083 !important;
            border-color: #1f4083 !important;
        }

        .btn-outline-primary {
            color: #1f4083 !important;
            border-color: #1f4083 !important;
        }

        .btn-outline-primary:hover {
            background-color: #1f4083 !important;
            color: white !important;
        }

        .fw-900 {
            font-weight: 900;
        }

        .fw-800 {
            font-weight: 800;
        }

        .fw-700 {
            font-weight: 700;
        }

        .search-wrapper {
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .search-wrapper:focus-within {
            transform: scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2) !important;
        }

        .service-card-new {
            background: #fdfdfd;
            border: 1px solid #f0f0f0 !important;
        }

        .service-card-new:hover {
            transform: translateY(-10px);
            background: white;
            box-shadow: 0 25px 50px rgba(31, 64, 131, 0.1) !important;
            border-color: #1f4083 !important;
        }

        .icon-box i {
            transition: transform 0.3s ease;
        }

        .service-card-new:hover .icon-box i {
            transform: scale(1.2);
        }

        @media (max-width: 991px) {
            .service-hero {
                padding: 120px 0 60px;
                border-radius: 0 0 40px 40px;
            }

            .display-3 {
                font-size: 2.5rem !important;
            }

            .search-wrapper {
                margin-top: 20px;
                flex-direction: column;
                border-radius: 20px !important;
                padding: 15px !important;
            }

            .search-wrapper input {
                width: 100%;
                text-align: center;
                margin: 10px 0;
            }

            .search-wrapper button {
                width: 100%;
                margin-left: 0 !important;
            }
        }

        @media (max-width: 768px) {
            .display-3 {
                font-size: 2rem !important;
            }
        }
    </style>
@endpush
