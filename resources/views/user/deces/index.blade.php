@extends('user.layouts.template')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <style>
        :root {
            --primary: #1977cc;
            --primary-light: #eef5fc;
            --success: #28a745;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-navy: #1a365d;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.5);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            animation: fadeInDown 0.8s ease-out;
        }

        .page-title {
            color: var(--text-navy);
            font-weight: 800;
            font-size: 1.8rem;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .btn-add-premium {
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(25, 119, 204, 0.2);
            text-decoration: none;
        }

        .btn-add-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(25, 119, 204, 0.3);
            color: white;
        }

        .glass-container {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .table-responsive {
            border-radius: 16px;
            overflow-x: auto !important;
        }

        #decesTable {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 12px !important;
            background: transparent !important;
        }

        #decesTable thead th {
            background: #f8fafc !important;
            color: #64748b !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 15px !important;
            border: none !important;
        }

        #decesTable tbody tr {
            background: white !important;
            transition: 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        #decesTable tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            background: #f8fbff !important;
        }

        #decesTable tbody td {
            padding: 1.2rem !important;
            border: none !important;
            vertical-align: middle !important;
        }

        #decesTable tbody td:first-child {
            border-radius: 16px 0 0 16px !important;
        }

        #decesTable tbody td:last-child {
            border-radius: 0 16px 16px 0 !important;
        }

        .badge-status {
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .badge-en-attente {
            background: #fff7ed;
            color: #c2410c;
        }

        .badge-en-cours {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .badge-termine {
            background: #f0fdf4;
            color: #15803d;
        }

        .badge-rejete {
            background: #fef2f2;
            color: #b91c1c;
        }

        .delivery-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #f8fafc;
            border-radius: 8px;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .btn-action {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
            border: none;
            text-decoration: none;
        }

        .btn-edit {
            background: #eff6ff;
            color: #2563eb;
        }

        .btn-edit:hover {
            background: #2563eb;
            color: #fff;
        }

        .btn-delete {
            background: #fef2f2;
            color: #dc2626;
        }

        .btn-delete:hover {
            background: #dc2626;
            color: #fff;
        }

        .doc-thumbnail {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .doc-thumbnail:hover {
            transform: scale(1.15);
            z-index: 10;
        }

        .info-cell {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .info-label {
            font-size: 0.7rem;
            color: #94a3b8;
            text-transform: uppercase;
            font-weight: 700;
        }

        .info-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-navy);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dataTables_filter input {
            border-radius: 12px !important;
            padding: 8px 16px !important;
            border: 1px solid #e2e8f0 !important;
            background: #fff !important;
            margin-left: 10px !important;
        }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
                margin-bottom: 1.5rem;
            }

            .btn-add-premium {
                width: 100%;
                justify-content: center;
            }

            .glass-container {
                padding: 1.25rem;
                margin-left: -5px;
                margin-right: -5px;
            }

            #decesTable {
                min-width: 1000px;
                /* Plus large pour accueillir toutes les colonnes */
            }

            .page-title {
                font-size: 1.4rem;
            }
        }

        /* Stat summary cards */
        .stat-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-radius: 16px;
            background: #fff;
            border: 1.5px solid #f0f4fa;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transition: 0.25s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-navy);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #94a3b8;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-total .stat-icon {
            background: #eef5fc;
            color: var(--primary);
        }

        .stat-pending .stat-icon {
            background: #fff7ed;
            color: #ea580c;
        }

        .stat-progress .stat-icon {
            background: #eff6ff;
            color: #2563eb;
        }

        .stat-done .stat-icon {
            background: #f0fdf4;
            color: #16a34a;
        }

        .stat-rejected .stat-icon {
            background: #fef2f2;
            color: #dc2626;
        }
    </style>

    <div class="dashboard-final container-fluid py-4 animate-fade-in">
        <div class="page-header">
            <h1 class="page-title">Demandes d'acte de décès</h1>
            <a href="{{ route('user.extrait.deces.create') }}" class="btn-add-premium">
                <i class="fas fa-plus"></i> Nouvelle demande
            </a>
        </div>

        @php
            $total = $deces->count();
            $enAttente = $deces->where('etat', 'en attente')->count();
            $enCours = $deces->where('etat', 'réçu')->count();
            $termine = $deces->where('etat', 'terminé')->count();
            $rejete = $deces->where('etat', 'rejetée')->count();
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-6 col-md">
                <div class="stat-card stat-total">
                    <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                    <div>
                        <div class="stat-value">{{ $total }}</div>
                        <div class="stat-label">Total</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card stat-pending">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <div class="stat-value">{{ $enAttente }}</div>
                        <div class="stat-label">En attente</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card stat-progress">
                    <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                    <div>
                        <div class="stat-value">{{ $enCours }}</div>
                        <div class="stat-label">En cours</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card stat-done">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="stat-value">{{ $termine }}</div>
                        <div class="stat-label">Terminées</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card stat-rejected">
                    <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                    <div>
                        <div class="stat-value">{{ $rejete }}</div>
                        <div class="stat-label">Rejetées</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-container">
            <div class="table-responsive">
                <table id="decesTable" class="table">
                    <thead>
                        <tr>
                            <th class="text-center">Référence</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Défunt</th>
                            <th class="text-center">Parents</th>
                            <th class="text-center">Registre</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Retrait</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deces as $item)
                            <tr>
                                <td class="text-center">
                                    <span class="fw-bold">{{ $item->reference }}</span>
                                    <div class="small text-muted">{{ $item->quantite }} copie(s)</div>
                                </td>
                                <td class="text-center">
                                    @if ($item->type === 'groupee')
                                        <span class="badge bg-success rounded-pill small text-white">Simple +
                                            Intégrale</span>
                                    @elseif($item->type === 'integrale')
                                        <span class="badge bg-primary rounded-pill small text-white">Intégrale</span>
                                    @elseif($item->type === 'simple')
                                        <span class="badge bg-info rounded-pill small text-white">Simple</span>
                                    @else
                                        {{-- Fallback pour les anciennes données --}}
                                        <span
                                            class="badge {{ $item->type == 'integral' ? 'bg-primary' : 'bg-info' }} rounded-pill small text-white">
                                            {{ $item->type == 'integral' ? 'Intégrale' : 'Simple' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="info-cell">
                                        <span class="info-value">{{ $item->name }}</span>
                                        <span class="small text-muted"><i
                                                class="fas fa-map-marker-alt me-1"></i>{{ $item->commune_deces ?: 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="info-cell">
                                        <span class="small"><strong class="text-muted">P :</strong>
                                            {{ $item->nom_prenoms_pere ?: '-' }}</span>
                                        <span class="small"><strong class="text-muted">M :</strong>
                                            {{ $item->nom_prenoms_mere ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="info-cell">
                                        <span class="info-value">{{ $item->numberR ?: '-' }}</span>
                                        <span
                                            class="small text-muted">{{ $item->dateR ? date('d/m/Y', strtotime($item->dateR)) : '-' }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($item->etat == 'rejetée')
                                        <span class="badge-status badge-rejete">REJETÉ</span>
                                    @elseif($item->etat == 'en attente')
                                        <span class="badge-status badge-en-attente">EN ATTENTE</span>
                                    @elseif($item->etat == 'réçu')
                                        <span class="badge-status badge-en-cours">EN COURS</span>
                                    @else
                                        <span class="badge-status badge-termine">TERMINÉ</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="delivery-badge">
                                        <i
                                            class="fas {{ $item->choix_option == 'livraison' ? 'fa-motorcycle' : 'fa-university' }}"></i>
                                        {{ $item->choix_option == 'livraison' ? 'Livraison' : 'Mairie' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @if ($item->peut_modifier)
                                            <button
                                                onclick="showModificationPopup('{{ $item->id }}', {{ json_encode($item) }})"
                                                class="btn-action btn-edit" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif

                                        @if ($item->etat == 'en attente' || $item->etat == 'rejetée')
                                            <button
                                                onclick="confirmDelete('{{ route('user.extrait.deces.delete', $item->id) }}')"
                                                class="btn-action btn-delete" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modale Image -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 24px; border: none; overflow: hidden;">
                <div class="modal-body p-0 text-center bg-dark">
                    <img id="modalImage" src="" style="max-width: 100%; max-height: 85vh;">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal"></button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#decesTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                order: [
                    [0, 'desc']
                ],
                dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rtip'
            });
        });

        function confirmDelete(url) {
            Swal.fire({
                title: 'Supprimer cette demande ?',
                text: "Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Supprimer',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
        }

        function showModificationPopup(demandeId, demande) {
            let champsAModifier = JSON.parse(demande.champs_a_modifier || '[]');
            const fieldLabels = {
                'name': 'Nom défunt',
                'numberR': 'N° Registre',
                'dateR': 'Date Registre',
                'commune_deces': 'Commune décès',
                'quantite': 'Quantité',
                'CNIdfnt': 'CNI défunt',
                'CNIdcl': 'Certificat médical',
                'documentMariage': 'Acte mariage',
                'RequisPolice': 'Réquisition police'
            };

            let formHtml = `<form id="modificationForm" class="text-start" enctype="multipart/form-data">`;
            champsAModifier.forEach(field => {
                const label = fieldLabels[field] || field;
                let val = demande[field] || '';
                if (field === 'dateR' && val) val = new Date(val).toISOString().split('T')[0];

                formHtml += `<div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase">${label}</label>`;

                if (['CNIdfnt', 'CNIdcl', 'documentMariage', 'RequisPolice'].includes(field)) {
                    formHtml +=
                        `<input type="file" name="${field}" class="form-control" accept=".jpg,.jpeg,.png,.pdf">`;
                } else if (field === 'dateR') {
                    formHtml += `<input type="date" name="${field}" class="form-control" value="${val}">`;
                } else {
                    formHtml +=
                        `<input type="${field==='quantite'?'number':'text'}" name="${field}" class="form-control" value="${val}">`;
                }
                formHtml += `</div>`;
            });
            formHtml += `</form>`;

            Swal.fire({
                title: 'Modifier la demande',
                html: formHtml,
                showCancelButton: true,
                confirmButtonText: 'Enregistrer',
                confirmButtonColor: '#1977cc',
                preConfirm: () => new FormData(document.getElementById('modificationForm'))
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = result.value;
                    formData.append('_method', 'PUT');

                    Swal.showLoading();
                    fetch(`/user/extrait/deces/${demandeId}/modifier`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Succès', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Erreur', data.message, 'error');
                            }
                        });
                }
            });
        }
    </script>

    @if (session('success'))
        <script>
            Swal.fire('Succès', "{{ session('success') }}", 'success');
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire('Erreur', "{{ session('error') }}", 'error');
        </script>
    @endif
@endsection
