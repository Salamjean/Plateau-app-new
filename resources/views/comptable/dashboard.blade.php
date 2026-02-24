@extends('comptable.layouts.template')
@section('content')

    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tableau de Bord | Moderne</title>

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <style>
            :root {
                --primary: #4361ee;
                --secondary: #3f37c9;
                --success: #4cc9f0;
                --info: #4895ef;
                --warning: #f72585;
                --danger: #e63946;
                --light: #f8f9fa;
                --dark: #212529;
                --gray-100: #f8f9fa;
                --gray-200: #e9ecef;
                --gray-600: #6c757d;
                --text-main: #2b2d42;
                --text-muted: #8d99ae;
                --card-bg: #ffffff;
                --border-radius: 16px;
                --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.02);
                --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.05);
                --transition: all 0.3s ease;
            }

            body {
                font-family: 'Inter', sans-serif;
                background-color: #f3f6fd;
                color: var(--text-main);
                margin: 0;
                padding: 0;
            }

            .dashboard-container {
                padding: 2rem;
                width: 95%;
                margin: 0 auto;
            }

            /* --- Header --- */
            .dashboard-header {
                margin-bottom: 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .header-title h1 {
                font-size: 1.75rem;
                font-weight: 700;
                color: #1977cc;
                margin: 0;
            }

            .header-title p {
                color: var(--text-muted);
                margin-top: 0.5rem;
                font-size: 0.95rem;
            }

            .header-actions .date-badge {
                background: white;
                padding: 0.5rem 1rem;
                border-radius: 50px;
                font-size: 0.85rem;
                font-weight: 600;
                color: #1977cc;
                box-shadow: var(--shadow-sm);
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            /* --- KPI Grid (Activités) --- */
            .grid-stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
                gap: 1.5rem;
                margin-bottom: 2rem;
            }

            .kpi-card {
                background: var(--card-bg);
                border-radius: var(--border-radius);
                padding: 1.5rem;
                box-shadow: var(--shadow-sm);
                transition: var(--transition);
                border: 1px solid rgba(0, 0, 0, 0.03);
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .kpi-card:hover {
                transform: translateY(-5px);
                box-shadow: var(--shadow-md);
            }

            .kpi-content h3 {
                font-size: 2rem;
                font-weight: 700;
                margin: 0;
                color: var(--text-main);
            }

            .kpi-content p {
                margin: 0;
                color: var(--text-muted);
                font-size: 0.9rem;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .kpi-icon {
                width: 56px;
                height: 56px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                flex-shrink: 0;
            }

            .kpi-naissance .kpi-icon {
                background: rgba(67, 97, 238, 0.1);
                color: var(--primary);
            }

            .kpi-deces .kpi-icon {
                background: rgba(33, 37, 41, 0.1);
                color: var(--dark);
            }

            .kpi-mariage .kpi-icon {
                background: rgba(247, 37, 133, 0.1);
                color: var(--warning);
            }

            .kpi-total .kpi-icon {
                background: rgba(72, 149, 239, 0.1);
                color: var(--info);
            }


            /* --- Timbre Section (Middle) --- */
            .section-title {
                font-size: 1.1rem;
                font-weight: 600;
                color: var(--text-muted);
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .section-title::after {
                content: '';
                flex: 1;
                height: 1px;
                background: var(--gray-200);
            }

            .grid-finance {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 1.5rem;
                margin-bottom: 2rem;
            }

            @media (max-width: 1200px) {
                .grid-finance {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            @media (max-width: 768px) {
                .grid-finance {
                    grid-template-columns: 1fr;
                }
            }

            .finance-card {
                background: var(--card-bg);
                border-radius: var(--border-radius);
                padding: 1.5rem;
                box-shadow: var(--shadow-sm);
                border-left: 4px solid transparent;
            }

            .finance-card.allocated {
                border-color: #2ecc71;
            }

            .finance-card.sales-day {
                border-color: #4cc9f0;
            }

            /* Bleu clair */
            .finance-card.sales-month {
                border-color: #4361ee;
            }

            /* Bleu primaire */
            .finance-card.stock {
                border-color: #f72585;
            }

            /* Rose */

            .finance-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 1rem;
            }

            .finance-info h4 {
                margin: 0;
                font-size: 0.9rem;
                color: var(--text-muted);
                font-weight: 600;
                margin-bottom: 5px;
            }

            .finance-info .amount {
                font-size: 1.6rem;
                font-weight: 700;
                display: block;
                color: var(--text-main);
            }

            .finance-sub {
                font-size: 0.85rem;
                color: #888;
                margin-top: 4px;
                display: block;
            }

            .finance-icon-bg {
                font-size: 1.8rem;
                opacity: 0.15;
            }

            .finance-card.sales-day .finance-icon-bg {
                color: #4cc9f0;
            }

            .finance-card.sales-month .finance-icon-bg {
                color: #4361ee;
            }

            .finance-card.stock .finance-icon-bg {
                color: #f72585;
            }

            .finance-card.balance {
                border-color: #f39c12;
            }

            .finance-card.balance .finance-icon-bg {
                color: #f39c12;
            }


            /* --- Main Content --- */
            .grid-main {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 1.5rem;
            }

            @media (max-width: 1200px) {
                .grid-main {
                    grid-template-columns: 1fr;
                }
            }

            .chart-container-card {
                background: var(--card-bg);
                border-radius: var(--border-radius);
                padding: 1.5rem;
                box-shadow: var(--shadow-sm);
                height: 100%;
            }

            .card-header-flex {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
            }

            .card-heading {
                font-size: 1.1rem;
                font-weight: 700;
                color: var(--text-main);
            }

            .tabs-container {
                background: var(--gray-100);
                padding: 4px;
                border-radius: 8px;
                display: flex;
                gap: 4px;
            }

            .tab-btn {
                border: none;
                background: transparent;
                padding: 6px 12px;
                font-size: 0.85rem;
                border-radius: 6px;
                cursor: pointer;
                color: var(--text-muted);
                font-weight: 500;
                transition: all 0.2s;
            }

            .tab-btn.active {
                background: white;
                color: var(--primary);
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            /* Recent Lists */
            .recent-list {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                max-height: 450px;
                overflow-y: auto;
                padding-right: 5px;
            }

            .recent-item {
                display: flex;
                align-items: center;
                padding: 12px;
                border-radius: 12px;
                background: #fff;
                border: 1px solid var(--gray-200);
                transition: var(--transition);
            }

            .recent-item:hover {
                border-color: var(--primary);
                background: #f8faff;
            }

            .icon-circle {
                width: 42px;
                height: 42px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 12px;
                flex-shrink: 0;
                font-size: 1.1rem;
            }

            .recent-info {
                flex: 1;
            }

            .recent-info h5 {
                margin: 0;
                font-size: 0.95rem;
                font-weight: 600;
                color: var(--text-main);
            }

            .recent-info span {
                font-size: 0.8rem;
                color: var(--text-muted);
                display: block;
                margin-top: 2px;
            }

            .status-pill {
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                white-space: nowrap;
            }

            .status-pill.primary {
                background: #eaf2ff;
                color: #4361ee;
            }

            .status-pill.success {
                background: #d1e7dd;
                color: #0f5132;
            }

            /* Scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
            }

            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            ::-webkit-scrollbar-thumb {
                background: #ccc;
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #aaa;
            }
        </style>
    </head>

    <body>
        <div class="dashboard-container">

            <!-- HEADER -->
            <header class="dashboard-header">
                <div class="header-title">
                    <h1>Vue d'ensemble</h1>
                    <p>Bienvenue, {{ Auth::guard('comptable')->user()->name }}
                        {{ Auth::guard('comptable')->user()->prenom }}
                    </p>
                </div>
                <div class="header-actions">
                    <div class="date-badge">
                        <i class="far fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('D MMMM YYYY') }}
                    </div>
                </div>
            </header>

            <!-- KPI GRID (Activités) -->
            <div class="grid-stats">
                <div class="kpi-card kpi-naissance">
                    <div class="kpi-content">
                        <p>Demandes d'actes de naissances</p>
                        <h3>{{ $naissancenombre }}</h3>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-baby"></i>
                    </div>
                </div>

                <div class="kpi-card kpi-deces">
                    <div class="kpi-content">
                        <p>Demandes d'actes de décès</p>
                        <h3>{{ $decesnombre }}</h3>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-cross"></i>
                    </div>
                </div>

                <div class="kpi-card kpi-mariage">
                    <div class="kpi-content">
                        <p>Demandes d'actes de mariages</p>
                        <h3>{{ $mariagenombre }}</h3>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>

                <div class="kpi-card kpi-total">
                    <div class="kpi-content">
                        <p>Total demandes d'actes</p>
                        <h3>{{ $total }}</h3>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>

            <!-- FINANCE/TIMBRES SECTION -->
            <div class="section-title">
                <i class="fas fa-stamp"></i> Gestion des Timbres
            </div>

            <div class="grid-finance">
                <!-- Ventes Aujourd'hui -->
                <div class="finance-card sales-day">
                    <div class="finance-header">
                        <div class="finance-info">
                            <h4>Ventes Aujourd'hui</h4>
                            <span class="amount">{{ number_format($montantAujourdhui, 0, ',', ' ') }} FCFA</span>
                            <span class="finance-sub">{{ number_format($timbresAujourdhui, 0, ',', ' ') }} timbres
                                vendus</span>
                        </div>
                        <div class="finance-icon-bg">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                </div>

                <!-- Ventes Mois -->
                <div class="finance-card sales-month">
                    <div class="finance-header">
                        <div class="finance-info">
                            <h4>Ventes ce Mois</h4>
                            <span class="amount">{{ number_format($montantMois, 0, ',', ' ') }} FCFA</span>
                            <span class="finance-sub">{{ number_format($timbresMois, 0, ',', ' ') }} timbres vendus</span>
                        </div>
                        <div class="finance-icon-bg">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>

                <!-- Stock Restant -->
                <div class="finance-card stock">
                    <div class="finance-header">
                        <div class="finance-info">
                            <h4>Stock Disponible</h4>
                            <span class="amount">{{ number_format($soldeTimbres, 0, ',', ' ') }}</span>
                            <span class="finance-sub">Timbres en stock</span>
                        </div>
                        <div class="finance-icon-bg">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
                <!-- Solde Disponible -->
                <div class="finance-card balance">
                    <div class="finance-header">
                        <div class="finance-info">
                            <h4>Solde Disponible --> (KKS-TECHNOLOGIES)</h4>
                            <span class="amount">{{ number_format($montantRestant ?? 0, 0, ',', ' ') }} FCFA</span>
                            <span class="finance-sub">Montant actuel</span>
                        </div>
                        <div class="finance-icon-bg">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- MAIN CONTENT (Charts & Lists) -->
            <div class="grid-main">
                <!-- Chart Section -->
                <div class="chart-container-card">
                    <div class="card-header-flex">
                        <div class="card-heading">Analyses & Tendance</div>
                        <div class="tabs-container">
                            <button class="tab-btn active" onclick="switchChart('weekly', this)">Actes (7j)</button>
                            <button class="tab-btn" onclick="switchChart('sales', this)">Ventes Timbres</button>
                            <button class="tab-btn" onclick="switchChart('dist', this)">Répartition</button>
                        </div>
                    </div>

                    <div style="height: 320px; width: 100%; position: relative;">
                        <!-- Canvases superposés, on gère l'affichage -->
                        <div id="chart-wrapper-weekly" style="height:100%; width:100%;">
                            <canvas id="weeklyChart"></canvas>
                        </div>
                        <div id="chart-wrapper-sales" style="height:100%; width:100%; display:none;">
                            <canvas id="timbresChart"></canvas>
                        </div>
                        <div id="chart-wrapper-dist" style="height:100%; width:100%; display:none;">
                            <!-- Pour le doughnut, on limite la taille pour qu'il soit joli -->
                            <div style="height:100%; width:100%; display:flex; justify-content:center;">
                                <canvas id="distributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Lists Section -->
                <div class="chart-container-card">
                    <div class="card-header-flex">
                        <div class="card-heading">Récemment</div>
                        <div class="tabs-container">
                            <button class="tab-btn active" onclick="switchList('requests', this)">Demandes</button>
                            <button class="tab-btn" onclick="switchList('sales', this)">Ventes</button>
                        </div>
                    </div>

                    <!-- Liste Demandes Unifiée -->
                    <div id="list-requests" class="recent-list">
                        @forelse($recentDemandes as $demande)
                            <div class="recent-item">
                                @if($demande->type_demande == 'naissance')
                                    <div class="icon-circle" style="background:#eaf2ff; color:#4361ee;">
                                        <i class="fas fa-baby"></i>
                                    </div>
                                    <div class="recent-info">
                                        <h5>Acte de naissance {{ $demande->reference }}</h5>
                                        <span>{{ $demande->created_at->locale('fr')->diffForHumans() }}</span>
                                    </div>
                                @elseif($demande->type_demande == 'deces')
                                    <div class="icon-circle" style="background:#f2f2f2; color:#333;">
                                        <i class="fas fa-cross"></i>
                                    </div>
                                    <div class="recent-info">
                                        <h5>Acte de décès {{ $demande->reference }}</h5>
                                        <span>{{ $demande->created_at->locale('fr')->diffForHumans() }}</span>
                                    </div>
                                @elseif($demande->type_demande == 'mariage')
                                    <div class="icon-circle" style="background:#fff0f6; color:#f72585;">
                                        <i class="fas fa-heart"></i>
                                    </div>
                                    <div class="recent-info">
                                        <h5>Acte de mariage {{ $demande->reference }}</h5>
                                        <span>{{ $demande->created_at->locale('fr')->diffForHumans() }}</span>
                                    </div>
                                @endif
                                <span class="status-pill primary">{{ $demande->statut }}</span>
                            </div>
                        @empty
                            <div class="text-center text-muted p-4">Aucune demande récente</div>
                        @endforelse
                    </div>

                    <!-- Liste Ventes -->
                    <div id="list-sales" class="recent-list" style="display:none;">
                        @forelse($dernieresVentesTimbres as $vente)
                            <div class="recent-item">
                                <div class="icon-circle" style="background:#e0f7fa; color:#00acc1;">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="recent-info">
                                    <h5>Vente de {{ abs($vente->nombre_timbre) }} timbres</h5>
                                    <span>{{ $vente->created_at->locale('fr')->format('d M Y à H:i') }}</span>
                                </div>
                                <span
                                    class="status-pill success">{{ number_format(abs($vente->nombre_timbre) * 500, 0, ',', ' ') }}
                                    F</span>
                            </div>
                        @empty
                            <div class="text-center text-muted p-4">Aucune vente récente</div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>

        <!-- SCRIPTS -->
        <script>
            // Fonction Switch List
            function switchList(type, btn) {
                document.getElementById('list-requests').style.display = 'none';
                document.getElementById('list-sales').style.display = 'none';
                document.getElementById('list-' + type).style.display = 'flex';

                const buttons = btn.parentElement.querySelectorAll('button');
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            }

            // Fonction Switch Chart
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

            // Charts Initialization
            document.addEventListener('DOMContentLoaded', function () {
                // Options communes
                const commonOptions = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 6 } }
                    }
                };

                // 1. Weekly Chart
                const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
                new Chart(weeklyCtx, {
                    type: 'line',
                    data: {
                        labels: ['J-6', 'J-5', 'J-4', 'J-3', 'J-2', 'Hier', 'Aujourd\'hui'],
                        datasets: [
                            { label: 'Actes de naissances', data: @json($weeklyData['naissances']), borderColor: '#4361ee', backgroundColor: 'rgba(67, 97, 238, 0.1)', tension: 0.4, fill: true },
                            { label: 'Actes de décès', data: @json($weeklyData['deces']), borderColor: '#343a40', backgroundColor: 'rgba(52, 58, 64, 0.05)', tension: 0.4, fill: true, borderDash: [5, 5] },
                            { label: 'Actes de mariages', data: @json($weeklyData['mariages']), borderColor: '#f72585', backgroundColor: 'rgba(247, 37, 133, 0.1)', tension: 0.4, fill: true }
                        ]
                    },
                    options: { ...commonOptions, scales: { y: { beginAtZero: true } } }
                });

                // 2. Timbres Chart
                const timbresCtx = document.getElementById('timbresChart').getContext('2d');
                new Chart(timbresCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($labelsTimbres),
                        datasets: [{
                            label: 'Ventes',
                            data: @json($valeursTimbres),
                            backgroundColor: '#4cc9f0',
                            borderRadius: 4
                        }]
                    },
                    options: commonOptions
                });

                // 3. Distribution Chart
                const distCtx = document.getElementById('distributionChart').getContext('2d');
                new Chart(distCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Actes de naissances', 'Actes de décès', 'Actes de mariages'],
                        datasets: [{
                            data: [{{ $naissancenombre }}, {{ $decesnombre }}, {{ $mariagenombre }}],
                            backgroundColor: ['#4361ee', '#343a40', '#f72585'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'right' } }
                    }
                });
            });
        </script>
    </body>

    </html>
@endsection