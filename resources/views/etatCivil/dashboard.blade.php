@extends('etatCivil.layouts.template')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #1f4083;
            --secondary-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --background-color: #f8fafc;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --border-radius: 16px;
        }

        .dashboard-container {
            padding: 30px;
            background-color: var(--background-color);
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .page-title {
            color: var(--primary-color);
            font-weight: 800;
            font-size: 1.75rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title i {
            background: white;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            font-size: 1.25rem;
        }

        .current-date {
            background: white;
            padding: 10px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            font-weight: 600;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .stat-card.birth::before {
            background: var(--secondary-color);
        }

        .stat-card.death::before {
            background: var(--danger-color);
        }

        .stat-card.marriage::before {
            background: var(--success-color);
        }

        .stat-card.agents::before {
            background: var(--primary-color);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-card.birth .stat-icon {
            background: #eff6ff;
            color: var(--secondary-color);
        }

        .stat-card.death .stat-icon {
            background: #fef2f2;
            color: var(--danger-color);
        }

        .stat-card.marriage .stat-icon {
            background: #f0fdf4;
            color: var(--success-color);
        }

        .stat-card.agents .stat-icon {
            background: #f1f5f9;
            color: var(--primary-color);
        }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            line-height: 1;
        }

        .stat-label {
            color: #64748b;
            font-weight: 600;
            font-size: 0.95rem;
            margin-top: 8px;
        }

        /* Main Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 35px;
        }

        .card-container {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 25px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-title {
            font-weight: 700;
            font-size: 1.15rem;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Activity List */
        .activity-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-top: 5px;
            flex-shrink: 0;
        }

        .activity-content {
            flex: 1;
        }

        .activity-text {
            font-size: 0.95rem;
            color: #334155;
            margin: 0;
            font-weight: 500;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* Quick Actions Card */
        .quick-actions-card {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .action-link {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 18px;
            background: #f8fafc;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .action-link:hover {
            background: white;
            border-color: var(--primary-color);
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .action-icon-small {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            font-size: 1.1rem;
        }

        .action-info h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .action-info p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }

        @media (max-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 15px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            .quick-actions-card {
                flex-direction: column;
            }
            .stat-value {
                font-size: 1.75rem;
            }
            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>

    <div class="dashboard-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-th-large text-primary"></i>
                Vue d'ensemble
            </h1>
            <div class="current-date">
                <i class="far fa-calendar-alt"></i>
                {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </div>
        </div>

        <!-- Cartes de statistiques -->
        <div class="stats-grid">
            <div class="stat-card birth">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-baby"></i>
                    </div>
                </div>
                <h3 class="stat-value">{{ number_format($totalNaissances) }}</h3>
                <p class="stat-label">Extraits de Naissance</p>
            </div>

            <div class="stat-card death">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-cross"></i>
                    </div>
                </div>
                <h3 class="stat-value">{{ number_format($totalDeces) }}</h3>
                <p class="stat-label">Extraits de Décès</p>
            </div>

            <div class="stat-card marriage">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-ring"></i>
                    </div>
                </div>
                <h3 class="stat-value">{{ number_format($totalMariages) }}</h3>
                <p class="stat-label">Extraits de Mariage</p>
            </div>

            <div class="stat-card agents">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
                <h3 class="stat-value">{{ number_format($totalAgents) }}</h3>
                <p class="stat-label">Agents Actifs</p>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Graphique d'Évolution -->
            <div class="card-container">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line text-primary"></i>
                        Évolution des Demandes
                    </h3>
                </div>
                <div style="height: 350px;">
                    <canvas id="demandesChart"></canvas>
                </div>
            </div>

            <!-- Activité et Actions -->
            <div class="d-flex flex-column gap-4">
                <!-- Actions Rapides -->
                <div class="card-container">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-bolt text-warning"></i>
                            Actions Rapides
                        </h3>
                    </div>
                    <div class="quick-actions-card">
                        <a href="{{ route('etatCivil.rapports') }}" class="action-link">
                            <div class="action-icon-small">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="action-info">
                                <h4>Rapports Mensuels</h4>
                                <p>Générer les synthèses</p>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                        </a>

                        <a href="{{ route('etatCivil.rapports.preview') }}?periode=12" target="_blank" class="action-link">
                            <div class="action-icon-small">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="action-info">
                                <h4>Statistiques Annuelles</h4>
                                <p>Analyse des 12 derniers mois</p>
                            </div>
                            <i class="fas fa-chevron-right ms-auto text-muted small"></i>
                        </a>
                    </div>
                </div>

                <!-- Activité Récente -->
                <div class="card-container flex-grow-1">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock text-info"></i>
                            Dernières Activités
                        </h3>
                    </div>
                    <div class="activity-list">
                        @forelse($activiteRecente as $index => $activite)
                            <div class="activity-item">
                                <div class="activity-dot"
                                    style="background-color: {{ ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'][$index % 4] }};">
                                </div>
                                <div class="activity-content">
                                    <p class="activity-text">{{ $activite['message'] }}</p>
                                    <span class="activity-time">{{ $activite['time'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted">Aucune activité</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('demandesChart').getContext('2d');

            // Création d'un gradient pour le graphique
            const gradientBirth = ctx.createLinearGradient(0, 0, 0, 400);
            gradientBirth.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
            gradientBirth.addColorStop(1, 'rgba(59, 130, 246, 0)');

            const gradientDeath = ctx.createLinearGradient(0, 0, 0, 400);
            gradientDeath.addColorStop(0, 'rgba(239, 68, 68, 0.1)');
            gradientDeath.addColorStop(1, 'rgba(239, 68, 68, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($demandesParMois['mois']) !!},
                    datasets: [{
                            label: 'Naissances',
                            data: {!! json_encode($demandesParMois['naissances']) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: gradientBirth,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#3b82f6'
                        },
                        {
                            label: 'Décès',
                            data: {!! json_encode($demandesParMois['deces']) !!},
                            borderColor: '#ef4444',
                            backgroundColor: gradientDeath,
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#ef4444'
                        },
                        {
                            label: 'Mariages',
                            data: {!! json_encode($demandesParMois['mariages']) !!},
                            borderColor: '#10b981',
                            borderWidth: 3,
                            fill: false,
                            tension: 0.4,
                            pointRadius: 4,
                            pointBackgroundColor: '#10b981'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5],
                                color: '#e2e8f0'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
