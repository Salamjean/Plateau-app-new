@extends('comptable.layouts.template')
@section('content')

<div class="comptable-dashboard">
    <!-- Header Section -->
    <div class="dashboard-header">
        <h1>Tableau de Bord de l'agent financier</h1>
        <p>Bienvenue, {{ Auth::guard('comptable')->user()->name }} {{ Auth::guard('comptable')->user()->prenom }}</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <!-- Today's Stats -->
        <div class="stat-card today">
            <div class="stat-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <h3>Aujourd'hui</h3>
                <div class="stat-number">{{ $totalAujourdhui }}</div>
                <div class="stat-details">
                    <span>Naissances: {{ $naissanceAujourdhui }}</span>
                    <span>Décès: {{ $decesAujourdhui }}</span>
                    <span>Mariages: {{ $mariageAujourdhui }}</span>
                </div>
                <div class="timbres-stats">
                    <span class="timbre-badge">{{ number_format($timbresAujourdhui, 0, ',', ' ') }} timbres</span>
                    <span class="montant-badge">{{ number_format($montantAujourdhui, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>

        <!-- Week's Stats -->
        <div class="stat-card week">
            <div class="stat-icon">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stat-content">
                <h3>Cette semaine</h3>
                <div class="stat-number">{{ $totalSemaine }}</div>
                <div class="stat-details">
                    <span>Naissances: {{ $naissanceSemaine }}</span>
                    <span>Décès: {{ $decesSemaine }}</span>
                    <span>Mariages: {{ $mariageSemaine }}</span>
                </div>
                <div class="timbres-stats">
                    <span class="timbre-badge">{{ number_format($timbresSemaine, 0, ',', ' ') }} timbres</span>
                    <span class="montant-badge">{{ number_format($montantSemaine, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>

        <!-- Month's Stats -->
        <div class="stat-card month">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-content">
                <h3>Ce mois</h3>
                <div class="stat-number">{{ $totalMois }}</div>
                <div class="stat-details">
                    <span>Naissances: {{ $naissanceMois }}</span>
                    <span>Décès: {{ $decesMois }}</span>
                    <span>Mariages: {{ $mariageMois }}</span>
                </div>
                <div class="timbres-stats">
                    <span class="timbre-badge">{{ number_format($timbresMois, 0, ',', ' ') }} timbres</span>
                    <span class="montant-badge">{{ number_format($montantMois, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>

        <!-- Total Stats -->
        <div class="stat-card total">
            <div class="stat-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="stat-content">
                <h3>Total général</h3>
                <div class="stat-number">{{ $total }}</div>
                <div class="stat-details">
                    <span>Naissances: {{ $naissancenombre }}</span>
                    <span>Décès: {{ $decesnombre }}</span>
                    <span>Mariages: {{ $mariagenombre }}</span>
                </div>
                <div class="timbres-stats">
                    <span class="solde-badge">Solde: {{ number_format($soldeTimbres, 0, ',', ' ') }} timbres</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Requests & Charts Section -->
    <div class="dashboard-content">
        <!-- Left Column: Recent Requests and Timbres -->
        <div class="left-column">
            <!-- Recent Requests -->
            <div class="recent-requests">
                <h2>Demandes récentes</h2>
                
                <div class="request-type">
                    <h3>Naissances</h3>
                    @foreach($demandesNaissance as $demande)
                    <div class="request-item">
                        <div class="request-info">
                            <h4>Naissance #{{ $demande->reference }}</h4>
                            <p>Date: {{ $demande->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="request-status">
                            <span class="status-badge">{{ $demande->statut }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="request-type">
                    <h3>Décès</h3>
                    @foreach($demandesDeces as $demande)
                    <div class="request-item">
                        <div class="request-info">
                            <h4>Décès #{{ $demande->reference }}</h4>
                            <p>Date: {{ $demande->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="request-status">
                            <span class="status-badge">{{ $demande->statut }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="request-type">
                    <h3>Mariages</h3>
                    @foreach($demandesMariage as $demande)
                    <div class="request-item">
                        <div class="request-info">
                            <h4>Mariage #{{ $demande->reference }}</h4>
                            <p>Date: {{ $demande->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="request-status">
                            <span class="status-badge">{{ $demande->statut }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Timbre Sales -->
            <div class="recent-timbres">
                <h2>Ventes récentes de timbres</h2>
                @foreach($dernieresVentesTimbres as $vente)
                <div class="timbre-item">
                    <div class="timbre-info">
                        <h4>Vente de {{ abs($vente->nombre_timbre) }} timbres</h4>
                        <p>Le {{ $vente->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div class="timbre-montant">
                        {{ number_format(abs($vente->nombre_timbre) * 500, 0, ',', ' ') }} FCFA
                    </div>
                </div>
                @endforeach
                @if($dernieresVentesTimbres->isEmpty())
                <p class="no-data">Aucune vente de timbres récente</p>
                @endif
            </div>
        </div>

        <!-- Right Column: Charts -->
        <div class="right-column">
            <!-- Actes Chart -->
            <div class="chart-container">
                <h3>Actes des 7 derniers jours</h3>
                <canvas id="weeklyChart"></canvas>
            </div>
            
            <!-- Timbres Chart -->
            <div class="chart-container">
                <h3>Ventes de timbres (7 jours)</h3>
                <canvas id="timbresChart"></canvas>
            </div>
            
            <!-- Distribution Chart -->
            <div class="chart-container">
                <h3>Répartition des actes</h3>
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
.comptable-dashboard {
    padding: 20px;
    background-color: #f5f5f5;
    min-height: 100vh;
}

.dashboard-header {
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.dashboard-header h1 {
    color: #1977cc;
    margin: 0;
}

.dashboard-header p {
    color: #666;
    margin: 5px 0 0 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background-color: white;
    border-radius: 10px;
    padding: 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.stat-card.today {
    border-top: 4px solid #ff8800;
}

.stat-card.week {
    border-top: 4px solid #1977cc;
}

.stat-card.month {
    border-top: 4px solid #ff5500;
}

.stat-card.total {
    border-top: 4px solid #0055aa;
}

.stat-icon {
    font-size: 2rem;
    margin-right: 15px;
    color: #1977cc;
}

.stat-content h3 {
    margin: 0;
    color: #333;
    font-size: 1rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #1977cc;
    margin: 5px 0;
}

.stat-details {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.stat-details span {
    font-size: 0.85rem;
    color: #666;
}

.timbres-stats {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #f0f0f0;
}

.timbre-badge {
    background-color: #ff8800;
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    margin-right: 5px;
}

.montant-badge {
    background-color: #1977cc;
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.solde-badge {
    background-color: #0055aa;
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.dashboard-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.left-column, .right-column {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.recent-requests, .recent-timbres, .chart-container {
    background-color: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.recent-requests h2, .recent-timbres h2, .chart-container h3 {
    color: #1977cc;
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

.request-type {
    margin-bottom: 20px;
}

.request-type h3 {
    color: #ff8800;
    margin-bottom: 10px;
}

.request-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
}

.request-item:last-child {
    border-bottom: none;
}

.request-info h4 {
    margin: 0;
    color: #333;
}

.request-info p {
    margin: 5px 0 0 0;
    color: #666;
    font-size: 0.9rem;
}

.status-badge {
    background-color: #1977cc;
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
}

.timbre-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
}

.timbre-item:last-child {
    border-bottom: none;
}

.timbre-info h4 {
    margin: 0;
    color: #333;
}

.timbre-info p {
    margin: 5px 0 0 0;
    color: #666;
    font-size: 0.9rem;
}

.timbre-montant {
    font-weight: bold;
    color: #1977cc;
}

.no-data {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 20px;
}

.chart-container canvas {
    width: 100% !important;
    height: 250px !important;
}

@media (max-width: 992px) {
    .dashboard-content {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Weekly Chart
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    const weeklyChart = new Chart(weeklyCtx, {
        type: 'line',
        data: {
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
                    backgroundColor: 'rgba(51, 51, 51, 0.1)',
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
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Évolution des 7 derniers jours'
                }
            }
        }
    });

    // Timbres Chart
    const timbresCtx = document.getElementById('timbresChart').getContext('2d');
    const timbresChart = new Chart(timbresCtx, {
        type: 'bar',
        data: {
            labels: @json($labelsTimbres),
            datasets: [{
                label: 'Timbres vendus',
                data: @json($valeursTimbres),
                backgroundColor: '#ff8800',
                borderColor: '#e67e00',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Ventes quotidiennes de timbres'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nombre de timbres'
                    }
                }
            }
        }
    });

    // Distribution Chart
    const distCtx = document.getElementById('distributionChart').getContext('2d');
    const distChart = new Chart(distCtx, {
        type: 'doughnut',
        data: {
            labels: ['Naissances', 'Décès', 'Mariages'],
            datasets: [{
                data: [{{ $naissancenombre }}, {{ $decesnombre }}, {{ $mariagenombre }}],
                backgroundColor: [
                    '#1977cc',
                    '#333',
                    '#ff8800'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

@endsection