@extends('comptable.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div class="dashboard-container">
    <h1 class="page-title">
        <i class="fas fa-cash-register me-2"></i>Vente de Timbres
    </h1>

    <div class="two-column-layout">
        <!-- Colonne de gauche : Formulaire de vente -->
        <div class="left-column">
            <div class="card fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-stamp me-2"></i>Vendre des timbres</h5>
                </div>
                
                <div class="card-body">
                    <div class="form-section">
                        <form id="venteForm" action="{{ route('comptable.timbre.storeVente') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="nombre_timbre" class="form-label">
                                    <i class="fas fa-stamp me-1"></i>Nombre de timbres à vendre
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-stamp" style="color:#ff8800"></i></span>
                                    <input type="number" 
                                           class="form-control rounded-end @error('nombre_timbre') is-invalid @enderror" 
                                           id="nombre_timbre" 
                                           name="nombre_timbre" 
                                           min="1" 
                                           step="1"
                                           required 
                                           placeholder="Entrez le nombre de timbres"
                                           value="{{ old('nombre_timbre') }}">
                                </div>
                                <div class="form-text">1 timbre = 500 FCFA</div>
                                @error('nombre_timbre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Affichage du montant calculé -->
                            <div class="amount-card">
                                <div class="amount-label">Montant total:</div>
                                <div class="amount-value" id="montantTotal">0 FCFA</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary full-width">
                                <i class="fas fa-cash-register me-2"></i> Valider la vente
                            </button>
                        </form>
                    </div>
                    
                    <!-- Statistiques de vente -->
                    <div class="stats-grid mt-4">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ number_format($ventes_aujourdhui, 0, ',', ' ') }}</div>
                                <div class="stat-label">Ventes aujourd'hui</div>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-value">{{ number_format($ventes_semaine, 0, ',', ' ') }}</div>
                                <div class="stat-label">Ventes cette semaine</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Objectif du mois -->
                    <div class="progress-card mt-4">
                        <h6><i class="fas fa-bullseye me-2"></i>Objectif du mois</h6>
                        <div class="progress-container">
                            <div class="progress-info">
                                <span id="progressPercent">0%</span>
                                <span id="progressText">0/0 timbres</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonne de droite : Historique et stock -->
        <div class="right-column">
            <!-- Carte de stock -->
            <div class="card stats-card primary fade-in mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-warehouse me-2"></i>Stock de timbres</h5>
                </div>
                <div class="card-body text-center">
                    <div class="stock-value">{{ number_format($solde_timbres, 0, ',', ' ') }}</div>
                    <div class="stock-label">Timbre(s) disponible(s)</div>
                    <div class="stock-note">Dernière mise à jour: {{ now()->format('d/m/Y H:i') }}</div>
                </div>
            </div>
            
            <!-- Dernières ventes -->
            <div class="card fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-history me-2"></i>Dernières ventes</h5>
                </div>
                <div class="card-body">
                    <div class="sales-list">
                        @forelse($dernieres_ventes as $vente)
                        <div class="sale-item">
                            <div class="sale-info">
                                <div class="sale-date">
                                    <div class="date">{{ $vente->created_at->format('d M') }}</div>
                                    <div class="time">{{ $vente->created_at->format('H:i') }}</div>
                                </div>
                                <div class="sale-details">
                                    <div class="quantity">{{ number_format(abs($vente->nombre_timbre), 0, ',', ' ') }} timbres</div>
                                    <div class="amount">{{ number_format(abs($vente->nombre_timbre) * 500, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                            <div class="sale-status completed">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        @empty
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Aucune vente effectuée</p>
                        </div>
                        @endforelse
                    </div>
                    
                    @if($dernieres_ventes->count() > 0)
                    <div class="text-center mt-3">
                        <a href="{{route('comptable.timbre.history')}}" class="btn btn-outline view-all-btn">
                            <i class="fas fa-list me-2"></i>Voir tout l'historique
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclure Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --primary-color: #1977cc;
        --primary-light: #1977cc;
        --primary-dark: #1977cc;
        --secondary-color: #ff8800;
        --secondary-light: #ffaa40;
        --secondary-dark: #cc6d00;
        --light-color: #ffffff;
        --dark-color: #212529;
        --gray-color: #6c757d;
        --light-gray: #f8f9fa;
        --border-radius: 12px;
        --box-shadow: 0 8px 20px rgba(0, 126, 0, 0.1);
        --transition: all 0.3s ease;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        font-family: 'Poppins', sans-serif;
        color: var(--dark-color);
    }
    
    .dashboard-container {
        padding: 30px;
        max-width: 1500px;
        margin: 0 auto;
    }
    
    .page-title {
        color: var(--primary-color);
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
        background: var(--secondary-color);
        border-radius: 2px;
    }
    
    .two-column-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
    }
    
    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        overflow: hidden;
        background-color: var(--light-color);
        margin-bottom: 0;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0, 126, 0, 0.15);
    }
    
    .card-header {
        background-color: var(--primary-color);
        color: white;
        padding: 18px 25px;
        font-weight: 600;
        border-bottom: none;
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
    
    .form-section {
        background: var(--light-gray);
        border-radius: var(--border-radius);
        padding: 20px;
    }
    
    .form-group {
        margin-bottom: 20px;
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
    
    .input-group {
        border-radius: var(--border-radius);
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: var(--transition);
    }
    
    .input-group:focus-within {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(255, 136, 0, 0.1);
    }
    
    .input-group-text {
        background-color: var(--light-gray);
        border: none;
        color: var(--secondary-color);
    }
    
    .form-control {
        border: none;
        padding: 14px 15px;
        font-weight: 500;
        box-shadow: none;
    }
    
    .form-control:focus {
        box-shadow: none;
    }
    
    .form-text {
        font-size: 0.85rem;
        color: var(--gray-color);
        margin-top: 5px;
    }
    
    .amount-card {
        background: rgba(255, 136, 0, 0.05);
        border-radius: var(--border-radius);
        padding: 15px;
        margin-bottom: 20px;
        border-left: 4px solid var(--secondary-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .amount-label {
        font-weight: 600;
        color: var(--dark-color);
    }
    
    .amount-value {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--secondary-color);
    }
    
    .btn {
        border-radius: 20px;
        padding: 14px 25px;
        font-weight: 600;
        transition: var(--transition);
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .btn-primary {
        background-color: var(--secondary-color);
        color: var(--light-color);
        box-shadow: 0 4px 10px rgba(255, 136, 0, 0.25);
    }
    
    .btn-primary:hover {
        background-color: var(--secondary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(255, 136, 0, 0.35);
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
    
    .full-width {
        width: 100%;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
    
    .stat-card {
        background: var(--light-color);
        border-radius: var(--border-radius);
        padding: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        transition: var(--transition);
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(0, 126, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.2rem;
        color: var(--primary-color);
    }
    
    .stat-content {
        flex: 1;
    }
    
    .stat-value {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--primary-color);
        line-height: 1;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 0.8rem;
        color: var(--gray-color);
    }
    
    .progress-card {
        background: var(--light-color);
        border-radius: var(--border-radius);
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .progress-card h6 {
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--dark-color);
        display: flex;
        align-items: center;
    }
    
    .progress-container {
        margin-top: 15px;
    }
    
    .progress-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 0.875rem;
    }
    
    .progress-bar {
        height: 8px;
        background: var(--light-gray);
        border-radius: 4px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border-radius: 4px;
        transition: width 0.5s ease;
    }
    
    .stats-card.primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
    }
    
    .stats-card.primary .card-header {
        background: transparent;
    }
    
    .stats-card.primary .card-header h5 {
        color: white;
    }
    
    .stock-value {
        font-weight: 700;
        font-size: 3rem;
        color: white;
        margin-bottom: 5px;
    }
    
    .stock-label {
        font-size: 1rem;
        opacity: 0.9;
        margin-bottom: 10px;
    }
    
    .stock-note {
        font-size: 0.8rem;
        opacity: 0.7;
    }
    
    .sales-list {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .sale-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid rgba(0, 126, 0, 0.05);
        transition: var(--transition);
    }
    
    .sale-item:hover {
        background: rgba(0, 126, 0, 0.02);
    }
    
    .sale-item:last-child {
        border-bottom: none;
    }
    
    .sale-info {
        display: flex;
        align-items: center;
    }
    
    .sale-date {
        margin-right: 15px;
        text-align: center;
        min-width: 50px;
    }
    
    .date {
        font-weight: 600;
        color: var(--dark-color);
    }
    
    .time {
        font-size: 0.75rem;
        color: var(--gray-color);
    }
    
    .sale-details {
        display: flex;
        flex-direction: column;
    }
    
    .quantity {
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .amount {
        font-size: 0.875rem;
        color: var(--secondary-color);
    }
    
    .sale-status {
        color: var(--primary-color);
    }
    
    .empty-state {
        padding: 30px;
        text-align: center;
        color: var(--gray-color);
    }
    
    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 10px;
        opacity: 0.5;
    }
    
    .view-all-btn {
        font-size: 0.9rem;
        padding: 8px 16px;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @media (max-width: 992px) {
        .two-column-layout {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 768px) {
        .dashboard-container {
            padding: 15px;
        }
        
        .sale-info {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .sale-date {
            margin-right: 0;
            margin-bottom: 10px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('venteForm');
        const input = document.getElementById('nombre_timbre');
        const montantTotal = document.getElementById('montantTotal');
        const soldeActuel = {{ $solde_timbres }};
        const prixUnitaire = 500;
        
        // Fonction pour calculer et afficher le montant
        function calculerMontant() {
            const quantite = parseInt(input.value) || 0;
            const total = quantite * prixUnitaire;
            montantTotal.textContent = new Intl.NumberFormat('fr-FR').format(total) + ' FCFA';
        }
        
        // Calcul initial
        calculerMontant();
        
        // Écouter les changements sur l'input
        input.addEventListener('input', function() {
            calculerMontant();
            
            const valeur = parseInt(this.value) || 0;
            if (valeur < 1) {
                this.setCustomValidity('Veuillez entrer un nombre valide de timbres (min: 1)');
            } else if (valeur > soldeActuel) {
                this.setCustomValidity('Stock insuffisant. Maximum: ' + soldeActuel);
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Mettre à jour la barre de progression
        const ventesMois = {{ $ventes_mois }};
        const objectifMois = 2000; // Valeur par défaut
        const progressPercent = Math.min(100, Math.round((ventesMois / objectifMois) * 100));
        document.getElementById('progressPercent').textContent = progressPercent + '%';
        document.getElementById('progressText').textContent = ventesMois + '/' + objectifMois + ' timbres';
        document.getElementById('progressFill').style.width = progressPercent + '%';
        
        // Afficher les messages de session avec SweetAlert2
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Vente réussie !',
            text: '{{ session('success') }}',
            confirmButtonColor: '#ff8800',
            confirmButtonText: 'OK',
            background: '#ffffff'
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur !',
            text: '{{ session('error') }}',
            confirmButtonColor: '#ff8800',
            confirmButtonText: 'OK',
            background: '#ffffff'
        });
        @endif
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nombreTimbre = parseInt(input.value) || 0;
            const montant = nombreTimbre * prixUnitaire;
            
            if (nombreTimbre < 1) {
                showNotification('Veuillez entrer un nombre valide de timbres (min: 1)', 'danger');
                return;
            }
            
            if (nombreTimbre > soldeActuel) {
                showNotification('Stock insuffisant. Maximum: ' + soldeActuel, 'danger');
                return;
            }
            
            // Confirmation stylisée avec le montant
            Swal.fire({
                title: 'Confirmer la vente',
                html: `Êtes-vous sûr de vouloir vendre <b>${nombreTimbre}</b> timbre(s) ?<br>
                      <strong>Montant: ${new Intl.NumberFormat('fr-FR').format(montant)} FCFA</strong><br>
                      <small>Stock après vente: ${soldeActuel - nombreTimbre} timbres</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ff8800',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, vendre',
                cancelButtonText: 'Annuler',
                background: '#ffffff'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Animation de chargement
                    Swal.fire({
                        title: 'Traitement en cours...',
                        text: 'Veuillez patienter',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        },
                        background: '#ffffff'
                    });
                    
                    // Soumission du formulaire
                    form.submit();
                }
            });
        });
        
        function showNotification(message, type) {
            Swal.fire({
                icon: type === 'danger' ? 'error' : 'warning',
                title: 'Attention',
                text: message,
                confirmButtonColor: '#ff8800',
                confirmButtonText: 'OK',
                background: '#ffffff'
            });
        }
    });
</script>
@endsection