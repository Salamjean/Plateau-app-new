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
                        <a href="{{ route('user.extrait.deces.create') }}" class="btn btn-new-request">
                            <i class="fas fa-plus me-2"></i>Nouvelle demande
                        </a>
                    </div>

                    <!-- Section Demandes pour tierce personne -->
                    <div>
                        <h5 class="section-header">Mes demandes d'extraits de décès</h5>
                        <div class="table-responsive">
                            <table class="table select-table">
                                <thead>
                                    <tr style="background-color: #1977cc">
                                        <th>Référence</th>
                                        <th>Quantité</th>
                                        <th>Défunt</th>
                                        <th>Détails</th>
                                        <th>Documents</th>
                                        <th>Statut</th>
                                        <th>Agent</th>
                                        <th>Actions</th>
                                        <th>Retrait</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($deces as $decesItem)
                                        <tr>
                                            <td>{{ $decesItem->reference }}</td>
                                            <td>{{ $decesItem->quantite }} copie(s)</td>
                                            <td>{{ $decesItem->name }}</td>
                                            <td>
                                                <small>
                                                    <strong>Registre:</strong> {{ $decesItem->numberR }}<br>
                                                    <strong>Date:</strong> {{ $decesItem->dateR }}<br>
                                                </small>
                                            </td>
                                            <td>
                                                @if ($decesItem->CNIdfnt)
                                                    @if (pathinfo($decesItem->CNIdfnt, PATHINFO_EXTENSION) === 'pdf')
                                                        <a href="{{ asset('storage/' . $decesItem->CNIdfnt) }}"
                                                            target="_blank">
                                                            <img src="{{ asset('assets/assets/img/pdf.jpg') }}"
                                                                alt="PDF" class="document-preview">
                                                        </a>
                                                    @else
                                                        <img src="{{ asset('storage/' . $decesItem->CNIdfnt) }}"
                                                            alt="CNI défunt" class="document-preview"
                                                            onclick="showImage(this)">
                                                    @endif
                                                @else
                                                    <span class="text-muted">Aucun document</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($decesItem->etat == 'rejetée')
                                                    <span class="badge badge-danger">
                                                        REJETÉE
                                                        @if ($decesItem->peut_modifier)
                                                            <br><small class="text-white">(Modification requise)</small>
                                                        @endif
                                                    </span>
                                                @elseif($decesItem->etat == 'en attente')
                                                    <span class="badge badge-warning">EN ATTENTE</span>
                                                @elseif($decesItem->etat == 'réçu')
                                                    <span class="badge badge-success">EN COURS DE TRAITEMENT</span>
                                                @endif
                                            </td>
                                            <td>{{ $decesItem->agent ? $decesItem->agent->name . ' ' . $decesItem->agent->prenom : 'Non attribué' }}
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    @if ($decesItem->peut_modifier)
                                                        <button
                                                            onclick="showModificationPopup('{{ $decesItem->id }}', {{ json_encode($decesItem) }})"
                                                            class="btn btn-sm btn-warning action-btn"
                                                            title="Modifier la demande">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                    @endif

                                                    @if ($decesItem->etat !== 'réçu' && $decesItem->etat !== 'terminé' && !$decesItem->peut_modifier)
                                                        <button
                                                            onclick="confirmDelete('{{ route('user.extrait.deces.delete', $decesItem->id) }}')"
                                                            class="btn btn-sm btn-danger action-btn ms-1" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @elseif(!$decesItem->peut_modifier)
                                                        <button class="btn btn-sm btn-secondary action-btn disabled"
                                                            title="Non modifiable">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="retrait-badge">{{ $decesItem->choix_option }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">Aucune demande d'extrait de décès
                                                trouvée</td>
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
                'name': 'Nom et Prénoms du Défunt',
                'numberR': 'Numéro de Registre',
                'dateR': 'Date de Registre',
                'commune': 'Commune',
                'quantite': 'Quantité',
                'CNIdfnt': 'CNI/extrait de naissance du défunt',
                'CNIdcl': 'Certificat médical de décès',
                'documentMariage': 'Document de mariage',
                'RequisPolice': 'Réquisition de police'
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
                if (field === 'dateR' && fieldValue) {
                    fieldValue = new Date(fieldValue).toISOString().split('T')[0];
                }

                if (field === 'quantite') {
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
                } else if (['CNIdfnt', 'CNIdcl', 'documentMariage', 'RequisPolice'].includes(field)) {
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
                } else if (field === 'dateR') {
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
                        if (['CNIdfnt', 'CNIdcl', 'documentMariage', 'RequisPolice'].includes(field)) {
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
                    const url = `/user/extrait/deces/${demandeId}/modifier`;

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

        @if (session('success'))
            Swal.fire({
                title: 'Succès',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#3085d6'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'Erreur',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        @endif
    </script>

@endsection
