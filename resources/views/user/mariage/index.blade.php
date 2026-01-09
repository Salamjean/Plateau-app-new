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

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
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

            th,
            td {
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
                            <i class="fas fa-file-contract me-2"></i> Mes d'extrait de mariage
                        </h4>
                        <a href="{{ route('user.extrait.mariage.create') }}" class="btn btn-new-request">
                            <i class="fas fa-plus-circle me-2"></i>Nouvelle demande
                        </a>
                    </div>

                    <!-- Demandes d'extrait de mariage -->
                    <h5 class="section-title">
                        <i class="fas fa-list-check me-2"></i>Mes demandes d'extraits
                    </h5>
                    <div class="table-responsive">
                        <table class="table" id="mariageTable">
                            <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Quantité</th>
                                    <th class="d-none-tablet">Conjoint(e)</th>
                                    <th>Documents</th>
                                    <th>Statut</th>
                                    <th>Agent</th>
                                    <th>Actions</th>
                                    <th>Retrait</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($allMariages as $mariage)
                                    <tr class="animate__animated animate__fadeIn">
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $mariage->reference }}</span>
                                        </td>
                                        <td>{{ $mariage->quantite }} copie(s)</td>
                                        <td class="d-none-tablet">
                                            {{ $mariage->nomEpoux ?: 'N/A' }} {{ $mariage->prenomEpoux ?: '' }}
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                @if ($mariage->pieceIdentite)
                                                    @if (pathinfo($mariage->pieceIdentite, PATHINFO_EXTENSION) === 'pdf')
                                                        <a href="{{ asset('storage/' . $mariage->pieceIdentite) }}"
                                                            target="_blank" title="Pièce d'identité (PDF)">
                                                            <img src="{{ asset('assets/assets/img/pdf.jpg') }}"
                                                                alt="PDF" class="document-preview">
                                                        </a>
                                                    @else
                                                        <img src="{{ asset('storage/' . $mariage->pieceIdentite) }}"
                                                            alt="Pièce d'identité" class="document-preview"
                                                            onclick="showImage(this)" title="Pièce d'identité">
                                                    @endif
                                                @endif

                                                @if ($mariage->extraitMariage)
                                                    @if (pathinfo($mariage->extraitMariage, PATHINFO_EXTENSION) === 'pdf')
                                                        <a href="{{ asset('storage/' . $mariage->extraitMariage) }}"
                                                            target="_blank" title="Extrait (PDF)">
                                                            <img src="{{ asset('assets/assets/img/pdf.jpg') }}"
                                                                alt="PDF" class="document-preview">
                                                        </a>
                                                    @else
                                                        <img src="{{ asset('storage/' . $mariage->extraitMariage) }}"
                                                            alt="Extrait de mariage" class="document-preview"
                                                            onclick="showImage(this)" title="Extrait de mariage">
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if ($mariage->etat == 'rejetée')
                                                <span class="badge badge-rejected">
                                                    MODIFIER LES INFORMATIONS
                                                    @if ($mariage->peut_modifier)
                                                        <br><small class="text-white">(Modification requise)</small>
                                                    @endif
                                                </span>
                                            @elseif($mariage->etat == 'en attente')
                                                <span class="badge badge-waiting">EN ATTENTE</span>
                                            @elseif($mariage->etat == 'réçu')
                                                <span class="badge badge-received">EN COURS DE TRAITEMENT</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $mariage->agent ? $mariage->agent->name : 'Non attribué' }}
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                @if ($mariage->peut_modifier)
                                                    <button
                                                        onclick="showModificationPopup('{{ $mariage->id }}', {{ json_encode($mariage) }})"
                                                        class="action-btn btn-warning" title="Modifier la demande">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @endif

                                                @if ($mariage->etat !== 'réçu' && $mariage->etat !== 'terminé' && !$mariage->peut_modifier)
                                                    <button
                                                        onclick="confirmDelete('{{ route('user.extrait.mariage.delete', $mariage->id) }}')"
                                                        class="action-btn btn-delete" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @elseif(!$mariage->peut_modifier)
                                                    <button class="action-btn btn-disabled" title="Non modifiable"
                                                        onclick="showDisabledMessage()">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="retrait-badge">{{ $mariage->choix_option }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox"></i>
                                                <h5 class="mt-3">Aucune demande trouvée</h5>
                                                <p class="text-muted">Vous n'avez effectué aucune demande d'extrait de
                                                    mariage</p>
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
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                        responsivePriority: 1,
                        targets: 0
                    },
                    {
                        responsivePriority: 2,
                        targets: 3
                    },
                    {
                        responsivePriority: 3,
                        targets: 4
                    }
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

        // Fonction pour afficher le pop-up de modification
        function showModificationPopup(demandeId, demande) {
            // Récupérer les champs à modifier depuis le JSON
            let champsAModifier = JSON.parse(demande.champs_a_modifier || '[]');

            if (champsAModifier.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Aucun champ à modifier n\'a été spécifié.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Mapping des champs vers leurs libellés
            const fieldLabels = {
                'typeDemande': 'Type de demande',
                'nomEpoux': 'Nom du conjoint',
                'prenomEpoux': 'Prénom du conjoint',
                'dateNaissanceEpoux': 'Date de naissance du conjoint',
                'lieuNaissanceEpoux': 'Lieu de naissance du conjoint',
                'commune': 'Commune',
                'quantite': 'Quantité',
                'pieceIdentite': 'Pièce d\'identité',
                'extraitMariage': 'Extrait de mariage',
                'CMU': 'Numéro NNI'
            };

            // Créer le formulaire dynamique
            let formHtml = `
        <form id="modificationForm" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
    `;

            champsAModifier.forEach(field => {
                const label = fieldLabels[field] || field;
                let fieldValue = demande[field] || '';

                // Formater la date si nécessaire
                if (field === 'dateNaissanceEpoux' && fieldValue) {
                    fieldValue = new Date(fieldValue).toISOString().split('T')[0];
                }

                if (field === 'typeDemande') {
                    formHtml += `
                <div class="mb-3">
                    <label class="form-label">${label}</label>
                    <select name="${field}" class="form-control" required>
                        <option value="extraitSimple" ${fieldValue === 'extraitSimple' ? 'selected' : ''}>Extrait simple</option>
                        <option value="copieIntegrale" ${fieldValue === 'copieIntegrale' ? 'selected' : ''}>Copie intégrale</option>
                    </select>
                </div>
            `;
                } else if (field === 'quantite') {
                    formHtml += `
                <div class="mb-3">
                    <label class="form-label">${label}</label>
                    <input type="number" name="${field}" class="form-control" 
                           value="${fieldValue}" min="1" max="10" required>
                </div>
            `;
                } else if (field === 'commune') {
                    formHtml += `
                <div class="mb-3">
                    <label class="form-label">${label}</label>
                    <input type="text" name="${field}" class="form-control" 
                           value="plateau" readonly>
                    <small class="text-muted">Commune fixée à Plateau</small>
                </div>
            `;
                } else if (['pieceIdentite', 'extraitMariage'].includes(field)) {
                    formHtml += `
                <div class="mb-3">
                    <label class="form-label">${label}</label>
                    <div class="file-input-container mb-2">
                        <label class="file-input-label">
                            <span class="file-input-text" id="file-name-${field}">Choisir un fichier</span>
                            <span class="file-input-button">Parcourir</span>
                            <input type="file" id="${field}" name="${field}" class="file-input" 
                                   onchange="updateFileName(this, '${field}')" accept=".jpg,.jpeg,.png,.pdf">
                        </label>
                    </div>
                    <small class="text-muted">Formats acceptés: JPG, PNG, PDF (max 1MB)</small>
                    ${fieldValue ? '<div class="mt-2"><small>Document actuel: ' + fieldValue.split('/').pop() + '</small></div>' : ''}
                </div>
            `;
                } else if (field === 'dateNaissanceEpoux') {
                    formHtml += `
                <div class="mb-3">
                    <label class="form-label">${label}</label>
                    <input type="date" name="${field}" class="form-control" 
                           value="${fieldValue}" required>
                </div>
            `;
                } else {
                    formHtml += `
                <div class="mb-3">
                    <label class="form-label">${label}</label>
                    <input type="text" name="${field}" class="form-control" 
                           value="${fieldValue}" required>
                </div>
            `;
                }
            });

            formHtml += `
            </div>
        </form>
        <style>
            .file-input-container {
                position: relative;
                overflow: hidden;
            }
            .file-input-label {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0.8rem 1rem;
                background: #f9f9f9;
                border: 1px solid #e0e0e0;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            .file-input-label:hover {
                background: #f0f0f0;
            }
            .file-input-text {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                color: #666;
            }
            .file-input-button {
                background: #1977cc;
                color: white;
                padding: 0.3rem 0.8rem;
                border-radius: 6px;
                font-size: 0.85rem;
                margin-left: 1rem;
            }
            .file-input {
                position: absolute;
                left: 0;
                top: 0;
                opacity: 0;
                width: 100%;
                height: 100%;
                cursor: pointer;
            }
            .swal2-popup .form-control {
                margin-bottom: 10px;
            }
        </style>
    `;

            Swal.fire({
                title: 'Modifier la demande rejetée',
                html: formHtml,
                width: '600px',
                showCancelButton: true,
                confirmButtonText: 'Enregistrer les modifications',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#1977cc',
                didOpen: () => {
                    // Réinitialiser le nom de fichier
                    champsAModifier.forEach(field => {
                        if (['pieceIdentite', 'extraitMariage'].includes(field)) {
                            const fileElement = document.getElementById(`file-name-${field}`);
                            if (fileElement) {
                                fileElement.textContent = 'Choisir un fichier';
                            }
                        }
                    });
                },
                preConfirm: () => {
                    const form = document.getElementById('modificationForm');
                    const formData = new FormData(form);

                    // Validation côté client
                    let isValid = true;
                    champsAModifier.forEach(field => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input && input.hasAttribute('required') && !input.value.trim()) {
                            isValid = false;
                            input.classList.add('is-invalid');
                        } else if (input) {
                            input.classList.remove('is-invalid');
                        }
                    });

                    if (!isValid) {
                        Swal.showValidationMessage('Veuillez remplir tous les champs obligatoires');
                        return false;
                    }

                    return formData;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const formData = result.value;

                    // Ajouter l'ID de la demande
                    formData.append('_method', 'PUT');

                    // Afficher le loader
                    Swal.fire({
                        title: 'Traitement en cours',
                        html: 'Modification de la demande...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Utiliser la route nommée avec le paramètre ID
                    const url = `/user/extrait/mariage/${demandeId}/modifier`;

                    // Envoyer la requête AJAX
                    fetch(url, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.success) {
                                Swal.fire({
                                    title: 'Succès',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#1977cc'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Erreur',
                                    text: data.message || 'Une erreur est survenue',
                                    icon: 'error',
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            Swal.fire({
                                title: 'Erreur',
                                text: 'Erreur lors de la modification',
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        });
                }
            });
        }

        // Fonction pour mettre à jour le nom du fichier sélectionné
        function updateFileName(input, fieldId) {
            const fileName = input.files[0] ? input.files[0].name : 'Choisir un fichier';
            document.getElementById(`file-name-${fieldId}`).textContent = fileName;
        }

        // Notifications
        @if (session('success'))
            Swal.fire({
                title: 'Succès',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#1977cc',
                timer: 3000
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'Erreur',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#e74c3c'
            });
        @endif
    </script>

@endsection
