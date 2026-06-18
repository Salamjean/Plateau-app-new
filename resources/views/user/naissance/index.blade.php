@extends('user.layouts.template')

@section('content')
    <!-- Styles et Scripts -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <style>
        :root {
            --primary: #1f4083;
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        #naissanceTable tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            background: #f8fbff !important;
        }

        #naissanceTable tbody td {
            padding: 15px !important;
            border: none !important;
            vertical-align: middle !important;
            color: var(--text-navy);
            font-size: 0.9rem;
        }

        #naissanceTable tbody td:first-child {
            border-radius: 12px 0 0 12px;
        }

        #naissanceTable tbody td:last-child {
            border-radius: 0 12px 12px 0;
        }

        /* Badges */
        .badge-status {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            display: inline-block;
        }

        .badge-en-attente {
            background: #fff7ed;
            color: #9a3412;
        }

        .badge-en-cours {
            background: #f0f9ff;
            color: #0369a1;
        }

        .badge-rejete {
            background: #fef2f2;
            color: #991b1b;
        }

        .badge-termine {
            background: #f0fdf4;
            color: #166534;
        }

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

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .marquee-container {
            background: #fff5f5;
            border-radius: 12px;
            padding: 10px;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--danger);
        }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
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

            #naissanceTable {
                min-width: 900px;
                /* Plus large pour accueillir toutes les colonnes */
            }

            .page-title {
                font-size: 1.25rem;
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

    <div class="dashboard-final glass-container container-fluid animate-fade-in">
        <div class="page-header">
            <h4 class="page-title">Mes demandes d'actes de naissance</h4>
            <a href="{{ route('user.extrait.create') }}" class="btn-add-premium">
                <i class="fas fa-plus"></i> Nouvelle demande
            </a>
        </div>

        @php
            $total = $naissances->count();
            $enAttente = $naissances->where('etat', 'en attente')->count();
            $enCours = $naissances->where('etat', 'réçu')->count();
            $termine = $naissances->where('etat', 'terminé')->count();
            $rejete = $naissances->where('etat', 'rejetée')->count();
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
                    @forelse ($naissances as $naissance)
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
                                    <span>{{ $naissance->type == 'simple' ? 'Acte Simple' : ($naissance->type == 'groupee' ? 'Simple + Intégral' : 'Extrait Intégral') }}</span>
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
                                            <img src="{{ asset('assets/assets/img/pdf.jpg') }}" alt="PDF"
                                                class="doc-preview">
                                        </a>
                                    @else
                                        <img src="{{ asset('storage/' . $naissance->CNI) }}" alt="Doc"
                                            class="doc-preview" onclick="showImage(this)">
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
                                    <i
                                        class="fas {{ $naissance->choix_option == 'livraison' ? 'fa-motorcycle' : 'fa-university' }}"></i>
                                    {{ $naissance->choix_option == 'livraison' ? 'Livraison' : 'Mairie' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('demande.details.view', ['type' => 'naissance', 'id' => $naissance->id]) }}"
                                        class="btn-action shadow-sm" title="Détails"
                                        style="background: #eef5fc; color: #1f4083;">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if ($naissance->peut_modifier)
                                        <button
                                            onclick="showModificationPopup('{{ $naissance->id }}', {{ json_encode($naissance) }})"
                                            class="btn-action btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @elseif (is_null($naissance->agent_id))
                                        <a href="{{ route('user.naissances.edit', $naissance->id) }}"
                                            class="btn-action btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if ($naissance->etat !== 'réçu' && $naissance->etat !== 'terminé' && !$naissance->peut_modifier)
                                        <button
                                            onclick="confirmDelete('{{ route('user.extrait.delete', $naissance->id) }}')"
                                            class="btn-action btn-delete" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-baby fa-3x text-muted mb-3"></i>
                                    <p class="text-grey mb-0">Aucune demande de naissance effectuée</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
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
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
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
            $('#naissanceTable').DataTable({
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

            // Si la demande n'a pas d'agent assigné et n'a pas de champs spécifiques à modifier définis par l'IA (peut_modifier est faux)
            // On permet de modifier tous les champs principaux.
            if (!demande.agent_id && !demande.peut_modifier) {
                champsAModifier = ['name', 'prenom', 'number', 'DateR', 'type', 'quantite', 'CNI', 'commune'];
            }

            // On s'assure que la quantité est toujours sélectionnable/modifiable pour calculer la différence
            if (!champsAModifier.includes('quantite')) {
                champsAModifier.push('quantite');
            }

            const fieldLabels = {
                'name': 'Nom',
                'prenom': 'Prénoms',
                'number': 'Numéro de registre',
                'DateR': 'Date de registre',
                'commune': 'Commune',
                'CNI': 'Pièce d\'identité',
                'type': 'Type',
                'quantite': 'Quantité'
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
                if (field === 'DateR' && val) val = new Date(val).toISOString().split('T')[0];

                formHtml += `<div class="mb-3">
                    <label class="form-label fw-bold small text-uppercase">${label}</label>`;

                if (field === 'type') {
                    formHtml +=
                        `<select name="${field}" class="form-select"><option value="simple" ${val==='simple'?'selected':''}>Simple</option><option value="integrale" ${val==='integrale'||val==='extrait_integral'?'selected':''}>Intégral</option></select>`;
                } else if (field === 'CNI') {
                    formHtml +=
                        `<input type="file" name="${field}" class="form-control" accept=".jpg,.jpeg,.png,.pdf">`;
                } else if (field === 'DateR') {
                    formHtml += `<input type="date" name="${field}" class="form-control" value="${val}">`;
                } else {
                    formHtml +=
                        `<input type="${field==='quantite'?'number':'text'}" name="${field}" class="form-control" value="${val}">`;
                }
                formHtml += `</div>`;
            });

            // Ajouter les champs non modifiés en tant que champs cachés pour éviter qu'ils soient écrasés ou qu'ils échouent la validation
            const allPossibleFields = [
                'pour', 'type', 'name', 'prenom', 'commune', 'commune_naissance',
                'number', 'DateR', 'nom_prenoms_pere', 'nom_prenoms_mere',
                'relation', 'qty_simple', 'qty_integral', 'quantite', 'choix_option'
            ];

            allPossibleFields.forEach(field => {
                if (!champsAModifier.includes(field)) {
                    if (field === 'qty_simple' || field === 'qty_integral') {
                        if (champsAModifier.includes('quantite')) return;
                    }
                    if (field === 'quantite' && (champsAModifier.includes('qty_simple') || champsAModifier.includes(
                            'qty_integral'))) return;

                    let val = demande[field] !== null && demande[field] !== undefined ? demande[field] : '';
                    if (field === 'DateR' && val) val = new Date(val).toISOString().split('T')[0];
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
                <div id="mod-payment-phone-container" style="display: block; margin-top: 8px; text-align: left;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: #4a5568; margin-bottom: 3px; text-transform: uppercase;">Numéro Wave</label>
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
                    const typeSelect = dom.querySelector('[name="type"]');
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
                            phoneContainer.style.display = 'block';
                            phoneContainer.querySelector('label').innerText = 'Numéro Wave';
                        });

                        btnMtn.addEventListener('click', () => {
                            btnMtn.style.background = '#eff6ff';
                            btnMtn.style.border = '2px solid #1e3a8a';
                            btnWave.style.background = 'white';
                            btnWave.style.border = '1px solid #edf2f7';
                            inputPaymentMethod.value = 'mtn';
                            phoneContainer.style.display = 'block';
                            phoneContainer.querySelector('label').innerText = 'Numéro MTN Money';
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
                        const typeInput = form.querySelector('[name="type"]');
                        const typeVal = typeInput ? typeInput.value : (demande.type || 'simple');
                        const qtyVal = parseInt(quantiteInput.value) || 1;

                        if (typeVal === 'simple') {
                            formData.set('qty_simple', qtyVal);
                            formData.set('qty_integral', 0);
                        } else if (typeVal === 'integrale' || typeVal === 'extrait_integral') {
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
                    if (paymentMethodInput) {
                        const method = paymentMethodInput.value;
                        const phoneInput = form.querySelector('#mod-mtn_number');
                        const phoneVal = phoneInput ? phoneInput.value.replace(/\s+/g, '') : '';
                        
                        if (method === 'mtn') {
                            if (!/^05\d{8}$/.test(phoneVal)) {
                                Swal.showValidationMessage(
                                    'Le numéro MTN Money doit comporter 10 chiffres et commencer par 05.');
                                return false;
                            }
                            formData.set('mtn_number', phoneVal);
                            formData.delete('wave_number');
                        } else if (method === 'wave') {
                            if (!/^0[157]\d{8}$/.test(phoneVal)) {
                                Swal.showValidationMessage(
                                    'Le numéro Wave doit comporter 10 chiffres et commencer par 01, 05 ou 07.');
                                return false;
                            }
                            formData.set('wave_number', phoneVal);
                            formData.delete('mtn_number');
                        }
                    }

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
                                if (data.redirect_url && data.mtn_ref && data.reference) {
                                    Swal.fire({
                                        title: 'Paiement MTN Money',
                                        html: `<div class="text-center">
                                            <div class="mtn-spinner" style="margin: 20px auto; width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #fcb711; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                                            <p style="font-weight: 600; color: #1f4083;">Requête push envoyée au numéro MTN</p>
                                            <p style="font-size: 0.9rem; color: #555;">Veuillez valider le paiement sur votre téléphone en saisissant votre code secret.<br><br>
                                            <span style="font-size: 0.8rem; color: #777;">En attente de validation... (Ne fermez pas cette page)</span></p>
                                        </div>`,
                                        allowOutsideClick: false,
                                        showConfirmButton: false,
                                        didOpen: () => {
                                            if (!document.getElementById('mtn-spin-style')) {
                                                const style = document.createElement('style');
                                                style.id = 'mtn-spin-style';
                                                style.innerHTML = `@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }`;
                                                document.head.appendChild(style);
                                            }
                                        }
                                    });
                                    startMtnPaymentPolling(data.reference, data.mtn_ref, 'naissance');
                                } else if (data.redirect_url) {
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

        function startMtnPaymentPolling(reference, mtnRef, type) {
            const csrfToken = '{{ csrf_token() }}';
            const checkStatus = () => {
                fetch('{{ route("user.payment.mtn.check") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        reference: reference,
                        type: type,
                        mtn_ref: mtnRef
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'SUCCESSFUL') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Paiement Réussi',
                            text: 'Votre paiement a été validé avec succès.',
                            confirmButtonColor: '#1f4083',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = data.redirect || "{{ route('user.extrait.index') }}";
                        });
                    } else if (data.status === 'FAILED') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Échec du paiement',
                            text: data.message || 'Le paiement a échoué ou a été annulé.',
                            confirmButtonColor: '#1f4083',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = data.redirect || "{{ route('user.extrait.index') }}";
                        });
                    } else {
                        setTimeout(checkStatus, 4000);
                    }
                })
                .catch(error => {
                    console.error('Erreur de vérification:', error);
                    setTimeout(checkStatus, 4000);
                });
            };
            setTimeout(checkStatus, 4000);
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
