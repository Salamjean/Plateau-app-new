@extends('etatCivil.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<style>
  :root {
    --primary-color: #1977cc;
    --primary-light: #1977cc;
    --primary-dark: #1977cc;
    --secondary-color: #1977cc;
    --secondary-light: #1977cc;
    --secondary-dark: #1977cc;
    --light-color: #ffffff;
    --dark-color: #212529;
    --gray-color: #6c757d;
    --light-gray: #f8f9fa;
    --border-radius: 12px;
    --box-shadow: 0 8px 20px rgba(0, 126, 0, 0.1);
    --transition: all 0.3s ease;
  }

  body {
    background-color: var(--light-gray);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
  }

  .dashboard-container {
    padding: 30px;
    max-width: 1400px;
    margin: 0 auto;
  }

  .page-title {
    color: var(--secondary-color);
    font-weight: 700;
    margin-bottom: 30px;
    position: relative;
    padding-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
  }

  .page-title:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100px;
    height: 4px;
    background: var(--primary-color);
    border-radius: 2px;
  }

  .page-actions {
    display: flex;
    gap: 10px;
  }

  .filter-tabs {
    display: flex;
    background: white;
    border-radius: var(--border-radius);
    padding: 5px;
    margin-bottom: 25px;
    box-shadow: var(--box-shadow);
    overflow-x: auto;
  }

  .filter-tab {
    padding: 12px 20px;
    border-radius: 8px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    white-space: nowrap;
    font-weight: 500;
  }

  .filter-tab i {
    margin-right: 8px;
  }

  .filter-tab.active {
    background-color: var(--secondary-color);
    color: white;
  }

  .filter-tab:not(.active):hover {
    background-color: rgba(0, 126, 0, 0.1);
  }

  .stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }

  .stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 20px;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    text-align: center;
  }

  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(0, 126, 0, 0.15);
  }

  .stat-icon {
    font-size: 2rem;
    margin-bottom: 15px;
    color: var(--primary-color);
  }

  .stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 5px;
    color: var(--dark-color);
  }

  .stat-label {
    color: var(--gray-color);
    font-weight: 500;
  }

  .dashboard-card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    margin-bottom: 30px;
    overflow: hidden;
    background-color: var(--light-color);
  }

  .dashboard-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(0, 126, 0, 0.15);
  }

  .card-header {
    background-color: var(--secondary-color);
    color: white;
    padding: 18px 25px;
    font-weight: 600;
    border-bottom: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
  }

  .card-header h5 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
  }

  .card-header i {
    font-size: 1.3rem;
    margin-right: 12px;
  }

  .search-box {
    position: relative;
    margin-bottom: 20px;
  }

  .search-box input {
    padding-left: 40px;
    border-radius: 20px;
    border: 1px solid #e0e8ff;
    box-shadow: none;
    height: 40px;
    transition: var(--transition);
  }

  .search-box input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 136, 0, 0.1);
  }

  .search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-color);
  }

  .table-responsive {
    border-radius: var(--border-radius);
    overflow: hidden;
  }

  .table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
  }

  .table thead th {
    background-color: rgba(0, 126, 0, 0.05);
    color: var(--secondary-color);
    font-weight: 600;
    border: none;
    padding: 15px;
    vertical-align: middle;
    border-bottom: 2px solid rgba(0, 126, 0, 0.1);
  }

  .table tbody tr {
    transition: var(--transition);
  }

  .table tbody tr:hover {
    background-color: rgba(0, 126, 0, 0.03);
  }

  .table tbody td {
    padding: 15px;
    vertical-align: middle;
    border-top: 1px solid rgba(0, 126, 0, 0.05);
  }

  .badge-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
  }

  .badge-completed {
    background-color: rgba(0, 126, 0, 0.1);
    color: var(--secondary-color);
  }

  .btn-action {
    background-color: var(--primary-color);
    border: none;
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: var(--transition);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .btn-action:hover {
    background-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 136, 0, 0.2);
    color: white;
  }

  .btn-secondary {
    background-color: var(--secondary-color);
  }

  .btn-secondary:hover {
    background-color: var(--secondary-dark);
    box-shadow: 0 4px 8px rgba(0, 126, 0, 0.2);
  }

  .btn-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 3px;
  }

  .empty-state {
    text-align: center;
    padding: 40px 0;
    color: var(--gray-color);
  }

  .empty-state i {
    font-size: 50px;
    margin-bottom: 15px;
    color: #dee2e6;
  }

  .empty-state h5 {
    font-weight: 500;
    color: var(--gray-color);
  }

  .user-info {
    display: flex;
    align-items: center;
  }

  .user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 10px;
  }

  .user-details {
    line-height: 1.2;
  }

  .user-name {
    font-weight: 600;
    color: var(--dark-color);
  }

  .user-email {
    font-size: 0.85rem;
    color: var(--gray-color);
  }

  .pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
  }

  .pagination .page-item .page-link {
    color: var(--secondary-color);
    border: 1px solid #dee2e6;
    padding: 8px 16px;
    border-radius: 20px;
    margin: 0 3px;
  }

  .pagination .page-item.active .page-link {
    background-color: var(--secondary-color);
    border-color: var(--secondary-color);
    color: white;
  }

  .pagination .page-item.disabled .page-link {
    color: #6c757d;
  }

  .export-btn {
    background-color: white;
    color: var(--secondary-color);
    border: 1px solid var(--secondary-color);
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
  }

  .export-btn:hover {
    background-color: var(--secondary-color);
    color: white;
  }

  @media (max-width: 768px) {
    .dashboard-container {
      padding: 15px;
    }
    
    .page-title {
      flex-direction: column;
      align-items: flex-start;
    }
    
    .filter-tabs {
      flex-direction: column;
    }
    
    .filter-tab {
      justify-content: center;
    }
    
    .card-header {
      padding: 15px;
      flex-direction: column;
      align-items: flex-start;
    }
    
    .card-header h5 {
      font-size: 1.1rem;
    }
    
    .table thead {
      display: none;
    }
    
    .table tbody tr {
      display: block;
      margin-bottom: 15px;
      border: 1px solid rgba(0, 126, 0, 0.1);
      border-radius: var(--border-radius);
      padding: 10px;
    }
    
    .table tbody td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 15px;
      border: none;
      border-bottom: 1px solid rgba(0, 126, 0, 0.05);
    }
    
    .table tbody td::before {
      content: attr(data-label);
      font-weight: 600;
      color: var(--secondary-color);
      margin-right: 15px;
    }
    
    .table tbody td:last-child {
      border-bottom: none;
      justify-content: center;
    }
    
    .user-info {
      justify-content: space-between;
      width: 100%;
    }
  }
