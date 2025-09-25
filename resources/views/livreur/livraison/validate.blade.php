@extends('livreur.layouts.template')
@section('content')
<style>
    :root {
        --primary: #1977cc;
        --primary-dark: #0f5da0;
        --secondary: #ea8c51;
        --secondary-dark: #ea8c51;
        --light: #f8f9fa;
        --dark: #343a40;
        --success: #28a745;
        --danger: #dc3545;
        --border-radius: 12px;
        --box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }
    
    .validation-card {
        max-width: 720px;
        margin: 2rem auto;
        padding: 2.5rem;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        border-top: 4px solid var(--primary);
    }
    
    .validation-header {
        text-align: center;
        margin-bottom: 2rem;
        color: var(--primary);
    }
    
    .validation-header i {
        font-size: 1.8rem;
        vertical-align: middle;
        margin-right: 0.5rem;
    }
    
    .form-group {
        margin-bottom: 1.75rem;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-control {
        border-radius: var(--border-radius);
        padding: 0.75rem 1.25rem;
        border: 1px solid #e0e0e0;
        transition: var(--transition);
    }
    
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.2rem rgba(6, 99, 78, 0.2);
    }
    
    /* Style amélioré pour le bouton Vérifier */
    .verify-btn {
        background-color: var(--secondary);
        color: var(--dark);
        font-weight: 600;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius);
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 120px;
        box-shadow: 0 2px 8px rgba(249, 207, 3, 0.3);
    }
    
    .verify-btn:hover {
        background-color: var(--secondary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(249, 207, 3, 0.4);
    }
    
    .verify-btn i {
        margin-right: 8px;
    }
    
    .btn-validate {
        background-color: var(--primary);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius);
        font-weight: 600;
        width: 100%;
        transition: var(--transition);
        margin-top: 1rem;
        cursor: pointer;
    }
    
    .btn-validate:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(6, 99, 78, 0.2);
    }
    
    /* Styles personnalisés pour les messages */
    .alert-message {
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        border-radius: var(--border-radius);
        display: flex;
        align-items: center;
        animation: fadeIn 0.3s ease-out;
    }
    
    .alert-success {
        background-color: rgba(40, 167, 69, 0.15);
        border-left: 4px solid var(--success);
        color: var(--success);
    }
    
    .alert-error {
        background-color: rgba(220, 53, 69, 0.15);
        border-left: 4px solid var(--danger);
        color: var(--danger);
    }
    
    .alert-warning {
        background-color: rgba(255, 193, 7, 0.15);
        border-left: 4px solid #ffc107;
        color: #d39e00;
    }
    
    .alert-message i {
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }
    
    .alert-close {
        margin-left: auto;
        cursor: pointer;
        background: none;
        border: none;
        color: inherit;
        font-size: 1.25rem;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    #demandeInfos {
        display: none;
        animation: fadeIn 0.4s ease-out;
        background: rgba(248, 249, 250, 0.6);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        margin-top: 1.5rem;
        border-left: 4px solid var(--primary);
    }
    
    .info-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .badge-primary {
        background-color: rgba(6, 99, 78, 0.1);
        color: var(--primary);
    }
    
    .loading-spinner {
        display: none;
        width: 1.25rem;
        height: 1.25rem;
        border: 3px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top-color: var(--dark);
        animation: spin 1s ease-in-out infinite;
        margin-left: 8px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Disposition en grille pour les informations */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .info-item {
        background: white;
        padding: 1rem;
        border-radius: var(--border-radius);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    
    .info-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-weight: 600;
        color: var(--dark);
        font-size: 1.05rem;
    }
    #codeColis {
    font-size: 2rem; /* plus grand */
    font-weight: 700; /* gras */
    color: var(--primary); /* couleur principale */
    background: rgba(6, 99, 78, 0.1); /* fond léger */
    padding: 0.75rem 1rem; /* espace interne */
    border-radius: var(--border-radius);
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* léger relief */
    transition: all 0.3s ease;
    letter-spacing: 1px; /* espacement des lettres pour visibilité */
}

#codeColis:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
}

    
    .full-width-item {
        grid-column: span 2;
    }
    
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
        
        .full-width-item {
            grid-column: span 1;
        }
    }
</style>

<div class="validation-card">
    <div class="validation-header">
        <h2><i class="fas fa-users"></i> Validation de Livraison</h2>
        <p class="text-muted">Le client vous fournira la référence pour la validation</p>
    </div>
    
    <!-- Container pour les messages -->
    <div id="messageContainer"></div>
    
    <form id="validationForm" method="POST" action="{{ route('livreur.validated') }}">
        @csrf
        
        <div class="form-group">
            <label for="referenceInput" class="form-label">Référence de la demande</label>
            <div class="d-flex" style="gap: 0.75rem;">
                <input type="text" class="form-control flex-grow-1" id="referenceInput" 
                       placeholder="Saisissez ou scannez la référence" required>
                <button class="verify-btn text-white" type="button" id="checkReferenceBtn">
                    <i class="fas fa-search"></i>
                    <span id="btnText" >Vérifier</span>
                    <span id="btnLoading" class="loading-spinner"></span>
                </button>
            </div>
            <small class="form-text text-muted mt-1">
                <i class="fas fa-info-circle"></i> La référence se trouve sur le bon de livraison ou le colis
            </small>
        </div>
        
        <div id="demandeInfos">
            <span class="info-badge badge-primary">
                <i class="fas fa-info-circle"></i> Informations de la demande
            </span>
            
            <div class="info-grid">
                <div class="info-item full-width-item">
                    <div class="info-label">Code du colis</div>
                    <div class="info-value" id="codeColis"></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Destinataire</div>
                    <div class="info-value" id="destinataire"></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value" id="typeDemande"></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Contact</div>
                    <div class="info-value" id="contact"></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Montant</div>
                    <div class="info-value" id="montant"></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Adresse</div>
                    <div class="info-value" id="adresse"></div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Ville/Commune/Quartier</div>
                    <div class="info-value" id="villeCommune"></div>
                </div>
            </div>
            
            <input type="hidden" name="reference" id="referenceHidden">
            <input type="hidden" name="demande_id" id="demandeId">
            <input type="hidden" name="demande_type" id="demandeType">
            
            <button type="submit" class="btn btn-validate">
                <i class="fas fa-check-circle mr-2"></i> Confirmer la Livraison
            </button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
