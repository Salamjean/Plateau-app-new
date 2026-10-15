@extends($authData['layout'])

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary: #1f4083;
        --primary-light: #2d5bb9;
        --accent-gold: #d4af37;
        --accent-gold-light: #fef9e7;
        --success: #2ec4b6;
        --danger: #e71d36;
        --dark: #0f172a;
        --gray-100: #f8fafc;
        --gray-200: #e2e8f0;
        --gray-500: #64748b;
    }

    .reversement-container {
        padding: 2rem 2.5rem;
        background: #f7fafc;
        min-height: calc(100vh - 70px);
    }

    /* Page Header */
    .page-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-subtitle {
        color: var(--gray-500);
        font-size: 0.95rem;
        margin: 0.3rem 0 0 0;
    }

    .author-badge {
        background: white;
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
        border: 1px solid var(--gray-200);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .author-badge .badge-role {
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.35rem 0.8rem;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-financier {
        background: var(--primary);
        color: white;
    }

    .badge-comptable {
        background: #0284c7;
        color: white;
    }

    /* Main Form Card */
    .card-modern {
        background: white;
        border-radius: 16px;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-200);
        margin-bottom: 2.5rem;
        overflow: hidden;
    }

    .card-modern-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        padding: 1.25rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-modern-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .card-modern-body {
        padding: 2rem;
    }

    /* Sub-cards inside form */
    .inner-form-card {
        background: #ffffff;
        border: 1.5px solid var(--gray-200);
        border-radius: 14px;
        padding: 1.5rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .inner-form-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .inner-card-header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding-bottom: 0.85rem;
        margin-bottom: 1.25rem;
        border-bottom: 2px solid #f1f5f9;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--primary);
    }

    .inner-card-header i {
        font-size: 1.2rem;
    }

    /* Premier reversement banner */
    .premier-banner {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 2px dashed #f59e0b;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .premier-banner i {
        font-size: 1.8rem;
        color: #d97706;
    }

    .premier-title {
        font-weight: 800;
        font-size: 1rem;
        color: #92400e;
        margin: 0;
    }

    .premier-desc {
        font-size: 0.85rem;
        color: #b45309;
        margin: 0.2rem 0 0 0;
    }

    /* Form Fields */
    .form-group-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-control-custom {
        border: 1.5px solid var(--gray-200);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(31, 64, 131, 0.15);
        outline: none;
    }

    .form-control-custom:read-only {
        background-color: #f1f5f9;
        color: #475569;
        cursor: not-allowed;
        font-weight: 600;
    }

    /* Month Selector Grid */
    .months-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 0.75rem;
        margin-top: 0.75rem;
    }

    .month-checkbox-item {
        position: relative;
    }

    .month-checkbox-item input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 2;
    }

    .month-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 0.5rem;
        border-radius: 10px;
        border: 1.5px solid var(--gray-200);
        background: white;
        font-weight: 600;
        font-size: 0.88rem;
        color: #475569;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        user-select: none;
    }

    .month-checkbox-item input[type="checkbox"]:hover + .month-label {
        border-color: var(--primary);
        background: #f8fafc;
    }

    .month-checkbox-item input[type="checkbox"]:checked + .month-label {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 4px 10px rgba(31, 64, 131, 0.25);
    }

    /* Quick select buttons */
    .quick-actions-bar {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .btn-quick {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        background: #e2e8f0;
        color: #475569;
        border: none;
        transition: 0.2s;
    }

    .btn-quick:hover {
        background: var(--primary);
        color: white;
    }

    /* Author Traceability Box */
    .author-seal-box {
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .seal-avatar {
        width: 50px;
        height: 50px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        font-weight: bold;
    }

    /* Submit Button */
    .btn-submit-reversement {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: white;
        font-weight: 700;
        font-size: 1.05rem;
        padding: 0.85rem 2.5rem;
        border-radius: 50px;
        border: none;
        box-shadow: 0 6px 20px rgba(31, 64, 131, 0.3);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-submit-reversement:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(31, 64, 131, 0.4);
        color: white;
    }
</style>

<div class="reversement-container">

    {{-- Alertes d'erreurs (si applicable) --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                <div>
                    <h5 class="alert-heading mb-1 fw-bold">Erreur de saisie</h5>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- En-tête de la Page --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="page-title">
                <i class="fas fa-hand-holding-usd text-primary"></i>
                Gestion des Reversements
            </h1>
            <p class="page-subtitle">
                Régie des taxes &bull; Mairie de <strong>{{ $authData['commune'] }}</strong> &bull; Saisie et traçabilité des versements
            </p>
        </div>
        <div class="author-badge">
            <span class="badge-role {{ $authData['role'] === 'Financier' ? 'badge-financier' : 'badge-comptable' }}">
                {{ $authData['role'] }}
            </span>
            <div>
                <strong class="d-block text-dark" style="font-size: 0.95rem;">{{ $authData['name'] }}</strong>
                <small class="text-muted">{{ $authData['email'] }}</small>
            </div>
        </div>
    </div>

    {{-- Formulaire de Renseignement du Reversement --}}
    <div class="card-modern">
        <div class="card-modern-header">
            <h3>
                <i class="fas fa-edit"></i>
                Renseigner un Nouveau Reversement
            </h3>
            @php
                $historiqueUrl = Auth::guard('finance')->check() 
                    ? route('finance.reversements.historique') 
                    : route('comptable.reversements.historique');
            @endphp
            <a href="{{ $historiqueUrl }}" class="btn btn-sm btn-light text-primary fw-bold px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-2" style="text-decoration: none;">
                <i class="fas fa-history text-primary"></i>
                <span>Historique des Versements</span>
            </a>
        </div>
        <div class="card-modern-body">

            @php
                $storeRoute = Auth::guard('finance')->check() 
                    ? route('finance.reversements.store') 
                    : route('comptable.reversements.store');
            @endphp

            <form action="{{ $storeRoute }}" method="POST" id="reversementForm">
                @csrf

                <div class="row g-4 align-items-stretch">
                    {{-- CARTE 1 : Informations Financières du Versement --}}
                    <div class="col-lg-6">
                        <div class="inner-form-card">
                            <div class="inner-card-header">
                                <i class="fas fa-coins text-warning"></i>
                                <span>Informations Financières</span>
                            </div>

                            {{-- Bannière dynamique si c'est le 1er reversement --}}
                            @if($isPremier)
                                <div class="premier-banner mb-3">
                                    <i class="fas fa-star text-warning"></i>
                                    <div class="flex-grow-1">
                                        <h5 class="premier-title mb-1">✨ Premier Reversement de la Régie</h5>
                                        <p class="premier-desc mb-0" style="font-size: 0.82rem;">
                                            Aucun reversement n'a encore été enregistré. Ce versement sera automatiquement scellé comme le <strong>Premier Reversement</strong> de la commune.
                                        </p>
                                    </div>
                                    <input type="hidden" name="is_premier" value="1">
                                </div>
                            @else
                                <div class="mb-3 d-flex align-items-center gap-2 p-2 px-3 rounded-3" style="background: #f8fafc; border: 1px solid var(--gray-200);">
                                    <i class="fas fa-info-circle text-primary"></i>
                                    <div>
                                        <strong class="text-dark d-block" style="font-size: 0.85rem;">Type : Reversement Régulier</strong>
                                        <small class="text-muted" style="font-size: 0.75rem;">Versement n° {{ $totalReversementsCount + 1 }}</small>
                                    </div>
                                </div>
                            @endif

                            {{-- Montant du reversement --}}
                            <div class="mb-3">
                                <label class="form-group-title" for="montant">
                                    <i class="fas fa-money-bill-wave text-success"></i>
                                    Montant du Reversement (FCFA) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           name="montant" 
                                           id="montant" 
                                           class="form-control form-control-custom form-control-lg @error('montant') is-invalid @enderror" 
                                           placeholder="Ex: 1 500 000" 
                                           value="{{ old('montant') }}"
                                           min="1" 
                                           step="any" 
                                           required>
                                    <span class="input-group-text bg-light fw-bold text-primary">FCFA</span>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Montant saisi : <strong id="montantFormatPreview" class="text-primary">0 FCFA</strong>
                                </small>
                                @error('montant')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                {{-- Date du reversement (NON MODIFIABLE) --}}
                                <div class="col-sm-7">
                                    <label class="form-group-title" for="date_display">
                                        <i class="fas fa-lock text-danger"></i>
                                        Date du Versement <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;">Fixe</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-calendar-day text-primary"></i></span>
                                        <input type="text" 
                                               id="date_display" 
                                               class="form-control form-control-custom" 
                                               value="{{ $currentDateFormatted }} (Aujourd'hui)" 
                                               readonly>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                        <i class="fas fa-shield-alt text-success"></i> Date système certifiée.
                                    </small>
                                </div>

                                {{-- Année de référence (NON MODIFIABLE) --}}
                                <div class="col-sm-5">
                                    <label class="form-group-title" for="annee">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                        Année <span class="badge bg-secondary ms-1" style="font-size: 0.65rem;">Fixe</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-calendar text-primary"></i></span>
                                        <input type="text" 
                                               id="annee" 
                                               name="annee" 
                                               class="form-control form-control-custom" 
                                               value="{{ $currentYear }}" 
                                               readonly>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                        <i class="fas fa-lock text-danger"></i> Année en cours fixe.
                                    </small>
                                </div>
                            </div>

                            {{-- Commentaire / Justificatif --}}
                            <div class="mb-0">
                                <label class="form-group-title" for="commentaire">
                                    <i class="fas fa-comment-dots text-primary"></i>
                                    Commentaire / Observation
                                </label>
                                <textarea name="commentaire" 
                                          id="commentaire" 
                                          rows="3" 
                                          class="form-control form-control-custom @error('commentaire') is-invalid @enderror" 
                                          placeholder="Numéro de quittance, bordereau de dépôt, compte bancaire, motif...">{{ old('commentaire') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- CARTE 2 : Mois Concernés & Traçabilité --}}
                    <div class="col-lg-6">
                        <div class="inner-form-card">
                            <div class="inner-card-header">
                                <i class="fas fa-calendar-check text-primary"></i>
                                <span>Mois Concernés & Traçabilité</span>
                            </div>

                            {{-- Sélection des mois concernés --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-group-title mb-0">
                                        <i class="fas fa-calendar-week text-primary"></i>
                                        Mois Concernés <span class="text-danger">*</span>
                                    </label>
                                    <div class="quick-actions-bar">
                                        <button type="button" class="btn-quick" id="btnSelectAllMonths">
                                            <i class="fas fa-check-double me-1"></i> Tout cocher
                                        </button>
                                        <button type="button" class="btn-quick" id="btnClearMonths">
                                            <i class="fas fa-times me-1"></i> Tout décocher
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted d-block mb-2" style="font-size: 0.8rem;">
                                    Cochez les mois couverts par ce reversement :
                                </small>

                                <div class="months-grid">
                                    @foreach($frenchMonths as $month)
                                        <div class="month-checkbox-item">
                                            <input type="checkbox" 
                                                   name="mois_concernes[]" 
                                                   id="month_{{ $loop->index }}" 
                                                   value="{{ $month }}"
                                                   {{ (is_array(old('mois_concernes')) && in_array($month, old('mois_concernes'))) ? 'checked' : '' }}>
                                            <label for="month_{{ $loop->index }}" class="month-label">
                                                <i class="far fa-calendar"></i>
                                                <span>{{ $month }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('mois_concernes')
                                    <div class="text-danger small mt-2 fw-bold">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Traçabilité de l'utilisateur qui enregistre --}}
                            <div class="mt-auto pt-2">
                                <label class="form-group-title mb-1">
                                    <i class="fas fa-user-shield text-success"></i>
                                    Certificat d'Enregistrement (Traçabilité)
                                </label>
                                <div class="author-seal-box">
                                    <div class="seal-avatar">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <strong class="text-dark" style="font-size: 0.95rem;">{{ $authData['name'] }}</strong>
                                            <span class="badge {{ $authData['role'] === 'Financier' ? 'bg-primary' : 'bg-info text-dark' }} rounded-pill px-2" style="font-size: 0.72rem;">
                                                {{ $authData['role'] }}
                                            </span>
                                        </div>
                                        <p class="text-muted small mb-0" style="font-size: 0.8rem;">
                                            {{ $authData['email'] }}<br>
                                            <span class="text-success"><i class="fas fa-shield-alt"></i> Saisie scellée sous votre responsabilité.</span>
                                        </p>
                                    </div>
                                    <div class="text-end text-muted small d-none d-sm-block">
                                        <div style="font-size: 0.72rem;"><i class="fas fa-clock text-primary"></i> Horodatage</div>
                                        <strong style="font-size: 0.8rem;">{{ now()->format('d/m/Y H:i') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bouton de soumission --}}
                <div class="text-end mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-submit-reversement">
                        <i class="fas fa-paper-plane me-2"></i>
                        Valider et Enregistrer le Reversement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Notification SweetAlert2 en cas de succès
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Reversement Enregistré !',
            text: "{{ session('success') }}",
            confirmButtonColor: '#1f4083',
            confirmButtonText: '<i class="fas fa-check me-1"></i> D\'accord',
            background: 'white',
            customClass: {
                popup: 'rounded-4 shadow-lg border-0',
                confirmButton: 'rounded-pill px-4 py-2'
            }
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: "{{ session('error') }}",
            confirmButtonColor: '#1f4083',
            confirmButtonText: 'Fermer',
            background: 'white'
        });
        @endif

        // Formattage visuel instantané du montant
        const montantInput = document.getElementById('montant');
        const montantPreview = document.getElementById('montantFormatPreview');

        if (montantInput && montantPreview) {
            montantInput.addEventListener('input', function() {
                const val = parseFloat(this.value);
                if (!isNaN(val) && val > 0) {
                    montantPreview.textContent = new Intl.NumberFormat('fr-FR').format(val) + ' FCFA';
                } else {
                    montantPreview.textContent = '0 FCFA';
                }
            });
        }

        // Boutons Tout cocher / Tout décocher pour les mois
        const btnSelectAll = document.getElementById('btnSelectAllMonths');
        const btnClear = document.getElementById('btnClearMonths');
        const monthCheckboxes = document.querySelectorAll('.month-checkbox-item input[type="checkbox"]');

        if (btnSelectAll) {
            btnSelectAll.addEventListener('click', function() {
                monthCheckboxes.forEach(cb => cb.checked = true);
            });
        }

        if (btnClear) {
            btnClear.addEventListener('click', function() {
                monthCheckboxes.forEach(cb => cb.checked = false);
            });
        }

        // Validation avant soumission avec SweetAlert2
        const reversementForm = document.getElementById('reversementForm');
        if (reversementForm) {
            reversementForm.addEventListener('submit', function(e) {
                let anyMonthChecked = false;
                monthCheckboxes.forEach(cb => {
                    if (cb.checked) anyMonthChecked = true;
                });

                if (!anyMonthChecked) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Mois requis',
                        text: 'Veuillez sélectionner au moins un mois concerné par le reversement.',
                        confirmButtonColor: '#1f4083',
                        confirmButtonText: 'Compris'
                    });
                    return false;
                }
            });
        }
    });
</script>
@endsection
