@extends('home.layouts.main')

@section('content')
    <div class="service-detail-page">
        <!-- Hero Section -->
        <section class="service-hero">
            <div class="container text-center">
                <div class="badge-service mb-3" data-aos="fade-down">
                    <span class="badge rounded-pill bg-white bg-opacity-25 text-white px-3 py-2 fw-700">
                        <i class="bi bi-file-earmark-person me-1"></i> ÉTAT CIVIL
                    </span>
                </div>
                <h1 class="display-3 fw-900 text-white mb-4" data-aos="fade-up">Extraits de Naissance</h1>
                <p class="lead text-white-50 mx-auto" style="max-width: 700px;" data-aos="fade-up" data-aos-delay="100">
                    Obtenez vos actes de naissance officiels en quelques clics. Un service rapide et sécurisé proposé par la
                    Mairie du Plateau.
                </p>
            </div>
        </section>

        <!-- Main Content -->
        <section class="py-5" style="margin-top: -60px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <!-- Navigation Tabs -->
                        <div class="tabs-modern mb-5" data-aos="fade-up">
                            <button class="tab-link active" onclick="openTab(event, 'copie-simple')">
                                <i class="bi bi-file-text me-2"></i>Copie Simple (Extrait)
                            </button>
                            <button class="tab-link" onclick="openTab(event, 'copie-integrale')">
                                <i class="bi bi-file-earmark-text me-2"></i>Copie Intégrale
                            </button>
                        </div>

                        <!-- Tab Content: Copie Simple -->
                        <div id="copie-simple" class="tab-pane active" data-aos="fade-up">
                            <div class="card-modern">
                                <div class="card-top-bar" style="background: #1f4083;"></div>
                                <div class="p-5">
                                    <div class="row g-5">
                                        <div class="col-md-7">
                                            <h2 class="fw-900 text-primary mb-4">Copie Simple (Extrait)</h2>
                                            <p class="text-muted fs-5 mb-4">Pour obtenir un extrait d'acte de naissance
                                                standard contenant les informations essentielles.</p>

                                            <div class="alert-modern mb-4">
                                                <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                                                <div>
                                                    <span class="fw-800 d-block text-primary">Usage courant</span>
                                                    <span class="small text-muted">Ce document est généralement suffisant
                                                        pour la plupart des démarches administratives courantes.</span>
                                                </div>
                                            </div>

                                            <h4 class="fw-800 mb-3 text-dark">Informations à fournir :</h4>
                                            <ul class="list-unstyled custom-list">
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Nom et prénoms
                                                    complets de l'intéressé</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Date et lieu de
                                                    naissance</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Numéro d'acte et
                                                    année du registre (si possible)</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Nom et prénoms des
                                                    parents</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="price-card p-4 rounded-4 text-center glass-effect">
                                                <span class="text-uppercase fw-800 text-muted small">Tarif Officiel</span>
                                                <h2 class="display-5 fw-900 text-primary my-2">500 FCFA</h2>
                                                <p class="small text-muted mb-4">Par exemplaire (hors frais de timbre)</p>
                                                <a href="{{ route('user.dashboard') }}"
                                                    class="btn btn-primary w-100 py-3 rounded-3 fw-800">
                                                    FAIRE UNE DEMANDE <i class="bi bi-arrow-right ms-2"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Content: Copie Intégrale -->
                        <div id="copie-integrale" class="tab-pane">
                            <div class="card-modern">
                                <div class="card-top-bar" style="background: #1f4083;"></div>
                                <div class="p-5">
                                    <div class="row g-5">
                                        <div class="col-md-7">
                                            <h2 class="fw-900 text-primary mb-4">Copie Intégrale</h2>
                                            <p class="text-muted fs-5 mb-4">Une reproduction complète de l'acte original
                                                avec toutes les mentions marginales.</p>

                                            <div class="alert-modern info mb-4">
                                                <i class="bi bi-file-earmark-check-fill text-primary fs-4"></i>
                                                <div>
                                                    <span class="fw-800 d-block text-primary">Détails Complets</span>
                                                    <span class="small text-muted">Indispensable pour certaines démarches
                                                        comme le mariage ou l'obtention de la nationalité.</span>
                                                </div>
                                            </div>

                                            <h4 class="fw-800 mb-3 text-dark">Documents requis :</h4>
                                            <ul class="list-unstyled custom-list">
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Références
                                                    complètes de l'acte original</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Justificatif
                                                    d'identité du demandeur</li>
                                                <li><i class="bi bi-check-circle-fill text-success"></i> Preuve de lien de
                                                    parenté (si demande pour un tiers)</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="price-card p-4 rounded-4 text-center glass-effect">
                                                <span class="text-uppercase fw-800 text-muted small">Tarif Officiel</span>
                                                <h2 class="display-5 fw-900 text-primary my-2">500 FCFA</h2>
                                                <p class="small text-muted mb-4">Par exemplaire (hors frais de timbre)</p>
                                                <a href="{{ route('user.dashboard') }}"
                                                    class="btn btn-primary w-100 py-3 rounded-3 fw-800">
                                                    FAIRE UNE DEMANDE <i class="bi bi-arrow-right ms-2"></i>
                                                </a>
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

        <!-- FAQ Section -->
        <section class="py-5 bg-light">
            <div class="container py-5">
                <h2 class="text-center fw-900 text-primary mb-5">Questions Fréquentes</h2>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-4">
                        <div class="faq-card p-4 bg-white rounded-4 shadow-sm h-100 transition">
                            <h4 class="fw-800 text-primary mb-3">Quel est le délai ?</h4>
                            <p class="text-muted mb-0">Le traitement prend généralement 3 heures après validation
                                de votre paiement.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="faq-card p-4 bg-white rounded-4 shadow-sm h-100 transition">
                            <h4 class="fw-800 text-primary mb-3">Puis-je être livré ?</h4>
                            <p class="text-muted mb-0">Oui, nous proposons une livraison sécurisée à domicile ou à votre
                                bureau partout en Côte d'Ivoire.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="faq-card p-4 bg-white rounded-4 shadow-sm h-100 transition">
                            <h4 class="fw-800 text-primary mb-3">Quels sont les tarifs ?</h4>
                            <p class="text-muted mb-0">L'extrait coûte 500 FCFA l'unité, auxquels s'ajoutent les frais de
                                timbre et de livraison éventuelle.</p>
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

        .tabs-modern {
            display: flex;
            justify-content: center;
            gap: 15px;
            background: white;
            padding: 10px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .tab-link {
            border: none;
            background: transparent;
            padding: 15px 30px;
            border-radius: 15px;
            font-weight: 700;
            color: #64748b;
            transition: all 0.3s ease;
        }

        .tab-link.active {
            background: #1f4083;
            color: white;
        }

        .card-modern {
            background: white;
            border-radius: 35px;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(15, 23, 42, 0.08);
            border: 1px solid #f1f5f9;
            position: relative;
        }

        .card-top-bar {
            height: 10px;
            width: 100%;
        }

        .alert-modern {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 20px;
            background: rgba(31, 64, 131, 0.05);
            border-radius: 15px;
        }

        .alert-modern.warning {
            background: rgba(245, 158, 11, 0.05);
        }

        .custom-list li {
            margin-bottom: 15px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .price-card {
            border: 2px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }

        .faq-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 991px) {
            .service-hero {
                padding: 120px 0 60px !important;
                border-radius: 0 0 40px 40px;
            }

            .display-3 {
                font-size: 2.5rem !important;
            }

            .tabs-modern {
                flex-direction: column;
                padding: 10px;
                gap: 8px;
            }

            .tab-link {
                width: 100%;
                padding: 12px 20px;
                text-align: center;
            }

            .card-modern {
                border-radius: 25px;
                margin-top: -40px !important;
            }

            .p-5 {
                padding: 1.5rem !important;
            }

            .row.g-5 {
                --bs-gutter-y: 2rem;
            }

            .price-card {
                padding: 1.5rem !important;
            }

            .alert-modern {
                padding: 15px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 768px) {
            .display-3 {
                font-size: 2rem !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function openTab(evt, tabName) {
            var i, tabContent, tabLinks;
            tabContent = document.getElementsByClassName("tab-pane");
            for (i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
                tabContent[i].classList.remove("active");
            }
            tabLinks = document.getElementsByClassName("tab-link");
            for (i = 0; i < tabLinks.length; i++) {
                tabLinks[i].className = tabLinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.className += " active";
        }
    </script>
@endpush
