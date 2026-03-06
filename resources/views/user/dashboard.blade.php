@extends('user.layouts.template')
@section('content')
    <div class="modern-dashboard">
            <!-- Header Section -->
            <div class="dashboard-header">
                <div class="welcome-section">
                    <h1>Tableau de bord</h1>
                    <p class="welcome-message">Bienvenue, {{ Auth::user()->name }} {{ Auth::user()->prenom }}!</p>
                    <p>{{ now()->locale('fr')->format('l, d F Y') }}</p>
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

            {{-- Bannière Demandes Gratuites (Mode Test) --}}
            @if(isset($showFreeRequestsMessage) && $showFreeRequestsMessage)
            <div id="free-requests-banner" class="free-requests-banner" style="
                background: linear-gradient(135deg, #ffffffff 10%, #d0f0ffff 10%);
                border-radius: 16px;
                padding: 1.5rem 2rem;
                margin-bottom: 1.5rem;
                color: white;
                position: relative;
                overflow: hidden;
                animation: slideDown 0.5s ease-out;
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            ">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -30px; left: 50%; width: 80px; height: 80px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                
                <button onclick="dismissFreeRequestsBanner()" style="
                    position: absolute; top: 12px; right: 16px;
                    background: rgba(255,255,255,0.2); border: none;
                    color: white; width: 30px; height: 30px;
                    border-radius: 50%; cursor: pointer;
                    font-size: 1rem; display: flex; align-items: center; justify-content: center;
                    transition: background 0.2s;
                " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    <i class="fas fa-times"></i>
                </button>

                <div style="display: flex; align-items: center; gap: 1.2rem; position: relative; z-index: 1;">
                    <div style="
                        width: 60px; height: 60px;
                        background: rgba(255,255,255,0.2);
                        border-radius: 16px;
                        display: flex; align-items: center; justify-content: center;
                        font-size: 1.8rem;
                        animation: pulseGift 2s ease-in-out infinite;
                    ">
                        🎁
                    </div>
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 0.3rem; font-size: 1.2rem; font-weight: 800;">
                            Bonne nouvelle ! Vos {{ $freeRequestsRemaining }} première{{ $freeRequestsRemaining > 1 ? 's' : '' }} demande{{ $freeRequestsRemaining > 1 ? 's' : '' }} {{ $freeRequestsRemaining > 1 ? 'sont gratuites' : 'est gratuite' }} ! 🎉
                        </h3>
                        <p style="margin: 0; opacity: 0.9; font-size: 0.95rem; line-height: 1.5;">
                            Pour célébrer votre inscription, vous bénéficiez de <strong>{{ $freeRequestsRemaining }} demande{{ $freeRequestsRemaining > 1 ? 's' : '' }} gratuite{{ $freeRequestsRemaining > 1 ? 's' : '' }}</strong> 
                            (timbres offerts, soit {{ $freeRequestsRemaining * 500 }} FCFA d'économie). Profitez-en dès maintenant !
                        </p>
                    </div>
                </div>
            </div>

            <style>
                @keyframes slideDown {
                    from { opacity: 0; transform: translateY(-20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                @keyframes pulseGift {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.1); }
                }
                @keyframes slideUp {
                    from { opacity: 1; transform: translateY(0); }
                    to { opacity: 0; transform: translateY(-20px); height: 0; padding: 0; margin: 0; }
                }
            </style>

            <script>
                function dismissFreeRequestsBanner() {
                    document.getElementById('free-requests-banner').style.animation = 'slideUp 0.3s ease-in forwards';
                    setTimeout(function() {
                        document.getElementById('free-requests-banner').style.display = 'none';
                    }, 300);
                    
                    fetch('{{ route("user.dismiss.free.requests") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                }
            </script>
            @endif

            {{-- Indication demandes gratuites restantes --}}
            @if(isset($freeRequestsModeActive) && $freeRequestsModeActive && isset($freeRequestsRemaining) && $freeRequestsRemaining > 0)
            <div style="
                background: linear-gradient(90deg, #f0fdf4, #dcfce7);
                border: 1px solid #86efac;
                border-radius: 12px;
                padding: 0.8rem 1.2rem;
                margin-bottom: 1.5rem;
                display: flex;
                align-items: center;
                gap: 0.8rem;
            ">
                <span style="font-size: 1.3rem;">🎁</span>
                <span style="color: #166534; font-weight: 600; font-size: 0.9rem;">
                    Il vous reste <strong>{{ $freeRequestsRemaining }} demande{{ $freeRequestsRemaining > 1 ? 's' : '' }} gratuite{{ $freeRequestsRemaining > 1 ? 's' : '' }}</strong> 
                    ({{ $freeRequestsRemaining * 500 }} FCFA de timbres offerts)
                </span>
            </div>
            @endif

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon birth">
                        <i class="fas fa-baby"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Demandes d'actes de Naissances</h3>
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
                        <h3>Demandes d'actes de Mariages</h3>
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
                        <h3>Demandes d'actes de Décès</h3>
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
                        <h3>Total des demandes d'actes</h3>
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
            <script>
                document.addEventListener('DOMContentLoaded', function () {
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
                                        label: function (context) {
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
                    padding: 30px 30px 0 30px;
                    background-color: #f5f7fb;
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

                .stat-trend,
                .stat-progress {
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