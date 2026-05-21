@extends('admin.layouts.template')

@section('content')
<div class="container-fluid py-4">
    <!-- Header de la page -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold text-dark mb-1">
                <i class="fas fa-history text-primary mr-2"></i>Historique des Transactions
            </h2>
            <p class="text-muted mb-0">Visualisez toutes les demandes payées et transférées automatiquement vers TrésorPay pour l'ensemble des communes.</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4" style="border-top: 3px solid #6777ef; border-radius: 8px;">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.transactions') }}" class="row align-items-end">
                <!-- Recherche par Demandeur -->
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="demandeur" class="font-weight-bold text-secondary small">NOM DU DEMANDEUR</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-user text-muted"></i></span>
                        </div>
                        <input type="text" name="demandeur" id="demandeur" class="form-control border-left-0 border-right-0" placeholder="Rechercher un demandeur..." value="{{ $searchDemandeur }}" onchange="this.form.submit()">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filtre par Mois -->
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="month" class="font-weight-bold text-secondary small">MOIS DE TRANSACTION</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                        </div>
                        <select name="month" id="month" class="form-control border-left-0" onchange="this.form.submit()">
                            <option value="">Tous les mois</option>
                            @foreach($availableMonths as $month)
                                @php
                                    $carbonDate = \Carbon\Carbon::createFromFormat('Y-m', $month);
                                    $monthLabel = ucwords($carbonDate->translatedFormat('F Y'));
                                @endphp
                                <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                                    {{ $monthLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Filtre par Type -->
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="type" class="font-weight-bold text-secondary small">TYPE D'ACTE</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-file-invoice text-muted"></i></span>
                        </div>
                        <select name="type" id="type" class="form-control border-left-0" onchange="this.form.submit()">
                            <option value="">Tous les types</option>
                            <option value="Naissance" {{ $selectedType == 'Naissance' ? 'selected' : '' }}>Naissance</option>
                            <option value="Mariage" {{ $selectedType == 'Mariage' ? 'selected' : '' }}>Mariage</option>
                            <option value="Décès" {{ $selectedType == 'Décès' ? 'selected' : '' }}>Décès</option>
                        </select>
                    </div>
                </div>

                <!-- Bouton réinitialiser -->
                <div class="col-md-2 text-md-right">
                    <a href="{{ route('admin.transactions') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-undo mr-1"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des Transactions -->
    <div class="card" style="border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" style="border-radius: 8px 8px 0 0;">
            <h5 class="mb-0 font-weight-bold"><i class="fas fa-receipt mr-2"></i>Flux de Transactions</h5>
            <span class="badge badge-light px-3 py-2 text-primary font-weight-bold">
                {{ $transactions->total() }} au total
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 text-secondary text-center">DATE & HEURE</th>
                        <th class="py-3 text-secondary text-center">COMMUNE / MAIRIE</th>
                        <th class="py-3 text-secondary text-center">RÉFÉRENCE</th>
                        <th class="py-3 text-secondary text-center">DEMANDEUR</th>
                        <th class="py-3 text-secondary text-center">TYPE D'ACTE</th>
                        <th class="py-3 text-secondary text-center">DESTINATAIRE</th>
                        <th class="py-3 text-secondary text-center">MONTANT</th>
                        <th class="py-3 text-secondary text-center">STATUT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="p-2 bg-light rounded mr-2">
                                        <i class="far fa-calendar-alt text-muted"></i>
                                    </div>
                                    <div class="text-center">
                                        <span class="font-weight-bold text-dark d-block">{{ $t->date->format('d M Y') }}</span>
                                        <small class="text-muted">{{ $t->date->format('H:i') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center font-weight-bold text-dark">
                                {{ $t->commune }}
                            </td>
                            <td class="text-center">
                                <code class="text-secondary font-weight-bold">{{ $t->reference }}</code>
                            </td>
                            <td class="text-center">
                                <span class="text-muted small font-weight-bold">{{ $t->demandeur }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge 
                                    @if($t->type == 'Naissance') badge-info
                                    @elseif($t->type == 'Mariage') badge-danger
                                    @else badge-secondary @endif px-3 py-2 font-weight-bold">
                                    {{ $t->type }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="small font-weight-bold text-dark">{{ $t->destinataire }}</span>
                            </td>
                            <td class="text-center">
                                <span class="font-weight-bold text-success">
                                    {{ number_format($t->montant, 0, ',', ' ') }} FCFA
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-success rounded-pill px-3 py-2 font-weight-bold">
                                    <i class="fas fa-check-circle mr-1"></i> {{ $t->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-5 text-center">
                                <div class="py-4 text-center">
                                    <i class="fas fa-receipt fa-3x text-light mb-3"></i>
                                    <h5 class="text-muted">Aucune transaction trouvée</h5>
                                    <p class="text-muted small mb-0">Les transactions payées via TrésorPay s'afficheront ici en temps réel.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Premium -->
    <div class="mt-4 d-flex justify-content-center">
        @if ($transactions->hasPages())
            <nav class="d-flex justify-content-center align-items-center">
                <ul class="pagination pagination-premium">
                    {{-- Bouton Page Précédente --}}
                    @if ($transactions->onFirstPage())
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $transactions->appends(request()->query())->previousPageUrl() }}" rel="prev"><i class="fas fa-chevron-left"></i></a>
                        </li>
                    @endif

                    {{-- Éléments de pages --}}
                    @foreach ($transactions->appends(request()->query())->getUrlRange(max(1, $transactions->currentPage() - 2), min($transactions->lastPage(), $transactions->currentPage() + 2)) as $page => $url)
                        @if ($page == $transactions->currentPage())
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Bouton Page Suivante --}}
                    @if ($transactions->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $transactions->appends(request()->query())->nextPageUrl() }}" rel="next"><i class="fas fa-chevron-right"></i></a>
                        </li>
                    @else
                        <li class="page-item disabled" aria-disabled="true">
                            <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    </div>
</div>

<style>
    /* Styles Premium pour la Pagination */
    .pagination-premium {
        display: flex;
        gap: 8px;
        padding: 10px 20px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 50px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        margin: 0;
    }

    .pagination-premium .page-item {
        display: inline-block;
    }

    .pagination-premium .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 50% !important;
        border: 1px solid #e3e6f0;
        background: white;
        color: #6777ef;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
    }

    .pagination-premium .page-item .page-link:hover {
        background: #6777ef;
        color: white !important;
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 15px rgba(103, 119, 239, 0.25);
        border-color: #6777ef;
    }

    .pagination-premium .page-item.active .page-link {
        background: #6777ef;
        color: white !important;
        border-color: #6777ef;
        box-shadow: 0 8px 20px rgba(103, 119, 239, 0.35);
        transform: scale(1.05);
    }

    .pagination-premium .page-item.disabled .page-link {
        background: rgba(241, 245, 249, 0.5);
        color: #94a3b8 !important;
        border-color: #e2e8f0;
        cursor: not-allowed;
        box-shadow: none;
    }
</style>
@endsection
