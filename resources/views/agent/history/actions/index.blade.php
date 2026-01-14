@extends('agent.layouts.template')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{asset('dasboard/agent.css')}}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<div class="dashboard-container">
  <h1 class="dashboard-title">
    <i class="fas fa-history me-2"></i>Historique des Actions
    <p>Consultez l'historique de toutes vos actions sur les demandes</p>
  </h1>

  <!-- Statistiques -->
  <div class="stats-container">
    <div class="stat-card">
      <i class="fas fa-chart-bar stat-icon"></i>
      <div class="stat-number">{{ $stats['total'] }}</div>
      <div class="stat-label">Total Actions</div>
    </div>
    
    <div class="stat-card">
      <i class="fas fa-exchange-alt stat-icon"></i>
      <div class="stat-number">{{ $stats['changements'] }}</div>
      <div class="stat-label">Changements d'état</div>
    </div>
    
    <div class="stat-card">
      <i class="fas fa-times-circle stat-icon"></i>
      <div class="stat-number">{{ $stats['rejets'] }}</div>
      <div class="stat-label">Rejets</div>
    </div>
    
    <div class="stat-card">
      <i class="fas fa-check-circle stat-icon"></i>
      <div class="stat-number">{{ $stats['recuperations'] }}</div>
      <div class="stat-label">Reçus</div>
    </div>
  </div>

  <!-- Filtres -->
  <div class="filter-section">
    <h5 class="filter-title"><i class="fas fa-filter"></i> Filtrer les actions</h5>
    <form class="filter-form" method="GET" action="{{ route('agent.history.actions') }}">
      <div class="form-group">
        <label class="form-label">Type de demande</label>
        <select class="form-select" name="type">
          <option value="">Tous</option>
          <option value="naissance" {{ request('type') == 'naissance' ? 'selected' : '' }}>Naissance</option>
          <option value="mariage" {{ request('type') == 'mariage' ? 'selected' : '' }}>Mariage</option>
          <option value="deces" {{ request('type') == 'deces' ? 'selected' : '' }}>Décès</option>
        </select>
      </div>
      
      <div class="form-group">
        <label class="form-label">Action</label>
        <select class="form-select" name="action">
          <option value="">Toutes</option>
          <option value="changement_etat" {{ request('action') == 'changement_etat' ? 'selected' : '' }}>Changement d'état</option>
          <option value="rejet" {{ request('action') == 'rejet' ? 'selected' : '' }}>Rejet</option>
          <option value="recu" {{ request('action') == 'recu' ? 'selected' : '' }}>Reçu</option>
          <option value="termine" {{ request('action') == 'termine' ? 'selected' : '' }}>Terminé</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Référence</label>
        <input type="text" class="form-control" name="reference" placeholder="Rechercher..." value="{{ request('reference') }}">
      </div>
      
      <div class="form-group">
        <label class="form-label">Date début</label>
        <input type="date" class="form-control" name="date_debut" value="{{ request('date_debut') }}">
      </div>

      <div class="form-group">
        <label class="form-label">Date fin</label>
        <input type="date" class="form-control" name="date_fin" value="{{ request('date_fin') }}">
      </div>
      
      <button type="submit" class="btn-filter">
        <i class="fas fa-search me-1"></i>Filtrer
      </button>
      <a href="{{ route('agent.history.actions') }}" class="btn-filter" style="background: #6c757d; text-decoration: none;">
        <i class="fas fa-times"></i>
      </a>
    </form>
  </div>

  <!-- Tableau des actions -->
  <div class="dashboard-card">
    <div class="card-header">
      <h5><i class="fas fa-list me-2"></i>Liste des actions</h5>
      <span class="badge bg-light text-dark">{{ $actions->total() }} actions</span>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th class="text-center">Date/Heure</th>
              <th class="text-center">Référence</th>
              <th class="text-center">Type</th>
              <th class="text-center">Action</th>
              <th class="text-center">Ancien État</th>
              <th class="text-center">Nouvel État</th>
              <th class="text-center">Détails</th>
            </tr>
          </thead>
          <tbody>
            @forelse($actions as $action)
              <tr class="text-center">
                <td>
                  <strong>{{ $action->created_at->format('d/m/Y') }}</strong><br>
                  <small class="text-muted">{{ $action->created_at->format('H:i') }}</small>
                </td>
                <td>
                  <span class="badge" >{{ $action->reference }}</span>
                </td>
                <td>
                  @if($action->demande_type == 'naissance')
                    <span class="badge-type badge-naiss">Naissance</span>
                  @elseif($action->demande_type == 'mariage')
                    <span class="badge-type badge-mariage">Mariage</span>
                  @else
                    <span class="badge-type badge-deces">Décès</span>
                  @endif
                </td>
                <td>
                  @if($action->action == 'rejet')
                    <span class="badge-action badge-action-rejet">Rejet</span>
                  @elseif($action->action == 'changement_etat')
                    <span class="badge-action badge-action-changement">Changement</span>
                  @elseif($action->action == 'recu')
                    <span class="badge-action badge-action-recu">Reçu</span>
                  @elseif($action->action == 'termine')
                    <span class="badge-action badge-action-termine">Terminé</span>
                  @else
                    <span class="badge-action badge-action-default">{{ $action->action }}</span>
                  @endif
                </td>
                <td>
                  @if($action->ancien_etat == 'terminé')
                    <span class="badge-etat badge-etat-termine">{{ $action->ancien_etat }}</span>
                  @elseif($action->ancien_etat == 'rejetée')
                    <span class="badge-etat badge-etat-rejete">{{ $action->ancien_etat }}</span>
                  @elseif($action->ancien_etat == 'réçu')
                    <span class="badge-etat badge-etat-recu">{{ $action->ancien_etat }}</span>
                  @elseif($action->ancien_etat == 'en attente')
                    <span class="badge-etat badge-etat-attente">{{ $action->ancien_etat }}</span>
                  @else
                    <span class="badge-etat badge-etat-default">{{ $action->ancien_etat ?? '-' }}</span>
                  @endif
                </td>
                <td>
                  @if($action->nouvel_etat == 'terminé')
                    <span class="badge-etat badge-etat-termine">{{ $action->nouvel_etat }}</span>
                  @elseif($action->nouvel_etat == 'rejetée')
                    <span class="badge-etat badge-etat-rejete">{{ $action->nouvel_etat }}</span>
                  @elseif($action->nouvel_etat == 'réçu')
                    <span class="badge-etat badge-etat-recu">{{ $action->nouvel_etat }}</span>
                  @elseif($action->nouvel_etat == 'en attente')
                    <span class="badge-etat badge-etat-attente">{{ $action->nouvel_etat }}</span>
                  @else
                    <span class="badge-etat badge-etat-default">{{ $action->nouvel_etat ?? '-' }}</span>
                  @endif
                </td>
                <td>
                  @if($action->motif)
                    <button type="button" class="btn-action" onclick="showMotif({{ $action->id }})">
                      <i class="fas fa-eye"></i> Voir motif
                    </button>
                    
                    <!-- Div caché pour le motif -->
                    <div id="motif-{{ $action->id }}" style="display: none;">
                      <div class="motif-content">
                        <strong>Motif de rejet - {{ $action->reference }}</strong>
                        <hr>
                        <pre style="white-space: pre-wrap; text-align: left; background: #f8f9fa; padding: 10px; border-radius: 5px;">{{ $action->motif }}</pre>
                        
                        @if($action->champs_modifies)
                          <hr>
                          <strong>Champs concernés:</strong>
                          <ul style="text-align: left;">
                            @foreach($action->champs_modifies as $champ)
                              <li>{{ $champ }}</li>
                            @endforeach
                          </ul>
                        @endif
                      </div>
                    </div>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="empty-state">
                  <i class="fas fa-inbox"></i>
                  <h5>Aucune action enregistrée</h5>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      
      <!-- Pagination personnalisée -->
      @if($actions->hasPages())
      <div class="custom-pagination">
        <div class="pagination-info">
          Affichage {{ $actions->firstItem() }} à {{ $actions->lastItem() }} sur {{ $actions->total() }} résultats
        </div>
        <div class="pagination-buttons">
          @if($actions->onFirstPage())
            <span class="page-btn disabled">« Précédent</span>
          @else
            <a href="{{ $actions->previousPageUrl() }}" class="page-btn">« Précédent</a>
          @endif
          
          <span class="page-numbers">
            @foreach($actions->getUrlRange(1, $actions->lastPage()) as $page => $url)
              @if($page == $actions->currentPage())
                <span class="page-num active">{{ $page }}</span>
              @else
                <a href="{{ $url }}" class="page-num">{{ $page }}</a>
              @endif
            @endforeach
          </span>
          
          @if($actions->hasMorePages())
            <a href="{{ $actions->nextPageUrl() }}" class="page-btn">Suivant »</a>
          @else
            <span class="page-btn disabled">Suivant »</span>
          @endif
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

