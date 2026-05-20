@extends('poste.layouts.template')

@section('content')
<style>
    :root {
        --primary: #1f4083;
        --primary-light: #2d5aa8;
        --primary-dark: #152e5c;
        --secondary: #718096;
        --accent: #38a169;
        --danger: #e53e3e;
        --warning: #f6ad55;
        --info: #4299e1;
        --success: #48bb78;
        --light-bg: #f8fafc;
        --card-bg: #ffffff;
        --text-primary: #2d3748;
        --text-secondary: #718096;
        --text-muted: #a0aec0;
        --border-color: #e2e8f0;
        --shadow-sm: 0 2px 8px rgba(31, 64, 131, 0.08);
        --shadow-md: 0 4px 16px rgba(31, 64, 131, 0.12);
        --shadow-lg: 0 8px 32px rgba(31, 64, 131, 0.16);
        --gradient-primary: linear-gradient(135deg, #1f4083 0%, #2d5aa8 100%);
        --radius-sm: 12px;
        --radius-md: 16px;
        --radius-lg: 20px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dashboard-container {
        padding: 1.5rem 2rem;
        background-color: var(--light-bg);
        min-height: 100vh;
    }

    /* === HEADER === */
    .dashboard-header {
        margin-bottom: 2.5rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .header-content h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary);
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.5px;
    }

    .date-badge {
        background: white;
        padding: 0.875rem 1.5rem;
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid var(--border-color);
    }

    .date-badge i {
        color: var(--primary);
        font-size: 1.25rem;
    }

    .date-badge span {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 0.95rem;
    }

    /* === KPI CARDS === */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .kpi-card {
        background: var(--card-bg);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-light);
    }

    .kpi-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        transition: var(--transition);
    }

    .kpi-info {
        flex: 1;
    }

    .kpi-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        font-weight: 500;
        margin-bottom: 0.25rem;
        display: block;
    }

    .kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
    }

    /* KPI Colors */
    .kpi-total .kpi-icon { background: rgba(31, 64, 131, 0.1); color: var(--primary); }
    .kpi-pending .kpi-icon { background: rgba(246, 173, 85, 0.1); color: var(--warning); }
    .kpi-process .kpi-icon { background: rgba(66, 153, 225, 0.1); color: var(--info); }
    .kpi-delivered .kpi-icon { background: rgba(72, 187, 120, 0.1); color: var(--success); }

    /* === CHARTS === */
    .dashboard-row {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .chart-card {
        background: white;
        border-radius: var(--radius-md);
        padding: 1.5rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .chart-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .chart-title i {
        color: var(--primary);
    }

    .table-container {
        padding: 0;
    }

    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .modern-table th {
        background: #f8fafc;
        padding: 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-secondary);
        border-bottom: 2px solid var(--border-color);
        text-align: center;
    }

    .modern-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        text-align: center;
        font-size: 0.95rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    @media (max-width: 1024px) {
        .dashboard-row { grid-template-columns: 1fr; }
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="header-content">
            <h1>Tableau de Bord</h1>
            <p class="text-muted">Gérez vos livraisons et suivez vos statistiques en temps réel.</p>
        </div>
        <div class="date-badge">
            <i class="material-icons">calendar_today</i>
            <span>{{ now()->translatedFormat('l d F Y') }}</span>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="kpi-grid">
        <div class="kpi-card kpi-total">
            <div class="kpi-icon"><i class="material-icons">inventory_2</i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total des colis</span>
                <div class="kpi-value">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="kpi-card kpi-pending">
            <div class="kpi-icon"><i class="material-icons">pending_actions</i></div>
            <div class="kpi-info">
                <span class="kpi-label">En attente</span>
                <div class="kpi-value">{{ $stats['en_attente'] }}</div>
            </div>
        </div>
        <div class="kpi-card kpi-process">
            <div class="kpi-icon"><i class="material-icons">local_shipping</i></div>
            <div class="kpi-info">
                <span class="kpi-label">En cours</span>
                <div class="kpi-value">{{ $stats['en_cours'] }}</div>
            </div>
        </div>
        <div class="kpi-card kpi-delivered">
            <div class="kpi-icon"><i class="material-icons">task_alt</i></div>
            <div class="kpi-info">
                <span class="kpi-label">Livrés</span>
                <div class="kpi-value">{{ $stats['livre'] }}</div>
            </div>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card kpi-total" style="cursor: pointer;" id="availablePackagesCard">
            <div class="kpi-icon"><i class="material-icons">apartment</i></div>
            <div class="kpi-info">
                <span class="kpi-label">Colis en mairie</span>
                <div class="kpi-value">{{ $stats['non_attribue'] }}</div>
            </div>
        </div>
        <div class="kpi-card kpi-delivered">
            <div class="kpi-icon"><i class="material-icons">person</i></div>
            <div class="kpi-info">
                <span class="kpi-label">Livreurs dispo</span>
                <div class="kpi-value">{{ $livreurDispo }}</div>
            </div>
        </div>
        <div class="kpi-card kpi-pending">
            <div class="kpi-icon"><i class="material-icons">person_off</i></div>
            <div class="kpi-info">
                <span class="kpi-label">Livreurs indispo</span>
                <div class="kpi-value">{{ $livreurIndispo }}</div>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="dashboard-row">
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title"><i class="material-icons">bar_chart</i> Activité récente</h3>
            </div>
            <canvas id="deliveryChart" height="120"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-header">
                <h3 class="chart-title"><i class="material-icons">pie_chart</i> Répartition</h3>
            </div>
            <canvas id="typeChart"></canvas>
        </div>
    </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bar Chart
    const barCtx = document.getElementById('deliveryChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [
                { 
                    label: 'Livrés', 
                    data: @json($chartData['livre']), 
                    backgroundColor: '#1f4083',
                    borderRadius: 6
                },
                { 
                    label: 'En cours', 
                    data: @json($chartData['en_cours']), 
                    backgroundColor: '#f6ad55',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top', align: 'end' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5] },
                    ticks: { precision: 0 }
                },
                x: { grid: { display: false } }
            }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Naissance', 'Décès', 'Mariage'],
            datasets: [{
                data: [
                    {{ $counts['naissance'] }},
                    {{ $counts['deces'] }},
                    {{ $counts['mariage'] }}
                ],
                backgroundColor: ['#1f4083', '#f6ad55', '#4299e1'],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: { 
            responsive: true, 
            plugins: { 
                legend: { position: 'bottom' } 
            } 
        }
    });

    // Mairies Click
    const mairieBtn = document.getElementById('availablePackagesCard');
    if (mairieBtn) {
        mairieBtn.addEventListener('click', function() {
            Swal.fire({
                title: 'Chargement...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('{{ route("poste.mairies.colis") }}')
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    if (data.success) {
                        let content = '<div style="max-height: 400px; overflow-y: auto;">';
                        if (data.mairies.length > 0) {
                            data.mairies.forEach(mairie => {
                                content += `
                                    <div style="display: flex; justify-content: space-between; padding: 12px; border-bottom: 1px solid #eee;">
                                        <span style="font-weight: 600;">${mairie.commune}</span>
                                        <span style="background: #1f4083; color: white; padding: 2px 10px; border-radius: 20px; font-size: 0.85rem;">${mairie.total} colis</span>
                                    </div>`;
                            });
                        } else {
                            content += '<p>Aucun colis disponible</p>';
                        }
                        content += '</div>';
                        Swal.fire({
                            title: 'Colis par Mairies',
                            html: content,
                            icon: 'info',
                            width: '500px'
                        });
                    }
                });
        });
    }
});
</script>
@endsection
