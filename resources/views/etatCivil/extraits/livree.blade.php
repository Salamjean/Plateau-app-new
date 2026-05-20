@extends('etatCivil.layouts.template')
@section('content')
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>

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

        .filter-tabs {
            display: flex;
            background: white;
            border-radius: 12px;
            padding: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow-x: auto;
            gap: 8px;
        }

        .filter-tab {
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            white-space: nowrap;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .filter-tab.active {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 12px rgba(31, 64, 131, 0.2);
        }

        .dashboard-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            background: white;
        }

        .card-header {
            background: white;
            padding: 20px 25px;
            border-bottom: 1px solid #edf2f7;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h5 {
            color: var(--primary-color);
            font-weight: 700;
            margin: 0;
        }

        .search-box {
            width: 300px;
        }

        .table-responsive {
            padding: 0;
            width: 100%;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0;
        }

        .table thead th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 15px 20px;
            border-top: none;
            border-bottom: 1px solid #edf2f7;
            text-align: center;
        }

        .table tbody td {
            padding: 18px 20px;
            vertical-align: middle;
            color: #334155;
            border-bottom: 1px solid #edf2f7;
            font-size: 0.9rem;
            text-align: center;
        }

        .user-info-cell {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .user-avatar-small {
            width: 35px;
            height: 35px;
            background: #e2e8f0;
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .badge-type {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .badge-naissance {
            background: #eff6ff;
            color: #2563eb;
        }

        .badge-deces {
            background: #fef2f2;
            color: #dc2626;
        }

        .badge-mariage {
            background: #f0fdf4;
            color: #16a34a;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: #f0fdf4;
            color: #16a34a;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
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

        @media (max-width: 768px) {
            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                border: 1px solid #edf2f7;
                border-radius: 12px;
                margin-bottom: 15px;
                padding: 10px;
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid #f1f5f9;
                padding: 12px 10px;
            }

            .table tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                font-size: 0.75rem;
                text-transform: uppercase;
                color: #94a3b8;
            }
        }
    </style>

    <div class='dashboard-container'>
        <div class='page-title'>
            <h2><i class='{{ $icon }} me-2'></i>{{ $title }}</h2>
            <div class='page-actions'>
                <a href='{{ route('etat_civil.dashboard') }}' class='btn-action'>
                    <i class='fas fa-arrow-left me-1'></i>Tableau de bord
                </a>
            </div>
        </div>

        <div class='filter-tabs'>
            <a href='{{ route('etat_civil.livree.taskend', ['type' => 'naissance']) }}'
                class='filter-tab {{ $type == 'naissance' ? 'active' : '' }}'>
                Naissances
            </a>
            <a href='{{ route('etat_civil.livree.taskend', ['type' => 'deces']) }}'
                class='filter-tab {{ $type == 'deces' ? 'active' : '' }}'>
                Décès
            </a>
            <a href='{{ route('etat_civil.livree.taskend', ['type' => 'mariage']) }}'
                class='filter-tab {{ $type == 'mariage' ? 'active' : '' }}'>
                Mariages
            </a>
        </div>

        <div class='dashboard-card'>
            <div class='card-header'>
                <h5>Actes Livrés</h5>
                <div class='search-box'>
                    <div class='input-group'>
                        <span class='input-group-text bg-light border-end-0'><i class='fas fa-search text-muted'></i></span>
                        <input type='text' id='searchInput' class='form-control bg-light border-start-0'
                            placeholder='Rechercher...'>
                    </div>
                </div>
            </div>

            <div class='table-responsive'>
                <table class='table'>
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Demandeur</th>
                            <th>Type</th>
                            <th>Date Livraison</th>
                            <th>Mode</th>
                            <th>Statut</th>
                            <th>Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr>
                                <td data-label='Référence'><span class='id-badge'>{{ $task->reference }}</span></td>
                                <td data-label='Demandeur'>
                                    <div class='user-info-cell'>
                                        <div class='user-avatar-small'>{{ substr($task->user->name ?? 'U', 0, 1) }}</div>
                                        <div class='user-name'>{{ $task->user->name ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td data-label='Type'>
                                    <span class='badge-type badge-{{ $type }}'>
                                        {{ $type == 'naissance' ? 'Naissance' : ($type == 'deces' ? 'Décès' : 'Mariage') }}
                                    </span>
                                </td>
                                <td data-label='Date Livraison'>
                                    {{ $task->updated_at ? $task->updated_at->format('d/m/Y') : '' }}</td>
                                <td data-label='Mode'>
                                    @if ($task->livraison_code)
                                        <span class='text-primary fw-bold'>Expédié</span>
                                    @else
                                        <span class='text-muted'>Sur place</span>
                                    @endif
                                </td>
                                <td data-label='Statut'>
                                    <div class='status-pill'>Livré</div>
                                </td>
                                <td data-label='Agent'>
                                    <span class='text-muted'>{{ $task->agent->name ?? 'Agent' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan='7' class='empty-state-cell'>
                                    <i class="fas fa-folder-open fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted">Aucun acte livré trouvé</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($tasks->hasPages())
                <div class='card-footer bg-white'>
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
