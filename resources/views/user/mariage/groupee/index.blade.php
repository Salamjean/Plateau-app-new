@extends('user.layouts.template')

@section('content')
    <style>
        :root {
            --primary: #1f4083;
            --primary-light: #e8f1fb;
            --text-navy: #1a2b4a;
        }

        .glass-container {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.5);
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
            padding: 1.5rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-weight: 800;
            color: var(--text-navy);
            font-size: 1.6rem;
            letter-spacing: -0.5px;
            margin: 0;
        }

        .btn-add-premium {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary), #1565c0);
            color: #fff;
            padding: 10px 22px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: 0.25s;
            box-shadow: 0 4px 12px rgba(25,119,204,0.3);
        }
        .btn-add-premium:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(25,119,204,0.4); color: #fff; }

        /* Table */
        #groupesMariageTable { border-collapse: separate; border-spacing: 0 6px; }
        #groupesMariageTable thead th {
            background: var(--primary-light);
            color: var(--primary);
            font-weight: 700;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border: none;
            white-space: nowrap;
        }
        #groupesMariageTable tbody tr {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: 0.2s;
        }
        #groupesMariageTable tbody tr:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.09); transform: translateY(-1px); }
        #groupesMariageTable tbody td {
            padding: 14px 16px;
            border: none;
            vertical-align: middle;
            font-size: 0.88rem;
        }
        #groupesMariageTable tbody td:first-child { border-radius: 16px 0 0 16px !important; }
        #groupesMariageTable tbody td:last-child  { border-radius: 0 16px 16px 0 !important; }

        /* Badges statut */
        .badge-status {
            padding: 6px 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.72rem;
            text-transform: uppercase;
            white-space: nowrap;
        }
        .badge-en-attente { background: #fff7ed; color: #c2410c; }
        .badge-en-cours   { background: #eff6ff; color: #1d4ed8; }
        .badge-termine    { background: #f0fdf4; color: #15803d; }
        .badge-rejete     { background: #fef2f2; color: #b91c1c; }

        /* Badges type */
        .badge-type {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: 8px;
            font-size: 0.72rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.4px; white-space: nowrap;
        }
        .badge-type-simple   { background: #eff6ff; color: #1d4ed8; }
        .badge-type-integral { background: #f5f3ff; color: #7c3aed; }

        /* Mode badge */
        .delivery-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 12px; background: #f8fafc;
            border-radius: 8px; color: #64748b;
            font-size: 0.8rem; font-weight: 600;
        }

        /* Montant */
        .montant-cell { font-weight: 800; color: var(--text-navy); font-size: 0.92rem; }
        .montant-cell small { font-weight: 500; color: #94a3b8; font-size: 0.72rem; }

        /* Action button */
        .btn-action {
            width: 36px; height: 36px; border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 0.85rem; text-decoration: none; transition: 0.2s;
        }
        .btn-view { background: var(--primary-light); color: var(--primary); }
        .btn-view:hover { background: var(--primary); color: #fff; }

        /* Stat cards */
        .stat-card {
            display: flex; align-items: center; gap: 1rem;
            padding: 1rem 1.25rem; border-radius: 16px;
            background: #fff; border: 1.5px solid #f0f4fa;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04); transition: 0.25s;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0; }
        .stat-value { font-size: 1.5rem; font-weight: 800; color: var(--text-navy); line-height: 1; }
        .stat-label { font-size: 0.72rem; font-weight: 600; color: #94a3b8; margin-top: 3px; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-total    .stat-icon { background: #eef5fc; color: var(--primary); }
        .stat-pending  .stat-icon { background: #fff7ed; color: #ea580c; }
        .stat-progress .stat-icon { background: #eff6ff; color: #2563eb; }
        .stat-done     .stat-icon { background: #f0fdf4; color: #16a34a; }
        .stat-rejected .stat-icon { background: #fef2f2; color: #dc2626; }
        .stat-acts     .stat-icon { background: #fdf4ff; color: #9333ea; }

        @media (max-width: 768px) {
            #groupesMariageTable { min-width: 800px; }
            .page-title { font-size: 1.25rem; }
        }
    </style>

    <div class="dashboard-final container-fluid py-4 animate-fade-in">

        <div class="page-header">
            <h1 class="page-title">Mes demandes groupées de mariage</h1>
            <a href="{{ route('user.extrait.mariage.groupee.create') }}" class="btn-add-premium">
                <i class="fas fa-plus"></i> Nouvelle demande groupée
            </a>
        </div>

        @php
            $total         = $groupes->count();
            $enAttente     = $groupes->where('etat', 'en attente')->count();
            $enCours       = $groupes->where('etat', 'réçu')->count();
            $termine       = $groupes->where('etat', 'terminé')->count();
            $rejete        = $groupes->where('etat', 'rejetée')->count();
            $totalActes    = $groupes->sum('qty_simple') + $groupes->sum('qty_integral');
        @endphp

        <div class="row g-3 mb-4">
            <div class="col-6 col-md">
                <div class="stat-card stat-total">
                    <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
                    <div><div class="stat-value">{{ $total }}</div><div class="stat-label">Demandes</div></div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card stat-acts">
                    <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                    <div><div class="stat-value">{{ $totalActes }}</div><div class="stat-label">Actes total</div></div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card stat-pending">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div><div class="stat-value">{{ $enAttente }}</div><div class="stat-label">En attente</div></div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card stat-progress">
                    <div class="stat-icon"><i class="fas fa-spinner"></i></div>
                    <div><div class="stat-value">{{ $enCours }}</div><div class="stat-label">En cours</div></div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card stat-done">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div><div class="stat-value">{{ $termine }}</div><div class="stat-label">Terminées</div></div>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="stat-card stat-rejected">
                    <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                    <div><div class="stat-value">{{ $rejete }}</div><div class="stat-label">Rejetées</div></div>
                </div>
            </div>
        </div>

        <div class="glass-container">
            <div class="table-responsive">
                <table id="groupesMariageTable" class="table">
                    <thead>
                        <tr>
                            <th class="text-center">Référence</th>
                            <th class="text-center">Quantités</th>
                            <th class="text-center">Total actes</th>
                            <th class="text-center">Montant</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Mode</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($groupes as $groupe)
                            <tr>
                                <td class="text-center">
                                    <span class="fw-bold text-navy">{{ $groupe->reference }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        @if ($groupe->qty_simple > 0)
                                            <span class="badge-type badge-type-simple">
                                                <i class="fas fa-file-alt"></i> {{ $groupe->qty_simple }} Simple
                                            </span>
                                        @endif
                                        @if ($groupe->qty_integral > 0)
                                            <span class="badge-type badge-type-integral">
                                                <i class="fas fa-scroll"></i> {{ $groupe->qty_integral }} Intégrale
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ $groupe->qty_simple + $groupe->qty_integral }}</span>
                                    <small class="text-muted"> acte(s)</small>
                                </td>
                                <td class="text-center montant-cell">
                                    {{ number_format($groupe->montant_total, 0, ',', ' ') }}
                                    <small>FCFA</small>
                                </td>
                                <td class="text-center">
                                    @if ($groupe->etat === 'rejetée')
                                        <span class="badge-status badge-rejete">REJETÉ</span>
                                    @elseif ($groupe->etat === 'en attente')
                                        <span class="badge-status badge-en-attente">EN ATTENTE</span>
                                    @elseif ($groupe->etat === 'réçu')
                                        <span class="badge-status badge-en-cours">EN COURS</span>
                                    @else
                                        <span class="badge-status badge-termine">TERMINÉ</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="delivery-badge">
                                        <i class="fas {{ $groupe->choix_option === 'livraison' ? 'fa-motorcycle' : 'fa-university' }}"></i>
                                        {{ $groupe->choix_option === 'livraison' ? 'Livraison' : 'Mairie' }}
                                    </span>
                                </td>
                                <td class="text-center text-muted" style="font-size:0.82rem;">
                                    {{ $groupe->created_at->format('d/m/Y') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('agent.demandes.mariage.groupe.show', $groupe->id) }}"
                                       class="btn-action btn-view" title="Voir le détail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-layer-group fa-2x mb-3 d-block opacity-25"></i>
                                    Aucune demande groupée pour le moment.
                                    <br>
                                    <a href="{{ route('user.extrait.mariage.groupee.create') }}" class="btn-add-premium mt-3 d-inline-flex">
                                        <i class="fas fa-plus"></i> Créer une demande groupée
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#groupesMariageTable')) {
                $('#groupesMariageTable').DataTable({
                    pageLength: 10,
                    order: [[6, 'desc']],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
                    },
                    columnDefs: [{ orderable: false, targets: [1, 7] }]
                });
            }
        });
    </script>
@endsection
