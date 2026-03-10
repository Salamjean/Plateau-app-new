@extends('finance.layouts.template')

@section('content')
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tableau de Bord</title>

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
                --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.08);
                --transition: all 0.3s ease;
            }

            body {
                font-family: 'Inter', sans-serif;
                background-color: #f3f6fd;
                /* Fond très léger bleuté */
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
                margin: 0;
                color: #1977cc;
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

            /* --- Stats Grid (Top) --- */
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
                position: relative;
                overflow: hidden;
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

            /* Couleurs Spécifiques KPI */
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


            /* --- Finance Section (Middle) --- */
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
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
                margin-bottom: 2rem;
            }

            @media (max-width: 992px) {
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
                position: relative;
            }

            .finance-card.add {
                border-color: #2ecc71;
            }

            .finance-card.debit {
                border-color: #e74c3c;
            }

            .finance-card.balance {
                border-color: #f1c40f;
            }

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
            }

            .finance-info .amount {
                font-size: 1.8rem;
                font-weight: 700;
                margin-top: 5px;
                display: block;
            }

            .finance-card.add .amount {
                color: #27ae60;
            }

            .finance-card.debit .amount {
                color: #c0392b;
            }

            .finance-card.balance .amount {
                color: #f39c12;
            }

            .finance-icon-bg {
                font-size: 2rem;
                opacity: 0.1;
            }

            .finance-detail {
                font-size: 0.85rem;
                color: var(--text-muted);
                background: var(--gray-100);
                padding: 8px 12px;
                border-radius: 8px;
                display: inline-block;
            }


            /* --- Main Content Split (Charts & Recent) --- */
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

            .chart-tabs {
                background: var(--gray-100);
                padding: 4px;
                border-radius: 8px;
                display: flex;
            }

            .chart-tab-btn {
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

            .chart-tab-btn.active {
                background: white;
                color: var(--primary);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            /* Liste Récente */
            .recent-list {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                max-height: 400px;
                overflow-y: auto;
                padding-right: 5px;
            }

            .recent-item {
                display: flex;
                align-items: center;
                padding: 10px;
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
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 12px;
                font-size: 1rem;
                flex-shrink: 0;
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
            }

            .status-pill {
                margin-left: auto;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                background: var(--light);
                color: var(--text-muted);
            }

            .status-pill.new {
                background: #d1e7dd;
                color: #0f5132;
            }

            /* Scrollbar custom */
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
                    <p>Bienvenue sur votre espace de gestion des recettes</p>
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
                <!-- Naissances -->
                <div class="kpi-card kpi-naissance">
                    <div class="kpi-content">
                        <p>Demandes d'actes de naissances</p>
                        <h3>{{ $naissancenombre }}</h3>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-baby"></i>
                    </div>
                </div>

                <!-- Décès -->
                <div class="kpi-card kpi-deces">
                    <div class="kpi-content">
                        <p>Demandes d'actes de décès</p>
                        <h3>{{ $decesnombre }}</h3>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-cross"></i>
                    </div>
                </div>

                <!-- Mariages -->
                <div class="kpi-card kpi-mariage">
                    <div class="kpi-content">
                        <p>Demandes d'actes de mariages</p>
                        <h3>{{ $mariagenombre }}</h3>
                    </div>
                    <div class="kpi-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>

                <!-- Total -->
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

            <!-- FINANCE SECTION -->
            <div class="section-title">
                <i class="fas fa-coins"></i> Situation Financière de la Mairie
            </div>

            <div class="grid-finance">
                <!-- Débité -->
                <div class="finance-card debit">
                    <div class="finance-header">
                        <div class="finance-info">
                            <h4>Dépenses (Demandes)</h4>
                            <span class="amount">{{ number_format($montantTotalDebite, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="finance-icon-bg">
                            <i class="fas fa-arrow-down" style="color: #e74c3c;"></i>
                        </div>
                    </div>
                    <div class="finance-detail">
                        <i class="fas fa-receipt"></i> Débits cumulés
                    </div>
                </div>

                <!-- Restant -->
                <div class="finance-card balance">
                    <div class="finance-header">
                        <div class="finance-info">
                            <h4>Solde Disponible</h4>
                            <span class="amount">{{ number_format($montantRestant, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div class="finance-icon-bg">
                            <i class="fas fa-wallet" style="color: #f39c12;"></i>
                        </div>
                    </div>
                    <div class="finance-detail">
                        <i class="fas fa-check-circle"></i> Montant actuel
                    </div>
                </div>
            </div>

            <!-- MAIN CHART & RECENT LIST -->
            <div class="grid-main">
                <!-- Graphique -->
                <div class="chart-container-card">
                    <div class="card-header-flex">
                        <div class="card-heading">Évolution des Activités</div>
                        <div class="chart-tabs">
                            <button class="chart-tab-btn active" onclick="updateChart('weekly', this)">7 Jours</button>
                            <button class="chart-tab-btn" onclick="updateChart('monthly', this)">30 Jours</button>
                            <button class="chart-tab-btn" onclick="updateChart('yearly', this)">12 Mois</button>
                        </div>
                    </div>
                    <div style="height: 300px; width: 100%;">
                        <canvas id="mainChart"></canvas>
                    </div>
                </div>

                <!-- Demandes Récentes -->
                <div class="chart-container-card">
                    <div class="card-header-flex">
                        <div class="card-heading">Récemment</div>
                    </div>

                    <div class="recent-tabs" style="display:flex; gap:10px; margin-bottom:15px;">
                        <button class="chart-tab-btn active" onclick="showRecent('naissance', this)">Actes de naissances</button>
                        <button class="chart-tab-btn" onclick="showRecent('deces', this)">Actes de décès</button>
                        <button class="chart-tab-btn" onclick="showRecent('mariage', this)">Actes de mariages</button>
                    </div>

                    <div class="recent-list" id="list-naissance">
                        @forelse($demandesNaissance as $demande)
                            <div class="recent-item">
                                <div class="icon-circle" style="background:#eaf2ff; color:#4361ee;">
                                    <i class="fas fa-baby"></i>
                                </div>
                                <div class="recent-info">
                                    <h5>Demande #{{ $demande->reference }}</h5>
                                    <span>{{ $demande->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="status-pill new">Nouveau</span>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">Aucune demande récente</div>
                        @endforelse
                    </div>

                    <div class="recent-list" id="list-deces" style="display:none;">
                        @forelse($demandesDeces as $demande)
                            <div class="recent-item">
                                <div class="icon-circle" style="background:#f2f2f2; color:#333;">
                                    <i class="fas fa-cross"></i>
                                </div>
                                <div class="recent-info">
                                    <h5>Demande #{{ $demande->reference }}</h5>
                                    <span>{{ $demande->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="status-pill new">Nouveau</span>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">Aucune demande récente</div>
                        @endforelse
                    </div>

                    <div class="recent-list" id="list-mariage" style="display:none;">
                        @forelse($demandesMariage as $demande)
                            <div class="recent-item">
                                <div class="icon-circle" style="background:#fff0f6; color:#f72585;">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="recent-info">
                                    <h5>Demande #{{ $demande->reference }}</h5>
                                    <span>{{ $demande->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="status-pill new">Nouveau</span>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3">Aucune demande récente</div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>

        <!-- Scripts -->
        <script>
            // Gestion des onglets "Récemment"
            function showRecent(type, btn) {
                // Masquer toutes les listes
                document.getElementById('list-naissance').style.display = 'none';
                document.getElementById('list-deces').style.display = 'none';
                document.getElementById('list-mariage').style.display = 'none';

                // Afficher la liste choisie
                document.getElementById('list-' + type).style.display = 'flex';

                // Gérer la classe active
                const buttons = btn.parentElement.querySelectorAll('button');
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            }

            // Configuration Charts
            const ctx = document.getElementById('mainChart').getContext('2d');

            // Données injectées depuis le backend
            const chartData = {
                weekly: {
                    labels: ['J-6', 'J-5', 'J-4', 'J-3', 'J-2', 'Hier', 'Aujourd\'hui'],
                    datasets: [
                        { label: 'Naissances', data: @json($weeklyData['naissances']), borderColor: '#4361ee', backgroundColor: 'rgba(67, 97, 238, 0.1)', tension: 0.4, fill: true },
                        { label: 'Décès', data: @json($weeklyData['deces']), borderColor: '#343a40', backgroundColor: 'rgba(52, 58, 64, 0.05)', tension: 0.4, fill: true, borderDash: [5, 5] },
                        { label: 'Mariages', data: @json($weeklyData['mariages']), borderColor: '#f72585', backgroundColor: 'rgba(247, 37, 133, 0.1)', tension: 0.4, fill: true }
                    ]
                },
                monthly: {
                    labels: Array.from({ length: 30 }, (_, i) => i + 1),
                    datasets: [
                        { label: 'Naissances', data: @json($monthlyData['naissances']), borderColor: '#4361ee', tension: 0.4 },
                        { label: 'Décès', data: @json($monthlyData['deces']), borderColor: '#343a40', tension: 0.4 },
                        { label: 'Mariages', data: @json($monthlyData['mariages']), borderColor: '#f72585', tension: 0.4 }
                    ]
                },
                yearly: {
                    labels: ['M-11', 'M-10', 'M-9', 'M-8', 'M-7', 'M-6', 'M-5', 'M-4', 'M-3', 'M-2', 'M-1', 'Actuel'],
                    datasets: [
                        { label: 'Naissances', data: @json($yearlyData['naissances']), borderColor: '#4361ee', tension: 0.4 },
                        { label: 'Décès', data: @json($yearlyData['deces']), borderColor: '#343a40', tension: 0.4 },
                        { label: 'Mariages', data: @json($yearlyData['mariages']), borderColor: '#f72585', tension: 0.4 }
                    ]
                }
            };

            let myChart = new Chart(ctx, {
                type: 'line',
                data: chartData.weekly,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 6 } },
                        tooltip: { mode: 'index', intersect: false, backgroundColor: 'rgba(255, 255, 255, 0.9)', titleColor: '#333', bodyColor: '#666', borderColor: '#eee', borderWidth: 1 }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f0f0f0' }, ticks: { font: { family: 'Inter' } } },
                        x: { grid: { display: false }, ticks: { font: { family: 'Inter' } } }
                    }
                }
            });

            window.updateChart = function (period, btn) {
                // Update Boutons
                const buttons = btn.parentElement.querySelectorAll('button');
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Update Data
                myChart.data = chartData[period];
                myChart.update();
            }
        </script>
    </body>

    </html>
@endsection