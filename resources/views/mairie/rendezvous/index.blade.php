@extends('mairie.layouts.template')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  :root {
    --primary-color: #1977cc;
    --primary-light: #1977cc;
    --primary-dark: #1977cc;
    --secondary-color: #ffffff;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --light-color: #f8f9fa;
    --dark-color: #343a40;
    --border-radius: 8px;
    --box-shadow: 0 4px 12px rgba(0, 51, 196, 0.1);
    --transition: all 0.3s ease;
  }

  body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
  }

  .card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    margin: 25px;
    background-color: var(--secondary-color);
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 51, 196, 0.15);
  }

  .card-header {
    background-color: var(--primary-color);
    color: var(--secondary-color);
    border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
    padding: 15px 20px;
    font-weight: 600;
    border-bottom: none;
  }

  .table-responsive {
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }

  .table {
    margin-bottom: 0;
    background-color: var(--secondary-color);
  }

  .table thead th {
    background-color: #f1f5ff;
    color: #0033c4;
    font-weight: 600;
    border: none;
    padding: 12px 15px;
    vertical-align: middle;
  }

  .table tbody tr {
    transition: var(--transition);
  }

  .table tbody tr:hover {
    background-color: rgba(0, 51, 196, 0.05);
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

  .status-en-attente {
    background-color: var(--warning-color);
    color: var(--dark-color);
  }

  .status-validee {
    background-color: var(--success-color);
    color: var(--secondary-color);
  }

  .status-refusee {
    background-color: var(--danger-color);
    color: var(--secondary-color);
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
    border-color: var(--primary-light);
    box-shadow: 0 0 0 0.2rem rgba(0, 51, 196, 0.1);
  }

  .search-box i {
    position: absolute;
    left: 15px;
    top: 10px;
    color: var(--primary-color);
  }

  .img-thumbnail {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
    cursor: pointer;
    transition: var(--transition);
    border: 1px solid rgba(0, 51, 196, 0.1);
    background-color: var(--secondary-color);
  }

  .img-thumbnail:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0, 51, 196, 0.2);
    border-color: var(--primary-light);
  }

  .pdf-icon {
    width: 30px;
    height: 30px;
    cursor: pointer;
    transition: var(--transition);
  }

  .pdf-icon:hover {
    transform: scale(1.1);
  }

  .breadcrumb {
    background-color: transparent;
    padding: 0;
    font-size: 14px;
  }

  .breadcrumb-item a {
    color: var(--primary-color);
    text-decoration: none;
    transition: var(--transition);
  }

  .breadcrumb-item a:hover {
    color: var(--primary-dark);
    text-decoration: underline;
  }

  .breadcrumb-item.active {
    color: var(--primary-dark);
    font-weight: 500;
  }

  .page-title {
    color: var(--primary-dark);
    font-weight: 700;
    margin-bottom: 20px;
  }

  .modal-content {
    border-radius: var(--border-radius);
    border: none;
    box-shadow: 0 5px 20px rgba(0, 51, 196, 0.2);
  }

  .modal-header {
    background-color: var(--primary-color);
    color: var(--secondary-color);
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    border-bottom: none;
  }

  .modal-body img {
    max-height: 70vh;
    width: auto;
    margin: 0 auto;
    display: block;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .empty-state {
    text-align: center;
    padding: 40px 0;
    color: #6c757d;
  }

  .empty-state i {
    font-size: 50px;
    margin-bottom: 15px;
    color: var(--primary-light);
  }

  .badge-agent {
    background-color: var(--primary-color);
    color: var(--secondary-color);
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
  }

  .document-container {
    position: relative;
    width: 60px;
    height: 60px;
    margin: 0 auto;
  }

  .document-placeholder {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 12px;
    color: gray;
    text-align: center;
    width: 100%;
  }

  .btn-close {
    filter: invert(1);
  }

  /* Styles pour les nouveaux boutons */
  .btn-action {
    border-radius: 6px;
    padding: 6px 12px;
    font-weight: 600;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 0.85rem;
    margin: 2px;
  }

  .btn-confirm {
    background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
    border: none;
    color: white;
  }

  .btn-confirm:hover {
    background: linear-gradient(135deg, #218838 0%, var(--success-color) 100%);
    transform: translateY(-2px);
    color: white;
  }

  .btn-modify {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    border: none;
    color: white;
  }

  .btn-modify:hover {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
    transform: translateY(-2px);
    color: white;
  }

  .action-buttons {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 10px;
  }

  @media (max-width: 768px) {
    .table-responsive {
      border: 1px solid rgba(0, 51, 196, 0.1);
    }
    
    .table thead {
      display: none;
    }
    
    .table tbody tr {
      display: block;
      margin-bottom: 15px;
      border: 1px solid rgba(0, 51, 196, 0.1);
      border-radius: var(--border-radius);
      box-shadow: 0 2px 5px rgba(0, 51, 196, 0.05);
    }
    
    .table tbody td {
      display: block;
      text-align: right;
      padding-left: 50%;
      position: relative;
      border-top: 1px solid rgba(0, 51, 196, 0.05);
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
    
    .img-thumbnail {
      float: right;
    }

    .page-title {
      font-size: 1.5rem;
    }

    .card-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .search-box {
      max-width: 100%;
      width: 100%;
      margin-top: 10px;
    }

    .action-buttons {
      flex-direction: row;
      justify-content: center;
    }

    .btn-action {
      flex: 1;
    }
  }
</style>

<div class="container-fluid">
  <!-- Notifications SweetAlert -->
  <div class="row" style="width:100%; justify-content:center">
    @if (Session::get('success1'))
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Suppression réussie',
          text: '{{ Session::get('success1') }}',
          showConfirmButton: true,
          confirmButtonText: 'OK',
          customClass: {
            popup: 'custom-swal-popup',
            confirmButton: 'btn-swal-confirm'
          }
        });
      </script>
    @endif

    @if (Session::get('success'))
      <script>
        Swal.fire({
          icon: 'success',
          title: 'Action réussie',
          text: '{{ Session::get('success') }}',
          showConfirmButton: true,
          confirmButtonText: 'OK',
          customClass: {
            popup: 'custom-swal-popup',
            confirmButton: 'btn-swal-confirm'
          }
        });
      </script>
    @endif

    @if (Session::get('error'))
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Erreur',
          text: '{{ Session::get('error') }}',
          showConfirmButton: true,
          confirmButtonText: 'OK',
          customClass: {
            popup: 'custom-swal-popup',
            confirmButton: 'btn-swal-confirm'
          }
        });
      </script>
    @endif
  </div>

  <!-- En-tête de page -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="page-title"><i class="fas fa-home me-2 mt-4 " style="color: var(--primary-color);margin-left:25px"></i>Liste des rendez-vous</h1>
  </div>

  <!-- Table des rendez-vous -->
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold"><i class="fas fa-calendar-alt me-2"> </i>Gestion des rendez-vous</h6>
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
          </div>
        </div>
        <div class="table-responsive">
          <table class="table align-items-center" id="dataTable">
            <thead>
                <tr class="text-center">
                    <th class="text-center">Informations époux</th>
                    <th class="text-center">Informations épouse</th>
                    <th class="text-center">Contact</th>
                    <th class="text-center">Rendez-vous</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
              @forelse ($rendezvous as $item)
                <tr class="animate__animated animate__fadeIn">
                  <td class="text-center" style="text-align: center">
                    <div style="text-align: center">
                      <h6 class="mb-1 text-muted">Nom: <span class="text-black">{{ $item->nom_epoux.' '.$item->prenom_epoux }} </span></h6>
                      <h6 class="mb-1 text-muted">Date Naiss: <span class="text-black">{{ $item->date_naissance_epoux }}  </span></h6>
                      <small class="mb-1 text-muted">Lieu Naiss: <span class="text-black">{{ $item->lieu_naissance_epoux }}  </span></small>
                    </div>
                  </td>
                  <td class="text-center" style="text-align: center">
                    <div style="text-align: center">
                      <h6 class="mb-1 text-muted">Nom: <span class="text-black">{{ $item->nom_epouse.' '.$item->prenom_epouse }} </span></h6>
                      <h6 class="mb-1 text-muted">Date Naiss: <span class="text-black">{{ $item->date_naissance_epouse }} </span></h6>
                      <small class="mb-1 text-muted">Lieu Naiss: <span class="text-black">{{ $item->lieu_naissance_epouse }}</span></small>
                    </div>
                  </td>
                  <td class="text-center" style="text-align: center">
                    <div style="text-align: center">
                      <h6 class="mb-1 text-muted">Adresse: <span class="text-black">{{ $item->adresse }}</span></h6>
                      <h6 class="mb-1 text-muted">Contact: <span class="text-black">{{ $item->telephone }}</span></h6>
                      <small class="mb-1 text-muted">Email: <span class="text-black">{{ $item->email }}</span></small>
                    </div>
                  </td>
                  <td class="text-center" style="text-align: center">
                    <div style="text-align: center">
                      <h6 class="mb-1 text-muted">Date: <span class="text-black">{{ $item->date_mariage_souhaitee }}</span></h6>
                      <h6 class="mb-1 text-muted">Heure: <span class="text-black">{{ $item->heure_souhaitee }}</span></h6>
                      @php
                        $badgeClasses = [
                          'en attente' => 'bg-warning text-white',
                          'confirmé' => 'bg-success text-white',
                          'annulé' => 'bg-danger text-white',
                          'terminé' => 'bg-primary text-white',
                          'reporté' => 'bg-info text-dark'
                        ];
                        $defaultClass = 'bg-secondary text-white';
                      @endphp
                      <span class="badge {{ $badgeClasses[$item->statut] ?? $defaultClass }}">
                        {{ $item->statut }}
                      </span>
                    </div>
                  </td>
                  <td class="text-center">
                    <div class="action-buttons">
                      <!-- Bouton pour confirmer directement -->
                      <button class="btn btn-confirm btn-action" onclick="confirmRendezVous({{ $item->id }})">
                        <i class="fas fa-check-circle"></i> Confirmer
                      </button>
                      
                      <!-- Bouton pour aller sur la page de modification -->
                      <a href="{{ route('rendezvous.confirmation', $item->id) }}" class="btn btn-modify btn-action">
                        <i class="fas fa-edit"></i> Modifier
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8">
                    <div class="empty-state">
                      <i class="fas fa-inbox"></i>
                      <h5 class="mt-3">Aucun rendez-vous trouvé</h5>
                      <p class="text-muted">Vous n'avez effectué aucun rendez-vous de mariage</p>
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
  // Fonction pour confirmer directement avec SweetAlert2
  function confirmRendezVous(id) {
    Swal.fire({
      title: 'Confirmer le rendez-vous',
      text: "Voulez-vous confirmer ce rendez-vous sans modification ?",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Oui, confirmer',
      cancelButtonText: 'Annuler',
      customClass: {
        popup: 'custom-swal-popup',
        confirmButton: 'btn-swal-confirm',
        cancelButton: 'btn-swal-cancel'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        // Utiliser une route nommée pour l'appel AJAX
        fetch("{{ route('rendezvous.confirm', ['id' => ':id']) }}".replace(':id', id), {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire(
              'Confirmé!',
              'Le rendez-vous a été confirmé avec succès.',
              'success'
            ).then(() => {
              // Recharger la page pour voir les changements
              window.location.reload();
            });
          } else {
            Swal.fire(
              'Erreur!',
              'Une erreur s\'est produite lors de la confirmation.',
              'error'
            );
          }
        })
        .catch(error => {
          Swal.fire(
            'Erreur!',
            'Une erreur s\'est produite lors de la confirmation.',
            'error'
          );
        });
      }
    });
  }

  // Fonction de recherche dans le tableau
  document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchText = this.value.toLowerCase();
    const rows = document.querySelectorAll('#dataTable tbody tr');
    
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      if (text.includes(searchText)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  });
</script>

@endsection