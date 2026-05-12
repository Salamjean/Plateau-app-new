@extends('user.layouts.template')

@section('content')
    <!-- Styles et Scripts -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    
    <style>
        :root {
            --primary: #1977cc;
            --primary-light: #eef5fc;
            --success: #28a745;
            --warning: #f39c12;
            --danger: #e74c3c;
            --text-navy: #1a365d;
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

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            color: var(--text-navy);
            font-weight: 800;
            font-size: 1.5rem;
            margin: 0;
        }

        .btn-add-premium {
            background: linear-gradient(135deg, var(--primary), #0d4a85);
            color: white !important;
            padding: 10px 25px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 5px 15px rgba(25, 119, 204, 0.2);
        }

        .btn-add-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(25, 119, 204, 0.3);
        }

        /* Table Styling */
        #naissanceTable_wrapper .dataTables_length, 
        #naissanceTable_wrapper .dataTables_filter {
            margin-bottom: 1.5rem;
            font-weight: 600;
            color: var(--text-navy);
        }

        #naissanceTable {
            border: none !important;
            border-collapse: separate !important;
            border-spacing: 0 10px !important;
        }

        #naissanceTable thead th {
            background: #f8fafc !important;
            color: #64748b !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 15px !important;
            border: none !important;
        }

        #naissanceTable tbody tr {
            background: white !important;
            transition: 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        #naissanceTable tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            background: #f8fbff !important;
        }

        #naissanceTable tbody td {
            padding: 15px !important;
            border: none !important;
            vertical-align: middle !important;
            color: var(--text-navy);
            font-size: 0.9rem;
        }

        #naissanceTable tbody td:first-child { border-radius: 12px 0 0 12px; }
        #naissanceTable tbody td:last-child { border-radius: 0 12px 12px 0; }

        /* Badges */
        .badge-status {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-en-attente { background: #fff7ed; color: #9a3412; }
        .badge-en-cours { background: #f0f9ff; color: #0369a1; }
        .badge-rejete { background: #fef2f2; color: #991b1b; }
        .badge-termine { background: #f0fdf4; color: #166534; }

        .delivery-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f1f5f9;
            color: #475569;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* Action Buttons */
        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-edit { background: #fef3c7; color: #92400e; }
        .btn-delete { background: #fee2e2; color: #991b1b; }
        
        .btn-action:hover {
            transform: scale(1.1);
        }

        .doc-preview {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .marquee-container {
            background: #fff5f5;
            border-radius: 12px;
            padding: 10px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--danger);
        }
    </style>

    @if ($naissances->contains(fn($n) => $n->archived_at))
        <div class="marquee-container">
            <marquee behavior="scroll" direction="left" scrollamount="5">
                @foreach ($naissances as $naissance)
                    @if ($naissance->archived_at)
                        <span class="text-danger fw-bold mx-4">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Rejet ({{ $naissance->name }} {{ $naissance->prenom }}) : 
                            {{ $naissance->autre_motif_text ?? $naissance->motif_annulation }}
                        </span>
                    @endif
                @endforeach
            </marquee>
        </div>
    @endif

    <div class="glass-container">
        <div class="page-header">
            <h4 class="page-title">Mes demandes d'actes de naissance</h4>
            <a href="{{ route('user.extrait.create') }}" class="btn-add-premium">
                <i class="fas fa-plus"></i> Nouvelle demande
            </a>
        </div>

        <div class="table-responsive">
            <table id="naissanceTable" class="table">
                <thead>
                    <tr>
                        <th class="text-center">Référence</th>
                        <th class="text-center">Bénéficiaire</th>
                        <th class="text-center">Type & Quantité</th>
                        <th class="text-center">Informations</th>
                        <th class="text-center">Document</th>
                        <th class="text-center">Statut</th>
                        <th class="text-center">Mode</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($naissances as $naissance)
                        <tr>
                            <td class="text-center"><span class="fw-bold">{{ $naissance->reference }}</span></td>
                            <td class="text-center">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $naissance->name }} {{ $naissance->prenom }}</span>
                                    <small class="text-muted">{{ $naissance->pour }}</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-column">
                                    <span>{{ $naissance->type == 'simple' ? 'Acte Simple' : 'Extrait Intégral' }}</span>
                                    <small class="fw-bold text-primary">{{ $naissance->quantite }} copie(s)</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <small>
                                    <i class="fas fa-hashtag text-muted"></i> {{ $naissance->number }}<br>
                                    <i class="fas fa-calendar text-muted"></i> {{ $naissance->DateR }}
                                </small>
                            </td>
                            <td class="text-center">
                                @if ($naissance->CNI)
                                    @if (pathinfo($naissance->CNI, PATHINFO_EXTENSION) === 'pdf')
                                        <a href="{{ asset('storage/' . $naissance->CNI) }}" target="_blank">
                                            <img src="{{ asset('assets/assets/img/pdf.jpg') }}" alt="PDF" class="doc-preview">
                                        </a>
                                    @else
                                        <img src="{{ asset('storage/' . $naissance->CNI) }}" alt="Doc" class="doc-preview" onclick="showImage(this)">
                                    @endif
                                @else
                                    <span class="text-muted small">Aucun</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($naissance->etat == 'rejetée')
                                    <span class="badge-status badge-rejete">REJETÉ</span>
                                @elseif($naissance->etat == 'en attente')
                                    <span class="badge-status badge-en-attente">EN ATTENTE</span>
                                @elseif($naissance->etat == 'réçu')
                                    <span class="badge-status badge-en-cours">EN COURS</span>
                                @else
                                    <span class="badge-status badge-termine">TERMINÉ</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="delivery-badge">
                                    <i class="fas {{ $naissance->choix_option == 'livraison' ? 'fa-motorcycle' : 'fa-university' }}"></i>
                                    {{ $naissance->choix_option == 'livraison' ? 'Livraison' : 'Mairie' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    @if ($naissance->peut_modifier)
                                        <button onclick="showModificationPopup('{{ $naissance->id }}', {{ json_encode($naissance) }})" class="btn-action btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif

                                    @if ($naissance->etat !== 'réçu' && $naissance->etat !== 'terminé' && !$naissance->peut_modifier)
                                        <button onclick="confirmDelete('{{ route('user.extrait.delete', $naissance->id) }}')" class="btn-action btn-delete" title="Supprimer">
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

    <!-- Modale Image -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-body p-0 text-center">
                    <img id="modalImage" src="" style="max-width: 100%; border-radius: 20px;">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $('#naissanceTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                order: [[0, 'desc']],
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
                confirmButtonColor: '#e74c3c',
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
            const fieldLabels = {
                'name': 'Nom', 'prenom': 'Prénoms', 'number': 'Numéro de registre',
                'DateR': 'Date de registre', 'commune': 'Commune', 'CNI': 'Pièce d\'identité',
                'type': 'Type', 'quantite': 'Quantité'
            };

            let formHtml = `<form id="modificationForm" class="text-start" enctype="multipart/form-data">`;
            champsAModifier.forEach(field => {
                const label = fieldLabels[field] || field;
                let val = demande[field] || '';
                if (field === 'DateR' && val) val = new Date(val).toISOString().split('T')[0];

                formHtml += `<div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase">${label}</label>`;
                
                if (field === 'type') {
                    formHtml += `<select name="${field}" class="form-select"><option value="simple" ${val==='simple'?'selected':''}>Simple</option><option value="extrait_integral" ${val==='extrait_integral'?'selected':''}>Intégral</option></select>`;
                } else if (field === 'CNI') {
                    formHtml += `<input type="file" name="${field}" class="form-control" accept=".jpg,.jpeg,.png,.pdf">`;
                } else if (field === 'DateR') {
                    formHtml += `<input type="date" name="${field}" class="form-control" value="${val}">`;
                } else {
                    formHtml += `<input type="${field==='quantite'?'number':'text'}" name="${field}" class="form-control" value="${val}">`;
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
                preConfirm: () => {
                    const formData = new FormData(document.getElementById('modificationForm'));
                    return formData;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = result.value;
                    formData.append('_method', 'PUT');
                    
                    Swal.showLoading();
                    fetch(`/user/naissances/${demandeId}/modifier`, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
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
        <script>Swal.fire('Succès', "{{ session('success') }}", 'success');</script>
    @endif
    @if (session('error'))
        <script>Swal.fire('Erreur', "{{ session('error') }}", 'error');</script>
    @endif
@endsection