@extends('user.layouts.template')

@section('content')
<div class="dashboard-final container-fluid px-4 pt-4 mb-5 animate-fade-in">
    
    <!-- Header Section -->
    <div class="row align-items-center mb-5">
        <div class="col-md-8">
            <h1 class="text-navy-bold mb-1">Tableau de bord</h1>
            <p class="text-grey mb-0">Bienvenue, <span class="text-primary font-weight-bold">{{ Auth::user()->prenom }}</span>. Voici l'état de vos démarches aujourd'hui.</p>
            @if(isset($freeRequestsRemaining) && $freeRequestsRemaining > 0)
            <div class="mt-2 d-inline-flex align-items-center bg-white px-3 py-1 shadow-sm border" style="border-radius: 50px;">
                <i class="fas fa-magic text-primary mr-2" style="font-size: 12px;"></i>
                <span class="x-small font-weight-bold text-navy">Plus que <span class="text-primary">{{ $freeRequestsRemaining }}</span> actes offerts !</span>
            </div>
            @endif
        </div>
        <div class="col-md-4 text-md-right mt-3 mt-md-0">
            <div class="d-inline-flex align-items-center bg-white p-2 px-3 shadow-sm" style="border-radius: 14px;">
                <i class="fas fa-calendar-alt text-primary mr-2"></i>
                <span class="small font-weight-bold text-navy">{{ now()->locale('fr')->translatedFormat('d M Y') }}</span>
            </div>
        </div>
    </div>


    <!-- Category KPIs -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm hover-lift glass-card h-100" style="border-radius: 24px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box-lg" style="background: rgba(1, 181, 116, 0.1); color: #01B574;">
                            <i class="fas fa-baby"></i>
                        </div>
                        <span class="badge-soft" style="background: rgba(1, 181, 116, 0.1); color: #01B574;">Naissance</span>
                    </div>
                    <h5 class="text-navy font-weight-bold mb-1">Demandes d'actes de Naissance</h5>
                    <p class="text-grey small mb-3">Suivi de vos demandes d'extraits</p>
                    <div class="d-flex align-items-baseline">
                        <h2 class="text-navy-bold mb-0 mr-2">{{ $naissancesCount }}</h2>
                        <small class="text-success font-weight-bold">Demandes</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm hover-lift glass-card h-100" style="border-radius: 24px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box-lg" style="background: rgba(255, 59, 103, 0.1); color: #FF3B67;">
                            <i class="fas fa-heart"></i>
                        </div>
                        <span class="badge-soft" style="background: rgba(255, 59, 103, 0.1); color: #FF3B67;">Mariage</span>
                    </div>
                    <h5 class="text-navy font-weight-bold mb-1">Demandes d'actes de Mariage</h5>
                    <p class="text-grey small mb-3">Suivi de vos contrats civils</p>
                    <div class="d-flex align-items-baseline">
                        <h2 class="text-navy-bold mb-0 mr-2">{{ $mariageCount }}</h2>
                        <small class="text-pink font-weight-bold" style="color: #FF3B67;">Demandes</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm hover-lift glass-card h-100" style="border-radius: 24px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="icon-box-lg" style="background: rgba(238, 93, 80, 0.1); color: #EE5D50;">
                            <i class="fas fa-cross"></i>
                        </div>
                        <span class="badge-soft" style="background: rgba(238, 93, 80, 0.1); color: #EE5D50;">Décès</span>
                    </div>
                    <h5 class="text-navy font-weight-bold mb-1">Demandes d'actes de Décès</h5>
                    <p class="text-grey small mb-3">Suivi des déclarations</p>
                    <div class="d-flex align-items-baseline">
                        <h2 class="text-navy-bold mb-0 mr-2">{{ $decesCount }}</h2>
                        <small class="text-danger font-weight-bold">Demandes</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Activity Row -->
    <div class="row">
        <!-- Performance Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 30px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="text-navy-bold mb-1">Évolution des Demandes</h5>
                        <p class="text-grey small mb-0">Performance mensuelle par type d'acte</p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="d-flex align-items-center mr-3">
                            <span style="width: 10px; height: 10px; background: #1977cc; border-radius: 50%; display: block; margin-right: 6px;"></span>
                            <small class="text-grey font-weight-bold">Naissance</small>
                        </div>
                        <div class="d-flex align-items-center mr-3">
                            <span style="width: 10px; height: 10px; background: #8c52ff; border-radius: 50%; display: block; margin-right: 6px;"></span>
                            <small class="text-grey font-weight-bold">Mariage</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span style="width: 10px; height: 10px; background: #EE5D50; border-radius: 50%; display: block; margin-right: 6px;"></span>
                            <small class="text-grey font-weight-bold">Décès</small>
                        </div>
                    </div>
                </div>
                <div class="chart-container-responsive" style="height: 350px;">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 30px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-navy-bold mb-0">Activités</h5>
                    <a href="{{route('user.history')}}" class="text-primary small font-weight-bold">Voir plus</a>
                </div>
                <div class="activity-timeline">
                    <!-- Timeline Item 1 -->
                    <div class="d-flex mb-4 position-relative">
                        <div class="timeline-line" style="position: absolute; left: 22px; top: 40px; bottom: -20px; width: 2px; background: #f4f7fe;"></div>
                        <div class="icon-box-sm bg-primary-light text-primary mr-3 flex-shrink-0" style="width: 45px; height: 45px; border-radius: 14px; display: flex; align-items: center; justify-content: center; z-index: 1;">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div>
                            <p class="text-navy font-weight-bold mb-0 small">Demande enregistrée</p>
                            <p class="text-grey x-small mb-1">Votre demande d'acte a été reçue.</p>
                            <span class="badge badge-soft-primary" style="font-size: 9px;">Recu</span>
                        </div>
                    </div>
                    <!-- Timeline Item 2 -->
                    <div class="d-flex mb-4 position-relative">
                        <div class="icon-box-sm bg-warning-light text-warning mr-3 flex-shrink-0" style="width: 45px; height: 45px; border-radius: 14px; display: flex; align-items: center; justify-content: center; z-index: 1;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <p class="text-navy font-weight-bold mb-0 small">En cours de traitement</p>
                            <p class="text-grey x-small mb-1">L'agent vérifie vos informations.</p>
                            <span class="badge badge-soft-warning" style="font-size: 9px; background: rgba(255, 181, 71, 0.1); color: #FFB547;">Traitement</span>
                        </div>
                    </div>
                    <!-- Timeline Item 3 -->
                    <div class="d-flex position-relative">
                        <div class="icon-box-sm bg-success-light text-success mr-3 flex-shrink-0" style="width: 45px; height: 45px; border-radius: 14px; display: flex; align-items: center; justify-content: center; z-index: 1;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-navy font-weight-bold mb-0 small">Dossier complété</p>
                            <p class="text-grey x-small mb-1">Tous vos documents sont valides.</p>
                            <span class="badge badge-soft-success" style="font-size: 9px; background: rgba(1, 181, 116, 0.1); color: #01B574;">Terminé</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.font.family = "'Poppins', sans-serif";
        Chart.defaults.color = '#a3aed0';

        const labels = ['Jan', 'Féb', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        const naissancesData = @json($totalNaissancesMonthly);
        const decesData = @json($totalDecesMonthly);
        const mariagesData = @json($mariageMonthly);

        const ctx = document.getElementById('evolutionChart').getContext('2d');
        
        // Custom Gradients
        const blueGrad = ctx.createLinearGradient(0, 0, 0, 300);
        blueGrad.addColorStop(0, 'rgba(25, 119, 204, 0.2)');
        blueGrad.addColorStop(1, 'rgba(25, 119, 204, 0)');

        const purpleGrad = ctx.createLinearGradient(0, 0, 0, 300);
        purpleGrad.addColorStop(0, 'rgba(140, 82, 255, 0.2)');
        purpleGrad.addColorStop(1, 'rgba(140, 82, 255, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Naissances',
                        data: naissancesData,
                        borderColor: '#1977cc',
                        backgroundColor: blueGrad,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Mariages',
                        data: mariagesData,
                        borderColor: '#8c52ff',
                        backgroundColor: purpleGrad,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Décès',
                        data: decesData,
                        borderColor: '#EE5D50',
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.4,
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#fff',
                        titleColor: '#2b3674',
                        bodyColor: '#a3aed0',
                        borderColor: '#f4f7fe',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 12,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f4f7fe', borderDash: [5, 5], drawBorder: false },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false, drawBorder: false }
                    }
                }
            }
        });

        // Welcome Popup
        @if(isset($showFreeRequestsMessage) && $showFreeRequestsMessage)
            setTimeout(() => {
                Swal.fire({
                    title: '<span class="text-primary font-weight-bold">FÉLICITATIONS ! 🎊</span>',
                    html: `
                        <div class="p-3">
                            <h4 class="text-navy font-weight-bold mb-3">Vos premiers actes sont offerts !</h4>
                            <p class="text-grey small mb-4">
                                Pour célébrer votre arrivée sur Plateau App, nous vous offrons vos 
                                <span class="text-primary font-weight-bold">{{ $freeRequestsRemaining }} prochaines demandes</span> entièrement gratuitement.
                            </p>
                        </div>
                    `,
                    confirmButtonText: 'C\'est génial ! 🚀',
                    confirmButtonColor: '#1977cc',
                    borderRadius: '30px',
                    backdrop: `rgba(25, 119, 204, 0.2)`
                }).then(() => {
                    fetch("{{ route('user.dismiss.free.requests') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                });
            }, 1000);
        @endif
    });
</script>
@endpush