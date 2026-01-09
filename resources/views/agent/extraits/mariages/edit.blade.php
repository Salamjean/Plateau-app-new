@extends('agent.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="{{asset('dasboard/edit.css')}}">

<style>
    .rejet-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .rejet-item {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        transition: all 0.3s ease;
    }

    .rejet-item:hover {
        background: #e9f7fe;
        border-color: #1977cc;
    }

    .rejet-checkbox {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .rejet-checkbox input[type="checkbox"] {
        margin-right: 10px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .rejet-current-value {
        background: white;
        padding: 8px 12px;
        border-radius: 5px;
        border-left: 3px solid #1977cc;
    }

    .rejet-current-value small {
        color: #6c757d;
        font-size: 0.8rem;
        display: block;
        margin-bottom: 3px;
    }

    .value-text {
        color: #495057;
        font-weight: 500;
        word-break: break-word;
    }

    .motif-checkbox:checked + .checkmark + .rejet-label {
        color: #dc3545;
        font-weight: bold;
    }

    .rejet-item .motif-checkbox:checked ~ .rejet-current-value {
        border-left-color: #dc3545;
        background: #fff5f5;
    }
    
    .status-badge.status-rejected {
        background-color: #ffebee;
        color: #d32f2f;
        border: 1px solid #d32f2f;
    }
    
    .info-item-full {
        grid-column: 1 / -1;
    }
</style>

<div class="dashboard-container">
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                html: `<ul class="text-left">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
                confirmButtonColor: '#007e00',
                background: 'white'
            });
        </script>
    @endif

    <div class="page-title">
        <h2>
            <i class="fas fa-edit me-2"></i>Modifier l'état de la demande
        </h2>
        <div class="page-actions">
            <a href="{{ route('agent.demandes.wedding.index') }}" class="btn-action">
                <i class="fas fa-arrow-left btn-icon"></i>Retour
            </a>
        </div>
    </div>

    <div class="form-container">
        <div class="info-card">
            <div class="info-header">
                <i class="fas fa-info-circle info-icon"></i>
                <h4 class="info-title">Informations sur la demande</h4>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Demandeur</span>
                    <span class="info-value">{{ $mariage->user->name.' '.$mariage->user->prenom ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date de demande</span>
                    <span class="info-value">{{ $mariage->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Référence</span>
                    <span class="info-value">{{ $mariage->reference }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Type de demande</span>
                    <span class="info-value">{{ $mariage->typeDemande == 'copieIntegrale' ? 'Copie intégrale' : 'Extrait simple' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Statut actuel</span>
                    <span class="info-value">
                        @if($mariage->etat == 'en attente')
                            <span class="status-badge status-pending">En attente</span>
                        @elseif($mariage->etat == 'réçu')
                            <span class="status-badge status-recu">En cours</span>
                        @elseif($mariage->etat == 'rejetée')
                            <span class="status-badge status-rejected">Rejetée</span>
                        @else
                            <span class="status-badge status-termine">Terminé</span>
                        @endif
                    </span>
                </div>
                
                @if($mariage->etat == 'rejetée' && $mariage->motif_de_rejet)
                    <div class="info-item info-item-full">
                        <span class="info-label">Motif de Rejet (ancien)</span>
                        <span class="info-value" style="white-space: pre-wrap;">{{ $mariage->motif_de_rejet }}</span>
                    </div>
                @endif
                
                @if($mariage->etat == 'rejetée' && $mariage->champs_a_modifier)
                    <div class="info-item info-item-full">
                        <span class="info-label">Champs à modifier</span>
                        <span class="info-value">
                            @php
                                $champs = json_decode($mariage->champs_a_modifier, true);
                                $labels = [
                                    'typeDemande' => 'Type de demande',
                                    'nomEpoux' => 'Nom du conjoint',
                                    'prenomEpoux' => 'Prénom du conjoint',
                                    'dateNaissanceEpoux' => 'Date de naissance du conjoint',
                                    'lieuNaissanceEpoux' => 'Lieu de naissance du conjoint',
                                    'commune' => 'Commune',
                                    'quantite' => 'Quantité',
                                    'pieceIdentite' => 'Pièce d\'identité',
                                    'extraitMariage' => 'Extrait de mariage',
                                    'CMU' => 'Numéro NNI'
                                ];
                            @endphp
                            @foreach($champs as $champ)
                                <span class="badge badge-warning me-1">{{ $labels[$champ] ?? $champ }}</span>
                            @endforeach
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <form action="{{ route('agent.demandes.wedding.update', $mariage->id) }}" method="POST" id="update-etat-form">
            @csrf
            @method('POST')
            
            <input type="hidden" name="motif_de_rejet" id="motif_de_rejet_input">

            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-cog"></i>Modifier le statut de la demande
                </h4>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-tasks"></i>Nouveau statut
                    </label>
                    <select class="form-select" name="etat" id="etat_select" required>
                        <option value="">Sélectionnez un statut</option>
                        @foreach($etats as $etat)
                            <option value="{{ $etat }}" {{ $mariage->etat == $etat ? 'selected' : '' }}>
                                {{ ucfirst($etat) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Section pour les motifs de rejet détaillés --}}
            <div id="rejet-details" class="form-section" style="display: none;">
                <h4 class="section-title">
                    <i class="fas fa-exclamation-triangle"></i>Spécifiez les informations incorrectes
                </h4>
                
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle"></i>
                    Cochez les champs qui contiennent des informations incorrectes :
                </div>
                
                <div class="rejet-grid">
                    {{-- Champs de base --}}
                    @foreach([
                        'typeDemande' => 'Type de demande',
                        'nomEpoux' => 'Nom du conjoint',
                        'prenomEpoux' => 'Prénom du conjoint',
                        'commune' => 'Commune',
                        'quantite' => 'Quantité',
                        'CMU' => 'Numéro NNI'
                    ] as $field => $label)
                        <div class="rejet-item">
                            <label class="rejet-checkbox">
                                <input type="checkbox" name="motif_champs[]" value="{{ $field }}" 
                                       class="motif-checkbox" data-label="{{ $label }}">
                                <span class="checkmark"></span>
                                <span class="rejet-label">{{ $label }}</span>
                            </label>
                            <div class="rejet-current-value">
                                <small>Valeur actuelle:</small>
                                <span class="value-text">
                                    @if($field === 'typeDemande')
                                        {{ $mariage->$field == 'copieIntegrale' ? 'Copie intégrale' : 'Extrait simple' }}
                                    @elseif($field === 'quantite')
                                        {{ $mariage->$field }} copie(s)
                                    @else
                                        {{ $mariage->$field ?? 'Non renseigné' }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                    
                    {{-- Champs conditionnels (si copie intégrale) --}}
                    @if($mariage->typeDemande == 'copieIntegrale')
                        @foreach([
                            'dateNaissanceEpoux' => 'Date de naissance du conjoint',
                            'lieuNaissanceEpoux' => 'Lieu de naissance du conjoint'
                        ] as $field => $label)
                            <div class="rejet-item">
                                <label class="rejet-checkbox">
                                    <input type="checkbox" name="motif_champs[]" value="{{ $field }}" 
                                           class="motif-checkbox" data-label="{{ $label }}">
                                    <span class="checkmark"></span>
                                    <span class="rejet-label">{{ $label }}</span>
                                </label>
                                <div class="rejet-current-value">
                                    <small>Valeur actuelle:</small>
                                    <span class="value-text">
                                        @if($field === 'dateNaissanceEpoux')
                                            {{ \Carbon\Carbon::parse($mariage->$field)->format('d/m/Y') }}
                                        @else
                                            {{ $mariage->$field ?? 'Non renseigné' }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    
                    {{-- Fichiers --}}
                    @foreach([
                        'pieceIdentite' => 'Pièce d\'identité',
                        'extraitMariage' => 'Extrait de mariage'
                    ] as $field => $label)
                        <div class="rejet-item">
                            <label class="rejet-checkbox">
                                <input type="checkbox" name="motif_champs[]" value="{{ $field }}" 
                                       class="motif-checkbox" data-label="{{ $label }}">
                                <span class="checkmark"></span>
                                <span class="rejet-label">{{ $label }}</span>
                            </label>
                            <div class="rejet-current-value">
                                <small>Valeur actuelle:</small>
                                <span class="value-text">
                                    {{ $mariage->$field ? 'Fichier joint' : 'Non fourni' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="form-group mt-3">
                    <label class="form-label">
                        <i class="fas fa-comment-alt"></i>Commentaire additionnel (optionnel)
                    </label>
                    <textarea class="form-control" name="motif_commentaire" id="motif_commentaire" 
                              rows="3" placeholder="Ajoutez des détails supplémentaires si nécessaire..."></textarea>
                </div>
            </div>

            <div class="action-buttons">
                <button type="submit" class="btn-action btn-secondary">
                    <i class="fas fa-save btn-icon"></i>Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation d'apparition
        const formContainer = document.querySelector('.form-container');
        formContainer.style.opacity = '0';
        formContainer.style.transform = 'translateY(20px)';
        setTimeout(() => {
            formContainer.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            formContainer.style.opacity = '1';
            formContainer.style.transform = 'translateY(0)';
        }, 100);

        // Éléments du DOM
        const form = document.getElementById('update-etat-form');
        const etatSelect = document.getElementById('etat_select');
        const rejetDetailsSection = document.getElementById('rejet-details');
        const motifInput = document.getElementById('motif_de_rejet_input');
        
        // Afficher/masquer la section de rejet détaillé
        etatSelect.addEventListener('change', function() {
            if (this.value === 'rejetée') {
                rejetDetailsSection.style.display = 'block';
                // Faire défiler jusqu'à la section
                setTimeout(() => {
                    rejetDetailsSection.scrollIntoView({ behavior: 'smooth' });
                }, 300);
            } else {
                rejetDetailsSection.style.display = 'none';
                // Décocher toutes les cases
                document.querySelectorAll('.motif-checkbox').forEach(cb => {
                    cb.checked = false;
                });
                // Vider le commentaire
                document.getElementById('motif_commentaire').value = '';
            }
        });
        
        // Initialiser l'état au chargement
        if (etatSelect.value === 'rejetée') {
            rejetDetailsSection.style.display = 'block';
        }
        
        // Gestion de la soumission du formulaire
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const selectedEtat = etatSelect.value;
            
            // CAS 1: 'rejetée'
            if (selectedEtat === 'rejetée') {
                // Récupérer les champs cochés
                const checkedBoxes = document.querySelectorAll('.motif-checkbox:checked');
                const commentaire = document.getElementById('motif_commentaire').value.trim();
                
                if (checkedBoxes.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sélection requise',
                        text: 'Veuillez sélectionner au moins un champ incorrect pour justifier le rejet.',
                        confirmButtonColor: '#1977cc'
                    });
                    return;
                }
                
                // Construire le message de motif
                let motif = "Les champs suivants contiennent des informations incorrectes ou incomplètes :\n\n";
                
                checkedBoxes.forEach((checkbox, index) => {
                    const label = checkbox.getAttribute('data-label');
                    motif += `• ${label}\n`;
                });
                
                if (commentaire) {
                    motif += `\nCommentaire additionnel : ${commentaire}`;
                }
                
                // Afficher la confirmation avec le motif
                Swal.fire({
                    title: 'Confirmer le rejet',
                    html: `
                        <div class="text-start">
                            <p>Vous êtes sur le point de rejeter cette demande pour les raisons suivantes :</p>
                            <div class="alert alert-danger p-3">
                                <strong>Motif :</strong><br>
                                <pre style="white-space: pre-wrap; background: #fff5f5; padding: 10px; border-radius: 5px; margin-top: 5px;">${motif}</pre>
                            </div>
                            <p>Êtes-vous sûr de vouloir procéder ?</p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Oui, rejeter la demande',
                    cancelButtonText: 'Annuler',
                    width: '600px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mettre le motif dans le champ caché
                        motifInput.value = motif;
                        // Soumettre le formulaire
                        form.submit();
                    }
                });
                
            } else if (selectedEtat === 'terminé') {
                // CAS 2: 'terminé'
                Swal.fire({
                    title: 'Confirmer la finalisation',
                    text: 'Êtes-vous sûr de vouloir marquer cette demande comme terminée ? Cette action est irréversible.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1977cc',
                    cancelButtonColor: 'red',
                    confirmButtonText: 'Oui, terminer',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
                
            } else {
                // CAS 3: Tous les autres statuts ('en attente', 'réçu')
                form.submit();
            }
        });
    });
</script>
@endsection