@extends('dhl.layouts.template')

@section('content')
<style>
    :root {
        --primary-color: #d40511;
        --secondary-color: #f9cf03;
        --light-color: #f8f9fa;
    }
    
    .small-card-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem 20px;
    }
    
    .small-demand-card {
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        border-top: 3px solid var(--secondary-color);
        transition: all 0.3s ease;
        background: white;
        overflow: hidden;
    }
    
    .small-demand-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .small-card-header {
        background-color: rgba(6, 99, 78, 0.05);
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .small-card-ref {
        font-weight: 600;
        color: var(--primary-color);
        font-size: 0.9rem;
    }
    
    .small-card-type {
        background-color: var(--secondary-color);
        color: var(--primary-color);
        padding: 0.2rem 0.6rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .small-card-body {
        padding: 1rem;
    }
    
    .small-card-row {
        display: flex;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }
    
    .small-card-label {
        font-weight: 600;
        color: var(--primary-color);
        min-width: 80px;
        font-size: 0.8rem;
    }
    
    .status-en-attente {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .status-en-cours {
        background-color: #cce5ff;
        color: #004085;
        border: 1px solid #b8daff;
    }

    .status-livre {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .small-card-status {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .small-card-footer {
        padding: 0.75rem 1rem;
        background-color: rgba(249, 207, 3, 0.05);
        border-top: 1px solid #eee;
        text-align: right;
    }
    
    .btn-assigner {
        background-color: var(--primary-color);
        color: white;
        border: 2px solid #f9cf03;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.85rem;
        transition: all 0.2s;
        margin-left: 10px;
    }
    
    .btn-assigner:hover {
        background-color: #8b030a;
        transform: translateY(-1px);
        cursor: pointer;
    }
    
    .btn-select-all {
        background-color: var(--secondary-color);
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.85rem;
        transition: all 0.2s;
        font-weight: bold;
    }
    
    .btn-select-all:hover {
        background-color: #e6b800;
        transform: translateY(-1px);
        cursor: pointer;
    }
    
    .empty-state-small {
        text-align: center;
        padding: 2rem;
        grid-column: 1 / -1;
    }
    
    .empty-icon-small {
        color: var(--secondary-color);
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    
    .checkbox-container {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }
    
    .checkbox-label {
        margin-right: 0.5rem;
        font-size: 0.8rem;
        color: var(--primary-color);
    }
    
    .demande-checkbox {
        width: 18px;
        height: 18px;
        accent-color: var(--primary-color);
        cursor: pointer;
    }
    
    .demande-checkbox:disabled {
        cursor: not-allowed;
        opacity: 0.5;
    }
    
    .action-buttons {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 1rem;
        padding: 0 20px;
    }
    
    .badge-count {
        background-color: var(--secondary-color);
        color: var(--primary-color);
        padding: 5px;
        border: 2px solid var(--primary-color);
        border-radius: 50%;
        font-weight: bold;
        margin-left: 5px;
    }
    
    .card-disabled {
        opacity: 0.8;
        background-color: #f8f9fa;
    }
</style>
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3" style="margin: 0 25px">
        <h4 style="color: var(--primary-color);">
            <i class="fas fa-clipboard-check mr-2"></i> Vos colis enregistrés
        </h4>
        <div>
            <span class="badge badge-pill mr-2" style="background-color: var(--secondary-color); color: var(--primary-color);padding:5px; border:2px solid var(--primary-color)">
                {{ $demandes->count() }} colis pas encore livré(s)
            </span>
        </div>
    </div>
    
    <div class="action-buttons">
        <button id="selectAllBtn" class="btn-select-all">
            <i class="fas fa-check-circle mr-1"></i> Tout sélectionner
        </button>
        <button id="assignerBtn" class="btn-assigner" disabled>
            <i class="fas fa-home mr-1"></i> Attribuer à une agence
            <span id="selectedCount" class="badge-count" style="display: none;">0</span>
        </button>
    </div>
    
    <div class="small-card-container">
        @if($demandes->isEmpty())
            <div class="empty-state-small">
                <div class="empty-icon-small">
                    <i class="fas fa-inbox"></i>
                </div>
                <h5 style="color: var(--primary-color);">Aucun colis enregistré</h5>
                <p class="text-muted small">Les colis enregistré apparaîtront ici.</p>
            </div>
       @else
        @foreach($demandes as $demande)
            <div class="small-demand-card @if($demande->statut_livraison === 'en cours') card-disabled @endif">
                <div class="small-card-header">
                    <span class="small-card-ref">{{ $demande->livraison_code }}</span>
                    <span class="small-card-type">{{ Str::limit($demande->type_demande, 5) }}</span>
                </div>
                
                <div class="small-card-body">
                    <div class="small-card-row">
                        <span class="small-card-label">Option:</span>
                        <span>{{ $demande->choix_option === 'livraison' ? 'Livraison' : 'Retrait' }}</span>
                    </div>
                    
                    @if($demande->choix_option === 'livraison')
                        <div class="small-card-row">
                            <span class="small-card-label">Destinataire:</span>
                            <span>{{ Str::limit($demande->nom_destinataire.' '.$demande->prenom_destinataire, 20) }}</span>
                        </div>
                        
                        <div class="small-card-row">
                            <span class="small-card-label">Email :</span>
                            <span>{{ $demande->email_destinataire ?? 'N/A' }}</span>
                        </div>
                        <div class="small-card-row">
                            <span class="small-card-label">Contact :</span>
                            <span>{{ $demande->contact_destinataire ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="small-card-row">
                            <span class="small-card-label">Ville:</span>
                            <span>{{ Str::limit($demande->ville ?? 'N/A', 20) }}</span>
                        </div>
                        <div class="small-card-row">
                            <span class="small-card-label">Commune:</span>
                            <span>{{ Str::limit($demande->commune_livraison ?? 'N/A', 20) }}</span>
                        </div>
                        <div class="small-card-row">
                            <span class="small-card-label">Quartier:</span>
                            <span>{{ Str::limit($demande->quartier ?? 'N/A', 20) }}</span>
                        </div>
                        <div class="small-card-row">
                            <span class="small-card-label">Code postal:</span>
                            <span>{{ Str::limit($demande->code_postal ?? 'N/A', 20) }}</span>
                        </div>
                        <div class="small-card-row">
                            <span class="small-card-label">Lieu:</span>
                            <span>{{ Str::limit($demande->adresse_livraison ?? $demande->commune_livraison ?? $demande->ville ?? 'N/A', 20) }}</span>
                        </div>
                    @else
                        <div class="small-card-row">
                            <span class="small-card-label">Retrait:</span>
                            <span>{{ $demande->commune ?? 'Non précisé' }}</span>
                        </div>
                    @endif
                    
                    <div class="small-card-row">
                        <span class="small-card-label">Montant:</span>
                        <span>{{ $demande->montant_livraison }} FCFA</span>
                    </div>
                    
                    <span class="small-card-status status-{{ Str::slug($demande->statut_livraison) }}">
                        {{ $demande->statut_livraison }}
                    </span>
                </div>
                
                <div class="small-card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            {{ $demande->created_at->format('d/m/Y H:i') }}
                        </small>
                        <div class="checkbox-container">
                            <label class="checkbox-label">Sélectionner</label>
                            <input type="checkbox" class="demande-checkbox" 
                                   data-id="{{ $demande->id }}" 
                                   data-type="{{ Str::slug($demande->type_demande) }}"
                            @if($demande->statut_livraison === 'en cours' || $demande->statut_livraison === 'livré') disabled @endif>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let allSelected = false;
    
    // Bouton Tout sélectionner/désélectionner
    $('#selectAllBtn').click(function() {
        allSelected = !allSelected;
        $('.demande-checkbox:not(:disabled)').prop('checked', allSelected).trigger('change');
        $(this).html(allSelected 
            ? '<i class="fas fa-times-circle mr-1"></i> Tout désélectionner' 
            : '<i class="fas fa-check-circle mr-1"></i> Tout sélectionner');
    });
    
    // Mise à jour du compteur et de l'état des boutons
    function updateSelectionState() {
        const selectedCount = $('.demande-checkbox:checked:not(:disabled)').length;
        $('#selectedCount').text(selectedCount).toggle(selectedCount > 0);
        $('#assignerBtn').prop('disabled', selectedCount === 0);
        
        // Mettre à jour l'état de "Tout sélectionner"
        const totalSelectable = $('.demande-checkbox:not(:disabled)').length;
        const selectedSelectable = $('.demande-checkbox:checked:not(:disabled)').length;
        
        if (selectedSelectable === totalSelectable && totalSelectable > 0) {
            allSelected = true;
            $('#selectAllBtn').html('<i class="fas fa-times-circle mr-1"></i> Tout désélectionner');
        } else if (selectedSelectable === 0) {
            allSelected = false;
            $('#selectAllBtn').html('<i class="fas fa-check-circle mr-1"></i> Tout sélectionner');
        }
    }
    
    // Gestion de la sélection des demandes
    $('.demande-checkbox').change(updateSelectionState);
    
    // Initialisation de l'état
    updateSelectionState();
    
    // Gestion du clic sur le bouton d'attribution
    $('#assignerBtn').click(function() {
        // Récupérer les données des demandes sélectionnées
        var selectedDemandes = [];
        $('.demande-checkbox:checked:not(:disabled)').each(function() {
            selectedDemandes.push({
                id: $(this).data('id'),
                type: $(this).data('type')
            });
        });
        
        if (selectedDemandes.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Aucune sélection',
                text: 'Veuillez sélectionner au moins une demande à attribuer.',
                confirmButtonColor: '#06634e',
            });
            return;
        }
        
        // Afficher la popup SweetAlert2 avec la liste des livreurs
        Swal.fire({
            title: 'Attribuer les demandes',
            html: `
                <div class="form-group">
                    <label for="livreurSelect">Sélectionnez un livreur :</label>
                    <select class="form-control" id="livreurSelect" style="padding:10px">
                        <option value="">-- Sélectionnez une agence DHL --</option>
                        @foreach($livreurs as $livreur)
                            <option value="{{ $livreur->id }}">{{ $livreur->name }} ({{ $livreur->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-3 text-muted small">
                    <i class="fas fa-info-circle"></i> ${selectedDemandes.length} demande(s) seront attribuées
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Attribuer',
            cancelButtonText: 'Annuler',
            cancelButtonColor: '#d40511',
            confirmButtonColor: '#f9cf03',
            width: '600px',
            preConfirm: () => {
                const livreurId = $('#livreurSelect').val();
                if (!livreurId) {
                    Swal.showValidationMessage('Veuillez sélectionner une agennce DHL');
                }
                return { livreurId: livreurId };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const livreurId = result.value.livreurId;
                
                // Afficher un loader pendant l'envoi
                Swal.fire({
                    title: 'Attribution en cours',
                    html: 'Veuillez patienter...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Envoyer la requête AJAX pour attribuer les demandes
                $.ajax({
                    url: '{{ route("dhl.assigner-livreur") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        demandes: selectedDemandes,
                        livreur_id: livreurId
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Succès',
                                text: response.message,
                                confirmButtonColor: '#d40511',
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: response.message,
                                confirmButtonColor: '#d40511',
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de l\'attribution.',
                            confirmButtonColor: '#d40511',
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection