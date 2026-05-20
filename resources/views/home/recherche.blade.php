@extends('home.layouts.main')

@section('content')
    <div class="search-tracking-page">
        <!-- Hero Section -->
        <section class="service-hero">
            <div class="container text-center">
                <div class="badge-service mb-3" data-aos="fade-down">
                    <span class="badge rounded-pill bg-white bg-opacity-25 text-white px-3 py-2 fw-700">
                        <i class="bi bi-search me-1"></i> SUIVI EN LIGNE
                    </span>
                </div>
                <h1 class="display-3 fw-900 text-white mb-4" data-aos="fade-up">Suivre ma demande</h1>
                <p class="lead text-white-50 mx-auto" style="max-width: 700px;" data-aos="fade-up" data-aos-delay="100">
                    Saisissez votre numéro de référence pour consulter l'avancement de votre dossier administratif.
                </p>
            </div>
        </section>

        <!-- Search Content -->
        <section class="py-5">
            <div class="container pb-5">
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="search-card overflow-hidden" data-aos="zoom-in">
                            <div class="card-top-bar bg-primary" style="height: 8px;"></div>
                            <div class="p-4 p-md-5">
                                <div class="text-center mb-5">
                                    <div class="icon-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3"
                                        style="width: 80px; height: 80px; border-radius: 25px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-search fs-2"></i>
                                    </div>
                                    <h3 class="fw-800 text-dark">Suivi de dossier</h3>
                                    <p class="text-muted">Accédez aux informations en temps réel</p>
                                </div>

                                <form method="POST" action="{{ route('recherche.demande') }}" class="needs-validation">
                                    @csrf
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label
                                                class="form-label fw-700 text-dark small text-uppercase tracking-wider">Référence
                                                de la demande</label>
                                            <span class="badge bg-light text-primary border rounded-pill px-3">Format :
                                                REFXXXXXX</span>
                                        </div>
                                        <input type="text" class="form-control form-control-luxe"
                                            id="reference_naissance" name="reference_naissance"
                                            placeholder="Entrez votre numéro de référence..." required>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 fw-800 shadow-sm pulse-soft">
                                        RECHERCHER MON DOSSIER <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </form>

                                <div id="resultat-recherche" class="mt-5">
                                    @if (isset($etatDemande))
                                        <hr class="my-5 opacity-10">
                                        <div class="result-display fade-in">
                                            @if ($etatDemande)
                                                <div class="status-box p-4 rounded-4 bg-light">
                                                    <div class="d-flex align-items-center mb-4">
                                                        <div class="status-badge bg-primary text-white p-3 rounded-4 me-3">
                                                            <i class="bi bi-file-earmark-check fs-3"></i>
                                                        </div>
                                                        <div>
                                                            <h5 class="fw-800 mb-0 text-primary">Dossier trouvé</h5>
                                                            <p class="text-muted small mb-0">Réf:
                                                                {{ request('reference_naissance') }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-white rounded-3 border">
                                                                <span class="text-muted small fw-600 d-block mb-1">ÉTAT DU
                                                                    DOSSIER</span>
                                                                <span class="fw-800 text-primary">{{ $etatDemande }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="p-3 bg-white rounded-3 border">
                                                                <span class="text-muted small fw-600 d-block mb-1">MODE DE
                                                                    LIVRAISON</span>
                                                                <span class="fw-800 text-dark">{{ $statutDemande }}</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-4">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="fw-700 small">Progression</span>
                                                            <span class="badge bg-primary fw-800">85%</span>
                                                        </div>
                                                        <div class="progress rounded-pill bg-white shadow-sm"
                                                            style="height: 12px; border: 1px solid #eee;">
                                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                                                style="width: 85%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center p-5 rounded-4 bg-danger bg-opacity-10">
                                                    <i class="bi bi-search-heart text-danger display-4 mb-3 d-block"></i>
                                                    <h5 class="fw-800 text-danger">AUCUNE RÉFÉRENCE TROUVÉE</h5>
                                                    <p class="text-muted mb-0">Vérifiez votre numéro de suivi ou contactez
                                                        le support technique de la mairie.</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
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
            padding: 200px 0 140px;
            border-radius: 0 0 100px 100px;
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
            padding: 15px 30px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(31, 64, 131, 0.2);
        }

        .search-card {
            background: white;
            border-radius: 40px;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            margin-top: -100px;
        }

        .form-control-luxe {
            background: #f8fafc !important;
            border: 2px solid #f1f5f9 !important;
            border-radius: 15px !important;
            padding: 18px 25px !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .form-control-luxe:focus {
            background: #fff !important;
            border-color: #1f4083 !important;
            box-shadow: 0 0 0 5px rgba(31, 64, 131, 0.1) !important;
        }

        .status-tracker {
            position: relative;
            padding: 20px 0;
        }

        .status-dot {
            width: 15px;
            height: 15px;
            background: #cbd5e1;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }

        .status-active .status-dot {
            background: #1f4083;
            box-shadow: 0 0 0 4px rgba(31, 64, 131, 0.2);
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

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .pulse-soft {
            animation: pulse 2s infinite ease-in-out;
        }

        @media (max-width: 991px) {
            .service-hero {
                padding: 120px 0 60px;
                border-radius: 0 0 40px 40px;
            }

            .display-3 {
                font-size: 2.22rem !important;
            }

            .search-card {
                border-radius: 25px;
                margin-top: -40px;
            }

            .p-4.p-md-5 {
                padding: 1.5rem !important;
            }

            .icon-circle {
                width: 60px !important;
                height: 60px !important;
            }

            .form-control-luxe {
                padding: 15px !important;
            }
        }

        @media (max-width: 768px) {
            .display-3 {
                font-size: 2rem !important;
            }
        }
    </style>
@endpush
