@extends('user.layouts.template')
@section('content')
<div class="modern-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
        <div class="welcome-section">
            <h1>Tableau de bord</h1>
            <p class="welcome-message">Bienvenue, {{ Auth::user()->name }} {{ Auth::user()->prenom }}!</p>
            <p class="current-date">{{ now()->format('l, d F Y') }}</p>
        </div>
        <div class="quick-actions">
           <a href="{{route('user.history')}}">
             <button class="action-btn outline ">
                <i class="fas fa-history" style="color: #1977cc"></i>
                Voir l'historique
            </button>
           </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon birth">
                <i class="fas fa-baby"></i>
            </div>
            <div class="stat-content">
                <h3>Naissances</h3>
                <span class="stat-number">{{ $naissancesCount }}</span>
                <div class="stat-trend">
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon marriage">
                <i class="fas fa-ring"></i>
            </div>
            <div class="stat-content">
                <h3>Mariages</h3>
                <span class="stat-number">{{ $mariageCount }}</span>
                <div class="stat-trend">
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon death">
                <i class="fas fa-cross"></i>
            </div>
            <div class="stat-content">
                <h3>Décès</h3>
                <span class="stat-number">{{ $decesCount}}</span>
                <div class="stat-trend">
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <h3>Total des demandes</h3>
                <span class="stat-number">{{ $nombreDemandes }}</span>
            </div>
        </div>
    </div>
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon marriage">
               <i class="fas fa-sync fa-spin"></i> 
            </div>
            <div class="stat-content">
                <h3>Demandes de traitement</h3>
                <span class="stat-number">{{ $TotalEtatCount }}</span>
                <div class="stat-trend">
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon total">
                 <i class="fas fa-motorcycle fa-beat" style="color:#ff9800;"></i>
                 <i class="fas fa-spinner fa-spin" style="color:#ff9800;"></i>
            </div>
            <div class="stat-content">
                <h3>Demandes en cours de livraison</h3>
                <span class="stat-number">{{ $TotalLivreCount }}</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-container">
        <!-- Evolution Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h2>Évolution des demandes</h2>
                <div class="chart-filter">
                    <select id="chartRange">
                        <option value="monthly">Mensuel</option>
                        <option value="quarterly">Trimestriel</option>
                        <option value="yearly">Annuel</option>
                    </select>
                </div>
            </div>
            <div class="chart-content">
                <canvas id="evolutionChart" height="250"></canvas>
            </div>
        </div>

        <!-- Distribution Chart -->
        <div class="chart-card">
            <div class="chart-header">
                <h2>Répartition par type</h2>
            </div>
            <div class="chart-content">
                <canvas id="distributionChart" height="250"></canvas>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Evolution Chart
        const labels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        
        const naissancesData = @json($totalNaissancesMonthly);
        const decesData = @json($totalDecesMonthly);
        const mariagesData = @json($mariageMonthly);
        
        const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
        const evolutionChart = new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Naissances',
                        data: naissancesData,
                        borderColor: 'rgba(74, 144, 226, 1)',
                        backgroundColor: 'rgba(74, 144, 226, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgba(74, 144, 226, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Décès',
                        data: decesData,
                        borderColor: 'rgba(255,8,84, 1)',
                        backgroundColor: 'rgba(255,8,84, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgba(255,8,84, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Mariages',
                        data: mariagesData,
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Distribution Chart (Doughnut)
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        const distributionChart = new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Naissances', 'Décès', 'Mariages'],
                datasets: [{
                    data: [{{ $totalNaissances }}, {{ $totalDeces }}, {{ $mariageCount }}],
                    backgroundColor: [
                        'rgba(74, 144, 226, 0.8)',
                        'rgba(255,8,84, 0.8)',
                        'rgba(40, 167, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(74, 144, 226, 1)',
                        'rgba(255,8,84, 1)',
                        'rgba(40, 167, 69, 1)'
                    ],
                    borderWidth: 1,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    });
</script>

