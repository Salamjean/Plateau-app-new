@extends('agent.layouts.template')
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
    <a href="{{ route('agent.history.taskend', ['type' => 'naissance']) }}" 
       class="filter-tab {{ $type == 'naissance' ? 'active' : '' }}">
      <i class="fas fa-baby"></i>Acte Naissance
    </a>
    <a href="{{ route('agent.history.taskend', ['type' => 'deces']) }}" 
       class="filter-tab {{ $type == 'deces' ? 'active' : '' }}">
      <i class="fas fa-cross"></i>Acte Décès
    </a>
    <a href="{{ route('agent.history.taskend', ['type' => 'mariage']) }}" 
       class="filter-tab {{ $type == 'mariage' ? 'active' : '' }}">
      <i class="fas fa-ring"></i>Acte Mariage
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
                    Acte naissance
                  @elseif($type == 'deces')
                    Acte décès
                  @else
                    Acte mariage
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
                <td style="text-align: center" colspan="8" class="empty-state">
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

  // Fonction pour afficher tous les détails de la demande
  function showRequestDetails(taskOrElement, type) {
      let task = taskOrElement;
      if (taskOrElement && (taskOrElement.dataset || (taskOrElement.getAttribute && taskOrElement.getAttribute('data-task')))) {
          const raw = taskOrElement.dataset.task || taskOrElement.getAttribute('data-task');
          if (raw) {
              try {
                  task = typeof raw === 'string' ? JSON.parse(raw) : raw;
              } catch(e) {
                  console.error("Error parsing task JSON:", e);
              }
          }
      } else if (typeof taskOrElement === 'string') {
          try {
              task = JSON.parse(taskOrElement);
          } catch(e) {}
      }
      const user = (task && task.user) || {};
      
      const statusMap = {
          'en attente': { color: '#f59e0b', bg: '#fffbeb', border: '#fde68a', icon: 'fa-clock', label: 'En attente' },
          'réçu': { color: '#3b82f6', bg: '#eff6ff', border: '#bfdbfe', icon: 'fa-spinner', label: 'Reçu' },
          'traité': { color: '#10b981', bg: '#ecfdf5', border: '#a7f3d0', icon: 'fa-check-circle', label: 'Traité' },
          'rejeté': { color: '#ef4444', bg: '#fef2f2', border: '#fecaca', icon: 'fa-times-circle', label: 'Rejeté' },
          'terminé': { color: '#10b981', bg: '#ecfdf5', border: '#a7f3d0', icon: 'fa-check-circle', label: 'Terminé' },
      };
      const status = statusMap[task.etat] || {
          color: '#10b981',
          bg: '#ecfdf5',
          border: '#a7f3d0',
          icon: 'fa-check-circle',
          label: task.etat || 'Terminé'
      };

      let documentType = '';
      let detailsTitle = '';
      let heroIcon = '';
      let infoSectionHeadIcon = '';
      let infoSectionTitle = '';
      let infoSectionContent = '';
      let formatDocuments = null;

      if (type === 'naissance') {
          documentType = task.type === 'simple' ? 'Copie Simple' : (task.type === 'groupee' ? 'Simple + Intégral' : 'Copie Intégrale');
          detailsTitle = "Demande d'Acte de Naissance";
          heroIcon = "fa-baby";
          infoSectionHeadIcon = "fa-baby";
          infoSectionTitle = "Informations de l'Enfant";
          
          infoSectionContent = `
              <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Nom</span><span class="dp-value">${task.name||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Prénom</span><span class="dp-value">${task.prenom||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-hashtag"></i> N° Registre</span><span class="dp-value">${task.number||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-calendar"></i> Date Reg.</span><span class="dp-value">${task.DateR||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-map-pin"></i> Commune</span><span class="dp-value">${task.commune||'--'}</span></div>
          `;

          formatDocuments = (naissance) => {
              const docs = [];
              if (naissance.CNI) {
                  const p = '{{ asset('storage/') }}/' + naissance.CNI;
                  docs.push({
                      label: "Pièce d'identité",
                      path: p,
                      isPdf: p.toLowerCase().endsWith('.pdf')
                  });
              }
              if (!docs.length)
                  return `<div style="text-align:center;padding:24px;color:#94a3b8;"><i class="fas fa-folder-open" style="font-size:2rem;margin-bottom:8px;display:block;"></i><p style="margin:0;font-size:0.85rem;">Aucun document joint</p></div>`;
              return docs.map(d => `
                  <div style="display:flex;align-items:center;gap:14px;padding:10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:10px;">
                      <div style="width:60px;height:60px;border-radius:8px;overflow:hidden;background:white;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                          ${d.isPdf ? '<i class="fas fa-file-pdf" style="color:#ef4444;font-size:1.8rem;"></i>' : `<img src="${d.path}" onclick="openImageModal('${d.path}')" style="width:100%;height:100%;object-fit:cover;cursor:pointer;" alt="${d.label}">`}
                      </div>
                      <div style="flex:1;">
                          <div style="font-weight:600;font-size:0.85rem;color:#0f172a;margin-bottom:6px;">${d.label}</div>
                          <div style="display:flex;gap:8px;">
                              ${!d.isPdf ? `<a href="javascript:void(0)" onclick="openImageModal('${d.path}')" style="color:#1977cc;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #bfdbfe;background:white;"><i class="fas fa-eye"></i> Aperçu</a>` : `<a href="${d.path}" target="_blank" style="color:#1977cc;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #bfdbfe;background:white;"><i class="fas fa-external-link-alt"></i> Ouvrir</a>`}
                              <a href="${d.path}" download style="color:#475569;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #e2e8f0;background:white;"><i class="fas fa-download"></i> Télécharger</a>
                          </div>
                      </div>
                  </div>
              `).join('');
          };

      } else if (type === 'deces') {
          documentType = task.type === 'simple' ? 'Copie Simple' : (task.type === 'simpleIntegrale' || task.type === 'groupee' ? 'Simple + Intégral' : 'Copie Intégrale');
          detailsTitle = "Demande d'Acte de Décès";
          heroIcon = "fa-cross";
          infoSectionHeadIcon = "fa-cross";
          infoSectionTitle = "Informations du Défunt";
          
          infoSectionContent = `
              <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Nom</span><span class="dp-value">${task.name||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Prénom</span><span class="dp-value">${task.prenom||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-hashtag"></i> N° Registre</span><span class="dp-value">${task.numberR||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-calendar"></i> Date Reg.</span><span class="dp-value">${task.dateR||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-map-pin"></i> Commune</span><span class="dp-value">${task.commune||'--'}</span></div>
          `;

          formatDocuments = (dece) => {
              const docs = [];
              if (dece.CNIdfnt) {
                  const p = '{{ asset('storage/') }}/' + dece.CNIdfnt;
                  docs.push({ label: 'CNI Défunt', path: p, isPdf: p.toLowerCase().endsWith('.pdf') });
              }
              if (dece.CNIdcl) {
                  const p = '{{ asset('storage/') }}/' + dece.CNIdcl;
                  docs.push({ label: 'CNI Déclarant', path: p, isPdf: p.toLowerCase().endsWith('.pdf') });
              }
              if (dece.documentMariage) {
                  const p = '{{ asset('storage/') }}/' + dece.documentMariage;
                  docs.push({ label: 'Document Mariage', path: p, isPdf: p.toLowerCase().endsWith('.pdf') });
              }
              if (dece.RequisPolice) {
                  const p = '{{ asset('storage/') }}/' + dece.RequisPolice;
                  docs.push({ label: 'Réquis Police', path: p, isPdf: p.toLowerCase().endsWith('.pdf') });
              }
              if (!docs.length)
                  return `<div style="text-align:center;padding:24px;color:#94a3b8;"><i class="fas fa-folder-open" style="font-size:2rem;margin-bottom:8px;display:block;"></i><p style="margin:0;font-size:0.85rem;">Aucun document joint</p></div>`;
              return docs.map(d => `
                  <div style="display:flex;align-items:center;gap:14px;padding:10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:10px;">
                      <div style="width:60px;height:60px;border-radius:8px;overflow:hidden;background:white;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                          ${d.isPdf ? '<i class="fas fa-file-pdf" style="color:#ef4444;font-size:1.8rem;"></i>' : `<img src="${d.path}" onclick="openImageModal('${d.path}')" style="width:100%;height:100%;object-fit:cover;cursor:pointer;" alt="${d.label}">`}
                      </div>
                      <div style="flex:1;">
                          <div style="font-weight:600;font-size:0.85rem;color:#0f172a;margin-bottom:6px;">${d.label}</div>
                          <div style="display:flex;gap:8px;">
                              ${!d.isPdf ? `<a href="javascript:void(0)" onclick="openImageModal('${d.path}')" style="color:#1977cc;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #bfdbfe;background:white;"><i class="fas fa-eye"></i> Aperçu</a>` : `<a href="${d.path}" target="_blank" style="color:#1977cc;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #bfdbfe;background:white;"><i class="fas fa-external-link-alt"></i> Ouvrir</a>`}
                              <a href="${d.path}" download style="color:#475569;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #e2e8f0;background:white;"><i class="fas fa-download"></i> Télécharger</a>
                          </div>
                      </div>
                  </div>
              `).join('');
          };

      } else if (type === 'mariage') {
          const isCopieSimple = task.nomEpoux === null;
          documentType = isCopieSimple ? 'Copie Simple' : 'Extrait Complet';
          detailsTitle = "Demande d'Extrait de Mariage";
          heroIcon = "fa-heart";
          infoSectionHeadIcon = isCopieSimple ? "fa-file-alt" : "fa-venus-mars";
          infoSectionTitle = isCopieSimple ? "Informations Générales" : "Informations des Conjoints";
          
          infoSectionContent = isCopieSimple ? `
              <div class="dp-row"><span class="dp-label"><i class="fas fa-tag"></i> Type</span><span class="dp-value" style="color:#1977cc;font-weight:700;">Copie Simple</span></div>
          ` : `
              <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Nom Époux</span><span class="dp-value">${task.nomEpoux||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Prénom Époux</span><span class="dp-value">${task.prenomEpoux||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-calendar"></i> Naiss. Époux</span><span class="dp-value">${task.dateNaissanceEpoux||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-map-pin"></i> Lieu naiss.</span><span class="dp-value">${task.lieuNaissanceEpoux||'--'}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-map-marker-alt"></i> Commune</span><span class="dp-value">${task.commune||'--'}</span></div>
          `;

          formatDocuments = (mariage) => {
              const docs = [];
              if (mariage.pieceIdentite) {
                  const p = '{{ asset('storage/') }}/' + mariage.pieceIdentite;
                  docs.push({ label: "Pièce d'identité", path: p, isPdf: p.toLowerCase().endsWith('.pdf') });
              }
              if (mariage.extraitMariage) {
                  const p = '{{ asset('storage/') }}/' + mariage.extraitMariage;
                  docs.push({ label: 'Extrait de mariage', path: p, isPdf: p.toLowerCase().endsWith('.pdf') });
              }
              if (!docs.length)
                  return `<div style="text-align:center;padding:24px;color:#94a3b8;"><i class="fas fa-folder-open" style="font-size:2rem;margin-bottom:8px;display:block;"></i><p style="margin:0;font-size:0.85rem;">Aucun document joint</p></div>`;
              return docs.map(d => `
                  <div style="display:flex;align-items:center;gap:14px;padding:10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;margin-bottom:10px;">
                      <div style="width:60px;height:60px;border-radius:8px;overflow:hidden;background:white;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                          ${d.isPdf ? '<i class="fas fa-file-pdf" style="color:#ef4444;font-size:1.8rem;"></i>' : `<img src="${d.path}" onclick="openImageModal('${d.path}')" style="width:100%;height:100%;object-fit:cover;cursor:pointer;" alt="${d.label}">`}
                      </div>
                      <div style="flex:1;">
                          <div style="font-weight:600;font-size:0.85rem;color:#0f172a;margin-bottom:6px;">${d.label}</div>
                          <div style="display:flex;gap:8px;">
                              ${!d.isPdf ? `<a href="javascript:void(0)" onclick="openImageModal('${d.path}')" style="color:#1977cc;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #bfdbfe;background:white;"><i class="fas fa-eye"></i> Aperçu</a>` : `<a href="${d.path}" target="_blank" style="color:#1977cc;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #bfdbfe;background:white;"><i class="fas fa-external-link-alt"></i> Ouvrir</a>`}
                              <a href="${d.path}" download style="color:#475569;font-size:0.78rem;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:5px;border:1px solid #e2e8f0;background:white;"><i class="fas fa-download"></i> Télécharger</a>
                          </div>
                      </div>
                  </div>
              `).join('');
          };
      }

      const htmlContent = `
        <div class="dp-wrap">
          <div class="dp-hero">
            <div class="dp-status-pill"><i class="fas ${status.icon}"></i> ${status.label}</div>
            <div class="dp-hero-icon"><i class="fas ${heroIcon}"></i></div>
            <div class="dp-hero-title">${detailsTitle}</div>
            <div class="dp-hero-meta">
              <span><i class="fas fa-hashtag"></i> #${task.id}</span>
              <span style="opacity:.4">|</span>
              <span><i class="fas fa-calendar-alt"></i> ${new Date(task.created_at).toLocaleDateString('fr-FR',{day:'2-digit',month:'short',year:'numeric'})}</span>
              <span style="opacity:.4">|</span>
              <span><i class="fas fa-file-alt"></i> ${documentType}</span>
            </div>
          </div>
          <div class="dp-tabs" id="dpTabs">
            <div class="dp-tab dp-active" data-panel="dpP-infos"><i class="fas fa-info-circle"></i> Informations</div>
            <div class="dp-tab" data-panel="dpP-livraison"><i class="fas fa-${task.choix_option === 'livraison' ? 'truck' : 'store'}"></i> ${task.choix_option === 'livraison' ? 'Livraison' : 'Retrait'}</div>
            <div class="dp-tab" data-panel="dpP-docs"><i class="fas fa-paperclip"></i> Documents</div>
          </div>
          <div class="dp-panel dp-active" id="dpP-infos">
            ${task.motif_de_rejet ? `<div class="dp-alert"><div class="dp-alert-icon"><i class="fas fa-exclamation-triangle"></i></div><div><div class="dp-alert-title">Demande rejetée</div><div class="dp-alert-text">${task.motif_de_rejet}</div></div></div>` : ''}
            <div class="dp-grid">
              <div class="dp-section">
                <div class="dp-section-head"><div class="dp-section-icon"><i class="fas ${infoSectionHeadIcon}"></i></div><div class="dp-section-title">${infoSectionTitle}</div></div>
                ${infoSectionContent}
              </div>
              <div class="dp-section">
                <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-user-circle"></i></div><div class="dp-section-title">Demandeur</div></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Nom</span><span class="dp-value">${(user.name||'')+' '+(user.prenom||'')}</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-envelope"></i> Email</span><span class="dp-value">${user.email||'--'}</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-phone"></i> Contact</span><span class="dp-value">${user.contact||'--'}</span></div>
                <div class="dp-row"><span class="dp-label"><i class="fas fa-clock"></i> Date</span><span class="dp-value">${new Date(task.created_at).toLocaleString('fr-FR')}</span></div>
              </div>
            </div>
            <div class="dp-section">
              <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-file-invoice"></i></div><div class="dp-section-title">Détails de la Commande</div></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-copy"></i> Quantité</span><span class="dp-value">${task.quantite} copie(s)${(task.qty_simple>0&&task.qty_integral>0)?` <small style="color:#64748b;font-weight:400;">(${task.qty_simple||0}s + ${task.qty_integral||0}i)</small>`:''}</span></div>
              <div class="dp-row"><span class="dp-label"><i class="fas fa-circle"></i> Statut</span><span class="dp-value"><span class="dp-badge" style="background:${status.bg};color:${status.color};border:1px solid ${status.border};"><i class="fas ${status.icon}"></i> ${status.label}</span></span></div>
            </div>
          </div>
          <div class="dp-panel" id="dpP-livraison">
            ${task.choix_option === 'livraison' ? `
                <div class="dp-section">
                  <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-truck"></i></div><div class="dp-section-title">Informations de Livraison</div></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-user"></i> Destinataire</span><span class="dp-value">${task.nom_destinataire||'--'} ${task.prenom_destinataire||''}</span></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-phone"></i> Contact</span><span class="dp-value">${task.contact_destinataire||'--'}</span></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-envelope"></i> Email</span><span class="dp-value">${task.email_destinataire||'--'}</span></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-map-marker-alt"></i> Adresse</span><span class="dp-value">${task.adresse_livraison||'--'}</span></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-city"></i> Ville</span><span class="dp-value">${task.ville||'--'}</span></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-map"></i> Commune</span><span class="dp-value">${task.commune_livraison||'--'}</span></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-home"></i> Quartier</span><span class="dp-value">${task.quartier||'--'}</span></div>
                  <div class="dp-row"><span class="dp-label"><i class="fas fa-mail-bulk"></i> Code postal</span><span class="dp-value">${task.code_postal||'--'}</span></div>
                </div>
                ` : `<div style="text-align:center;padding:36px 20px;"><div class="dp-pickup"><i class="fas fa-store"></i> Retrait sur place</div><p style="margin-top:12px;color:#64748b;font-size:0.82rem;">Le demandeur récupérera son document directement à la mairie.</p></div>`}
          </div>
          <div class="dp-panel" id="dpP-docs">
            <div class="dp-section">
              <div class="dp-section-head"><div class="dp-section-icon"><i class="fas fa-paperclip"></i></div><div class="dp-section-title">Documents Joints</div></div>
              <div style="padding:12px;">${formatDocuments(task)}</div>
            </div>
          </div>
        </div>
      `;

      Swal.fire({
          html: htmlContent,
          width: '860px',
          confirmButtonText: '<i class="fas fa-times"></i> Fermer',
          confirmButtonColor: '#1977cc',
          showCloseButton: true,
          padding: 0,
          customClass: {
              popup: 'request-details-popup'
          },
          didOpen: () => {
              var tabs = document.querySelectorAll('#dpTabs .dp-tab');
              tabs.forEach(function(t) {
                  t.addEventListener('click', function() {
                      tabs.forEach(function(x) { x.classList.remove('dp-active'); });
                      document.querySelectorAll('#dpTabs ~ .dp-panel').forEach(function(p) { p.classList.remove('dp-active'); });
                      t.classList.add('dp-active');
                      var panel = document.getElementById(t.dataset.panel);
                      if (panel) panel.classList.add('dp-active');
                  });
              });
          }
      });
  }

  // Fonction pour ouvrir une image en grand dans une modal
  function openImageModal(imageSrc) {
      const htmlContent = `
          <div style="text-align:center;">
            <div style="border-radius:12px;overflow:hidden;background:#000;display:inline-block;max-width:100%;box-shadow:0 8px 32px rgba(0,0,0,0.25);">
              <img src="${imageSrc}" style="max-width:100%;max-height:65vh;display:block;" alt="Document">
            </div>
            <div style="margin-top:16px;display:flex;justify-content:center;gap:10px;flex-wrap:wrap;">
              <a href="${imageSrc}" download style="color:#1977cc;text-decoration:none;display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border:1px solid #bfdbfe;border-radius:8px;background:#eff6ff;font-size:0.85rem;font-weight:600;">
                <i class="fas fa-download"></i> Télécharger
              </a>
              <button onclick="Swal.close()" style="color:#475569;display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border:1px solid #e2e8f0;border-radius:8px;background:white;font-size:0.85rem;font-weight:600;cursor:pointer;">
                <i class="fas fa-times"></i> Fermer
              </button>
            </div>
          </div>
      `;
      Swal.fire({
          title: '<span style="font-size:1rem;color:#0f172a;">Visualisation du document</span>',
          html: htmlContent,
          width: '800px',
          showConfirmButton: false,
          showCloseButton: true,
          customClass: {
              popup: 'image-modal-popup'
          }
      });
  }
