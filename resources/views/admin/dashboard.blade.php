@extends('admin.layouts.template')
@section('content')
<div class="container-fluid">
    <!-- Section des statistiques principales -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card" style="background-color: #ffffff; border-left: 4px solid #6777ef;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon">
                            <i class="fas fa-baby fa-2x" style="color: #6777ef;"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="card-title mb-0">{{ $naissance }}</h5>
                            <span class="text-muted">Naissances</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card" style="background-color: #ffffff; border-left: 4px solid #6777ef;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon">
                            <i class="fas fa-heart fa-2x" style="color: #6777ef;"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="card-title mb-0">{{ $mariage }}</h5>
                            <span class="text-muted">Mariages</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card" style="background-color: #ffffff; border-left: 4px solid #6777ef;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon">
                            <i class="fas fa-cross fa-2x" style="color: #6777ef;"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="card-title mb-0">{{ $deces }}</h5>
                            <span class="text-muted">Décès</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card" style="background-color: #ffffff; border-left: 4px solid #6777ef;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon">
                            <i class="fas fa-users fa-2x" style="color: #6777ef;"></i>
                        </div>
                        <div class="ml-3">
                            <h5 class="card-title mb-0">{{ $utilisateurs }}</h5>
                            <span class="text-muted">Utilisateurs inscrits</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section des statistiques financières -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card" style="background-color: #ffffff; border-top: 3px solid #6777ef;">
            <div class="card-body text-center">
                <h5 class="card-title">Solde Actuel</h5>
                <h3 class="text-primary" style="color: #6777ef !important;">{{ number_format($soldeActuel, 0, ',', ' ') }} FCFA</h3>
                <p class="text-muted">Total des fonds disponibles</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card" style="background-color: #ffffff; border-top: 3px solid #6777ef;">
            <div class="card-body text-center">
                <h5 class="card-title">Timbres sortis</h5>
                <h3 class="text-danger">{{ number_format($timbresSortis, 0, ',', ' ') }}</h3>
                <p class="text-muted">Nombre total de timbres délivrés</p>
            </div>
        </div>
    </div>
</div>

