@extends('home.layouts.main')

@section('content')
    <div class="contact-page-modern">
        <!-- Hero Section -->
        <section class="service-hero">
            <div class="container text-center">
                <div class="badge-service mb-3" data-aos="fade-down">
                    <span class="badge rounded-pill bg-white bg-opacity-25 text-white px-3 py-2 fw-700">
                        <i class="bi bi-headset me-1"></i> SUPPORT
                    </span>
                </div>
                <h1 class="display-3 fw-900 text-white mb-4" data-aos="fade-up">Contactez la Mairie</h1>
                <p class="lead text-white-50 mx-auto" style="max-width: 700px;" data-aos="fade-up" data-aos-delay="100">
                    Nos équipes sont à votre disposition pour répondre à toutes vos interrogations.
                </p>
            </div>
        </section>

        <!-- Contact Cards -->
        <section class="py-5" style="margin-top: -80px;">
            <div class="container">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="contact-info-card p-4 bg-white rounded-4 shadow-sm text-center h-100 border-0">
                            <div class="icon-circle mb-3 mx-auto shadow-sm"
                                style="background: rgba(31, 64, 131, 0.1); color: #1f4083; width:60px; height:60px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-telephone-fill fs-4"></i>
                            </div>
                            <h5 class="fw-800 text-primary">Téléphone</h5>
                            <p class="text-muted small mb-0">+225 20 21 22 23</p>
                            <p class="text-muted small">+225 20 21 22 24</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="contact-info-card p-4 bg-white rounded-4 shadow-sm text-center h-100 border-0">
                            <div class="icon-circle mb-3 mx-auto shadow-sm"
                                style="background: rgba(31, 64, 131, 0.1); color: #1f4083; width:60px; height:60px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-envelope-fill fs-4"></i>
                            </div>
                            <h5 class="fw-800 text-primary">Email</h5>
                            <p class="text-muted small mb-0">contact@mairieplateau.ci</p>
                            <p class="text-muted small">support@mairieplateau.ci</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="contact-info-card p-4 bg-white rounded-4 shadow-sm text-center h-100 border-0">
                            <div class="icon-circle mb-3 mx-auto shadow-sm"
                                style="background: rgba(31, 64, 131, 0.1); color: #1f4083; width:60px; height:60px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-geo-alt-fill fs-4"></i>
                            </div>
                            <h5 class="fw-800 text-primary">Localisation</h5>
                            <p class="text-muted small mb-0">Hôtel de Ville, Place de la Mairie</p>
                            <p class="text-muted small">Le Plateau, Abidjan</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Form & Map -->
        <section class="py-5">
            <div class="container py-5">
                <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-lg-6 p-5">
                            <h2 class="fw-900 text-primary mb-4">Envoyez un message</h2>
                            <form action="#" method="POST" class="contact-form">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="name"
                                                placeholder="Nom complet">
                                            <label for="name">Votre nom</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="email" placeholder="Email">
                                            <label for="email">Votre email</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="subject">
                                                <option selected>Sélectionnez un sujet</option>
                                                <option value="1">État Civil</option>
                                                <option value="2">Urbanisme</option>
                                                <option value="3">Assistance technique</option>
                                            </select>
                                            <label for="subject">Objet de votre message</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating mb-4">
                                            <textarea class="form-control" placeholder="Votre message" id="message" style="height: 150px"></textarea>
                                            <label for="message">Comment pouvons-nous vous aider ?</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-800">
                                            ENVOYER LE MESSAGE <i class="bi bi-send-fill ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="h-100 w-100" style="min-height: 500px; background: #f0f4f8;">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15891.1378872085!2d-4.0205!3d5.3246!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNcKwMTknMjguNiJOIDTCsDAxJzEzLjgiVw!5e0!3m2!1sfr!2sci!4v1620000000000!5m2!1sfr!2sci"
                                    width="100%" height="100%" style="border:0;" allowfullscreen=""
                                    loading="lazy"></iframe>
                            </div>
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
            padding: 180px 0 120px;
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

        .fw-900 {
            font-weight: 900;
        }

        .fw-800 {
            font-weight: 800;
        }

        .fw-700 {
            font-weight: 700;
        }

        .contact-info-card {
            transition: transform 0.3s ease;
        }

        .contact-info-card:hover {
            transform: translateY(-10px);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #1f4083;
            box-shadow: 0 0 0 0.25rem rgba(31, 64, 131, 0.1);
        }

        @media (max-width: 768px) {
            .service-hero {
                padding: 140px 0 80px;
                border-radius: 0 0 40px 40px;
            }
        }
    </style>
@endpush
