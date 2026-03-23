@extends('user.layouts.template')
@section('content')
<div class="dashboard-final container-fluid px-4 pt-4 mb-5">
    
    <!-- Sophisticated Split Hero Banner -->
    <div class="card border-0 mb-4 overflow-hidden shadow-sm" style="border-radius: 24px; min-height: 200px;">
        <div class="row no-gutters hero-banner-row">
            <!-- Left Side (Blue Stats Summary) -->
            <div class="col-12 col-lg-7 p-4 p-md-5 d-flex flex-column justify-content-center bg-primary-gradient text-white">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge badge-light mr-2 px-3 py-1" style="border-radius: 50px; color: var(--primary); font-weight: 800; font-size: 10px;">VOTRE ACTIVITÉ</span>
                    <span class="small opacity-8">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</span>
                </div>
                <h2 class="font-weight-bold mb-2">Bonjour, {{ Auth::user()->prenom }} !</h2>
                <p class="opacity-8 mb-0">Ravi de vous revoir. Voici un aperçu de vos demandes d'actes d'état civil.</p>
                
                @if(isset($freeRequestsRemaining) && $freeRequestsRemaining > 0)
                <div class="mt-4 d-inline-flex align-items-center px-3 py-1 bg-white-20" style="border-radius: 50px; background: rgba(255,255,255,0.15); width: fit-content;">
                    <i class="fas fa-magic mr-2"></i>
                    <small class="font-weight-bold">Plus que {{ $freeRequestsRemaining }} actes offerts !</small>
                </div>
                @endif
            </div>
            <!-- Right Side (Main Action) -->
            <div class="col-12 col-lg-5 p-4 p-md-5 bg-white d-flex flex-column justify-content-center border-left hero-banner-right">
                <h5 class="font-weight-bold text-navy mb-3">Besoin d'un acte ?</h5>
                <p class="text-grey small mb-4">Commandez vos extraits en ligne et suivez leur livraison.</p>
                <div class="d-flex gap-2">
                    <a href="{{route('user.extrait.create')}}" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm" style="border-radius: 12px; flex: 1;">
                        Nouvelle Demande
                    </a>
                    <a href="{{route('user.history')}}" class="btn btn-outline-primary px-3 py-2" style="border-radius: 12px;">
                        <i class="fas fa-history"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Section (Compact & Premium) -->
    <div class="row mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 18px; border-top: 3px solid var(--success) !important;">
                <div class="d-flex align-items-center">
                    <div class="mr-3 text-success p-2 bg-success-light" style="border-radius: 10px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-baby"></i></div>
                    <div>
                        <p class="text-grey x-small mb-0">Naissances</p>
                        <h5 class="font-weight-bold text-navy mb-0">{{ $naissancesCount }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 18px; border-top: 3px solid var(--pink) !important;">
                <div class="d-flex align-items-center">
                    <div class="mr-3 text-pink p-2 bg-pink-light" style="border-radius: 10px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-heart"></i></div>
                    <div>
                        <p class="text-grey x-small mb-0">Mariages</p>
                        <h5 class="font-weight-bold text-navy mb-0">{{ $mariageCount }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius: 18px; border-top: 3px solid var(--danger) !important;">
                <div class="d-flex align-items-center">
                    <div class="mr-3 text-danger p-2 bg-danger-light" style="border-radius: 10px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-skull-crossbones"></i></div>
                    <div>
                        <p class="text-grey x-small mb-0">Décès</p>
                        <h5 class="font-weight-bold text-navy mb-0">{{ $decesCount }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 border-top-primary" style="border-radius: 18px;">
                <div class="d-flex align-items-center">
                    <div class="mr-3 text-primary p-2 bg-primary-light" style="border-radius: 10px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-file-invoice"></i></div>
                    <div>
                        <p class="text-grey x-small mb-0">Total</p>
                        <h5 class="font-weight-bold text-navy mb-0">{{ $nombreDemandes }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="row">
        <!-- Main Column -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4 border-top-primary" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold text-navy mb-0">Performance des Services</h5>
                    <div class="chart-legend d-flex gap-3 small text-grey">
                        <span><i class="fas fa-circle text-primary mr-1"></i> Naissances</span>
                        <span><i class="fas fa-circle text-danger mr-1"></i> Décès</span>
                        <span><i class="fas fa-circle mr-1" style="color: #8c52ff;"></i> Mariages</span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="chart-container-responsive" style="height: 380px; position: relative; width: 100%;">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Activity List -->
            <div class="card border-0 shadow-sm p-4 border-top-primary mb-4" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="font-weight-bold text-navy mb-0">Activités Récentes</h5>
                    <a href="{{route('user.history')}}" class="text-primary small font-weight-bold">Voir tout</a>
                </div>
                <div class="activity-feed">
                    <div class="activity-item d-flex align-items-center py-3 border-bottom">
                        <div class="activity-icon bg-success-light text-success mr-3 p-2" style="border-radius: 12px; width:45px; height:45px; display:flex; align-items:center; justify-content:center; font-size:1.2rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="font-weight-bold text-navy mb-1 small">Demande d'acte transmise</h6>
                            <p class="text-grey mb-0 x-small">Date : {{ now()->format('d/m/Y') }} • Ref: #{{ rand(1000, 9999) }}</p>
                        </div>
                        <span class="badge badge-soft-primary px-3 py-1" style="border-radius: 50px; font-size: 10px;">Traitement</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 border-top-primary" style="border-radius: 20px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="font-weight-bold text-navy mb-0">Répartition par type</h5>
                </div>
                <div class="card-body p-4">
                    <div style="height: 250px;">
                        <canvas id="distributionChart"></canvas>
                    </div>
                    <div class="mt-4 px-2">
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="text-grey small"><i class="fas fa-truck-fade mr-2 text-warning"></i> En cours</span>
                            <span class="font-weight-bold text-navy">{{ $TotalEtatCount }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3">
                            <span class="text-grey small"><i class="fas fa-check-double mr-2 text-success"></i> Livrées</span>
                            <span class="font-weight-bold text-navy">{{ $TotalLivreCount }}</span>
                        </div>
                    </div>
                </div>
                 <!-- Support -->
            <div class="card border-0 p-4 shadow-sm position-relative overflow-hidden" style="border-radius: 24px; background: #2b3674;">
                <div style="position: absolute; top: -10px; right: -10px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                <div class="text-white">
                    <h5 class="font-weight-bold mb-2">Un souci ?</h5>
                    <p class="small opacity-8 mb-4">Nos agents sont là pour vous aider par WhatsApp ou Email.</p>
                    <a href="#" class="btn btn-white btn-block font-weight-bold" style="border-radius: 50px; color: #2b3674; font-size: 0.9rem;">Contacter Support</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Modern Chart.js Global Config
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#a3aed0';
        Chart.defaults.scale.grid.color = 'rgba(163, 174, 208, 0.05)';

        // Data from Controller
        const labels = ['Jan', 'Féb', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        const naissancesData = @json($totalNaissancesMonthly);
        const decesData = @json($totalDecesMonthly);
        const mariagesData = @json($mariageMonthly);

        // Evolution Chart (Evolution des actes)
        const evolutionCtx = document.getElementById('evolutionChart');
        if (evolutionCtx) {
            const ctx = evolutionCtx.getContext('2d');
            const blueGradient = ctx.createLinearGradient(0, 0, 0, 400);
            blueGradient.addColorStop(0, 'rgba(25, 119, 204, 0.2)');
            blueGradient.addColorStop(1, 'rgba(25, 119, 204, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Naissances',
                            data: naissancesData,
                            borderColor: '#1977cc',
                            backgroundColor: blueGradient,
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Décès',
                            data: decesData,
                            borderColor: '#EE5D50',
                            backgroundColor: 'rgba(238, 93, 80, 0.05)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Mariages',
                            data: mariagesData,
                            borderColor: '#8c52ff',
                            backgroundColor: 'rgba(140, 82, 255, 0.05)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 0,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false } // We use custom legends in HTML
                    },
                    scales: {
                        y: { 
                            beginAtZero: true,
                            grid: { borderDash: [5, 5] },
                            border: { display: false }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Distribution Chart (Répartition)
        const distributionCtx = document.getElementById('distributionChart');
        if (distributionCtx) {
            new Chart(distributionCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Naissances', 'Décès', 'Mariages'],
                    datasets: [{
                        data: [{{ $totalNaissances }}, {{ $totalDeces }}, {{ $mariageCount }}],
                        backgroundColor: ['#1977cc', '#EE5D50', '#8c52ff'],
                        borderWidth: 0,
                        hoverOffset: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 10, padding: 20 } }
                    },
                    cutout: '80%'
                }
            });
        }
    });
</script>
@endpush