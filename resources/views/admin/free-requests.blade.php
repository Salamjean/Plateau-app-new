@extends('admin.layouts.template')

@section('content')
<div class="container-fluid" id="container-wrapper">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Paramètres des Demandes Gratuites</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Demandes Gratuites</li>
        </ol>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Configuration Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow" style="border: 2px solid {{ $freeRequestsMode && $freeRequestsMode->value ? '#28a745' : '#e9ecef' }};">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #28a745, #20c997);">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-gift mr-2"></i>Mode Test - Demandes Gratuites
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <strong>Phase de test :</strong> Lorsqu'il est activé, chaque nouvel utilisateur bénéficie de 
                        <strong>2 demandes gratuites</strong> (sans payer les timbres). Les timbres de ces 2 demandes 
                        seront déduits directement du <strong>Solde Disponible (KKS-TECHNOLOGIES)</strong>.
                    </p>
                    
                    <div class="alert {{ $freeRequestsMode && $freeRequestsMode->value ? 'alert-success' : 'alert-secondary' }} py-2 mb-3">
                        <div class="d-flex align-items-center">
                            <div>
                                <span class="font-weight-bold">Statut actuel :</span>
                                @if($freeRequestsMode && $freeRequestsMode->value)
                                    <span class="badge badge-success ml-2" style="font-size: 0.85rem;">
                                        <i class="fas fa-check-circle mr-1"></i>Activé
                                    </span>
                                @else
                                    <span class="badge badge-secondary ml-2" style="font-size: 0.85rem;">
                                        <i class="fas fa-times-circle mr-1"></i>Désactivé
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded">
                        <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-info-circle text-primary mr-1"></i> Fonctionnement :</h6>
                        <ul class="text-muted mb-0 small">
                            <li>Les <strong>2 premières demandes</strong> de chaque utilisateur seront gratuites (timbres offerts)</li>
                            <li>Chaque timbre gratuit (500 FCFA) sera déduit du <strong>Solde KKS-TECHNOLOGIES</strong></li>
                            <li>À partir de la <strong>3ème demande</strong>, l'utilisateur paie normalement</li>
                            <li>Un message d'accueil sera affiché lors du premier accès au dashboard</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.maintenance.free_requests.toggle') }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="{{ $freeRequestsMode && $freeRequestsMode->value ? '0' : '1' }}">
                        
                        @if($freeRequestsMode && $freeRequestsMode->value)
                            <button type="submit" class="btn btn-danger btn-block" style="padding: 12px; font-size: 1rem;">
                                <i class="fas fa-power-off mr-2"></i>Désactiver les Demandes Gratuites
                            </button>
                        @else
                            <button type="submit" class="btn btn-success btn-block" style="padding: 12px; font-size: 1rem;">
                                <i class="fas fa-gift mr-2"></i>Activer les Demandes Gratuites
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" style="background-color: #f8f9fc; border-left: 4px solid #4e73df;">
                    <h6 class="m-0 font-weight-bold" style="color: #4e73df;">
                        <i class="fas fa-chart-pie mr-2"></i>Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded" style="border-bottom: 3px solid #1cc88a;">
                                <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #1cc88a;">
                                    Timbres Offerts
                                </div>
                                <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $totalFreeTimbres }}</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-3 bg-light rounded" style="border-bottom: 3px solid #f6c23e;">
                                <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #f6c23e;">
                                    Montant Déduit
                                </div>
                                <div class="h3 mb-0 font-weight-bold text-gray-800">{{ number_format($totalFreeMontant, 0, ',', ' ') }} <small>FCFA</small></div>
                            </div>
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-dark mb-3 border-bottom pb-2">Détails par type</h6>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Actes de Naissance
                            <span class="badge badge-primary badge-pill">{{ $totalFreeNaissance }} timbres</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Actes de Décès
                            <span class="badge badge-info badge-pill">{{ $totalFreeDeces }} timbres</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Actes de Mariage
                            <span class="badge badge-warning badge-pill">{{ $totalFreeMariage }} timbres</span>
                        </li>
                    </ul>

                    <div class="alert alert-info py-2 m-0 d-flex align-items-center">
                        <i class="fas fa-users fa-2x mr-3"></i>
                        <div>
                            <span class="d-block font-weight-bold">{{ $usersWithFreeRequests }} utilisateurs</span>
                            <small>ont bénéficié de ce programme jusqu'à présent.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
