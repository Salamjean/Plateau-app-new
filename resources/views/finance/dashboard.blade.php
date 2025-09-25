@extends('finance.layouts.template')
@section('content')
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion Communale</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-green: #1977cc;
            --primary-orange: #ff8800;
            --primary-black: #333333;
            --light-bg: #f8fafc;
            --card-shadow: 0 10px 20px rgba(0, 0, 0, 0.05), 0 6px 6px rgba(0, 0, 0, 0.1);
            --card-radius: 16px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light-bg);
            color: #333;
            line-height: 1.6;
            padding: 0;
            margin: 0;
        }

        .dashboard-container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
        }

        .dashboard-header {
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .dashboard-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-black);
            margin-bottom: 10px;
        }

        .dashboard-header p {
            font-size: 1.1rem;
            color: #666;
            font-weight: 400;
        }

        /* Grille principale */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
        }

        /* Cartes de statistiques principales */
        .stats-grid {
            grid-column: span 12;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }

        .stat-card:nth-child(1)::before { background-color: var(--primary-green); }
        .stat-card:nth-child(2)::before { background-color: var(--primary-black); }
        .stat-card:nth-child(3)::before { background-color: var(--primary-orange); }
        .stat-card:nth-child(4)::before { background-color: var(--primary-green); }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .stat-card:nth-child(1) .stat-icon { background-color: rgba(0, 126, 0, 0.1); }
        .stat-card:nth-child(2) .stat-icon { background-color: rgba(0, 0, 0, 0.1); }
        .stat-card:nth-child(3) .stat-icon { background-color: rgba(255, 136, 0, 0.1); }
        .stat-card:nth-child(4) .stat-icon { background-color: rgba(0, 126, 0, 0.1); }

        .stat-icon i {
            font-size: 24px;
        }

        .stat-card:nth-child(1) .stat-icon i { color: var(--primary-green); }
        .stat-card:nth-child(2) .stat-icon i { color: var(--primary-black); }
        .stat-card:nth-child(3) .stat-icon i { color: var(--primary-orange); }
        .stat-card:nth-child(4) .stat-icon i { color: var(--primary-green); }

        .stat-info h3 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            color: var(--primary-black);
        }

        .stat-info p {
            margin: 5px 0 0;
            color: #666;
            font-weight: 500;
        }

        /* Cartes de statistiques temporelles */
        .time-stats-section {
            grid-column: span 12;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary-black);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .time-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .time-stat-card {
            background: white;
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
        }

        .time-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }

        .time-stat-card h3 {
            color: var(--primary-black);
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }

        .time-stat-card h3 i {
            margin-right: 10px;
            font-size: 1rem;
        }

        .time-stat-content {
            display: flex;
            align-items: center;
        }

        .time-stat-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary-orange);
            margin-right: 20px;
            min-width: 60px;
        }

        .time-stat-details p {
            margin: 8px 0;
            color: #666;
            display: flex;
            align-items: center;
        }

        .time-stat-details i {
            margin-right: 8px;
            font-size: 0.9rem;
        }

        /* Cartes de statistiques financières */
        .finance-stats-section {
            display: block;
            grid-column: span 4;
        }

        .finance-stats-grid {
            display: grid;
            grid-template-rows: repeat(3, auto);
            gap: 20px;
        }

        .finance-stat-card {
            background: white;
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
        }

        .finance-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }

        .finance-stat-card h3 {
            color: var(--primary-black);
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
        }

        .finance-stat-card h3 i {
            margin-right: 10px;
            font-size: 1rem;
        }

        .finance-stat-content {
            display: flex;
            align-items: center;
        }

        .finance-stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            background-color: rgba(0, 126, 0, 0.1);
            flex-shrink: 0;
        }

        .finance-stat-icon i {
            font-size: 20px;
            color: var(--primary-green);
        }

        .finance-stat-details {
            flex: 1;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            align-items: center;
        }

        .stat-row span:first-child {
            color: #666;
            font-size: 0.95rem;
        }

        .stat-row span:last-child {
            font-weight: 600;
            color: var(--primary-black);
        }

        .solde-card .solde-content {
            text-align: center;
            padding: 10px 0;
        }

        .solde-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            background-color: rgba(0, 126, 0, 0.1);
        }

        .solde-icon i {
            font-size: 30px;
            color: var(--primary-green);
        }

        .solde-amount {
            font-size: 42px;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 5px;
        }

        .solde-content p {
            color: #666;
            margin: 0;
            font-weight: 500;
        }

        /* Graphiques et demandes récentes */
        .charts-section {
            grid-column: span 8;
        }

        .charts-container {
            background: white;
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--card-shadow);
            height: 100%;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-header h3 {
            color: var(--primary-black);
            margin: 0;
            font-size: 1.3rem;
        }

        .chart-tabs {
            display: flex;
            background: #f5f7f9;
            border-radius: 50px;
            padding: 4px;
        }

        .chart-tab {
            background: none;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            color: #666;
            font-weight: 500;
            border-radius: 50px;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .chart-tab.active {
            background: white;
            color: var(--primary-orange);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .chart-content {
            height: 300px;
            position: relative;
        }

        /* Demandes récentes */
        .recent-demands-section {
            grid-column: span 4;
        }

        .recent-demands {
            background: white;
            border-radius: var(--card-radius);
            padding: 24px;
            box-shadow: var(--card-shadow);
            height: 100%;
        }

        .recent-demands h3 {
            color: var(--primary-black);
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .demands-tabs {
            display: flex;
            background: #f5f7f9;
            border-radius: 50px;
            padding: 4px;
            margin-bottom: 20px;
        }

        .demand-tab {
            background: none;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            color: #666;
            font-weight: 500;
            border-radius: 50px;
            font-size: 0.9rem;
            transition: var(--transition);
            flex: 1;
            text-align: center;
        }

        .demand-tab.active {
            background: white;
            color: var(--primary-green);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .demands-content {
            max-height: 300px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .demands-content::-webkit-scrollbar {
            width: 6px;
        }

        .demands-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .demands-content::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        .demand-item {
            display: flex;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .demand-item:last-child {
            border-bottom: none;
        }

        .demand-info h4 {
            margin: 0 0 5px;
            color: var(--primary-black);
            font-size: 0.95rem;
            font-weight: 600;
        }

        .demand-info p {
            margin: 0;
            color: #666;
            font-size: 0.85rem;
        }

        .demand-status {
            flex-shrink: 0;
        }

        .status-badge {
            background-color: rgba(0, 126, 0, 0.1);
            color: var(--primary-green);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .hidden {
            display: none;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: repeat(6, 1fr);
            }
            
            .time-stats-section, .charts-section {
                grid-column: span 6;
            }
            
            .finance-stats-section, .recent-demands-section {
                grid-column: span 3;
            }
        }
        
        @media (max-width: 900px) {
            .time-stats-section, .finance-stats-section, .charts-section, .recent-demands-section {
                grid-column: span 6;
            }
            
            .time-stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 600px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .time-stats-section, .finance-stats-section, .charts-section, .recent-demands-section {
                grid-column: span 6;
            }
            
            .time-stat-content, .finance-stat-content {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .time-stat-number {
                margin-bottom: 10px;
            }
            
            .finance-stat-icon {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- En-tête du tableau de bord -->
        <div class="dashboard-header">
            <h1>Tableau de Bord</h1>
            <p>Vue d'ensemble des activités de votre commune</p>
        </div>

        <!-- Cartes de statistiques principales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-baby"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $naissancenombre }}</h3>
                    <p>Naissances</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-cross"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $decesnombre }}</h3>
                    <p>Décès</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $mariagenombre }}</h3>
                    <p>Mariages</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $total }}</h3>
                    <p>Total Demandes</p>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Cartes de statistiques temporelles -->
            <div class="time-stats-section">
                <h2 class="section-title"><i class="fas fa-calendar-alt"></i> Statistiques Temporelles</h2>
                <div class="time-stats-grid">
                    <div class="time-stat-card">
                        <h3><i class="fas fa-sun"></i> Aujourd'hui</h3>
                        <div class="time-stat-content">
                            <div class="time-stat-number">{{ $totalAujourdhui }}</div>
                            <div class="time-stat-details">
                                <p><i class="fas fa-baby" style="color: #1977cc;"></i> Naissances: {{ $naissanceAujourdhui }}</p>
                                <p><i class="fas fa-cross" style="color: #000;"></i> Décès: {{ $decesAujourdhui }}</p>
                                <p><i class="fas fa-heart" style="color: #ff8800;"></i> Mariages: {{ $mariageAujourdhui }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="time-stat-card">
                        <h3><i class="fas fa-calendar-week"></i> Cette semaine</h3>
                        <div class="time-stat-content">
                            <div class="time-stat-number">{{ $totalSemaine }}</div>
                            <div class="time-stat-details">
                                <p><i class="fas fa-baby" style="color: #1977cc;"></i> Naissances: {{ $naissanceSemaine }}</p>
                                <p><i class="fas fa-cross" style="color: #000;"></i> Décès: {{ $decesSemaine }}</p>
                                <p><i class="fas fa-heart" style="color: #ff8800;"></i> Mariages: {{ $mariageSemaine }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="time-stat-card">
                        <h3><i class="fas fa-calendar"></i> Ce mois</h3>
                        <div class="time-stat-content">
                            <div class="time-stat-number">{{ $totalMois }}</div>
                            <div class="time-stat-details">
                                <p><i class="fas fa-baby" style="color: #1977cc;"></i> Naissances: {{ $naissanceMois }}</p>
                                <p><i class="fas fa-cross" style="color: #000;"></i> Décès: {{ $decesMois }}</p>
                                <p><i class="fas fa-heart" style="color: #ff8800;"></i> Mariages: {{ $mariageMois }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <h2 class="section-title"><i class="fas fa-chart-line"></i> Statistiques Financières</h2>
                <div class="finance-stats-grid">
                    <div class="finance-stat-card">
                        <h3><i class="fas fa-stamp"></i> Timbres utilisés</h3>
                        <div class="finance-stat-content">
                            <div class="finance-stat-icon">
                                <i class="fas fa-stamp"></i>
                            </div>
                            <div class="finance-stat-details">
                                <div class="stat-row">
                                    <span>Aujourd'hui:</span>
                                    <span>{{ $timbresAujourdhui }}</span>
                                </div>
                                <div class="stat-row">
                                    <span>Cette semaine:</span>
                                    <span>{{ $timbresSemaine }}</span>
                                </div>
                                <div class="stat-row">
                                    <span>Ce mois:</span>
                                    <span>{{ $timbresMois }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="finance-stat-card">
                        <h3><i class="fas fa-money-bill-wave"></i> Revenus générés</h3>
                        <div class="finance-stat-content">
                            <div class="finance-stat-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="finance-stat-details">
                                <div class="stat-row">
                                    <span>Aujourd'hui:</span>
                                    <span>{{ number_format($montantAujourdhui, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="stat-row">
                                    <span>Cette semaine:</span>
                                    <span>{{ number_format($montantSemaine, 0, ',', ' ') }} FCFA</span>
                                </div>
                                <div class="stat-row">
                                    <span>Ce mois:</span>
                                    <span>{{ number_format($montantMois, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="finance-stat-card solde-card">
                        <h3><i class="fas fa-wallet"></i> Solde des timbres</h3>
                        <div class="solde-content">
                            <div class="solde-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="solde-amount">
                                {{ number_format($soldeTimbres, 0, ',', ' ') }}
                            </div>
                            <p>Timbres disponibles</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="charts-section">
                <h2 class="section-title"><i class="fas fa-chart-area"></i> Évolution des Demandes</h2>
                <div class="charts-container">
                    <div class="chart-header">
                        <h3>Activités récentes</h3>
                        <div class="chart-tabs">
                            <button class="chart-tab active" data-chart="weekly">7 derniers jours</button>
                            <button class="chart-tab" data-chart="monthly">30 derniers jours</button>
                            <button class="chart-tab" data-chart="yearly">12 derniers mois</button>
                        </div>
                    </div>
                    
                    <div class="chart-content">
                        <canvas id="statsChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Demandes récentes -->
            <div class="recent-demands-section">
                <h2 class="section-title"><i class="fas fa-clock"></i> Demandes Récentes</h2>
                <div class="recent-demands">
                    <div class="demands-tabs">
                        <button class="demand-tab active" data-demand="naissance">Naissances</button>
                        <button class="demand-tab" data-demand="deces">Décès</button>
                        <button class="demand-tab" data-demand="mariage">Mariages</button>
                    </div>
                    
                    <div class="demands-content">
                        <div class="demand-list" id="naissance-list">
                            @foreach($demandesNaissance as $demande)
                            <div class="demand-item">
                                <div class="demand-info">
                                    <h4>Naissance #{{ $demande->reference }}</h4>
                                    <p>{{ $demande->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="demand-status">
                                    <span class="status-badge">Nouveau</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="demand-list hidden" id="deces-list">
                            @foreach($demandesDeces as $demande)
                            <div class="demand-item">
                                <div class="demand-info">
                                    <h4>Décès #{{ $demande->reference }}</h4>
                                    <p>{{ $demande->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="demand-status">
                                    <span class="status-badge">Nouveau</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="demand-list hidden" id="mariage-list">
                            @foreach($demandesMariage as $demande)
                            <div class="demand-item">
                                <div class="demand-info">
                                    <h4>Mariage #{{ $demande->reference }}</h4>
                                    <p>{{ $demande->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="demand-status">
                                    <span class="status-badge">Nouveau</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Données pour les graphiques
            const weeklyData = {
                labels: ['J-6', 'J-5', 'J-4', 'J-3', 'J-2', 'Hier', 'Aujourd\'hui'],
                datasets: [
                    {
                        label: 'Naissances',
                        data: @json($weeklyData['naissances']),
                        borderColor: '#1977cc',
                        backgroundColor: 'rgba(0, 126, 0, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Décès',
                        data: @json($weeklyData['deces']),
                        borderColor: '#333',
                        backgroundColor: 'rgba(0, 0, 0, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Mariages',
                        data: @json($weeklyData['mariages']),
                        borderColor: '#ff8800',
                        backgroundColor: 'rgba(255, 136, 0, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            };
            
            const monthlyData = {
                labels: Array.from({length: 30}, (_, i) => 'J-' + (29 - i)),
                datasets: [
                    {
                        label: 'Naissances',
                        data: @json($monthlyData['naissances']),
                        borderColor: '#1977cc',
                        backgroundColor: 'rgba(0, 126, 0, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Décès',
                        data: @json($monthlyData['deces']),
                        borderColor: '#333',
                        backgroundColor: 'rgba(0, 0, 0, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Mariages',
                        data: @json($monthlyData['mariages']),
                        borderColor: '#ff8800',
                        backgroundColor: 'rgba(255, 136, 0, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            };
            
            const yearlyData = {
                labels: ['M-11', 'M-10', 'M-9', 'M-8', 'M-7', 'M-6', 'M-5', 'M-4', 'M-3', 'M-2', 'M-1', 'Ce mois'],
                datasets: [
                    {
                        label: 'Naissances',
                        data: @json($yearlyData['naissances']),
                        borderColor: '#1977cc',
                        backgroundColor: 'rgba(0, 126, 0, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Décès',
                        data: @json($yearlyData['deces']),
                        borderColor: '#333',
                        backgroundColor: 'rgba(0, 0, 0, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Mariages',
                        data: @json($yearlyData['mariages']),
                        borderColor: '#ff8800',
                        backgroundColor: 'rgba(255, 136, 0, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            };
            
            // Configuration du graphique
            const ctx = document.getElementById('statsChart').getContext('2d');
            let chart = new Chart(ctx, {
                type: 'line',
                data: weeklyData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
            
            // Gestion des onglets de graphique
            const chartTabs = document.querySelectorAll('.chart-tab');
            chartTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Mettre à jour l'onglet actif
                    chartTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Mettre à jour les données du graphique
                    const period = this.getAttribute('data-chart');
                    let newData;
                    
                    if (period === 'weekly') {
                        newData = weeklyData;
                    } else if (period === 'monthly') {
                        newData = monthlyData;
                    } else {
                        newData = yearlyData;
                    }
                    
                    chart.data = newData;
                    chart.update();
                });
            });
            
            // Gestion des onglets de demandes
            const demandTabs = document.querySelectorAll('.demand-tab');
            const demandLists = document.querySelectorAll('.demand-list');
            
            demandTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Mettre à jour l'onglet actif
                    demandTabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Afficher la liste correspondante
                    const demandType = this.getAttribute('data-demand');
                    demandLists.forEach(list => {
                        list.classList.add('hidden');
                    });
                    document.getElementById(demandType + '-list').classList.remove('hidden');
                });
            });
        });
    </script>
</body>
</html>
@endsection