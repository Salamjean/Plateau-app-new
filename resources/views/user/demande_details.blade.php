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
            width: 98%;
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
        @php
            $backRoute = route('user.history');
            $backLabel = 'Historique';
            if (isset($type)) {
                if ($type === 'naissance') {
                    $backRoute = route('user.extrait.index');
                    $backLabel = 'Mes Naissances';
                } elseif ($type === 'deces') {
                    $backRoute = route('user.extrait.deces.index');
                    $backLabel = 'Mes Décès';
                } elseif ($type === 'mariage') {
                    $backRoute = route('user.extrait.mariage.index');
                    $backLabel = 'Mes Mariages';
                }
            }
        @endphp
        <div class="header-section d-flex justify-content-between align-items-center mb-4">
            <div class="flex-grow-1 pr-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0 mb-1">
                        <li class="breadcrumb-item"><a href="{{ $backRoute }}" class="text-muted">{{ $backLabel }}</a>
                        </li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">Détails</li>
                    </ol>
                </nav>
                <h2 class="demande-title font-weight-800 text-navy mb-0">Demande #{{ $demande->reference }}</h2>
            </div>
            <a href="{{ $backRoute }}" class="btn btn-white shadow-sm border-0 px-4 flex-shrink-0"
                style="border-radius: 12px; height: fit-content;">
                <i class="fas fa-times"></i>
            </a>
        </div>

        <!-- Rangée 1 : Stepper (Suivi traitement) et Agent Instructeur -->
        <div class="row mb-4">
            <!-- Stepper de statut -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="glass-card-modern p-4 h-100">
                    <h6 class="text-navy font-weight-800 text-uppercase x-small mb-4"
                        style="letter-spacing: 1px; font-size: 0.8rem;">
                        <i class="fas fa-tasks mr-1 text-primary"></i> Suivi du traitement
                    </h6>
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

                    <div class="status-timeline mb-2">
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
                        <div class="alert alert-danger border-0 mb-0 mt-3" style="border-radius: 15px;">
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
            </div>

            <!-- Agent d'instruction -->
            <div class="col-lg-4">
                <div class="glass-card-modern p-4 text-center h-100 d-flex flex-column justify-content-center">
                    <h6 class="text-navy font-weight-800 text-uppercase x-small mb-4"
                        style="letter-spacing: 1px; font-size: 0.8rem;">
                        <i class="fas fa-user-shield mr-1 text-primary"></i> Agent Instructeur
                    </h6>

                    @if ($demande->agent)
                        <div class="agent-avatar mb-3 shadow-sm mx-auto">
                            {{ substr($demande->agent->name, 0, 1) }}
                        </div>
                        <h6 class="font-weight-800 text-navy mb-1" style="font-size: 0.95rem;">{{ $demande->agent->name }}
                        </h6>
                        <p class="small text-muted mb-3" style="font-size: 0.75rem;">Agent instructeur assigné</p>

                        <div class="p-2 px-3 bg-light rounded-pill d-inline-block shadow-sm mx-auto">
                            <span class="small font-weight-bold text-primary" style="font-size: 0.75rem;">
                                <i class="fas fa-user-check mr-2"></i>Dossier en main
                            </span>
                        </div>
                    @else
                        <div
                            class="agent-avatar mb-3 shadow-sm bg-light text-muted mx-auto d-flex align-items-center justify-content-center">
                            <i class="fas fa-user-clock text-muted"></i>
                        </div>
                        <h6 class="font-weight-800 text-navy mb-1" style="font-size: 0.95rem;">Attribution en cours</h6>
                        <p class="small text-muted mb-0" style="font-size: 0.75rem;">Votre dossier va être assigné à un
                            agent d'ici peu.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Rangée 2 : Nature de l'acte et Mode de récupération -->
        <div class="row">
            <!-- Informations de la demande -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="glass-card-modern p-4 h-100 d-flex flex-column">
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
                    <div class="info-group mb-0">
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

                    <!-- Note importante -->
                    <div class="mt-auto pt-3 border-top"
                        style="border-top-style: dashed !important; border-top-color: #cbd5e1 !important;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-info-circle text-warning mr-2"></i>
                            <span class="x-small font-weight-800 text-uppercase text-muted" style="font-size: 0.75rem;">Note
                                importante</span>
                        </div>
                        <p class="x-small text-muted leading-relaxed mb-0" style="font-size: 0.75rem;">
                            Le délai de traitement standard est de 4 heures après validation de votre paiement.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mode de récupération (Récupération du document) -->
            <div class="col-lg-4">
                <div class="glass-card-modern p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="text-navy font-weight-800 text-uppercase x-small mb-3"
                            style="letter-spacing: 1px; font-size: 0.8rem;">
                            <i class="fas fa-truck mr-1 text-primary"></i> Récupération du document
                        </h6>

                        <div class="delivery-box p-3"
                            style="background: {{ $demande->choix_option == 'livraison' ? 'rgba(31, 64, 131, 0.05)' : 'rgba(16, 185, 129, 0.05)' }}; border-left: 4px solid {{ $demande->choix_option == 'livraison' ? 'var(--primary-navy)' : '#10b981' }}; border-radius: 16px;">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-white shadow-sm p-2 rounded-circle text-primary d-flex align-items-center justify-content-center"
                                    style="width: 42px; height: 42px; min-width: 42px;">
                                    <i
                                        class="fas {{ $demande->choix_option == 'livraison' ? 'fa-motorcycle' : 'fa-university text-success' }} fa-md"></i>
                                </div>
                                <div class="ml-3">
                                    <span class="data-value d-block font-weight-800 text-navy"
                                        style="font-size: 0.95rem;">
                                        {{ $demande->choix_option == 'livraison' ? 'Livraison à domicile' : 'Retrait en Mairie' }}
                                    </span>
                                    <span class="small text-muted d-block" style="font-size: 0.75rem;">
                                        {{ $demande->choix_option == 'livraison' ? 'Via coursier express' : 'Au guichet unique d\'état civil' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if ($demande->choix_option == 'livraison')
                            <div class="mt-3 pt-3 border-top"
                                style="border-top-style: dashed !important; border-top-color: #cbd5e1 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="font-weight-800 text-navy mb-0"
                                        style="font-size: 0.8rem; letter-spacing: 0.5px; text-transform: uppercase;">
                                        <i class="fas fa-map-marked-alt mr-1"></i> Infos de Livraison
                                    </h6>
                                    <!-- @php
                                        $statutLiv = strtolower($demande->statut_livraison ?? 'en attente');
                                    @endphp
                                    @if (!in_array($statutLiv, ['en cours', 'livré', 'livrée']))
                                        <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm"
                                            id="btn-edit-livraison" data-prenom="{{ $demande->prenom_destinataire }}"
                                            data-nom="{{ $demande->nom_destinataire }}"
                                            data-contact="{{ $demande->contact_destinataire }}"
                                            data-email="{{ $demande->email_destinataire }}"
                                            data-adresse="{{ $demande->adresse_livraison }}"
                                            data-quartier="{{ $demande->quartier }}"
                                            data-commune="{{ $demande->commune_livraison }}"
                                            data-date="{{ $demande->date_livraison }}"
                                            data-heure="{{ $demande->heure_livraison }}"
                                            style="border-radius: 8px; font-weight: 700; font-size: 0.75rem; background: var(--primary-navy); border-color: var(--primary-navy); padding-top: 4px; padding-bottom: 4px;">
                                            <i class="fas fa-edit mr-1"></i> Modifier
                                        </button>
                                    @endif -->
                                </div>

                                <div class="mb-2">
                                    <span class="data-label" style="font-size: 0.7rem; color: var(--text-muted);"><i
                                            class="fas fa-user-alt mr-1"></i> Réceptionnaire</span>
                                    <span class="data-value text-capitalize font-weight-700"
                                        style="font-size: 0.85rem; color: var(--text-main);">
                                        {{ $demande->prenom_destinataire }} {{ $demande->nom_destinataire }}
                                    </span>
                                </div>

                                <div class="mb-2 mt-2">
                                    <span class="data-label" style="font-size: 0.7rem; color: var(--text-muted);"><i
                                            class="fas fa-phone-alt mr-1"></i> Téléphone</span>
                                    <span class="data-value font-weight-700"
                                        style="font-size: 0.85rem; color: var(--text-main);">
                                        {{ $demande->contact_destinataire ?? 'N/A' }}
                                    </span>
                                </div>

                                @if ($demande->email_destinataire)
                                    <div class="mb-2 mt-2">
                                        <span class="data-label" style="font-size: 0.7rem; color: var(--text-muted);"><i
                                                class="fas fa-envelope mr-1"></i> Email</span>
                                        <span class="data-value font-weight-700"
                                            style="font-size: 0.85rem; color: var(--text-main); word-break: break-all;">
                                            {{ $demande->email_destinataire }}
                                        </span>
                                    </div>
                                @endif

                                <div class="mb-2 mt-2">
                                    <span class="data-label" style="font-size: 0.7rem; color: var(--text-muted);"><i
                                            class="fas fa-map-marker-alt mr-1"></i> Adresse</span>
                                    <span class="data-value font-weight-700"
                                        style="font-size: 0.85rem; color: var(--text-main); line-height: 1.3; display: block;">
                                        {{ $demande->adresse_livraison }}
                                        @if ($demande->quartier)
                                            ({{ $demande->quartier }})
                                        @endif
                                        @if ($demande->commune_livraison)
                                            - {{ $demande->commune_livraison }}
                                        @endif
                                    </span>
                                </div>

                                <div class="mb-2 mt-2">
                                    <span class="data-label" style="font-size: 0.7rem; color: var(--text-muted);"><i
                                            class="fas fa-calendar-day mr-1"></i> Date & Heure souhaitées</span>
                                    <span class="data-value font-weight-700"
                                        style="font-size: 0.85rem; color: var(--text-main);">
                                        @if ($demande->date_livraison)
                                            {{ \Carbon\Carbon::parse($demande->date_livraison)->format('d/m/Y') }}
                                            @if ($demande->heure_livraison)
                                                à {{ \Carbon\Carbon::parse($demande->heure_livraison)->format('H:i') }}
                                            @endif
                                        @else
                                            Dès que prêt
                                        @endif
                                    </span>
                                </div>

                                <div class="mb-2 mt-3 p-2 rounded-lg d-flex justify-content-between align-items-center"
                                    style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <span class="font-weight-700 text-muted" style="font-size: 0.75rem;"><i
                                            class="fas fa-coins mr-1 text-success"></i> Frais de port :</span>
                                    <span class="font-weight-800 text-success" style="font-size: 0.85rem;">
                                        {{ number_format($demande->montant_livraison ?? 1500, 0, ',', ' ') }} FCFA
                                    </span>
                                </div>

                                <div class="p-2 rounded-lg d-flex justify-content-between align-items-center"
                                    style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <span class="font-weight-700 text-muted" style="font-size: 0.75rem;"><i
                                            class="fas fa-shipping-fast mr-1 text-primary"></i> Statut envoi :</span>
                                    @php
                                        $statutLiv = strtolower($demande->statut_livraison ?? 'en attente');
                                        $badgeCol = '#64748b';
                                        $lblText = 'En attente';
                                        if ($statutLiv === 'en cours') {
                                            $badgeCol = '#f59e0b';
                                            $lblText = 'En cours';
                                        } elseif ($statutLiv === 'livré' || $statutLiv === 'livrée') {
                                            $badgeCol = '#10b981';
                                            $lblText = 'Livré';
                                        } elseif ($statutLiv === 'annulé' || $statutLiv === 'annulée') {
                                            $badgeCol = '#ef4444';
                                            $lblText = 'Annulé';
                                        }
                                    @endphp
                                    <span class="badge py-1 px-2 text-white font-weight-800 text-uppercase"
                                        style="background-color: {{ $badgeCol }}; font-size: 0.65rem; border-radius: 6px; letter-spacing: 0.5px;">
                                        {{ $lblText }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 pt-3 border-top"
                                style="border-top-style: dashed !important; border-top-color: #cbd5e1 !important;">
                                <div style="background: rgba(31, 64, 131, 0.04); border: 1px dashed rgba(31, 64, 131, 0.15); padding: 14px; border-radius: 16px;"
                                    class="small">
                                    <p class="text-navy font-weight-800 mb-2" style="font-size: 0.8rem;"><i
                                            class="fas fa-id-card mr-1 text-primary"></i> Retrait physique :</p>
                                    <ul class="pl-3 mb-0 text-muted"
                                        style="list-style-type: square; font-size: 0.75rem; line-height: 1.4;">
                                        <li class="mb-1.5">Présentez-vous au guichet unique de l'état civil.</li>
                                        <li class="mb-1.5">Munissez-vous de votre <strong>pièce d'identité
                                                originale</strong>.
                                        </li>
                                        <li>Fournissez le numéro de référence unique :
                                            <strong>{{ $demande->reference }}</strong>.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclure SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#btn-edit-livraison').on('click', function() {
                    const btn = $(this);
                    const prenom = btn.data('prenom') || '';
                    const nom = btn.data('nom') || '';
                    const contact = btn.data('contact') || '';
                    const email = btn.data('email') || '';
                    const adresse = btn.data('adresse') || '';
                    const quartier = btn.data('quartier') || '';
                    const commune = btn.data('commune') || '';
                    const dateLiv = btn.data('date') || '';
                    const heureLiv = btn.data('heure') || '';

                    Swal.fire({
                        title: '<h5 class="font-weight-800 text-navy mb-0">Modifier les infos de livraison</h5>',
                        html: `
                        <form id="edit-livraison-form" class="text-left py-2" style="max-height: 450px; overflow-y: auto; overflow-x: hidden; padding-right: 5px;">
                            <div class="row mx-0">
                                <div class="form-group col-md-6 mb-3 px-1">
                                    <label class="font-weight-bold mb-1 text-navy" style="font-size: 0.8rem;"><i class="fas fa-user-alt mr-1 text-primary"></i> Nom</label>
                                    <input type="text" id="swal-nom" class="form-control rounded-lg" style="font-size: 0.85rem; border: 1px solid #cbd5e1;" value="${nom}">
                                </div>
                                <div class="form-group col-md-6 mb-3 px-1">
                                    <label class="font-weight-bold mb-1 text-navy" style="font-size: 0.8rem;"><i class="fas fa-user-alt mr-1 text-primary"></i> Prénom</label>
                                    <input type="text" id="swal-prenom" class="form-control rounded-lg" style="font-size: 0.85rem; border: 1px solid #cbd5e1;" value="${prenom}">
                                </div>
                            </div>
                            <div class="row mx-0">
                                <div class="form-group col-md-6 mb-3 px-1">
                                    <label class="font-weight-bold mb-1 text-navy" style="font-size: 0.8rem;"><i class="fas fa-phone mr-1 text-primary"></i> Téléphone</label>
                                    <input type="text" id="swal-contact" class="form-control rounded-lg" style="font-size: 0.85rem; border: 1px solid #cbd5e1;" value="${contact}">
                                </div>
                                <div class="form-group col-md-6 mb-3 px-1">
                                    <label class="font-weight-bold mb-1 text-navy" style="font-size: 0.8rem;"><i class="fas fa-envelope mr-1 text-primary"></i> Email (Optionnel)</label>
                                    <input type="email" id="swal-email" class="form-control rounded-lg" style="font-size: 0.85rem; border: 1px solid #cbd5e1;" value="${email}">
                                </div>
                            </div>
                            <div class="form-group mb-3 px-1">
                                <label class="font-weight-bold mb-1 text-navy" style="font-size: 0.8rem;"><i class="fas fa-map-marker-alt mr-1 text-primary"></i> Adresse de livraison</label>
                                <input type="text" id="swal-adresse" class="form-control rounded-lg" style="font-size: 0.85rem; border: 1px solid #cbd5e1;" value="${adresse}">
                            </div>
                            <div class="row mx-0">
                                <div class="form-group col-md-6 mb-1 px-1">
                                    <label class="font-weight-bold mb-1 text-navy" style="font-size: 0.8rem;"><i class="fas fa-calendar-day mr-1 text-primary"></i> Date souhaitée</label>
                                    <input type="date" id="swal-date" class="form-control rounded-lg" style="font-size: 0.85rem; border: 1px solid #cbd5e1;" value="${dateLiv}">
                                </div>
                                <div class="form-group col-md-6 mb-1 px-1">
                                    <label class="font-weight-bold mb-1 text-navy" style="font-size: 0.8rem;"><i class="fas fa-clock mr-1 text-primary"></i> Heure souhaitée</label>
                                    <input type="time" id="swal-heure" class="form-control rounded-lg" style="font-size: 0.85rem; border: 1px solid #cbd5e1;" value="${heureLiv}">
                                </div>
                            </div>
                        </form>
                    `,
                        showCancelButton: true,
                        confirmButtonText: 'Enregistrer',
                        cancelButtonText: 'Annuler',
                        confirmButtonColor: '#1f4083',
                        cancelButtonColor: '#64748b',
                        focusConfirm: false,
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'rounded-lg px-4',
                            cancelButton: 'rounded-lg px-4'
                        },
                        didOpen: () => {
                            const addressInput = document.getElementById('swal-adresse');
                            if (addressInput && typeof google !== 'undefined' && google.maps && google.maps.places) {
                                new google.maps.places.Autocomplete(addressInput, {
                                    types: ['geocode', 'establishment']
                                });
                            }
                        },
                        preConfirm: () => {
                            const prenom_destinataire = $('#swal-prenom').val().trim();
                            const nom_destinataire = $('#swal-nom').val().trim();
                            const contact_destinataire = $('#swal-contact').val().trim();
                            const email_destinataire = $('#swal-email').val().trim();
                            const adresse_livraison = $('#swal-adresse').val().trim();
                            const date_livraison = $('#swal-date').val().trim();
                            const heure_livraison = $('#swal-heure').val().trim();

                            if (!prenom_destinataire || !nom_destinataire || !
                                contact_destinataire || !adresse_livraison) {
                                Swal.showValidationMessage(
                                    'Veuillez remplir les champs obligatoires (Nom, Prénom, Téléphone et Adresse)'
                                );
                                return false;
                            }

                            return {
                                prenom_destinataire,
                                nom_destinataire,
                                contact_destinataire,
                                email_destinataire,
                                adresse_livraison,
                                quartier: '',
                                commune_livraison: '',
                                date_livraison,
                                heure_livraison
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Enregistrement...',
                                text: 'Veuillez patienter pendant la mise à jour des informations.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            console.log("Envoi de la requête AJAX vers la route relative...");
                            $.ajax({
                                url: '{{ route('user.demande.update-livraison', ['type' => $type, 'id' => $demande->id], false) }}',
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    prenom_destinataire: result.value.prenom_destinataire,
                                    nom_destinataire: result.value.nom_destinataire,
                                    contact_destinataire: result.value.contact_destinataire,
                                    email_destinataire: result.value.email_destinataire,
                                    adresse_livraison: result.value.adresse_livraison,
                                    date_livraison: result.value.date_livraison,
                                    heure_livraison: result.value.heure_livraison
                                },
                                success: function(response) {
                                    console.log("Réponse AJAX reçue avec succès :",
                                        response);
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Mis à jour !',
                                            text: response.message,
                                            confirmButtonColor: '#10b981',
                                            confirmButtonText: 'OK'
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Erreur',
                                            text: response.message ||
                                                'Une erreur est survenue.',
                                            confirmButtonColor: '#1f4083'
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    console.error("Erreur AJAX rencontrée :", xhr);
                                    let errorsMsg =
                                        'Une erreur est survenue lors de la mise à jour.';
                                    try {
                                        if (xhr.status === 419) {
                                            errorsMsg =
                                                "Votre session a expiré. Veuillez rafraîchir la page et réessayer.";
                                        } else if (xhr.responseJSON && xhr.responseJSON
                                            .errors) {
                                            let errs = xhr.responseJSON.errors;
                                            if (typeof errs === 'object') {
                                                errorsMsg = Object.values(errs)
                                                    .map(err => {
                                                        if (Array.isArray(err)) {
                                                            return err.join('<br>');
                                                        }
                                                        return String(err);
                                                    }).join('<br>');
                                            } else {
                                                errorsMsg = String(errs);
                                            }
                                        } else if (xhr.responseJSON && xhr.responseJSON
                                            .message) {
                                            errorsMsg = xhr.responseJSON.message;
                                        } else if (xhr.responseText) {
                                            errorsMsg = xhr.responseText.substring(0, 300);
                                        }
                                    } catch (e) {
                                        console.error(
                                            "Erreur lors de l'analyse du message d'erreur :",
                                            e);
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erreur de validation',
                                        html: errorsMsg,
                                        confirmButtonColor: '#1f4083'
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
