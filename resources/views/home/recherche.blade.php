@extends('home.layouts.main')

@section('content')
    <div class="search-tracking-page">
        <!-- Hero Section -->
        <section class="service-hero">
            <div class="container text-center">
                <div class="badge-service mb-3" data-aos="fade-down">
                    <span class="badge rounded-pill bg-white bg-opacity-25 text-white px-3 py-2 fw-700">
                        <i class="bi bi-house-door me-1"></i> SUIVI EN LIGNE
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
            <div class="container-fluid px-10percent pb-5">
                <div class="row g-4 search-cards-row">
                    <!-- Colonne Gauche : Formulaire de recherche -->
                    <div class="col-lg-5 d-flex flex-column" data-aos="fade-right">
                        <div class="search-card overflow-hidden flex-grow-1">
                            <div class="card-top-bar bg-primary" style="height: 8px;"></div>
                            <div class="p-4 p-md-5">
                                <div class="text-center mb-5">
                                    <div class="icon-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3"
                                        style="width: 80px; height: 80px; border-radius: 25px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-search fs-2 text-white"></i>
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
                                                ANXXXXPXXXX</span>
                                        </div>
                                        <input type="text" class="form-control form-control-luxe"
                                            id="reference_naissance" name="reference_naissance"
                                            value="{{ old('reference_naissance', $reference ?? '') }}"
                                            placeholder="Entrez votre numéro de référence..." required>
                                    </div>

                                    <button type="submit" class="btn w-100 py-3 rounded-4 fw-700 shadow-sm pulse-soft"
                                        style="background-color: #103a83; color: #ffffff; border: none; font-size: 0.95rem; transition: all 0.3s ease;">
                                        SUIVRE MON DOSSIER <i class="bi bi-arrow-right ms-2"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Colonne Droite : Résultats ou Guide d'accueil -->
                    <div class="col-lg-7 d-flex flex-column" data-aos="fade-left">
                        @if (!isset($etatDemande))
                            <!-- État initial : Carte de bienvenue premium -->
                            <div class="search-card overflow-hidden flex-grow-1 bg-white d-flex align-items-center"
                                style="position: relative;">
                                <div class="card-top-bar bg-primary"
                                    style="height: 8px; position: absolute; top: 0; left: 0; right: 0;"></div>
                                <div class="p-4 p-md-5 text-center w-100">
                                    <div class="illustration-box mb-4">
                                        <i class="bi bi-file-earmark-medical text-primary display-2 animate-bounce"
                                            style="display: inline-block;"></i>
                                    </div>
                                    <h4 class="fw-800 text-dark mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                                        Suivi de dossier en ligne</h4>
                                    <p class="text-muted mx-auto"
                                        style="max-width: 450px; font-size: 0.95rem; line-height: 1.6;">
                                        Saisissez votre numéro de référence à gauche (ex: AN012345P8956) pour suivre en
                                        temps réel chaque étape de l'avancement de votre dossier administratif.
                                    </p>
                                    <div class="d-flex justify-content-center gap-3 mt-4 flex-wrap">
                                        <span class="badge bg-light text-primary border rounded-pill px-3 py-2 fw-600">
                                            <i class="bi bi-clock-history me-1"></i> Temps réel
                                        </span>
                                        <span class="badge bg-light text-success border rounded-pill px-3 py-2 fw-600">
                                            <i class="bi bi-shield-check me-1"></i> Sécurisé
                                        </span>
                                        <span class="badge bg-light text-info border rounded-pill px-3 py-2 fw-600">
                                            <i class="bi bi-lightning-charge me-1"></i> Sans déplacement
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @elseif ($etatDemande === false)
                            <!-- État : Aucun dossier trouvé -->
                            <div class="search-card overflow-hidden flex-grow-1 bg-white d-flex align-items-center"
                                style="position: relative;">
                                <div class="card-top-bar bg-danger"
                                    style="height: 8px; position: absolute; top: 0; left: 0; right: 0;"></div>
                                <div class="p-4 p-md-5 text-center w-100">
                                    <div class="icon-circle bg-danger bg-opacity-10 text-danger mx-auto mb-4"
                                        style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-search-heart display-5"></i>
                                    </div>
                                    <h4 class="fw-800 text-danger mb-3"
                                        style="font-family: 'Plus Jakarta Sans', sans-serif;">Aucune référence trouvée</h4>
                                    <p class="text-muted mx-auto mb-3"
                                        style="max-width: 450px; font-size: 0.95rem; line-height: 1.6;">
                                        Nous n'avons trouvé aucune demande correspondant à la référence <strong
                                            class="text-dark">"{{ $reference }}"</strong>.
                                    </p>
                                    <p class="text-muted small mb-0">Veuillez vérifier l'exactitude de votre numéro de suivi
                                        (respectez les majuscules et les chiffres) ou contactez le support technique de la
                                        mairie.</p>
                                </div>
                            </div>
                        @else
                            <!-- État : Dossier trouvé -->
                            @php
                                $progress = 10;
                                $progressClass = 'bg-primary';
                                $statusLabel = $etatDemande;
                                $statusDesc = '';

                                $etatNormalise = trim(strtolower($etatDemande));

                                switch ($etatNormalise) {
                                    case 'non_paye':
                                    case 'paiement_en_attente':
                                    case 'en attente de paiement':
                                    case 'paiement_echoue':
                                        $progress = 10;
                                        $progressClass = 'bg-warning text-dark';
                                        $statusLabel = 'En attente de paiement';
                                        $statusDesc =
                                            'Le paiement de votre demande n\'a pas encore été validé. Le traitement commencera dès réception du règlement.';
                                        break;
                                    case 'en attente':
                                        $progress = 40;
                                        $progressClass = 'bg-info text-dark';
                                        $statusLabel = 'Dossier reçu';
                                        $statusDesc =
                                            'Votre demande a bien été reçue et est en cours d\'analyse par nos agents.';
                                        break;
                                    case 'réçu':
                                    case 'recu':
                                        $progress = 75;
                                        $progressClass = 'bg-primary text-white';
                                        $statusLabel = 'En cours de traitement';
                                        $statusDesc =
                                            'Votre dossier est validé et le document est en cours de rédaction ou de signature.';
                                        break;
                                    case 'terminé':
                                    case 'termine':
                                        $progress = 100;
                                        $progressClass = 'bg-success text-white';
                                        $statusLabel = 'Terminé';
                                        $statusDesc =
                                            'Votre document officiel est prêt et disponible selon le mode de retrait ou livraison choisi.';
                                        break;
                                    case 'rejetée':
                                    case 'rejete':
                                    case 'rejetee':
                                        $progress = 100;
                                        $progressClass = 'bg-danger text-white';
                                        $statusLabel = 'Rejeté';
                                        $statusDesc =
                                            'Votre demande a été rejetée. Veuillez consulter le motif de rejet.';
                                        break;
                                    default:
                                        $progress = 50;
                                        $progressClass = 'bg-primary text-white';
                                        $statusLabel = $etatDemande;
                                        $statusDesc = 'Votre demande est en cours de traitement.';
                                }
                            @endphp
                            <div class="search-card overflow-hidden flex-grow-1 bg-white" style="position: relative;">
                                <div class="card-top-bar bg-primary"
                                    style="height: 8px; position: absolute; top: 0; left: 0; right: 0;"></div>
                                <div class="p-4 p-md-5">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="status-badge bg-primary text-white p-3 rounded-4 me-3">
                                            <i class="bi bi-file-earmark-check fs-3"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-800 mb-0 text-primary"
                                                style="font-family: 'Plus Jakarta Sans', sans-serif;">Dossier trouvé</h5>
                                            <p class="text-muted small mb-0">Réf: {{ $reference }}</p>
                                        </div>
                                    </div>

                                    <div class="row g-3 mb-4">
                                        <div class="col-sm-6">
                                            <div class="p-3 bg-light rounded-3 border">
                                                <span class="text-muted small fw-600 d-block mb-1 text-uppercase"
                                                    style="font-size: 0.75rem;">État actuel</span>
                                                <span class="fw-800 text-primary"
                                                    style="font-size: 0.95rem;">{{ $statusLabel }}</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="p-3 bg-light rounded-3 border">
                                                <span class="text-muted small fw-600 d-block mb-1 text-uppercase"
                                                    style="font-size: 0.75rem;">Mode de livraison</span>
                                                <span class="fw-800 text-dark"
                                                    style="font-size: 0.95rem;">{{ $statutDemande }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="fw-700 small">Progression</span>
                                            <span class="badge {{ $progressClass }} fw-800">{{ $progress }}%</span>
                                        </div>
                                        <div class="progress rounded-pill bg-light shadow-sm"
                                            style="height: 12px; border: 1px solid #eee;">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated {{ $progressClass }}"
                                                style="width: {{ $progress }}%"></div>
                                        </div>
                                        @if ($statusDesc)
                                            <div class="mt-2 text-muted small fw-600">
                                                <i class="bi bi-info-circle me-1"></i> {{ $statusDesc }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Stepper d'étapes de demande (3 étapes) -->
                                    <div class="step-tracker-container">
                                        <div class="step-tracker-line">
                                            @php
                                                $lineWidth = '0%';
                                                if (in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee'])) {
                                                    $lineWidth = '100%';
                                                } elseif ($progress == 40) {
                                                    $lineWidth = '0%';
                                                } elseif ($progress == 75) {
                                                    $lineWidth = '50%';
                                                } elseif ($progress == 100) {
                                                    $lineWidth = '100%';
                                                }
                                            @endphp
                                            <div class="step-tracker-line-fill"
                                                style="width: {{ $lineWidth }}; @if (in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee'])) background-color: #dc3545; @endif">
                                            </div>
                                        </div>
                                        <div class="step-tracker-steps">
                                            <!-- Étape 1: Réception -->
                                            @php
                                                $step1Class = 'pending';
                                                $step1Icon = 'bi-file-earmark-text';
                                                if (
                                                    $progress >= 75 ||
                                                    in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee'])
                                                ) {
                                                    $step1Class = 'completed';
                                                    $step1Icon = 'bi-check-circle-fill';
                                                } elseif ($progress == 40) {
                                                    $step1Class = 'active';
                                                    $step1Icon = 'bi-file-earmark-text-fill';
                                                }
                                            @endphp
                                            <div class="step-tracker-item {{ $step1Class }}">
                                                <div class="step-tracker-bubble">
                                                    <i class="bi {{ $step1Icon }}"></i>
                                                </div>
                                                <div class="step-tracker-title">Réception</div>
                                                <div class="step-tracker-desc text-muted">Dossier reçu et vérifié</div>
                                            </div>

                                            <!-- Étape 2: Traitement -->
                                            @php
                                                $step2Class = 'pending';
                                                $step2Icon = 'bi-gear';
                                                if (
                                                    $progress == 100 ||
                                                    in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee'])
                                                ) {
                                                    $step2Class = 'completed';
                                                    $step2Icon = 'bi-check-circle-fill';
                                                } elseif ($progress == 75) {
                                                    $step2Class = 'active';
                                                    $step2Icon = 'bi-gear-wide-connected';
                                                }
                                            @endphp
                                            <div class="step-tracker-item {{ $step2Class }}">
                                                <div class="step-tracker-bubble">
                                                    <i class="bi {{ $step2Icon }}"></i>
                                                </div>
                                                <div class="step-tracker-title">Traitement</div>
                                                <div class="step-tracker-desc text-muted">Rédaction et signatures</div>
                                            </div>

                                            <!-- Étape 3: Disponibilité / Rejet -->
                                            @php
                                                $step3Class = 'pending';
                                                $step3Icon = 'bi-box-seam';
                                                $step3Title = 'Disponibilité';
                                                $step3Desc = 'Prêt pour retrait / livraison';

                                                if (in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee'])) {
                                                    $step3Class = 'rejected';
                                                    $step3Icon = 'bi-x-circle-fill';
                                                    $step3Title = 'Rejeté';
                                                    $step3Desc = 'Dossier non validé';
                                                } elseif ($progress == 100) {
                                                    $step3Class = 'completed';
                                                    $step3Icon = 'bi-check-circle-fill';
                                                }
                                            @endphp
                                            <div class="step-tracker-item {{ $step3Class }}">
                                                <div class="step-tracker-bubble">
                                                    <i class="bi {{ $step3Icon }}"></i>
                                                </div>
                                                <div class="step-tracker-title">{{ $step3Title }}</div>
                                                <div class="step-tracker-desc text-muted">{{ $step3Desc }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Encadré en cas de rejet -->
                                    @if (in_array($etatNormalise, ['rejetée', 'rejete', 'rejetee']))
                                        <div class="alert alert-danger rounded-4 p-4 mt-4 border border-danger border-opacity-25 animate-fade-in"
                                            style="background-color: #fff5f5; border-left: 5px solid #dc3545 !important;">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-exclamation-triangle-fill text-danger fs-4 me-2"></i>
                                                <h5 class="fw-800 mb-0 text-danger"
                                                    style="font-family: 'Plus Jakarta Sans', sans-serif;">Demande rejetée
                                                </h5>
                                            </div>
                                            <p class="text-muted small mb-3">Votre demande n'a pas pu être validée par les
                                                services municipaux. Veuillez consulter le motif ci-dessous :</p>
                                            <div class="bg-white p-3 rounded-3 border border-danger border-opacity-10 text-dark font-monospace small"
                                                style="white-space: pre-line; line-height: 1.5; font-weight: 500;">
                                                {!! nl2br(
                                                    e(
                                                        $demande
                                                            ? $demande->motif_de_rejet
                                                            : 'Aucun motif de rejet spécifique fourni. Veuillez contacter le support de la mairie.',
                                                    ),
                                                ) !!}
                                            </div>
                                            <div class="mt-3 text-muted small">
                                                <i class="bi bi-info-circle-fill me-1 text-primary"></i> Connectez-vous à
                                                votre compte citoyen pour modifier et soumettre à nouveau ce dossier.
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
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

        .search-cards-row {
            margin-top: -100px;
            position: relative;
            z-index: 10;
        }

        .search-card {
            background: white;
            border-radius: 40px;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
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

        /* --- STEP TRACKER SYSTEM --- */
        .step-tracker-container {
            margin-top: 3rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding: 0 10px;
        }

        .step-tracker-line {
            position: absolute;
            top: 22px;
            left: 16.66%;
            right: 16.66%;
            height: 4px;
            background-color: #e2e8f0;
            z-index: 1;
            border-radius: 2px;
        }

        .step-tracker-line-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #1f4083;
            transition: width 0.5s ease-in-out;
            z-index: 2;
            border-radius: 2px;
        }

        .step-tracker-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 3;
        }

        .step-tracker-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 33.33%;
        }

        .step-tracker-bubble {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background-color: #ffffff;
            border: 3px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: #94a3b8;
            margin-bottom: 0.75rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Completed Step */
        .step-tracker-item.completed .step-tracker-bubble {
            background-color: #dcfce7;
            border-color: #22c55e;
            color: #15803d;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.15);
        }

        .step-tracker-item.completed .step-tracker-title {
            color: #15803d;
            font-weight: 800;
        }

        /* Active Step */
        .step-tracker-item.active .step-tracker-bubble {
            background-color: #e0f2fe;
            border-color: #0284c7;
            color: #0284c7;
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15);
            animation: pulse-border 2s infinite;
        }

        .step-tracker-item.active .step-tracker-title {
            color: #0284c7;
            font-weight: 800;
        }

        /* Rejected Step */
        .step-tracker-item.rejected .step-tracker-bubble {
            background-color: #fee2e2;
            border-color: #ef4444;
            color: #b91c1c;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
        }

        .step-tracker-item.rejected .step-tracker-title {
            color: #b91c1c;
            font-weight: 800;
        }

        /* Pending Step */
        .step-tracker-item.pending .step-tracker-bubble {
            background-color: #ffffff;
            border-color: #e2e8f0;
            color: #cbd5e1;
        }

        .step-tracker-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            color: #475569;
            margin-bottom: 0.25rem;
            transition: color 0.3s;
        }

        .step-tracker-desc {
            font-size: 0.7rem;
            color: #64748b;
            max-width: 140px;
            line-height: 1.3;
            font-weight: 500;
        }

        @keyframes pulse-border {
            0% {
                box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.4);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(2, 132, 199, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(2, 132, 199, 0);
            }
        }

        @media (max-width: 576px) {
            .step-tracker-line {
                display: none;
            }

            .step-tracker-steps {
                flex-direction: column;
                gap: 1.25rem;
                align-items: flex-start;
                padding-left: 1rem;
            }

            .step-tracker-item {
                flex-direction: row;
                text-align: left;
                width: 100%;
                gap: 1rem;
                align-items: center;
            }

            .step-tracker-bubble {
                margin-bottom: 0;
                flex-shrink: 0;
            }

            .step-tracker-desc {
                max-width: 100%;
            }
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
