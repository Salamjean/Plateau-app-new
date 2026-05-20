@extends('poste.layouts.template')

@section('content')
    <style>
        :root {
            --primary: #1f4083;
            --secondary: #ea8c51;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --text-main: #2d3748;
            --text-muted: #718096;
            --bg-light: #f8fafc;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title i {
            background: rgba(31, 64, 131, 0.1);
            padding: 10px;
            border-radius: 12px;
            color: var(--primary);
        }

        .btn-add {
            background: var(--primary);
            color: white !important;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none !important;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(31, 64, 131, 0.2);
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(31, 64, 131, 0.3);
            background: #163266;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .stat-info .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            display: block;
        }

        .stat-info .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
        }

        /* Table Customization */
        .card-table {
            background: white;
            border-radius: 24px;
            box-shadow: var(--shadow);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .table {
            margin: 0;
            width: 100%;
        }

        .table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #f1f5f9;
            color: var(--text-muted);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 1.25rem 1.5rem;
            text-align: left;
        }

        .table tbody tr:hover {
            background-color: #fcfcfd;
        }

        .table td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .livreur-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            position: relative;
            background: #f1f5f9;
            color: var(--primary);
        }

        .avatar .status-dot {
            position: absolute;
            bottom: -2px;
            right: -2px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 3px solid white;
        }

        .livreur-name {
            font-weight: 700;
            color: var(--text-main);
            display: block;
            font-size: 0.95rem;
        }

        .livreur-email {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: var(--text-main);
        }

        .contact-item i {
            font-size: 1rem;
            color: var(--text-muted);
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .bg-success-lite {
            background: #ecfdf5;
            color: #059669;
        }

        .bg-danger-lite {
            background: #fef2f2;
            color: #dc2626;
        }

        .solde-badge {
            color: var(--primary);
            font-weight: 800;
            font-size: 1.1rem;
        }

        .actions-cell {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-action {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            text-decoration: none !important;
        }

        .btn-edit {
            background: #eff6ff;
            color: #2563eb;
        }

        .btn-edit:hover {
            background: #2563eb;
            color: white;
        }

        .btn-money {
            background: #fff7ed;
            color: #ea580c;
        }

        .btn-money:hover {
            background: #ea580c;
            color: white;
        }

        .btn-archive {
            background: #fef2f2;
            color: #dc2626;
        }

        .btn-archive:hover {
            background: #dc2626;
            color: white;
        }

        .empty-state {
            padding: 5rem 2rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 5rem;
            color: #e2e8f0;
            margin-bottom: 1.5rem;
        }
    </style>

    <div class="page-header">
        <h2 class="page-title">
            <i class="material-icons">diversity_3</i>
            Effectifs Livreurs
        </h2>
        <a href="{{ route('delivery.create') }}" class="btn-add">
            <i class="material-icons">person_add</i>
            Nouveau livreur
        </a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Total Livreurs</span>
                <div class="stat-value">{{ $livreurs->count() }}</div>
            </div>
            <div class="stat-icon" style="background: rgba(31, 64, 131, 0.1); color: var(--primary);">
                <i class="material-icons">groups</i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">En Service</span>
                <div class="stat-value">{{ $livreurs->where('disponible', '1')->count() }}</div>
            </div>
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                <i class="material-icons">sensors</i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span class="stat-label">Solde Cumulé</span>
                <div class="stat-value">{{ number_format($livreurs->sum('solde'), 0, ',', ' ') }} <small
                        style="font-size: 0.8rem;">F</small></div>
            </div>
            <div class="stat-icon" style="background: rgba(234, 140, 81, 0.1); color: var(--secondary);">
                <i class="material-icons">wallet</i>
            </div>
        </div>
    </div>

    <div class="card-table">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Livreur</th>
                        <th>Coordonnées</th>
                        <th>Zone d'activité</th>
                        <th>Statut</th>
                        <th>Solde</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($livreurs as $livreur)
                        <tr>
                            <td>
                                <div class="livreur-info">
                                    <div class="avatar">
                                        {{ substr($livreur->nom, 0, 1) }}{{ substr($livreur->prenom, 0, 1) }}
                                        <div class="status-dot"
                                            style="background: {{ $livreur->disponible == '1' ? 'var(--success)' : 'var(--danger)' }};">
                                        </div>
                                    </div>
                                    <div>
                                        <span class="livreur-name">{{ $livreur->nom }} {{ $livreur->prenom }}</span>
                                        <span class="livreur-email">{{ $livreur->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="contact-info">
                                    <div class="contact-item"><i class="material-icons">phone</i> {{ $livreur->telephone }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="contact-info">
                                    <div class="contact-item"><i class="material-icons">location_city</i>
                                        {{ $livreur->ville }}</div>
                                    <div class="contact-item text-muted" style="font-size: 0.75rem;">
                                        {{ $livreur->commune }}</div>
                                </div>
                            </td>
                            <td>
                                <span
                                    class="status-badge {{ $livreur->disponible == '1' ? 'bg-success-lite' : 'bg-danger-lite' }}">
                                    {{ $livreur->disponible == '1' ? 'ACTIF' : 'INACTIF' }}
                                </span>
                            </td>
                            <td>
                                <span class="solde-badge">{{ number_format($livreur->solde, 0, ',', ' ') }}</span>
                            </td>
                            <td class="text-end">
                                <div class="actions-cell">
                                    <a href="{{ route('poste.livreur.edit', $livreur->id) }}" class="btn-action btn-edit"
                                        title="Modifier">
                                        <i class="material-icons">edit</i>
                                    </a>
                                    <a href="{{ route('poste.livreur.versement', $livreur->id) }}"
                                        class="btn-action btn-money" title="Versement">
                                        <i class="material-icons">payments</i>
                                    </a>
                                    <form action="{{ route('poste.livreur.archive', $livreur->id) }}" method="POST"
                                        style="display:inline;" onsubmit="return confirm('Archiver ce livreur ?')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn-action btn-archive" title="Archiver">
                                            <i class="material-icons">archive</i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="material-icons">hail</i>
                                    <h3>Aucun livreur</h3>
                                    <p class="text-muted">Commencez par ajouter votre premier livreur pour votre section
                                        Courrier.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
