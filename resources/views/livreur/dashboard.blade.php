@extends('livreur.layouts.template')
@section('content')
<style>
    :root {
        --primary: #1977cc;
        --indisponible: red;
        --primary-light: rgba(6, 99, 78, 0.1);
        --secondary: #ea8c51;
        --secondary-light: rgba(249, 207, 3, 0.1);
        --success: #28a745;
        --success-light: rgba(40, 167, 69, 0.1);
        --white: #ffffff;
        --light: #f8f9fa;
        --dark: #343a40;
        --gray: #6c757d;
    }
    
    .dashboard-container {
        padding: 2rem;
        max-width: 90%;
        margin: 0 auto;
    }
    
    .welcome-card {
        background: linear-gradient(135deg, var(--primary) 0%, #1977cc 100%);
        color: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }
    
    .welcome-card::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-card.primary {
        border-left: 4px solid var(--primary);
    }
    
    .stat-card.secondary {
        border-left: 4px solid var(--secondary);
    }
    
    .stat-card.success {
        border-left: 4px solid var(--success);
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }
    
    .stat-card.primary .stat-value {
        color: var(--primary);
    }
    
    .stat-card.secondary .stat-value {
        color: var(--secondary);
    }
    
    .stat-card.success .stat-value {
        color: var(--success);
    }
    
    .stat-label {
        color: var(--gray);
        font-size: 0.9rem;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }
    
    .stat-card.primary .stat-icon {
        background-color: var(--primary-light);
        color: var(--primary);
    }
    
    .stat-card.secondary .stat-icon {
        background-color: var(--secondary-light);
        color: var(--secondary);
    }
    
    .stat-card.success .stat-icon {
        background-color: var(--success-light);
        color: var(--success);
    }
    
    .recent-activity {
        background: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: var(--primary);
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        margin-right: 0.75rem;
    }
    
    .activity-item {
        display: flex;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .activity-content {
        flex-grow: 1;
    }
    
    .activity-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .activity-time {
        color: var(--gray);
        font-size: 0.85rem;
    }
    
    .activity-statut {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.25rem;
    }
    
    .statut-en-cours {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .statut-livre {
        background-color: #d4edda;
        color: #155724;
    }
    
    .activity-montant {
        font-weight: 600;
        color: var(--success);
        margin-top: 0.25rem;
    }
    
    .btn-action {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 5px
    }
    
    .btn-primary {
        background-color: var(--primary);
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #1262a8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(6, 99, 78, 0.2);
    }
    
    .btn-secondary {
        background-color: var(--secondary);
        color:white;
    }
    
    .btn-secondary:hover {
        background-color: #bb6129;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(249, 207, 3, 0.3);
    }
    
    .btn-action i {
        margin-right: 0.5rem;
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .disponible-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .disponible-status .badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
    }

    .disponible-status .badge-disponible {
        background-color: var(--primary-light);
        color: var(--primary);
    }

    .disponible-status .badge-indisponible {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-container">
    <!-- Carte de bienvenue -->
    <div class="welcome-card">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2>Bonjour, {{ $livreur->name }}!</h2>
                <p>Bienvenue sur votre tableau de bord livreur</p>
            </div>
            <form action="{{ route('livreur.toggleDisponibilite') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-action {{ $livreur->disponible ? 'btn-secondary' : 'btn-secondary' }}" 
                        style="margin-left: auto;">
                    <i class="fas {{ $livreur->disponible ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                    {{ $livreur->disponible ? 'Disponible' : 'Non disponible' }}
                </button>
            </form>
        </div>
        <div style="margin-top: 1.5rem;">
            <a href="{{route('livreur.livraison')}}" class="btn-action btn-secondary">
                <i class="fas fa-tasks"></i> Voir les colis à livrer
            </a>
        </div>
    </div>
    
    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-truck fa-lg"></i>
            </div>
            <div class="stat-value">{{ $stats['en_cours'] }}</div>
            <div class="stat-label">Livraisons en cours</div>
        </div>
        
        <div class="stat-card secondary">
            <div class="stat-icon">
                <i class="fas fa-check-circle fa-lg"></i>
            </div>
            <div class="stat-value">{{ $stats['livrees'] }}</div>
            <div class="stat-label">Livraisons terminées</div>
        </div>
        
        {{-- <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave fa-lg"></i> 
            </div>
            <div class="stat-title">Solde disponible</div>
            <div class="stat-value">{{ number_format($stats['montant_total'], 0, ',', ' ') }} FCFA</div>
            <div class="stat-label">Montant total des livraisons</div>
        </div> --}}
    </div>
    
    <!-- Actions rapides -->
    <div class="quick-actions">
        <a href="{{route('livreur.livraison')}}" class="btn-action btn-primary">
            <i class="fas fa-clipboard-list"></i> Colis attribuées
        </a>
        <a href="{{route('livreur.livree')}}" class="btn-action btn-primary">
            <i class="fas fa-check-circle"></i> Colis livrées
        </a>
        <a href="{{route('livreur.validated')}}" class="btn-action btn-secondary">
            <i class="fas fa-qrcode"></i> Valider une livraison
        </a>
    </div>
    
    <!-- Activité récente -->
    <div class="recent-activity">
        <h3 class="section-title">
            <i class="fas fa-history"></i> Activité récente
        </h3>
        
        <div class="activity-list">
            @forelse($activites as $activite)
            <div class="activity-item">
                <div class="activity-icon">
                    @if($activite['statut'] == 'livré')
                        <i class="fas fa-check"></i>
                    @else
                        <i class="fas fa-truck"></i>
                    @endif
                </div>
                <div class="activity-content">
                    <div class="activity-title">
                        {{ $activite['type'] }} - {{ $activite['livraison_code'] }}
                        <span class="activity-statut statut-{{ $activite['statut'] == 'livré' ? 'livre' : 'en-cours' }}">
                            {{ $activite['statut'] == 'livré' ? 'Livré' : 'En cours' }}
                        </span>
                    </div>
                    <div class="activity-time">
                        {{ $activite['date']->diffForHumans() }} • {{ $activite['destinataire'] }}
                    </div>
                    {{-- @if($activite['statut'] == 'livré' && $activite['montant'])
                    <div class="activity-montant">
                        Montant de livraison: {{ number_format($activite['montant'], 0, ',', ' ') }} FCFA
                    </div>
                    @endif --}}
                </div>
            </div>
            @empty
            <div class="activity-item">
                <div class="activity-content">
                    <div class="activity-title">Aucune activité récente</div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
@endsection