@extends('home.layouts.main')

@section('content')
    <div class="privacy-policy-page">
        <!-- Hero Section -->
        <section class="service-hero">
            <div class="container text-center">
                <div class="badge-service mb-3" data-aos="fade-down">
                    <span class="badge rounded-pill bg-white bg-opacity-25 text-white px-3 py-2 fw-700">
                        <i class="bi bi-shield-check me-1"></i> CONFIDENTIALITÉ
                    </span>
                </div>
                <h1 class="display-3 fw-900 text-white mb-4" data-aos="fade-up">Politique de Confidentialité</h1>
                <p class="lead text-white-50 mx-auto" style="max-width: 700px;" data-aos="fade-up" data-aos-delay="100">
                    Découvrez comment nous protégeons vos données personnelles dans le cadre de nos services numériques.
                </p>
            </div>
        </section>

        <!-- Content Section -->
        <section class="py-5" style="margin-top: -60px;">
            <div class="container pb-5">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                            <div class="p-5">
                                <div
                                    class="d-flex align-items-center mb-5 p-3 rounded-4 bg-light border-start border-4 border-primary">
                                    <i class="bi bi-info-circle-fill text-primary fs-3 me-3"></i>
                                    <div>
                                        <h6 class="fw-800 mb-1">Dernière mise à jour</h6>
                                        <p class="small text-muted mb-0">Septembre 2024</p>
                                    </div>
                                </div>

                                <div class="row g-5">
                                    <div class="col-md-6">
                                        <div class="policy-section mb-5">
                                            <h4 class="fw-800 text-primary mb-3">1. Collecte des données</h4>
                                            <p class="text-muted small">
                                                Dans le cadre de l'utilisation du portail citoyen, la Mairie du Plateau
                                                collecte les informations nécessaires au traitement de vos demandes
                                                administratives : identité, coordonnées, pièces justificatives.
                                            </p>
                                        </div>

                                        <div class="policy-section mb-5">
                                            <h4 class="fw-800 text-primary mb-3">2. Utilisation des données</h4>
                                            <p class="text-muted small">
                                                Vos données sont exclusivement utilisées pour :
                                            </p>
                                            <ul class="list-unstyled small text-muted">
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>
                                                    Le traitement de vos actes d'état civil</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>
                                                    La communication de l'avancement de vos dossiers</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>
                                                    L'amélioration de nos services numériques</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="policy-section mb-5">
                                            <h4 class="fw-800 text-primary mb-3">3. Protection & Sécurité</h4>
                                            <p class="text-muted small">
                                                Nous mettons en œuvre des mesures de sécurité techniques et
                                                organisationnelles rigoureuses pour protéger vos données contre tout accès
                                                non autorisé, modification ou divulgation.
                                            </p>
                                        </div>

                                        <div class="policy-section mb-5">
                                            <h4 class="fw-800 text-primary mb-3">4. Vos Droits</h4>
                                            <p class="text-muted small">
                                                Conformément à la législation en vigueur sur la protection des données à
                                                caractère personnel en Côte d'Ivoire, vous disposez d'un droit d'accès, de
                                                rectification et de suppression de vos informations.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-5 opacity-10">

                                <div class="text-center">
                                    <p class="small text-muted mb-4">Pour toute question concernant vos données :</p>
                                    <a href="{{ route('home.contact') }}"
                                        class="btn btn-outline-primary rounded-pill px-5 fw-800">NOUS CONTACTER</a>
                                </div>
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

        .policy-section h4 {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        @media (max-width: 768px) {
            .service-hero {
                padding: 140px 0 80px;
                border-radius: 0 0 40px 40px;
            }
        }
    </style>
@endpush