<style>
    :root {
        --primary: #1977cc;
        --primary-light: #e8f2ff;
        --secondary: #6c757d;
        --success: #28a745;
        --danger: #dc3545;
        --warning: #ffc107;
        --info: #17a2b8;
        --light: #f8f9fa;
        --dark: #343a40;
        --white: #ffffff;
        --border-radius: 12px;
        --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    .modern-dashboard {
        padding: 30px;
        background-color: #f5f7fb;
        min-height: 100vh;
    }

    /* Header Styles */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .welcome-section h1 {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 8px;
    }

    .welcome-message {
        font-size: 16px;
        color: var(--secondary);
        margin-bottom: 5px;
    }

    .current-date {
        font-size: 14px;
        color: var(--secondary);
        font-weight: 500;
    }

    .quick-actions {
        display: flex;
        gap: 15px;
    }

    .action-btn {
        padding: 12px 20px;
        border-radius: var(--border-radius);
        border: none;
        background-color: var(--primary);
        color: white;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
    }

    .action-btn:hover {
        background-color: #1565b8;
        transform: translateY(-2px);
    }

    .action-btn.outline {
        background-color: transparent;
        border: 2px solid var(--primary);
        color: var(--primary);
    }

    .action-btn.outline:hover {
        background-color: var(--primary-light);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 24px;
        box-shadow: var(--box-shadow);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: var(--transition);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .stat-icon.birth {
        background-color: rgba(74, 144, 226, 0.15);
        color: #4a90e2;
    }

    .stat-icon.marriage {
        background-color: rgba(40, 167, 69, 0.15);
        color: #28a745;
    }

    .stat-icon.death {
        background-color: #ffe9ef;
        color: #ff0854;
    }

    .stat-icon.total {
        background-color: rgba(255, 193, 7, 0.15);
        color: #ffc107;
    }

    .stat-content h3 {
        font-size: 16px;
        color: var(--secondary);
        margin-bottom: 8px;
        font-weight: 500;
    }

    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: var(--dark);
        display: block;
        margin-bottom: 8px;
    }

    .stat-trend, .stat-progress {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
    }

    .stat-trend {
        color: var(--success);
    }

    .stat-trend .fa-arrow-down {
        color: var(--danger);
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        margin: 5px 0;
    }

    .progress-fill {
        height: 100%;
        background-color: var(--primary);
        border-radius: 10px;
        transition: width 1s ease;
    }

    /* Charts Section */
    .charts-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 24px;
        box-shadow: var(--box-shadow);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .chart-header h2 {
        font-size: 20px;
        font-weight: 600;
        color: var(--dark);
    }

    .chart-filter select {
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        background-color: var(--white);
    }

    .chart-content {
        height: 350px;
        position: relative;
    }

    /* Activity Section */
    .activity-section {
        background: var(--white);
        border-radius: var(--border-radius);
        padding: 24px;
        box-shadow: var(--box-shadow);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-header h2 {
        font-size: 20px;
        font-weight: 600;
        color: var(--dark);
    }

    .view-all {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }

    .view-all:hover {
        text-decoration: underline;
    }

    .activity-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .activity-item {
        display: flex;
        align-items: center;
        padding: 16px;
        border-radius: var(--border-radius);
        background-color: var(--light);
        transition: var(--transition);
    }

    .activity-item:hover {
        background-color: #e9ecef;
    }

    .activity-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(25, 119, 204, 0.1);
        color: var(--primary);
        font-size: 20px;
        margin-right: 16px;
    }

    .activity-content {
        flex: 1;
    }

    .activity-content h4 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 4px;
        color: var(--dark);
    }

    .activity-content p {
        font-size: 14px;
        color: var(--secondary);
        margin-bottom: 4px;
    }

    .activity-time {
        font-size: 12px;
        color: var(--secondary);
    }

    .activity-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .activity-status.approved {
        background-color: rgba(40, 167, 69, 0.15);
        color: var(--success);
    }

    .activity-status.completed {
        background-color: rgba(23, 162, 184, 0.15);
        color: var(--info);
    }

    .activity-status.pending {
        background-color: rgba(255, 193, 7, 0.15);
        color: var(--warning);
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .charts-container {
            grid-template-columns: 1fr;
        }
        
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 768px) {
        .modern-dashboard {
            padding: 20px;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .stat-card {
            flex-direction: column;
            text-align: center;
        }
        
        .activity-item {
            flex-direction: column;
            text-align: center;
        }
        
        .activity-icon {
            margin-right: 0;
            margin-bottom: 12px;
        }
        
        .activity-status {
            margin-top: 12px;
        }
    }
</style>
@endsection