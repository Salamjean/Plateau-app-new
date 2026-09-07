@extends('livreur.layouts.template')

@section('content')
<style>
    :root {
        --primary: #1f4083;
        --secondary: #ea8c51;
        --success: #10b981;
        --warning: #f59e0b;
        --text-main: #2d3748;
        --text-muted: #718096;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
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
        padding: 8px;
        border-radius: 10px;
        color: var(--primary);
    }

    .stats-badge {
        background: var(--primary);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .demandes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .demande-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        border: 1px solid #eef2f7;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .demande-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .card-accent {
        height: 4px;
        background: var(--secondary);
    }

    .card-header {
        padding: 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .ref-badge {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--primary);
        background: rgba(31, 64, 131, 0.05);
        padding: 4px 10px;
        border-radius: 6px;
    }

    .type-badge {
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 20px;
        background: var(--secondary);
        color: white;
    }

    .card-body {
        padding: 1.25rem;
        flex-grow: 1;
    }

    .info-row {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }

    .info-row i {
        color: var(--text-muted);
        font-size: 1.1rem;
        margin-top: 2px;
    }

    .info-content {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 0.95rem;
        color: var(--text-main);
        font-weight: 600;
    }

    .status-banner {
        padding: 6px 15px;
        font-size: 0.8rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
        border-radius: 8px;
        margin-top: 10px;
    }

    .status-en-cours { background: #eff6ff; color: #1e40af; }
    .status-pending { background: #fffbeb; color: #92400e; }

    .card-footer {
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        gap: 10px;
    }

    .btn-action {
        flex: 1;
        padding: 8px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none !important;
        transition: all 0.2s;
    }

    .btn-validate {
        background: var(--primary);
        color: white;
    }

    .btn-validate:hover {
        background: #163266;
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 16px;
        border: 2px dashed #e2e8f0;
    }

    .empty-state i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }
</style>

<div class="page-header">
    <h2 class="page-title">
        <i class="material-icons">local_shipping</i>
        Colis à livrer
    </h2>
    <span class="stats-badge">{{ $demandes->count() }} Demande(s)</span>
</div>

<div class="demandes-grid">
    @forelse($demandes as $demande)
        <div class="demande-card">
            <div class="card-accent"></div>
            <div class="card-header">
                <span class="ref-badge" title="Code de Livraison">{{ $demande->livraison_code ?? 'N/A' }}</span>
                <span class="type-badge">{{ $demande->type_demande }}</span>
            </div>
            
            <div class="card-body">
                <div class="info-row">
                    <i class="material-icons">person</i>
                    <div class="info-content">
                        <span class="info-label">Destinataire</span>
                        <span class="info-value">{{ $demande->nom_destinataire }} {{ $demande->prenom_destinataire ?? '' }}</span>
                    </div>
                </div>

                <div class="info-row">
                    <i class="material-icons">phone</i>
                    <div class="info-content">
                        <span class="info-label">Contact</span>
                        <span class="info-value">{{ $demande->contact_destinataire }}</span>
                    </div>
                </div>

                <div class="info-row">
                    <i class="material-icons">location_on</i>
                    <div class="info-content">
                        <span class="info-label">Adresse de livraison</span>
                        <span class="info-value">{{ $demande->quartier }}</span>
                        <small class="text-muted" style="font-size: 0.8rem;">{{ $demande->adresse_livraison }}</small>
                    </div>
                </div>

                <div class="status-banner {{ $demande->statut_livraison == 'en cours' ? 'status-en-cours' : 'status-pending' }}">
                    <i class="material-icons" style="font-size: 1rem;">info</i>
                    {{ strtoupper($demande->statut_livraison ?? 'En attente') }}
                </div>
            </div>

            <div class="card-footer">
                <a href="{{ route('livreur.validated') }}?ref={{ $demande->reference }}" class="btn-action btn-validate">
                    <i class="material-icons">check_circle</i>
                    Valider maintenant
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="material-icons">inbox</i>
            <h3>Aucun colis à livrer</h3>
            <p class="text-muted">Toutes vos livraisons sont à jour pour le moment.</p>
        </div>
    @endforelse
</div>
@endsection
