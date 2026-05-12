@extends('user.layouts.template')

@section('content')
<div class="container-fluid px-4 pt-4 mb-5 animate-fade-in">
    
    <!-- Header Section -->
    <div class="row align-items-center mb-5">
        <div class="col-md-8">
            <h1 class="text-navy-bold mb-1">Historique des demandes</h1>
            <p class="text-grey mb-0">Consultez et suivez l'évolution de toutes vos démarches administratives.</p>
        </div>
        <div class="col-md-4 text-md-right mt-3 mt-md-0">
            <a href="{{route('user.extrait.create')}}" class="btn btn-premium shadow-sm">
                <i class="fas fa-plus-circle mr-2"></i> Nouvelle Demande
            </a>
        </div>
    </div>


    <!-- History Table Card -->
    <div class="glass-card shadow-sm border-0 mb-4" style="border-radius: 30px; overflow: hidden;">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <h5 class="text-navy-bold mb-0">Liste de vos demandes</h5>
                <div class="d-flex gap-2">
                    <select id="filterType" class="custom-select-modern">
                        <option value="">Tous les types</option>
                        <option value="Naissance">Naissance</option>
                        <option value="Mariage">Mariage</option>
                        <option value="Décès">Décès</option>
                    </select>
                    <select id="filterLivraison" class="custom-select-modern">
                        <option value="">Toutes les livraisons</option>
                        <option value="Retrait Mairie">Retrait Mairie</option>
                        <option value="En cours">En cours</option>
                        <option value="Livré">Livré</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table custom-modern-table" id="historyTable">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Type d'acte</th>
                            <th>Livraison</th>
                            <th>Agent assigné</th>
                            <th>Date</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historique as $demande)
                            <tr>
                                <td>
                                    <span class="font-weight-bold text-navy" style="font-family: 'Courier New', monospace;">{{ $demande->reference }}</span>
                                </td>
                                <td data-search="@switch($demande->table_name)@case('naissance')Naissance@break @case('deces')Décès@break @case('mariage')Mariage@break @default{{ ucfirst($demande->table_name) }}@endswitch">
                                    <div class="d-flex align-items-center">
                                        <div class="type-icon mr-2">
                                            @switch($demande->table_name)
                                                @case('naissance') <i class="fas fa-baby text-success"></i> @break
                                                @case('deces') <i class="fas fa-cross text-danger"></i> @break
                                                @case('mariage') <i class="fas fa-heart text-pink"></i> @break
                                                @default <i class="fas fa-file text-primary"></i>
                                            @endswitch
                                        </div>
                                        <span class="small font-weight-bold">
                                            @switch($demande->table_name)
                                                @case('naissance') Naissance @break
                                                @case('deces') Décès @break
                                                @case('mariage') Mariage @break
                                                @default {{ ucfirst($demande->table_name) }}
                                            @endswitch
                                        </span>
                                    </div>
                                </td>
                                <td data-search="{{ $demande->statut_livraison ?: 'Retrait Mairie' }}">
                                    @if($demande->statut_livraison)
                                        <span class="small text-navy font-weight-bold"><i class="fas fa-truck mr-1 text-primary"></i> {{ $demande->statut_livraison }}</span>
                                    @else
                                        <span class="text-grey x-small">Retrait Mairie</span>
                                    @endif
                                </td>
                                <td>
                                    @if($demande->agent_nom)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary-light text-primary mr-2" style="width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                                {{ substr($demande->agent_nom, 0, 1) }}{{ substr($demande->agent_prenom, 0, 1) }}
                                            </div>
                                            <span class="small font-weight-bold text-navy">{{ $demande->agent_nom }}</span>
                                        </div>
                                    @else
                                        <span class="text-grey x-small italic">En attente d'assignation</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-grey small">{{ \Carbon\Carbon::parse($demande->created_at)->format('d/m/Y') }}</span>
                                    <br>
                                    <span class="x-small text-grey opacity-8">{{ \Carbon\Carbon::parse($demande->created_at)->format('H:i') }}</span>
                                </td>
                                <td class="text-right">
                                    <button class="btn btn-sm btn-light shadow-sm view-details" 
                                            style="border-radius: 10px; border: 1px solid #eee;"
                                            data-type="{{ $demande->table_name }}" 
                                            data-id="{{ $demande->id }}">
                                        <i class="fas fa-eye text-primary"></i>
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

