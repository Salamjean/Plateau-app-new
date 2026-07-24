@extends('admin.layouts.template')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --admin-primary: #6777ef;
            --admin-accent: #6777ef;
            --admin-success: #47c363;
            --admin-warning: #ffa426;
            --admin-danger: #fc544b;
            --admin-bg: #f4f6f9;
            --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
            --wave-color: #00c3ff;
            --tresor-color: #d4af37;
            --stripe-color: #635bff;
        }

        .admin-wallet {
            background-color: var(--admin-bg);
            font-family: 'Nunito', sans-serif;
        }

        /* En-tête de la page */
        .page-header {
            background: var(--admin-primary);
            border-radius: 12px;
            color: white;
            padding: 25px 30px;
            box-shadow: 0 10px 20px rgba(43, 52, 82, 0.15);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        /* Cartes de Statistiques Principales */
        .stat-box {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0, 0, 0, 0.02);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        }

        .stat-box.wave-box {
            border-bottom: 4px solid var(--wave-color);
        }

        .stat-box.tresor-box {
            border-bottom: 4px solid var(--tresor-color);
        }

        .stat-box.stripe-box {
            border-bottom: 4px solid var(--stripe-color);
        }

        .stat-box.total-box {
            border-bottom: 4px solid var(--admin-accent);
            background: linear-gradient(to bottom right, #ffffff, #f8f9fe);
        }

        .stat-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--admin-primary);
            line-height: 1.2;
        }

        .stat-currency {
            font-size: 1rem;
            color: #888;
            font-weight: 600;
        }

        .sub-stats {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px dashed #eee;
        }

        .sub-stat-item {
            text-align: center;
        }

        .sub-stat-label {
            font-size: 0.75rem;
            color: #999;
            text-transform: uppercase;
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
        }

        .sub-stat-val {
            font-size: 1rem;
            font-weight: 700;
            color: #333;
        }

        /* Cartes Actes */
        .acte-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: var(--card-shadow);
            border: 1px solid #f0f0f0;
        }

        .acte-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 15px;
        }

        .icon-naissance {
            background: rgba(103, 119, 239, 0.1);
            color: var(--admin-accent);
        }

        .icon-mariage {
            background: rgba(255, 164, 38, 0.1);
            color: var(--admin-warning);
        }

        .icon-deces {
            background: rgba(252, 84, 75, 0.1);
            color: var(--admin-danger);
        }

        /* Tableau */
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
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .badge-soft-success {
            background-color: rgba(71, 195, 99, 0.15);
            color: var(--admin-success);
        }

        .badge-soft-primary {
            background-color: rgba(103, 119, 239, 0.15);
            color: var(--admin-accent);
        }

        .badge-soft-warning {
            background-color: rgba(255, 164, 38, 0.15);
            color: #d68100;
        }

        /* Liste Comptabilité */
        .compta-list {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .compta-header {
            background: var(--admin-primary);
            color: white;
            padding: 20px;
            font-weight: 700;
        }

        .compta-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }

        .compta-item:hover {
            background: #fcfcfc;
        }

        .compta-item:last-child {
            border-bottom: none;
        }
    </style>

    <div class="container-fluid py-4 admin-wallet">
        <!-- Header -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1"><i class="fas fa-wallet me-2 text-white-50"></i> Portefeuille Global (Transactions)
                </h2>
                <p class="mb-0 text-white-50">Vue d'ensemble des revenus générés sur toute la plateforme (Timbres &
                    Livraisons).</p>
            </div>
            <div>
                <form method="GET" action="{{ route('admin.transactions') }}"
                    class="d-flex align-items-center bg-white rounded p-1 shadow-sm">
                    <i class="fas fa-calendar-alt text-muted ms-2 me-2"></i>
                    <input type="month" name="month" value="{{ $selectedMonth }}"
                        class="form-control border-0 bg-transparent text-dark fw-bold"
                        style="box-shadow: none; width: 140px;" onchange="this.form.submit()">
                </form>
            </div>
        </div>

        <!-- Main Stats Row -->
        <div class="row g-4 mb-4">
            <!-- Total Platform -->
            <div class="col-lg-3 col-md-6">
                <div class="stat-box total-box">
                    <div class="stat-title"><i class="fas fa-chart-pie me-2 text-primary"></i>Total Global (Ce Mois)</div>
                    <div class="stat-value">{{ number_format($stats['total'], 0, ',', ' ') }} <span
                            class="stat-currency">FCFA</span></div>
                    <div class="sub-stats">
                        <div class="sub-stat-item">
                            <span class="sub-stat-label">Timbres</span>
                            <span
                                class="sub-stat-val text-primary">{{ number_format($stats['timbre'], 0, ',', ' ') }}</span>
                        </div>
                        <div class="sub-stat-item">
                            <span class="sub-stat-label">Livraisons</span>
                            <span
                                class="sub-stat-val text-success">{{ number_format($stats['livraison'], 0, ',', ' ') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wave Stats -->
            <div class="col-lg-3 col-md-6">
                <div class="stat-box wave-box">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="stat-title"><i class="fas fa-water me-2" style="color: var(--wave-color);"></i>Via Wave
                        </div>
                        <img src="{{ asset('assets/assets/img/Wave.png') }}" alt="Wave"
                            style="height: 20px; border-radius: 4px;">
                    </div>
                    <div class="stat-value">{{ number_format($stats['wave_total'], 0, ',', ' ') }} <span
                            class="stat-currency">FCFA</span></div>
                    <div class="sub-stats">
                        <div class="sub-stat-item">
                            <span class="sub-stat-label">Timbres</span>
                            <span class="sub-stat-val">{{ number_format($stats['wave_timbre'], 0, ',', ' ') }}</span>
                        </div>
                        <div class="sub-stat-item">
                            <span class="sub-stat-label">Livraisons</span>
                            <span class="sub-stat-val">{{ number_format($stats['wave_livraison'], 0, ',', ' ') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TresorPay Stats -->
            <div class="col-lg-3 col-md-6">
                <div class="stat-box tresor-box">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="stat-title"><i class="fas fa-gem me-2" style="color: var(--tresor-color);"></i>Via
                            TrésorPay</div>
                        <img src="{{ asset('assets/assets/img/tresormoney.png') }}" alt="TresorPay"
                            style="height: 20px; border-radius: 4px;">
                    </div>
                    <div class="stat-value">{{ number_format($stats['tresor_total'], 0, ',', ' ') }} <span
                            class="stat-currency">FCFA</span></div>
                    <div class="sub-stats">
                        <div class="sub-stat-item">
                            <span class="sub-stat-label">Timbres</span>
                            <span class="sub-stat-val">{{ number_format($stats['tresor_timbre'], 0, ',', ' ') }}</span>
                        </div>
                        <div class="sub-stat-item">
                            <span class="sub-stat-label">Livraisons</span>
                            <span class="sub-stat-val">{{ number_format($stats['tresor_livraison'], 0, ',', ' ') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stripe Stats -->
            <div class="col-lg-3 col-md-6">
                <div class="stat-box stripe-box">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="stat-title"><i class="fab fa-stripe fs-5 me-2"
                                style="color: var(--stripe-color);"></i>Via Stripe</div>
                        <i class="fab fa-stripe fs-3" style="color: var(--stripe-color);"></i>
                    </div>
                    <div class="stat-value">{{ number_format($stats['stripe_total'], 0, ',', ' ') }} <span
                            class="stat-currency">FCFA</span></div>
                    <div class="sub-stats">
                        <div class="sub-stat-item">
                            <span class="sub-stat-label">Timbres</span>
                            <span class="sub-stat-val">{{ number_format($stats['stripe_timbre'], 0, ',', ' ') }}</span>
                        </div>
                        <div class="sub-stat-item">
                            <span class="sub-stat-label">Livraisons</span>
                            <span class="sub-stat-val">{{ number_format($stats['stripe_livraison'], 0, ',', ' ') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actes Breakdown Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="acte-card">
                    <div class="acte-icon icon-naissance"><i class="fas fa-baby"></i></div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-1">Revenus Naissances</div>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['naissance_total'], 0, ',', ' ') }} <small
                                class="text-muted" style="font-size:0.8rem;">FCFA</small></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="acte-card">
                    <div class="acte-icon icon-mariage"><i class="fas fa-ring"></i></div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-1">Revenus Mariages</div>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['mariage_total'], 0, ',', ' ') }} <small
                                class="text-muted" style="font-size:0.8rem;">FCFA</small></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="acte-card">
                    <div class="acte-icon icon-deces"><i class="fas fa-ribbon"></i></div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase mb-1">Revenus Décès</div>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['deces_total'], 0, ',', ' ') }} <small
                                class="text-muted" style="font-size:0.8rem;">FCFA</small></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Historique des Transactions -->
            <div class="col-lg-8">
                <div class="modern-table-container h-100">
                    <div class="modern-table-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-list-ul me-2 text-primary"></i>Dernières
                                Transactions</h5>
                            <span class="badge bg-light text-dark border px-3 py-2 ms-3">{{ $totalTransactionsCount }} au
                                total ce mois</span>
                        </div>
                        <a href="{{ route('admin.transactions.all') }}"
                            class="btn btn-sm btn-primary px-3 rounded-pill fw-bold shadow-sm">
                            Voir tout <i class="fas fa-arrow-right ms-1"></i>
                        </a>
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
                                            @if (strtolower($t->payment_method) == 'wave')
                                                <span class="badge bg-info text-white"><i
                                                        class="fas fa-water me-1"></i>Wave</span>
                                            @elseif(strtolower($t->payment_method) == 'tresorpay')
                                                <span class="badge bg-warning text-dark"><i
                                                        class="fas fa-gem me-1"></i>TrésorPay</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $t->payment_method }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="fw-bold text-dark">
                                                {{ number_format($t->montant_total, 0, ',', ' ') }} F</div>
                                            <div class="small text-muted" style="font-size:0.7rem;">
                                                T: {{ number_format($t->part_timbre, 0, ',', ' ') }} | L:
                                                {{ number_format($t->part_livraison, 0, ',', ' ') }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-success px-2 py-1"><i
                                                    class="fas fa-check-circle me-1"></i>{{ $t->status }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-5">
                                            <div class="text-center text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                                <p>Aucune transaction pour la période sélectionnée.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Comptabilité Mensuelle Annuelle -->
            <div class="col-lg-4">
                <div class="compta-list h-100">
                    <div class="compta-header">
                        <i class="fas fa-calendar-check me-2"></i> Comptabilité {{ $currentYear }}
                    </div>
                    <div style="max-height: 600px; overflow-y: auto;">
                        @foreach ($comptabiliteMensuelle as $num => $m)
                            <div class="compta-item">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light text-muted fw-bold rounded d-flex align-items-center justify-content-center me-3"
                                        style="width: 35px; height: 35px; font-size: 0.85rem;">
                                        {{ str_pad($num, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $m['nom'] }}
                                        </div>
                                        <div class="small text-muted" style="font-size: 0.7rem;">
                                            T: {{ number_format($m['timbre'], 0, ',', ' ') }} | L:
                                            {{ number_format($m['livraison'], 0, ',', ' ') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="fw-bold text-success">
                                    {{ number_format($m['total'], 0, ',', ' ') }} <small class="text-muted">F</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
