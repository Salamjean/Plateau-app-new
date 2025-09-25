@extends('finance.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<div class="dashboard-container">
    <h1 class="page-title">
        <i class="fas fa-chart-line me-2"></i>Tableau de Bord des Ventes
    </h1>

    <div class="row">
        <!-- Cartes statistiques -->
        <div class="stats-grid">
            <div class="col-md-4">
                <div class="stat-card stat-card-today fade-in">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-number">{{ number_format($stats['today'], 0, ',', ' ') }}</div>
                    <div class="stat-label">Timbres vendus aujourd'hui</div>
                    <span class="stat-badge">Aujourd'hui</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card stat-card-week fade-in">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stat-number">{{ number_format($stats['this_week'], 0, ',', ' ') }}</div>
                    <div class="stat-label">Timbres vendus cette semaine</div>
                    <span class="stat-badge">Cette semaine</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card stat-card-month fade-in">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-number">{{ number_format($stats['this_month'], 0, ',', ' ') }}</div>
                    <div class="stat-label">Timbres vendus ce mois</div>
                    <span class="stat-badge">Ce mois</span>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-list me-2"></i>Liste des ventes effectuées par les financiers</h5>
                    <div class="card-actions">
                        <a href="{{ route('finance.timbre.vente') }}" class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Actualiser">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="filter-section">
                        <form method="GET" class="filter-form">
                            <div class="filter-inputs">
                                <div class="input-group">
                                    <label class="form-label"><i class="fas fa-calendar me-1"></i>Date début</label>
                                    <input type="date" name="date_debut" class="form-control" 
                                        value="{{ request('date_debut') }}">
                                </div>
                                <div class="input-group">
                                    <label class="form-label"><i class="fas fa-calendar me-1"></i>Date fin</label>
                                    <input type="date" name="date_fin" class="form-control" 
                                        value="{{ request('date_fin') }}">
                                </div>
                                <div class="input-group">
                                    <label class="form-label"><i class="fas fa-user me-1"></i>Financier</label>
                                    <select name="financier_id" class="form-control">
                                        <option value="">Tous les agents finnanciers</option>
                                        @foreach($financiers as $financier)
                                            <option value="{{ $financier->id }}" 
                                                    {{ request('financier_id') == $financier->id ? 'selected' : '' }}>
                                                {{ $financier->name }} {{ $financier->prenom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-2"></i> Appliquer
                                    </button>
                                </div>
                                <div class="input-group">
                                    <a href="{{ route('finance.timbre.vente') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i> Réinitialiser
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col" style="text-align: center">Date</th>
                                    <th scope="col" style="text-align: center">Heure</th>
                                    <th scope="col" style="text-align: center">Financier</th>
                                    <th scope="col" style="text-align: center" class="text-right">Quantité vendue</th>
                                    <th scope="col" style="text-align: center" class="text-center">Type opération</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventes as $vente)
                                <tr>
                                    <td style="text-align: center" >
                                        <div class="d-flex align-items-center" style="display: flex; justify-content:center">
                                            <i class="far fa-calendar-alt me-2"></i>
                                            {{ $vente->created_at->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td style="text-align: center">
                                        <span class="time-badge">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $vente->created_at->format('H:i') }}
                                        </span>
                                    </td>
                                    <td style="text-align: center">
                                        <div class="d-flex align-items-center" style="display: flex; justify-content:center">
                                            <div class="user-avatar">
                                                {{ substr(($vente->comptable->name ?? 'N'), 0, 1) }}{{ substr(($vente->comptable->prenom ?? 'A'), 0, 1) }}
                                            </div>
                                            <div class="user-info">
                                                {{ $vente->comptable->name ?? 'N/A' }} {{ $vente->comptable->prenom ?? '' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center" style="display: flex; justify-content:center">
                                        <span class="quantity quantity-vente">
                                            {{ number_format(abs($vente->nombre_timbre), 0, ',', ' ') }} timbres
                                        </span>
                                    </td>
                                    <td style="text-align: center" class="text-center">
                                        <span class="operation-badge">
                                            <i class="fas fa-shopping-cart me-1"></i> Vente
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td style="text-align: center" colspan="5" class="empty-state">
                                        <i class="fas fa-receipt"></i>
                                        <h5>Aucune vente enregistrée pour les financiers</h5>
                                        <a href="{{ route('finance.timbre.vente') }}" class="btn btn-outline">
                                            Actualiser
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($ventes->hasPages())
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Affichage de {{ $ventes->firstItem() }} à {{ $ventes->lastItem() }} sur {{ $ventes->total() }} résultats
                        </div>
                        <div class="pagination-links">
                            {{ $ventes->appends(request()->query())->links('pagination.custom') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-color: #ff8800;
        --primary-light: #ffaa40;
        --primary-dark: #cc6d00;
        --secondary-color: #1977cc;
        --secondary-light: #1977cc;
        --secondary-dark: #1977cc;
        --light-color: #ffffff;
        --dark-color: #212529;
        --gray-color: #6c757d;
        --light-gray: #f8f9fa;
        --border-radius: 12px;
        --box-shadow: 0 8px 20px rgba(0, 126, 0, 0.1);
        --transition: all 0.3s ease;
    }
    
    body {
        background-color: var(--light-gray);
        font-family: 'Inter', 'Segoe UI', sans-serif;
        color: var(--dark-color);
    }
    
    .dashboard-container {
        padding: 30px;
        width: 90%;
        margin: 0 auto;
    }
    
    .page-title {
        color: var(--secondary-color);
        font-weight: 700;
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
        display: flex;
        align-items: center;
    }
    
    .page-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100px;
        height: 4px;
        background: var(--primary-color);
        border-radius: 2px;
    }
    
    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        margin-bottom: 25px;
        overflow: hidden;
        background-color: var(--light-color);
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0, 126, 0, 0.15);
    }
    
    .card-header {
        background-color: var(--secondary-color);
        color: white;
        padding: 18px 25px;
        font-weight: 600;
        border-bottom: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-header h5 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    
    .card-header i {
        font-size: 1.3rem;
        margin-right: 12px;
    }
    
    .card-body {
        padding: 25px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        text-align: center;
        border-left: 5px solid var(--primary-color);
    }
    
    .stat-card-today {
        border-left: 5px solid var(--primary-color);
    }
    
    .stat-card-week {
        border-left: 5px solid var(--secondary-color);
    }
    
    .stat-card-month {
        border-left: 5px solid #6a11cb;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0, 126, 0, 0.15);
    }
    
    .stat-icon {
        font-size: 2rem;
        margin-bottom: 15px;
        color: var(--primary-color);
    }
    
    .stat-card-week .stat-icon {
        color: var(--secondary-color);
    }
    
    .stat-card-month .stat-icon {
        color: #6a11cb;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: var(--dark-color);
    }
    
    .stat-label {
        color: var(--gray-color);
        font-weight: 500;
        margin-bottom: 10px;
    }
    
    .stat-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        background-color: rgba(0, 126, 0, 0.1);
        color: var(--secondary-color);
    }
    
    .filter-section {
        margin-bottom: 25px;
    }
    
    .filter-form {
        background: var(--light-gray);
        border-radius: var(--border-radius);
        padding: 20px;
    }
    
    .filter-inputs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }
    
    .input-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-label {
        font-weight: 600;
        margin-bottom: 10px;
        color: var(--dark-color);
        display: flex;
        align-items: center;
    }
    
    .form-label i {
        margin-right: 8px;
        color: var(--secondary-color);
    }
    
    .form-control {
        border-radius: var(--border-radius);
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        font-weight: 500;
        transition: var(--transition);
        background: var(--light-color);
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(255, 136, 0, 0.1);
    }
    
    .btn {
        border-radius: 20px;
        padding: 12px 20px;
        font-weight: 600;
        transition: var(--transition);
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-sm {
        padding: 8px 16px;
        font-size: 0.875rem;
    }
    
    .btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--light-gray);
        color: var(--primary-color);
        transition: var(--transition);
    }
    
    .btn-icon:hover {
        background: var(--primary-color);
        color: var(--light-color);
        transform: rotate(45deg);
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        color: var(--light-color);
    }
    
    .btn-primary:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 136, 0, 0.3);
    }
    
    .btn-secondary {
        background-color: var(--secondary-color);
        color: var(--light-color);
    }
    
    .btn-secondary:hover {
        background-color: var(--secondary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 126, 0, 0.3);
    }
    
    .btn-outline {
        background: transparent;
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
    }
    
    .btn-outline:hover {
        background: var(--primary-color);
        color: var(--light-color);
    }
    
    .table {
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        width: 100%;
        margin-bottom: 0;
    }
    
    .table th {
        background-color: rgba(0, 126, 0, 0.05);
        color: var(--secondary-color);
        font-weight: 600;
        border: none;
        padding: 15px;
        text-transform: uppercase;
        font-size: 0.8rem;
        text-align: left;
    }
    
    .table th.text-right {
        text-align: right;
    }
    
    .table th.text-center {
        text-align: center;
    }
    
    .table td {
        padding: 15px;
        vertical-align: middle;
        border-top: 1px solid rgba(0, 126, 0, 0.05);
        color: var(--dark-color);
    }
    
    .table tbody tr {
        transition: var(--transition);
    }
    
    .table tbody tr:hover {
        background: rgba(255, 136, 0, 0.03);
    }
    
    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: var(--light-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    
    .time-badge {
        background: var(--light-gray);
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }
    
    .quantity {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--primary-color);
        display: inline-flex;
        align-items: center;
    }
    
    .operation-badge {
        background: rgba(0, 126, 0, 0.1);
        color: var(--secondary-color);
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 0;
        color: var(--gray-color);
    }
    
    .empty-state i {
        display: flex;
        justify-content: center;
        font-size: 50px;
        margin-bottom: 15px;
        color: #dee2e6;
    }
    
    .empty-state h5 {
        text-align: center;
        font-weight: 500;
        color: var(--gray-color);
        margin-bottom: 20px;
    }
    
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 25px;
        padding: 15px;
        background: var(--light-gray);
        border-radius: var(--border-radius);
    }
    
    .pagination-info {
        font-size: 0.875rem;
        color: var(--gray-color);
    }
    
    .pagination {
        margin-bottom: 0;
    }
    
    .page-link {
        border-radius: 8px;
        margin: 0 0.25rem;
        border: 1px solid var(--gray-color);
        color: var(--gray-color);
        padding: 0.5rem 0.75rem;
        transition: var(--transition);
    }
    
    .page-link:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: var(--light-color);
    }
    
    .page-item.active .page-link {
        background: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 992px) {
        .filter-inputs {
            grid-template-columns: 1fr 1fr;
        }
        
        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 15px;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .card-header h5 {
            font-size: 1.1rem;
        }
        
        .filter-inputs {
            grid-template-columns: 1fr;
        }
        
        .pagination-container {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        
        .table thead {
            display: none;
        }
        
        .table tbody tr {
            display: block;
            margin-bottom: 15px;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
        }
        
        .table tbody td {
            display: block;
            text-align: right;
            padding: 10px;
            border-bottom: 1px solid rgba(0, 126, 0, 0.05);
            position: relative;
        }
        
        .table tbody td:before {
            content: attr(data-label);
            position: absolute;
            left: 15px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            color: var(--gray-color);
        }
        
        .table tbody td:last-child {
            border-bottom: none;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation pour l'apparition des éléments
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
        
        // Add data labels for mobile view
        if (window.innerWidth < 768) {
            const headers = document.querySelectorAll('.table th');
            const cells = document.querySelectorAll('.table td');
            
            headers.forEach((header, index) => {
                const label = header.textContent;
                document.querySelectorAll('.table td:nth-child(' + (index + 1) + ')').forEach(cell => {
                    cell.setAttribute('data-label', label);
                });
            });
        }
    });
</script>
@endsection