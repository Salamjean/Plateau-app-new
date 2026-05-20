@extends('home.layouts.main')

@section('content')
    <div class="about-page">
        <!-- Hero Section -->
        <section class="about-hero">
            <div class="container text-center">
                <h1 class="display-3 fw-900 text-white mb-4">À Propos de la Mairie</h1>
                <p class="lead text-white-50 mx-auto" style="max-width: 700px;">Découvrez l'institution au service des
                    citoyens de la commune du Plateau, cœur administratif et économique d'Abidjan.</p>
            </div>
        </section>

        <!-- Content Sections... (rest of the file stays same but within section.about-content etc) -->
        <section class="py-md-5 py-3">
            <div class="container py-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <h2 class="fw-900 text-primary mb-4">Notre Histoire</h2>
                        <p class="text-muted fs-5">La Mairie du Plateau représente l'autorité administrative de la commune
                            centrale d'Abidjan, cœur économique de la Côte d'Ivoire. Depuis sa création, notre institution
                            n'a cessé d'évoluer pour répondre aux besoins croissants de la population.</p>
                        <p class="text-muted fs-5">Notre mission quotidienne est de garantir le bon fonctionnement des
                            services publics et d'assurer le développement urbain harmonieux de la commune.</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-img-wrapper">
                            <img src="{{ asset('assets/assets/img/Plateau-immeuble.jpg') }}"
                                class="img-fluid rounded-5 shadow-lg" alt="Mairie">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission, Vision & Valeurs -->
        <section class="py-5 bg-light" style="border-radius: 60px;">
            <div class="container py-5">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div
                            class="mission-card h-100 p-5 bg-white rounded-5 shadow-sm border-top border-primary border-5 transition">
                            <div class="icon-circle bg-primary-light text-primary mb-4">
                                <i class="bi bi-bullseye fs-1"></i>
                            </div>
                            <h3 class="fw-900 text-primary mb-3">Notre Mission</h3>
                            <p class="text-muted leading-relaxed">Offrir des services administratifs de qualité aux citoyens
                                et aux entreprises, assurer un développement urbain durable, et maintenir un environnement
                                favorable aux activités économiques et sociales dans la commune du Plateau.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div
                            class="mission-card h-100 p-5 bg-white rounded-5 shadow-sm border-top border-secondary border-5 transition">
                            <div class="icon-circle bg-secondary-light text-secondary mb-4">
                                <i class="bi bi-eye fs-1"></i>
                            </div>
                            <h3 class="fw-900 text-secondary mb-3">Notre Vision</h3>
                            <p class="text-muted leading-relaxed">Faire du Plateau une commune moderne, inclusive et
                                durable, qui préserve son patrimoine tout en s'adaptant aux défis contemporains, et qui sert
                                de modèle de gouvernance locale en Côte d'Ivoire.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div
                            class="mission-card h-100 p-5 bg-white rounded-5 shadow-sm border-top border-warning border-5 transition">
                            <div class="icon-circle bg-warning-light text-warning mb-4">
                                <i class="bi bi-shield-heart fs-1"></i>
                            </div>
                            <h3 class="fw-900 text-warning mb-3">Nos Valeurs</h3>
                            <p class="text-muted leading-relaxed">Intégrité, transparence, innovation et service public. Ces
                                valeurs guident chacune de nos actions et nous aident à construire une administration proche
                                des citoyens et efficace.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .about-hero {
            background: linear-gradient(rgba(31, 64, 131, 0.9), rgba(31, 64, 131, 0.8)), url('{{ asset('assets/assets/img/Plateau-immeuble.jpg') }}');
            background-size: cover;
            background-position: center;
            padding: 180px 0 100px;
            border-radius: 0 0 80px 80px;
        }

        .fw-900 {
            font-weight: 900;
        }

        .text-primary {
            color: #1f4083 !important;
        }

        .border-primary {
            border-color: #1f4083 !important;
        }

        .mission-card {
            border: none;
            transition: all 0.3s ease;
        }

        .mission-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-primary-light {
            background-color: rgba(31, 64, 131, 0.1);
        }

        .bg-secondary-light {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .bg-warning-light {
            background-color: rgba(245, 158, 11, 0.1);
        }

        .leading-relaxed {
            line-height: 1.6;
        }

        .about-img-wrapper {
            position: relative;
            padding: 20px;
        }

        /* Responsive Improvements */
        @media (max-width: 991px) {
            .about-hero {
                padding: 100px 15px 40px;
                border-radius: 0 0 30px 30px;
                margin-bottom: 0;
                background-attachment: scroll;
                background-position: center top;
            }

            .display-3 {
                font-size: 2rem !important;
            }

            .py-md-5.py-3 {
                padding-top: 0.5rem !important;
                padding-bottom: 1rem !important;
            }

            .container.py-md-5 {
                padding-top: 0 !important;
                padding-bottom: 0 !important;
            }

            .about-img-wrapper {
                margin-top: 10px;
                padding: 0;
                text-align: center;
            }

            .about-img-wrapper img {
                width: 100%;
                max-width: 500px;
                height: auto;
                object-fit: cover;
                border-radius: 20px !important;
            }

            /* Fix horizontal scroll */
            .row {
                margin-right: -15px;
                margin-left: -15px;
            }

            .container {
                padding-right: 20px;
                padding-left: 20px;
            }
        }

        @media (max-width: 768px) {
            .display-3 {
                font-size: 2.2rem !important;
            }

            .mission-card {
                padding: 30px !important;
            }

            .section-title {
                font-size: 1.8rem !important;
            }
        }
    </style>
@endpush
