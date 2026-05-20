@extends('agent.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .groupes-container { padding: 2rem; max-width: 1400px; margin: 0 auto; }
    .page-header {
        background: linear-gradient(135deg, #1f4083 0%, #4facfe 100%);
        color: #fff; padding: 2rem; border-radius: 20px; margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(25,119,204,0.2);
        display: flex; justify-content: space-between; align-items: center;
    }
    .page-header h1 { font-size: 1.8rem; font-weight: 800; margin: 0; }
    .page-header p { opacity: 0.9; margin: 5px 0 0 0; font-size: 0.95rem; }
    .badge-count {
        background: rgba(255,255,255,0.25); padding: 8px 16px; border-radius: 50px;
        font-weight: 700; font-size: 1.1rem;
    }
    .groupe-card {
        background: #fff; border-radius: 18px; padding: 1.5rem;
        margin-bottom: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-left: 5px solid #1f4083; transition: 0.3s;
    }
    .groupe-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.1); transform: translateY(-2px); }
    .groupe-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem; }
    .groupe-ref { font-size: 1.1rem; font-weight: 800; color: #2b3674; }
    .groupe-meta { color: #718096; font-size: 0.85rem; margin-top: 4px; }
    .groupe-stats {
        display: flex; gap: 12px; flex-wrap: wrap;
    }
    .stat-pill {
        background: #f4f7fe; padding: 6px 14px; border-radius: 50px;
        font-size: 0.85rem; font-weight: 700; color: #2b3674;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .stat-pill.simple { background: rgba(1,181,116,0.1); color: #01B574; }
    .stat-pill.integral { background: rgba(255,59,103,0.1); color: #FF3B67; }
    .stat-pill.livraison { background: rgba(255,181,71,0.15); color: #c47900; }
    .stat-pill.retrait { background: rgba(99,102,241,0.1); color: #6366f1; }
    .stat-pill.amount { background: #2b3674; color: #fff; }

    .badge-etat { padding: 6px 14px; border-radius: 50px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .badge-etat.attente { background: #fff3cd; color: #856404; }
    .badge-etat.recu { background: #d1ecf1; color: #0c5460; }
    .badge-etat.termine { background: #d4edda; color: #155724; }
    .badge-etat.rejetee { background: #f8d7da; color: #721c24; }

    .demandeur-info { display: flex; align-items: center; gap: 12px; }
    .demandeur-avatar {
        width: 45px; height: 45px; border-radius: 50%;
        background: linear-gradient(135deg, #1f4083, #4facfe); color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1rem;
    }
    .btn-action {
        padding: 10px 20px; border-radius: 12px; font-weight: 700;
        text-decoration: none; transition: 0.3s; display: inline-flex;
        align-items: center; gap: 8px; border: none; cursor: pointer;
        font-size: 0.9rem;
    }
    .btn-traiter { background: #1f4083; color: #fff; }
    .btn-traiter:hover { background: #0d4a85; color: #fff; transform: translateX(3px); }
    .btn-voir { background: #f4f7fe; color: #2b3674; }
    .btn-voir:hover { background: #e2e8f0; }

    .empty-state { text-align: center; padding: 3rem; color: #a3aed0; }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; }

    @media (max-width: 768px) {
        .page-header { flex-direction: column; gap: 1rem; align-items: flex-start; }
        .groupe-header { flex-direction: column; }
    }
</style>

<div class="groupes-container">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-shopping-basket"></i> Demandes groupées — Naissances</h1>
            <p>Plusieurs actes commandés en une seule transaction</p>
        </div>
        <div class="badge-count">{{ $groupes->total() }} groupe(s)</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="border-radius: 12px; padding: 1rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="border-radius: 12px; padding: 1rem;">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    @forelse($groupes as $groupe)
        <div class="groupe-card">
            <div class="groupe-header">
                <div>
                    <div class="groupe-ref">
                        <i class="fas fa-tag"></i> {{ $groupe->reference }}
                        @if($groupe->etat == 'en attente')
                            <span class="badge-etat attente">En attente</span>
                        @elseif($groupe->etat == 'réçu')
                            <span class="badge-etat recu">Reçu</span>
                        @elseif($groupe->etat == 'terminé')
                            <span class="badge-etat termine">Terminé</span>
                        @elseif($groupe->etat == 'rejetée')
                            <span class="badge-etat rejetee">Rejeté</span>
                        @else
                            <span class="badge-etat attente">{{ $groupe->etat }}</span>
                        @endif
                    </div>
                    <div class="groupe-meta">
                        <i class="fas fa-clock"></i> {{ $groupe->created_at->format('d/m/Y à H:i') }}
                    </div>
                </div>

                <div class="demandeur-info">
                    <div class="demandeur-avatar">
                        {{ strtoupper(substr($groupe->user->name ?? '?', 0, 1) . substr($groupe->user->prenom ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight: 700; color: #2b3674;">{{ $groupe->user->name ?? '' }} {{ $groupe->user->prenom ?? '' }}</div>
                        <div style="color: #718096; font-size: 0.8rem;">{{ $groupe->user->contact ?? '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="groupe-stats">
                @if($groupe->qty_simple > 0)
                    <span class="stat-pill simple">
                        <i class="fas fa-file-alt"></i> {{ $groupe->qty_simple }} simple(s)
                    </span>
                @endif
                @if($groupe->qty_integral > 0)
                    <span class="stat-pill integral">
                        <i class="fas fa-file-contract"></i> {{ $groupe->qty_integral }} intégral(s)
                    </span>
                @endif

                @if($groupe->choix_option == 'livraison')
                    <span class="stat-pill livraison">
                        <i class="fas fa-motorcycle"></i> Livraison
                    </span>
                @else
                    <span class="stat-pill retrait">
                        <i class="fas fa-university"></i> Retrait sur place
                    </span>
                @endif

                @if($groupe->is_free_request)
                    <span class="stat-pill" style="background: rgba(67,24,255,0.1); color: #4318FF;">
                        <i class="fas fa-gift"></i> {{ $groupe->free_timbres_count }} offert(s)
                    </span>
                @endif

                <span class="stat-pill amount">
                    <i class="fas fa-coins"></i> {{ number_format($groupe->montant_total, 0, ',', ' ') }} FCFA
                </span>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 1.5rem; justify-content: flex-end;">
                <a href="{{ route('agent.demandes.naissance.groupe.show', $groupe->id) }}" class="btn-action btn-traiter">
                    <i class="fas fa-edit"></i> Traiter ce groupe
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h4>Aucune demande groupée en cours</h4>
            <p>Les demandes groupées en attente de traitement apparaîtront ici.</p>
        </div>
    @endforelse

    @if($groupes->hasPages())
        <div style="margin-top: 2rem;">
            {{ $groupes->links() }}
        </div>
    @endif
</div>
@endsection