<!-- Nouvelle ligne pour le solde total des livraisons -->
<div class="row mt-3">
    
    <div class="col-md-6">
        <div class="card" style="background-color: #ffffff; border-top: 3px solid #6777ef;">
            <div class="card-body text-center">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Porte-feuille Livraisons</h5>
                    <form action="" method="GET" class="form-inline">
                        <select name="month" class="form-control form-control-sm mr-1">
                            @php
                                $moisList = ['01'=>'Janvier','02'=>'Février','03'=>'Mars','04'=>'Avril','05'=>'Mai','06'=>'Juin','07'=>'Juillet','08'=>'Août','09'=>'Septembre','10'=>'Octobre','11'=>'Novembre','12'=>'Décembre'];
                            @endphp
                            @foreach($moisList as $k => $v)
                                <option value="{{ $k }}" {{ str_pad($month, 2, '0', STR_PAD_LEFT) == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                        <select name="year" class="form-control form-control-sm mr-1">
                            @for($y=date('Y'); $y>=2020; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary" style="background-color: #6777ef;">Filtrer</button>
                    </form>
                </div>
                <h3 class="text-info">{{ number_format($soldeMoisEnCours, 0, ',', ' ') }} FCFA</h3>
                <p class="text-muted">Solde du mois sélectionné</p>
                <!-- Bouton pour télécharger les rapports -->
                <button type="button" class="btn btn-primary mt-2" style="background-color: #6777ef; border-color: #6777ef;" data-toggle="modal" data-target="#rapportModal">
                    <i class="fas fa-download mr-2"></i>Télécharger Rapport
                </button>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card" style="background-color: #ffffff; border-top: 3px solid #28a745;">
            <div class="card-body text-center">
                <h5 class="card-title">Solde Total des Livraisons</h5>
                <h3 class="text-success">{{ number_format($soldeDisponible, 0, ',', ' ') }} FCFA</h3>
                <p class="text-muted">Montant total encaissé sur toutes les livraisons</p>
                 <!-- Bouton pour télécharger les rapports -->
                <button type="button" class="btn btn-primary mt-2" style="background-color: #6777ef; border-color: #6777ef;" data-toggle="modal" data-target="#rapportModal">
                    <i class="fas fa-download mr-2"></i>Télécharger Rapport
                </button>
            </div>
        </div>
    </div>
</div>
    <!-- Modal pour le téléchargement des rapports -->
    <div class="modal fade" id="rapportModal" tabindex="-1" role="dialog" aria-labelledby="rapportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #6777ef; color: white;">
                    <h5 class="modal-title" id="rapportModalLabel">Télécharger Rapport des Livraisons</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white;">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="rapportForm" action="{{ route('admin.rapports.download') }}" method="GET">
                        <div class="form-group">
                            <label for="annee">Année</label>
                            <select class="form-control" id="annee" name="annee" required>
                                <option value="">Sélectionner l'année</option>
                                @for($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="mois">Mois</label>
                            <select class="form-control" id="mois" name="mois" required>
                                <option value="">Sélectionner le mois</option>
                                <option value="01">Janvier</option>
                                <option value="02">Février</option>
                                <option value="03">Mars</option>
                                <option value="04">Avril</option>
                                <option value="05">Mai</option>
                                <option value="06">Juin</option>
                                <option value="07">Juillet</option>
                                <option value="08">Août</option>
                                <option value="09">Septembre</option>
                                <option value="10">Octobre</option>
                                <option value="11">Novembre</option>
                                <option value="12">Décembre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type_rapport">Type de Rapport</label>
                            <select class="form-control" id="type_rapport" name="type_rapport" required>
                                <option value="excel">Excel</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" style="background-color: #6777ef; border-color: #6777ef;" onclick="document.getElementById('rapportForm').submit();">
                        <i class="fas fa-download mr-2"></i>Télécharger
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Section des statistiques de livraison -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card" style="background-color: #ffffff;">
                <div class="card-header" style="background-color: #6777ef; color: white;">
                    <h5 class="mb-0">Statistiques de Livraison</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3>{{ $total }}</h3>
                                <p class="mb-0">Total des demandes</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3>{{ $stats['en_attente'] ?? 0 }}</h3>
                                <p class="mb-0">En attente</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3>{{ $stats['en_cours'] ?? 0 }}</h3>
                                <p class="mb-0">En cours</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3">
                                <h3>{{ $stats['livre'] ?? 0 }}</h3>
                                <p class="mb-0">Livrées</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section des activités récentes -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card" style="background-color: #ffffff;">
                <div class="card-header" style="background-color: #6777ef; color: white;">
                    <h5 class="mb-0">Activités Récentes</h5>
                </div>
                <div class="card-body">
                    @if($activites && $activites->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Utilisateur</th>
                                        <th>Statut à la mairie</th>
                                        <th>Statut de livraison</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activites->take(5) as $activite)
                                        <tr>
                                            <td class="text-center">{{ $activite->type }}</td>
                                            <td class="text-center">{{ $activite->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">{{ $activite->user ? ($activite->user->name.' '.$activite->user->prenom) : 'N/A' }}</td>
                                            <td class="text-center">
                                                @if(isset($activite->etat ))
                                                    <span class="badge 
                                                        @if($activite->etat == 'terminé') badge-success
                                                        @elseif($activite->etat == 'réçu') badge-warning
                                                        @elseif($activite->etat == 'en attente') badge-info
                                                        @else badge-secondary @endif">
                                                        {{ $activite->etat  }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">En attente</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(!empty($activite->statut_livraison))
                                                    <span class="badge 
                                                        @if($activite->statut_livraison == 'livré') badge-success
                                                        @elseif($activite->statut_livraison == 'en cours') badge-warning
                                                        @elseif($activite->statut_livraison == 'en attente') badge-info
                                                        @else badge-secondary @endif">
                                                        {{ $activite->statut_livraison  }}
                                                    </span>
                                                @else
                                                    @if(isset($activite->choix_option) && strtolower(trim($activite->choix_option)) === 'retrait sur place')
                                                        <span class="badge badge-secondary">En attente de retrait</span>
                                                    @else
                                                        <span class="badge badge-secondary">En attente de livraison</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted">Aucune activité récente</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(103, 119, 239, 0.1);
    }
    
    .card-header {
        border-radius: 8px 8px 0 0 !important;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
    
    .badge-success { background-color: #28a745; color: white; }
    .badge-warning { background-color: #ffc107; color: black; }
    .badge-info { background-color: #17a2b8; color: white; }
    .badge-secondary { background-color: #6c757d; color: white; }
    
    .btn-primary {
        background-color: #6777ef;
        border-color: #6777ef;
    }
    
    .btn-primary:hover {
        background-color: #5a6ae0;
        border-color: #5a6ae0;
    }
</style>

<!-- Inclure Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<!-- Inclure Bootstrap JS pour le modal -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Script pour pré-remplir le mois actuel
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const currentMonth = (now.getMonth() + 1).toString().padStart(2, '0');
        document.getElementById('mois').value = currentMonth;
    });

    // Actualisation automatique chaque 15 secondes
    setTimeout(function() {
        window.location.reload();
    }, 15000);
</script>
@endsection