</style>

<div class="dashboard-container">
  <div class="page-title">
    <h2>
      <i class="{{ $icon }} me-2"></i>{{ $title }}
    </h2>
    <div class="page-actions">
      <a href="{{ route('agent.dashboard') }}" class="btn-action">
        <i class="fas fa-arrow-left me-1"></i>Retour
      </a>
    </div>
  </div>

  <!-- Filtres par type -->
  <div class="filter-tabs">
    <a href="{{ route('etat_civil.history.taskend', ['type' => 'naissance']) }}" 
       class="filter-tab {{ $type == 'naissance' ? 'active' : '' }}">
      <i class="fas fa-baby"></i>Naissances
    </a>
    <a href="{{ route('etat_civil.history.taskend', ['type' => 'deces']) }}" 
       class="filter-tab {{ $type == 'deces' ? 'active' : '' }}">
      <i class="fas fa-cross"></i>Décès
    </a>
    <a href="{{ route('etat_civil.history.taskend', ['type' => 'mariage']) }}" 
       class="filter-tab {{ $type == 'mariage' ? 'active' : '' }}">
      <i class="fas fa-ring"></i>Mariages
    </a>
  </div>

  <!-- Tableau des demandes terminées -->
  <div class="dashboard-card">
    <div class="card-header">
      <h5><i class="fas fa-history me-2"></i>Historique des demandes terminées</h5>
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th style="text-align: center">Code Livraison</th>
              <th style="text-align: center">Référence</th>
              <th style="text-align: center">Demandeur</th>
              <th style="text-align: center">Type</th>
              <th style="text-align: center">Date de demande</th>
              <th style="text-align: center">Date de traitement</th>
              <th style="text-align: center">Statut</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($tasks as $task)
              <tr>
                <td style="text-align: center" data-label="Référence"><strong>{{ $task->livraison_code == null ? 'Retrait sur place' : $task->livraison_code }}</strong></td>
                <td style="text-align: center" data-label="Référence"><strong>{{ $task->reference }}</strong></td>
                <td style="text-align: center" data-label="Demandeur">
                  <div class="user-info" style="display: flex; justify-content:center">
                    <div class="user-avatar">
                      {{ substr($task->user->name ?? 'N', 0, 1) }}
                    </div>
                    <div class="user-details">
                      <div class="user-name">{{ $task->user->name ?? 'N/A' }}</div>
                      <div class="user-email">{{ $task->user->email ?? 'N/A' }}</div>
                    </div>
                  </div>
                </td>
                <td style="text-align: center" data-label="Type">
                  @if($type == 'naissance')
                    Extrait naissance
                  @elseif($type == 'deces')
                    Extrait décès
                  @else
                    Extrait mariage
                  @endif
                </td>
                <td style="text-align: center" data-label="Date demande">{{ $task->created_at->format('d/m/Y') }}</td>
                <td style="text-align: center" data-label="Date traitement">{{ $task->updated_at->format('d/m/Y') }}</td>
                <td style="text-align: center" data-label="Statut">
                  <span class="badge-status badge-completed">Terminé</span>
                </td>
              </tr>
            @empty
              <tr>
                <td style="text-align: center" colspan="7" class="empty-state">
                  <i class="fas fa-inbox"></i>
                  <h5>Aucune demande terminée</h5>
                  <p>Vous n'avez aucune demande de ce type marquée comme terminée.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if($tasks->hasPages())
        <div class="pagination-container">
          {{ $tasks->links() }}
        </div>
      @endif
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    // Recherche dans le tableau
    $('#searchInput').on('keyup', function() {
      const value = $(this).val().toLowerCase();
      $('table tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
      });
    });

    // Adaptation pour mobile
    function adaptForMobile() {
      if (window.innerWidth <= 768px) {
        // Ajout des data-labels pour l'affichage mobile
        $('table thead th').each(function() {
          const headerText = $(this).text();
          const columnIndex = $(this).index();
          $('table tbody tr td:nth-child(' + (columnIndex + 1) + ')').attr('data-label', headerText);
        });
      }
    }

    // Exécuter au chargement et lors du redimensionnement
    adaptForMobile();
    $(window).resize(adaptForMobile);
  });
</script>
@endsection