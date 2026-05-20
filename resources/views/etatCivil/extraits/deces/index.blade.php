@extends('etatCivil.layouts.template')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
        :root {
            --primary-color: #1f4083;
            --border-radius: 12px;
            --transition: all 0.3s ease;
        }

        .dashboard-container {
            padding: 30px 1%;
            width: 100%;
            margin: 0 auto;
        }

        .page-title {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title h2 {
            font-size: 1.8rem;
            margin: 0;
            font-weight: 700;
        }

        .page-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .btn-action {
            background-color: var(--primary-color);
            border: none;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-action:hover {
            background-color: #1a3670;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(31, 64, 131, 0.2);
            color: white;
        }

        .filter-section {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 180px;
        }

        .filter-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 8px;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.3s;
        }

        .filter-group select:focus {
            border-color: var(--primary-color);
        }

        .dashboard-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid #f1f5f9;
            background-color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h5 {
            margin: 0;
            color: #1e293b;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        .table th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 15px 25px;
            border-top: none;
            border-bottom: 1px solid #edf2f7;
            text-align: center;
        }

        .table td {
            padding: 18px 25px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.9rem;
            text-align: center;
        }

        .id-badge {
            background-color: #f1f5f9;
            color: var(--primary-color);
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-en-attente {
            background: #fff7ed;
            color: #ea580c;
        }

        .status-réçu {
            background: #f0fdf4;
            color: #16a34a;
        }

        .status-terminé {
            background: #eff6ff;
            color: #2563eb;
        }

        .user-info-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .user-avatar-small {
            width: 32px;
            height: 32px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
        }

        .text-center {
            text-align: center !important;
        }

        .empty-state-cell {
            display: table-cell !important;
            width: 100% !important;
            text-align: center !important;
            padding: 50px 0 !important;
        }

        /* Responsive Table */
        @media (max-width: 991px) {
            .dashboard-container {
                padding: 15px;
            }

            .table thead {
                display: none;
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 15px;
                border-bottom: 1px solid #f1f5f9;
                text-align: right;
            }

            .table tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                font-size: 0.75rem;
                text-transform: uppercase;
                color: #94a3b8;
                text-align: left;
            }

            .user-info-cell {
                justify-content: flex-end;
            }
        }
    </style>

    <div class="dashboard-container">
        <div class="page-title">
            <h2><i class="fas fa-cross me-2"></i>Demandes d'extraits de décès</h2>
            <div class="page-actions">
                <span class="badge bg-light text-dark p-2 rounded-pill">
                    <i class="fas fa-city me-1"></i>{{ $etatCivil->commune }}
                </span>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" action="{{ route('etat_civil.request.death') }}" class="filter-form">
                <div class="filter-group">
                    <label>État du traitement</label>
                    <select name="etat">
                        <option value="">Tous les états</option>
                        <option value="en attente" {{ request('etat') == 'en attente' ? 'selected' : '' }}>En attente
                        </option>
                        <option value="réçu" {{ request('etat') == 'réçu' ? 'selected' : '' }}>Reçu</option>
                        <option value="terminé" {{ request('etat') == 'terminé' ? 'selected' : '' }}>Terminé</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Type de copie</label>
                    <select name="type">
                        <option value="">Tous les types</option>
                        <option value="simple" {{ request('type') == 'simple' ? 'selected' : '' }}>Copie Simple</option>
                        <option value="integrale" {{ request('type') == 'integrale' ? 'selected' : '' }}>Copie Intégrale
                        </option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Mode de retrait</label>
                    <select name="livraison">
                        <option value="">Tous les modes</option>
                        <option value="livré" {{ request('livraison') == 'livré' ? 'selected' : '' }}>Livré</option>
                        <option value="en cours" {{ request('livraison') == 'en cours' ? 'selected' : '' }}>En cours
                            d'expédition</option>
                        <option value="non livré" {{ request('livraison') == 'non livré' ? 'selected' : '' }}>Sur place
                        </option>
                    </select>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-action">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                    @if (request()->anyFilled(['etat', 'type', 'livraison']))
                        <a href="{{ route('etat_civil.request.death') }}" class="btn-action"
                            style="background-color: #64748b;">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="dashboard-card">
            <div class="card-header">
                <h5>Liste des demandes</h5>
                <span class="badge rounded-pill bg-light text-primary fw-bold">
                    {{ $deces->total() }} total
                </span>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Demandeur</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Date</th>
                            <th>État</th>
                            <th>Livraison</th>
                            <th>Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deces as $dece)
                            <tr>
                                <td data-label="Référence"><span class="id-badge">{{ $dece->reference }}</span></td>
                                <td data-label="Demandeur">
                                    <div class="user-info-cell">
                                        <div class="user-avatar-small">{{ substr($dece->user->name ?? 'U', 0, 1) }}</div>
                                        <span class="user-name">{{ $dece->user->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td data-label="Type">
                                    <span class="fw-600">{{ $dece->type == 'simple' ? 'Simple' : 'Intégrale' }}</span>
                                </td>
                                <td data-label="Quantité">{{ $dece->quantite }} copie(s)</td>
                                <td data-label="Date">{{ $dece->created_at->format('d/m/Y') }}</td>
                                <td data-label="État">
                                    <span class="status-badge status-{{ str_replace(' ', '-', $dece->etat) }}">
                                        {{ ucfirst($dece->etat) }}
                                    </span>
                                </td>
                                <td data-label="Livraison">
                                    @if ($dece->livraison_code)
                                        <span class="text-primary fw-bold"><i class="fas fa-truck me-1"></i>Expédié</span>
                                    @else
                                        <span class="text-muted">Sur place</span>
                                    @endif
                                </td>
                                <td data-label="Agent">
                                    <span class="text-muted">{{ $dece->agent->name ?? '---' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-state-cell">
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">Aucune demande trouvée</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($deces->count() > 0)
                <div class="p-4 border-top">
                    {{ $deces->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
