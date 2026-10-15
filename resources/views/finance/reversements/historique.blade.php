@extends($authData['layout'])

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary: #1f4083;
        --primary-light: #2d5bb9;
        --accent-gold: #d4af37;
        --dark: #0f172a;
        --gray-100: #f8fafc;
        --gray-200: #e2e8f0;
        --gray-500: #64748b;
    }

    .historique-container {
        padding: 2rem 2.5rem;
        background: #f7fafc;
        min-height: calc(100vh - 70px);
    }

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

    .btn-back-form {
        background: white;
        color: var(--primary);
        border: 2px solid var(--primary);
        font-weight: 700;
        padding: 0.6rem 1.4rem;
        border-radius: 50px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 10px rgba(31, 64, 131, 0.1);
    }

    .btn-back-form:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(31, 64, 131, 0.25);
    }

    /* Filter Bar Card */
    .filter-card {
        background: white;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        border: 1px solid var(--gray-200);
        margin-bottom: 1.5rem;
    }

    /* Modern Table Card */
    .card-modern {
        background: white;
        border-radius: 16px;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--gray-200);
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
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .table-custom {
        margin: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    /* TOUS LES ÉLÉMENTS DU TABLEAU SONT CENTRÉS */
    .table-custom th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
        border-bottom: 2px solid var(--gray-200);
        text-align: center !important;
        vertical-align: middle !important;
    }

    .table-custom td {
        padding: 1.1rem 0.75rem;
        vertical-align: middle !important;
        text-align: center !important;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.95rem;
    }

    .table-custom tbody tr:hover {
        background-color: #f8fafc;
    }

    .badge-premier {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.78rem;
        box-shadow: 0 2px 6px rgba(217, 119, 6, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .badge-standard {
        background: #e2e8f0;
        color: #475569;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.78rem;
        display: inline-block;
    }

    .badge-month {
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        display: inline-block;
        margin: 2px;
    }

    .btn-detail {
        background: white;
        color: var(--primary);
        border: 1.5px solid var(--primary);
        border-radius: 50px;
        padding: 0.4rem 1.1rem;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        box-shadow: 0 2px 5px rgba(31, 64, 131, 0.08);
    }

    .btn-detail:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(31, 64, 131, 0.2);
    }

    /* Modal Styling */
    .recu-card-header {
        background: linear-gradient(135deg, #1f4083, #2d5bb9);
        color: white;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        padding: 1.5rem 1.75rem;
    }

    .recu-watermark {
        position: relative;
    }

    .info-label {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .info-value-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 1rem;
    }

    /* Impression propre du reçu */
    @media print {
        body * {
            visibility: hidden;
        }
        #recuModal, #recuModal * {
            visibility: visible;
        }
        #recuModal {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .modal-footer, .btn-close {
            display: none !important;
        }
    }
</style>

