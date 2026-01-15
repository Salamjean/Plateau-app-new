@extends('admin.layouts.template')
@section('content')
<div class="container-fluid">
    <!-- Titre de la page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tools mr-2" style="color: #6777ef;"></i>
            Gestion de la Maintenance
        </h1>
    </div>

    <!-- Messages de succès -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Carte Maintenance Web -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color: #6777ef;">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-globe mr-2"></i>Maintenance Site Web
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Activer ce mode bloquera l'accès au site web pour tous les visiteurs. 
                        Les administrateurs pourront toujours accéder au panneau d'administration.
                    </p>

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <span class="font-weight-bold">Statut actuel:</span>
                            @if($webMaintenance && $webMaintenance->value)
                                <span class="badge badge-danger ml-2">
                                    <i class="fas fa-lock mr-1"></i>Maintenance Active
                                </span>
                            @else
                                <span class="badge badge-success ml-2">
                                    <i class="fas fa-unlock mr-1"></i>Site en ligne
                                </span>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('admin.maintenance.web.toggle') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="web_message">Message affiché aux visiteurs</label>
                            <textarea class="form-control" id="web_message" name="message" rows="3" 
                                placeholder="Message personnalisé...">{{ $webMaintenance->message ?? '' }}</textarea>
                        </div>
                        
                        <input type="hidden" name="status" value="{{ $webMaintenance && $webMaintenance->value ? '0' : '1' }}">
                        
                        @if($webMaintenance && $webMaintenance->value)
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-play mr-2"></i>Désactiver la Maintenance Web
                            </button>
                        @else
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-pause mr-2"></i>Activer la Maintenance Web
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Carte Maintenance API -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color: #6777ef;">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-mobile-alt mr-2"></i>Maintenance API Mobile
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        Activer ce mode bloquera l'accès aux APIs pour les applications mobiles. 
                        Les utilisateurs verront un message de maintenance dans l'application.
                    </p>

                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <span class="font-weight-bold">Statut actuel:</span>
                            @if($apiMaintenance && $apiMaintenance->value)
                                <span class="badge badge-danger ml-2">
                                    <i class="fas fa-lock mr-1"></i>API Bloquée
                                </span>
                            @else
                                <span class="badge badge-success ml-2">
                                    <i class="fas fa-unlock mr-1"></i>API Active
                                </span>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('admin.maintenance.api.toggle') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="api_message">Message affiché dans l'application</label>
                            <textarea class="form-control" id="api_message" name="message" rows="3" 
                                placeholder="Message personnalisé...">{{ $apiMaintenance->message ?? '' }}</textarea>
                        </div>
                        
                        <input type="hidden" name="status" value="{{ $apiMaintenance && $apiMaintenance->value ? '0' : '1' }}">
                        
                        @if($apiMaintenance && $apiMaintenance->value)
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-play mr-2"></i>Désactiver la Maintenance API
                            </button>
                        @else
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-pause mr-2"></i>Activer la Maintenance API
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Information complémentaire -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3" style="background-color: #f8f9fc; border-left: 4px solid #6777ef;">
                    <h6 class="m-0 font-weight-bold" style="color: #6777ef;">
                        <i class="fas fa-info-circle mr-2"></i>Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-globe text-primary mr-2"></i>Maintenance Web</h6>
                            <ul class="text-muted mb-3">
                                <li>Bloque l'accès à toutes les pages publiques</li>
                                <li>Une page de maintenance sera affichée</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-mobile-alt text-primary mr-2"></i>Maintenance API</h6>
                            <ul class="text-muted mb-0">
                                <li>Bloque toutes les requêtes API mobiles</li>
                                <li>Retourne une erreur 503 avec le message</li>
                                <li>Utilisé pour les mises à jour de l'application</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }
    .btn-block {
        padding: 12px;
        font-size: 1rem;
    }
</style>
@endsection
