@extends('home.layouts.main')

@section('content')
    <div class="service-detail-page">
        <!-- Hero Section -->
        <section class="service-hero">
            <div class="container text-center">
                <div class="badge-service mb-3" data-aos="fade-down">
                    <span class="badge rounded-pill bg-white bg-opacity-25 text-white px-3 py-2 fw-700">
                        <i class="bi bi-calendar-event me-1"></i> PRISE DE RDV
                    </span>
                </div>
                <h1 class="display-3 fw-900 text-white mb-4" data-aos="fade-up">Rendez-vous de Mariage</h1>
                <p class="lead text-white-50 mx-auto" style="max-width: 700px;" data-aos="fade-up" data-aos-delay="100">
                    Planifiez votre union à la Mairie du Plateau. Un accompagnement personnalisé pour le plus beau jour de
                    votre vie.
                </p>
            </div>
        </section>

        <!-- Process Steps -->
        <section class="py-5" style="margin-top: -80px;">
            <div class="container">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="step-card p-4 bg-white rounded-4 shadow-sm text-center h-100 border-0">
                            <div class="step-icon mb-3 mx-auto" style="background: rgba(31, 64, 131, 0.1); color: #1f4083;">
                                1</div>
                            <h5 class="fw-800">Dossier</h5>
                            <p class="small text-muted mb-0">Rassemblez tous les documents requis</p>
                        </div>
                    </div>
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="step-card p-4 bg-white rounded-4 shadow-sm text-center h-100 border-0">
                            <div class="step-icon mb-3 mx-auto" style="background: rgba(31, 64, 131, 0.1); color: #1f4083;">
                                2</div>
                            <h5 class="fw-800">Date</h5>
                            <p class="small text-muted mb-0">Choisissez un créneau disponible</p>
                        </div>
                    </div>
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                        <div class="step-card p-4 bg-white rounded-4 shadow-sm text-center h-100 border-0">
                            <div class="step-icon mb-3 mx-auto" style="background: rgba(31, 64, 131, 0.1); color: #1f4083;">
                                3</div>
                            <h5 class="fw-800">Validation</h5>
                            <p class="small text-muted mb-0">Recevez votre confirmation</p>
                        </div>
                    </div>
                    <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                        <div class="step-card p-4 bg-white rounded-4 shadow-sm text-center h-100 border-0">
                            <div class="step-icon mb-3 mx-auto" style="background: rgba(31, 64, 131, 0.1); color: #1f4083;">
                                4</div>
                            <h5 class="fw-800">Célébration</h5>
                            <p class="small text-muted mb-0">Présentez-vous pour l'union</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-11">
                        <div class="card-modern overflow-hidden border-0 shadow-lg">
                            <div class="card-top-bar" style="background: #1f4083;"></div>
                            <div class="p-5">
                                <div class="row g-5">
                                    <div class="col-lg-8">
                                        <h2 class="fw-900 text-primary mb-4">Pièces à Fournir</h2>

                                        <div class="row g-4 mb-4">
                                            <div class="col-md-6">
                                                <div class="doc-category p-4 rounded-4 bg-light h-100">
                                                    <h6 class="fw-800 text-primary mb-3"><i
                                                            class="bi bi-person-badge me-2"></i>Futurs Époux</h6>
                                                    <ul class="list-unstyled small custom-list-check">
                                                        <li><i class="bi bi-check2"></i> Acte de naissance (- de 3 mois)
                                                        </li>
                                                        <li><i class="bi bi-check2"></i> Pièce d'identité originale</li>
                                                        <li><i class="bi bi-check2"></i> Certificat de résidence</li>
                                                        <li><i class="bi bi-check2"></i> 4 photos d'identité</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="doc-category p-4 rounded-4 bg-light h-100">
                                                    <h6 class="fw-800 text-primary mb-3"><i
                                                            class="bi bi-people me-2"></i>Témoins</h6>
                                                    <ul class="list-unstyled small custom-list-check">
                                                        <li><i class="bi bi-check2"></i> Pièce d'identité (CNI/Passeport)
                                                        </li>
                                                        <li><i class="bi bi-check2"></i> Justificatif de domicile</li>
                                                        <li><i class="bi bi-check2"></i> Copie lisible de l'identité</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div
                                                    class="doc-category p-4 rounded-4 bg-light border-start border-4 border-warning">
                                                    <h6 class="fw-800 text-warning mb-3"><i
                                                            class="bi bi-file-medical me-2"></i>Documents Médicaux</h6>
                                                    <ul class="list-unstyled small custom-list-check mb-0">
                                                        <li><i class="bi bi-check2"></i> Certificat prénuptial délivré par
                                                            un médecin agréé</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert-modern bg-primary bg-opacity-10 text-primary p-4 rounded-4">
                                            <i class="bi bi-info-circle-fill fs-3 me-3"></i>
                                            <p class="small mb-0 fw-600">
                                                Tous les documents originaux doivent être présentés lors du dépôt physique
                                                du dossier après validation de votre rendez-vous en ligne.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="sticky-top" style="top: 100px;">
                                            <div class="availability-card p-4 rounded-4 text-center border-0 shadow-sm"
                                                style="background: #f8fafc;">
                                                <span class="text-uppercase fw-800 text-muted small">Célébrations</span>
                                                <h3 class="fw-900 text-primary my-3">Mardi au Samedi</h3>
                                                <hr>
                                                <div class="d-flex justify-content-between mb-2 small">
                                                    <span class="text-muted">Matinée</span>
                                                    <span class="fw-700">09:00 - 12:00</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-4 small">
                                                    <span class="text-muted">Après-midi</span>
                                                    <span class="fw-700">14:00 - 16:30</span>
                                                </div>
                                                <a href="{{ route('user.dashboard') }}"
                                                    class="btn btn-primary w-100 py-3 rounded-3 fw-800 mb-3">
                                                    PRENDRE RDV <i class="bi bi-calendar-plus ms-2"></i>
                                                </a>
                                                <p class="x-small text-muted mb-0">Minimum 1 mois avant la date souhaitée
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="py-5 bg-light">
            <div class="container py-5">
                <h2 class="text-center fw-900 text-primary mb-5">Précisions Utiles</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="faq-card p-4 bg-white rounded-4 shadow-sm h-100 transition border-0">
                            <h5 class="fw-800 text-primary mb-3">Délai d'obtention ?</h5>
                            <p class="text-muted small mb-0">Comptez un délai moyen de 4 à 6 semaines selon la période
                                souhaitée et l'affluence.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="faq-card p-4 bg-white rounded-4 shadow-sm h-100 transition border-0">
                            <h5 class="fw-800 text-primary mb-3">Annulation ?</h5>
                            <p class="text-muted small mb-0">Tout report ou annulation doit être signalé au moins 72 heures
                                à l'avance via votre tableau de bord.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="faq-card p-4 bg-white rounded-4 shadow-sm h-100 transition border-0">
                            <h5 class="fw-800 text-primary mb-3">Présence ?</h5>
                            <p class="text-muted small mb-0">La présence des deux futurs époux est fortement recommandée
                                lors du dépôt final du dossier.</p>
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
            background: linear-gradient(rgba(31, 64, 131, 0.9), rgba(31, 64, 131, 0.8)), url('{{ asset('assets/assets/img/Plateau-immeuble.jpg') }}');
            background-size: cover;
            background-position: center;
            padding: 180px 0 120px;
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

        .fw-600 {
            font-weight: 600;
        }

        .step-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.2rem;
        }

        .card-modern {
            background: white;
            border-radius: 35px;
            box-shadow: 0 40px 80px rgba(15, 23, 42, 0.08);
            position: relative;
        }

        .card-top-bar {
            height: 10px;
            width: 100%;
        }

        .custom-list-check li {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.4;
        }

        .custom-list-check li i {
            color: #1f4083;
            font-weight: 900;
        }

        .x-small {
            font-size: 0.75rem;
        }

        .faq-card:hover {
            transform: translateY(-5px);
        }

        @media (max-width: 768px) {
            .service-hero {
                padding: 140px 0 80px;
                border-radius: 0 0 40px 40px;
            }
        }
    </style>
@endpush
