@extends('agent.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="{{asset('dasboard/edit.css')}}">


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
                    <span class="info-label">Type de demande</span>
                    <span class="info-value">{{ $mariage->typeDemande == 'copieIntegrale' ? 'Copie intégrale' : 'Extrait simple' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Statut actuel</span>
                    <span class="info-value">
                        @if($mariage->etat == 'réçu')
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
                                    'pieceIdentite' => 'Pièce d\'identité',
                                    'extraitMariage' => 'Extrait de mariage'
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
                        'nomEpoux' => 'Nom du conjoint',
                        'prenomEpoux' => 'Prénom du conjoint'
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
                                    {{ $mariage->$field ?? 'Non renseigné' }}
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
<style>
 .status-badge.status-rejected {
   background-color: #ffebee;
   color: #d32f2f;
   border: 1px solid #d32f2f;
 }
 .info-item-full {
   grid-column: 1 / -1;
 }
 
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

 /* edit.css - Styles pour la modification d'état */

.dashboard-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #e4efe9 100%);
    min-height: 100vh;
    padding: 30px;
}

.page-title {
    background: white;
    padding: 20px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 5px solid #1977cc;
}

.page-title h2 {
    color: #1977cc;
    font-weight: 700;
    margin: 0;
    font-size: 1.8rem;
}

.page-actions .btn-action {
    background: linear-gradient(135deg, #1977cc, #1565c0);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
}

.page-actions .btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(44, 120, 115, 0.3);
}

.btn-icon {
    margin-right: 8px;
}

.form-container {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

.info-card {
    background: linear-gradient(135deg, #f8fdff 0%, #e3f2fd 100%);
    border: 1px solid #bbdefb;
    border-radius: 10px;
    padding: 25px;
    margin-bottom: 30px;
    border-left: 5px solid #1977cc;
}

.info-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.info-icon {
    color: #1977cc;
    font-size: 1.5rem;
    margin-right: 10px;
}

.info-title {
    color: #1977cc;
    margin: 0;
    font-weight: 700;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.info-item {
    background: white;
    padding: 12px 15px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.info-label {
    display: block;
    color: #666;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 5px;
}

.info-value {
    display: block;
    color: #333;
    font-weight: 600;
    font-size: 1rem;
}

.form-section {
    margin-bottom: 30px;
    padding: 25px;
    background: #f8fafc;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.section-title {
    color: #1977cc;
    font-weight: 700;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    font-size: 1.3rem;
}

.section-title i {
    margin-right: 10px;
    font-size: 1.2rem;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    color: #495057;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 1rem;
}

.form-label i {
    margin-right: 8px;
    color: #1977cc;
}

.form-select, .form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.form-select:focus, .form-control:focus {
    outline: none;
    border-color: #1977cc;
    box-shadow: 0 0 0 3px rgba(25, 119, 204, 0.2);
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 1px solid #ffc107;
    color: #856404;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid #ffc107;
}

.alert-warning i {
    margin-right: 10px;
    color: #ffc107;
}

.action-buttons {
    text-align: right;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.btn-secondary {
    background: linear-gradient(135deg, #1977cc, #1565c0);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
}

.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(44, 120, 115, 0.3);
}

.status-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #856404;
}

.status-recu {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #0c5460;
}

.status-rejected {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #721c24;
}

.status-termine {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #155724;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 15px;
    }
    
    .page-title {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .form-container {
        padding: 20px;
    }
    
    .form-section {
        padding: 15px;
    }
}
</style>
@endsection