@extends('user.layouts.template')

@section('content')
    <style>
        :root {
            --primary-navy: #1f4083;
            --accent-blue: #3b82f6;
            --bg-glass: rgba(255, 255, 255, 0.85);
            --border-glass: rgba(255, 255, 255, 0.4);
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        .details-container {
            padding: 2rem;
            width: 85%;
            margin: 0 auto;
        }

        .glass-card-modern {
            background: var(--bg-glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        /* Timeline Stepper */
        .status-timeline {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 3rem;
            padding: 0 1rem;
        }

        .status-timeline::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 25%;
        }

        .step-icon {
            width: 32px;
            height: 32px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .step-item.active .step-icon {
            background: var(--primary-navy);
            border-color: var(--primary-navy);
            color: white;
            box-shadow: 0 0 0 4px rgba(31, 64, 131, 0.1);
        }

        .step-item.completed .step-icon {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }

        .step-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: 0.3s;
        }

        .step-item.active .step-label {
            color: var(--primary-navy);
        }

        /* Data Groups */
        .info-group {
            margin-bottom: 2rem;
        }

        .group-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--primary-navy);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .group-title::after {
            content: '';
            height: 1px;
            flex-grow: 1;
            background: linear-gradient(to right, #cbd5e1, transparent);
        }

        .data-label {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
            display: block;
            font-weight: 600;
        }

        .data-value {
            font-size: 0.95rem;
            color: var(--text-main);
            font-weight: 700;
            word-break: break-word;
        }

        .type-badge {
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Specific styles for types */
        .badge-naissance {
            background: #eff6ff;
            color: #2563eb;
        }

        .badge-deces {
            background: #fef2f2;
            color: #dc2626;
        }

        .badge-mariage {
            background: #fdf2f8;
            color: #db2777;
        }

        .agent-avatar {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: var(--primary-navy);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 1rem;
        }

        .delivery-box {
            background: linear-gradient(135deg, rgba(31, 64, 131, 0.05) 0%, rgba(31, 64, 131, 0) 100%);
            border-radius: 20px;
            border-left: 4px solid var(--primary-navy);
        }

        @media (max-width: 991px) {
            .details-container {
                width: 95%;
            }
        }

        @media (max-width: 768px) {
            .details-container {
                padding: 1rem 0.5rem;
                width: 100%;
            }

            .glass-card-modern {
                padding: 1.5rem !important;
                border-radius: 20px;
            }

            .demande-title {
                font-size: 1.25rem !important;
                word-break: break-all;
            }

            .header-section {
                margin-bottom: 2rem !important;
            }

            .status-timeline {
                margin-bottom: 2.5rem;
                padding: 0;
            }

            .status-timeline::before {
                top: 13px;
            }

            .step-icon {
                width: 26px;
                height: 26px;
                font-size: 0.7rem;
            }

            .step-label {
                font-size: 0.55rem;
                display: none;
            }

            .step-item.active .step-label {
                display: block;
                position: absolute;
                top: 35px;
                white-space: nowrap;
            }

            .group-title {
                font-size: 0.8rem;
            }

            .data-value {
                font-size: 0.9rem;
            }

            .type-badge {
                padding: 6px 12px;
                font-size: 0.75rem;
            }

            .delivery-box {
                padding: 1rem !important;
            }

            .delivery-box .d-flex {
                flex-direction: column;
                align-items: center !important;
                text-align: center;
            }

            .delivery-box .ml-3 {
                margin-left: 0 !important;
                margin-top: 1rem;
            }

            .agent-avatar {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }
    </style>

    <div class="details-container animate-fade-in">
        <!-- Breadcrumb / Header -->
        <div class="header-section d-flex justify-content-between align-items-center mb-4">
            <div class="flex-grow-1 pr-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('user.history') }}" class="text-muted">Historique</a>
                        </li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Détails</li>
                    </ol>
                </nav>
                <h2 class="demande-title font-weight-800 text-navy mb-0">Demande #{{ $demande->reference }}</h2>
            </div>
            <a href="{{ route('user.history') }}" class="btn btn-white shadow-sm border-0 px-4 flex-shrink-0"
                style="border-radius: 12px; height: fit-content;">
                <i class="fas fa-times"></i>
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Status Stepper Card -->
                <div class="glass-card-modern p-4 mb-4">
                    @php
                        $etat = strtolower($demande->etat);
                        $steps = [
                            ['key' => 'en attente', 'label' => 'Soumis'],
                            ['key' => 'en cours', 'label' => 'Traitement'],
                            ['key' => 'terminé', 'label' => 'Prêt'],
                            ['key' => 'livré', 'label' => 'Remis'],
                        ];
                        // If rejected, we might want to handle it differently
                        $isRejected = $etat === 'rejeté' || $etat === 'rejetée';

                        $activeIndex = 0;
                        if ($etat === 'en cours' || $etat === 'réçu') {
                            $activeIndex = 1;
                        }
                        if ($etat === 'terminé') {
                            $activeIndex = 2;
                        }
                        if (
                            $demande->statut_livraison &&
                            str_contains(strtolower($demande->statut_livraison), 'livré')
                        ) {
                            $activeIndex = 3;
                        }
                    @endphp

                    <div class="status-timeline">
                        @foreach ($steps as $index => $step)
                            @php
                                $class = '';
                                if ($isRejected && $index > 0) {
                                    $class = 'disabled';
                                } elseif ($index < $activeIndex) {
                                    $class = 'completed';
                                } elseif ($index == $activeIndex) {
                                    $class = 'active';
                                }
                            @endphp
                            <div class="step-item {{ $class }}">
                                <div class="step-icon">
                                    @if ($class === 'completed')
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </div>
                                <span class="step-label">{{ $step['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($isRejected)
                        <div class="alert alert-danger border-0 mb-0 mt-2" style="border-radius: 15px;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle fa-lg mr-3"></i>
                                <div>
                                    <h6 class="font-weight-bold mb-1">Dossier rejeté</h6>
                                    <p class="small mb-0">Motif :
                                        {{ $demande->motif_annulation ?? ($demande->autre_motif_text ?? 'Informations incomplètes') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Detailed Information Card -->
                <div class="glass-card-modern p-4 mb-4">
                    <div class="info-group">
                        <h6 class="group-title"><i class="fas fa-info-circle"></i> Nature de l'acte</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <span class="data-label">Type de document</span>
                                <div class="type-badge badge-{{ $type }}">
                                    @switch($type)
                                        @case('naissance')
                                            <i class="fas fa-baby"></i> Naissance
                                        @break

                                        @case('deces')
                                            <i class="fas fa-cross"></i> Décès
                                        @break

                                        @case('mariage')
                                            <i class="fas fa-heart"></i> Mariage
                                        @break
                                    @endswitch
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <span class="data-label">Référence unique</span>
                                <span class="data-value"
                                    style="font-family: 'Courier New', monospace;">{{ $demande->reference }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic sections based on the type of certificate -->
                    <div class="info-group">
                        <h6 class="group-title"><i class="fas fa-user"></i> Informations du bénéficiaire</h6>
                        <div class="row">
                            @if ($type == 'naissance')
                                <div class="col-md-6 mb-3">
                                    <span class="data-label">Nom & Prénoms enfant</span>
                                    <span class="data-value">{{ $demande->name }} {{ $demande->prenom }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="data-label">Date de naissance</span>
                                    <span
                                        class="data-value">{{ $demande->DateR ? \Carbon\Carbon::parse($demande->DateR)->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                            @elseif($type == 'mariage')
                                <div class="col-md-6 mb-3">
                                    <span class="data-label">Nom de l'époux</span>
                                    <span class="data-value">{{ $demande->nomEpoux ?? ($demande->name ?? 'N/A') }}
                                        {{ $demande->prenomEpoux ?? ($demande->prenom ?? '') }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="data-label">Nom de l'épouse</span>
                                    <span class="data-value">{{ $demande->nomEpouse ?? 'N/A' }}
                                        {{ $demande->prenomEpouse ?? '' }}</span>
                                </div>
                            @elseif($type == 'deces')
                                <div class="col-md-6 mb-3">
                                    <span class="data-label">Nom du défunt</span>
                                    <span class="data-value">{{ $demande->name }} {{ $demande->prenom }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <span class="data-label">Date du décès</span>
                                    <span
                                        class="data-value">{{ $demande->date_deces ? \Carbon\Carbon::parse($demande->date_deces)->format('d/m/Y') : 'N/A' }}</span>
                                </div>
                            @endif

                            <div class="col-md-6 mb-3">
                                <span class="data-label">Quantité demandée</span>
                                <span class="data-value">{{ $demande->quantite }} exemplaire(s)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery/Collection Section -->
                    <div class="info-group mb-0">
                        <h6 class="group-title"><i class="fas fa-truck"></i> Mode de récupération</h6>
                        <div class="delivery-box p-3">
                            <div class="d-flex align-items-start">
                                <div class="icon-circle bg-white shadow-sm p-3 rounded-circle text-primary flex-shrink-0"
                                    style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                    <i
                                        class="fas {{ $demande->choix_option == 'livraison' ? 'fa-motorcycle' : 'fa-university' }} fa-lg"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="data-value d-block mb-1">
                                        {{ $demande->choix_option == 'livraison' ? 'Livraison à domicile' : 'Retrait en Mairie' }}
                                    </span>
                                    @if ($demande->choix_option == 'livraison')
                                        <span class="small text-muted d-block">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            {{ $demande->adresse_livraison ?? 'Adresse non spécifiée' }}
                                        </span>
                                        <span class="small text-muted d-block">
                                            <i class="fas fa-phone mr-1"></i>
                                            {{ $demande->contact_destinataire ?? 'Contact non spécifié' }}
                                        </span>
                                    @else
                                        <span class="small text-muted d-block">
                                            Veuillez vous munir de votre pièce d'identité originale lors du retrait.
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Agent / Tracking Card -->
                <div class="glass-card-modern p-4 mb-4 text-center">
                    <h6 class="text-navy font-weight-800 text-uppercase x-small mb-4" style="letter-spacing: 1px;">Suivi du
                        traitement</h6>

                    @if ($demande->agent)
                        <div class="agent-avatar mb-3 shadow-sm">
                            {{ substr($demande->agent->name, 0, 1) }}
                        </div>
                        <h6 class="font-weight-800 text-navy mb-1">{{ $demande->agent->name }}</h6>
                        <p class="small text-muted mb-3">Agent instructeur assigné</p>

                        <div class="p-2 px-3 bg-light rounded-pill d-inline-block shadow-sm">
                            <span class="small font-weight-bold text-primary"><i class="fas fa-user-check mr-2"></i>Dossier
                                en main</span>
                        </div>
                    @else
                        <div class="agent-avatar mb-3 shadow-sm bg-light text-muted">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <h6 class="font-weight-800 text-navy mb-1">Attribution en cours</h6>
                        <p class="small text-muted mb-0">Votre dossier va être assigné à un agent d'ici peu.</p>
                    @endif
                </div>

                <!-- Quick Help / Support -->
                <div class="glass-card-modern p-4 text-white"
                    style="background: linear-gradient(135deg, var(--primary-navy) 0%, #173671 100%);">
                    <h5 class="font-weight-800 mb-3">Assistance</h5>
                    <p class="small opacity-8 mb-4">Une question sur votre demande ou sur le délai de traitement ? Nos
                        agents sont là pour vous aider.</p>
                    <a href="#" class="btn btn-white btn-block py-2 font-weight-700 shadow-sm"
                        style="border-radius: 12px; color: var(--primary-navy);">
                        <i class="fas fa-headset mr-2"></i> Ouvrir un ticket
                    </a>
                </div>

                <!-- Important Note -->
                <div class="mt-4 px-2">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-info-circle text-warning mr-2"></i>
                        <span class="x-small font-weight-800 text-uppercase text-muted">Note importante</span>
                    </div>
                    <p class="x-small text-muted leading-relaxed">
                        Le délai de traitement standard est de 24h à 48h jours ouvrés après validation de votre paiement.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
