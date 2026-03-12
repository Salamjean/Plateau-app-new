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
  /* Fix pagination - alignement horizontal */
.pagination {
  display: flex !important;
  flex-direction: row !important;
  flex-wrap: wrap;
  align-items: center;
  list-style: none !important;
  padding-left: 0 !important;
  margin: 0 !important;
  gap: 4px;
}

.pagination .page-item {
  list-style: none !important;
  display: inline-block;
}

.pagination .page-link {
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 36px;
  height: 36px;
  padding: 0 10px;
  border-radius: 50% !important;
  border: 1px solid #dee2e6;
  color: var(--secondary-color);
  background-color: white;
  font-size: 0.85rem;
  transition: var(--transition);
  text-decoration: none;
  cursor: pointer;
}

.pagination .page-item.active .page-link {
  background-color: var(--secondary-color);
  border-color: var(--secondary-color);
  color: white;
}

.pagination .page-item.disabled .page-link {
  color: #adb5bd;
  pointer-events: none;
  background-color: #f8f9fa;
}

.pagination .page-link:hover {
  background-color: var(--secondary-color);
  border-color: var(--secondary-color);
  color: white;
}
/* Ajoute ceci dans le bloc <style> de chaque page */
.pagination .page-link {
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 38px;
  height: 38px;
  padding: 0 12px;
  border-radius: 20px !important;
  margin: 0 3px;
  border: 1px solid #dee2e6;
  color: var(--secondary-color);
  background-color: white;
  font-size: 0.85rem;
  transition: var(--transition);
}

.pagination .page-item.active .page-link {
  background-color: var(--secondary-color);
  border-color: var(--secondary-color);
  color: white;
}

.pagination .page-item.disabled .page-link {
  color: #adb5bd;
  pointer-events: none;
  background-color: #f8f9fa;
}

