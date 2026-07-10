@extends('comptable.layouts.template')

@section('content')
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Demandes en Ligne | Comptable</title>
        <link
            href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root {
                --primary: #1f4083;
                --primary-light: #eff6ff;
                --primary-hover: #1f4083;
                --success: #059669;
                --success-light: #ecfdf5;
                --warning: #d97706;
                --warning-light: #fffbeb;
                --danger: #dc2626;
                --danger-light: #fef2f2;
                --purple: #7c3aed;
                --purple-light: #f5f3ff;
                --pink: #db2777;
                --pink-light: #fdf2f8;
                --text-main: #0f172a;
                --text-muted: #64748b;
                --text-light: #94a3b8;
                --card-bg: #ffffff;
                --body-bg: #f1f5f9;
                --border: #e2e8f0;
                --radius-sm: 8px;
                --radius-md: 12px;
                --radius-lg: 16px;
                --radius-xl: 20px;
                --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
                --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.06), 0 2px 6px rgba(0, 0, 0, 0.04);
                --transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
            }

            * {
                box-sizing: border-box;
            }

            body {
                font-family: 'DM Sans', sans-serif;
                background-color: var(--body-bg);
                color: var(--text-main);
                margin: 0;
            }

            /* PAGE HEADER */
            .page-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                flex-wrap: wrap;
                gap: 1rem;
                margin-bottom: 1.75rem;
            }

            .page-title {
                font-size: 1.7rem;
                font-weight: 800;
                color: var(--text-main);
                margin: 0;
                letter-spacing: -0.6px;
            }

            .page-subtitle {
                font-size: 0.88rem;
                color: var(--text-muted);
                margin-top: 0.3rem;
                display: flex;
                align-items: center;
                gap: 0.4rem;
            }

            /* STATS */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1.25rem;
                margin-bottom: 1.75rem;
            }

            .stat-card {
                background: var(--card-bg);
                border-radius: var(--radius-lg);
                padding: 1.25rem 1.5rem;
                box-shadow: var(--shadow-sm);
                display: flex;
                align-items: center;
                gap: 1rem;
                border: 1px solid var(--border);
                transition: var(--transition);
            }

            .stat-card:hover {
                transform: translateY(-3px);
                box-shadow: var(--shadow-md);
            }

            .stat-icon {
                width: 46px;
                height: 46px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                flex-shrink: 0;
            }

            .stat-label {
                font-size: 0.72rem;
                font-weight: 700;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: 0.6px;
            }

            .stat-value {
                font-size: 1.45rem;
                font-weight: 800;
                color: var(--text-main);
                line-height: 1.1;
                font-family: 'DM Mono', monospace;
            }

            .stat-value small {
                font-size: 0.68rem;
                font-weight: 600;
                font-family: 'DM Sans', sans-serif;
                color: var(--text-muted);
            }

            /* ALERTS */
            .alert {
                padding: 0.85rem 1.2rem;
                border-radius: var(--radius-md);
                margin-bottom: 1.25rem;
                display: flex;
                align-items: center;
                gap: 0.6rem;
                font-size: 0.9rem;
                font-weight: 500;
            }

            .alert-success {
                background: var(--success-light);
                color: var(--success);
                border: 1px solid #a7f3d0;
            }

            .alert-error {
                background: var(--danger-light);
                color: var(--danger);
                border: 1px solid #fecaca;
            }

            /* MAIN CARD */
            .card-modern {
                background: var(--card-bg);
                border-radius: var(--radius-xl);
                box-shadow: var(--shadow-md);
                border: 1px solid var(--border);
                overflow: hidden;
            }

            /* TOOLBAR */
            .toolbar {
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid var(--border);
                display: flex;
                flex-direction: column;
                gap: 1rem;
                background: #fafbfc;
            }

            .toolbar-row {
                display: flex;
                flex-wrap: wrap;
                gap: 0.75rem;
                align-items: center;
                justify-content: space-between;
            }

            .toolbar-title {
                font-size: 1rem;
                font-weight: 700;
                color: var(--text-main);
                margin: 0;
            }

            /* TOGGLE */
            .toggle-group {
                display: inline-flex;
                background: var(--body-bg);
                border-radius: var(--radius-md);
                padding: 4px;
                border: 1px solid var(--border);
                gap: 2px;
            }

            .toggle-btn {
                padding: 0.5rem 1.1rem;
                border-radius: var(--radius-sm);
                border: none;
                background: transparent;
                font-family: 'DM Sans', sans-serif;
                font-size: 0.85rem;
                font-weight: 600;
                color: var(--text-muted);
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                transition: var(--transition);
                white-space: nowrap;
            }

            .toggle-btn.active {
                background: var(--card-bg);
                color: var(--text-main);
                box-shadow: var(--shadow-sm);
            }

            .toggle-btn.active.pending-tab {
                color: var(--warning);
            }

            .toggle-btn.active.treated-tab {
                color: var(--success);
            }

            .toggle-count {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 20px;
                height: 20px;
                border-radius: 6px;
                font-size: 0.72rem;
                font-weight: 700;
                padding: 0 4px;
            }

            .pending-tab .toggle-count {
                background: #fef3c7;
                color: var(--warning);
            }

            .treated-tab .toggle-count {
                background: var(--success-light);
                color: var(--success);
            }

            /* FILTERS */
            .filters-row {
                display: flex;
                flex-wrap: wrap;
                gap: 0.6rem;
                align-items: center;
            }

            .search-box {
                position: relative;
                flex: 1;
                min-width: 220px;
                max-width: 340px;
            }

            .search-input {
                width: 100%;
                padding: 0.6rem 1rem 0.6rem 2.5rem;
                border: 1px solid var(--border);
                border-radius: var(--radius-md);
                font-size: 0.88rem;
                font-family: 'DM Sans', sans-serif;
                background: var(--card-bg);
                color: var(--text-main);
                transition: var(--transition);
            }

            .search-input:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            }

            .search-input::placeholder {
                color: var(--text-light);
            }

            .search-icon {
                position: absolute;
                left: 0.85rem;
                top: 50%;
                transform: translateY(-50%);
                color: var(--text-light);
                font-size: 0.85rem;
            }

            .filter-select {
                padding: 0.6rem 2rem 0.6rem 0.85rem;
                border: 1px solid var(--border);
                border-radius: var(--radius-md);
                font-size: 0.85rem;
                font-family: 'DM Sans', sans-serif;
                background: var(--card-bg);
                color: var(--text-main);
                cursor: pointer;
                appearance: none;
                -webkit-appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 0.65rem center;
                transition: var(--transition);
            }

            .filter-select:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            }

            .btn-reset {
                padding: 0.6rem 1rem;
                border-radius: var(--radius-md);
                border: 1px solid var(--border);
                background: var(--card-bg);
                font-size: 0.82rem;
                font-family: 'DM Sans', sans-serif;
                font-weight: 600;
                color: var(--text-muted);
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 0.4rem;
                transition: var(--transition);
            }

            .btn-reset:hover {
                background: var(--body-bg);
                color: var(--text-main);
            }

            /* RESULT COUNT */
            .result-count {
                font-size: 0.8rem;
                color: var(--text-muted);
                font-weight: 500;
                padding: 0.6rem 1.5rem;
                border-bottom: 1px solid var(--border);
                background: #fafbfc;
            }

            .result-count span {
                font-weight: 700;
                color: var(--text-main);
            }

            /* TABLE */
            .table-wrapper {
                overflow-x: auto;
            }

            .table-modern {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
            }

            .table-modern th {
                padding: 0.85rem 1.25rem;
                font-size: 0.72rem;
                font-weight: 700;
                color: var(--text-muted);
                text-transform: uppercase;
                letter-spacing: 0.7px;
                border-bottom: 1px solid var(--border);
                background: #f8fafc;
                white-space: nowrap;
                text-align: center;
            }

            .table-modern td {
                padding: 1.1rem 1.25rem;
                vertical-align: middle;
                border-bottom: 1px solid #f1f5f9;
                font-size: 0.9rem;
                text-align: center;
                transition: background 0.15s;
            }

            .table-modern tr:hover td {
                background: #f8fafc;
            }

            .table-modern tr:last-child td {
                border-bottom: none;
            }

            /* BADGES */
            .badge {
                padding: 0.35em 0.75em;
                font-size: 0.72rem;
                font-weight: 700;
                border-radius: 6px;
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                letter-spacing: 0.2px;
            }

            .badge-naissance {
                background: var(--primary-light);
                color: var(--primary);
            }

            .badge-deces {
                background: var(--purple-light);
                color: var(--purple);
            }

            .badge-mariage {
                background: var(--pink-light);
                color: var(--pink);
            }

            .badge-pending {
                background: var(--warning-light);
                color: var(--warning);
            }

            .badge-done {
                background: var(--success-light);
                color: var(--success);
            }

            /* DEMANDEUR */
            .demandeur-name {
                font-weight: 600;
                color: var(--text-main);
            }

            .demandeur-contact {
                font-size: 0.75rem;
                color: var(--text-muted);
                margin-top: 2px;
            }

            /* DATE */
            .date-main {
                font-weight: 500;
            }

            .date-time {
                font-size: 0.75rem;
                color: var(--text-muted);
                margin-top: 2px;
                font-family: 'DM Mono', monospace;
            }

            /* QTY */
            .qty-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                border-radius: 8px;
                background: #f1f5f9;
                font-weight: 800;
                font-size: 0.95rem;
                color: var(--text-main);
                font-family: 'DM Mono', monospace;
            }

            /* BUTTONS */
            .btn-validate {
                background: var(--primary);
                color: white;
                border: none;
                padding: 0.55rem 1.1rem;
                border-radius: var(--radius-md);
                font-size: 0.85rem;
                font-weight: 600;
                font-family: 'DM Sans', sans-serif;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
                transition: var(--transition);
            }

            .btn-validate:hover {
                background: var(--primary-hover);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
            }

            .btn-done {
                background: transparent;
                color: var(--success);
                border: 1px solid #a7f3d0;
                padding: 0.5rem 1rem;
                border-radius: var(--radius-md);
                font-size: 0.85rem;
                font-weight: 600;
                font-family: 'DM Sans', sans-serif;
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
            }

            /* EMPTY STATE */
            .empty-state {
                text-align: center;
                padding: 4rem 1rem;
                color: var(--text-muted);
            }

            .empty-icon {
                font-size: 2.8rem;
                color: #e2e8f0;
                margin-bottom: 0.75rem;
            }

            .empty-state h3 {
                font-size: 1rem;
                font-weight: 700;
                margin: 0 0 0.35rem;
                color: var(--text-muted);
            }

            .empty-state p {
                font-size: 0.85rem;
                margin: 0;
            }

            @media (max-width: 640px) {
                .toggle-group {
                    width: 100%;
                }

                .toggle-btn {
                    flex: 1;
                    justify-content: center;
                }

                .search-box {
                    max-width: 100%;
                }
            }
        </style>
    </head>

    <body>

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Demandes en Ligne</h1>
                <div class="page-subtitle">
                    <i class="far fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('D MMMM YYYY') }}
                </div>
            </div>
        </div>

        <!-- Conteneur rafraîchi dynamiquement chaque 10 secondes -->
        <div id="comptable-table-and-stats-wrapper">
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card" style="border-left:4px solid var(--warning);">
                    <div class="stat-icon" style="background:#fef3c7;color:var(--warning);">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <div class="stat-label">Solde Disponible</div>
                        <div class="stat-value">{{ number_format($montantRestant ?? 0, 0, ',', ' ') }} <small>FCFA</small>
                        </div>
                    </div>
                </div>

                <div class="stat-card" style="border-left:4px solid var(--warning);">
                    <div class="stat-icon" style="background:var(--warning-light);color:var(--warning);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="stat-label">À Valider</div>
                        <div class="stat-value">{{ $demandesEnAttente->count() }}</div>
                    </div>
                </div>

                <div class="stat-card" style="border-left:4px solid var(--success);">
                    <div class="stat-icon" style="background:var(--success-light);color:var(--success);">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div>
                        <div class="stat-label">Traitées</div>
                        <div class="stat-value">{{ $demandesTraitees->count() }}</div>
                    </div>
                </div>

                <div class="stat-card" style="border-left:4px solid var(--primary);">
                    <div class="stat-icon" style="background:#fef3c7;color:var(--warning);">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div>
                        <div class="stat-label">Stock de Timbres</div>
                        <div class="stat-value">{{ number_format($soldeTimbres ?? 0, 0, ',', ' ') }}</div>
                    </div>
                </div>

                <div class="stat-card" style="border-left:4px solid var(--primary);">
                    <div class="stat-icon" style="background:var(--primary-light);color:var(--primary);">
                        <i class="fas fa-stamp"></i>
                    </div>
                    <div>
                        <div class="stat-label">Timbres Non Récupérés</div>
                        <div class="stat-value">{{ $demandesEnAttente->sum(fn($d) => (int) ($d->free_timbres_count ?? 0)) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Main Card -->
            <div class="card-modern">

                <div class="toolbar">
                    <!-- Titre + Toggle -->
                    <div class="toolbar-row">
                        <h2 class="toolbar-title">
                            Liste des Retraits & Livraisons
                            <span style="font-size:0.75rem;font-weight:500;color:var(--text-muted);margin-left:0.5rem;">
                                — demandes <span style="color:#059669;font-weight:700;">réglées</span> uniquement
                            </span>
                        </h2>

                        <div class="toggle-group" role="tablist">
                            <button class="toggle-btn pending-tab active" id="tab-pending" onclick="switchTab('pending')"
                                role="tab">
                                <i class="fas fa-clock"></i>
                                À valider
                                <span class="toggle-count" id="count-pending">{{ $demandesEnAttente->count() }}</span>
                            </button>
                            <button class="toggle-btn treated-tab" id="tab-treated" onclick="switchTab('treated')"
                                role="tab">
                                <i class="fas fa-check-circle"></i>
                                Traitées
                                <span class="toggle-count" id="count-treated">{{ $demandesTraitees->count() }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Filtres (re-utilises) -->
                    <div class="filters-row">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="searchInput" class="search-input" placeholder="Nom, contact…">
                        </div>

                        <select id="filterType" class="filter-select" onchange="applyFilters()">
                            <option value="">Tous les types</option>
                            <option value="naissance">Acte de naissance</option>
                            <option value="deces">Acte de décès</option>
                            <option value="mariage">Acte de mariage</option>
                        </select>

                        <select id="filterPeriod" class="filter-select" onchange="applyFilters()">
                            <option value="">Toutes les périodes</option>
                            <option value="today">Aujourd'hui</option>
                            <option value="week">Cette semaine</option>
                            <option value="month">Ce mois-ci</option>
                        </select>

                        <button class="btn-reset" onclick="resetFilters()">
                            <i class="fas fa-times"></i> Réinitialiser
                        </button>
                    </div>
                </div>

                <!-- Result count -->
                <div class="result-count" id="resultCount"></div>

                <div class="table-wrapper">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Type de Demande</th>
                                <th>Demandeur</th>
                                <th>Date & Heure</th>
                                <th>Nb Timbres</th>
                                <th>État Timbre</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">

                            {{-- DEMANDES EN ATTENTE (timbre_recupere = 0) --}}
                            @forelse($demandesEnAttente as $demande)
                                <tr class="data-row pending-row" data-type="{{ $demande->type_demande }}"
                                    data-date="{{ \Carbon\Carbon::parse($demande->created_at)->format('Y-m-d') }}"
                                    data-search="{{ strtolower($demande->demandeur_nom . ' ' . ($demande->contact ?? '') . ' ' . $demande->type_demande) }}">
                                    <td>
                                        @if (in_array($demande->type_demande, ['naissance', 'naissance_groupe']))
                                            <span class="badge badge-naissance"><i class="fas fa-baby"></i>
                                                Naissance{{ $demande->type_demande === 'naissance_groupe' ? ' (Groupe)' : '' }}</span>
                                        @elseif(in_array($demande->type_demande, ['deces', 'deces_groupe']))
                                            <span class="badge badge-deces"><i class="fas fa-cross"></i>
                                                Décès{{ $demande->type_demande === 'deces_groupe' ? ' (Groupe)' : '' }}</span>
                                        @elseif(in_array($demande->type_demande, ['mariage', 'mariage_groupe']))
                                            <span class="badge badge-mariage"><i class="fas fa-heart"></i>
                                                Mariage{{ $demande->type_demande === 'mariage_groupe' ? ' (Groupe)' : '' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="demandeur-name">{{ $demande->demandeur_nom }}</div>
                                        <div class="demandeur-contact">{{ $demande->contact }}</div>
                                    </td>
                                    <td>
                                        <div class="date-main">
                                            {{ \Carbon\Carbon::parse($demande->created_at)->locale('fr')->isoFormat('D MMM YYYY') }}
                                        </div>
                                        <div class="date-time">
                                            {{ \Carbon\Carbon::parse($demande->created_at)->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <span class="qty-badge">{{ $demande->quantite ?? '1' }}</span>
                                        @if (($demande->free_timbres_count ?? 0) > 0)
                                            <small
                                                style="color:var(--success); display:block; font-weight:600; font-size:0.7rem; margin-top:2px;">
                                                ({{ $demande->free_timbres_count ?? 0 }}
                                                gratuit{{ ($demande->free_timbres_count ?? 0) > 1 ? 's' : '' }})
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-pending"><i class="fas fa-hourglass-half"></i> En
                                            attente</span>
                                    </td>
                                    <td>
                                        <form
                                            action="{{ route('comptable.demandes.recover', ['type' => $demande->type_demande, 'id' => $demande->id]) }}"
                                            method="POST" style="display:inline-block;" class="recover-form">
                                            @csrf
                                            <button type="submit" class="btn-validate">
                                                <i class="fas fa-arrow-up-from-bracket"></i> Valider Sortie
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="pending-row empty-pending-row">
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <div class="empty-icon"><i class="fas fa-check-circle"
                                                    style="color:#a7f3d0;"></i></div>
                                            <h3>Aucune demande en attente</h3>
                                            <p>Toutes les livraisons terminées ont été traitées.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            {{-- DEMANDES TRAITÉES (timbre_recupere = 1) --}}
                            @forelse($demandesTraitees as $demande)
                                <tr class="data-row treated-row" style="display:none;"
                                    data-type="{{ $demande->type_demande }}"
                                    data-date="{{ \Carbon\Carbon::parse($demande->created_at)->format('Y-m-d') }}"
                                    data-search="{{ strtolower($demande->demandeur_nom . ' ' . ($demande->contact ?? '') . ' ' . $demande->type_demande) }}">
                                    <td>
                                        @if (in_array($demande->type_demande, ['naissance', 'naissance_groupe']))
                                            <span class="badge badge-naissance"><i class="fas fa-baby"></i>
                                                Naissance{{ $demande->type_demande === 'naissance_groupe' ? ' (Groupe)' : '' }}</span>
                                        @elseif(in_array($demande->type_demande, ['deces', 'deces_groupe']))
                                            <span class="badge badge-deces"><i class="fas fa-cross"></i>
                                                Décès{{ $demande->type_demande === 'deces_groupe' ? ' (Groupe)' : '' }}</span>
                                        @elseif(in_array($demande->type_demande, ['mariage', 'mariage_groupe']))
                                            <span class="badge badge-mariage"><i class="fas fa-heart"></i>
                                                Mariage{{ $demande->type_demande === 'mariage_groupe' ? ' (Groupe)' : '' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="demandeur-name">{{ $demande->demandeur_nom }}</div>
                                        <div class="demandeur-contact">{{ $demande->contact }}</div>
                                    </td>
                                    <td>
                                        <div class="date-main">
                                            {{ \Carbon\Carbon::parse($demande->created_at)->locale('fr')->isoFormat('D MMM YYYY') }}
                                        </div>
                                        <div class="date-time">
                                            {{ \Carbon\Carbon::parse($demande->created_at)->format('H:i') }}</div>
                                    </td>
                                    <td><span class="qty-badge">{{ $demande->quantite ?? '1' }}</span></td>
                                    <td>
                                        <span class="badge badge-done"><i class="fas fa-check-circle"></i> Récupéré</span>
                                    </td>
                                    <td>
                                        <div class="btn-done"><i class="fas fa-check-double"></i> Traité</div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="treated-row" style="display:none;">
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                                            <h3>Aucune demande traitée</h3>
                                            <p>Les demandes validées apparaîtront ici.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            let currentTab = 'pending';

            function switchTab(tab) {
                currentTab = tab;
                document.getElementById('tab-pending').classList.toggle('active', tab === 'pending');
                document.getElementById('tab-treated').classList.toggle('active', tab === 'treated');
                applyFilters();
            }

            function applyFilters() {
                const search = document.getElementById('searchInput').value.toLowerCase().trim();
                const type = document.getElementById('filterType').value;
                const period = document.getElementById('filterPeriod').value;

                const today = new Date();
                today.setHours(0, 0, 0, 0);
                let periodStart = null;
                if (period === 'today') {
                    periodStart = today;
                } else if (period === 'week') {
                    periodStart = new Date(today);
                    const day = today.getDay();
                    periodStart.setDate(today.getDate() - (day === 0 ? 6 : day - 1));
                } else if (period === 'month') {
                    periodStart = new Date(today.getFullYear(), today.getMonth(), 1);
                }

                const activeClass = currentTab === 'pending' ? 'pending-row' : 'treated-row';
                const inactiveClass = currentTab === 'pending' ? 'treated-row' : 'pending-row';

                // Masquer l'onglet inactif
                document.querySelectorAll('.' + inactiveClass).forEach(r => r.style.display = 'none');

                // Filtrer l'onglet actif
                const rows = document.querySelectorAll('.data-row.' + activeClass);
                let visible = 0;

                rows.forEach(row => {
                    const rowType = row.dataset.type || '';
                    const rowSearch = row.dataset.search || '';
                    const rowDate = row.dataset.date ? new Date(row.dataset.date) : null;

                    let show = true;
                    if (type && rowType !== type) show = false;
                    if (search && !rowSearch.includes(search)) show = false;
                    if (periodStart && rowDate && rowDate < periodStart) show = false;

                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                // Compteur
                const label = currentTab === 'pending' ? 'à valider' : 'traitée(s)';
                const resultCountEl = document.getElementById('resultCount');
                resultCountEl.textContent = '';
                
                const spanEl = document.createElement('span');
                spanEl.textContent = visible;
                resultCountEl.appendChild(spanEl);
                
                resultCountEl.appendChild(
                    document.createTextNode(` demande${visible > 1 ? 's' : ''} ${label}`)
                );

                // Empty rows
                document.querySelectorAll('.empty-pending-row').forEach(r => {
                    r.style.display = (currentTab === 'pending' && visible === 0) ? '' : 'none';
                });
            }

            function resetFilters() {
                document.getElementById('searchInput').value = '';
                document.getElementById('filterType').value = '';
                document.getElementById('filterPeriod').value = '';
                applyFilters();
            }

            document.getElementById('searchInput').addEventListener('input', applyFilters);

            function bindRecoverFormEvents() {
                document.querySelectorAll('.recover-form').forEach(function(form) {
                    // Ensure we don't bind duplicate submit event listeners
                    if (form.dataset.bound) return;
                    form.dataset.bound = "true";

                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Confirmer la sortie ?',
                            text: 'Le timbre sera marqué comme récupéré et le stock sera mis à jour.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#1f4083',
                            cancelButtonColor: '#dc2626',
                            confirmButtonText: 'Oui, valider !',
                            cancelButtonText: 'Annuler',
                            reverseButtons: true,
                        }).then((result) => {
                            if (result.isConfirmed) form.submit();
                        });
                    });
                });
            }

            function refreshData() {
                // Si aucune interaction SweetAlert (popup ouverte) n'est en cours, on actualise
                if (typeof Swal !== 'undefined' && Swal.isVisible()) return;

                if (typeof jQuery === 'undefined') {
                    console.warn("jQuery n'est pas chargé. Impossible de rafraîchir le tableau de bord.");
                    return;
                }

                jQuery.ajax({
                    url: window.location.href,
                    method: 'GET',
                    dataType: 'html',
                    success: function(response) {
                        const $html = jQuery('<div>').html(response);

                        // Mettre à jour les statistiques
                        jQuery('.stats-grid').html($html.find('.stats-grid').html());

                        // Mettre à jour les compteurs des onglets
                        jQuery('#count-pending').html($html.find('#count-pending').html());
                        jQuery('#count-treated').html($html.find('#count-treated').html());

                        // Mettre à jour le corps du tableau
                        jQuery('#tableBody').html($html.find('#tableBody').html());

                        // Appliquer les filtres courants sur les nouvelles lignes
                        applyFilters();

                        // Re-lier les événements de validation aux nouveaux formulaires
                        bindRecoverFormEvents();
                    },
                    error: function(xhr) {
                        console.error("Erreur lors de l'actualisation en arrière-plan :", xhr);
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                applyFilters();
                bindRecoverFormEvents();

                // Actualisation automatique toutes les 10 secondes asynchronously
                setInterval(refreshData, 10000);
            });
        </script>

    </body>

    </html>
@endsection
