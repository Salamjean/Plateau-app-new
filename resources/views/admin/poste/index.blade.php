@extends('mairie.layouts.template')
@section('content')
<!-- Inclusion des liens CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-15">
                <div class="card-header py-3 bg-white">
                    <div class="row align-items-center">
                        <div class="col text-center">
                            <h2 class="mb-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Gestion du service de livraison</h2>
                            <p class="text-muted mb-0">Informations sur le service de livraison</p>
                            {{-- <p class="text-muted mb-0">Liste des responsables d'état civil enregistrés</p> --}}
                        </div>
                        {{-- <div class="col-auto">
                            <button class="btn btn-primary rounded-pill px-4 py-2">
                                <i class="fas fa-plus me-2"></i>Nouveau responsable
                            </button>
                        </div> --}}
                    </div>
                </div>

                <div class="card-body px-4">
                    <!-- Filtres et recherche -->
                    {{-- <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="input-group rounded-pill">
                                <span class="input-group-text bg-transparent border-0 ps-4">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-0 bg-light rounded-pill" placeholder="Rechercher un responsable...">
                            </div>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end">
                            <div class="btn-group rounded-pill shadow-sm" role="group">
                                <button type="button" class="btn btn-light active">Tous</button>
                                <button type="button" class="btn btn-light">Actifs</button>
                                <button type="button" class="btn btn-light">Archivés</button>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Tableau des états civils -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="border-0 rounded-start-15 ps-4" style="text-align: center">Responsable</th>
                                    <th scope="col" class="border-0" style="text-align: center">Contact</th>
                                    <th scope="col" class="border-0" style="text-align: center">Commune</th>
                                    <th scope="col" class="border-0" style="text-align: center">Statut</th>
                                    <th scope="col" class="border-0  text-center rounded-end-15" style="text-align: center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($postes as $etatCivil)
                                <tr class="bg-white">
                                    <td class="ps-4">
                                        <div style="text-align: center; display:flex; justify-content:center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-light rounded-circle text-primary fw-bold">
                                                    {{ substr($etatCivil->name ?? 'EC', 0, 1) }}
                                                </div>
                                            </div>
                                            <div style="text-align:center">
                                                <h6 class="mb-0 fw-semibold">{{ $etatCivil->name ?? 'Non spécifié' }}</h6>
                                                <small class="text-muted">{{ $etatCivil->email ?? 'Email non renseigné' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center">
                                        <span class="d-block fw-medium">{{ $etatCivil->contact ?? 'Non spécifié' }}</span>
                                    </td>
                                    <td style="text-align: center">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                            {{ $etatCivil->commune ?? 'Non spécifié' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center">
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill">
                                            <i class="fas fa-circle me-1 small"></i>Actif
                                        </span>
                                    </td>
                                    <td style="text-align: center">
                                        <div class="d-flex justify-content-center">
                                            <!-- Bouton Modifier -->
                                            <a href="{{ route('post.edit', $etatCivil->id) }}" 
                                            class="btn btn-sm btn-light rounded-pill me-2" 
                                            data-bs-toggle="tooltip" title="Modifier">
                                                <i class="fas fa-edit text-warning"></i>
                                            </a>
                                            
                                            <!-- Formulaire de suppression -->
                                            {{-- <form action="{{ route('etat-civil.destroy', $etatCivil->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light rounded-pill" 
                                                        data-bs-toggle="tooltip" title="Supprimer"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce responsable?')">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </form> --}}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Affichage de <span class="fw-semibold">{{ $postes->firstItem() }}</span> à 
                            <span class="fw-semibold">{{ $postes->lastItem() }}</span> sur 
                            <span class="fw-semibold">{{ $postes->total() }}</span> résultats
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination rounded-pill">
                                <li class="page-item {{ $postes->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link rounded-start-pill" href="{{ $postes->previousPageUrl() }}">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                                
                                @for ($i = 1; $i <= $postes->lastPage(); $i++)
                                <li class="page-item {{ $postes->currentPage() == $i ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $postes->url($i) }}">{{ $i }}</a>
                                </li>
                                @endfor
                                
                                <li class="page-item {{ $postes->hasMorePages() ? '' : 'disabled' }}">
                                    <a class="page-link rounded-end-pill" href="{{ $postes->nextPageUrl() }}">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #1977cc;
        --secondary: #ff8800;
        --light-bg: #f8f9fa;
    }
    
    body {
        background-color: #f8f9fa;
        font-family: 'Poppins', sans-serif;
    }
    
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    
    .rounded-15 {
        border-radius: 15px !important;
    }
    
    .btn-primary {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    .btn-primary:hover {
        background-color: #006600;
        border-color: #006600;
    }
    
    .text-primary {
        color: var(--primary) !important;
    }
    
    .text-warning {
        color: var(--secondary) !important;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .avatar-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-title {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
    }
    
    .pagination .page-item.active .page-link {
        background-color: var(--primary);
        border-color: var(--primary);
    }
    
    .pagination .page-link {
        color: var(--primary);
        border: none;
        padding: 0.5rem 0.9rem;
    }
    
    .pagination .page-link:hover {
        background-color: #e9ecef;
        color: #006600;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 0.25rem rgba(255, 136, 0, 0.15);
    }
    
    .btn-light {
        background-color: #f8f9fa;
        border-color: #e9ecef;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 126, 0, 0.03) !important;
    }
    
    @media (max-width: 768px) {
        .card-header .row {
            flex-direction: column;
            text-align: center;
        }
        
        .card-header .col-auto {
            margin-top: 1rem;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Fonctionnalité de recherche en temps réel
        const searchInput = document.querySelector('input[type="text"]');
        const tableRows = document.querySelectorAll('tbody tr');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchText = this.value.toLowerCase();
                
                tableRows.forEach(row => {
                    const textContent = row.textContent.toLowerCase();
                    if (textContent.includes(searchText)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
        
        // Animation de survol des lignes
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'transform 0.2s ease, box-shadow 0.2s ease';
                this.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = '';
                this.style.boxShadow = '';
            });
        });
    });
  @if (Session::has('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Succès',
                    text: '{{ Session::get('success') }}',
                    confirmButtonText: 'OK',
                    background: 'white',
                });
            @endif

            @if (Session::has('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: '{{ Session::get('error') }}',
                    confirmButtonText: 'OK',
                    background: 'white',
                    
                });
            @endif

            @if (Session::has('no_account'))
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: '{{ Session::get('no_account') }}',
                    confirmButtonText: 'Créer un compte',
                    background: 'white',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('register') }}';
                    }
                });
            @endif
     
</script>
@endsection