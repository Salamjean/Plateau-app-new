@extends('dhl.layouts.template')

@section('content')
<style>
    :root {
        --primary-color: #d40511;
        --primary-light: rgba(6, 99, 78, 0.1);
        --secondary-color: #f9cf03;
        --secondary-light: rgba(249, 207, 3, 0.1);
        --light-color: #f8f9fa;
        --dark-color: #343a40;
    }

    .dashboard-container {
        padding: 2rem;
        background-color: #f5f7fa;
        min-height: 100vh;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .dashboard-title {
        color: var(--primary-color);
        font-weight: 700;
        font-size: 1.8rem;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
        border-top: 4px solid var(--primary-color);
    }

    .stat-card:hover { transform: translateY(-5px); }
    .stat-card.secondary { border-top-color: var(--secondary-color); }

    .stat-title { color: #6c757d; font-size: 0.9rem; margin-bottom: 0.5rem; font-weight: 600; }
    .stat-value { color: var(--dark-color); font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem; }

    .section-title { color: var(--primary-color); font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center; }
    .section-title i { margin-right: 0.5rem; }

    .charts-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .chart-card {
        flex: 1;
        min-width: 300px;
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .recent-activity {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }

    table { width: 100%; border-collapse: collapse; }
    table th, table td { padding: 0.75rem; border: 1px solid #ddd; text-align: left; }
    table th { background-color: #d40511 ; font-weight: 600; color: white }

    @media (max-width: 768px) { 
        .charts-row { flex-direction: column; } 
        .stats-container { grid-template-columns: 1fr; } 
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Tableau de Bord</h1>
        <div class="date-selector">
            <span class="text-black">{{ now()->translatedFormat('l d F Y') }}</span>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-title">Total des colis enregistrés</div>
            <div class="stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card secondary">
            <div class="stat-title">En attente</div>
            <div class="stat-value">{{ $stats['en_attente'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">En cours</div>
            <div class="stat-value">{{ $stats['en_cours'] }}</div>
        </div>
        <div class="stat-card secondary">
            <div class="stat-title">Livrés</div>
            <div class="stat-value">{{ $stats['livre'] }}</div>
        </div>
    </div>
    <div class="stats-container">
       <div class="stat-card secondary" style="cursor: pointer;" id="availablePackagesCard">
            <div class="stat-title">Total de colis disponibles dans les mairies</div>
            <div class="stat-value">{{ $stats['non_attribue'] }}</div>
        </div>
        <div class="stat-card ">
            <div class="stat-title">Agence(s) disponible(s) </div>
            <div class="stat-value"> {{$livreurDispo}} </div>
        </div>
        <div class="stat-card secondary">
            <div class="stat-title">Agence(s) indisponible(s)</div>
            <div class="stat-value">{{$livreurIndispo}}</div>
        </div>
        <div class="stat-card ">
    <div class="stat-title">Solde disponible</div>
    <div class="stat-value">{{ number_format($soldeDisponible, 0, ',', ' ') }} FCFA</div>
    <div class="stat-subtitle">Total des livraisons effectuées</div>
</div>
    </div>

    <!-- Graphiques côte à côte -->
    <div class="charts-row">
        <div class="chart-card">
            <h3 class="section-title"><i class="fas fa-chart-bar"></i> Activité récente</h3>
            <canvas id="deliveryChart" height="150"></canvas>
        </div>
        <div class="chart-card">
            <h3 class="section-title"><i class="fas fa-chart-pie"></i> Répartition par type</h3>
            <canvas id="typeChart" height="150"></canvas>
        </div>
    </div>

    <!-- Dernières demandes dans un tableau -->
    <div class="recent-activity">
        <h3 class="section-title"><i class="fas fa-history"></i> Dernières demandes</h3>
        @if($activites->isEmpty())
            <div class="text-center py-3 text-muted">Aucune activité récente</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th class="text-center">Code de livraison</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activites as $activite)
                    <tr>
                        <td class="text-center">{{ $activite->livraison_code }}</td>
                        <td class="text-center">{{ $activite->statut_livraison }}</td>
                        <td class="text-center">{{ $activite->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<!-- Scripts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique Barres (nombres entiers)
    new Chart(document.getElementById('deliveryChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: @json($chartData['labels']),
        datasets: [
            { label: 'Livrés', data: @json($chartData['livre']), backgroundColor: '#d40511' },
            { label: 'En cours', data: @json($chartData['en_cours']), backgroundColor: '#f9cf03' }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    precision: 0,
                    callback: function(value) { return Number(value).toFixed(0); }
                },
                suggestedMax: Math.max(...@json($chartData['livre']), ...@json($chartData['en_cours'])) + 1
            }
        }
    }
});


    // Graphique Circulaire
    new Chart(document.getElementById('typeChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Naissance', 'Décès', 'Mariage'],
            datasets: [{
                data: [
                    {{ $counts['naissance'] }},
                    {{ $counts['deces'] }},
                    {{ $counts['mariage'] }}
                ],
                backgroundColor: ['#d40511', '#f9cf03', '#d4a700']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'right' } } }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du clic sur la carte des colis disponibles
    document.getElementById('availablePackagesCard').addEventListener('click', function() {
        // Afficher un loader pendant le chargement des données
        Swal.fire({
            title: 'Chargement des données...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Récupérer les données des mairies via AJAX
        fetch('{{ route("dhl.mairies.colis") }}', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            
            if (data.success) {
                // Formater le contenu HTML pour SweetAlert2
                let content = '<div class="text-left">';
                
                if (data.mairies.length > 0) {
                    data.mairies.forEach(mairie => {
                        content += `
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom">
                                <div>
                                    <strong>${mairie.commune}</strong>
                                </div>
                                <div>
                                    <span class="badge rounded-pill">${mairie.total} colis</span>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    content += '<p class="text-center">Aucun colis disponible dans les mairies</p>';
                }
                
                content += '</div>';
                
                // Afficher la popup avec les données
                Swal.fire({
                    title: 'Colis disponibles par mairie',
                    html: content,
                    icon: 'info',
                    confirmButtonText: 'Fermer',
                    width: '600px'
                });
            } else {
                Swal.fire('Erreur', 'Impossible de charger les données des mairies', 'error');
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire('Erreur', 'Une erreur s\'est produite lors du chargement des données', 'error');
            console.error('Error:', error);
        });
    });
});
</script>
@endsection
