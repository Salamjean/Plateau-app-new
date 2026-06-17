@extends('user.layouts.template')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <style>
        :root {
            --primary: #1f4083;
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

        #mariageTable {
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 12px !important;
            background: transparent !important;
        }

        #mariageTable thead th {
            background: #f8fafc !important;
            color: #64748b !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 15px !important;
            border: none !important;
        }

        #mariageTable tbody tr {
            background: white !important;
            transition: 0.3s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        #mariageTable tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            background: #f8fbff !important;
        }

        #mariageTable tbody td {
            padding: 1.2rem !important;
            border: none !important;
            vertical-align: middle !important;
        }

        #mariageTable tbody td:first-child {
            border-radius: 16px 0 0 16px !important;
        }

        #mariageTable tbody td:last-child {
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

        .badge-type {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }

        .badge-type-simple {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .badge-type-integral {
            background: #f5f3ff;
            color: #7c3aed;
        }

        .badge-type-both {
            background: linear-gradient(90deg, #eff6ff, #f5f3ff);
            color: #4338ca;
            border: 1px solid #c7d2fe;
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

        .agent-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .agent-avatar {
            width: 32px;
            height: 32px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
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

        /* Custom search for DataTables */
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

            #mariageTable {
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
            <h1 class="page-title">Mes actes de mariage</h1>
            <a href="{{ route('user.extrait.mariage.create') }}" class="btn-add-premium">
                <i class="fas fa-plus"></i> Nouvelle demande
            </a>
        </div>

        @php
            $total = $allMariages->count();
            $enAttente = $allMariages->where('etat', 'en attente')->count();
            $enCours = $allMariages->where('etat', 'réçu')->count();
            $termine = $allMariages->where('etat', 'terminé')->count();
            $rejete = $allMariages->where('etat', 'rejetée')->count();
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
                <table id="mariageTable" class="table">
                    <thead>
                        <tr>
                            <th class="text-center">Référence</th>
                            <th class="text-center">Quantité</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Conjoint(e)</th>
                            <th class="text-center">Documents</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Mode</th>
                            <th class="text-center">Agent</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($allMariages as $mariage)
                            <tr>
                                <td class="text-center">
                                    <span class="fw-bold">{{ $mariage->reference }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ $mariage->quantite }}</span> <small
                                        class="text-muted">copie(s)</small>
                                </td>
                                <td class="text-center">
                                    @if ($mariage->type === 'groupee')
                                        <span class="badge-type badge-type-both"><i class="fas fa-layer-group"></i> Simple +
                                            Intégrale</span>
                                    @elseif($mariage->type === 'integrale')
                                        <span class="badge-type badge-type-integral"><i class="fas fa-scroll"></i>
                                            Intégrale</span>
                                    @elseif($mariage->type === 'simple')
                                        <span class="badge-type badge-type-simple"><i class="fas fa-file-alt"></i>
                                            Simple</span>
                                    @else
                                        @if ($mariage->typeDemande === 'simpleIntegrale' || $mariage->typeDemande === 'groupee')
                                            <span class="badge-type badge-type-both"><i class="fas fa-layer-group"></i>
                                                Simple + Intégrale</span>
                                        @elseif($mariage->typeDemande === 'copieIntegrale' || $mariage->typeDemande === 'integrale' || $mariage->nomEpoux)
                                            <span class="badge-type badge-type-integral"><i class="fas fa-scroll"></i>
                                                Intégrale</span>
                                        @else
                                            <span class="badge-type badge-type-simple"><i class="fas fa-file-alt"></i>
                                                Simple</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-navy">{{ $mariage->nomEpoux ?: 'N/A' }}</span>
                                        <small class="text-muted">{{ $mariage->prenomEpoux }}</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if ($mariage->pieceIdentite)
                                            @php $ext = pathinfo($mariage->pieceIdentite, PATHINFO_EXTENSION); @endphp
                                            @if ($ext === 'pdf')
                                                <a href="{{ asset('storage/' . $mariage->pieceIdentite) }}" target="_blank"
                                                    class="btn-action btn-edit" title="Voir CNI (PDF)">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            @else
                                                <img src="{{ asset('storage/' . $mariage->pieceIdentite) }}"
                                                    class="doc-thumbnail" onclick="showImage(this)"
                                                    title="Pièce d'identité">
                                            @endif
                                        @endif

                                        @if ($mariage->extraitMariage)
                                            @php $extE = pathinfo($mariage->extraitMariage, PATHINFO_EXTENSION); @endphp
                                            @if ($extE === 'pdf')
                                                <a href="{{ asset('storage/' . $mariage->extraitMariage) }}"
                                                    target="_blank" class="btn-action btn-edit"
                                                    title="Voir Ancien Acte (PDF)">
                                                    <i class="fas fa-file-contract"></i>
                                                </a>
                                            @else
                                                <img src="{{ asset('storage/' . $mariage->extraitMariage) }}"
                                                    class="doc-thumbnail" onclick="showImage(this)" title="Ancien acte">
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($mariage->etat == 'rejetée')
                                        <span class="badge-status badge-rejete"
                                            title="Cliquez sur modifier pour voir les motifs">REJETÉ</span>
                                    @elseif($mariage->etat == 'en attente')
                                        <span class="badge-status badge-en-attente">EN ATTENTE</span>
                                    @elseif($mariage->etat == 'réçu')
                                        <span class="badge-status badge-en-cours">EN COURS</span>
                                    @else
                                        <span class="badge-status badge-termine">TERMINÉ</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="delivery-badge">
                                        <i
                                            class="fas {{ $mariage->choix_option == 'livraison' ? 'fa-motorcycle' : 'fa-university' }}"></i>
                                        {{ $mariage->choix_option == 'livraison' ? 'Livraison' : 'Mairie' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($mariage->agent)
                                        <div class="agent-info justify-content-center">
                                            <div class="agent-avatar">{{ substr($mariage->agent->name, 0, 1) }}</div>
                                            <span class="small fw-bold">{{ $mariage->agent->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">Non attribué</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('demande.details.view', ['type' => 'mariage', 'id' => $mariage->id]) }}"
                                            class="btn-action shadow-sm" title="Détails"
                                            style="background: #eef5fc; color: #1f4083;">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if ($mariage->peut_modifier)
                                            <button
                                                onclick="showModificationPopup('{{ $mariage->id }}', {{ json_encode($mariage) }})"
                                                class="btn-action btn-edit" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @elseif (is_null($mariage->agent_id))
                                            <a href="{{ route('user.extrait.mariage.edit', $mariage->id) }}"
                                                class="btn-action btn-edit" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif

                                        @if ($mariage->etat == 'en attente' || $mariage->etat == 'rejetée')
                                            <button
                                                onclick="confirmDelete('{{ route('user.extrait.mariage.delete', $mariage->id) }}')"
                                                class="btn-action btn-delete" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                                        <p class="text-grey mb-0">Aucune demande de mariage effectuée</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#mariageTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                order: [
                    [0, 'desc']
                ],
                pageLength: 10,
                dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rtip'
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
                title: 'Confirmer la suppression ?',
                text: "Cette action est irréversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Supprimer',
                cancelButtonText: 'Annuler',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }

        function showModificationPopup(demandeId, demande) {
            let champsAModifier = JSON.parse(demande.champs_a_modifier || '[]');

            // Si la demande n'a pas d'agent assigné et n'a pas de champs spécifiques à modifier définis par l'IA (peut_modifier est faux)
            // On permet de modifier tous les champs principaux.
            if (!demande.agent_id && !demande.peut_modifier) {
                champsAModifier = ['typeDemande', 'nomEpoux', 'prenomEpoux', 'dateNaissanceEpoux', 'lieuNaissanceEpoux',
                    'quantite', 'pieceIdentite', 'extraitMariage', 'commune'
                ];
            }

            // On s'assure que la quantité est toujours sélectionnable/modifiable pour calculer la différence
            if (!champsAModifier.includes('quantite')) {
                champsAModifier.push('quantite');
            }

            const fieldLabels = {
                'typeDemande': 'Type de demande',
                'nomEpoux': 'Nom conjoint',
                'prenomEpoux': 'Prénom conjoint',
                'dateNaissanceEpoux': 'Date naissance conjoint',
                'lieuNaissanceEpoux': 'Lieu naissance conjoint',
                'quantite': 'Quantité',
                'pieceIdentite': 'Pièce d\'identité',
                'extraitMariage': 'Ancien acte',
                'CMU': 'Numéro NNI',
                'commune': 'Commune'
            };

            let formHtml = '';
            let motifAffichage = '';
            if (demande.motif_de_rejet) {
                let text = demande.motif_de_rejet;
                if (text.includes('Commentaire additionnel :')) {
                    motifAffichage = text.split('Commentaire additionnel :')[1].trim();
                } else if (text.includes('Commentaire additionnel:')) {
                    motifAffichage = text.split('Commentaire additionnel:')[1].trim();
                } else {
                    text = text.replace(
                        /Les champs suivants contiennent des informations incorrectes ou incomplètes\s*:?/gi, '');
                    const lines = text.split('\n').map(l => l.trim()).filter(l => l && !l.startsWith('•'));
                    motifAffichage = lines.join('\n').trim();
                }
            }

            if (motifAffichage) {
                formHtml += `<div class="alert alert-danger p-2  text-center" style="border-radius: 12px; background-color: #fff5f5; border: 1px solid #fed7d7; color: #c53030;">
                        <p class="mb-0 small text-start" style="white-space: pre-wrap; font-weight: 500; line-height: 1.5; display: flex; justify-content: center;">
                            <strong style="color: #9b2c2c;"><i class="fas fa-exclamation-triangle"></i> Motif :</strong> ${motifAffichage}
                        </p>
                    </div>`;
            }
            formHtml += `<form id="modificationForm" class="text-start" enctype="multipart/form-data">`;
            champsAModifier.forEach(field => {
                const label = fieldLabels[field] || field;
                let val = demande[field] || '';
                if (field === 'dateNaissanceEpoux' && val) val = new Date(val).toISOString().split('T')[0];

                formHtml += `<div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">${label}</label>`;

                if (field === 'typeDemande') {
                    formHtml +=
                        `<select name="${field}" class="form-select"><option value="simple" ${val === 'simple' ? 'selected' : ''}>Simple</option><option value="integrale" ${val === 'integrale' ? 'selected' : ''}>Intégrale</option><option value="groupee" ${val === 'groupee' ? 'selected' : ''}>Simple + Intégrale</option></select>`;
                } else if (['pieceIdentite', 'extraitMariage'].includes(field)) {
                    formHtml +=
                        `<input type="file" name="${field}" class="form-control" accept=".jpg,.jpeg,.png,.pdf">`;
                } else if (field === 'dateNaissanceEpoux') {
                    formHtml += `<input type="date" name="${field}" class="form-control" value="${val}">`;
                } else {
                    formHtml +=
                        `<input type="${field === 'quantite' ? 'number' : 'text'}" name="${field}" class="form-control" value="${val}">`;
                }
                formHtml += `</div>`;
            });

            // Ajouter les champs non modifiés en tant que champs cachés pour éviter qu'ils soient écrasés ou qu'ils échouent la validation
            const allPossibleFields = [
                'typeDemande', 'pour', 'relation', 'nomEpoux', 'prenomEpoux',
                'dateNaissanceEpoux', 'lieuNaissanceEpoux', 'nomEpouse', 'prenomEpouse',
                'dateNaissanceEpouse', 'lieuNaissanceEpouse', 'commune', 'commune_mariage',
                'qty_simple', 'qty_integral', 'quantite', 'choix_option', 'CMU'
            ];

            allPossibleFields.forEach(field => {
                if (!champsAModifier.includes(field)) {
                    if (field === 'qty_simple' || field === 'qty_integral') {
                        if (champsAModifier.includes('quantite')) return;
                    }
                    if (field === 'quantite' && (champsAModifier.includes('qty_simple') || champsAModifier.includes(
                            'qty_integral'))) return;

                    let val = demande[field] !== null && demande[field] !== undefined ? demande[field] : '';
                    if (['dateNaissanceEpoux', 'dateNaissanceEpouse'].includes(field) && val) val = new Date(val)
                        .toISOString().split('T')[0];
                    formHtml += `<input type="hidden" name="${field}" value="${val}">`;
                }
            });

            formHtml += `
            <div id="payment-section-modification" style="display: none; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #cbd5e0;">
                <div style="background: #ebf8ff; border: 1px solid #bee3f8; padding: 12px; border-radius: 8px; margin-bottom: 12px; color: #2b6cb0;">
                    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 0.95rem;">
                        <span>Reste à payer :</span>
                        <span id="modification-reste-payer-text">0 FCFA</span>
                    </div>
                </div>
                
                <h5 style="font-size: 0.85rem; font-weight: bold; color: #1f4083; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left;">💳 Moyen de paiement</h5>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 10px;">
                    <button type="button" id="btn-mod-pay-wave" class="payment-mod-method-btn" style="background: #eff6ff; border: 2px solid #1e3a8a; border-radius: 8px; padding: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;">
                        <img src="{{ asset('assets/assets/img/Wave.png') }}" alt="Wave" style="height: 25px; object-fit: contain;">
                    </button>
                    <button type="button" id="btn-mod-pay-mtn" class="payment-mod-method-btn" style="background: white; border: 1px solid #edf2f7; border-radius: 8px; padding: 6px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px;">
                        <img src="{{ asset('assets/assets/img/MTN.png') }}" alt="MTN" style="height: 25px; object-fit: contain;">
                    </button>
                </div>
                <input type="hidden" name="payment_method" id="mod-payment_method" value="wave">
                <div id="mod-payment-phone-container" style="display: none; margin-top: 8px; text-align: left;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #4a5568; margin-bottom: 3px; text-transform: uppercase;">Numéro MTN Money</label>
                    <input name="mtn_number" id="mod-mtn_number" class="form-control" style="font-size: 0.85rem; height: 35px; border-radius: 6px;" placeholder="Ex: 0707070707" value="${demande.contact_destinataire || demande.number || ''}" maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                </div>
            </div>
            `;

            formHtml += `</form>`;

            Swal.fire({
                title: 'Modifier la demande',
                html: formHtml,
                showCancelButton: true,
                confirmButtonText: 'Enregistrer',
                confirmButtonColor: '#1f4083',
                cancelButtonText: 'Annuler',
                didOpen: (dom) => {
                    const qtyInput = dom.querySelector('[name="quantite"]');
                    const typeSelect = dom.querySelector('[name="typeDemande"]');
                    const optionInput = dom.querySelector('[name="choix_option"]');
                    const paymentSection = dom.querySelector('#payment-section-modification');
                    const restePayerText = dom.querySelector('#modification-reste-payer-text');
                    const btnWave = dom.querySelector('#btn-mod-pay-wave');
                    const btnMtn = dom.querySelector('#btn-mod-pay-mtn');
                    const inputPaymentMethod = dom.querySelector('#mod-payment_method');
                    const phoneContainer = dom.querySelector('#mod-payment-phone-container');

                    if (btnWave && btnMtn) {
                        btnWave.addEventListener('click', () => {
                            btnWave.style.background = '#eff6ff';
                            btnWave.style.border = '2px solid #1e3a8a';
                            btnMtn.style.background = 'white';
                            btnMtn.style.border = '1px solid #edf2f7';
                            inputPaymentMethod.value = 'wave';
                            phoneContainer.style.display = 'none';
                        });

                        btnMtn.addEventListener('click', () => {
                            btnMtn.style.background = '#eff6ff';
                            btnMtn.style.border = '2px solid #1e3a8a';
                            btnWave.style.background = 'white';
                            btnWave.style.border = '1px solid #edf2f7';
                            inputPaymentMethod.value = 'mtn';
                            phoneContainer.style.display = 'block';
                        });
                    }

                    const isDejaPaye = !['non_paye', 'paiement_en_attente', 'en attente de paiement'].includes(
                        demande.etat);
                    const ancienMontantPaye = isDejaPaye ? (parseFloat(demande.montant_timbre || 0) +
                        parseFloat(demande.montant_livraison || 0)) : 0;
                    const quantiteInitiale = parseInt(demande.quantite) || 1;
                    const freeCountInitial = parseInt(demande.free_timbres_count) || 0;

                    function updateCalcul() {
                        const nouvelleQuantite = qtyInput ? (parseInt(qtyInput.value) || 1) : quantiteInitiale;
                        const nouvelleOption = optionInput ? optionInput.value : (demande.choix_option ||
                            'Retrait sur place');

                        const nouveauMontantTimbres = Math.max(0, nouvelleQuantite - freeCountInitial) * 500;
                        const nouveauMontantLivraison = (nouvelleOption === 'livraison') ? (parseFloat(demande
                            .montant_livraison) || 1000) : 0;

                        const nouveauMontantTotal = nouveauMontantTimbres + nouveauMontantLivraison;
                        const resteAPayer = Math.max(0, nouveauMontantTotal - ancienMontantPaye);

                        if (resteAPayer > 0) {
                            restePayerText.innerText = resteAPayer + ' FCFA';
                            paymentSection.style.display = 'block';
                            Swal.getConfirmButton().innerText = 'Enregistrer & Payer (' + resteAPayer + ' F)';
                        } else {
                            paymentSection.style.display = 'none';
                            Swal.getConfirmButton().innerText = 'Enregistrer';
                        }
                    }

                    if (qtyInput) qtyInput.addEventListener('input', updateCalcul);
                    if (typeSelect) typeSelect.addEventListener('change', updateCalcul);
                    if (optionInput) optionInput.addEventListener('change', updateCalcul);

                    updateCalcul();
                },
                preConfirm: () => {
                    const form = document.getElementById('modificationForm');
                    const formData = new FormData(form);

                    const quantiteInput = form.querySelector('input[name="quantite"]');
                    if (quantiteInput) {
                        const typeInput = form.querySelector('[name="typeDemande"]');
                        const typeVal = typeInput ? typeInput.value : (demande.typeDemande || 'simple');
                        const qtyVal = parseInt(quantiteInput.value) || 1;

                        if (typeVal === 'simple') {
                            formData.set('qty_simple', qtyVal);
                            formData.set('qty_integral', 0);
                        } else if (typeVal === 'integrale') {
                            formData.set('qty_simple', 0);
                            formData.set('qty_integral', qtyVal);
                        } else if (typeVal === 'groupee') {
                            const oldSimple = parseInt(demande.qty_simple) || 1;
                            const oldIntegral = parseInt(demande.qty_integral) || 1;
                            const oldTotal = oldSimple + oldIntegral;
                            if (oldTotal > 0) {
                                const ratio = qtyVal / oldTotal;
                                formData.set('qty_simple', Math.round(oldSimple * ratio) || 1);
                                formData.set('qty_integral', Math.round(oldIntegral * ratio) || 1);
                            } else {
                                formData.set('qty_simple', Math.ceil(qtyVal / 2));
                                formData.set('qty_integral', Math.floor(qtyVal / 2));
                            }
                        }
                    }

                    const paymentMethodInput = form.querySelector('#mod-payment_method');
                    if (paymentMethodInput && paymentMethodInput.value === 'mtn') {
                        const mtnNumberInput = form.querySelector('#mod-mtn_number');
                        const mtnVal = mtnNumberInput ? mtnNumberInput.value.replace(/\s+/g, '') : '';
                        if (!/^\d{10}$/.test(mtnVal)) {
                            Swal.showValidationMessage(
                                'Veuillez entrer un numéro MTN Money valide à 10 chiffres.');
                            return false;
                        }
                        formData.set('mtn_number', mtnVal);
                    }

                    return formData;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = result.value;
                    formData.append('_method', 'PUT');

                    Swal.showLoading();
                    fetch(`/user/extrait/mariage/${demandeId}/modifier`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => {
                            if (!r.ok) {
                                return r.json().then(err => {
                                    throw err;
                                });
                            }
                            return r.json();
                        })
                        .then(data => {
                            if (data.success) {
                                if (data.redirect_url) {
                                    window.location.href = data.redirect_url;
                                } else {
                                    Swal.fire('Succès', data.message, 'success').then(() => location.reload());
                                }
                            } else {
                                Swal.fire('Erreur', data.message || 'Une erreur est survenue.', 'error');
                            }
                        })
                        .catch(err => {
                            Swal.close();
                            const msg = err.message || (err.errors ? Object.values(err.errors).flat().join(
                                '<br>') : 'Une erreur de communication est survenue.');
                            Swal.fire('Erreur', msg, 'error');
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
