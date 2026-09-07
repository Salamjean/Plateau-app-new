@extends('admin.layouts.template')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --admin-primary: #6777ef;
        --admin-bg: #f4f6f9;
        --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
    }

    .admin-wallet {
        background-color: var(--admin-bg);
        font-family: 'Nunito', sans-serif;
    }

    .page-header {
        background: var(--admin-primary);
        border-radius: 12px;
        color: white;
        padding: 25px 30px;
        box-shadow: 0 10px 20px rgba(43, 52, 82, 0.15);
        margin-bottom: 30px;
    }

    .modern-table-container {
        background: white;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        overflow: hidden;
    }

    .modern-table-header {
        background: #fff;
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f0;
    }

    .modern-table th {
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #888;
        padding: 15px 20px;
        border-bottom: 2px solid #f4f6f9;
    }

    .modern-table td {
        padding: 15px 20px;
        vertical-align: middle;
        font-weight: 600;
        color: #444;
        border-bottom: 1px solid #f8f9fa;
    }
    
    .badge-soft-success { background-color: rgba(71, 195, 99, 0.15); color: #47c363; }
    .badge-soft-primary { background-color: rgba(103, 119, 239, 0.15); color: var(--admin-primary); }
</style>

<div class="container-fluid py-4 admin-wallet">
    <!-- Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1"><i class="fas fa-list-ul me-2 text-white-50"></i> Historique Complet des Transactions</h2>
            <p class="mb-0 text-white-50">Liste paginée de toutes les transactions de la plateforme.</p>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.transactions') }}" class="btn btn-light rounded-pill px-3 fw-bold me-3 shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour au Portefeuille
            </a>
            <form method="GET" action="{{ route('admin.transactions.all') }}" class="d-flex align-items-center bg-white rounded p-1 shadow-sm">
                <i class="fas fa-calendar-alt text-muted ms-2 me-2"></i>
                <input type="month" name="month" value="{{ $selectedMonth }}" class="form-control border-0 bg-transparent text-dark fw-bold" style="box-shadow: none; width: 140px;" onchange="this.form.submit()">
            </form>
        </div>
    </div>

    <!-- Historique des Transactions -->
    <div class="modern-table-container">
        <div class="modern-table-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-history me-2 text-primary"></i>Toutes les transactions {{ $selectedMonth ? "pour " . \Carbon\Carbon::parse($selectedMonth . '-01')->locale('fr')->translatedFormat('F Y') : '' }}</h5>
            <span class="badge bg-light text-dark border px-3 py-2">{{ $transactions->total() }} résultats</span>
        </div>
        <div class="table-responsive">
            <table class="table modern-table mb-0 text-center">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Source / Type</th>
                        <th>Méthode</th>
                        <th>Détails Revenus</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $t->date->format('d M Y') }}</div>
                                <div class="text-muted small">{{ $t->date->format('H:i') }}</div>
                            </td>
                            <td class="text-start">
                                <div class="fw-bold text-dark">{{ $t->commune }}</div>
                                <div class="badge badge-soft-primary small mt-1">{{ $t->type }}</div>
                            </td>
                            <td>
                                @if(strtolower($t->payment_method) == 'wave')
                                    <span class="badge bg-info text-white"><i class="fas fa-water me-1"></i>Wave</span>
                                @elseif(strtolower($t->payment_method) == 'tresorpay')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-gem me-1"></i>TrésorPay</span>
                                @else
                                    <span class="badge bg-secondary">{{ $t->payment_method }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="fw-bold text-dark">{{ number_format($t->montant_total, 0, ',', ' ') }} F</div>
                                <div class="small text-muted" style="font-size:0.7rem;">
                                    T: {{ number_format($t->part_timbre, 0, ',', ' ') }} | L: {{ number_format($t->part_livraison, 0, ',', ' ') }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-soft-success px-2 py-1"><i class="fas fa-check-circle me-1"></i>{{ $t->status }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5">
                                <div class="text-center text-muted">
                                    <i class="fas fa-search fa-3x mb-3 opacity-50"></i>
                                    <p>Aucune transaction trouvée.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if ($transactions->hasPages())
        <div class="p-4 border-top d-flex justify-content-center">
            {{ $transactions->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>
</div>
@endsection
