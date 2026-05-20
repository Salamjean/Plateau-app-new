@extends('user.layouts.template')

@section('content')
    <div class="dashboard-final container-fluid px-4 pt-4 mb-5 animate-fade-in">

        <!-- Header Section -->
        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <h1 class="text-navy-bold mb-1">{{ $title ?? 'Historique des demandes' }}</h1>
                <p class="text-grey mb-0">
                    {{ $subtitle ?? "Consultez et suivez l'évolution de toutes vos démarches administratives." }}</p>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <a href="{{ route('user.extrait.create') }}" class="btn btn-premium shadow-sm">
                    <i class="fas fa-plus-circle mr-2"></i> Nouvelle Demande
                </a>
            </div>
        </div>


        <!-- History Table Card -->
        <div class="glass-card shadow-sm border-0 mb-4" style="border-radius: 30px; overflow: hidden;">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <h5 class="text-navy-bold mb-0">Liste de vos demandes</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <select id="filterStatus" class="custom-select-modern">
                            <option value="">Tous les statuts</option>
                            <option value="en attente">En attente</option>
                            <option value="en cours">En cours</option>
                            <option value="terminé">Terminé</option>
                            <option value="rejeté">Rejeté</option>
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
                                <th>Statut</th>
                                <th>Agent assigné</th>
                                <th>Date</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($historique as $demande)
                                <tr>
                                    <td>
                                        <span class="font-weight-bold text-navy"
                                            style="font-family: 'Courier New', monospace;">{{ $demande->reference }}</span>
                                    </td>
                                    <td
                                        data-search="@switch($demande->table_name)@case('naissance')Naissance@break @case('deces')Décès@break @case('mariage')Mariage@break @default{{ ucfirst($demande->table_name) }}@endswitch">
                                        <div class="d-flex align-items-center">
                                            <div class="type-icon mr-2">
                                                @switch($demande->table_name)
                                                    @case('naissance')
                                                        <i class="fas fa-baby text-success"></i>
                                                    @break

                                                    @case('deces')
                                                        <i class="fas fa-cross text-danger"></i>
                                                    @break

                                                    @case('mariage')
                                                        <i class="fas fa-heart text-pink"></i>
                                                    @break

                                                    @default
                                                        <i class="fas fa-file text-primary"></i>
                                                @endswitch
                                            </div>
                                            <span class="small font-weight-bold">
                                                @switch($demande->table_name)
                                                    @case('naissance')
                                                        Naissance
                                                    @break

                                                    @case('deces')
                                                        Décès
                                                    @break

                                                    @case('mariage')
                                                        Mariage
                                                    @break

                                                    @default
                                                        {{ ucfirst($demande->table_name) }}
                                                @endswitch
                                            </span>
                                        </div>
                                    </td>
                                    <td data-search="{{ $demande->statut_livraison ?: 'Retrait Mairie' }}">
                                        @if ($demande->statut_livraison)
                                            <span class="small text-navy font-weight-bold"><i
                                                    class="fas fa-truck mr-1 text-primary"></i>
                                                {{ $demande->statut_livraison }}</span>
                                        @else
                                            <span class="text-grey x-small">Retrait Mairie</span>
                                        @endif
                                    </td>
                                    <td data-search="{{ $demande->etat }}">
                                        @php
                                            $statusClass = 'secondary';
                                            $statusIcon = 'clock';
                                            switch (strtolower($demande->etat)) {
                                                case 'terminé':
                                                    $statusClass = 'success';
                                                    $statusIcon = 'check-circle';
                                                    break;
                                                case 'en attente':
                                                    $statusClass = 'warning';
                                                    $statusIcon = 'pause-circle';
                                                    break;
                                                case 'en cours':
                                                    $statusClass = 'info';
                                                    $statusIcon = 'spinner fa-spin';
                                                    break;
                                                case 'rejeté':
                                                    $statusClass = 'danger';
                                                    $statusIcon = 'times-circle';
                                                    break;
                                            }
                                        @endphp
                                        <span class="badge badge-soft-{{ $statusClass }} py-2 px-3"
                                            style="font-size: 10px; border-radius: 50px;">
                                            <i class="fas fa-{{ $statusIcon }} mr-1"></i> {{ ucfirst($demande->etat) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($demande->agent_nom)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary-light text-primary mr-2"
                                                    style="width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                                    {{ substr($demande->agent_nom, 0, 1) }}{{ substr($demande->agent_prenom, 0, 1) }}
                                                </div>
                                                <span
                                                    class="small font-weight-bold text-navy">{{ $demande->agent_nom }}</span>
                                            </div>
                                        @else
                                            <span class="text-grey x-small italic">En attente d'assignation</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="text-grey small">{{ \Carbon\Carbon::parse($demande->created_at)->format('d/m/Y') }}</span>
                                        <br>
                                        <span
                                            class="x-small text-grey opacity-8">{{ \Carbon\Carbon::parse($demande->created_at)->format('H:i') }}</span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('demande.details.view', ['type' => $demande->table_name, 'id' => $demande->id]) }}"
                                            class="btn btn-sm btn-light shadow-sm"
                                            style="border-radius: 10px; border: 1px solid #eee;">
                                            <i class="fas fa-eye text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                                                <p class="text-grey mb-0">Aucune demande effectuée</p>
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

        <style>
            .custom-modern-table {
                border-collapse: separate;
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
                width: 100%;
            }

            @media (max-width: 768px) {
                .custom-modern-table {
                    min-width: 800px;
                    /* Force la largeur pour le défilement horizontal */
                }
            }

            @media (max-width: 576px) {
                .text-navy-bold {
                    font-size: 1.5rem;
                }

                .custom-modern-table tbody td {
                    padding: 12px 10px;
                    font-size: 13px;
                }

                .type-icon {
                    font-size: 14px;
                    margin-right: 5px !important;
                }

                .badge {
                    padding: 4px 8px !important;
                    font-size: 10px !important;
                }

                .btn-premium {
                    width: 100%;
                    text-align: center;
                }
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
                    order: [
                        [5, 'desc']
                    ], // Tri par date (6ème colonne)
                    pageLength: 10,
                });

                // Filter Statut Demande
                $('#filterStatus').on('change', function() {
                    table.column(3).search(this.value).draw();
                });

            });
        </script>
    @endpush