.pagination .page-link:hover:not(.disabled) {
  background-color: var(--secondary-color);
  border-color: var(--secondary-color);
  color: white;
}
  body {
    background-color: var(--light-gray);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
  }

  .dashboard-container {
    padding: 30px;
    max-width: 100%;
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

  .stats-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 20px;
    box-shadow: var(--box-shadow);
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    border-left: 5px solid var(--primary-color);
  }

  .stats-icon {
    font-size: 2rem;
    margin-right: 15px;
    color: var(--primary-color);
    background: rgba(255, 136, 0, 0.1);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .stats-content h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    color: var(--dark-color);
  }

  .stats-content p {
    margin: 0;
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
    overflow-x: auto;
  }

  .table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.85rem;
    min-width: 1100px;
  }

  .table thead th {
    background-color: rgba(0, 126, 0, 0.05);
    color: var(--secondary-color);
    font-weight: 600;
    border: none;
    padding: 10px 8px;
    vertical-align: middle;
    border-bottom: 2px solid rgba(0, 126, 0, 0.1);
    white-space: nowrap;
    font-size: 0.8rem;
  }

  .table tbody tr {
    transition: var(--transition);
  }

  .table tbody tr:hover {
    background-color: rgba(0, 126, 0, 0.03);
  }

  .table tbody td {
    padding: 10px 8px;
    vertical-align: middle;
    border-top: 1px solid rgba(0, 126, 0, 0.05);
    font-size: 0.8rem;
  }

  .badge-status {
    padding: 4px 8px;
    border-radius: 15px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
  }

  .badge-pending {
    background-color: #fff3cd;
    color: #856404;
  }

  .badge-progress {
    background-color: #cce5ff;
    color: #004085;
  }

  .badge-completed {
    background-color: rgba(0, 126, 0, 0.1);
    color: var(--secondary-color);
  }

  .btn-action {
    background-color: var(--primary-color);
    border: none;
    border-radius: 15px;
    padding: 5px 10px;
    font-size: 0.75rem;
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

  .btn-action:active {
    transform: translateY(0);
  }

  .btn-secondary {
    background-color: var(--secondary-color);
  }

  .btn-secondary:hover {
    background-color: var(--secondary-dark);
    box-shadow: 0 4px 8px rgba(0, 126, 0, 0.2);
  }

  .btn-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 2px;
    padding: 0;
  }
  
  .badge{
    padding: 4px 8px;
    color: white;
    border-radius: 50px;
    background-color: #1977cc;
    font-size: 0.7rem;
    white-space: nowrap;
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
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.7rem;
    margin-right: 8px;
    flex-shrink: 0;
  }
  .user-avatar1 {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: var(--secondary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.7rem;
    margin-right: 8px;
    flex-shrink: 0;
  }

  .user-details {
    line-height: 1.1;
  }

  .user-name {
    font-weight: 600;
    color: var(--dark-color);
    font-size: 0.75rem;
    white-space: nowrap;
  }

  .user-email {
    font-size: 0.7rem;
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
  /* Styles pour le bouton de téléchargement */
  .download-btn {
    background-color: var(--secondary-color);
    color: white;
    border: none;
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: var(--transition);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .download-btn:hover {
    background-color: var(--secondary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 126, 0, 0.2);
    color: white;
  }

  /* Styles pour les aperçus de documents */
  .document-preview {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    border: 1px solid #eee;
    margin: 2px;
  }

  .document-preview:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .document-container {
    display: flex;
    justify-content: center;
    gap: 5px;
    flex-wrap: wrap;
  }

  /* Styles pour la popup de détails */
  .request-details-popup {
    border-radius: 12px;
  }

  .request-details-popup .swal2-html-container {
    max-height: 70vh;
    overflow-y: auto;
  }

  /* Style pour les badges dans la popup */
  .badge-status-popup {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-block;
  }

  .badge-pending-popup {
    background-color: #fff3cd;
    color: #856404;
  }

  .badge-progress-popup {
    background-color: #cce5ff;
    color: #004085;
  }

  .badge-completed-popup {
    background-color: rgba(0, 126, 0, 0.1);
    color: #1977cc;
  }

  /* Styles pour la modal d'image */
  .image-modal-popup {
    border-radius: 12px;
  }

  .image-modal-popup .swal2-close {
    color: #666;
    font-size: 24px;
  }

  @media (max-width: 768px) {
    .dashboard-container {
      padding: 15px;
    }
    
    .page-title {
      flex-direction: column;
      align-items: flex-start;
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
  <!-- Notifications -->
  @if (Session::get('success'))
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Succès',
        text: '{{ Session::get('success') }}',
        confirmButtonColor: '#1977cc',
        background: 'white'
      });
    </script>
  @endif

  @if (Session::get('error'))
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Erreur',
        text: '{{ Session::get('error') }}',
        confirmButtonColor: '#1977cc',
        background: 'white'
      });
    </script>
  @endif

  <div class="page-title">
    <h2>
      <i class="fas fa-ring me-2"></i>Gestion des demandes d'acte de mariage
    </h2>
    <div class="page-actions">
      <a href="{{ route('agent.dashboard') }}" class="btn-action">
        <i class="fas fa-arrow-left me-1"></i>Retour
      </a>
    </div>
  </div>

  <!-- Statistiques -->
  <div class="stats-card">
    <div class="stats-icon">
      <i class="fas fa-tasks"></i>
    </div>
    <div class="stats-content">
      <h3>{{ $mariages->total() }}</h3>
      <p>Demandes d'actes de mariages en cours</p>
    </div>
  </div>

  <!-- Tableau des demandes -->
  <div class="dashboard-card">
    <div class="card-header">
      <h5><i class="fas fa-list me-2"></i>Liste des demandes</h5>
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
              <th style="text-align: center">Infos Livraison</th>
              <th class="text-center">Quantité</th>
              <th class="text-center">Demandeur</th>
              <th class="text-center">Conjoint(e)</th>
              <th class="text-center">Document</th>
              <th class="text-center">Date demande</th>
              <th class="text-center">État</th>
              <th class="text-center">Actions</th>
              <th class="text-center">Retrait</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($mariages as $mariage)
              <tr>
                <td style="text-align: center">
                  @if($mariage->choix_option == 'livraison' && $mariage->etat == 'terminé')
                    <button class="download-btn" onclick="showDeliveryInfo({{ json_encode($mariage) }})">
                      <i class="fas fa-download me-1"></i>Télécharger
                    </button>
                  @else
                    <span class="badge bg-secondary">N/A</span>
                  @endif
                </td>
                <td>
                    <div class="text-center">{{ $mariage->quantite }} copie(s)</div>
                </td>
                <td style="text-align: center" data-label="Demandeur">
                  <div class="user-info" style="text-align: center; display:flex; justify-content:center">
                    <div class="user-avatar" style="text-align: center">
                      {{ substr($mariage->user->name, 0, 1).''.substr($mariage->user->prenom, 0, 1) }}
                    </div>
                    <div class="user-details">
                      <div class="user-name">{{ $mariage->user->name.' '.$mariage->user->prenom ?? '--' }} </div>
                      <div class="user-email">{{ $mariage->user->contact ?? '--' }}</div>
                      <div class="user-email">{{ $mariage->user->email ?? '--' }}</div>
                    </div>
                  </div>
                </td>
                <td style="text-align: center" data-label="Conjoint(e)">
                  <div class="user-info" style="text-align: center; display:flex; justify-content:center">
                    <div class="user-details">
                      <div class="user-name">{{ $mariage->nomEpoux == null ?  'Copie Simple' : $mariage->nomEpoux  }}</div>
                      <div class="user-name">{{ $mariage->prenomEpoux == null ?  'Copie Simple' : $mariage->prenomEpoux }}</div>
                      <div class="user-email">{{ $mariage->dateNaissanceEpoux == null ?  'Copie Simple' : $mariage->dateNaissanceEpoux  }}</div>
                      <div class="user-email">{{ $mariage->lieuNaissanceEpoux == null ?  'Copie Simple' : $mariage->lieuNaissanceEpoux  }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="d-flex justify-content-center gap-2">
                    @if ($mariage->pieceIdentite)
                      @php
                        $piecePath = asset('storage/' . $mariage->pieceIdentite);
                        $isPiecePdf = strtolower(pathinfo($piecePath, PATHINFO_EXTENSION)) === 'pdf';
                      @endphp
                      @if ($isPiecePdf)
                        <a href="{{ $piecePath }}" target="_blank" title="Pièce d'identité (PDF)">
                          <img src="{{ asset('assets/assets/img/pdf.jpg') }}" alt="PDF" class="document-preview">
                        </a>
                      @else
                        <img src="{{ $piecePath }}" 
                          alt="Pièce d'identité" 
                          class="document-preview"
                          onclick="openImageModal('{{ $piecePath }}')"
                          title="Pièce d'identité">
                      @endif
                    @endif

                    @if ($mariage->extraitMariage)
                      @php
                        $extraitPath = asset('storage/' . $mariage->extraitMariage);
                        $isExtraitPdf = strtolower(pathinfo($extraitPath, PATHINFO_EXTENSION)) === 'pdf';
                      @endphp
                      @if ($isExtraitPdf)
                        <a href="{{ $extraitPath }}" target="_blank" title="Extrait (PDF)">
                          <img src="{{ asset('assets/assets/img/pdf.jpg') }}" alt="PDF" class="document-preview">
                        </a>
                      @else
                        <img src="{{ $extraitPath }}" 
                          alt="Extrait de mariage" 
                          class="document-preview"
                          onclick="openImageModal('{{ $extraitPath }}')"
                          title="Extrait de mariage">
                      @endif
                    @endif
                  </div>
                </td>
                <td style="text-align: center" data-label="Date demande">{{ $mariage->created_at->format('d/m/Y H:i') }}</td>
                <td style="text-align: center" data-label="Statut">
                  @if($mariage->etat == 'en attente')
                    <span class="badge-status badge-pending">En attente</span>
                  @elseif($mariage->etat == 'réçu')
                    <span class="badge-status badge-progress">En cours</span>
                  @else
                    <span class="badge-status badge-completed">Terminé</span>
                  @endif
                </td>
                <td style="text-align: center" data-label="Actions">
                  <!-- Bouton pour voir les détails -->
                  <button class="btn-action btn-icon" style="background-color: #17a2b8;" 
                          onclick="showRequestDetails({{ json_encode($mariage) }})" 
                          title="Voir les détails de la demande">
                      <i class="fas fa-eye"></i>
                  </button>
                  
                  @if($mariage->etat === 'terminé')
                    <a href="#" class="btn-action btn-secondary btn-icon disabled" title="Demande terminée" style="opacity: 0.5; pointer-events: none; background-color: #6c757d;">
                      <i class="fas fa-edit"></i>
                    </a>
                  @else
                    <a href="{{ route('agent.demandes.wedding.edit', $mariage->id) }}" class="btn-action btn-secondary btn-icon" title="Modifier l'état de la demande">
                      <i class="fas fa-edit"></i>
                    </a>
                  @endif
                </td>
                <td style="text-align: center">
                    <div class="d-flex justify-content-center gap-2">
                        @if($mariage->choix_option == 'livraison')
                            <a href="#" class="delivery-badge badge" data-bs-toggle="modal" data-bs-target="#livraisonModal" onclick="showLivraisonModal({{ json_encode($mariage) }})">
                                <i class="fas fa-truck"></i> Livraison
                            </a>
                        @else
                            @if($mariage->etat !== 'terminé')
                              <span class="badge"><i class="fas fa-home"></i> Retrait sur place</span>
                            @endif
                            @if($mariage->etat == 'terminé')
                                <button class="btn-action" onclick="markAsDelivered({{ $mariage->id }})" title="Livré l'extrait">
                                    <i class="fas fa-file"></i>Retrait
                                </button>
                            @endif
                        @endif
                    </div>
                </td>
              </tr>
            @empty
              <tr>
                <td style="text-align: center" colspan="9" class="empty-state">
                  <i class="fas fa-cross"></i>
                  <h5>Aucune demande d'extrait de mariage en cours</h5>
                  <p>Toutes les demandes sont traitées ou vous n'avez pas encore récuperer demande.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
       @if($mariages->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de <strong>{{ $mariages->firstItem() }}</strong> à <strong>{{ $mariages->lastItem() }}</strong> sur <strong>{{ $mariages->total() }}</strong> résultats
                </div>
                
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0">
                        <!-- Première page -->
                        @if($mariages->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-angle-double-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $mariages->url(1) }}" aria-label="Première page">
                                    <i class="fas fa-angle-double-left"></i>
                                </a>
                            </li>
                        @endif

                        <!-- Page précédente -->
                        @if($mariages->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $mariages->previousPageUrl() }}" aria-label="Précédent">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        @endif

                        <!-- Pages numérotées -->
                        @php
                            $current = $mariages->currentPage();
                            $last = $mariages->lastPage();
                            $start = max($current - 2, 1);
                            $end = min($current + 2, $last);
                            
                            if($start > 1) {
                                $start = max($current - 1, 1);
                                $end = min($current + 1, $last);
                            }
                            
                            if($end - $start < 2) {
                                $start = max($current - 2, 1);
                                $end = min($current + 2, $last);
                            }
                        @endphp

                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ $mariages->url(1) }}">1</a>
                            </li>
                            @if($start > 2)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++)
                            <li class="page-item {{ $i == $current ? 'active' : '' }}">
                                @if($i == $current)
                                    <span class="page-link">{{ $i }}</span>
                                @else
                                    <a class="page-link" href="{{ $mariages->url($i) }}">{{ $i }}</a>
                                @endif
                            </li>
                        @endfor

                        @if($end < $last)
                            @if($end < $last - 1)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="{{ $mariages->url($last) }}">{{ $last }}</a>
                            </li>
                        @endif

                        <!-- Page suivante -->
                        @if($mariages->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $mariages->nextPageUrl() }}" aria-label="Suivant">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            </li>
                        @endif

                        <!-- Dernière page -->
                        @if($mariages->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $mariages->url($last) }}" aria-label="Dernière page">
                                    <i class="fas fa-angle-double-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">
                                    <i class="fas fa-angle-double-right"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
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
     if (window.innerWidth <= 768) {
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

<script>
    const markAsDeliveredUrl = "{{ route('livraison.mark.mariage', ':id') }}";
    const downloadDeliveryInfoUrl = "{{ route('agent.download.mariage.delivery.info', ':id') }}";

    // Fonction pour afficher tous les détails de la demande
    function showRequestDetails(mariage) {
        // Récupérer les informations de l'utilisateur
        const user = mariage.user || {};
        
        // Formater les documents avec prévisualisation
        const formatDocuments = (mariage) => {
            let documentsHTML = '';
            
            // Pièce d'identité
            if (mariage.pieceIdentite) {
                const piecePath = '{{ asset("storage/") }}/' + mariage.pieceIdentite;
                const isPdf = piecePath.toLowerCase().endsWith('.pdf');
                documentsHTML += `
                    <div style="margin-bottom: 15px; padding: 10px; background: white; border-radius: 5px; border: 1px solid #e0e0e0;">
                        <strong>Pièce d'identité:</strong><br>
                        ${isPdf ? 
                            `<a href="${piecePath}" target="_blank" style="color: #1977cc; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; margin-top: 5px;">
                                <i class="fas fa-file-pdf" style="color: #e74c3c;"></i> Voir le PDF
                            </a>` : 
                            `<div style="margin-top: 5px;">
                                <img src="${piecePath}" style="max-width: 150px; cursor: pointer; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;" 
                                    onclick="openImageModal('${piecePath}')" alt="Pièce d'identité">
                                <br>
                                <a href="javascript:void(0)" onclick="openImageModal('${piecePath}')" style="color: #1977cc; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-eye"></i> Voir en grand
                                </a>
                                <span style="margin: 0 5px;">|</span>
                                <a href="${piecePath}" download style="color: #1977cc; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-download"></i> Télécharger
                                </a>
                            </div>`
                        }
                    </div>
                `;
            }
            
            // Extrait de mariage
            if (mariage.extraitMariage) {
                const extraitPath = '{{ asset("storage/") }}/' + mariage.extraitMariage;
                const isPdf = extraitPath.toLowerCase().endsWith('.pdf');
                documentsHTML += `
                    <div style="margin-bottom: 15px; padding: 10px; background: white; border-radius: 5px; border: 1px solid #e0e0e0;">
                        <strong>Extrait de mariage:</strong><br>
                        ${isPdf ? 
                            `<a href="${extraitPath}" target="_blank" style="color: #1977cc; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; margin-top: 5px;">
                                <i class="fas fa-file-pdf" style="color: #e74c3c;"></i> Voir le PDF
                            </a>` : 
                            `<div style="margin-top: 5px;">
                                <img src="${extraitPath}" style="max-width: 150px; cursor: pointer; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;" 
                                    onclick="openImageModal('${extraitPath}')" alt="Extrait de mariage">
                                <br>
                                <a href="javascript:void(0)" onclick="openImageModal('${extraitPath}')" style="color: #1977cc; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-eye"></i> Voir en grand
                                </a>
                                <span style="margin: 0 5px;">|</span>
                                <a href="${extraitPath}" download style="color: #1977cc; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-download"></i> Télécharger
                                </a>
                            </div>`
                        }
                    </div>
                `;
            }
            
            return documentsHTML || '<div style="text-align: center; color: #666; font-style: italic;">Aucun document joint</div>';
        };
        
        // Informations de livraison si applicable
        const livraisonInfo = mariage.choix_option === 'livraison' ? `
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
                <h4 style="color: #1977cc; margin-bottom: 10px;">Informations de Livraison</h4>
                <div><strong>Destinataire:</strong> ${mariage.nom_destinataire || ''} ${mariage.prenom_destinataire || ''}</div>
                <div><strong>Contact:</strong> ${mariage.contact_destinataire || ''}</div>
                <div><strong>Email:</strong> ${mariage.email_destinataire || ''}</div>
                <div><strong>Adresse:</strong> ${mariage.adresse_livraison || ''}</div>
                <div><strong>Ville:</strong> ${mariage.ville || ''}</div>
                <div><strong>Commune:</strong> ${mariage.commune_livraison || ''}</div>
                <div><strong>Quartier:</strong> ${mariage.quartier || ''}</div>
                <div><strong>Code Postal:</strong> ${mariage.code_postal || ''}</div>
            </div>
        ` : '<div style="margin: 10px 0;"><strong>Mode de retrait:</strong> Retrait sur place</div>';
        
        // Vérifier si c'est une copie simple
        const isCopieSimple = mariage.nomEpoux === null;
        
        // Créer le contenu HTML pour SweetAlert
        const htmlContent = `
            <div style="text-align: left; max-height: 70vh; overflow-y: auto;">
                <h3 style="color: #1977cc; text-align: center; margin-bottom: 20px;">Détails de la Demande d'Extrait de Mariage</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <h4 style="color: #1977cc; margin-bottom: 10px;">${isCopieSimple ? 'Informations Générales' : 'Informations du Conjoint'}</h4>
                        ${isCopieSimple ? 
                            `<div><strong>Type:</strong> Copie Simple</div>` : 
                            `
                            <div><strong>Nom:</strong> ${mariage.nomEpoux}</div>
                            <div><strong>Prénom:</strong> ${mariage.prenomEpoux}</div>
                            <div><strong>Date de naissance:</strong> ${mariage.dateNaissanceEpoux}</div>
                            <div><strong>Lieu de naissance:</strong> ${mariage.lieuNaissanceEpoux}</div>
                            <div><strong>Commune:</strong> ${mariage.commune}</div>
                            `
                        }
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                        <h4 style="color: #1977cc; margin-bottom: 10px;">Informations du Demandeur</h4>
                        <div><strong>Nom:</strong> ${user.name} ${user.prenom}</div>
                        <div><strong>Email:</strong> ${user.email}</div>
                        <div><strong>Contact:</strong> ${user.contact}</div>
                        <div><strong>Date demande:</strong> ${new Date(mariage.created_at).toLocaleString('fr-FR')}</div>
                    </div>
                </div>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="color: #1977cc; margin-bottom: 10px;">Détails de la Commande</h4>
                    <div><strong>Quantité:</strong> ${mariage.quantite} copie(s)</div>
                    
                    <div><strong>Statut:</strong> 
                        <span class="badge-status-popup ${mariage.etat === 'en attente' ? 'badge-pending-popup' : mariage.etat === 'réçu' ? 'badge-progress-popup' : 'badge-completed-popup'}">
                            ${mariage.etat}
                        </span>
                    </div>
                    ${mariage.motif_de_rejet ? `<div><strong>Motif de rejet:</strong> ${mariage.motif_de_rejet}</div>` : ''}
                </div>
                
                ${livraisonInfo}
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <h4 style="color: #1977cc; margin-bottom: 10px;">Documents joints</h4>
                    ${formatDocuments(mariage)}
                </div>
            </div>
        `;
        
        // Afficher la popup avec SweetAlert
        Swal.fire({
            title: 'Détails de la Demande',
            html: htmlContent,
            width: '900px',
            confirmButtonText: 'Fermer',
            confirmButtonColor: '#1977cc',
            showCloseButton: true,
            customClass: {
                popup: 'request-details-popup'
            }
        });
    }

    // Fonction pour ouvrir une image en grand dans une modal
    function openImageModal(imageSrc) {
        const htmlContent = `
            <div style="text-align: center;">
                <img src="${imageSrc}" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);" alt="Document">
                <div style="margin-top: 20px; display: flex; justify-content: center; gap: 15px;">
                    <a href="${imageSrc}" download style="color: #1977cc; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; padding: 8px 15px; border: 1px solid #1977cc; border-radius: 5px;">
                        <i class="fas fa-download"></i> Télécharger l'image
                    </a>
                    <button onclick="Swal.close()" style="color: #6c757d; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; padding: 8px 15px; border: 1px solid #6c757d; border-radius: 5px; background: white; cursor: pointer;">
                        <i class="fas fa-times"></i> Fermer
                    </button>
                </div>
            </div>
        `;
        
        Swal.fire({
            title: 'Visualisation du document',
            html: htmlContent,
            width: '800px',
            showConfirmButton: false,
            showCloseButton: true,
            customClass: {
                popup: 'image-modal-popup'
            }
        });
    }

    function markAsDelivered(id) {
        Swal.fire({
            title: 'Entrer la référence',
            input: 'text',
            inputLabel: 'Veuillez entrer la référence du colis',
            inputPlaceholder: 'Référence',
            showCancelButton: true,
            confirmButtonText: 'Valider',
            cancelButtonText: 'Annuler',
            preConfirm: (reference) => {
                if (!reference) {
                    Swal.showValidationMessage('Vous devez entrer une référence');
                }
                return reference;
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const url = markAsDeliveredUrl.replace(':id', id);
                
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        statut_livraison: 'livré',
                        reference: result.value
                    },
                    success: function(response) {
                        Swal.fire('Succès!', 'La demande a été marquée comme livrée.', 'success');
                        location.reload();
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseJSON.error || 'Une erreur est survenue lors de la mise à jour.';
                        Swal.fire('Erreur!', errorMessage, 'error');
                    }
                });
            }
        });
    }

     // Fonction pour afficher les informations de livraison
    function showDeliveryInfo(mariage) {
        const deliveryInfo = mariage || {};
        
        const htmlContent = `
            <div style="text-align: center;">
                <h3 style="color: #1977cc; margin-bottom: 20px;">Informations de Livraison</h3>
                
                <div style="margin-bottom: 15px;">
                    <strong>Nom du destinataire:</strong> ${deliveryInfo.nom_destinataire + ' ' + deliveryInfo.prenom_destinataire || mariage.user.name + ' ' + mariage.user.prenom}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Téléphone:</strong> ${deliveryInfo.contact_destinataire || mariage.user.contact}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Ville:</strong> ${deliveryInfo.ville || 'Non spécifiée'}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Commune:</strong> ${deliveryInfo.commune_livraison || 'Non spécifiée'}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Quartier:</strong> ${deliveryInfo.quartier || 'Non spécifiée'}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Code de livraison:</strong> ${deliveryInfo.livraison_code || 'Non spécifiée'}
                </div>
                
                <div style="margin-bottom: 15px;">
                    <strong>Adresse de livraison:</strong> ${deliveryInfo.adresse_livraison || 'Non spécifiée'}
                </div>
            </div>
        `;
        
        Swal.fire({
            title: 'Détails de Livraison',
            html: htmlContent,
            showCancelButton: true,
            confirmButtonText: 'Télécharger en PDF',
            cancelButtonText: 'Fermer',
            confirmButtonColor: '#1977cc',
            width: '600px',
            customClass: {
                popup: 'delivery-info-popup'
            },
            didOpen: () => {
                const popup = Swal.getPopup();
                popup.style.borderRadius = '12px';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const url = downloadDeliveryInfoUrl.replace(':id', mariage.id);
                window.open(url, '_blank');
            }
        });
    }
</script>
@endsection