@extends('livreur.layouts.template')

@section('content')
<style>
    :root {
        --primary: #1f4083;
        --secondary: #ea8c51;
        --success: #10b981;
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
        background: rgba(16, 185, 129, 0.1);
        padding: 8px;
        border-radius: 10px;
        color: var(--success);
    }

    .stats-badge {
        background: var(--success);
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
        position: relative;
    }

    .card-accent {
        height: 4px;
        background: var(--success);
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
        background: #f1f5f9;
        color: var(--text-muted);
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
        background: #ecfdf5;
        color: #065f46;
    }

    .card-footer {
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .delivery-date {
        font-size: 0.8rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 5px;
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
        <i class="material-icons">task_alt</i>
        Colis livrés
    </h2>
    <span class="stats-badge">{{ $demandes->count() }} Livraison(s)</span>
</div>

<div class="demandes-grid">
    @forelse($demandes as $demande)
        <div class="demande-card">
            <div class="card-accent"></div>
            <div class="card-header">
                <span class="ref-badge">{{ $demande->reference }}</span>
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
                    <i class="material-icons">location_on</i>
                    <div class="info-content">
                        <span class="info-label">Lieu de livraison</span>
                        <span class="info-value">{{  $demande->adresse_livraison  }}</span>
                    </div>
                </div>

                <div class="status-banner">
                    <i class="material-icons" style="font-size: 1rem;">check_circle</i>
                    LIVRAISON EFFECTUÉE
                </div>
            </div>

            <div class="card-footer">
                <div class="delivery-date">
                    <i class="material-icons" style="font-size: 1rem;">event_available</i>
                    Livré le {{ $demande->updated_at->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="material-icons">history</i>
            <h3>Aucune livraison effectuée</h3>
            <p class="text-muted">Vos livraisons validées apparaîtront ici.</p>
        </div>
    @endforelse
</div>
@endsection