<div class="historique-container">

    {{-- En-tête avec bouton retour vers le formulaire --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="page-title">
                <i class="fas fa-history text-primary"></i>
                Historique des Versements
            </h1>
            <p class="page-subtitle">
                Régie des taxes &bull; Mairie de <strong>{{ $authData['commune'] }}</strong> &bull; Liste et traçabilité de tous les reversements
            </p>
        </div>
        <div>
            @php
                $formRoute = Auth::guard('finance')->check() 
                    ? route('finance.reversements.index') 
                    : route('comptable.reversements.index');
            @endphp
            <a href="{{ $formRoute }}" class="btn-back-form">
                <i class="fas fa-plus-circle"></i>
                Nouveau Reversement
            </a>
        </div>
    </div>

    {{-- Barre de filtres et récapitulatif --}}
    <div class="filter-card">
        <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-center">
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold mb-1" for="year">Filtrer par Année</label>
                <select name="year" id="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Toutes les années</option>
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                            Année {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold mb-1" for="month">Filtrer par Mois</label>
                <select name="month" id="month" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Tous les mois</option>
                    @foreach($frenchMonths as $m)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                @if($selectedYear || $selectedMonth)
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i> Réinitialiser
                    </a>
                @endif
            </div>

            <div class="col-md-4 text-center text-md-end">
                <span class="text-muted small d-block">Total Reversé :</span>
                <strong class="text-primary fs-5">{{ number_format($totalMontant, 0, ',', ' ') }} FCFA</strong>
                <small class="text-muted d-block">({{ $totalCount }} reversement(s))</small>
            </div>
        </form>
    </div>

    {{-- Carte Tableau --}}
    <div class="card-modern">
        <div class="card-modern-header">
            <h3>
                <i class="fas fa-receipt"></i>
                Tous les Versements Effectués
            </h3>
            <span class="badge bg-light text-primary fw-bold px-3 py-2 rounded-pill">
                {{ $reversements->total() }} résultat(s)
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th class="text-center">Réf. Versement</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">Montant</th>
                        <th class="text-center">Date d'Enregistrement</th>
                        <th class="text-center">Mois Concernés</th>
                        <th class="text-center">Enregistré Par</th>
                        <th class="text-center">Commentaire</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reversements as $rev)
                        <tr>
                            {{-- 1. Référence (Centrée) --}}
                            <td class="text-center align-middle">
                                <span class="badge bg-light text-primary border fw-bold px-3 py-2 rounded-pill font-monospace" style="font-size: 0.88rem;">
                                    {{ $rev->reference }}
                                </span>
                            </td>

                            {{-- 2. Type (Centré) --}}
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center">
                                    @if($rev->is_premier)
                                        <span class="badge-premier">
                                            <i class="fas fa-star"></i> 1er Reversement
                                        </span>
                                    @else
                                        <span class="badge-standard">
                                            Standard
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- 3. Montant (Centré) --}}
                            <td class="text-center align-middle">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <strong class="text-dark fs-6">
                                        {{ number_format($rev->montant, 0, ',', ' ') }}
                                    </strong>
                                    <small class="text-muted fw-bold" style="font-size: 0.72rem;">FCFA</small>
                                </div>
                            </td>

                            {{-- 4. Date d'Enregistrement (Centrée) --}}
                            <td class="text-center align-middle">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <span class="fw-semibold text-dark">
                                        {{ $rev->date_reversement ? $rev->date_reversement->format('d/m/Y') : $rev->created_at->format('d/m/Y') }}
                                    </span>
                                    <small class="text-muted">
                                        {{ $rev->date_reversement ? $rev->date_reversement->format('H:i') : $rev->created_at->format('H:i') }}
                                    </small>
                                </div>
                            </td>

                            {{-- 5. Mois Concernés (Centrés) --}}
                            <td class="text-center align-middle">
                                <div class="d-flex flex-wrap justify-content-center gap-1" style="max-width: 220px; margin: 0 auto;">
                                    @if(!empty($rev->mois_concernes) && is_array($rev->mois_concernes))
                                        @foreach($rev->mois_concernes as $m)
                                            <span class="badge-month">{{ $m }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">Non renseigné</span>
                                    @endif
                                </div>
                                @if($rev->annee)
                                    <small class="text-muted d-block fw-bold mt-1 text-center">({{ $rev->annee }})</small>
                                @endif
                            </td>

                            {{-- 6. Enregistré Par (Nom de la personne en évidence, centré) --}}
                            <td class="text-center align-middle">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    {{-- Nom complet de la personne physique --}}
                                    <strong class="text-dark" style="font-size: 0.95rem;">
                                        {{ $rev->nom_auteur }}
                                    </strong>
                                    <div class="d-flex align-items-center justify-content-center gap-1 mt-1">
                                        <span class="badge {{ $rev->auteur_role === 'Financier' ? 'bg-primary' : 'bg-info text-dark' }}" style="font-size: 0.72rem;">
                                            <i class="fas {{ $rev->auteur_role === 'Financier' ? 'fa-wallet' : 'fa-calculator' }} me-1"></i>
                                            {{ $rev->auteur_role ?? 'Financier' }}
                                        </span>
                                    </div>
                                    <small class="text-muted mt-1" style="font-size: 0.78rem;">
                                        {{ $rev->enregistre_par_email }}
                                    </small>
                                </div>
                            </td>

                            {{-- 7. Commentaire (Centré) --}}
                            <td class="text-center align-middle">
                                <div class="text-center" style="max-width: 180px; margin: 0 auto;">
                                    @if($rev->commentaire)
                                        <span class="text-dark small d-inline-block text-truncate" style="max-width: 170px;" title="{{ $rev->commentaire }}">
                                            {{ $rev->commentaire }}
                                        </span>
                                    @else
                                        <span class="text-muted small"><em>-</em></span>
                                    @endif
                                </div>
                            </td>

                            {{-- 8. Action (Centrée) --}}
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-detail" onclick="showRecuModal({{ $rev->id }})" title="Voir les détails et le reçu">
                                        <i class="fas fa-file-invoice-dollar text-primary"></i>
                                        <span>Reçu</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 text-secondary" style="opacity: 0.4;"></i>
                                    <p class="mb-1 fw-bold fs-6">Aucun reversement trouvé.</p>
                                    <small>Aucun versement ne correspond aux critères sélectionnés.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reversements->hasPages())
            <div class="p-3 border-top d-flex justify-content-center">
                {{ $reversements->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Pop-up Modal de Détail & Reçu de Reversement --}}
<div class="modal fade" id="recuModal" tabindex="-1" aria-labelledby="recuModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            {{-- En-tête du reçu --}}
            <div class="recu-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-receipt fa-2x text-white"></i>
                    </div>
                    <div>
                        <h4 class="modal-title fw-bold mb-0 text-white" id="recuModalLabel">Bordereau & Détails du Versement</h4>
                        <small class="text-white-50">Mairie du {{ $authData['commune'] }} &bull; Régie des taxes communales</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="closeRecuModal()" aria-label="Close"></button>
            </div>

            {{-- Corps du reçu --}}
            <div class="modal-body p-4">
                {{-- Carte Montant & Référence --}}
                <div class="text-center p-3 mb-4 rounded-4" style="background: linear-gradient(135deg, #f8fafc, #edf2f7); border: 1.5px dashed #cbd5e1;">
                    <div id="modalTypeBadgeContainer" class="mb-2">
                        <span id="modalTypeBadge" class="badge-standard"></span>
                    </div>
                    <div class="text-muted small text-uppercase fw-bold letter-spacing-1">Montant Total Reversé</div>
                    <h1 class="display-6 fw-extrabold text-primary my-1" id="modalMontant" style="font-weight: 800;">
                        0 FCFA
                    </h1>
                    <div class="d-flex align-items-center justify-content-center gap-2 mt-2">
                        <span class="text-muted small">Numéro de Référence :</span>
                        <span id="modalReference" class="badge bg-primary px-3 py-1 font-monospace" style="font-size: 0.95rem;">-</span>
                    </div>
                </div>

                {{-- Grille des détails --}}
                <div class="row g-3">
                    {{-- Date & Heure --}}
                    <div class="col-md-6">
                        <div class="info-value-box">
                            <div class="info-label"><i class="fas fa-calendar-check text-primary me-1"></i> Date d'enregistrement</div>
                            <strong id="modalDate" class="text-dark fs-6">-</strong>
                        </div>
                    </div>

                    {{-- Année d'exercice --}}
                    <div class="col-md-6">
                        <div class="info-value-box">
                            <div class="info-label"><i class="fas fa-calendar-alt text-primary me-1"></i> Année budgétaire</div>
                            <strong id="modalAnnee" class="text-dark fs-6">-</strong>
                        </div>
                    </div>

                    {{-- Mois couverts --}}
                    <div class="col-12">
                        <div class="info-value-box">
                            <div class="info-label"><i class="fas fa-calendar-week text-primary me-1"></i> Mois concernés par le versement</div>
                            <div id="modalMoisContainer" class="d-flex flex-wrap gap-1 mt-1"></div>
                        </div>
                    </div>

                    {{-- Enregistré par (Nom de la personne physique certifiée) --}}
                    <div class="col-12">
                        <div class="info-value-box" style="border-left: 4px solid var(--primary); background: #f0f4ff;">
                            <div class="info-label text-primary">
                                <i class="fas fa-user-check text-primary me-1"></i> Enregistré par (Agent Responsable)
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-1">
                                <div>
                                    <h5 class="fw-bold text-dark mb-0" id="modalEnregistrePar" style="font-size: 1.1rem;">
                                        -
                                    </h5>
                                    <small id="modalEnregistreParEmail" class="text-muted d-block mt-1"></small>
                                </div>
                                <span id="modalAuteurRole" class="badge bg-primary px-3 py-2 rounded-pill fw-bold" style="font-size: 0.82rem;"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Commentaire / Justificatif --}}
                    <div class="col-12">
                        <div class="info-value-box">
                            <div class="info-label"><i class="fas fa-comment-dots text-primary me-1"></i> Commentaire / Justificatif</div>
                            <p id="modalCommentaire" class="text-dark mb-0 mt-1 small fst-italic">-</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pied du modal --}}
            <div class="modal-footer bg-light px-4 py-3 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" onclick="closeRecuModal()">
                    <i class="fas fa-times me-1"></i> Fermer
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer le Reçu
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Scripts d'ouverture et gestion du Pop-up modal --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès !',
            text: "{{ session('success') }}",
            confirmButtonColor: '#1f4083',
            confirmButtonText: 'OK',
            background: 'white'
        });
        @endif
    });

    function showRecuModal(id) {
        const baseUrl = "{{ Auth::guard('finance')->check() ? url('finance/reversements') : url('accounting/reversements') }}";
        
        fetch(`${baseUrl}/${id}/recu`)
            .then(res => {
                if (!res.ok) {
                    throw new Error('Erreur réseau');
                }
                return res.json();
            })
            .then(res => {
                if (res.status === 'success') {
                    const d = res.data;

                    document.getElementById('modalReference').textContent = d.reference;
                    document.getElementById('modalMontant').textContent = d.montant;
                    document.getElementById('modalDate').textContent = d.date;
                    document.getElementById('modalAnnee').textContent = d.annee || '-';
                    document.getElementById('modalEnregistrePar').textContent = d.enregistre_par || 'Responsable';
                    document.getElementById('modalEnregistreParEmail').textContent = d.enregistre_par_email || '';
                    document.getElementById('modalAuteurRole').textContent = d.auteur_role || 'Financier';
                    document.getElementById('modalCommentaire').textContent = d.commentaire || 'Aucun commentaire.';

                    // Type Badge
                    const badge = document.getElementById('modalTypeBadge');
                    badge.replaceChildren();
                    if (d.is_premier) {
                        badge.className = 'badge-premier';
                        const star = document.createElement('i');
                        star.className = 'fas fa-star me-1';
                        badge.appendChild(star);
                        badge.appendChild(document.createTextNode(' Premier Reversement'));
                    } else {
                        badge.className = 'badge-standard';
                        badge.textContent = 'Reversement Standard';
                    }

                    // Mois concernés
                    const moisCont = document.getElementById('modalMoisContainer');
                    moisCont.replaceChildren();
                    if (d.mois_concernes && Array.isArray(d.mois_concernes) && d.mois_concernes.length > 0) {
                        d.mois_concernes.forEach(m => {
                            const span = document.createElement('span');
                            span.className = 'badge-month';
                            span.textContent = m;
                            moisCont.appendChild(span);
                        });
                    } else {
                        const emptySpan = document.createElement('span');
                        emptySpan.className = 'text-muted small';
                        emptySpan.textContent = 'Aucun mois spécifié';
                        moisCont.appendChild(emptySpan);
                    }

                    // Ouverture fiable du modal (Support Bootstrap 5 + Vanilla fallback)
                    const modalEl = document.getElementById('recuModal');
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        try {
                            let bsModal = bootstrap.Modal.getInstance(modalEl);
                            if (!bsModal) {
                                bsModal = new bootstrap.Modal(modalEl);
                            }
                            bsModal.show();
                            return;
                        } catch (e) {
                            console.warn('Bootstrap Modal init fallback:', e);
                        }
                    }

                    // Fallback Vanilla JS
                    modalEl.classList.add('show');
                    modalEl.style.display = 'block';
                    modalEl.removeAttribute('aria-hidden');
                    modalEl.setAttribute('aria-modal', 'true');
                    document.body.classList.add('modal-open');

                    let backdrop = document.getElementById('customBackdrop');
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.id = 'customBackdrop';
                        backdrop.className = 'modal-backdrop fade show';
                        document.body.appendChild(backdrop);
                    }
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de charger les détails du versement.',
                    confirmButtonColor: '#1f4083',
                    confirmButtonText: 'Fermer'
                });
            });
    }

    function closeRecuModal() {
        const modalEl = document.getElementById('recuModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                let bsModal = bootstrap.Modal.getInstance(modalEl);
                if (bsModal) {
                    bsModal.hide();
                }
            } catch (e) {}
        }
        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
        modalEl.setAttribute('aria-hidden', 'true');
        modalEl.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');

        const backdrop = document.getElementById('customBackdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
</script>
@endsection
