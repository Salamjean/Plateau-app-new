@extends('mairie.layouts.template')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="modern-dashboard">
        <!-- Filtres Section -->
        <div class="filters-card">
            <div class="filters-header">
                <i class="fas fa-filter"></i>
                <h5>Filtres de recherche</h5>
            </div>
            <form method="GET" action="" class="filters-form">
                <div class="filter-group">
                    <label for="month" class="filter-label">
                        <i class="fas fa-calendar"></i> Mois
                    </label>
                    <select class="modern-select" id="month" name="month">
                        <option value="">Tous les mois</option>
                        @php
                            $months = [
                                1 => 'Janvier',
                                2 => 'Février',
                                3 => 'Mars',
                                4 => 'Avril',
                                5 => 'Mai',
                                6 => 'Juin',
                                7 => 'Juillet',
                                8 => 'Août',
                                9 => 'Septembre',
                                10 => 'Octobre',
                                11 => 'Novembre',
                                12 => 'Décembre',
                            ];
                        @endphp
                        @foreach ($months as $key => $month)
                            <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }}>
                                {{ $month }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="year" class="filter-label">
                        <i class="fas fa-calendar-week"></i> Année
                    </label>
                    <select class="modern-select" id="year" name="year">
                        <option value="">Toutes les années</option>
                        @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="filter-group">
                    <button type="submit" class="apply-filter-btn">
                        <i class="fas fa-sync-alt"></i>
                        <span>Appliquer</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <!-- Naissances Card -->
            <div class="stat-card stat-card-birth">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <div class="stat-label">Actes de Naissances</div>
                        <div class="stat-value">{{ $naissancedash }}</div>
                        <div class="stat-meta">
                            <span class="stat-badge">
                                <i class="fas fa-arrow-up"></i> {{ number_format($NaissP, 1) }}%
                            </span>
                            <span class="stat-text">du total des demandes</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <div class="icon-wrapper icon-birth">
                            <i class="fas fa-baby"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-footer">
                    <a href="{{ route('mairie.request.birth') }}" class="stat-link">
                        Voir les demandes <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Décès Card -->
            <div class="stat-card stat-card-death">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <div class="stat-label">Actes de Décès</div>
                        <div class="stat-value">{{ $decesdash }}</div>
                        <div class="stat-meta">
                            <span class="stat-badge">
                                <i class="fas fa-arrow-up"></i> {{ number_format($DecesP, 1) }}%
                            </span>
                            <span class="stat-text">du total des demandes</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <div class="icon-wrapper icon-death">
                            <i class="fas fa-cross"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-footer">
                    <a href="{{ route('mairie.request.death') }}" class="stat-link">
                        Voir les demandes <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Mariages Card -->
            <div class="stat-card stat-card-marriage">
                <div class="stat-card-content">
                    <div class="stat-info">
                        <div class="stat-label">Actes de Mariages</div>
                        <div class="stat-value">{{ $mariagedash }}</div>
                        <div class="stat-meta">
                            <span class="stat-badge">
                                <i class="fas fa-arrow-up"></i> {{ number_format($mariagePercentage, 1) }}%
                            </span>
                            <span class="stat-text">du total des demandes</span>
                        </div>
                    </div>
                    <div class="stat-icon">
                        <div class="icon-wrapper icon-marriage">
                            <i class="fas fa-heart"></i>
                        </div>
                    </div>
                </div>
                <div class="stat-footer">
                    <a href="{{ route('mairie.request.wedding') }}" class="stat-link">
                        Voir les demandes <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Charts & Recent Data -->
        <div class="dashboard-grid">
            <!-- Distribution Chart -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-pie"></i> Répartition des demandes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Data -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-clock"></i> Demandes récentes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="modern-tabs">
                        <div class="tabs-nav">
                            <button class="tab-btn active" data-tab="naissances">
                                <i class="fas fa-baby"></i> Naissances
                            </button>
                            <button class="tab-btn" data-tab="deces">
                                <i class="fas fa-cross"></i> Décès
                            </button>
                            <button class="tab-btn" data-tab="mariages">
                                <i class="fas fa-heart"></i> Mariages
                            </button>
                        </div>

                        <div class="tabs-content">
                            <div class="tab-pane active" id="naissances">
                                @forelse($recentNaissances as $naissance)
                                    <div class="recent-item">
                                        <div class="recent-icon">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="recent-info">
                                            <h6>{{ $naissance->name }} {{ $naissance->prenom }}</h6>
                                            <p>
                                                <i class="fas fa-calendar"></i>
                                                {{ $naissance->created_at->format('d/m/Y') }}
                                                <i class="fas fa-clock"></i> {{ $naissance->created_at->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>Aucune demande récente</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="tab-pane" id="deces">
                                @forelse($recentDeces as $deces)
                                    <div class="recent-item">
                                        <div class="recent-icon">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="recent-info">
                                            <h6>{{ $deces->name }}</h6>
                                            <p>
                                                <i class="fas fa-calendar"></i> {{ $deces->created_at->format('d/m/Y') }}
                                                <i class="fas fa-clock"></i> {{ $deces->created_at->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>Aucune demande récente</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="tab-pane" id="mariages">
                                @forelse($recentMariages as $mariage)
                                    <div class="recent-item">
                                        <div class="recent-icon">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <div class="recent-info">
                                            <h6>
                                                {{ $mariage->nomEpoux == null ? 'Copie simple' : 'Copie intégrale' }}
                                            </h6>
                                            <p>
                                                Par {{ $mariage->user->name }} {{ $mariage->user->prenom }} •
                                                <i class="fas fa-calendar"></i>
                                                {{ $mariage->created_at->format('d/m/Y') }}
                                                <i class="fas fa-clock"></i> {{ $mariage->created_at->format('H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p>Aucune demande récente</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modern Dashboard Styles */
        .modern-dashboard {
            padding: 24px;
            background: #f8fafc;
            min-height: 100vh;
        }

        /* Filters Card */
        .filters-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .filters-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .filters-header i {
            color: #1f4083;
            font-size: 20px;
        }

        .filters-header h5 {
            margin: 0;
            color: #1f4083;
            font-weight: 600;
            font-size: 18px;
        }

        .filters-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-label i {
            color: #1f4083;
            font-size: 14px;
        }

        .modern-select {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #2d3748;
            background: white;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .modern-select:focus {
            outline: none;
            border-color: #1f4083;
            box-shadow: 0 0 0 3px rgba(31, 64, 131, 0.1);
        }

        .apply-filter-btn {
            background: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .apply-filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(31, 64, 131, 0.3);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .stat-card-birth {
            border-top: 4px solid #1f4083;
        }

        .stat-card-death {
            border-top: 4px solid #718096;
        }

        .stat-card-marriage {
            border-top: 4px solid #e53e3e;
        }

        .stat-card-content {
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-info {
            flex: 1;
        }

        .stat-label {
            font-size: 14px;
            color: #718096;
            font-weight: 600;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 40px;
            font-weight: 700;
            color: #1f4083;
            margin-bottom: 12px;
            line-height: 1;
        }

        .stat-card-death .stat-value {
            color: #718096;
        }

        .stat-card-marriage .stat-value {
            color: #e53e3e;
        }

        .stat-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .stat-badge {
            background: rgba(31, 64, 131, 0.1);
            color: #1f4083;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stat-card-death .stat-badge {
            background: rgba(113, 128, 150, 0.1);
            color: #718096;
        }

        .stat-card-marriage .stat-badge {
            background: rgba(229, 62, 62, 0.1);
            color: #e53e3e;
        }

        .stat-text {
            font-size: 13px;
            color: #a0aec0;
        }

        .stat-icon {
            margin-left: 16px;
        }

        .icon-wrapper {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .icon-birth {
            background: linear-gradient(135deg, rgba(31, 64, 131, 0.15), rgba(31, 64, 131, 0.05));
            color: #1f4083;
        }

        .icon-death {
            background: linear-gradient(135deg, rgba(113, 128, 150, 0.15), rgba(113, 128, 150, 0.05));
            color: #718096;
        }

        .icon-marriage {
            background: linear-gradient(135deg, rgba(229, 62, 62, 0.15), rgba(229, 62, 62, 0.05));
            color: #e53e3e;
        }

        .stat-footer {
            padding: 16px 24px;
            background: #f7fafc;
            border-top: 1px solid #e2e8f0;
        }

        .stat-link {
            color: #1f4083;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .stat-link:hover {
            gap: 12px;
            color: #2d5aa8;
        }

        .stat-link i {
            font-size: 12px;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 24px;
        }

        .dashboard-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(to right, #f7fafc, white);
        }

        .card-title {
            margin: 0;
            color: #1f4083;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            font-size: 20px;
        }

        .card-body {
            padding: 24px;
        }

        /* Chart Wrapper */
        .chart-wrapper {
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Modern Tabs */
        .modern-tabs {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .tabs-nav {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0;
        }

        .tab-btn {
            padding: 12px 20px;
            background: transparent;
            border: none;
            color: #718096;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-btn:hover {
            color: #1f4083;
            background: rgba(31, 64, 131, 0.05);
        }

        .tab-btn.active {
            color: #1f4083;
            border-bottom-color: #1f4083;
        }

        .tabs-content {
            min-height: 300px;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Recent Items */
        .recent-item {
            display: flex;
            gap: 16px;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .recent-item:hover {
            background: #f7fafc;
            border-color: #1f4083;
            transform: translateX(4px);
        }

        .recent-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #1f4083, #2d5aa8);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .recent-info h6 {
            margin: 0 0 8px 0;
            color: #2d3748;
            font-size: 15px;
            font-weight: 600;
        }

        .recent-info p {
            margin: 0;
            color: #718096;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .recent-info p i {
            color: #1f4083;
            font-size: 12px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: #a0aec0;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state p {
            margin: 0;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modern-dashboard {
                padding: 16px;
            }

            .dashboard-header {
                padding: 24px;
            }

            .dashboard-title {
                font-size: 24px;
            }

            .stats-grid,
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .filters-form {
                grid-template-columns: 1fr;
            }

            .tabs-nav {
                flex-wrap: wrap;
            }

            .tab-btn {
                flex: 1;
                min-width: 100px;
                justify-content: center;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab Switching
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');

                    // Remove active class from all tabs and panes
                    tabBtns.forEach(b => b.classList.remove('active'));
                    tabPanes.forEach(p => p.classList.remove('active'));

                    // Add active class to clicked tab and corresponding pane
                    this.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });

            // Distribution Chart
            const ctx = document.getElementById('distributionChart').getContext('2d');
            const distributionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Actes de Naissances', 'Actes de Décès', 'Actes de Mariages'],
                    datasets: [{
                        data: [{{ $naissancedash }}, {{ $decesdash }}, {{ $mariagedash }}],
                        backgroundColor: [
                            '#1f4083',
                            '#718096',
                            '#e53e3e'
                        ],
                        borderWidth: 0,
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
                                font: {
                                    size: 13,
                                    family: "'Inter', sans-serif",
                                    weight: '600'
                                },
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1
                        }
                    },
                    cutout: '60%'
                }
            });
        });
    </script>
@endsection
