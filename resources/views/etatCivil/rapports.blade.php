@extends('etatCivil.layouts.template')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        .reports-container {
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

        /* Filter Section */
        .controls-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px 25px;
            box-shadow: var(--card-shadow);
            margin-bottom: 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .period-selector {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .period-label {
            font-weight: 700;
            color: #475569;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-group-pill {
            display: flex;
            background: #f1f5f9;
            padding: 5px;
            border-radius: 50px;
            gap: 5px;
        }

        .btn-pill {
            padding: 8px 20px;
            border-radius: 50px;
            border: none;
            background: transparent;
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-pill.active {
            background: white;
            color: var(--primary-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .btn-pill:hover:not(.active) {
            color: var(--primary-color);
        }

        .export-actions {
            display: flex;
            gap: 12px;
        }

        .btn-export {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(31, 64, 131, 0.2);
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(31, 64, 131, 0.3);
            background: #162e5f;
            color: white;
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

        .stat-card.completion::before {
            background: var(--primary-color);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
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

        .stat-card.completion .stat-icon {
            background: #f1f5f9;
            color: var(--primary-color);
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
        }

        .stat-label {
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
            margin: 5px 0 15px 0;
        }

        .stat-meta {
            display: flex;
            gap: 10px;
            border-top: 1px solid #f1f5f9;
            padding-top: 15px;
        }

        .stat-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .badge-done {
            background: #dcfce7;
            color: #166534;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        /* Charts Layout */
        .charts-row {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 35px;
        }

        .chart-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--card-shadow);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .chart-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e293b;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Agents Section */
        .agents-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--card-shadow);
        }

        .agents-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .agent-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }

        .agent-item:hover {
            border-color: var(--primary-color);
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .agent-rank {
            width: 35px;
            height: 35px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--primary-color);
            flex-shrink: 0;
        }

        .agent-avatar {
            width: 45px;
            height: 45px;
            background: var(--primary-color);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .agent-info {
            flex-grow: 1;
        }

        .agent-name {
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            font-size: 0.95rem;
        }

        .agent-count {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0;
        }

        .agent-rating {
            background: #f0fdf4;
            color: #166534;
            padding: 4px 10px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.8rem;
        }

        @media (max-width: 1024px) {
            .charts-row {
                grid-template-columns: 1fr;
            }

            .controls-card {
                flex-direction: column;
                align-items: stretch;
            }

            .period-selector {
                justify-content: space-between;
            }
        }
    </style>

    <div class="reports-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-file-invoice text-primary"></i>
                Rapports d'Activité
            </h1>
        </div>

        <!-- Contrôles -->
        <div class="controls-card">
            <div class="period-selector">
                <span class="period-label">
                    <i class="far fa-calendar-alt me-2"></i>Période :
                </span>
                <div class="btn-group-pill">
                    <a href="?periode=3mois" class="btn-pill {{ $periode == '3mois' ? 'active' : '' }}">3 Mois</a>
                    <a href="?periode=6mois" class="btn-pill {{ $periode == '6mois' ? 'active' : '' }}">6 Mois</a>
                    <a href="?periode=12mois"
                        class="btn-pill {{ $periode == '12mois' || $periode == '' ? 'active' : '' }}">12 Mois</a>
                </div>
            </div>

            <div class="export-actions">
                <button class="btn-export" onclick="exportReport()">
                    <i class="fas fa-file-pdf"></i>
                    Exporter le PDF
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <!-- Naissances -->
            <div class="stat-card birth">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-baby"></i></div>
                </div>
                <h3 class="stat-value">{{ number_format($statsDetaillees['naissances']['total']) }}</h3>
                <p class="stat-label">Naissances Totales</p>
                <div class="stat-meta">
                    <span class="stat-badge badge-done">{{ $statsDetaillees['naissances']['termine'] }} Terminés</span>
                    <span class="stat-badge badge-pending">{{ $statsDetaillees['naissances']['en_cours'] }} En cours</span>
                </div>
            </div>

            <!-- Décès -->
            <div class="stat-card death">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-cross"></i></div>
                </div>
                <h3 class="stat-value">{{ number_format($statsDetaillees['deces']['total']) }}</h3>
                <p class="stat-label">Décès Totaux</p>
                <div class="stat-meta">
                    <span class="stat-badge badge-done">{{ $statsDetaillees['deces']['termine'] }} Terminés</span>
                    <span class="stat-badge badge-pending">{{ $statsDetaillees['deces']['en_cours'] }} En cours</span>
                </div>
            </div>

            <!-- Mariages -->
            <div class="stat-card marriage">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-ring"></i></div>
                </div>
                <h3 class="stat-value">{{ number_format($statsDetaillees['mariages']['total']) }}</h3>
                <p class="stat-label">Mariages Totaux</p>
                <div class="stat-meta">
                    <span class="stat-badge badge-done">{{ $statsDetaillees['mariages']['termine'] }} Terminés</span>
                    <span class="stat-badge badge-pending">{{ $statsDetaillees['mariages']['en_cours'] }} En cours</span>
                </div>
            </div>

            <!-- Performance -->
            <div class="stat-card completion">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fas fa-tachometer-alt"></i></div>
                </div>
                @php
                    $avgCompletion =
                        ($statsDetaillees['naissances']['taux_completion'] +
                            $statsDetaillees['deces']['taux_completion'] +
                            $statsDetaillees['mariages']['taux_completion']) /
                        3;
                @endphp
                <h3 class="stat-value">{{ number_format($avgCompletion, 1) }}%</h3>
                <p class="stat-label">Taux de Complétion</p>
                <div class="progress" style="height: 6px; background: #f1f5f9; border-radius: 10px; margin-top: 15px;">
                    <div class="progress-bar"
                        style="width: {{ $avgCompletion }}%; background: var(--primary-color); border-radius: 10px;"></div>
                </div>
            </div>
        </div>

        <!-- Charts Layout -->
        <div class="charts-row">
            <!-- Évolution -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-line text-primary"></i>
                        Évolution des demandes ({{ $months }}m)
                    </h3>
                </div>
                <div style="height: 350px;">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </div>

            <!-- Répartition -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-pie text-warning"></i>
                        Répartition par Acte
                    </h3>
                </div>
                <div style="height: 350px; position: relative;">
                    <canvas id="repartitionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Agents -->
        <div class="agents-card">
            <div class="chart-header">
                <h3 class="chart-title">
                    <i class="fas fa-award text-warning"></i>
                    Palmarès des Agents
                </h3>
            </div>
            <div class="agents-list">
                @foreach ($topAgents as $index => $agent)
                    <div class="agent-item">
                        <div class="agent-rank">{{ $index + 1 }}</div>
                        <div class="agent-avatar">
                            {{ substr($agent['name'], 0, 1) }}
                        </div>
                        <div class="agent-info">
                            <h5 class="agent-name">{{ $agent['name'] }}</h5>
                            <p class="agent-count">
                                <strong>{{ $agent['demandes_traitees'] }}</strong> demandes traitées
                            </p>
                        </div>
                        <div class="agent-rating">
                            <i class="fas fa-star me-1 small"></i>{{ $agent['taux_satisfaction'] }}%
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js n\'est pas chargé !');
                return;
            }

            // Chart global context configuration
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = '#64748b';

            // 1. Évolution Chart
            const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
            const evoGradient = evolutionCtx.createLinearGradient(0, 0, 0, 400);
            evoGradient.addColorStop(0, 'rgba(31, 64, 131, 0.1)');
            evoGradient.addColorStop(1, 'rgba(31, 64, 131, 0)');

            new Chart(evolutionCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($donneesGraphiques['labels']) !!},
                    datasets: {!! json_encode($donneesGraphiques['datasets']) !!}.map((ds, index) => {
                        const colors = ['#3b82f6', '#ef4444', '#10b981'];
                        const bgColors = ['rgba(59, 130, 246, 0.1)', 'rgba(239, 68, 68, 0.1)',
                            'rgba(16, 185, 129, 0.1)'
                        ];
                        return {
                            ...ds,
                            borderColor: colors[index] || ds.borderColor,
                            backgroundColor: bgColors[index] || ds.backgroundColor,
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true
                        };
                    })
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
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5]
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

            // 2. Répartition Chart
            const repartitionCtx = document.getElementById('repartitionChart').getContext('2d');
            new Chart(repartitionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Naissances', 'Décès', 'Mariages'],
                    datasets: [{
                        data: [
                            {{ $statsDetaillees['naissances']['total'] }},
                            {{ $statsDetaillees['deces']['total'] }},
                            {{ $statsDetaillees['mariages']['total'] }}
                        ],
                        backgroundColor: ['#3b82f6', '#ef4444', '#10b981'],
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 25
                            }
                        }
                    }
                }
            });
        });

        function exportReport() {
            const periode = new URLSearchParams(window.location.search).get('periode') || '12mois';

            Swal.fire({
                title: 'Générer le rapport',
                html: `
                    <div class="text-start mt-3">
                        <p class="mb-3 text-muted">Choisissez le mode d'exportation pour la période de <b>${periode.replace('mois', ' mois')}</b> :</p>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="exportType" id="dl" value="download" checked>
                            <label class="form-check-label" for="dl">Télécharger le PDF</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="exportType" id="pv" value="preview">
                            <label class="form-check-label" for="pv">Aperçu avant impression</label>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1f4083',
                confirmButtonText: '<i class="fas fa-check me-2"></i>Confirmer',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const exportType = document.querySelector('input[name="exportType"]:checked').value;
                    if (exportType === 'download') {
                        window.location.href = `{{ route('etatCivil.rapports.export') }}?periode=${periode}`;
                        Swal.fire({
                            title: 'Exportation',
                            text: 'Le fichier est en préparation...',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        window.open(`{{ route('etatCivil.rapports.preview') }}?periode=${periode}`, '_blank');
                    }
                }
            });
        }
    </script>
@endpush
