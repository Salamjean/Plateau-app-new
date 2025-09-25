@extends('mairie.layouts.template')
@section('content')
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demandes d'extrait de mariage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1977cc;
            --secondary: #1977cc;
            --light-bg: #f8f9fa;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        
        .dashboard-header {
            background: linear-gradient(120deg, var(--secondary), #1977cc);
            color: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 126, 0, 0.15);
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .card-primary {
            border-bottom: 4px solid var(--primary);
        }
        
        .card-secondary {
            border-bottom: 4px solid var(--secondary);
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        
        .icon-primary {
            background-color: rgba(255, 136, 0, 0.15);
            color: var(--primary);
        }
        
        .icon-secondary {
            background-color: rgba(0, 126, 0, 0.15);
            color: var(--secondary);
        }
        
        .icon-light {
            background-color: rgba(108, 117, 125, 0.15);
            color: #6c757d;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .table-header {
            background: linear-gradient(120deg, var(--secondary), #1977cc);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            padding: 1rem 0.75rem;
            color: var(--secondary);
        }
        
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .badge-pending {
            background-color: #fff8e6;
            color: #e6b400;
        }
        
        .badge-approved {
            background-color: #e6f7ee;
            color: var(--secondary);
        }
        
        .badge-rejected {
            background-color: #fee6e6;
            color: #dc3545;
        }
        
        .badge-normal {
            background-color: #e6f0ff;
            color: #0d6efd;
        }
        
        .badge-urgent {
            background-color: #fff0e6;
            color: var(--primary);
        }
        
        .badge-delivered {
            background-color: #e6f7ee;
            color: var(--secondary);
        }
        
        .badge-not-delivered {
            background-color: #f8f9fa;
            color: #6c757d;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--secondary);
            border-color: var(--secondary);
        }
        
        .pagination .page-link {
            color: var(--secondary);
        }
        
        .pagination .page-link:hover {
            color: #1977cc;
        }
        
        .filter-section {
            background-color: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }
        
        .btn-filter {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
        }
        
        .btn-filter:hover {
            background-color: #689ac7;
            color: white;
        }
        
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }
        
        .status-pending {
            background-color: #ffc107;
        }
        
        .status-approved {
            background-color: var(--secondary);
        }
        
        .status-rejected {
            background-color: #dc3545;
        }
        
        .filter-form .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(255, 136, 0, 0.25);
        }
    </style>
</head>
<body>
    <div class="container-fluid px-4 py-4">
        <!-- En-tête -->
        <div class="dashboard-header p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h3 mb-2">Demandes d'extrait de mariage</h1>
                    <p class="mb-0">Gérez et suivez toutes les demandes d'extrait de mariages de votre commune</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-light text-dark p-2">
                        <i class="fas fa-city me-2"></i>{{ $mairie->name }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filter-section mb-4">
            <form method="GET" action="{{ route('mairie.request.wedding') }}" class="filter-form">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">État</label>
                        <select name="etat" class="form-select">
                            <option value="">Tous</option>
                            <option value="en attente" {{ request('etat') == 'en attente' ? 'selected' : '' }}>En attente</option>
                            <option value="réçu" {{ request('etat') == 'réçu' ? 'selected' : '' }}>Reçu</option>
                            <option value="terminé" {{ request('etat') == 'terminé' ? 'selected' : '' }}>Terminé</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Livraison</label>
                        <select name="livraison" class="form-select">
                            <option value="">Tous</option>
                            <option value="livré" {{ request('livraison') == 'livré' ? 'selected' : '' }}>Livré</option>
                            <option value="en cours" {{ request('livraison') == 'en cours' ? 'selected' : '' }}>En cours</option>
                            <option value="non livré" {{ request('livraison') == 'non livré' ? 'selected' : '' }}>Non livré</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-filter w-100">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                        @if(request()->has('etat') || request()->has('type') || request()->has('livraison'))
                        <a href="{{ route('mairie.request.wedding') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Tableau des demandes -->
        <div class="table-container mb-4">
            <div class="table-header">
                <h5 class="mb-0">Liste des demandes</h5>
                <span class="badge bg-light text-dark">
                    {{ $mariages->count() }} résultat(s)
                </span>
            </div>
            <div class="p-4">
                <div class="table-responsive">
                    <table class="table table-hover" id="demandesTable">
                        <thead>
                            <tr >
                                <th style="text-align: center">Référence</th>
                                <th style="text-align: center">Type</th>
                                <th style="text-align: center">Date de demande</th>
                                <th style="text-align: center">État</th>
                                <th style="text-align: center">Retrait</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mariages as $mariage)
                            <tr>
                                <td style="text-align: center">
                                    <span class="fw-bold" style="color: var(--primary); text-align: start" >
                                        {{ $mariage->reference }}
                                    </span>
                                </td>
                                <td style="text-align: center">
                                    @if($mariage->nomEpoux == null)
                                    <span class="badge badge-normal">
                                        <i class="fas fa-clock me-1"></i> Copie Simple
                                    </span>
                                    @else
                                    <span class="badge badge-urgent">
                                        <i class="fas fa-bolt me-1"></i> Copie Integrale
                                    </span>
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    <div class="text-muted">
                                        {{ \Carbon\Carbon::parse($mariage->created_at)->format('d/m/Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($mariage->created_at)->format('H:i') }}
                                    </small>
                                </td>
                                <td style="text-align: center">
                                    @if($mariage->etat == 'en attente')
                                    <span class="badge badge-pending">
                                        <span class="status-indicator status-pending"></span> En attente
                                    </span>
                                    @elseif($mariage->etat == 'réçu')
                                    <span class="badge badge-approved">
                                        <span class="status-indicator status-approved"></span> Reçu
                                    </span>
                                    @else
                                    <span class="badge badge-rejected">
                                        <span class="status-indicator status-rejected"></span> Terminé
                                    </span>
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    @if($mariage->statut_livraison == 'livré')
                                    <span class="badge badge-delivered">
                                        <i class="fas fa-check-circle me-1"></i> Livré
                                    </span>
                                    @elseif($mariage->statut_livraison == 'en cours')
                                    <span class="badge badge-info">
                                        <i class="fas fa-truck me-1"></i> En cours
                                    </span>
                                    @else
                                    <span class="badge badge-not-delivered">
                                        <i class="fas fa-clock me-1"></i> En attente
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucune demande trouvée</h5>
                                    <p class="text-muted">Aucune demande d'extrait de mariage n'a été enregistrée pour le moment.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($mariages->hasPages())
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Affichage de {{ $mariages->firstItem() }} à {{ $mariages->lastItem() }} sur {{ $mariages->total() }} résultats
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            {{ $mariages->withQueryString()->links() }}
                        </ul>
                    </nav>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script pour les interactions utilisateur
        document.addEventListener('DOMContentLoaded', function() {
            // Animation pour les cartes de statistiques
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.12)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.08)';
                });
            });
        });
    </script>
</body>
</html>
@endsection