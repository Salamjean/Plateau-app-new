@extends('livreur.layouts.template')

@section('content')
<style>
    :root {
        --primary: #1f4083;
        --primary-light: rgba(31, 64, 131, 0.1);
        --secondary: #ea8c51;
        --success: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --bg-body: #f4f7fe;
        --text-main: #2d3748;
        --text-muted: #718096;
        --card-bg: #ffffff;
        --radius: 16px;
        --shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-title i {
        background: var(--primary-light);
        padding: 10px;
        border-radius: 12px;
        font-size: 1.25rem;
    }

    .validation-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .modern-card {
        background: var(--card-bg);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .card-header-accent {
        height: 6px;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
    }

    .card-body {
        padding: 2.5rem;
    }

    .instruction-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 2rem;
        border: 1px dashed #cbd5e1;
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .instruction-box i {
        color: var(--warning);
        font-size: 1.5rem;
    }

    .instruction-text h4 {
        margin: 0 0 5px 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .instruction-text p {
        margin: 0;
        font-size: 0.9rem;
        color: var(--text-muted);
    }

    .form-label {
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.75rem;
        display: block;
        font-size: 0.95rem;
    }

    .search-input-group {
        display: flex;
        gap: 12px;
        margin-bottom: 0.5rem;
    }

    .modern-input {
        flex-grow: 1;
        background: #f1f5f9;
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 12px 20px;
        font-size: 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
        color: var(--text-main);
    }

    .modern-input:focus {
        background: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--primary-light);
        outline: none;
    }

    .verify-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0 25px;
        border-radius: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .verify-btn:hover {
        background: #163266;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(31, 64, 131, 0.2);
    }

    .verify-btn:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        transform: none;
    }

    /* Message Styles */
    #messageContainer {
        margin-bottom: 1.5rem;
    }

    .custom-alert {
        padding: 1rem 1.25rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideIn 0.4s ease-out;
    }

    .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #10b981; }
    .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #ef4444; }

    @keyframes slideIn {
        from { opacity:0; transform: translateY(-10px); }
        to { opacity:1; transform: translateY(0); }
    }

    /* Result Panel */
    #demandeInfos {
        display: none;
        margin-top: 2.5rem;
        padding-top: 2.5rem;
        border-top: 1px solid #e2e8f0;
        animation: fadeIn 0.5s ease;
    }

    .result-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 2rem;
    }

    .info-card {
        background: #f8fafc;
        padding: 15px 20px;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        background: white;
        border-color: var(--primary-light);
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }

    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 5px;
        font-weight: 700;
    }

    .info-value {
        font-size: 1rem;
        color: var(--text-main);
        font-weight: 600;
    }

    .code-colis-wrapper {
        grid-column: span 2;
        background: var(--primary);
        color: white;
        padding: 20px;
        border-radius: 16px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .code-colis-wrapper::before {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .code-colis-value {
        font-size: 2.25rem;
        font-weight: 800;
        letter-spacing: 3px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .btn-confirm {
        background: linear-gradient(135deg, var(--success), #059669);
        color: white;
        border: none;
        width: 100%;
        padding: 16px;
        border-radius: 14px;
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
    }

    .btn-confirm:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        transform: none;
    }

    .loading-spinner {
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s linear infinite;
        display: none;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 640px) {
        .info-grid { grid-template-columns: 1fr; }
        .code-colis-wrapper { grid-column: span 1; }
        .search-input-group { flex-direction: column; }
        .verify-btn { padding: 12px; justify-content: center; }
    }
</style>

<div class="validation-container">
    <div class="page-header">
        <h2 class="page-title">
            <i class="material-icons">verified_user</i>
            Validation de Livraison
        </h2>
    </div>

    <div class="modern-card">
        <div class="card-header-accent"></div>
        <div class="card-body">
            
            <div class="instruction-box">
                <i class="material-icons">info</i>
                <div class="instruction-text">
                    <h4>Scanner ou Saisir</h4>
                    <p>Veuillez entrer le numéro de référence fourni par le client pour authentifier la livraison du colis.</p>
                </div>
            </div>

            <div id="messageContainer"></div>

            <form id="validationForm" method="POST" onsubmit="return handleFormSubmit(event)">
                @csrf
                
                <div class="form-group">
                    <label class="form-label" for="referenceInput">Référence du Colis</label>
                    <div class="search-input-group">
                        <input type="text" class="modern-input" id="referenceInput" 
                               placeholder="Ex: REF-2024-XXXX" required autocomplete="off">
                        <button class="verify-btn" type="button" id="checkReferenceBtn">
                            <span id="btnText">Vérifier</span>
                            <div id="btnLoading" class="loading-spinner"></div>
                        </button>
                    </div>
                </div>

                <div id="demandeInfos">
                    <h3 class="result-title">
                        <i class="material-icons text-primary" style="color: var(--primary);">inventory</i>
                        Vérification des Détails
                    </h3>
                    
                    <div class="info-grid">
                        <div class="code-colis-wrapper">
                            <div class="info-label" style="color: rgba(255,255,255,0.8)">Code de Livraison (OTP)</div>
                            <div class="code-colis-value" id="codeColis"></div>
                            <div style="font-size: 0.75rem; margin-top: 5px; opacity: 0.9;">Demandez ce code au client pour valider</div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">Référence Colis</div>
                            <div class="info-value" id="refColisLabel"></div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-label">Destinataire</div>
                            <div class="info-value" id="destinataire"></div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">Contact</div>
                            <div class="info-value" id="contact"></div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">Email</div>
                            <div class="info-value" id="typeDemande"></div>
                        </div>

                        <div class="info-card">
                            <div class="info-label">Quartier / Commune</div>
                            <div class="info-value" id="villeCommune"></div>
                        </div>

                        <div class="info-card" style="grid-column: span 2">
                            <div class="info-label">Adresse Précise</div>
                            <div class="info-value" id="adresse"></div>
                        </div>
                    </div>

                    <input type="hidden" name="reference" id="referenceHidden">
                    <input type="hidden" name="demande_id" id="demandeId">
                    <input type="hidden" name="demande_type" id="demandeType">
                    
                    <button type="submit" id="confirmBtn" class="btn-confirm">
                        <i class="material-icons">task_alt</i>
                        <span id="confirmBtnText">Confirmer la Livraison</span>
                        <div id="confirmBtnLoading" class="loading-spinner"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    function showMessage(type, message) {
        const icon = type === 'success' ? 'check_circle' : 'error_outline';
        const html = `
            <div class="custom-alert alert-${type}">
                <i class="material-icons">${icon}</i>
                <span>${message}</span>
            </div>
        `;
        $('#messageContainer').html(html);
        if (type === 'error') {
            setTimeout(() => $('#messageContainer').empty(), 6000);
        }
    }

    $('#checkReferenceBtn').on('click', function() {
        const reference = $('#referenceInput').val().trim();
        if (!reference) {
            showMessage('error', 'Veuillez saisir une référence.');
            return;
        }

        $('#btnText').hide();
        $('#btnLoading').show();
        $('#checkReferenceBtn').prop('disabled', true);
        $('#demandeInfos').hide();
        $('#messageContainer').empty();

        $.ajax({
            url: "{{ route('livreur.check-reference') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                reference: reference
            },
            success: function(response) {
                if (response.success) {
                    $('#codeColis').text(response.data.livraison_code || '---'); 
                    $('#refColisLabel').text(reference);
                    $('#destinataire').text(response.data.destinataire);
                    $('#contact').text(response.data.contact);
                    $('#typeDemande').text(response.data.email || 'Non renseigné');
                    $('#adresse').text(response.data.adresse || 'N/A');
                    $('#villeCommune').text((response.data.commune || '') + ' ' + (response.data.quartier || ''));
                    
                    $('#referenceHidden').val(reference);
                    $('#demandeId').val(response.data.id);
                    $('#demandeType').val(response.data.type);
                    
                    $('#demandeInfos').fadeIn();
                    showMessage('success', 'Référence valide ! Veuillez vérifier les informations.');
                } else {
                    showMessage('error', response.message);
                }
            },
            error: function(xhr) {
                let msg = 'Une erreur est survenue lors de la vérification.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                showMessage('error', msg);
            },
            complete: function() {
                $('#btnText').show();
                $('#btnLoading').hide();
                $('#checkReferenceBtn').prop('disabled', false);
            }
        });
    });
});