$(document).ready(function() {
    // Fonction pour afficher les messages
    function showMessage(type, message) {
        const icon = type === 'success' ? 'check-circle' : 
                    type === 'error' ? 'exclamation-circle' : 'exclamation-triangle';
        
        const messageHtml = `
            <div class="alert-message alert-${type}">
                <i class="fas fa-${icon}"></i>
                <span>${message}</span>
                <button class="alert-close">&times;</button>
            </div>
        `;
        
        $('#messageContainer').html(messageHtml);
        
        // Fermer le message après 5 secondes
        setTimeout(() => {
            $('.alert-message').fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // Fermer le message manuellement
    $(document).on('click', '.alert-close', function() {
        $(this).closest('.alert-message').fadeOut(300, function() {
            $(this).remove();
        });
    });

    // Afficher les messages de session
    @if(session('success'))
        showMessage('success', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
        showMessage('error', '{{ session('error') }}');
    @endif

    // Vérification de la référence
    $('#checkReferenceBtn').click(function() {
        const reference = $('#referenceInput').val().trim();
        
        if (!reference) {
            showMessage('warning', 'Veuillez entrer une référence valide');
            return;
        }
        
        // Afficher le loader
        $('#btnText').hide();
        $('#btnLoading').show();
        $('#checkReferenceBtn').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("livreur.check-reference") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                reference: reference
            },
            success: function(response) {
                if (response.success) {
                    // Remplir les champs avec les données reçues
                    $('#typeDemande').text(response.data.email);
                    $('#codeColis').text(response.data.livraison_code);
                    $('#destinataire').text(response.data.destinataire);
                    $('#contact').text(response.data.contact);
                    $('#adresse').text(response.data.adresse);
                    $('#villeCommune').text(response.data.ville + ' / ' + response.data.commune + ' / ' + response.data.quartier);
                    $('#montant').text(formatCurrency(response.data.montant));
                    
                    // Remplir les champs cachés
                    $('#referenceHidden').val(reference);
                    $('#demandeId').val(response.data.id);
                    $('#demandeType').val(response.data.type);
                    
                    // Afficher la section des informations
                    $('#demandeInfos').slideDown();
                    
                    // Feedback visuel
                    showMessage('success', 'Référence validée avec succès');
                } else {
                    showMessage('error', response.message);
                    $('#demandeInfos').hide();
                }
            },
            error: function(xhr) {
                let message = 'Une erreur est survenue lors de la vérification';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showMessage('error', message);
                $('#demandeInfos').hide();
            },
            complete: function() {
                // Cacher le loader
                $('#btnText').show();
                $('#btnLoading').hide();
                $('#checkReferenceBtn').prop('disabled', false);
            }
        });
    });
    
    // Validation avec la touche Entrée
    $('#referenceInput').keypress(function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#checkReferenceBtn').click();
        }
    });
    
    // Formatage du montant
    function formatCurrency(amount) {
        return new Intl.NumberFormat('fr-FR', { 
            style: 'currency', 
            currency: 'XOF' 
        }).format(amount).replace('XOF', 'FCFA');
    }
    
    // Simulation de scan (pour démo)
    $('#referenceInput').on('paste', function(e) {
        setTimeout(() => {
            $('#checkReferenceBtn').click();
        }, 100);
    });

    // Gestion de la soumission du formulaire - Version corrigée
    $('#validationForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!$('#demandeId').val()) {
            showMessage('warning', 'Veuillez d\'abord vérifier une référence valide');
            return;
        }

        // Afficher un indicateur de chargement
        const validateBtn = $('.btn-validate');
        validateBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Validation en cours...');
        validateBtn.prop('disabled', true);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response && typeof response.success !== 'undefined') {
                    if (response.success) {
                        // Afficher le message de succès
                        showMessage('success', response.message || 'Livraison confirmée avec succès');
                        
                        // Redirection après 2 secondes
                        setTimeout(function() {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                // Redirection par défaut si aucune URL n'est fournie
                                window.location.href = "{{ route('delivery.dashboard') }}";
                            }
                        }, 2000);
                        
                    } else {
                        showMessage('error', response.message || 'Erreur lors de la confirmation');
                    }
                } else {
                    showMessage('error', 'Réponse serveur inattendue');
                    console.error('Réponse serveur:', response);
                }
            },
            error: function(xhr) {
                let message = 'Une erreur est survenue';
                if (xhr.responseJSON) {
                    // Essayer de récupérer le message d'erreur de différentes manières
                    message = xhr.responseJSON.message || 
                             xhr.responseJSON.error ||
                             JSON.stringify(xhr.responseJSON);
                } else if (xhr.responseText) {
                    try {
                        const jsonResponse = JSON.parse(xhr.responseText);
                        message = jsonResponse.message || jsonResponse.error || xhr.responseText;
                    } catch (e) {
                        message = xhr.responseText;
                    }
                } else if (xhr.statusText) {
                    message = xhr.statusText;
                }
                showMessage('error', message);
            },
            complete: function() {
                validateBtn.html('<i class="fas fa-check-circle mr-2"></i> Confirmer la Livraison');
                validateBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection