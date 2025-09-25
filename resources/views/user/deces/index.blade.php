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
        --success-color: #2ecc71;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --light-bg: #f8f9fa;
        --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .form-background {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(46, 204, 113, 0.1) 100%);
        padding: 20px;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
    }

    .card-rounded {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        border: none;
    }

    .section-header {
        color: #1977cc;
        padding: 12px 20px;
        border-radius: 8px 8px 0 0;
        margin-bottom: 0;
    }

    .table-responsive {
        border-radius: 0 0 8px 8px;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    th {
        color: white;
        font-weight: 600;
        padding: 12px;
        text-align: center;
    }


    td {
        padding: 10px;
        vertical-align: middle;
        border-bottom: 1px solid #eee;
        text-align: center;
    }

    tr:nth-child(even) {
        background-color: rgba(0, 0, 0, 0.02);
    }

    tr:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .badge {
        padding: 6px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .badge-warning {
        background-color: rgba(243, 156, 18, 0.1);
        color: var(--warning-color);
    }

    .badge-success {
        background-color: rgba(46, 204, 113, 0.1);
        color: var(--success-color);
    }

    .badge-danger {
        background-color: rgba(231, 76, 60, 0.1);
        color: var(--danger-color);
    }

    .btn-new-request {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
    }

    .document-preview {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
        cursor: pointer;
        border: 1px solid #eee;
    }

    .retrait-badge {
        background-color: var(--danger-color);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
    }

    /* Styles responsive */
    @media (max-width: 768px) {
        .document-preview {
            width: 60px;
            height: 60px;
        }
    }

    @media (max-width: 576px) {
        .btn-new-request {
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        .section-header {
            font-size: 1.1rem;
            padding: 10px 15px;
        }
    }
</style>

<div class="row flex-grow form-background">
    <div class="col-12 grid-margin stretch-card">
        <div class="card card-rounded">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title card-title-dash mb-0">Demandes d'extrait de décès</h4>
                    <a href="{{route('user.extrait.deces.create')}}" class="btn btn-new-request">
                        <i class="fas fa-plus me-2"></i>Nouvelle demande
                    </a>
                </div>

                <!-- Section Demandes pour tierce personne -->
                <div>
                    <h5 class="section-header">Mes demandes d'extraits de naissance</h5>
                    <div class="table-responsive">
                        <table class="table select-table">
                            <thead>
                                <tr style="background-color: #1977cc">
                                    <th>Référence</th>
                                    <th>Défunt</th>
                                    <th>Détails</th>
                                    <th>Documents</th>
                                    <th>Statut</th>
                                    <th>Agent</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($deces as $dece)
                                <tr>
                                    <td>{{ $dece->reference }}</td>
                                    <td>
                                        <strong>{{ $dece->name }}</strong><br>
                                    </td>
                                    <td>
                                        <small>
                                            <strong>Registre:</strong> {{ $dece->numberR }}<br>
                                            <strong>Date:</strong> {{ \Carbon\Carbon::parse($dece->dateR)->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            @if (pathinfo($dece->CNIdcl, PATHINFO_EXTENSION) === 'pdf')
                                                <a href="{{ asset('storage/' . $dece->CNIdcl) }}" target="_blank">
                                                    <img src="{{ asset('assets/assets/img/pdf.jpg') }}" alt="PDF" class="document-preview">
                                                </a>
                                            @else
                                                <img src="{{ asset('storage/' . $dece->CNIdcl) }}" 
                                                     alt="Certificat médical" 
                                                     class="document-preview"
                                                     onclick="showImage(this)">
                                            @endif

                                            @if (pathinfo($dece->CNIdfnt, PATHINFO_EXTENSION) === 'pdf')
                                                <a href="{{ asset('storage/' . $dece->CNIdfnt) }}" target="_blank">
                                                    <img src="{{ asset('assets/assets/img/pdf.jpg') }}" alt="PDF" class="document-preview">
                                                </a>
                                            @else
                                                <img src="{{ asset('storage/' . $dece->CNIdfnt) }}" 
                                                     alt="CNI Défunt" 
                                                     class="document-preview"
                                                     onclick="showImage(this)">
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($dece->etat == 'en attente') badge-warning
                                            @elseif($dece->etat == 'réçu') badge-success
                                            @else badge-danger @endif">
                                            {{ $dece->etat == 'en attente' ? 'EN ATTENTE' : 'EN COURS DE TRAITEMENT'  }}
                                        </span>
                                        <div class="mt-1">
                                            <span class="retrait-badge">{{ $dece->choix_option }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $dece->agent ? $dece->agent->name.' '.$dece->agent->prenom : 'Non attribué' }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center">
                                            @if ($dece->etat !== 'réçu' && $dece->etat !== 'terminé')
                                                <button onclick="confirmDelete('{{ route('user.extrait.deces.delete', $dece->id) }}')" 
                                                        class="btn btn-sm btn-danger action-btn" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-secondary action-btn disabled" title="Non supprimable">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Aucune déclaration trouvée</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                <h5 class="modal-title" id="imageModalLabel">Visualisation du document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" class="modal-image" src="" alt="Document agrandi">
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
    $(document).ready(function() {
        $('.table').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            }
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
            title: 'Confirmation',
            text: "Êtes-vous sûr de vouloir supprimer cette demande ?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            title: 'Succès',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonColor: '#3085d6'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Erreur',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonColor: '#3085d6'
        });
    @endif
</script>

@endsection