<style>
    .custom-modern-table {
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    .custom-modern-table thead th {
        border: none;
        color: #a3aed0;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 1px;
        padding: 15px 20px;
    }
    .custom-modern-table tbody tr {
        transition: 0.3s;
    }
    .custom-modern-table tbody td {
        background: #fff;
        border: none;
        padding: 15px 20px;
        vertical-align: middle;
    }
    .custom-modern-table tbody tr td:first-child {
        border-radius: 15px 0 0 15px;
    }
    .custom-modern-table tbody tr td:last-child {
        border-radius: 0 15px 15px 0;
    }
    .custom-modern-table tbody tr:hover td {
        background: #f8faff;
        transform: scale(1.005);
    }
    
    /* DataTables Overrides */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #f4f7fe;
        border-radius: 10px;
        padding: 8px 15px;
        background: #f4f7fe;
        margin-left: 10px;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #f4f7fe;
        border-radius: 8px;
        padding: 5px;
    }
    .dataTables_wrapper .dataTables_info, 
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 20px;
        font-size: 13px;
        color: #a3aed0 !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary) !important;
        color: white !important;
        border: none !important;
        border-radius: 8px !important;
    }

    .custom-select-modern {
        border: 1px solid #f4f7fe;
        border-radius: 12px;
        padding: 8px 15px;
        background: #f4f7fe;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-navy);
        outline: none;
        cursor: pointer;
    }
</style>

@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        const table = $('#historyTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            order: [[4, 'desc']], // Adjusted index since we removed a column
            pageLength: 10,
        });

        // Filter Type d'acte
        $('#filterType').on('change', function() {
            table.column(1).search(this.value).draw();
        });

        // Filter Statut Livraison
        $('#filterLivraison').on('change', function() {
            table.column(2).search(this.value).draw();
        });

        // Event listener for details
        $('.view-details').on('click', function() {
            const type = $(this).data('type');
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Chargement...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            fetch(`{{ route('demande.details.json', ['type' => ':type', 'id' => ':id']) }}`.replace(':type', type).replace(':id', id))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: `<h4 class="text-navy-bold mb-0">Détails de la demande</h4>`,
                        html: formatDetails(data.demande, type),
                        width: '600px',
                        showConfirmButton: false,
                        showCloseButton: true,
                        borderRadius: '24px',
                    });
                }
            });
        });

        function formatDetails(data, type) {
            const statusClass = data.etat === 'traité' ? 'success' : (data.etat === 'en attente' ? 'warning' : 'danger');
            return `
                <div class="text-left p-3">
                    <div class="d-flex justify-content-between mb-4 pb-2 border-bottom">
                        <span class="text-grey font-weight-bold">Référence:</span>
                        <span class="text-navy-bold">${data.reference}</span>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-grey d-block">Type d'acte</small>
                            <span class="font-weight-bold text-navy">${type.toUpperCase()}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-grey d-block">Statut actuel</small>
                            <span class="badge badge-soft-${statusClass} py-1 px-3">${data.etat}</span>
                        </div>
                    </div>
                    <div class="bg-light p-3" style="border-radius: 15px;">
                        <h6 class="text-navy-bold mb-3 small uppercase">Informations du dossier</h6>
                        ${getSpecificFields(data, type)}
                    </div>
                    ${data.adresse_livraison ? `
                        <div class="mt-3 p-3 border" style="border-radius: 15px; border-style: dashed !important;">
                            <h6 class="text-primary font-weight-bold mb-2 small"><i class="fas fa-truck mr-2"></i>Livraison</h6>
                            <p class="x-small mb-1"><strong>Adresse:</strong> ${data.adresse_livraison}</p>
                            <p class="x-small mb-0"><strong>Contact:</strong> ${data.contact_destinataire}</p>
                        </div>
                    ` : ''}
                </div>
            `;
        }

        function getSpecificFields(data, type) {
            let html = '';
            // Basic logic for fields, adapt based on your actual data structure
            if (type === 'naissance') {
                html = `<p class="x-small mb-1"><strong>Nom:</strong> ${data.name || data.nom_enfant || 'N/A'}</p>
                        <p class="x-small mb-0"><strong>Date Naissance:</strong> ${data.DateR || data.date_naissance || 'N/A'}</p>`;
            } else if (type === 'mariage') {
                html = `<p class="x-small mb-1"><strong>Époux:</strong> ${data.nomEpoux} & ${data.nomEpouse || 'N/A'}</p>`;
            } else {
                html = `<p class="x-small mb-0"><strong>Détails:</strong> Données archivées</p>`;
            }
            return html;
        }
    });
</script>
@endpush