@extends('finance.layouts.template')

@section('content')
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tableau de Bord - Finance</title>

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
            rel="stylesheet">
        <!-- Material Icons -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            :root {
                --primary: #1f4083;
                --primary-light: #2d5aa8;
                --primary-dark: #152e5c;
                --secondary: #718096;
                --accent: #38a169;
                --danger: #e53e3e;
                --warning: #f6ad55;
                --info: #4299e1;
                --success: #48bb78;
                --light-bg: #f7fafc;
                --card-bg: #ffffff;
                --text-primary: #2d3748;
                --text-secondary: #718096;
                --text-muted: #a0aec0;
                --border-color: #e2e8f0;
                --shadow-sm: 0 2px 8px rgba(31, 64, 131, 0.08);
                --shadow-md: 0 4px 16px rgba(31, 64, 131, 0.12);
                --shadow-lg: 0 8px 32px rgba(31, 64, 131, 0.16);
                --gradient-primary: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
                --gradient-card: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 1) 100%);
                --radius-sm: 12px;
                --radius-md: 16px;
                --radius-lg: 20px;
                --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            body {
                font-family: 'Poppins', sans-serif;
                background: var(--light-bg);
                color: var(--text-primary);
                line-height: 1.6;
            }

            .dashboard-wrapper {
                padding: 2.5rem;
                max-width: none;
                width: 100%;
                margin: 0 auto;
                animation: fadeIn 0.5s ease;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* === HEADER === */
            .dashboard-header {
                margin-bottom: 2.5rem;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                flex-wrap: wrap;
                gap: 1.5rem;
            }

            .header-content {
                flex: 1;
                min-width: 250px;
            }

            .header-content h1 {
                font-size: 2rem;
                font-weight: 700;
                color: var(--primary);
                margin-bottom: 0.5rem;
                letter-spacing: -0.5px;
            }

            .header-content p {
                color: var(--text-secondary);
                font-size: 1rem;
                font-weight: 400;
            }

            .header-badge {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .date-badge {
                background: white;
                padding: 0.875rem 1.5rem;
                border-radius: var(--radius-sm);
                box-shadow: var(--shadow-sm);
                display: flex;
                align-items: center;
                gap: 0.75rem;
                border: 1px solid var(--border-color);
                transition: var(--transition);
            }

            .date-badge:hover {
                box-shadow: var(--shadow-md);
                transform: translateY(-2px);
            }

            .date-badge i {
                color: var(--primary);
                font-size: 1.25rem;
            }

            .date-badge span {
                color: var(--text-primary);
                font-weight: 600;
                font-size: 0.95rem;
            }

            /* === KPI CARDS === */
            .kpi-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 1.5rem;
                margin-bottom: 2.5rem;
            }

            .kpi-card {
                background: white;
                border-radius: var(--radius-md);
                padding: 1.75rem;
                box-shadow: var(--shadow-sm);
                border: 1px solid var(--border-color);
                position: relative;
                overflow: hidden;
                transition: var(--transition);
            }

            .kpi-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: var(--gradient-primary);
                transform: scaleX(0);
                transform-origin: left;
                transition: var(--transition);
            }

            .kpi-card:hover {
                transform: translateY(-8px);
                box-shadow: var(--shadow-lg);
                border-color: var(--primary);
            }

            .kpi-card:hover::before {
                transform: scaleX(1);
            }

            .kpi-card-content {
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: relative;
                z-index: 1;
            }

            .kpi-info h3 {
                font-size: 2.5rem;
                font-weight: 700;
                color: var(--text-primary);
                margin-bottom: 0.5rem;
                line-height: 1;
            }

            .kpi-info p {
                color: var(--text-secondary);
                font-size: 0.9rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .kpi-icon {
                width: 70px;
                height: 70px;
                border-radius: var(--radius-md);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                position: relative;
            }

            .kpi-icon i {
                font-size: 2rem;
                position: relative;
                z-index: 1;
            }

            .kpi-card.naissance .kpi-icon {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
            }

            .kpi-card.naissance .kpi-icon i {
                color: white;
            }

            .kpi-card.deces .kpi-icon {
                background: linear-gradient(135deg, #434343 0%, #000000 100%);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            }

            .kpi-card.deces .kpi-icon i {
                color: white;
            }

            .kpi-card.mariage .kpi-icon {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                box-shadow: 0 8px 16px rgba(245, 87, 108, 0.3);
            }

            .kpi-card.mariage .kpi-icon i {
                color: white;
            }

            .kpi-card.total .kpi-icon {
                background: var(--gradient-primary);
                box-shadow: 0 8px 16px rgba(31, 64, 131, 0.3);
            }

            .kpi-card.total .kpi-icon i {
                color: white;
            }

            /* === FINANCE SECTION === */
            .section-header {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin-bottom: 1.5rem;
            }

            .section-header i {
                color: var(--primary);
                font-size: 1.5rem;
            }

            .section-header h2 {
                font-size: 1.25rem;
                font-weight: 600;
                color: var(--text-primary);
            }

            .finance-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
                gap: 1.5rem;
                margin-bottom: 2.5rem;
            }

            .finance-card {
                background: white;
                border-radius: var(--radius-md);
                padding: 2rem;
                box-shadow: var(--shadow-sm);
                border-left: 5px solid;
                transition: var(--transition);
                position: relative;
                overflow: hidden;
            }

            .finance-card::before {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                width: 150px;
                height: 150px;
                opacity: 0.05;
                font-size: 8rem;
                font-family: 'Material Icons';
                line-height: 1;
                pointer-events: none;
            }

            .finance-card.debit {
                border-color: var(--danger);
            }

            .finance-card.debit::before {
                content: 'trending_down';
                color: var(--danger);
            }

            .finance-card.balance {
                border-color: var(--success);
            }

            .finance-card.balance::before {
                content: 'account_balance_wallet';
                color: var(--success);
            }

            .finance-card:hover {
                transform: translateY(-8px);
                box-shadow: var(--shadow-lg);
            }

            .finance-card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1rem;
            }

            .finance-label {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                color: var(--text-secondary);
                font-size: 0.95rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .finance-label i {
                font-size: 1.25rem;
            }

            .finance-amount {
                font-size: 2rem;
                font-weight: 700;
                margin: 0.75rem 0;
                position: relative;
                z-index: 1;
            }

            .finance-card.debit .finance-amount {
                color: var(--danger);
            }

            .finance-card.balance .finance-amount {
                color: var(--success);
            }

            .finance-meta {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
                background: var(--light-bg);
                border-radius: var(--radius-sm);
                font-size: 0.85rem;
                color: var(--text-secondary);
                font-weight: 500;
            }

            .finance-meta i {
                font-size: 1rem;
            }

            /* === MAIN CONTENT GRID === */
            .main-content-grid {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 1.5rem;
                margin-bottom: 2.5rem;
            }

            @media (max-width: 1200px) {
                .main-content-grid {
                    grid-template-columns: 1fr;
                }
            }

            /* === CHART CARD === */
            .chart-card {
                background: white;
                border-radius: var(--radius-md);
                padding: 2rem;
                box-shadow: var(--shadow-sm);
                border: 1px solid var(--border-color);
            }

            .chart-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .chart-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--text-primary);
            }

            .chart-tabs {
                display: flex;
                gap: 0.5rem;
                background: var(--light-bg);
                padding: 0.25rem;
                border-radius: var(--radius-sm);
            }

            .chart-tab {
                padding: 0.5rem 1rem;
                border: none;
                background: transparent;
                color: var(--text-secondary);
                font-weight: 600;
                font-size: 0.85rem;
                border-radius: 8px;
                cursor: pointer;
                transition: var(--transition);
            }

            .chart-tab:hover {
                background: rgba(31, 64, 131, 0.1);
                color: var(--primary);
            }

            .chart-tab.active {
                background: var(--primary);
                color: white;
                box-shadow: 0 4px 12px rgba(31, 64, 131, 0.3);
            }

            .chart-container {
                height: 350px;
                position: relative;
            }

            /* === RECENT LIST === */
            .recent-card {
                background: white;
                border-radius: var(--radius-md);
                padding: 2rem;
                box-shadow: var(--shadow-sm);
                border: 1px solid var(--border-color);
                height: fit-content;
            }

            .recent-header {
                margin-bottom: 1.5rem;
            }

            .recent-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: var(--text-primary);
                margin-bottom: 1rem;
            }

            .recent-tabs {
                display: flex;
                gap: 0.5rem;
                flex-wrap: wrap;
            }

            .recent-tab {
                padding: 0.5rem 1rem;
                border: 1px solid var(--border-color);
                background: white;
                color: var(--text-secondary);
                font-weight: 500;
                font-size: 0.85rem;
                border-radius: var(--radius-sm);
                cursor: pointer;
                transition: var(--transition);
                white-space: nowrap;
            }

            .recent-tab:hover {
                border-color: var(--primary);
                color: var(--primary);
            }

            .recent-tab.active {
                background: var(--primary);
                color: white;
                border-color: var(--primary);
            }

            .recent-list {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                max-height: 450px;
                overflow-y: auto;
                padding-right: 0.5rem;
            }

            .recent-item {
                display: flex;
                align-items: center;
                gap: 1rem;
                padding: 1rem;
                background: var(--light-bg);
                border-radius: var(--radius-sm);
                border: 1px solid transparent;
                transition: var(--transition);
            }

            .recent-item:hover {
                background: white;
                border-color: var(--primary);
                box-shadow: var(--shadow-sm);
            }

            .recent-icon {
                width: 45px;
                height: 45px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .recent-icon i {
                font-size: 1.25rem;
                color: white;
            }

            .recent-item.naissance .recent-icon {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .recent-item.deces .recent-icon {
                background: linear-gradient(135deg, #434343 0%, #000000 100%);
            }

            .recent-item.mariage .recent-icon {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            }

            .recent-info {
                flex: 1;
                min-width: 0;
            }

            .recent-info h5 {
                font-size: 0.95rem;
                font-weight: 600;
                color: var(--text-primary);
                margin-bottom: 0.25rem;
            }

            .recent-info span {
                font-size: 0.8rem;
                color: var(--text-secondary);
            }

            .recent-badge {
                padding: 0.375rem 0.75rem;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                background: var(--success);
                color: white;
                white-space: nowrap;
            }

            /* === SCROLLBAR === */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            ::-webkit-scrollbar-track {
                background: var(--light-bg);
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb {
                background: var(--text-muted);
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: var(--text-secondary);
            }

            /* === RESPONSIVE === */
            @media (max-width: 768px) {
                .dashboard-wrapper {
                    padding: 1.5rem;
                }

                .dashboard-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .header-content h1 {
                    font-size: 1.5rem;
                }

                .kpi-grid {
                    grid-template-columns: 1fr;
                }

                .finance-grid {
                    grid-template-columns: 1fr;
                }

                .chart-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .chart-container {
                    height: 250px;
                }
            }

            /* === EMPTY STATE === */
            .empty-state {
                text-align: center;
                padding: 3rem 1rem;
                color: var(--text-secondary);
            }

            .empty-state i {
                font-size: 4rem;
                color: var(--text-muted);
                margin-bottom: 1rem;
            }

            .empty-state p {
                font-size: 0.95rem;
            }

            /* Premium Glassmorphic Card */
            .glass-card {
                background: linear-gradient(135deg, rgba(31, 64, 131, 0.9), rgba(13, 110, 253, 0.75));
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: var(--radius-md);
                padding: 2rem;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
                color: white;
                position: relative;
                overflow: hidden;
                transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease;
                text-decoration: none;
            }

            .glass-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 25px 45px rgba(13, 110, 253, 0.3);
            }

            /* Reflective shine effect on card hover */
            .glass-card::after {
                content: '';
                position: absolute;
                top: 0;
                left: -150%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
                transform: skewX(-25deg);
                transition: 0.75s ease;
            }

            .glass-card:hover::after {
                left: 150%;
            }

            /* RFID / SIM Chip styling */
            .card-chip {
                width: 40px;
                height: 30px;
                background: linear-gradient(135deg, #ffd700, #b8860b);
                border-radius: 6px;
                position: relative;
                box-shadow: inset 0 1px 3px rgba(255, 255, 255, 0.5);
            }

            .card-chip::before {
                content: '';
                position: absolute;
                top: 5px;
                left: 5px;
                right: 5px;
                bottom: 5px;
                border: 1px solid rgba(0, 0, 0, 0.15);
                border-radius: 4px;
            }
        </style>
    </head>

    <body>
        <div class="dashboard-wrapper">

            <!-- === HEADER === -->
            <header class="dashboard-header">
                <div class="header-content">
                    <h1>🎯 Tableau de Bord Finance</h1>
                    <p>Vue d'ensemble de l'activité et des statistiques</p>
                </div>
                <div class="header-badge">
                    <div class="date-badge">
                        <i class="material-icons">calendar_today</i>
                        <span>{{ \Carbon\Carbon::now()->locale('fr')->isoFormat('DD MMMM YYYY') }}</span>
                    </div>
                </div>
            </header>

            <!-- === KPI CARDS === -->
            <div class="kpi-grid">
                <!-- Naissances -->
                <div class="kpi-card naissance">
                    <div class="kpi-card-content">
                        <div class="kpi-info">
                            <h3>{{ $naissancenombre }}</h3>
                            <p>Actes de Naissance</p>
                        </div>
                        <div class="kpi-icon">
                            <i class="material-icons">child_care</i>
                        </div>
                    </div>
                </div>

                <!-- Décès -->
                <div class="kpi-card deces">
                    <div class="kpi-card-content">
                        <div class="kpi-info">
                            <h3>{{ $decesnombre }}</h3>
                            <p>Actes de Décès</p>
                        </div>
                        <div class="kpi-icon">
                            <i class="material-icons">sentiment_dissatisfied</i>
                        </div>
                    </div>
                </div>

                <!-- Mariages -->
                <div class="kpi-card mariage">
                    <div class="kpi-card-content">
                        <div class="kpi-info">
                            <h3>{{ $mariagenombre }}</h3>
                            <p>Actes de Mariage</p>
                        </div>
                        <div class="kpi-icon">
                            <i class="material-icons">favorite</i>
                        </div>
                    </div>
                </div>

                <!-- Total -->
                <div class="kpi-card total">
                    <div class="kpi-card-content">
                        <div class="kpi-info">
                            <h3>{{ $total }}</h3>
                            <p>Total des Actes</p>
                        </div>
                        <div class="kpi-icon">
                            <i class="material-icons">description</i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- === FINANCE SECTION === -->
            <div class="section-header">
                <i class="material-icons">account_balance</i>
                <h2>Situation Financière</h2>
            </div>

            <div class="finance-grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
                <!-- La carte Bancaire Virtuelle Premium (Portefeuille en ligne) -->
                <div class="animate-up h-100" style="animation-delay: 0.1s; height: 100%;">
                    <a href="{{ route('finance.portefeuille.index') }}" class="text-decoration-none d-block h-100"
                        style="height: 100%;">
                        <div class="glass-card h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="card-chip"></div>
                                    <span class="fw-bold text-white text-uppercase tracking-wider small"
                                        style="font-size: 0.7rem; letter-spacing: 1px;">E-TIMBRE COLLECTE</span>
                                </div>
                                <h6 class="text-white-50 text-uppercase small mb-1"
                                    style="font-size: 0.65rem; opacity: 0.8; color: rgba(255,255,255,0.7) !important;">CUMUL
                                    DU MOIS EN COURS</h6>
                                <h1 class="fw-bold mb-0 text-white"
                                    style="font-feature-settings: 'tnum'; font-size: 1.8rem;">
                                    <span id="wallet-balance" data-target="{{ $soldePortefeuille }}">0</span> <span
                                        class="fs-6">FCFA</span>
                                </h1>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between align-items-end mt-2">
                                    <div>
                                        <small class="text-white-50 d-block small"
                                            style="font-size: 0.6rem; opacity: 0.8; color: rgba(255,255,255,0.7) !important;">COMMUNE
                                            DE</small>
                                        <span class="fw-bold text-white text-uppercase"
                                            style="font-size: 0.85rem;">{{ $finance->communeM }}</span>
                                    </div>
                                    <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Logo Plateau"
                                        style="height: 28px; filter: brightness(0) invert(1);">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Solde -->
                <div class="finance-card balance">
                    <div class="finance-card-header">
                        <div class="finance-label">
                            <i class="material-icons">account_balance_wallet</i>
                            <span>Solde Disponible (Physique)</span>
                        </div>
                    </div>
                    <div class="finance-amount">{{ number_format($montantRestant, 0, ',', ' ') }} FCFA</div>
                    <div class="finance-meta">
                        <i class="material-icons">check_circle</i>
                        <span>Timbres physiques en mairie</span>
                    </div>
                </div>
            </div>

            <!-- === MAIN CONTENT GRID === -->
            <div class="main-content-grid">

                <!-- === CHART CARD === -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">📊 Évolution des Activités</h3>
                        <div class="chart-tabs">
                            <button class="chart-tab active" onclick="updateChart('weekly', this)">7 Jours</button>
                            <button class="chart-tab" onclick="updateChart('monthly', this)">30 Jours</button>
                            <button class="chart-tab" onclick="updateChart('yearly', this)">12 Mois</button>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="mainChart"></canvas>
                    </div>
                </div>

                <!-- === RECENT ACTIVITIES === -->
                <div class="recent-card">
                    <div class="recent-header">
                        <h3 class="recent-title">🕒 Activités Récentes</h3>
                        <div class="recent-tabs">
                            <button class="recent-tab active" onclick="showRecent('naissance', this)">Naissances</button>
                            <button class="recent-tab" onclick="showRecent('deces', this)">Décès</button>
                            <button class="recent-tab" onclick="showRecent('mariage', this)">Mariages</button>
                        </div>
                    </div>

                    <!-- Liste Naissances -->
                    <div class="recent-list" id="list-naissance">
                        @forelse($demandesNaissance as $demande)
                            <div class="recent-item naissance">
                                <div class="recent-icon">
                                    <i class="material-icons">child_care</i>
                                </div>
                                <div class="recent-info">
                                    <h5>Demande #{{ $demande->reference }}</h5>
                                    <span>{{ $demande->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="recent-badge">Nouveau</span>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="material-icons">inbox</i>
                                <p>Aucune demande récente</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Liste Décès -->
                    <div class="recent-list" id="list-deces" style="display: none;">
                        @forelse($demandesDeces as $demande)
                            <div class="recent-item deces">
                                <div class="recent-icon">
                                    <i class="material-icons">sentiment_dissatisfied</i>
                                </div>
                                <div class="recent-info">
                                    <h5>Demande #{{ $demande->reference }}</h5>
                                    <span>{{ $demande->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="recent-badge">Nouveau</span>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="material-icons">inbox</i>
                                <p>Aucune demande récente</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Liste Mariages -->
                    <div class="recent-list" id="list-mariage" style="display: none;">
                        @forelse($demandesMariage as $demande)
                            <div class="recent-item mariage">
                                <div class="recent-icon">
                                    <i class="material-icons">favorite</i>
                                </div>
                                <div class="recent-info">
                                    <h5>Demande #{{ $demande->reference }}</h5>
                                    <span>{{ $demande->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="recent-badge">Nouveau</span>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="material-icons">inbox</i>
                                <p>Aucune demande récente</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

        <!-- === JAVASCRIPT === -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // progressive increment for wallet balance
                const balanceEl = document.getElementById('wallet-balance');
                if (balanceEl) {
                    const targetValue = parseInt(balanceEl.getAttribute('data-target'), 10);
                    if (targetValue > 0) {
                        let start = 0;
                        const duration = 1200; // ms
                        const stepTime = 15;
                        const steps = duration / stepTime;
                        const increment = targetValue / steps;

                        const timer = setInterval(() => {
                            start += increment;
                            if (start >= targetValue) {
                                balanceEl.textContent = targetValue.toLocaleString('fr-FR');
                                clearInterval(timer);
                            } else {
                                balanceEl.textContent = Math.floor(start).toLocaleString('fr-FR');
                            }
                        }, stepTime);
                    } else {
                        balanceEl.textContent = '0';
                    }
                }
            });

            // Fonction pour afficher les différentes listes d'activités récentes
            function showRecent(type, btn) {
                // Masquer toutes les listes
                document.getElementById('list-naissance').style.display = 'none';
                document.getElementById('list-deces').style.display = 'none';
                document.getElementById('list-mariage').style.display = 'none';

                // Afficher la liste sélectionnée
                document.getElementById('list-' + type).style.display = 'flex';

                // Gérer les classes actives des boutons
                const buttons = btn.parentElement.querySelectorAll('button');
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            }

            // Configuration du graphique Chart.js
            const ctx = document.getElementById('mainChart').getContext('2d');

            // Données des graphiques injectées depuis le backend
            const chartData = {
                weekly: {
                    labels: ['J-6', 'J-5', 'J-4', 'J-3', 'J-2', 'Hier', 'Aujourd\'hui'],
                    datasets: [{
                            label: 'Naissances',
                            data: @json($weeklyData['naissances']),
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Décès',
                            data: @json($weeklyData['deces']),
                            borderColor: '#434343',
                            backgroundColor: 'rgba(67, 67, 67, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Mariages',
                            data: @json($weeklyData['mariages']),
                            borderColor: '#f5576c',
                            backgroundColor: 'rgba(245, 87, 108, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                monthly: {
                    labels: Array.from({
                        length: 30
                    }, (_, i) => `Jour ${i + 1}`),
                    datasets: [{
                            label: 'Naissances',
                            data: @json($monthlyData['naissances']),
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        },
                        {
                            label: 'Décès',
                            data: @json($monthlyData['deces']),
                            borderColor: '#434343',
                            backgroundColor: 'rgba(67, 67, 67, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        },
                        {
                            label: 'Mariages',
                            data: @json($monthlyData['mariages']),
                            borderColor: '#f5576c',
                            backgroundColor: 'rgba(245, 87, 108, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        }
                    ]
                },
                yearly: {
                    labels: ['Janv', 'Févr', 'Mars', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'],
                    datasets: [{
                            label: 'Naissances',
                            data: @json($yearlyData['naissances']),
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        },
                        {
                            label: 'Décès',
                            data: @json($yearlyData['deces']),
                            borderColor: '#434343',
                            backgroundColor: 'rgba(67, 67, 67, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        },
                        {
                            label: 'Mariages',
                            data: @json($yearlyData['mariages']),
                            borderColor: '#f5576c',
                            backgroundColor: 'rgba(245, 87, 108, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        }
                    ]
                }
            };

            // Initialisation du graphique
            let myChart = new Chart(ctx, {
                type: 'line',
                data: chartData.weekly,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    family: 'Poppins',
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(255, 255, 255, 0.95)',
                            titleColor: '#2d3748',
                            bodyColor: '#718096',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 6,
                            usePointStyle: true,
                            font: {
                                family: 'Poppins'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f0f4f8',
                                lineWidth: 1
                            },
                            ticks: {
                                font: {
                                    family: 'Poppins',
                                    size: 11
                                },
                                color: '#718096'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'Poppins',
                                    size: 11
                                },
                                color: '#718096'
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });

            // Fonction pour mettre à jour le graphique selon la période
            window.updateChart = function(period, btn) {
                // Mettre à jour les boutons actifs
                const buttons = btn.parentElement.querySelectorAll('button');
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Mettre à jour les données du graphique
                myChart.data = chartData[period];
                myChart.update('active');
            }
        </script>
    </body>

    </html>
@endsection
