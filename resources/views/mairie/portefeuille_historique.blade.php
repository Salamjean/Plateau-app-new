@extends('mairie.layouts.template')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('dasboard/mairie.css') }}">
    <style>
        .portefeuille-page {
            background: #f8fafc;
            min-height: 100vh;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        :root {
                --primary: #1f4083;
                --accent-gold: #d4af37;
                --success: #2ec4b6;
                --danger: #e71d36;
                --glass-bg: rgba(255, 255, 255, 0.08);
                --glass-border: rgba(255, 255, 255, 0.15);
            }

            .animate-up {
                animation: fadeInUp 0.8s cubic-bezier(0.25, 0.8, 0.25, 1) both;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(25px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .table-container {
                background: white;
                border-radius: 15px;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            .badge-credit {
                background-color: rgba(46, 196, 182, 0.15) !important;
                color: var(--success) !important;
                font-weight: 600;
            }

            .back-btn {
                background: white;
                color: var(--primary);
                border: 2px solid var(--primary);
                font-weight: 700;
                padding: 0.6rem 1.5rem;
                border-radius: 50px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 10px rgba(31, 64, 131, 0.15);
            }

            .back-btn:hover {
                background: var(--primary);
                color: white !important;
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(31, 64, 131, 0.3);
            }
        </style>
        <div class="portefeuille-page">
            <div class="container-fluid py-4">
            <!-- Header avec bouton Retour -->
            <div class="d-flex justify-content-between align-items-center mb-4 animate-up" style="animation-delay: 0.1s;">
                <div>
                    <h2 class="fw-bold text-dark mb-1"><i class="fas fa-history text-primary me-2"></i>Historique Complet</h2>
                    <p class="text-muted mb-0">Visualisez toutes les demandes transférées de votre portefeuille vers le Trésor Public.</p>
                </div>
                <div>
                    <a href="{{ route('mairie.portefeuille.index') }}" class="btn back-btn">
                        <i class="fas fa-arrow-left me-2"></i>Retour au Portefeuille
                    </a>
                </div>
            </div>

            <!-- Historique unifié des Transactions -->
            <div class="table-container animate-up" style="animation-delay: 0.2s;">
                <div class="table-header p-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3" style="background: linear-gradient(120deg, var(--primary), #0d6efd); color: white;">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-receipt me-2"></i>Toutes les Demandes Transférées</h5>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <!-- Filtre par mois -->
                        <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center" id="filterForm">
                            <div class="input-group">
                                <span class="input-group-text bg-white text-primary border-0 rounded-start-pill ps-3 pe-2">
                                    <i class="fas fa-filter text-muted"></i>
                                </span>
                                <select name="month" class="form-select border-0 rounded-end-pill pe-4 ps-2 fw-semibold text-primary" style="height: 38px; outline: none; box-shadow: none; cursor: pointer; min-width: 160px; font-size: 0.9rem;" onchange="this.form.submit()">
                                    <option value="" class="text-dark">Tous les mois</option>
                                    @foreach($availableMonths as $month)
                                        @php
                                            $carbonDate = \Carbon\Carbon::createFromFormat('Y-m', $month);
                                            $monthLabel = ucwords($carbonDate->translatedFormat('F Y'));
                                        @endphp
                                        <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }} class="text-dark">
                                            {{ $monthLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        <!-- Export PDF par année -->
                        <form method="GET" action="{{ route('mairie.portefeuille.export_pdf') }}" class="d-flex align-items-center" target="_blank">
                            <div class="input-group">
                                <span class="input-group-text bg-white text-danger border-0 rounded-start-pill ps-3 pe-2">
                                    <i class="fas fa-file-pdf"></i>
                                </span>
                                <select name="year" class="form-select border-0 pe-4 ps-2 fw-semibold text-danger" style="height: 38px; outline: none; box-shadow: none; cursor: pointer; min-width: 100px; font-size: 0.9rem;">
                                    @forelse($availableYears as $year)
                                        <option value="{{ $year }}" class="text-dark">{{ $year }}</option>
                                    @empty
                                        <option value="{{ date('Y') }}" class="text-dark">{{ date('Y') }}</option>
                                    @endforelse
                                </select>
                                <button type="submit" class="btn btn-danger border-0 rounded-end-pill px-3 fw-bold" style="height: 38px; font-size: 0.85rem;">
                                    Exporter PDF
                                </button>
                            </div>
                        </form>

                        <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold" style="font-size: 0.85rem; height: 38px; display: inline-flex; align-items: center;">
                            {{ $transactions->total() }} Transactions au total
                        </span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 text-secondary text-center">DATE & HEURE</th>
                                <th class="py-3 text-secondary text-center">RÉFÉRENCE</th>
                                <th class="py-3 text-secondary text-center">DESTINATAIRE (TRÉSORPAY)</th>
                                <th class="py-3 text-secondary text-center">MONTANT</th>
                                <th class="py-3 text-secondary text-center">STATUT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $t)
                                <tr>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="p-2 bg-light rounded-3 me-2">
                                                <i class="fas fa-calendar-days text-muted"></i>
                                            </div>
                                            <div class="text-center">
                                                <span class="fw-bold text-dark d-block">{{ $t->date->format('d M Y') }}</span>
                                                <small class="text-muted">{{ $t->date->format('H:i') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <code class="text-secondary fw-semibold">{{ $t->reference }}</code>
                                    </td>
                                    <td class="text-center">
                                        <span class="small fw-bold">{{ $t->destinataire }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-success">
                                            {{ number_format($t->montant, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill px-3 py-2 badge-credit">
                                            <i class="fas fa-circle-check me-1"></i> {{ $t->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-5 text-center">
                                        <div class="py-4 text-center">
                                            <i class="fas fa-receipt fa-3x text-light mb-3"></i>
                                            <h5 class="text-muted">Aucun transfert effectué</h5>
                                            <p class="text-muted small mb-0">Les transferts vers TrésorPay s'afficheront ici en temps réel au fil des paiements des citoyens.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
@endsection
