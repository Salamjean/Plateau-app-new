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
                        <i class="fas fa-history me-2"></i> Historique de mes demandes
                    </h4>
                </div>

                <!-- Historique des demandes -->
                <h5 class="section-title">
                    <i class="fas fa-list-check me-2"></i>Toutes mes demandes
                </h5>
                <div class="table-responsive">
                    <table class="table table-striped" id="historyTable">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Type de demande</th>
                                <th>Statut</th>
                                <th>Livraison</th>
                                <th>Date et Heure</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historique as $demande)
                                <tr>
                                    <td>{{ $demande->reference }}</td>
                                    <td>
                                        @switch($demande->table_name)
                                            @case('naissance_simple') Naissance @break
                                            @case('naissance_certificat') Naissance @break
                                            @case('deces_simple') Décès @break
                                            @case('deces_certificat') Décès @break
                                            @case('mariage') Mariage @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($demande->etat == 'traité') bg-success 
                                            @elseif($demande->etat == 'en attente') bg-warning 
                                            @elseif($demande->etat == 'terminé') bg-danger 
                                            @else bg-secondary 
                                            @endif">
                                            {{ $demande->etat }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($demande->statut_livraison)
                                            <span class="badge bg-info">{{ $demande->statut_livraison }}</span>
                                        @else
                                            <span class="text-muted">Non applicable</span>
                                        @endif
                                    </td>
                                    <td>{{ $demande->created_at }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-details" 
                                                data-type="{{ $demande->table_name }}" 
                                                data-id="{{ $demande->id }}">
                                            <i class="fas fa-eye me-1"></i>Détails
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
        $('#historyTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            order: [[4, 'desc']],
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: 2 },
                { responsivePriority: 3, targets: 3 },
                { responsivePriority: 4, targets: 5 }
            ]
        });
    });

    // Fonction pour formater les données de manière avancée
    function formatDetailsAdvanced(data, type) {
        const getStatusColor = (status) => {
            switch(status) {
                case 'traité': return 'success';
                case 'en attente': return 'warning';
                case 'rejeté': return 'danger';
                default: return 'secondary';
            }
        };

        const getTypeLabel = (tableName) => {
            switch(tableName) {
                case 'naissance_simple': return 'Naissance';
                case 'naissance_certificat': return 'Naissance';
                case 'deces_simple': return 'Décès';
                case 'deces_certificat': return 'Décès';
                case 'mariage': return 'Mariage';
                default: return tableName;
            }
        };

        return `
            <div class="demande-details">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle me-2"></i>Informations Générales</h6>
                        <p><strong>Référence:</strong> ${data.reference}</p>
                        <p><strong>Type:</strong> ${getTypeLabel(type)}</p>
                        <p><strong>Statut:</strong> <span class="badge bg-${getStatusColor(data.etat)}">${data.etat}</span></p>
                        <p><strong>Créé le:</strong> ${new Date(data.created_at).toLocaleString('fr-FR')}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-file-alt me-2"></i>Détails Spécifiques</h6>
                        ${getSpecificDetails(data, type)}
                    </div>
                </div>
                ${getLivraisonDetails(data)}
            </div>
        `;
    }

    function getSpecificDetails(data, type) {
        let html = '';
        switch(type) {
            case 'naissance_simple':
                html = `
                    <p><strong>Type:</strong> ${data.type || 'N/A'}</p>
                    <p><strong>Pour:</strong> ${data.pour || 'N/A'}</p>
                    <p><strong>Nom:</strong> ${data.name || 'N/A'} ${data.prenom || ''}</p>
                    <p><strong>Numéro:</strong> ${data.number || 'N/A'}</p>
                    <p><strong>Date:</strong> ${data.DateR || 'N/A'}</p>
                `;
                break;
                
            case 'naissance_certificat':
                html = `
                    <p><strong>Nom Hôpital:</strong> ${data.nomHopital || 'N/A'}</p>
                    <p><strong>Nom Défunt:</strong> ${data.nomDefunt || 'N/A'}</p>
                    <p><strong>Date Naissance:</strong> ${data.dateNaiss || 'N/A'}</p>
                    <p><strong>Lieu Naissance:</strong> ${data.lieuNaiss || 'N/A'}</p>
                    <p><strong>Nom Père:</strong> ${data.nompere || 'N/A'}</p>
                `;
                break;
                
            case 'deces_simple':
                html = `
                    <p><strong>Nom:</strong> ${data.name || 'N/A'}</p>
                    <p><strong>Numéro:</strong> ${data.numberR || 'N/A'}</p>
                    <p><strong>Date:</strong> ${data.dateR || 'N/A'}</p>
                `;
                break;
                
            case 'deces_certificat':
                html = `
                    <p><strong>Nom Hôpital:</strong> ${data.nomHopital || 'N/A'}</p>
                    <p><strong>Nom Défunt:</strong> ${data.nomDefunt || 'N/A'}</p>
                    <p><strong>Date Décès:</strong> ${data.dateDces || 'N/A'}</p>
                    <p><strong>Date Naissance:</strong> ${data.dateNaiss || 'N/A'}</p>
                `;
                break;
                
            case 'mariage':
                html = `
                    <p><strong>Nom Époux:</strong> ${data.nomEpoux || 'N/A'}</p>
                    <p><strong>Prénom Époux:</strong> ${data.prenomEpoux || 'N/A'}</p>
                    <p><strong>Date Naissance Époux:</strong> ${data.dateNaissanceEpoux || 'N/A'}</p>
                `;
                break;
        }
        return html;
    }

    function getLivraisonDetails(data) {
        if (!data.nom_destinataire && !data.adresse_livraison) return '';
        
        return `
            <hr>
            <div class="row mt-3">
                <div class="col-12">
                    <h6><i class="fas fa-truck me-2"></i>Informations de Livraison</h6>
                    <p><strong>Destinataire:</strong> ${data.nom_destinataire || 'N/A'} ${data.prenom_destinataire || ''}</p>
                    <p><strong>Adresse:</strong> ${data.adresse_livraison || 'N/A'}</p>
                    <p><strong>Contact:</strong> ${data.contact_destinataire || 'N/A'}</p>
                    <p><strong>Email:</strong> ${data.email_destinataire || 'N/A'}</p>
                    <p><strong>Statut Livraison:</strong> ${data.statut_livraison || 'Non spécifié'}</p>
                </div>
            </div>
        `;
    }

    // Gestionnaire de clic pour les boutons de détails
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                const id = this.getAttribute('data-id');
                
                // Afficher le loader
                Swal.fire({
                    title: 'Chargement...',
                    text: 'Récupération des détails en cours',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Requête AJAX avec la route correcte
                fetch(`{{ route('demande.details.json', ['type' => ':type', 'id' => ':id']) }}`
                    .replace(':type', type)
                    .replace(':id', id))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: `<i class="fas fa-file-alt me-2"></i>Détails de la demande`,
                            html: formatDetailsAdvanced(data.demande, type),
                            icon: 'info',
                            width: '700px',
                            showCloseButton: true,
                            showConfirmButton: false,
                            showCancelButton: true,
                            cancelButtonText: '<i class="fas fa-times me-2"></i>Fermer',
                            customClass: {
                                popup: 'rounded-3'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Erreur',
                            text: data.error || 'Une erreur est survenue',
                            icon: 'error',
                            confirmButtonColor: '#1977cc'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Erreur',
                        text: 'Impossible de charger les détails de la demande',
                        icon: 'error',
                        confirmButtonColor: '#1977cc'
                    });
                    console.error('Error:', error);
                });
            });
        });
    });

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