@extends('etatCivil.layouts.template')
@section('content')
<!-- Inclusion des liens CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-15">
                <div class="card-header py-3 bg-white">
                    <div class="row align-items-center">
                        <div class="col text-center">
                            <h2 class="mb-0 fw-bold text-primary"><i class="fas fa-users me-2"></i>Gestion des agents du service d'État Civil</h2>
                            <p class="text-muted mb-0">Informations sur les agents du service d'État Civil</p>
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
                                    <th scope="col" class="border-0 rounded-start-15 ps-4" style="text-align: center">Agent</th>
                                    <th scope="col" class="border-0" style="text-align: center">Contact</th>
                                    <th scope="col" class="border-0" style="text-align: center">Commune</th>
                                    <th scope="col" class="border-0" style="text-align: center"> en cas d'urgence</th>
                                    <th scope="col" class="border-0" style="text-align: center">Statut</th>
                                    <th scope="col" class="border-0  text-center rounded-end-15" style="text-align: center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agents as $agent)
                                <tr class="bg-white">
                                    <td class="ps-4">
                                        <div style="text-align: center; display:flex; justify-content:center">
                                            <div class="avatar-sm me-3">
                                                <div class="avatar-title bg-light rounded-circle text-primary fw-bold">
                                                    {{ substr($agent->name ?? 'EC', 0, 1).''. substr($agent->prenom ?? 'EC', 0, 1) }}
                                                </div>
                                            </div>
                                            <div style="text-align:center">
                                                <h6 class="mb-0 fw-semibold">{{ $agent->name.' '.$agent->prenom ?? 'Non spécifié' }}</h6>
                                                <small class="text-muted">{{ $agent->email ?? 'Email non renseigné' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center">
                                        <span class="d-block fw-medium">{{ $agent->contact ?? 'Non spécifié' }}</span>
                                    </td>
                                    <td style="text-align: center">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                            {{ $agent->commune ?? 'Non spécifié' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center">
                                        <span class="d-block fw-medium">{{ $agent->cas_urgence ?? 'Non spécifié' }}</span>
                                    </td>
                                    <td style="text-align: center">
                                        @if($agent->archived_at === null )
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill">
                                            <i class="fas fa-circle me-1 small"></i>Actif
                                        </span>
                                        @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">
                                            <i class="fas fa-circle me-1 small"></i>Archivé
                                        </span>
                                        @endif
                                    </td>
                                    <td style="text-align: center">
                                        <div class="d-flex justify-content-center">
                                            <!-- Voir détails -->
                                            <button class="btn btn-sm btn-light rounded-pill me-2 view-agent" 
                                                    data-agent-id="{{ $agent->id }}"
                                                    data-url="{{ route('agents.show', $agent->id) }}"
                                                    data-bs-toggle="tooltip" title="Voir détails">
                                                <i class="fas fa-eye text-primary"></i>
                                            </button>
                                            
                                            <!-- Modifier -->
                                            <button class="btn btn-sm btn-light rounded-pill me-2 edit-agent" 
                                                    data-agent-id="{{ $agent->id }}"
                                                    data-url="{{ route('agents.edit', $agent->id) }}"
                                                    data-bs-toggle="tooltip" title="Modifier">
                                                <i class="fas fa-edit text-warning"></i>
                                            </button>
                                            
                                            <!-- Archiver/Désarchiver -->
                                            @if($agent->archived_at === null)
                                            <button class="btn btn-sm btn-light rounded-pill archive-agent" 
                                                    data-agent-id="{{ $agent->id }}"
                                                    data-url="{{ route('agents.archive', $agent->id) }}"
                                                    data-agent-name="{{ $agent->name.' '.$agent->prenom }}"
                                                    data-bs-toggle="tooltip" title="Archiver">
                                                <i class="fas fa-archive text-danger"></i>
                                            </button>
                                            @else
                                            <button class="btn btn-sm btn-light rounded-pill unarchive-agent" 
                                                    data-agent-id="{{ $agent->id }}"
                                                    data-url="{{ route('agents.unarchive', $agent->id) }}"
                                                    data-agent-name="{{ $agent->name.' '.$agent->prenom }}"
                                                    data-bs-toggle="tooltip" title="Désarchiver">
                                                <i class="fas fa-box-open text-success"></i>
                                            </button>
                                            @endif
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
                            Affichage de <span class="fw-semibold">{{ $agents->firstItem() }}</span> à 
                            <span class="fw-semibold">{{ $agents->lastItem() }}</span> sur 
                            <span class="fw-semibold">{{ $agents->total() }}</span> résultats
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination rounded-pill">
                                <li class="page-item {{ $agents->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link rounded-start-pill" href="{{ $agents->previousPageUrl() }}">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                                
                                @for ($i = 1; $i <= $agents->lastPage(); $i++)
                                <li class="page-item {{ $agents->currentPage() == $i ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $agents->url($i) }}">{{ $i }}</a>
                                </li>
                                @endfor
                                
                                <li class="page-item {{ $agents->hasMorePages() ? '' : 'disabled' }}">
                                    <a class="page-link rounded-end-pill" href="{{ $agents->nextPageUrl() }}">
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
        --secondary: #1977cc;
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
        background-color: #1977cc;
        border-color: #1977cc;
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // CSRF Token pour les requêtes AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        console.error('CSRF token not found');
    }

    // Voir les détails de l'agent
    document.querySelectorAll('.view-agent').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            if (url) {
                window.location.href = url;
            } else {
                console.error('URL not found for view agent');
            }
        });
    });

    // Modifier l'agent
    document.querySelectorAll('.edit-agent').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            if (url) {
                window.location.href = url;
            } else {
                console.error('URL not found for edit agent');
            }
        });
    });

    // Archiver un agent
    document.querySelectorAll('.archive-agent').forEach(button => {
        button.addEventListener('click', function() {
            const agentId = this.getAttribute('data-agent-id');
            const agentName = this.getAttribute('data-agent-name');
            const url = this.getAttribute('data-url');
            
            Swal.fire({
                title: 'Archiver l\'agent',
                html: `Êtes-vous sûr de vouloir archiver <strong>${agentName}</strong> ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, archiver',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed && url) {
                    archiveAgent(url, agentName);
                }
            });
        });
    });

    // Désarchiver un agent
    document.querySelectorAll('.unarchive-agent').forEach(button => {
        button.addEventListener('click', function() {
            const agentId = this.getAttribute('data-agent-id');
            const agentName = this.getAttribute('data-agent-name');
            const url = this.getAttribute('data-url');
            
            Swal.fire({
                title: 'Désarchiver l\'agent',
                html: `Êtes-vous sûr de vouloir désarchiver <strong>${agentName}</strong> ?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1977cc',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, désarchiver',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed && url) {
                    unarchiveAgent(url, agentName);
                }
            });
        });
    });

    // Fonction pour archiver un agent
    function archiveAgent(url, agentName) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Archivé !',
                    text: `L'agent ${agentName} a été archivé avec succès.`,
                    icon: 'success',
                    confirmButtonColor: '#1977cc'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Erreur !',
                    text: data.message || 'Une erreur est survenue lors de l\'archivage.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Erreur !',
                text: 'Une erreur est survenue lors de l\'archivage.',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        });
    }

    // Fonction pour désarchiver un agent
    function unarchiveAgent(url, agentName) {
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Désarchivé !',
                    text: `L'agent ${agentName} a été désarchivé avec succès.`,
                    icon: 'success',
                    confirmButtonColor: '#1977cc'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Erreur !',
                    text: data.message || 'Une erreur est survenue lors du désarchivage.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Erreur !',
                text: 'Une erreur est survenue lors du désarchivage.',
                icon: 'error',
                confirmButtonColor: '#d33'
            });
        });
    }

    // Recherche en temps réel
    const searchInput = document.querySelector('input[type="text"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
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
});
</script>
@endsection