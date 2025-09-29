@extends('poste.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  :root {
    --primary-color: #1977cc;
    --primary-light: #1977cc;
    --secondary-color: #ea8c51;
    --secondary-light: #fde16d;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #17a2b8;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --border-radius: 10px;
    --box-shadow: 0 5px 15px rgba(6, 99, 78, 0.1);
    --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  }

  body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
  }

  /* Styles pour les cartes de statistiques */
  .stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
    padding: 0 20px;
  }

  .stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 20px;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    border-top: 4px solid var(--primary-color);
    display: flex;
    flex-direction: column;
  }

  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(6, 99, 78, 0.15);
  }

  .stat-card.secondary {
    border-top-color: var(--secondary-color);
  }

  .stat-card.success {
    border-top-color: var(--success-color);
  }

  .stat-title {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
  }

  .stat-value {
    color: var(--dark-color);
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .stat-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    align-self: flex-end;
  }

  .stat-card .stat-icon {
    color: var(--primary-color);
  }

  .stat-card.secondary .stat-icon {
    color: var(--secondary-color);
  }

  .stat-card.success .stat-icon {
    color: var(--success-color);
  }

  .card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    margin-bottom: 25px;
    background-color: white;
    overflow: hidden;
    margin: 0 20px;
  }

  .archive-btn {
      color: var(--warning-color);
  }

  .archive-btn:hover {
      background-color: rgba(255, 193, 7, 0.1);
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(6, 99, 78, 0.15);
  }

  .card-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    color: white;
    border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
    padding: 15px 20px;
    font-weight: 600;
    border-bottom: none;
    position: relative;
    overflow: hidden;
  }

  .card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: var(--secondary-color);
  }

  .table-responsive {
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }

  .table {
    margin-bottom: 0;
    background-color: white;
  }

  .table thead th {
    background-color: #f1f8f5;
    color: var(--primary-color);
    font-weight: 600;
    border: none;
    padding: 12px 15px;
    vertical-align: middle;
    border-bottom: 2px solid var(--secondary-light);
  }

  .table tbody tr {
    transition: var(--transition);
  }

  .table tbody tr:hover {
    background-color: rgba(249, 207, 3, 0.05);
  }

  .table tbody td {
    padding: 12px 15px;
    vertical-align: middle;
    border-top: 1px solid rgba(0, 0, 0, 0.03);
  }

  .status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    display: inline-block;
    min-width: 100px;
    text-align: center;
  }

  .search-box {
    position: relative;
    margin-bottom: 20px;
    max-width: 300px;
  }

  .search-box input {
    padding-left: 40px;
    border-radius: 20px;
    border: 1px solid #ddd;
    box-shadow: none;
    transition: var(--transition);
  }

  .search-box input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(6, 99, 78, 0.1);
  }

  .search-box i {
    position: absolute;
    left: 15px;
    top: 10px;
    color: var(--primary-color);
  }

  .page-title {
    color: var(--primary-color);
    font-weight: 700;
    margin-bottom: 20px;
    position: relative;
    display: inline-block;
  }

  .page-title::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--secondary-color);
  }

  .empty-state {
    text-align: center;
    padding: 40px 0;
    color: #6c757d;
  }

  .empty-state i {
    font-size: 50px;
    margin-bottom: 15px;
    color: var(--secondary-color);
  }

  /* Boutons d'action */
  .action-btn {
    border: none;
    background: none;
    padding: 5px;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: var(--transition);
    cursor: pointer;
  }

  .edit-btn {
    color: var(--primary-color);
  }

  .edit-btn:hover {
    background-color: rgba(6, 99, 78, 0.1);
    color: var(--primary-light);
  }

  .delete-btn {
    color: var(--danger-color);
  }

  .delete-btn:hover {
    background-color: rgba(220, 53, 69, 0.1);
  }

  .payment-btn {
    color: var(--info-color);
  }

  .payment-btn:hover {
    background-color: rgba(23, 162, 184, 0.1);
  }

  /* Badge pour les états */
  .badge-agent {
    background-color: var(--secondary-color);
    color: var(--primary-color);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
  }

  /* Animation des icônes */
  i.fas {
    transition: var(--transition);
  }

  .action-btn:hover i.fas {
    transform: scale(1.1);
  }

  .badge {
      display: inline-block;
      padding: 0.35em 0.7em;
      font-size: 0.85rem;
      font-weight: 600;
      border-radius: 12px;
      color: #fff;
      transition: transform 0.2s, box-shadow 0.2s;
      cursor: default;
  }

  .badge-available {
      background-color: #28a745; /* vert */
      box-shadow: 0 2px 6px rgba(40, 167, 69, 0.4);
  }

  .badge-unavailable {
      background-color: #dc3545; /* rouge */
      box-shadow: 0 2px 6px rgba(220, 53, 69, 0.4);
  }

  .badge-info {
      background-color: #17a2b8; /* bleu info */
      box-shadow: 0 2px 6px rgba(23, 162, 184, 0.4);
  }

  .badge-primary {
      background-color: #007bff; /* bleu primary */
      box-shadow: 0 2px 6px rgba(0, 123, 255, 0.4);
  }

  .badge-success {
      background-color: #28a745; /* vert success */
      box-shadow: 0 2px 6px rgba(40, 167, 69, 0.4);
  }

  /* Effet au survol */
  .badge:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  }

  /* Style pour SweetAlert */
  .swal2-popup {
    border-radius: var(--border-radius) !important;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
  }

  .swal2-title {
    color: var(--primary-color) !important;
  }

  .swal2-confirm {
    background-color: var(--primary-color) !important;
  }

  .swal2-cancel {
    background-color: var(--secondary-color) !important;
    color: var(--primary-color) !important;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .stats-container {
      grid-template-columns: 1fr;
    }
    
    .table-responsive {
      border: 1px solid rgba(6, 99, 78, 0.1);
    }
    
    .table thead {
      display: none;
    }
    
    .table tbody tr {
      display: block;
      margin-bottom: 15px;
      border: 1px solid rgba(6, 99, 78, 0.1);
      border-radius: var(--border-radius);
      box-shadow: 0 2px 5px rgba(6, 99, 78, 0.05);
    }
    
    .table tbody td {
      display: block;
      text-align: right;
      padding-left: 50%;
      position: relative;
      border-top: 1px solid rgba(6, 99, 78, 0.05);
    }
    
    .table tbody td::before {
      content: attr(data-label);
      position: absolute;
      left: 15px;
      width: 45%;
      padding-right: 10px;
      font-weight: 600;
      text-align: left;
      color: var(--primary-color);
    }
    
    .search-box {
      max-width: 100%;
      width: 100%;
      margin-top: 10px;
    }

    .page-title {
      font-size: 1.5rem;
    }
  }