</script>
<style>
    .dp-wrap{font-family:'Inter','Segoe UI',system-ui,sans-serif;color:#1e293b;}
    .dp-hero{background:linear-gradient(135deg,#1977cc 0%,#0d47a1 100%);padding:24px 28px 20px;position:relative;overflow:hidden;}
    .dp-hero::before{content:'';position:absolute;top:-50px;right:-50px;width:160px;height:160px;background:rgba(255,255,255,0.06);border-radius:50%;}
    .dp-hero::after{content:'';position:absolute;bottom:-40px;left:-30px;width:120px;height:120px;background:rgba(255,255,255,0.04);border-radius:50%;}
    .dp-hero-icon{width:48px;height:48px;background:rgba(255,255,255,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:12px;font-size:1.3rem;color:white;position:relative;z-index:1;backdrop-filter:blur(4px);}
    .dp-hero-title{color:white;font-size:1.25rem;font-weight:800;margin:0 0 6px;position:relative;z-index:1;}
    .dp-hero-meta{color:rgba(255,255,255,0.75);font-size:0.78rem;margin:0;display:flex;align-items:center;gap:10px;flex-wrap:wrap;position:relative;z-index:1;}
    .dp-status-pill{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:999px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;background:rgba(255,255,255,0.2);color:white;border:1px solid rgba(255,255,255,0.3);position:absolute;top:18px;right:18px;z-index:2;backdrop-filter:blur(4px);}
    .dp-tabs{display:flex;background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:0 16px;}
    .dp-tab{padding:11px 16px;font-size:0.8rem;font-weight:600;color:#64748b;cursor:pointer;border-bottom:2px solid transparent;transition:all .2s;display:flex;align-items:center;gap:6px;user-select:none;white-space:nowrap;}
    .dp-tab:hover{color:#1977cc;}
    .dp-tab.dp-active{color:#1977cc;border-bottom-color:#1977cc;}
    .dp-panel{display:none;padding:16px;}
    .dp-panel.dp-active{display:block;}
    .dp-section{background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;margin-bottom:14px;}
    .dp-section-head{display:flex;align-items:center;gap:10px;padding:11px 16px;background:#f8fafc;border-bottom:1px solid #e2e8f0;}
    .dp-section-icon{width:30px;height:30px;border-radius:7px;background:linear-gradient(135deg,#1977cc,#0d47a1);display:flex;align-items:center;justify-content:center;color:white;font-size:0.8rem;flex-shrink:0;}
    .dp-section-title{font-weight:700;font-size:0.88rem;color:#0f172a;}
    .dp-row{display:flex;justify-content:space-between;align-items:flex-start;padding:8px 16px;gap:12px;transition:background .15s;}
    .dp-row:hover{background:#f8fafc;}
    .dp-label{color:#64748b;font-size:0.8rem;font-weight:500;min-width:120px;display:flex;align-items:center;gap:6px;flex-shrink:0;}
    .dp-label i{width:13px;color:#94a3b8;font-size:0.75rem;}
    .dp-value{color:#0f172a;font-size:0.8rem;font-weight:600;text-align:right;word-break:break-word;}
    .dp-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:0.72rem;font-weight:700;text-transform:uppercase;}
    .dp-alert{background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 14px;margin-bottom:14px;display:flex;gap:10px;}
    .dp-alert-icon{color:#ef4444;font-size:1rem;margin-top:2px;flex-shrink:0;}
    .dp-alert-title{color:#991b1b;font-weight:700;font-size:0.8rem;margin-bottom:2px;}
    .dp-alert-text{color:#b91c1c;font-size:0.78rem;}
    .dp-pickup{display:inline-flex;align-items:center;gap:8px;background:#eff6ff;color:#1d4ed8;padding:10px 16px;border-radius:10px;font-weight:600;font-size:0.85rem;border:1px solid #bfdbfe;}
    .dp-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
    @media(max-width:600px){.dp-grid{grid-template-columns:1fr}.dp-label{min-width:90px}}
    .request-details-popup{padding:0!important;border-radius:16px!important;overflow:hidden!important;}
</style>
@endsection