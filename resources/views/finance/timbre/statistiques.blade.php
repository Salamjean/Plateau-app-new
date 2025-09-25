@extends('caisse.layouts.template')
@section('content')
<style>
    :root {
        --primary-color: #0033c4;
        --primary-light: #3d5afe;
        --secondary-color: #6c757d;
        --light-bg: #f8f9fa;
        --card-shadow: 0 10px 20px rgba(0, 51, 196, 0.1);
        --card-hover: 0 15px 30px rgba(0, 51, 196, 0.15);
        --transition: all 0.3s ease;
    }

    .stats-container {
        padding: 2rem;
        max-width: 900px;
        margin: 0 auto;
        min-height: 80vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .stats-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .stats-header h1 {
        color: var(--primary-color);
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stats-header p {
        color: var(--secondary-color);
        font-size: 1.2rem;
    }

    .filter-container {
        display: flex;
        justify-content: center;
        margin-bottom: 2.5rem;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-label {
        font-weight: 600;
        color: var(--secondary-color);
        font-size: 1.1rem;
    }

    .date-filter {
        padding: 0.85rem 1.75rem;
        border: 2px solid var(--primary-color);
        border-radius: 50px;
        font-size: 1.1rem;
        color: var(--primary-color);
        background: white;
        cursor: pointer;
        transition: var(--transition);
    }

    .date-filter:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 51, 196, 0.2);
    }

    .btn-pdf {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        border: none;
        padding: 0.85rem 1.75rem;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 8px rgba(0, 51, 196, 0.2);
        text-decoration: none;
    }

    .btn-pdf:hover {
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
        box-shadow: 0 6px 12px rgba(0, 51, 196, 0.25);
    }

    .card-container {
        display: flex;
        justify-content: center;
        perspective: 1000px;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
        width: 100%;
        max-width: 700px;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--card-hover);
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        padding: 2rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h2 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 600;
    }

    .card-icon {
        font-size: 2.8rem;
        opacity: 0.9;
    }

    .card-body {
        padding: 2.5rem;
    }

    .stat-item {
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #f0f0f0;
    }

    .stat-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .stat-label {
        color: var(--secondary-color);
        font-size: 1.3rem;
        font-weight: 500;
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .stat-amount {
        font-size: 2.3rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .price-info {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px dashed #ddd;
        color: var(--secondary-color);
        font-size: 1.1rem;
    }

    .no-data {
        text-align: center;
        padding: 3rem;
        color: var(--secondary-color);
        font-size: 1.2rem;
    }

    @media (max-width: 768px) {
        .stats-container {
            padding: 1.5rem;
        }
        
        .stats-header h1 {
            font-size: 2.2rem;
        }
        
        .filter-container {
            flex-direction: column;
        }
        
        .stat-value, .stat-amount {
            font-size: 1.8rem;
        }
        
        .card-header {
            padding: 1.5rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .stat-label {
            font-size: 1.1rem;
        }
    }
</style>

<div class="stats-container">
    <div class="stats-header">
        <h1>Statistiques des Timbres</h1>
        <p>Suivez les ventes de timbres et les revenus générés</p>
    </div>

    <div class="filter-container">
        <span class="filter-label">Filtrer par mois :</span>
        <input type="month" id="month-filter" class="date-filter" value="{{ date('Y-m') }}">
        <a href="{{ route('caisse.stats.pdf', ['month' => date('Y-m')]) }}" class="btn-pdf" id="pdf-link">
            <i class="fas fa-file-pdf"></i> Télécharger PDF
        </a>
    </div>

    <div class="card-container">
        <div class="stat-card">
            <div class="card-header">
                <h2 id="card-title">Mois en cours</h2>
                <i class="fas fa-chart-line card-icon"></i>
            </div>
            <div class="card-body">
                <div class="stat-item">
                    <div class="stat-label">Timbres vendus</div>
                    <div class="stat-value" id="stamp-count">{{ $currentMonthStats->count ?? 0 }}</div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-label">Montant total généré</div>
                    <div class="stat-amount" id="total-amount">{{ number_format(($currentMonthStats->count ?? 0) * $timbrePrice, 0, ',', ' ') }} FCFA</div>
                </div>
                
                <div class="price-info">
                    Prix unitaire: {{ number_format($timbrePrice, 0, ',', ' ') }} FCFA
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const monthFilter = document.getElementById('month-filter');
        const cardTitle = document.getElementById('card-title');
        const stampCount = document.getElementById('stamp-count');
        const totalAmount = document.getElementById('total-amount');
        const pdfLink = document.getElementById('pdf-link');
        
        // Données réelles provenant du contrôleur
        const monthlyData = {
            @foreach($monthlyStats as $stat)
                "{{ $stat->year }}-{{ str_pad($stat->month, 2, '0', STR_PAD_LEFT) }}": {
                    count: {{ $stat->total }},
                    amount: {{ $stat->total * $timbrePrice }}
                },
            @endforeach
            "{{ date('Y-m') }}": {
                count: {{ $currentMonthStats->count ?? 0 }},
                amount: {{ ($currentMonthStats->count ?? 0) * $timbrePrice }}
            }
        };
        
        // Fonction pour formater le mois en français
        function formatMonth(dateString) {
            const date = new Date(dateString + '-01');
            const months = [
                'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
            ];
            return months[date.getMonth()] + ' ' + date.getFullYear();
        }
        
        // Fonction pour mettre à jour la carte en fonction du mois sélectionné
        function updateCard(month) {
            const data = monthlyData[month];
            
            if (data) {
                cardTitle.textContent = formatMonth(month);
                stampCount.textContent = data.count.toLocaleString('fr-FR');
                totalAmount.textContent = new Intl.NumberFormat('fr-FR').format(data.amount) + ' FCFA';
            } else {
                cardTitle.textContent = formatMonth(month);
                stampCount.textContent = '0';
                totalAmount.textContent = '0 FCFA';
            }
            
            // Mettre à jour le lien PDF avec le mois sélectionné
            pdfLink.href = "{{ route('caisse.stats.pdf') }}?month=" + month;
        }
        
        // Écouter les changements sur le filtre de mois
        monthFilter.addEventListener('change', function() {
            updateCard(this.value);
        });
        
        // Initialiser avec le mois en cours
        updateCard(monthFilter.value);
    });
</script>

<!-- Inclure Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@endsection