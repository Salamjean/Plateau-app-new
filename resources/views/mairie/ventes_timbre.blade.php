@extends('mairie.layouts.template')

@section('content')
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ventes de Timbres - Mairie</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('dasboard/mairie.css') }}">
        <style>
            :root {
                --primary: #1f4083;
                --secondary: #1f4083;
            }

            .dashboard-card {
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                background: white;
                padding: 1.5rem;
                height: 100%;
                border: none;
            }

            .card-primary {
                border-bottom: 4px solid var(--primary);
            }

            .card-success {
                border-bottom: 4px solid #28a745;
            }

            .card-warning {
                border-bottom: 4px solid #ffc107;
            }

            .card-danger {
                border-bottom: 4px solid #dc3545;
            }

            .stat-number {
                font-size: 1.8rem;
                font-weight: 700;
                color: #333;
            }

            .icon-circle {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
            }

            .table-container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                margin-top: 2rem;
            }

            .table-header {
                background: linear-gradient(120deg, var(--primary), #1a5ca3);
                color: white;
                padding: 1.2rem;
                border-radius: 15px 15px 0 0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .table th,
            .table td {
                text-align: center !important;
                vertical-align: middle !important;
            }

            .table td .d-flex {
                justify-content: center !important;
            }
        </style>
    </head>

    <body>
        <div class="container-fluid py-4">
            <!-- Statistiques -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="dashboard-card card-primary p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 text-uppercase small">Timbres Vendus Aujourd'hui</h6>
                                <h2 class="stat-number primary-text">{{ number_format($ventesAujourdhui) }}</h2>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary-light primary-text me-2">
                                        <i class="fas fa-calendar-check me-1"></i> Jour J
                                    </span>
                                </div>
                            </div>
                            <div class="icon-circle bg-primary-light">
                                <i class="fas fa-stamp primary-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="dashboard-card card-secondary p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 text-uppercase small">Timbres Restants (Stock)</h6>
                                <h2 class="stat-number secondary-text">{{ number_format($soldeTimbres) }}</h2>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-secondary-light secondary-text me-2">
                                        <i class="fas fa-layer-group me-1"></i> Stock actuel
                                    </span>
                                </div>
                            </div>
                            <div class="icon-circle bg-secondary-light">
                                <i class="fas fa-boxes-stacked secondary-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="dashboard-card p-4" style="border-bottom: 4px solid #ffc107;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 text-uppercase small">Ventes du Mois</h6>
                                <h2 class="stat-number" style="color: #ffc107;">{{ number_format($ventesMois) }}</h2>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light me-2" style="color: #ffc107;">
                                        <i class="fas fa-chart-line me-1"></i> Mensuel
                                    </span>
                                </div>
                            </div>
                            <div class="icon-circle bg-light">
                                <i class="fas fa-chart-pie" style="color: #ffc107;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="dashboard-card p-4" style="border-bottom: 4px solid #dc3545;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1 text-uppercase small">Ventes Totales (Cumul)</h6>
                                <h2 class="stat-number" style="color: #dc3545;">{{ number_format($totalTimbresVendus) }}
                                </h2>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light me-2" style="color: #dc3545;">
                                        <i class="fas fa-history me-1"></i> Global
                                    </span>
                                </div>
                            </div>
                            <div class="icon-circle bg-light">
                                <i class="fas fa-layer-group" style="color: #dc3545;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="filter-section mb-4 p-4"
                style="background: white; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                <form action="{{ route('mairie.timbres.index') }}" method="GET" class="row g-3 text-center">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Filtrer par Mois</label>
                        <select name="month" class="form-select border-0 bg-light text-center">
                            <option value="">Tous les mois</option>
                            @php
                                $months = [
                                    1 => 'Janvier',
                                    2 => 'Février',
                                    3 => 'Mars',
                                    4 => 'Avril',
                                    5 => 'Mai',
                                    6 => 'Juin',
                                    7 => 'Juillet',
                                    8 => 'Août',
                                    9 => 'Septembre',
                                    10 => 'Octobre',
                                    11 => 'Novembre',
                                    12 => 'Décembre',
                                ];
                            @endphp
                            @foreach ($months as $num => $name)
                                <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Filtrer par Comptable</label>
                        <select name="comptable_id" class="form-select border-0 bg-light text-center">
                            <option value="">Tous les comptables</option>
                            @foreach ($comptables as $comptable)
                                <option value="{{ $comptable->id }}"
                                    {{ $selectedComptable == $comptable->id ? 'selected' : '' }}>{{ $comptable->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                            <i class="fas fa-filter me-2"></i> Appliquer les filtres
                        </button>
                    </div>
                </form>
            </div>

            <!-- Historique -->
            <div class="table-container mt-4">
                <div class="table-header"
                    style="background: linear-gradient(120deg, #1f4083, #0d6efd); border-radius: 15px 15px 0 0; padding: 1.5rem;">
                    <h5 class="mb-0 text-white fw-bold"><i class="fas fa-history me-2"></i>Historique des Transactions</h5>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-2">
                        {{ number_format($totalTimbresVendus) }} Ventes au total
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-secondary py-3">DATE & HEURE</th>
                                <th class="text-secondary py-3">QUANTITÉ DE TIMBRES</th>
                                <th class="text-secondary py-3">SERVICE / AGENT</th>
                                <th class="text-secondary py-3">STATUT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ventes as $vente)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-light me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-calendar-alt text-muted small"></i>
                                            </div>
                                            <div class="d-flex flex-column text-start">
                                                <span
                                                    class="fw-bold text-dark">{{ $vente->created_at->format('d M Y') }}</span>
                                                <small class="text-muted">{{ $vente->created_at->format('H:i') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary-light primary-text px-3 py-2 rounded-3">
                                                <i class="fas fa-stamp me-1"></i> {{ abs($vente->nombre_timbre) }} Timbres
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($vente->finance)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary-light text-primary rounded-circle p-2 me-2"
                                                    style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-building small"></i>
                                                </div>
                                                <span class="small fw-medium">Régie: {{ $vente->finance->name }}</span>
                                            </div>
                                        @elseif($vente->comptable)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-secondary-light text-secondary rounded-circle p-2 me-2"
                                                    style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user-tie small"></i>
                                                </div>
                                                <span class="small fw-medium">Comptable:
                                                    {{ $vente->comptable->name }}</span>
                                            </div>
                                        @else
                                            <span class="badge bg-light text-muted fw-normal">Automatique</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-success-light text-success px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> Validé
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                                            <h5 class="text-muted">Aucune donnée trouvée</h5>
                                            <p class="text-muted small">Les transactions de vente apparaîtront ici.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $ventes->links() }}
            </div>
        </div>

        <style>
            .bg-primary-light {
                background-color: rgba(25, 119, 204, 0.1);
            }

            .bg-success-light {
                background-color: rgba(40, 167, 69, 0.1);
            }

            .bg-warning-light {
                background-color: rgba(255, 193, 7, 0.1);
            }

            .bg-danger-light {
                background-color: rgba(220, 53, 69, 0.1);
            }

            .table thead th {
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                padding: 1rem 0.75rem;
                border-bottom: 2px solid #eee;
            }

            .table tbody td {
                padding: 1rem 0.75rem;
            }
        </style>
    </body>

    </html>
@endsection
