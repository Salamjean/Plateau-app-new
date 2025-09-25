@extends('user.layouts.template')

@section('content')
<!-- Styles et Scripts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    :root {
        --primary-color: #1977cc;
        --secondary-color: #1977cc;
        --accent-color: #1977cc;
        --success-color: #27ae60;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --light-bg: #f8f9fa;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    .form-background {
        background: linear-gradient(135deg, rgba(25, 119, 204, 0.05) 0%, rgba(255, 255, 255, 0.9) 100%);
        padding: 30px;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .card-rounded {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        border: none;
        transition: var(--transition);
    }

    .card-rounded:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .section-title {
        color: var(--secondary-color);
        margin: 30px 0 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid rgba(25, 119, 204, 0.1);
        position: relative;
        font-weight: 600;
    }

    .section-title::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 80px;
        height: 2px;
        background: var(--accent-color);
    }

    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 30px;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    thead {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        color: white;
    }

    th {
        padding: 15px;
        font-weight: 600;
        text-align: center;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    td {
        padding: 12px 15px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        text-align: center;
    }

    tr:hover {
        background-color: rgba(25, 119, 204, 0.03) !important;
    }

    .badge {
        padding: 8px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .badge-waiting {
        background-color: rgba(243, 156, 18, 0.1);
        color: var(--warning-color);
    }

    .badge-received {
        background-color: rgba(46, 204, 113, 0.1);
        color: var(--success-color);
    }

    .badge-rejected {
        background-color: rgba(231, 76, 60, 0.1);
        color: var(--danger-color);
    }

    .btn-new-request {
        background: linear-gradient(to right, var(--primary-color), var(--accent-color));
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        box-shadow: 0 4px 10px rgba(25, 119, 204, 0.3);
    }

    .btn-new-request:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(25, 119, 204, 0.4);
        color: white;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        transition: var(--transition);
        color: white;
        background-color: red;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .btn-delete {
        background-color: #dc3545;
        color: var(--danger-color);
        border: none;
    }

    .btn-delete:hover {
        background-color: #d82d3e;
    }

    .btn-disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background-color: rgba(108, 117, 125, 0.1) !important;
        color: #6c757d !important;
    }

    .document-preview {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: var(--transition);
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .document-preview:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .retrait-badge {
        background-color: var(--danger-color);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
    }

    .empty-state {
        padding: 40px 0;
        text-align: center;
        background-color: rgba(0, 0, 0, 0.02);
        border-radius: 12px;
    }

    .empty-state i {
        font-size: 3rem;
        color: rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }

    /* Modal styles */
    .modal-content {
        border-radius: 16px;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .modal-image {
        max-width: 100%;
        max-height: 80vh;
        display: block;
        margin: 0 auto;
        border-radius: 8px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .form-background {
            padding: 15px;
        }
        
        th, td {
            padding: 10px 8px;
            font-size: 0.8rem;
        }
        
        .btn-new-request {
            padding: 8px 15px;
            font-size: 0.85rem;
        }
    }
</style>

<div class="row flex-grow form-background animate__animated animate__fadeIn">
    <div class="col-12 grid-margin stretch-card">
        <div class="card card-rounded">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                    <h4 class="card-title card-title-dash mb-3 mb-md-0 text-center text-md-start">
                        <i class="fas fa-file-contract me-2"></i> Mes rendez-vous planifier
                    </h4>
                    <a href="{{ route('user.rendezvous.create') }}" class="btn btn-new-request">
                        <i class="fas fa-plus-circle me-2"></i>Nouveau rendez-vous
                    </a>
                </div>

                <!-- Demandes d'extrait de mariage -->
                <h5 class="section-title">
                    <i class="fas fa-list-check me-2"></i>Historique des rendez-vous
                </h5>
                <div class="table-responsive">
                    <table class="table" id="mariageTable">
                        <thead>
                            <tr>
                                <th>Informations époux</th>
                                <th>Informations épouse</th>
                                <th>Contact</th>
                                <th>Rendez-vous</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rendezvous as $item)
                            <tr class="animate__animated animate__fadeIn">
                                 <td class="text-center" style="text-align: center">
                                    <div  style="text-align: center">
                                    
                                        <div class="text-center" style="text-align: center">
                                            <h6 class="mb-1 text-muted">Nom: <span class="text-black">{{ $item->nom_epoux.' '.$item->prenom_epoux }} </span></h6>
                                            <h6 class="mb-1 text-muted">Date Naiss: <span class="text-black">{{ $item->date_naissance_epoux }}  </span></h6>
                                            <small class="mb-1 text-muted">Lieu Naiss: <span class="text-black">{{ $item->lieu_naissance_epoux }}  </span></small>
                                        </div>
                                    </div>
                                 </td>
                                <td class="text-center" style="text-align: center">
                                    <div  style="text-align: center">
                                    
                                        <div class="text-center" style="text-align: center">
                                            <h6 class="mb-1 text-muted">Nom: <span class="text-black">{{ $item->nom_epouse.' '.$item->prenom_epouse }} </span></h6>
                                            <h6 class="mb-1 text-muted">Date Naiss: <span class="text-black">{{ $item->date_naissance_epouse }} </span></h6>
                                            <small class="mb-1 text-muted">Lieu Naiss: <span class="text-black">{{ $item->lieu_naissance_epouse }}</span></small>
                                        </div>
                                    </div>
                                 </td>
                                <td class="text-center" style="text-align: center">
                                    <div  style="text-align: center">
                                    
                                        <div class="text-center" style="text-align: center">
                                            <h6 class="mb-1 text-muted">Adresse: <span class="text-black">{{ $item->adresse }}</span></h6>
                                            <h6 class="mb-1 text-muted">Contact: <span class="text-black">{{ $item->telephone }}</span></h6>
                                            <small class="mb-1 text-muted">Email: <span class="text-black">{{ $item->email }}</span></small>
                                        </div>
                                    </div>
                                 </td>
                                 
                                <td class="text-center" style="text-align: center">
                                    <div  style="text-align: center">
                                    
                                        <div class="text-center" style="text-align: center">
                                            <h6 class="mb-1 text-muted">Date: <span class="text-black">{{ $item->date_mariage_souhaitee }}</span></h6>
                                            <h6 class="mb-1 text-muted">Heure: <span class="text-black">{{ $item->heure_souhaitee }}</span></h6>
                                           @php
                                                $badgeClasses = [
                                                    'en attente' => 'bg-warning text-dark',
                                                    'confirmé' => 'bg-success text-white',
                                                    'annulé' => 'bg-danger text-white',
                                                    'terminé' => 'bg-primary text-white',
                                                    'reporté' => 'bg-info text-dark'
                                                ];
                                                
                                                $defaultClass = 'bg-secondary text-white';
                                            @endphp

                                            <span class="badge {{ $badgeClasses[$item->statut] ?? $defaultClass }}">
                                                {{ $item->statut }}
                                            </span>
                                        </div>
                                    </div>
                                 </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h5 class="mt-3">Aucun rendez vous trouvée</h5>
                                        <p class="text-muted">Vous n'avez effectué aucun rendez vous de mariage</p>
                                    </div>
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

<!-- Modale pour afficher les images -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">
                    <i class="fas fa-file-image me-2"></i>Visualisation du document
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" class="modal-image" src="" alt="Document agrandi">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Initialiser DataTables
    $(document).ready(function() {
        $('#mariageTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            order: [[0, 'desc']],
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 3 },
                { responsivePriority: 3, targets: 4 }
            ]
        });
    });

    function showImage(imageElement) {
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageElement.src;
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }

    function confirmDelete(url) {
        Swal.fire({
            title: 'Confirmer la suppression',
            text: "Voulez-vous vraiment supprimer cette demande ? Cette action est irréversible.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1977cc',
            cancelButtonColor: '#e74c3c',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    function showDisabledMessage() {
        Swal.fire({
            title: 'Action non autorisée',
            text: 'Cette demande ne peut pas être supprimée car elle est en cours de traitement ou déjà finalisée.',
            icon: 'info',
            confirmButtonColor: '#1977cc'
        });
    }

    // Notifications
    @if(session('success'))
        Swal.fire({
            title: 'Succès',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#1977cc',
            timer: 3000
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Erreur',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#e74c3c'
        });
    @endif
</script>

@endsection