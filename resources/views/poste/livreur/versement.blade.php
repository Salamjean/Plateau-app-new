@extends('poste.layouts.template')

@section('content')
<style>
    :root {
        --primary: #1f4083;
        --secondary: #ea8c51;
        --success: #10b981;
        --danger: #ef4444;
        --text-main: #2d3748;
        --text-muted: #718096;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .btn-back {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }

    .btn-back:hover {
        color: var(--primary);
    }

    .versement-grid {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 2rem;
    }

    .livreur-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 2rem;
        height: min-content;
        position: sticky;
        top: 20px;
        border: 1px solid #edf2f7;
    }

    .profile-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .avatar-large {
        width: 80px;
        height: 80px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        margin: 0 auto 1rem;
    }

    .solde-badge {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
        padding: 15px;
        border-radius: 12px;
        text-align: center;
        margin-top: 1.5rem;
    }

    .solde-badge .label {
        font-size: 0.8rem;
        text-transform: uppercase;
        font-weight: 700;
        display: block;
    }

    .solde-badge .amount {
        font-size: 1.8rem;
        font-weight: 800;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 2.5rem;
        border: 1px solid #edf2f7;
    }

    .form-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: var(--primary);
        outline: none;
    }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-submit:hover {
        background: #163266;
        transform: translateY(-2px);
    }

    @media (max-width: 992px) {
        .versement-grid {
            grid-template-columns: 1fr;
        }
        .livreur-card {
            position: static;
        }
    }
</style>

<div class="page-header">
    <a href="{{ route('delivery.index') }}" class="btn-back">
        <i class="material-icons">arrow_back</i>
        Retour à la liste
    </a>
</div>

<div class="versement-grid">
    <div class="livreur-card">
        <div class="profile-header">
            <div class="avatar-large">{{ substr($livreur->nom, 0, 1) }}{{ substr($livreur->prenom, 0, 1) }}</div>
            <h3 style="margin: 0;">{{ $livreur->nom }} {{ $livreur->prenom }}</h3>
            <p class="text-muted" style="margin: 0;">{{ $livreur->telephone }}</p>
        </div>

        <div style="border-top: 1px solid #f1f5f9; padding-top: 1.5rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span class="text-muted">Commune</span>
                <span style="font-weight: 600;">{{ $livreur->commune }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span class="text-muted">Inscrit le</span>
                <span style="font-weight: 600;">{{ $livreur->created_at->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="solde-badge">
            <span class="label">Solde à verser</span>
            <span class="amount">{{ number_format($livreur->solde, 0, ',', ' ') }} F</span>
        </div>
    </div>

    <div class="form-card">
        <h2 class="form-title">
            <i class="material-icons">payments</i>
            Enregistrer un versement
        </h2>

        @if(session('success'))
            <div style="background: #ecfdf5; color: #059669; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
                <i class="material-icons">check_circle</i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fef2f2; color: #dc2626; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 10px;">
                <i class="material-icons">error</i>
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('poste.livreur.versement.process', $livreur->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Montant du versement (FCFA)</label>
                <input type="number" name="montant" class="form-control" placeholder="Entrez le montant reçu..." max="{{ $livreur->solde }}" required autofocus>
                <small class="text-muted">Le montant ne peut pas dépasser le solde disponible ({{ $livreur->solde }} F).</small>
            </div>

            <div class="form-group">
                <label class="form-label">Référence du reçu (Optionnel)</label>
                <input type="text" name="reference_recu" class="form-control" placeholder="Numéro du récépissé...">
            </div>

            <div class="form-group">
                <label class="form-label">Note / Commentaire</label>
                <textarea name="note" class="form-control" rows="3" placeholder="Informations complémentaires..."></textarea>
            </div>

            <button type="submit" class="btn-submit">
                <i class="material-icons">cloud_upload</i>
                Confirmer le versement
            </button>
        </form>
    </div>
</div>
@endsection
