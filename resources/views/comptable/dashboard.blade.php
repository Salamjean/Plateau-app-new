@extends('comptable.layouts.template')
@section('content')

    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tableau de Bord Premium | Régie</title>

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- jsPDF pour export PDF -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

        <style>
            :root {
                --primary: #1f4083;
                --primary-gradient: linear-gradient(135deg, #1f4083, #136efd);
                --accent-gold: #ffd700;
                --accent-gold-gradient: linear-gradient(135deg, #ffd700, #b8860b);
                --success: #2ec4b6;
                --success-light: rgba(46, 196, 182, 0.1);
                --danger: #e71d36;
                --danger-light: rgba(231, 29, 54, 0.1);
                --info: #0ea5e9;
                --info-light: rgba(14, 165, 233, 0.1);
                --warning: #f72585;
                --warning-light: rgba(247, 37, 211, 0.1);
                
                --neutral-50: #f8fafc;
                --neutral-100: #f1f5f9;
                --neutral-200: #e2e8f0;
                --neutral-700: #334155;
                --neutral-800: #1e293b;
                --neutral-900: #0f172a;
                
                --card-bg: #ffffff;
                --border-radius-lg: 24px;
                --border-radius-md: 16px;
                
                --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
                --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.06), 0 4px 6px -4px rgba(0, 0, 0, 0.06);
                --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
                
                --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background-color: #f6f8fc;
                color: var(--neutral-800);
                margin: 0;
                padding: 0;
            }

            .dashboard-container {
                padding: 2.5rem;
                max-width: 100%;
                margin: 0 auto;
            }

            /* --- Animations --- */
            .animate-up {
                animation: fadeInUp 0.7s cubic-bezier(0.2, 0.8, 0.2, 1) both;
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

            /* --- Header Section --- */
            .dashboard-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2.5rem;
                flex-wrap: wrap;
                gap: 1.5rem;
            }

            .header-title h1 {
                font-size: 2.25rem;
                font-weight: 800;
                color: var(--neutral-900);
                margin: 0;
                letter-spacing: -0.5px;
            }

            .header-title p {
                color: var(--neutral-700);
                margin: 0.5rem 0 0 0;
                font-size: 1.05rem;
                font-weight: 500;
            }

            .header-title strong {
                color: var(--primary);
                font-weight: 700;
            }

            .header-actions {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .date-badge {
                background: var(--card-bg);
                padding: 0.75rem 1.25rem;
                border-radius: 50px;
                font-size: 0.9rem;
                font-weight: 600;
                color: var(--primary);
                box-shadow: var(--shadow-sm);
                border: 1px solid var(--neutral-100);
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            /* --- Grid KPI (Demandes) --- */
            .grid-stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                gap: 1.5rem;
                margin-bottom: 3rem;
            }

            .kpi-card {
                background: var(--card-bg);
                border-radius: var(--border-radius-lg);
                padding: 1.75rem;
                box-shadow: var(--shadow-sm);
                transition: var(--transition);
                border: 1px solid var(--neutral-100);
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: relative;
                overflow: hidden;
            }

            .kpi-card:hover {
                transform: translateY(-5px);
                box-shadow: var(--shadow-lg);
                border-color: rgba(31, 64, 131, 0.15);
            }

            .kpi-card::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0;
                bottom: 0;
                width: 6px;
                background-color: transparent;
            }

            .kpi-naissance::before { background-color: var(--primary); }
            .kpi-deces::before { background-color: var(--neutral-700); }
            .kpi-mariage::before { background-color: var(--warning); }
            .kpi-total::before { background-color: var(--info); }

            .kpi-content p {
                margin: 0;
                color: var(--neutral-700);
                font-size: 0.85rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.75px;
            }

            .kpi-content h3 {
                font-size: 2.25rem;
                font-weight: 800;
                margin: 0.5rem 0 0 0;
                color: var(--neutral-900);
                font-feature-settings: 'tnum';
            }

            .kpi-icon {
                width: 56px;
                height: 56px;
                border-radius: var(--border-radius-md);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                flex-shrink: 0;
                transition: var(--transition);
            }

            .kpi-card:hover .kpi-icon {
                transform: scale(1.1) rotate(5deg);
            }

            .kpi-naissance .kpi-icon {
                background: rgba(31, 64, 131, 0.08);
                color: var(--primary);
            }

            .kpi-deces .kpi-icon {
                background: rgba(30, 41, 59, 0.08);
                color: var(--neutral-700);
            }

            .kpi-mariage .kpi-icon {
                background: rgba(247, 37, 133, 0.08);
                color: var(--warning);
            }

            .kpi-total .kpi-icon {
                background: rgba(14, 165, 233, 0.08);
                color: var(--info);
            }

            /* --- Section Title --- */
            .section-title {
                font-size: 1.35rem;
                font-weight: 800;
                color: var(--neutral-900);
                margin: 0 0 1.5rem 0;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                width: 100%;
            }

            .section-title i {
                color: var(--primary);
                font-size: 1.5rem;
            }

            .section-title-flex {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
                flex-wrap: wrap;
                gap: 1rem;
            }

            /* --- Finance Grid (Timbres & Portefeuille) --- */
            .grid-finance {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 1.5rem;
                margin-bottom: 3.5rem;
            }

            /* Premium Glassmorphic Card (Portefeuille en ligne) */
            .glass-card {
                background: linear-gradient(135deg, rgba(31, 64, 131, 0.9), rgba(19, 110, 253, 0.8));
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: var(--border-radius-lg);
                box-shadow: 0 15px 35px rgba(31, 64, 131, 0.15);
                color: white;
                position: relative;
                overflow: hidden;
                transition: var(--transition);
                padding: 1.25rem;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                height: 100%;
                min-height: 160px;
            }

            .glass-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 25px 45px rgba(19, 110, 253, 0.3);
            }

            .glass-card::after {
                content: '';
                position: absolute;
                top: 0;
                left: -150%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transform: skewX(-25deg);
                transition: 0.8s ease;
            }

            .glass-card:hover::after {
                left: 150%;
            }

            .card-chip {
                width: 40px;
                height: 30px;
                background: linear-gradient(135deg, #ffd700, #b8860b);
                border-radius: 8px;
                position: relative;
                box-shadow: inset 0 1px 3px rgba(255,255,255,0.5);
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

            .card-badge-online {
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(5px);
                border: 1px solid rgba(255, 255, 255, 0.25);
                color: #ffffff;
                font-weight: 700;
                padding: 0.35rem 0.85rem;
                border-radius: 50px;
                font-size: 0.75rem;
                letter-spacing: 1px;
                text-transform: uppercase;
            }

            .wallet-title {
                font-size: 0.75rem;
                opacity: 0.85;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 0.25rem;
                display: block;
            }

            .wallet-balance-container {
                font-size: 1.85rem;
                font-weight: 800;
                font-feature-settings: 'tnum';
                font-family: 'Plus Jakarta Sans', sans-serif;
            }

            .wallet-balance-container span.currency {
                font-size: 1.15rem;
                font-weight: 600;
                margin-left: 0.25rem;
            }

            .card-holder-info {
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                margin-top: 1rem;
            }

            .holder-lbl {
                font-size: 0.65rem;
                opacity: 0.75;
                text-transform: uppercase;
                letter-spacing: 1px;
                display: block;
            }

            .holder-val {
                font-size: 0.95rem;
                font-weight: 700;
                text-transform: uppercase;
            }

            /* Standard Finance Card */
            .finance-card {
                background: var(--card-bg);
                border-radius: var(--border-radius-lg);
                padding: 1.25rem;
                box-shadow: var(--shadow-sm);
                border: 1px solid var(--neutral-100);
                transition: var(--transition);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                position: relative;
                overflow: hidden;
                height: 100%;
                min-height: 160px;
            }

            .finance-card:hover {
                transform: translateY(-5px);
                box-shadow: var(--shadow-lg);
            }

            .finance-card::before {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                width: 120px;
                height: 120px;
                border-radius: 50%;
                background: transparent;
                opacity: 0.03;
                transform: translate(30px, -30px);
                pointer-events: none;
            }

            .finance-card.allocated::before { background: var(--success); }
            .finance-card.sales-day::before { background: var(--info); }
            .finance-card.sales-month::before { background: var(--primary); }
            .finance-card.stock::before { background: var(--warning); }

            .finance-card-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 1.25rem;
            }

            .finance-info h4 {
                margin: 0;
                font-size: 0.85rem;
                color: var(--neutral-700);
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.75px;
                margin-bottom: 0.5rem;
            }

            .finance-info .amount {
                font-size: 1.6rem;
                font-weight: 800;
                color: var(--neutral-900);
                display: block;
                font-feature-settings: 'tnum';
            }

            .finance-sub {
                font-size: 0.85rem;
                color: var(--neutral-700);
                font-weight: 500;
                margin-top: 0.25rem;
                display: block;
            }

            .finance-sub strong {
                color: var(--neutral-900);
                font-weight: 700;
            }

            .finance-icon-bg {
                width: 48px;
                height: 48px;
                border-radius: var(--border-radius-md);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
            }

            .finance-card.allocated .finance-icon-bg { background: var(--success-light); color: var(--success); }
            .finance-card.sales-day .finance-icon-bg { background: var(--info-light); color: var(--info); }
            .finance-card.sales-month .finance-icon-bg { background: rgba(31, 64, 131, 0.08); color: var(--primary); }
            .finance-card.stock .finance-icon-bg { background: var(--warning-light); color: var(--warning); }

            /* --- Main Content (Charts & Lists) --- */
            .grid-main {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 1.5rem;
                margin-bottom: 2.5rem;
            }

            @media (max-width: 1200px) {
                .grid-main {
                    grid-template-columns: 1fr;
                }
            }

            .card-section {
                background: var(--card-bg);
                border-radius: var(--border-radius-lg);
                padding: 1.75rem;
                box-shadow: var(--shadow-sm);
                border: 1px solid var(--neutral-100);
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .card-header-flex {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.75rem;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .card-heading {
                font-size: 1.2rem;
                font-weight: 800;
                color: var(--neutral-900);
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .card-heading i {
                color: var(--primary);
            }

            .tabs-container {
                background: var(--neutral-100);
                padding: 4px;
                border-radius: 12px;
                display: flex;
                gap: 4px;
            }

            .tab-btn {
                border: none;
                background: transparent;
                padding: 6px 14px;
                font-size: 0.85rem;
                border-radius: 8px;
                cursor: pointer;
                color: var(--neutral-700);
                font-weight: 600;
                transition: var(--transition);
            }

            .tab-btn.active {
                background: white;
                color: var(--primary);
                box-shadow: var(--shadow-sm);
            }

            /* --- Lists --- */
            .recent-list {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
                max-height: 420px;
                overflow-y: auto;
                padding-right: 4px;
            }

            .recent-item {
                display: flex;
                align-items: center;
                padding: 1rem;
                border-radius: var(--border-radius-md);
                background: var(--card-bg);
                border: 1px solid var(--neutral-100);
                transition: var(--transition);
            }

            .recent-item:hover {
                border-color: rgba(31, 64, 131, 0.2);
                background: var(--neutral-50);
                transform: translateX(4px);
            }

            .icon-circle {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 1rem;
                flex-shrink: 0;
                font-size: 1.1rem;
            }

            .recent-info {
                flex: 1;
            }

            .recent-info h5 {
                margin: 0;
                font-size: 0.95rem;
                font-weight: 700;
                color: var(--neutral-900);
            }

            .recent-info span {
                font-size: 0.8rem;
                color: var(--neutral-700);
                display: block;
                margin-top: 2px;
                font-weight: 500;
            }

            .status-pill {
                padding: 6px 12px;
                border-radius: 50px;
                font-size: 0.75rem;
                font-weight: 700;
                white-space: nowrap;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .status-pill.primary {
                background: rgba(31, 64, 131, 0.08);
                color: var(--primary);
            }

            .status-pill.success {
                background: var(--success-light);
                color: var(--success);
            }

            /* --- Actions/Buttons --- */
            .btn-pdf-jour {
                background: linear-gradient(135deg, #f59e0b, #d97706);
                color: white !important;
                border: none;
                padding: 0.75rem 1.5rem;
                border-radius: 50px;
                font-size: 0.85rem;
                font-weight: 700;
                cursor: pointer;
                transition: var(--transition);
                display: flex;
                align-items: center;
                gap: 0.5rem;
                box-shadow: 0 4px 15px rgba(217, 119, 6, 0.3);
            }

            .btn-pdf-jour:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(217, 119, 6, 0.4);
            }

            /* --- Custom Scrollbar --- */
            ::-webkit-scrollbar {
                width: 6px;
            }

            ::-webkit-scrollbar-track {
                background: var(--neutral-100);
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb {
                background: var(--neutral-200);
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #cbd5e1;
            }
        </style>
    </head>

    <body>
        <div class="dashboard-container">

            <!-- HEADER -->
            <header class="dashboard-header animate-up">
                <div class="header-title">
                    <h1>Vue d'ensemble</h1>
                    <p>Ravi de vous revoir, Comptable <strong>{{ Auth::guard('comptable')->user()->name }} {{ Auth::guard('comptable')->user()->prenom }}</strong></p>
                </div>
                <div class="header-actions">
                    <div class="date-badge">
                        <i class="far fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                    </div>
                </div>
            </header>

            <!-- GRID KPI (Demandes) -->
            <div class="grid-stats animate-up" style="animation-delay: 0.1s;">
                <div class="kpi-card kpi-naissance">
                    <div class="kpi-content">
                        <p>Demandes Naissances</p>
                        <h3>{{ number_format($naissancenombre, 0, ',', ' ') }}</h3>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-baby"></i>
                    </div>
                </div>

                <div class="kpi-card kpi-deces">
                    <div class="kpi-content">
                        <p>Demandes Décès</p>
                        <h3>{{ number_format($decesnombre, 0, ',', ' ') }}</h3>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-cross"></i>
                    </div>
                </div>

                <div class="kpi-card kpi-mariage">
                    <div class="kpi-content">
                        <p>Demandes Mariages</p>
                        <h3>{{ number_format($mariagenombre, 0, ',', ' ') }}</h3>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>

                <div class="kpi-card kpi-total">
                    <div class="kpi-content">
                        <p>Total Demandes</p>
                        <h3>{{ number_format($total, 0, ',', ' ') }}</h3>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>

            <!-- SITUATION FINANCIÈRE SECTION -->
            <div class="section-title-flex animate-up" style="animation-delay: 0.2s;">
                <div class="section-title" style="margin: 0; flex: 1;">
                    <i class="fas fa-wallet"></i> Situation Financière & Portefeuille
                </div>
                <button onclick="exporterPDFJournalier()" class="btn-pdf-jour" title="Télécharger les ventes du jour en PDF">
                    <i class="fas fa-file-pdf"></i> Rapport journalier (PDF)
                </button>
            </div>

            <div class="grid-finance animate-up" style="animation-delay: 0.3s;">
                <!-- Portefeuille Virtuel Premium -->
                <a href="{{ route('comptable.portefeuille.index') }}" style="text-decoration: none; color: inherit; display: block; height: 100%;">
                    <div class="glass-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="card-chip"></div>
                            <span class="card-badge-online">E-timbre En Ligne</span>
                        </div>
                        <div>
                            <span class="wallet-title">CUMUL DU MOIS EN COURS</span>
                            <div class="wallet-balance-container">
                                <span id="wallet-balance" data-target="{{ $soldePortefeuille }}">0</span>
                                <span class="currency">FCFA</span>
                            </div>
                        </div>
                        <div class="card-holder-info">
                            <div>
                                <span class="holder-lbl">COMMUNE DE</span>
                                <span class="holder-val">{{ Auth::guard('comptable')->user()->communeM }}</span>
                            </div>
                            <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Logo Plateau" style="height: 32px; filter: brightness(0) invert(1);">
                        </div>
                    </div>
                </a>

                <!-- Caisse KKS / Solde Disponible -->
                <div class="finance-card allocated">
                    <div class="finance-card-header">
                        <div class="finance-info">
                            <h4>Solde Caisse KKS</h4>
                            <span class="amount">{{ number_format($montantRestant ?? 0, 0, ',', ' ') }} FCFA</span>
                            <span class="finance-sub">Fonds d'actes physiques restants</span>
                        </div>
                        <div class="finance-icon-bg">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>

                <!-- Stock Timbres Physiques -->
                <div class="finance-card stock">
                    <div class="finance-card-header">
                        <div class="finance-info">
                            <h4>Stock Timbres</h4>
                            <span class="amount">{{ number_format($soldeTimbres, 0, ',', ' ') }}</span>
                            <span class="finance-sub">Timbres physiques disponibles</span>
                        </div>
                        <div class="finance-icon-bg">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                </div>

                <!-- Ventes Jour & Ventes Mois -->
                <div class="finance-card sales-day">
                    <div class="finance-card-header">
                        <div class="finance-info">
                            <h4>Ventes Aujourd'hui</h4>
                            <span class="amount">{{ number_format($montantAujourdhui, 0, ',', ' ') }} FCFA</span>
                            <span class="finance-sub"><strong>{{ number_format($timbresAujourdhui, 0, ',', ' ') }}</strong> timbres vendus</span>
                        </div>
                        <div class="finance-icon-bg">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS & LISTS -->
            <div class="grid-main animate-up" style="animation-delay: 0.4s;">
                <!-- Graphiques -->
                <div class="card-section">
                    <div class="card-header-flex">
                        <div class="card-heading">
                            <i class="fas fa-chart-line"></i> Analyses & Tendance
                        </div>
                        <div class="tabs-container">
                            <button class="tab-btn active" onclick="switchChart('weekly', this)">Actes (7j)</button>
                            <button class="tab-btn" onclick="switchChart('sales', this)">Ventes Timbres</button>
                            <button class="tab-btn" onclick="switchChart('dist', this)">Répartition</button>
                        </div>
                    </div>

                    <div style="height: 330px; width: 100%; position: relative; margin-top: 1rem;">
                        <div id="chart-wrapper-weekly" style="height:100%; width:100%;">
                            <canvas id="weeklyChart"></canvas>
                        </div>
                        <div id="chart-wrapper-sales" style="height:100%; width:100%; display:none;">
                            <canvas id="timbresChart"></canvas>
                        </div>
                        <div id="chart-wrapper-dist" style="height:100%; width:100%; display:none;">
                            <div style="height:100%; width:100%; display:flex; justify-content:center;">
                                <canvas id="distributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Récents -->
                <div class="card-section">
                    <div class="card-header-flex">
                        <div class="card-heading">
                            <i class="fas fa-clock"></i> Récemment
                        </div>
                        <div class="tabs-container">
                            <button class="tab-btn active" onclick="switchList('requests', this)">Demandes</button>
                            <button class="tab-btn" onclick="switchList('sales', this)">Ventes</button>
                        </div>
                    </div>

                    <!-- Liste Demandes -->
                    <div id="list-requests" class="recent-list">
                        @forelse($recentDemandes as $demande)
                            <div class="recent-item">
                                @if($demande->type_demande == 'naissance')
                                    <div class="icon-circle" style="background:rgba(31, 64, 131, 0.08); color:var(--primary);">
                                        <i class="fas fa-baby"></i>
                                    </div>
                                    <div class="recent-info">
                                        <h5>Naissance - {{ $demande->reference }}</h5>
                                        <span>{{ $demande->created_at->locale('fr')->diffForHumans() }}</span>
                                    </div>
                                @elseif($demande->type_demande == 'deces')
                                    <div class="icon-circle" style="background:rgba(30, 41, 59, 0.08); color:var(--neutral-700);">
                                        <i class="fas fa-cross"></i>
                                    </div>
                                    <div class="recent-info">
                                        <h5>Décès - {{ $demande->reference }}</h5>
                                        <span>{{ $demande->created_at->locale('fr')->diffForHumans() }}</span>
                                    </div>
                                @elseif($demande->type_demande == 'mariage')
                                    <div class="icon-circle" style="background:rgba(247, 37, 133, 0.08); color:var(--warning);">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div class="recent-info">
                                        <h5>Mariage - {{ $demande->reference }}</h5>
                                        <span>{{ $demande->created_at->locale('fr')->diffForHumans() }}</span>
                                    </div>
                                @endif
                                <span class="status-pill primary">{{ $demande->statut }}</span>
                            </div>
                        @empty
                            <div class="text-center text-muted p-5 my-auto">
                                <i class="fas fa-folder-open fa-3x mb-3 text-muted" style="opacity: 0.5;"></i>
                                <p class="mb-0 small">Aucune demande récente</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Liste Ventes -->
                    <div id="list-sales" class="recent-list" style="display:none;">
                        @forelse($dernieresVentesTimbres as $vente)
                            <div class="recent-item">
                                <div class="icon-circle" style="background:var(--success-light); color:var(--success);">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="recent-info">
                                    <h5>Achat de {{ abs($vente->nombre_timbre) }} timbres</h5>
                                    <span>{{ $vente->created_at->locale('fr')->format('d M Y à H:i') }}</span>
                                </div>
                                <span class="status-pill success">{{ number_format(abs($vente->nombre_timbre) * 500, 0, ',', ' ') }} F</span>
                            </div>
                        @empty
                            <div class="text-center text-muted p-5 my-auto">
                                <i class="fas fa-folder-open fa-3x mb-3 text-muted" style="opacity: 0.5;"></i>
                                <p class="mb-0 small">Aucune vente récente</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <!-- SCRIPTS -->
        <script>
            // Animation progressive du solde du portefeuille en ligne
            function animateValue(obj, start, end, duration) {
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    obj.textContent = Math.floor(progress * (end - start) + start).toLocaleString('fr-FR');
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            }

            // Gestion de l'affichage des listes
            function switchList(type, btn) {
                document.getElementById('list-requests').style.display = 'none';
                document.getElementById('list-sales').style.display = 'none';
                document.getElementById('list-' + type).style.display = 'flex';

                const buttons = btn.parentElement.querySelectorAll('button');
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            }

            // Gestion de l'affichage des graphiques
            function switchChart(type, btn) {
                document.getElementById('chart-wrapper-weekly').style.display = 'none';
                document.getElementById('chart-wrapper-sales').style.display = 'none';
                document.getElementById('chart-wrapper-dist').style.display = 'none';

                document.getElementById('chart-wrapper-' + type).style.display = (type === 'dist') ? 'flex' : 'block';
                if (type === 'dist') document.getElementById('chart-wrapper-' + type).style.justifyContent = 'center';

                const buttons = btn.parentElement.querySelectorAll('button');
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            }

            // Initialisation des graphiques et animations
            document.addEventListener('DOMContentLoaded', function () {
                // Lancer l'animation progressive du solde
                const walletBalanceEl = document.getElementById('wallet-balance');
                if (walletBalanceEl) {
                    const targetVal = parseInt(walletBalanceEl.getAttribute('data-target'), 10) || 0;
                    animateValue(walletBalanceEl, 0, targetVal, 1500);
                }

                // Options communes
                const commonOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, font: { family: 'Plus Jakarta Sans', weight: 600 }, boxWidth: 6 } }
                    }
                };

                // 1. Graphique 7j (Actes)
                const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
                new Chart(weeklyCtx, {
                    type: 'line',
                    data: {
                        labels: ['J-6', 'J-5', 'J-4', 'J-3', 'J-2', 'Hier', 'Aujourd\'hui'],
                        datasets: [
                            { label: 'Naissances', data: @json($weeklyData['naissances']), borderColor: '#1f4083', backgroundColor: 'rgba(31, 64, 131, 0.05)', tension: 0.4, fill: true },
                            { label: 'Décès', data: @json($weeklyData['deces']), borderColor: '#64748b', backgroundColor: 'rgba(100, 116, 139, 0.02)', tension: 0.4, fill: true },
                            { label: 'Mariages', data: @json($weeklyData['mariages']), borderColor: '#f72585', backgroundColor: 'rgba(247, 37, 133, 0.05)', tension: 0.4, fill: true }
                        ]
                    },
                    options: { ...commonOptions, scales: { y: { beginAtZero: true } } }
                });

                // 2. Graphique des ventes de timbres
                const timbresCtx = document.getElementById('timbresChart').getContext('2d');
                new Chart(timbresCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($labelsTimbres),
                        datasets: [{
                            label: 'Ventes Timbres',
                            data: @json($valeursTimbres),
                            backgroundColor: 'rgba(14, 165, 233, 0.85)',
                            borderRadius: 6
                        }]
                    },
                    options: commonOptions
                });

                // 3. Graphique répartition (Doughnut)
                const distCtx = document.getElementById('distributionChart').getContext('2d');
                new Chart(distCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Naissances', 'Décès', 'Mariages'],
                        datasets: [{
                            data: [{{ $naissancenombre }}, {{ $decesnombre }}, {{ $mariagenombre }}],
                            backgroundColor: ['#1f4083', '#64748b', '#f72585'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'right', labels: { font: { family: 'Plus Jakarta Sans', weight: 600 } } } }
                    }
                });
            });
        </script>

        <!-- Script de génération du PDF -->
        <script>
            window.jsPDF = window.jspdf.jsPDF;

            function exporterPDFJournalier() {
                try {
                    const doc = new jsPDF();
                    const today = new Date();
                    const dateStr = today.toLocaleDateString('fr-FR');
                    const comptableName = "{{ Auth::guard('comptable')->user()->name }} {{ Auth::guard('comptable')->user()->prenom }}";
                    const todayISO = today.toISOString().slice(0, 10);

                    // En-tête bleu premium
                    doc.setFillColor(31, 64, 131);
                    doc.rect(0, 0, 210, 40, 'F');
                    doc.setTextColor(255, 255, 255);
                    doc.setFontSize(20);
                    doc.setFont(undefined, 'bold');
                    doc.text('RAPPORT JOURNALIER DE VENTES', 105, 18, { align: 'center' });
                    doc.setFontSize(11);
                    doc.setFont(undefined, 'normal');
                    doc.text('Date : ' + dateStr + '  |  Comptable : ' + comptableName, 105, 28, { align: 'center' });

                    // Section Résumé
                    doc.setTextColor(15, 23, 42);
                    doc.setFontSize(14);
                    doc.setFont(undefined, 'bold');
                    doc.text('1. Indicateurs financiers & Caisse', 14, 52);

                    const timbresAuj = '{{ number_format($timbresAujourdhui, 0, ",", " ") }}';
                    const montantAuj = '{{ number_format($montantAujourdhui, 0, ",", " ") }}';
                    const timbresMois = '{{ number_format($timbresMois, 0, ",", " ") }}';
                    const montantMois = '{{ number_format($montantMois, 0, ",", " ") }}';
                    const soldeTimbres = '{{ number_format($soldeTimbres, 0, ",", " ") }}';
                    const soldePortefeuilleVal = '{{ number_format($soldePortefeuille, 0, ",", " ") }}';

                    // Tableau récapitulatif
                    doc.autoTable({
                        startY: 60,
                        head: [['Rubrique', 'Quantité / Détails', 'Solde / Montant']],
                        body: [
                            ["Ventes d'aujourd'hui", timbresAuj + ' timbres physiques', montantAuj + ' FCFA'],
                            ["Ventes du mois", timbresMois + ' timbres physiques', montantMois + ' FCFA'],
                            ['Stock physique disponible', soldeTimbres + ' timbres en stock', '-'],
                            ['Portefeuille virtuel en ligne', 'Timbres collectés via Wave/MTN', soldePortefeuilleVal + ' FCFA']
                        ],
                        theme: 'grid',
                        styles: { fontSize: 10, cellPadding: 5, font: 'helvetica' },
                        headStyles: { fillColor: [31, 64, 131], textColor: 255, fontStyle: 'bold' },
                        alternateRowStyles: { fillColor: [248, 250, 252] },
                        columnStyles: {
                            0: { fontStyle: 'bold', cellWidth: 70 },
                            1: { halign: 'left' },
                            2: { halign: 'right', fontStyle: 'bold' }
                        }
                    });

                    // Dernières ventes
                    const finalY = doc.lastAutoTable.finalY + 15;
                    doc.setFontSize(14);
                    doc.setFont(undefined, 'bold');
                    doc.text('2. Dernières transactions enregistrées', 14, finalY);

                    const ventesHeaders = [['Quantité', 'Date & Heure', 'Montant perçu']];
                    const ventesRows = [];

                    @foreach($dernieresVentesTimbres as $vente)
                        ventesRows.push([
                            '{{ abs($vente->nombre_timbre) }} timbres physiques',
                            '{{ $vente->created_at->locale("fr")->format("d M Y à H:i") }}',
                            '{{ number_format(abs($vente->nombre_timbre) * 500, 0, ",", " ") }} FCFA'
                        ]);
                    @endforeach

                    if (ventesRows.length > 0) {
                        doc.autoTable({
                            startY: finalY + 6,
                            head: ventesHeaders,
                            body: ventesRows,
                            theme: 'grid',
                            styles: { fontSize: 9, cellPadding: 4 },
                            headStyles: { fillColor: [71, 85, 105], textColor: 255, fontStyle: 'bold' },
                            alternateRowStyles: { fillColor: [248, 250, 252] },
                            columnStyles: {
                                2: { halign: 'right', fontStyle: 'bold' }
                            }
                        });
                    } else {
                        doc.setFont(undefined, 'normal');
                        doc.setFontSize(10);
                        doc.setTextColor(100);
                        doc.text('Aucune vente enregistrée aujourd\'hui.', 14, finalY + 10);
                    }

                    // Bas de page
                    const pageCount = doc.internal.getNumberOfPages();
                    for (let i = 1; i <= pageCount; i++) {
                        doc.setPage(i);
                        doc.setFontSize(8);
                        doc.setTextColor(148, 163, 184);
                        doc.text('Mairie de Plateau — Rapport de Régie. Généré le ' + dateStr + ' à ' + today.toLocaleTimeString('fr-FR') + ' — Page ' + i + '/' + pageCount, 105, 287, { align: 'center' });
                    }

                    doc.save('rapport_regie_' + todayISO + '.pdf');
                } catch (error) {
                    console.error('Erreur PDF journalier:', error);
                    alert('Une erreur est survenue lors de la génération du PDF.');
                }
            }
        </script>
    </body>

    </html>
@endsection
