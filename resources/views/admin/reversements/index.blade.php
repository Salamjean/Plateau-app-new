@extends('admin.layouts.template')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    :root {
        --admin-primary: #6777ef;
        --admin-primary-dark: #394eea;
        --admin-success: #47c363;
        --admin-warning: #ffa426;
        --admin-danger: #fc544b;
        --admin-bg: #f4f6f9;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .admin-container {
        padding: 1.5rem 1.75rem;
        background-color: var(--admin-bg);
        min-height: calc(100vh - 70px);
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* Page Header */
    .admin-header-card {
        background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
        border-radius: 14px;
        color: white;
        padding: 1.75rem 2rem;
        box-shadow: 0 8px 24px rgba(103, 119, 239, 0.25);
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
    }

    .admin-header-card::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 140px;
        height: 140px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
    }

    /* Stat Cards */
    .stat-card-modern {
        background: white;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(0,0,0,0.03);
        height: 100%;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stat-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }

    .stat-icon-wrapper {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    /* Filter Card */
    .filter-box-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(0,0,0,0.03);
        margin-bottom: 1.5rem;
    }

    /* Table Card */
    .card-table-wrapper {
        background: white;
        border-radius: 14px;
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(0,0,0,0.03);
        overflow: hidden;
    }

    .card-table-header {
        background: white;
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: gap;
    }

    /* CENTRAGE ABSOLU DE TOUS LES ÉLÉMENTS DU TABLEAU */
    .table-admin-custom {
        margin: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-admin-custom th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
        border-bottom: 2px solid #e2e8f0;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .table-admin-custom td {
        padding: 1.1rem 0.75rem;
        vertical-align: middle !important;
        text-align: center !important;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.92rem;
    }

    .table-admin-custom tbody tr:hover {
        background-color: #f8fafc;
    }

    .badge-commune {
        background: #eef2ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
    }

    .badge-premier {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        font-weight: 700;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.78rem;
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

    .btn-recu-admin {
        background: white;
        color: var(--admin-primary);
        border: 1.5px solid var(--admin-primary);
        border-radius: 50px;
        padding: 0.35rem 1rem;
        font-weight: 700;
        font-size: 0.82rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        box-shadow: 0 2px 5px rgba(103, 119, 239, 0.1);
    }

    .btn-recu-admin:hover {
        background: var(--admin-primary);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(103, 119, 239, 0.25);
    }

    /* Modal Styling */
    .recu-modal-header {
        background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-dark));
        color: white;
        padding: 1.5rem 1.75rem;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    .info-label {
        font-size: 0.75rem;
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

    @media print {
        body * {
            visibility: hidden;
        }
        #adminRecuModal, #adminRecuModal * {
            visibility: visible;
        }
        #adminRecuModal {
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

<div class="admin-container">

    {{-- En-tête Administration --}}
    <div class="admin-header-card d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-white text-primary fw-bold text-uppercase px-2 py-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    Supervision &bull; Audit
                </span>
                <span class="text-white-50 small">&bull; Consultation seule</span>
            </div>
            <h2 class="fw-bold mb-1" style="font-size: 1.6rem;">
                <i class="fas fa-hand-holding-usd me-2"></i>
                Historique des Reversements de Régie
            </h2>
            <p class="mb-0 text-white-50" style="font-size: 0.95rem;">
                Suivi centralisé de tous les versements financiers opérés par les mairies et régies communales.
            </p>
        </div>
        <div>
            <span class="badge bg-white bg-opacity-25 text-white px-3 py-2 rounded-pill fw-bold">
                <i class="fas fa-shield-alt me-1"></i> Espace Administrateur
            </span>
        </div>
    </div>

    {{-- Cartes Statistiques --}}
    <div class="row g-3 mb-4">
        {{-- Total Reversé --}}
        <div class="col-xl-4 col-md-6">
            <div class="stat-card-modern">
                <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                    <i class="fas fa-coins"></i>
                </div>
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Total Général Reversé</span>
                    <h3 class="fw-bold text-dark mb-0">{{ number_format($totalMontant, 0, ',', ' ') }} <small class="fs-6 text-muted">FCFA</small></h3>
                    <small class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> Fonds certifiés</small>
                </div>
            </div>
        </div>

        {{-- Nombre de versements --}}
        <div class="col-xl-4 col-md-6">
            <div class="stat-card-modern">
                <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Opérations de Versement</span>
                    <h3 class="fw-bold text-dark mb-0">{{ $totalCount }} <small class="fs-6 text-muted">versement(s)</small></h3>
                    <small class="text-primary fw-bold"><i class="fas fa-file-invoice me-1"></i> Bordereaux enregistrés</small>
                </div>
            </div>
        </div>

        {{-- Dernier Versement --}}
        <div class="col-xl-4 col-md-12">
            <div class="stat-card-modern">
                <div class="stat-icon-wrapper bg-warning bg-opacity-10 text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <span class="text-muted small fw-bold text-uppercase d-block mb-1">Dernier Versement</span>
                    @if($dernierReversement)
                        <h4 class="fw-bold text-dark mb-0" style="font-size: 1.1rem;">
                            {{ number_format($dernierReversement->montant, 0, ',', ' ') }} FCFA
                        </h4>
                        <small class="text-muted">
                            Le {{ $dernierReversement->date_reversement ? $dernierReversement->date_reversement->format('d/m/Y') : $dernierReversement->created_at->format('d/m/Y') }} 
                            ({{ $dernierReversement->commune ?? 'Plateau' }})
                        </small>
                    @else
                        <h5 class="fw-bold text-muted mb-0">Aucun versement</h5>
                        <small class="text-muted">En attente de premier versement</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres & Recherche --}}
    <div class="filter-box-card">
        <form method="GET" action="{{ route('admin.reversements.index') }}" class="row g-3 align-items-end">
            {{-- Filtre Commune --}}
            <div class="col-lg-3 col-md-6">
                <label class="form-label text-muted small fw-bold mb-1" for="commune">Commune / Régie</label>
                <select name="commune" id="commune" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Toutes les communes</option>
                    @foreach($availableCommunes as $com)
                        <option value="{{ $com }}" {{ $selectedCommune == $com ? 'selected' : '' }}>
                            Mairie de {{ ucfirst($com) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre Année --}}
            <div class="col-lg-2 col-md-6">
                <label class="form-label text-muted small fw-bold mb-1" for="year">Année</label>
                <select name="year" id="year" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Toutes les années</option>
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre Mois --}}
            <div class="col-lg-2 col-md-6">
                <label class="form-label text-muted small fw-bold mb-1" for="month">Mois</label>
                <select name="month" id="month" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Tous les mois</option>
                    @foreach($frenchMonths as $m)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ $m }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Recherche par mot-clé --}}
            <div class="col-lg-3 col-md-6">
                <label class="form-label text-muted small fw-bold mb-1" for="search">Recherche rapide</label>
                <div class="input-group input-group-sm">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Réf, agent, email..." value="{{ $searchTerm }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            {{-- Réinitialisation --}}
            <div class="col-lg-2 col-md-12">
                @if($selectedCommune || $selectedYear || $selectedMonth || $searchTerm)
                    <a href="{{ route('admin.reversements.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i> Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tableau des Reversements (TOUS LES ÉLÉMENTS CENTRÉS) --}}
    <div class="card-table-wrapper">
        <div class="card-table-header">
            <div>
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fas fa-list text-primary me-2"></i>
                    Bordereaux de Reversements Enregistrés
                </h5>
                <small class="text-muted">Historique complet des reversements effectués</small>
            </div>
            <span class="badge bg-light text-primary fw-bold px-3 py-2 rounded-pill">
                {{ $reversements->total() }} résultat(s)
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-admin-custom">
                <thead>
                    <tr>
                        <th class="text-center">Réf. Versement</th>
                        <th class="text-center">Commune</th>
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
                                <span class="badge bg-light text-primary border fw-bold px-3 py-2 rounded-pill font-monospace" style="font-size: 0.85rem;">
                                    {{ $rev->reference }}
                                </span>
                            </td>

                            {{-- 2. Commune (Centrée) --}}
                            <td class="text-center align-middle">
                                <span class="badge-commune">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ ucfirst($rev->commune ?? 'Plateau') }}
                                </span>
                            </td>

                            {{-- 3. Type (Centré) --}}
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

                            {{-- 4. Montant (Centré) --}}
                            <td class="text-center align-middle">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <strong class="text-dark fs-6">
                                        {{ number_format($rev->montant, 0, ',', ' ') }}
                                    </strong>
                                    <small class="text-muted fw-bold" style="font-size: 0.72rem;">FCFA</small>
                                </div>
                            </td>

                            {{-- 5. Date d'Enregistrement (Centrée) --}}
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

                            {{-- 6. Mois Concernés (Centrés) --}}
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

                            {{-- 7. Enregistré Par (Nom de la personne, centré) --}}
                            <td class="text-center align-middle">
                                <div class="d-flex flex-column align-items-center justify-content-center">
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

                            {{-- 8. Commentaire (Centré) --}}
                            <td class="text-center align-middle">
                                <div class="text-center" style="max-width: 170px; margin: 0 auto;">
                                    @if($rev->commentaire)
                                        <span class="text-dark small d-inline-block text-truncate" style="max-width: 160px;" title="{{ $rev->commentaire }}">
                                            {{ $rev->commentaire }}
                                        </span>
                                    @else
                                        <span class="text-muted small"><em>-</em></span>
                                    @endif
                                </div>
                            </td>

                            {{-- 9. Action (Centrée) --}}
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-recu-admin" onclick="showAdminRecuModal({{ $rev->id }})" title="Voir le reçu détaillé">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                        <span>Reçu</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 text-secondary" style="opacity: 0.3;"></i>
                                    <p class="mb-1 fw-bold fs-6">Aucun reversement trouvé.</p>
                                    <small>Aucun enregistrement ne correspond aux critères de recherche.</small>
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

{{-- Pop-up Modal de Détail & Reçu pour l'Admin --}}
<div class="modal fade" id="adminRecuModal" tabindex="-1" aria-labelledby="adminRecuModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            {{-- En-tête du reçu --}}
            <div class="recu-modal-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-receipt fa-2x text-white"></i>
                    </div>
                    <div>
                        <h4 class="modal-title fw-bold mb-0 text-white" id="adminRecuModalLabel">Bordereau & Détails du Versement</h4>
                        <small class="text-white-50">Supervision Administration &bull; Consultation officielle</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="closeAdminRecuModal()" aria-label="Close"></button>
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
                    {{-- Commune --}}
                    <div class="col-md-4">
                        <div class="info-value-box">
                            <div class="info-label"><i class="fas fa-building text-primary me-1"></i> Commune / Régie</div>
                            <strong id="modalCommune" class="text-dark fs-6">-</strong>
                        </div>
                    </div>

                    {{-- Date & Heure --}}
                    <div class="col-md-4">
                        <div class="info-value-box">
                            <div class="info-label"><i class="fas fa-calendar-check text-primary me-1"></i> Date d'enregistrement</div>
                            <strong id="modalDate" class="text-dark fs-6">-</strong>
                        </div>
                    </div>

                    {{-- Année d'exercice --}}
                    <div class="col-md-4">
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

                    {{-- Enregistré par (Nom de la personne certifiée) --}}
                    <div class="col-12">
                        <div class="info-value-box" style="border-left: 4px solid var(--admin-primary); background: #f0f4ff;">
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
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" onclick="closeAdminRecuModal()">
                    <i class="fas fa-times me-1"></i> Fermer
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer le Reçu
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function showAdminRecuModal(id) {
        const url = "{{ url('admin/reversements') }}/" + id + "/recu";
        
        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('Erreur réseau');
                return res.json();
            })
            .then(res => {
                if (res.status === 'success') {
                    const d = res.data;

                    document.getElementById('modalReference').textContent = d.reference;
                    document.getElementById('modalMontant').textContent = d.montant;
                    document.getElementById('modalCommune').textContent = 'Mairie de ' + (d.commune ? d.commune.toUpperCase() : 'PLATEAU');
                    document.getElementById('modalDate').textContent = d.date;
                    document.getElementById('modalAnnee').textContent = d.annee || '-';
                    document.getElementById('modalEnregistrePar').textContent = d.enregistre_par || 'Agent';
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

                    // Ouverture modal (Bootstrap 5 + fallback)
                    const modalEl = document.getElementById('adminRecuModal');
                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        try {
                            let bsModal = bootstrap.Modal.getInstance(modalEl);
                            if (!bsModal) {
                                bsModal = new bootstrap.Modal(modalEl);
                            }
                            bsModal.show();
                            return;
                        } catch (e) {
                            console.warn('Bootstrap modal init fallback', e);
                        }
                    }

                    // Vanilla JS fallback
                    modalEl.classList.add('show');
                    modalEl.style.display = 'block';
                    modalEl.removeAttribute('aria-hidden');
                    modalEl.setAttribute('aria-modal', 'true');
                    document.body.classList.add('modal-open');

                    let backdrop = document.getElementById('adminBackdrop');
                    if (!backdrop) {
                        backdrop = document.createElement('div');
                        backdrop.id = 'adminBackdrop';
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
                    text: 'Impossible de charger les détails du reversement.',
                    confirmButtonColor: '#6777ef',
                    confirmButtonText: 'Fermer'
                });
            });
    }

    function closeAdminRecuModal() {
        const modalEl = document.getElementById('adminRecuModal');
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

        const backdrop = document.getElementById('adminBackdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
</script>
@endsection