</style>

<div class="container-fluid">
  <!-- Notifications -->
  <div class="row" style="width:100%; justify-content:center">
    @if (Session::get('success'))
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Succès',
          text: '{{ Session::get('success') }}',
          showConfirmButton: true,
          confirmButtonText: 'OK'
        });
      </script>
    @endif
  </div>

  <!-- En-tête -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="page-title"><i class="fas fa-archive me-2"></i>Livreurs archivés</h1>
    <a href="{{ route('delivery.index') }}" class="btn btn-primary">
      <i class="fas fa-arrow-left me-2"></i>Retour aux livreurs actifs
    </a>
  </div>

  <!-- Tableau des livreurs archivés -->
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold"><i class="fas fa-archive me-2"></i>Liste des livreurs archivés</h6>
          <div class="search-box mt-2 mt-md-0">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInputArchives" class="form-control" placeholder="Rechercher...">
          </div>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center" id="dataTableArchives">
            <thead>
              <tr class="text-center">
                <th class="text-center">Nom</th>
                <th class="text-center">Prénoms</th>
                <th class="text-center">Email</th>
                <th class="text-center">Contact</th>
                <th class="text-center">Lieu de résidence</th>
                <th class="text-center">Date d'archivage</th>
                <th class="text-center">Ancien statut</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($livreurs as $livreur)
                <tr class="text-center">
                  <td class="text-center">{{ $livreur->name }}</td>
                  <td class="text-center">{{ $livreur->prenom }}</td>
                  <td class="text-center">{{ $livreur->email }}</td>
                  <td class="text-center">{{ $livreur->contact }}</td>
                  <td class="text-center">{{ $livreur->commune }}</td>
                  <td class="text-center">
                    <span class="badge badge-info">
                      {{ $livreur->archived_at}}
                    </span>
                  </td>
                  <td class="text-center">
                    <span class="badge {{ $livreur->disponible ? 'badge-success' : 'badge-unavailable' }}">
                      {{ $livreur->disponible ? 'Disponible' : 'Indisponible' }}
                    </span>
                  </td>
                  <td class="text-center">
                    <div class="d-flex justify-content-center">
                      <!-- Bouton Voir les détails -->
                      <button class="action-btn edit-btn me-2" title="Voir les détails">
                        <a href="{{ route('poste.livreur.edit', $livreur->id) }}">
                          <i class="fas fa-eye"></i>
                        </a>
                      </button>
                      
                      <!-- Bouton Désarchiver -->
                      <form action="{{ route('poste.livreur.restore', $livreur->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="button" class="action-btn restore-btn" title="Désarchiver" 
                          onclick="confirmRestore('{{ $livreur->id }}', '{{ $livreur->name }} {{ $livreur->prenom }}')">
                          <i class="fas fa-undo"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center py-5">
                    <div class="empty-archive">
                      <i class="fas fa-archive"></i>
                      <h4>Aucun livreur archivé</h4>
                      <p class="text-muted">Tous vos livreurs sont actuellement actifs.</p>
                      <a href="{{ route('delivery.index') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-users me-2"></i>Voir les livreurs actifs
                      </a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Fonction de confirmation de désarchivage
  function confirmRestore(id, nomLivreur) {
    Swal.fire({
      title: 'Confirmer le désarchivage',
      html: `Êtes-vous sûr de vouloir désarchiver le livreur <strong>${nomLivreur}</strong> ?<br><br>
             <small class="text-muted">Le livreur redeviendra actif et pourra recevoir de nouvelles livraisons.</small>`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Oui, désarchiver',
      cancelButtonText: 'Annuler',
      customClass: {
        popup: 'custom-swal-popup'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        // Soumettre le formulaire de restauration
        document.querySelector(`form[action*="${id}"]`).submit();
      }
    });
  }

  // Recherche dans le tableau des archives
  document.getElementById('searchInputArchives').addEventListener('keyup', function() {
    const input = this.value.toLowerCase();
    const rows = document.querySelectorAll('#dataTableArchives tbody tr');
    
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      row.style.display = text.includes(input) ? '' : 'none';
    });
  });

  // Tri par date d'archivage (optionnel)
  document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('dataTableArchives');
    if (table) {
      const rows = Array.from(table.querySelectorAll('tbody tr'));
      rows.sort((a, b) => {
        const dateA = new Date(a.cells[5].textContent);
        const dateB = new Date(b.cells[5].textContent);
        return dateB - dateA; // Du plus récent au plus ancien
      });
      
      const tbody = table.querySelector('tbody');
      tbody.innerHTML = '';
      rows.forEach(row => tbody.appendChild(row));
    }
  });
</script>

@endsection