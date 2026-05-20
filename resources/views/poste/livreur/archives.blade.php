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
    --shadow: 0 4px 15px rgba(0,0,0,0.05);
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
    background: rgba(239, 68, 68, 0.1);
    padding: 10px;
    border-radius: 12px;
    color: var(--danger);
  }

  .btn-back {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: white;
    color: var(--text-main);
    box-shadow: var(--shadow);
    text-decoration: none !important;
    transition: all 0.2s;
  }

  .btn-back:hover {
    background: var(--primary);
    color: white;
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
    background: #f1f5f9;
    color: var(--text-muted);
  }

  .livreur-name {
    font-weight: 700;
    color: var(--text-main);
    display: block;
    font-size: 0.95rem;
  }

  .status-badge {
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    display: inline-block;
    background: #f1f5f9;
    color: var(--text-muted);
  }

  .btn-restore {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
  }

  .btn-restore:hover {
    background: var(--success);
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
  <div class="d-flex align-items-center gap-3">
    <a href="{{ route('delivery.index') }}" class="btn-back">
      <i class="material-icons">arrow_back</i>
    </a>
    <h2 class="page-title">
      <i class="material-icons">archive</i>
      Livreurs Archivés
    </h2>
  </div>
</div>

<div class="card-table">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Livreur</th>
          <th>Dernière affectation</th>
          <th>Zone</th>
          <th>Statut</th>
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
              </div>
              <div>
                <span class="livreur-name">{{ $livreur->nom }} {{ $livreur->prenom }}</span>
                <span class="text-muted" style="font-size: 0.8rem;">{{ $livreur->telephone }}</span>
              </div>
            </div>
          </td>
          <td>
            <span class="text-muted">{{ $livreur->updated_at->format('d/m/Y') }}</span>
          </td>
          <td>
            <div class="text-main" style="font-size: 0.9rem;">{{ $livreur->ville }}</div>
            <div class="text-muted" style="font-size: 0.75rem;">{{ $livreur->commune }}</div>
          </td>
          <td>
            <span class="status-badge">ARCHIVÉ</span>
          </td>
          <td class="text-end">
            <form action="{{ route('poste.livreur.restore', $livreur->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Restaurer ce livreur ?')">
              @csrf
              @method('PUT')
              <button type="submit" class="btn-restore" title="Restaurer">
                <i class="material-icons">settings_backup_restore</i>
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5">
            <div class="empty-state">
              <i class="material-icons">inventory_2</i>
              <h3>Aucun archivage</h3>
              <p class="text-muted">Les livreurs archivés apparaîtront ici.</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
