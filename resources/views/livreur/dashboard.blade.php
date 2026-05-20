@extends('livreur.layouts.template')
@section('content')
<style>
    :root {
        --primary-color: #1f4083;
        --secondary-color: #ff9800;
        --success-color: #4caf50;
        --info-color: #00bcd4;
        --warning-color: #ffc107;
        --danger-color: #f44336;
        --light-bg: #f4f7f6;
        --card-shadow: 0 10px 30px 0 rgba(172, 168, 168, 0.1);
    }
    
    .dashboard-container {
        padding: 1.5rem;
    }
    
    .welcome-banner {
        background: linear-gradient(135deg, var(--primary-color) 0%, #3a5ba0 100%);
        color: white;
        border-radius: 15px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(31, 64, 131, 0.2);
    }
    
    .welcome-banner::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 250px;
        height: 250px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    
    .welcome-banner h2 {
        font-weight: 700;
        margin-bottom: 0.5rem;
        font-size: 1.8rem;
    }
    
    .welcome-banner p {
        opacity: 0.9;
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        margin-top: 10px;
    }

    .status-badge i {
        margin-right: 8px;
    }
    
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    
    .kpi-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px 0 rgba(172, 168, 168, 0.15);
    }
    
    .kpi-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1.2rem;
        font-size: 1.5rem;
        color: white;
    }
    
    .kpi-info h3 {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 5px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .kpi-info .value {
        font-size: 1.6rem;
        font-weight: 700;
        color: #2d3748;
    }

    .btn-toggle-status {
        background: white;
        color: var(--primary-color);
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .btn-toggle-status:hover {
        transform: scale(1.05);
        background: #f8f9fa;
    }

    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    .card {
        background: white;
        border-radius: 12px;
        border: none;
        box-shadow: var(--card-shadow);
        height: 100%;
    }

    .card-header {
        background: transparent;
        padding: 1.5rem;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h4 {
        margin: 0;
        font-weight: 600;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .activity-list {
        padding: 1rem;
    }

    .activity-item {
        display: flex;
        padding: 1rem;
        border-bottom: 1px solid #f8f9fa;
        transition: background 0.2s;
        border-radius: 8px;
    }

    .activity-item:hover {
        background: #fcfdfe;
    }

    .activity-icon-sm {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #f0f4ff;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .activity-detail {
        flex-grow: 1;
    }

    .activity-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }

    .activity-title {
        font-weight: 600;
        color: #2d3748;
        font-size: 0.95rem;
    }

    .activity-time {
        font-size: 0.8rem;
        color: #a0aec0;
    }

    .activity-desc {
        font-size: 0.85rem;
        color: #718096;
    }

    .badge-status {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 50px;
        font-weight: 600;
    }

    .badge-en-route { background: #fff8e1; color: #f57c00; }
    .badge-livre { background: #e8f5e9; color: #2e7d32; }

    .quick-actions-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 1.5rem;
    }

    .action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        border-radius: 12px;
        text-decoration: none;
        color: #4a5568;
        background: #f8fafc;
        transition: all 0.3s;
        font-weight: 500;
        border: 1px solid transparent;
    }

    .action-btn:hover {
        background: white;
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .action-btn i {
        font-size: 1.2rem;
        width: 30px;
        text-align: center;
    }
    
    @media (max-width: 992px) {
        .main-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-container">
    <!-- Bannière de bienvenue -->
    <div class="welcome-banner">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2>Bonjour, {{ $livreur->name }} ! ??</h2>
                <p>Prêt pour vos livraisons d'aujourd'hui ? Gérez vos colis et suivez vos performances en temps réel.</p>
                <div class="status-badge">
                    <i class="material-icons" style="font-size: 18px;">{{ $livreur->disponible ? 'check_circle' : 'do_not_disturb_on' }}</i>
                    Statut : {{ $livreur->disponible ? 'Disponible' : 'Indisponible' }}
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <form action="{{ route('livreur.toggleDisponibilite') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-toggle-status">
                        <i class="material-icons">{{ $livreur->disponible ? 'toggle_on' : 'toggle_off' }}</i>
                        Changer en {{ $livreur->disponible ? 'Indisponible' : 'Disponible' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Statistiques KPI -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #FF9800, #F57C00);">
                <i class="material-icons">local_shipping</i>
            </div>
            <div class="kpi-info">
                <h3>En cours</h3>
                <div class="value">{{ $stats['en_cours'] }}</div>
            </div>
        </div>
        
        <div class="kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #4CAF50, #2E7D32);">
                <i class="material-icons">task_alt</i>
            </div>
            <div class="kpi-info">
                <h3>Terminées</h3>
                <div class="value">{{ $stats['livrees'] }}</div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon" style="background: linear-gradient(135deg, #2196F3, #1976D2);">
                <i class="material-icons">timeline</i>
            </div>
            <div class="kpi-info">
                <h3>Total Livraisons</h3>
                <div class="value">{{ $stats['en_cours'] + $stats['livrees'] }}</div>
            </div>
        </div>
    </div>
    
    <div class="main-grid">
        <!-- Activités récentes -->
        <div class="card">
            <div class="card-header">
                <h4><i class="material-icons">history</i> Activités Récentes</h4>
                <a href="{{route('livreur.livraison')}}" class="btn btn-sm btn-link" style="color: var(--primary-color); font-weight: 600;">Voir tout</a>
            </div>
            <div class="activity-list">
                @forelse($activites as $activite)
                <div class="activity-item">
                    <div class="activity-icon-sm" style="background: {{ $activite['statut'] == 'livré' ? '#e8f5e9' : '#f0f4ff' }}; color: {{ $activite['statut'] == 'livré' ? '#2e7d32' : '#1f4083' }};">
                        <i class="material-icons">{{ $activite['statut'] == 'livré' ? 'done_all' : 'delivery_dining' }}</i>
                    </div>
                    <div class="activity-detail">
                        <div class="activity-header">
                            <span class="activity-title">{{ $activite['type'] }} #{{ $activite['livraison_code'] }}</span>
                            <span class="activity-time">{{ $activite['date']->diffForHumans() }}</span>
                        </div>
                        <div class="activity-desc">
                            Destinataire : <strong>{{ $activite['destinataire'] }}</strong>
                        </div>
                        <div class="mt-2 text-end">
                            <span class="badge-status {{ $activite['statut'] == 'livré' ? 'badge-livre' : 'badge-en-route' }}">
                                {{ $activite['statut'] == 'livré' ? 'Livré' : 'En cours' }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center p-5">
                    <i class="material-icons text-muted" style="font-size: 48px;">inbox</i>
                    <p class="text-muted mt-2">Aucune activité récente pour le moment.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="card">
            <div class="card-header">
                <h4><i class="material-icons">bolt</i> Actions Rapides</h4>
            </div>
            <div class="quick-actions-list">
                <a href="{{route('livreur.livraison')}}" class="action-btn">
                    <i class="material-icons" style="color: #FF9800;">inventory_2</i>
                    Colis à livrer
                </a>
                <a href="{{route('livreur.validated')}}" class="action-btn">
                    <i class="material-icons" style="color: #4CAF50;">qr_code_scanner</i>
                    Valider avec QR Code
                </a>
                <a href="{{route('livreur.livree')}}" class="action-btn">
                    <i class="material-icons" style="color: #2196F3;">history</i>
                    Historique complet
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
