@extends('mairie.layouts.template')
@section('content')

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Mairie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{asset('dasboard/mairie.css')}}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Filtres -->
        <div class="filter-section mb-4">
            <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filtres</h5>
            <form method="GET" action="" class="row g-3">
                <div class="col-md-6">
                    <label for="month" class="form-label">Mois</label>
                    <select class="form-select" id="month" name="month">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="year" class="form-label">Année</label>
                    <select class="form-select" id="year" name="year">
                        @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-filter">
                        <i class="fas fa-sync-alt me-1"></i> Appliquer les filtres
                    </button>
                </div>
            </form>
        </div>

        <!-- Cartes de statistiques -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="dashboard-card card-primary p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Naissances</h6>
                            <h2 class="stat-number primary-text">{{ $naissancedash }}</h2>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary-light primary-text me-2">
                                    <i class="fas fa-chart-line me-1"></i> {{ number_format($NaissP, 1) }}%
                                </span>
                                <small class="text-muted">du total</small>
                            </div>
                        </div>
                        <div class="icon-circle bg-primary-light">
                            <i class="fas fa-baby primary-text"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="dashboard-card card-secondary p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Décès</h6>
                            <h2 class="stat-number secondary-text">{{ $decesdash }}</h2>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary-light secondary-text me-2">
                                    <i class="fas fa-chart-line me-1"></i> {{ number_format($DecesP, 1) }}%
                                </span>
                                <small class="text-muted">du total</small>
                            </div>
                        </div>
                        <div class="icon-circle bg-secondary-light">
                            <i class="fas fa-cross secondary-text"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="dashboard-card p-4" style="border-bottom: 4px solid red;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Mariages</h6>
                            <h2 class="stat-number" style="color: red;">{{ $mariagedash }}</h2>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light me-2" style="color: red;">
                                    <i class="fas fa-chart-line me-1"></i> {{ number_format($mariagePercentage, 1) }}%
                                </span>
                                <small class="text-muted">du total</small>
                            </div>
                        </div>
                        <div class="icon-circle bg-light">
                            <i class="fas fa-ring" style="color: red;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques et données récentes -->
        <div class="row g-4">
            <!-- Graphique circulaire -->
            <div class="col-lg-5">
                <div class="dashboard-card p-4">
                    <h5 class="section-title">Répartition des actes demandés</h5>
                    <div class="chart-container">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Données récentes -->
            <div class="col-lg-7">
                <div class="dashboard-card p-4">
                    <h5 class="section-title">Actes récents demandés</h5>
                    
                    <ul class="nav nav-tabs" id="recentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="naissances-tab" data-bs-toggle="tab" data-bs-target="#naissances" type="button" role="tab" style="color:#1977cc">Naissances</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="deces-tab" data-bs-toggle="tab" data-bs-target="#deces" type="button" role="tab" style="color:#6c757d">Décès</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="mariages-tab" data-bs-toggle="tab" data-bs-target="#mariages" type="button" role="tab" style="color:#ff0000">Mariages</button>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-3" id="recentTabsContent">
                        <div class="tab-pane fade show active" id="naissances" role="tabpanel">
                            @forelse($recentNaissances as $naissance)
                            <div class="recent-item">
                                <h6 class="mb-1">Demande d'extrait de naissance de  {{ $naissance->name }} {{ $naissance->prenom }}</h6>
                                <p class="mb-0 text-muted">Le {{ $naissance->created_at->format('d/m/Y') }} à {{ $naissance->created_at->format('H:i') }}</p>
                            </div>
                            @empty
                            <p class="text-muted">Aucune naissance enregistrée ce mois-ci.</p>
                            @endforelse
                        </div>
                        
                        <div class="tab-pane fade" id="deces" role="tabpanel">
                            @forelse($recentDeces as $deces)
                            <div class="recent-item">
                                <h6 class="mb-1">Demande d'extrait de décès de {{ $deces->name }}</h6>
                                <p class="mb-0 text-muted">Le {{ $deces->created_at->format('d/m/Y') }} à {{ $deces->created_at->format('H:i') }}</p>
                            </div>
                            @empty
                            <p class="text-muted">Aucun décès enregistré ce mois-ci.</p>
                            @endforelse
                        </div>
                        
                        <div class="tab-pane fade" id="mariages" role="tabpanel">
                            @forelse($recentMariages as $mariage)
                            <div class="recent-item">
                                <h6 class="mb-1">Demande d'extrait de mariage {{$mariage->nomEpoux == null ? 'copie simple' : 'copie integrale'}} par {{ $mariage->user->name }} {{ $mariage->user->prenom }}</h6>
                                <p class="mb-0 text-muted">Le {{ $mariage->created_at->format('d/m/Y') }} à {{ $mariage->created_at->format('H:i') }}</p>
                            </div>
                            @empty
                            <p class="text-muted">Aucun mariage enregistré ce mois-ci.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Graphique circulaire
        const ctx = document.getElementById('distributionChart').getContext('2d');
        const distributionChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Naissances', 'Décès', 'Mariages'],
                datasets: [{
                    data: [{{ $naissancedash }}, {{ $decesdash }}, {{ $mariagedash }}],
                    backgroundColor: [
                        '#1977cc',
                        '#6c757d',
                        'red'
                    ],
                    borderWidth: 0,
                    hoverOffset: 12
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
                                size: 13
                            },
                            padding: 20
                        }
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>
@endsection