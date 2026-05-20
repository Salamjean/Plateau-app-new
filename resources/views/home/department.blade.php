@extends('home.layouts.main')

@section('content')
    <div class="departments-page">
        <!-- Hero Section -->
        <section class="service-hero">
            <div class="container text-center">
                <div class="badge-service mb-3" data-aos="fade-down">
                    <span class="badge rounded-pill bg-white bg-opacity-25 text-white px-3 py-2 fw-700">
                        <i class="bi bi-building me-1"></i> ORGANISATION
                    </span>
                </div>
                <h1 class="display-3 fw-900 text-white mb-4" data-aos="fade-up">Nos Départements</h1>
                <p class="lead text-white-50 mx-auto" style="max-width: 700px;" data-aos="fade-up" data-aos-delay="100">
                    Découvrez les différents services qui composent l'administration de la Mairie du Plateau.
                </p>
            </div>
        </section>

        <!-- Departments Grid -->
        <section class="py-5" style="margin-top: -60px;">
            <div class="container pb-5">
                <div class="row g-4">
                    <!-- État Civil -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up">
                        <div class="dept-card p-4 rounded-4 bg-white shadow-sm border-0 h-100 transition">
                            <div class="icon-sq mb-4 bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px;">
                                <i class="bi bi-person-lines-fill fs-3"></i>
                            </div>
                            <h4 class="fw-800 text-primary mb-3">ÉTAT CIVIL</h4>
                            <p class="text-muted small mb-4">Gestion des naissances, mariages, décès et documents d'identité
                                officiels.</p>
                            <div class="dept-contact small text-muted">
                                <p class="mb-2"><i class="bi bi-person-circle me-2 text-primary"></i> Mme. Koné Aminata
                                </p>
                                <p class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i> +225 20 21 00 01</p>
                                <p class="mb-0"><i class="bi bi-clock me-2 text-primary"></i> 07:30 - 16:30</p>
                            </div>
                            <hr class="my-4 opacity-10">
                            <a href="{{ route('home.contact') }}"
                                class="btn btn-primary btn-sm w-100 rounded-pill py-2 fw-700">Contacter le service</a>
                        </div>
                    </div>

                    <!-- Services Techniques -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="dept-card p-4 rounded-4 bg-white shadow-sm border-0 h-100 transition">
                            <div class="icon-sq mb-4 bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px;">
                                <i class="bi bi-tools fs-3"></i>
                            </div>
                            <h4 class="fw-800 text-primary mb-3">SERVICES TECHNIQUES</h4>
                            <p class="text-muted small mb-4">Entretien de la voirie, gestion des bâtiments et
                                infrastructures communales.</p>
                            <div class="dept-contact small text-muted">
                                <p class="mb-2"><i class="bi bi-person-circle me-2 text-primary"></i> M. Diallo Ibrahim
                                </p>
                                <p class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i> +225 20 21 00 02</p>
                                <p class="mb-0"><i class="bi bi-clock me-2 text-primary"></i> 08:00 - 17:00</p>
                            </div>
                            <hr class="my-4 opacity-10">
                            <a href="{{ route('home.contact') }}"
                                class="btn btn-primary btn-sm w-100 rounded-pill py-2 fw-700">Contacter le service</a>
                        </div>
                    </div>

                    <!-- Affaires Sociales -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="dept-card p-4 rounded-4 bg-white shadow-sm border-0 h-100 transition">
                            <div class="icon-sq mb-4 bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center"
                                style="width: 60px; height: 60px;">
                                <i class="bi bi-heart-pulse fs-3"></i>
                            </div>
                            <h4 class="fw-800 text-primary mb-3">AFFAIRES SOCIALES</h4>
                            <p class="text-muted small mb-4">Accompagnement des familles, bourses sociales et aides aux
                                citoyens.</p>
                            <div class="dept-contact small text-muted">
                                <p class="mb-2"><i class="bi bi-person-circle me-2 text-primary"></i> Mme. Tanoh Marie</p>
                                <p class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i> +225 20 21 00 03</p>
                                <p class="mb-0"><i class="bi bi-clock me-2 text-primary"></i> 08:00 - 16:00</p>
                            </div>
                            <hr class="my-4 opacity-10">
                            <a href="{{ route('home.contact') }}"
                                class="btn btn-primary btn-sm w-100 rounded-pill py-2 fw-700">Contacter le service</a>
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

        .fw-900 {
            font-weight: 900;
        }

        .fw-800 {
            font-weight: 800;
        }

        .fw-700 {
            font-weight: 700;
        }

        .dept-card {
            transition: all 0.3s ease;
        }

        .dept-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(31, 64, 131, 0.1) !important;
        }

        @media (max-width: 768px) {
            .service-hero {
                padding: 140px 0 80px;
                border-radius: 0 0 40px 40px;
            }
        }
    </style>
@endpush
