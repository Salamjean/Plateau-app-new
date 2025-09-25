@extends('finance.layouts.template')
@section('content')
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechargement de Timbre</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1977cc;
            --primary-light: #1977cc;
            --primary-dark: #1977cc;
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
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .page-title {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
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
            margin: 10px 100px;
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
            margin-bottom: 5px;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 10px 20px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            text-align: center;
            border-left: 5px solid var(--primary-color);
        }
        
        .stat-card.secondary {
            border-left: 5px solid var(--secondary-color);
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
        
        .stat-card.secondary .stat-icon {
            color: var(--secondary-color);
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
        
        .form-group {
            margin-bottom: 25px;
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
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 136, 0, 0.1);
        }
        
        .input-group-text {
            background-color: var(--light-gray);
            border: none;
            color: var(--gray-color);
        }
        
        .form-control {
            border: none;
            padding: 15px 15px;
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
        
        .cost-estimator {
            background: rgba(255, 136, 0, 0.05);
            border-radius: var(--border-radius);
            padding: 15px;
            margin-top: 15px;
            border-left: 4px solid var(--primary-color);
        }
        
        .btn {
            border-radius: 20px;
            padding: 12px 25px;
            font-weight: 600;
            transition: var(--transition);
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 136, 0, 0.3);
        }
        
        .table {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .table th {
            background-color: rgba(0, 126, 0, 0.05);
            color: var(--secondary-color);
            font-weight: 600;
            border: none;
            padding: 15px;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
            border-top: 1px solid rgba(0, 126, 0, 0.05);
        }
        
        .badge-success {
            background-color: rgba(0, 126, 0, 0.1);
            color: var(--secondary-color);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
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
        }
        
        .alert-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            animation: slideInRight 0.3s ease;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .card{
                margin: 10px;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .card-header h5 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <!-- Notifications -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show alert-notification" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>{{ session('error') }}</strong>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show alert-notification" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <strong>{{ session('success') }}</strong>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <h1 class="page-title">
        <i class="fas fa-stamp me-2"></i>Rechargement de Timbre
    </h1>
    <div class="col-lg-4">
            <!-- Cartes de statistiques -->
            <div class="stats-grid">
                <!-- Solde de timbres -->
                <div class="stat-card fade-in">
                    <div class="stat-icon">
                        <i class="fas fa-stamp"></i>
                    </div>
                    <div class="stat-number">{{ number_format($solde_timbres, 0, ',', ' ') }}</div>
                    <div class="stat-label">Solde actuel de timbres</div>
                    <span class="stat-badge">Disponibles</span>
                </div>
                
                <!-- Valeur du stock -->
                <div class="stat-card secondary fade-in">
                    <div class="stat-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-number">{{ number_format($solde_timbres * 500, 0, ',', ' ') }} FCFA</div>
                    <div class="stat-label">Valeur totale du stock</div>
                    <span class="stat-badge">500 FCFA/unité</span>
                </div>
                
                <!-- Recharges ce mois -->
                <div class="stat-card fade-in">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-number">
                        {{ number_format($dernieres_recharges->where('created_at', '>=', now()->startOfMonth())->sum('nombre_timbre'), 0, ',', ' ') }}
                    </div>
                    <div class="stat-label">Recharges ce mois</div>
                    <span class="stat-badge">Total mensuel</span>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-8">
            <div class="card fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-plus-circle me-2"></i>Ajouter des timbres</h5>
                </div>
                
                <div class="card-body">
                    <form id="rechargeForm" action="{{ route('finance.timbre.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nombre_timbre" class="form-label">
                                <i class="fas fa-stamp"></i>Nombre de timbres à ajouter
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-stamp"></i></span>
                                <input type="number" 
                                       class="form-control" 
                                       id="nombre_timbre" 
                                       name="nombre_timbre" 
                                       min="1" 
                                       step="1"
                                       required
                                       placeholder="Nombre de timbres"
                                       value="{{ old('nombre_timbre') }}">
                            </div>
                            <div class="form-text">Entrez un nombre positif de timbres à ajouter</div>
                            @error('nombre_timbre')
                                <div class="text-danger text-xs mt-1">{{ $message }}</div>
                            @enderror
                            
                            <!-- Cost Estimator -->
                            <div class="cost-estimator">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-sm">Coût estimé:</span>
                                    <span class="fw-bold" id="cost-estimation">0 FCFA</span>
                                </div>
                                <div class="progress mt-2" style="height: 5px;">
                                    <div class="progress-bar bg-primary" role="progressbar" id="cost-progress" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="text-xs text-muted mt-1">Prix unitaire: 500 FCFA</div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg text-white">
                                <i class="fas fa-plus-circle me-2"></i> Ajouter les timbres
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card fade-in">
                <div class="card-header">
                    <h5><i class="fas fa-history me-2"></i>Dernières recharges</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="text-align: center">Date</th>
                                    <th style="text-align: center">Quantité</th>
                                    <th style="text-align: center">Coût</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dernieres_recharges as $recharge)
                                <tr>
                                    <td style="text-align:center">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $recharge->created_at->format('d M Y') }}</span>
                                            <small class="text-muted">{{ $recharge->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td style="text-align:center">
                                        <span class="badge-success">+ {{ number_format($recharge->nombre_timbre, 0, ',', ' ') }}</span>
                                    </td>
                                    <td style="text-align:center">
                                        <span class="fw-bold">{{ number_format($recharge->nombre_timbre * 500, 0, ',', ' ') }} FCFA</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5>Aucune recharge effectuée</h5>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('rechargeForm');
        const input = document.getElementById('nombre_timbre');
        const costEstimation = document.getElementById('cost-estimation');
        const costProgress = document.getElementById('cost-progress');
        
        // Afficher les messages de session avec SweetAlert2
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès !',
            text: '{{ session('success') }}',
            confirmButtonColor: '#1977cc',
            confirmButtonText: 'OK',
            background: 'white'
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur !',
            text: '{{ session('error') }}',
            confirmButtonColor: '#1977cc',
            confirmButtonText: 'OK',
            background: 'white'
        });
        @endif

        // Mise à jour de l'estimation du coût en temps réel
        input.addEventListener('input', function() {
            const value = parseInt(this.value) || 0;
            const cost = value * 500;
            
            // Mettre à jour l'estimation du coût
            costEstimation.textContent = new Intl.NumberFormat('fr-FR').format(cost) + ' FCFA';
            
            // Mettre à jour la barre de progression (limite à 10000 timbres pour 100%)
            const progress = Math.min((value / 10000) * 100, 100);
            costProgress.style.width = `${progress}%`;
            costProgress.setAttribute('aria-valuenow', progress);
            
            // Validation
            if (value < 1) {
                this.setCustomValidity('Veuillez entrer un nombre valide de timbres (min: 1)');
                this.parentElement.style.borderColor = '#dc3545';
            } else {
                this.setCustomValidity('');
                this.parentElement.style.borderColor = '#1977cc';
            }
        });
        
        // Confirmation de soumission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nombreTimbre = parseInt(input.value);
            
            if (nombreTimbre < 1) {
                showNotification('Veuillez entrer un nombre valide de timbres (min: 1)', 'error');
                return;
            }
            
            const cost = nombreTimbre * 500;
            
            // Confirmation stylisée
            Swal.fire({
                title: 'Confirmer la recharge',
                html: `Êtes-vous sûr de vouloir ajouter <b>${new Intl.NumberFormat('fr-FR').format(nombreTimbre)}</b> timbre(s) pour un total de <b>${new Intl.NumberFormat('fr-FR').format(cost)} FCFA</b> ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1977cc',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, confirmer',
                cancelButtonText: 'Annuler',
                background: 'white'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Animation de chargement
                    Swal.fire({
                        title: 'Ajout en cours...',
                        text: 'Veuillez patienter',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        },
                        background: 'white'
                    });
                    
                    // Soumission du formulaire
                    form.submit();
                }
            });
        });
        
        // Fonction pour afficher les notifications
        function showNotification(message, type) {
            Swal.fire({
                icon: type,
                title: 'Attention',
                text: message,
                confirmButtonColor: type === 'error' ? '#1977cc' : '#1977cc',
                confirmButtonText: 'OK',
                background: 'white'
            });
        }
        
        // Auto-fermeture des alertes après 5 secondes
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
</body>
</html>
@endsection