function handleFormSubmit(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const confirmBtn = $('#confirmBtn');
    const confirmBtnText = $('#confirmBtnText');
    const confirmBtnLoading = $('#confirmBtnLoading');

    confirmBtn.prop('disabled', true);
    confirmBtnText.hide();
    confirmBtnLoading.show();

    $.ajax({
        url: "{{ route('livreur.validated') }}",
        method: 'POST',
        data: $(form).serialize(),
        success: function(response) {
            if (response.success) {
                // Utiliser displayMessage pour le succès avant de rediriger
                const html = `
                    <div class="custom-alert alert-success">
                        <i class="material-icons">check_circle</i>
                        <span>${response.message}</span>
                    </div>
                `;
                $('#messageContainer').html(html);
                setTimeout(() => {
                    window.location.href = response.redirect;
                }, 1500);
            } else {
                const html = `
                    <div class="custom-alert alert-error">
                        <i class="material-icons">error_outline</i>
                        <span>${response.message}</span>
                    </div>
                `;
                $('#messageContainer').html(html);
                confirmBtn.prop('disabled', false);
                confirmBtnText.show();
                confirmBtnLoading.hide();
            }
        },
        error: function(xhr) {
            let msg = 'Une erreur est survenue lors de la validation.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            
            const html = `
                <div class="custom-alert alert-error">
                    <i class="material-icons">error_outline</i>
                    <span>${msg}</span>
                </div>
            `;
            $('#messageContainer').html(html);
            confirmBtn.prop('disabled', false);
            confirmBtnText.show();
            confirmBtnLoading.hide();
        }
    });

    return false;
}
</script>
@endsection