<script>
function showMotif(id) {
  const motifContent = document.getElementById('motif-' + id).innerHTML;
  
  Swal.fire({
    title: '',
    html: motifContent,
    width: 600,
    confirmButtonText: 'Fermer',
    confirmButtonColor: '#1977cc'
  });
}
</script>

<style>
.filter-form {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  align-items: flex-end;
}

.filter-form .form-group {
  flex: 1;
  min-width: 150px;
}

.filter-form .form-control,
.filter-form .form-select {
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.badge-naiss {
  background: linear-gradient(135deg, #17a2b8, #138496) !important;
}

.badge-mariage {
  background: linear-gradient(135deg, #28a745, #20c997) !important;
}

.badge-deces {
  background: linear-gradient(135deg, #6c757d, #495057) !important;
}

.motif-content {
  text-align: center;
}

.pagination {
  justify-content: center;
}

.pagination .page-link {
  color: #1977cc;
  border-radius: 5px;
  margin: 0 3px;
}

.pagination .page-item.active .page-link {
  background-color: #1977cc;
  border-color: #1977cc;
}

/* Badges pour Action */
.badge-action {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  color: white;
}

.badge-action-rejet {
  background: linear-gradient(135deg, #dc3545, #c82333);
}

.badge-action-changement {
  background: linear-gradient(135deg, #007bff, #0056b3);
}

.badge-action-recu {
  background: linear-gradient(135deg, #17a2b8, #138496);
}

.badge-action-termine {
  background: linear-gradient(135deg, #28a745, #1e7e34);
}

.badge-action-default {
  background: linear-gradient(135deg, #6c757d, #495057);
}

/* Badges pour États */
.badge-etat {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  color: white;
}

.badge-etat-termine {
  background: linear-gradient(135deg, #28a745, #1e7e34);
}

.badge-etat-rejete {
  background: linear-gradient(135deg, #dc3545, #c82333);
}

.badge-etat-recu {
  background: linear-gradient(135deg, #17a2b8, #138496);
}

.badge-etat-attente {
  background: linear-gradient(135deg, #ffc107, #e0a800);
  color: #212529;
}

.badge-etat-default {
  background: linear-gradient(135deg, #6c757d, #495057);
}

/* Pagination personnalisée */
.custom-pagination {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
  margin-top: 25px;
  padding-top: 20px;
  border-top: 1px solid #e9ecef;
}

.pagination-info {
  color: #6c757d;
  font-size: 0.9rem;
}

.pagination-buttons {
  display: flex;
  align-items: center;
  gap: 10px;
}

.page-btn {
  padding: 8px 16px;
  background: linear-gradient(135deg, #1977cc, #1565c0);
  color: white;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 500;
  font-size: 0.85rem;
  transition: all 0.3s ease;
}

.page-btn:hover {
  background: linear-gradient(135deg, #1565c0, #0d47a1);
  color: white;
  transform: translateY(-1px);
}

.page-btn.disabled {
  background: #e9ecef;
  color: #adb5bd;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 5px;
}

.page-num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 6px;
  background: #f8f9fa;
  color: #495057;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s ease;
  border: 1px solid #dee2e6;
}

.page-num:hover {
  background: #e9ecef;
  color: #1977cc;
}

.page-num.active {
  background: linear-gradient(135deg, #1977cc, #1565c0);
  color: white;
  border-color: #1977cc;
}
</style>
@endsection
