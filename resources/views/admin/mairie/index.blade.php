@extends('admin.layouts.template')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<style>
  .signup-container {
    max-width: 100%;
    margin: 50px auto;
    background-color: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
  }

  .signup-container h6 {
    font-size: 1.75rem;
    font-weight: bold;
    color: #6777ef;
    text-align: center;
    margin-bottom: 30px;
  }

  .form-label {
    font-weight: 500;
  }

  .form-control {
    border-radius: 8px;
  }

  .btn-primary {
    background-color: #6777ef;
    border: none;
    width: 50%;
    border-radius: 8px;
    padding: 10px;
    margin-left: 25%;
    font-size: 1.1rem;
    transition: background-color 0.3s;
  }

  .btn-primary:hover {
    background-color: #4b5bd6;
  }

  .form-check-label {
    font-weight: 400;
    margin-left: 10px;
  }

  .table-responsive input {
    width: 100%;
    max-width: 400px;
    margin-bottom: 15px;
  }

  /* Style pour les alertes */
  .swal2-popup {
    font-size: 1.1rem;
  }

  .form-recherche{
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #f8f9fa;
    width: 250px;
    font-size: 14px;
    transition: background-color 0.3s;
  }

  /* Style pour les erreurs de validation */
  .invalid-feedback {
    display: block;
    font-size: 0.875rem;
  }

  .btn-primary{
    margin-top: 30px;
  }

  button {
    border: none;
    background: none;
    cursor: pointer;
    font-size: 16px;
  }

  a {
    text-decoration: none;
    color: black;
  }

  a .edit {
    color: #28a745;
    transition: color 0.3s ease;
  }

  a .eye {
    color: #3047b8;
    transition: color 0.3s ease;
    text-decoration: none;
  }

  a .delete {
    color: #dc3545;
    transition: color 0.3s ease;
  }

  .edit:hover {
    color: #1e7e34;
  }

  .eye:hover {
    color: #1e617e;
  }

  .delete:hover {
    color: #c82333;
  }
</style>

<div class="signup-container">
  <div class="ms-panel-body">
    <div class="row">
      @if (Session::get('success1'))
        <script>
          Swal.fire({
            icon: 'success',
            title: 'Archivage réussi',
            text: '{{ Session::get('success1') }}',
            showConfirmButton: true,
            confirmButtonText: 'OK',
            background: '#ffcccc',
            color: '#b30000'
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
            background: '#ccffcc',
            color: '#006600'
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
            background: '#f86750',
            color: '#ffffff'
          });
        </script>
      @endif
    </div>
    
    <div class="row col-12">
      <div class="col-lg-12">
          <div class="card mb-4">
              <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Liste de toutes les mairies</h6>
              </div>
              <div class="table-responsive p-3">
                  <!-- Formulaire pour sélectionner une mairie, saisir le montant et la recherche -->
                  <form method="POST" action="{{ route('admin.add_solde') }}" style="margin-left: 100px" class="mb-3">
                    @csrf <!-- Ajoutez ceci pour la protection CSRF -->
                    <div class="row">
                        <!-- Barre de recherche -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="searchInput">Rechercher :</label>
                                <input type="text" id="searchInput" name="searchInput" class="form-recherche" placeholder="Rechercher...">
                            </div>
                        </div>
                        <!-- Sélection de la mairie -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="mairie_selectionnee">Mairie :</label>
                                <select id="mairie_selectionnee" name="mairie_selectionnee" class="form-control" required>
                                   
                                    @foreach ($mairies as $mairie)
                                        <option value="{{ $mairie->id }}" {{ $mairieSelectionnee == $mairie->id ? 'selected' : '' }}>
                                            {{ $mairie->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Montant à ajouter ou retirer -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="ajout_solde">Montant :</label>
                                <input type="number" id="ajout_solde" name="ajout_solde" class="form-control" value="{{ $ajoutSolde }}">
                            </div>
                        </div>

                        <!-- Bouton -->
                        <div class="col-md-4">
                            <div class="form-group d-flex">
                              <div class="col-md-3 d-flex gap-2" style="margin-top: 30px; margin-left:30px">
                                <label>
                                  Ajouter<input type="radio" name="action" value="ajouter" checked> 
                                </label>
                                <label>
                                  Retirer<input type="radio" name="action" value="retirer"> 
                                </label>
                            </div>
                                <label></label> <!-- Espace pour aligner le bouton -->
                                <button type="submit" class="btn btn-primary btn-block h-100 mt-10">Appliquer</button>
                            </div>
                        </div>
                    </div>
                </form>
                
  
                  <!-- Tableau des mairies -->
                  <table class="table align-items-center table-flush" id="dataTable">
                      <thead class="bg-navbar text-white">
                          <tr style="font-size: 12px">
                              <th>Mairie</th>
                              <th>Mails</th>
                              <th>Nombres d'agents</th>
                              <th>Nombre de caissié</th>
                              <th>Nombre d'hôpital</th>
                              <th>Solde restant</th>
                              <th class="text-center">Archivé</th>
                          </tr>
                      </thead>
                      <tbody>
                          @forelse ($mairies as $mairie)
                              <tr style="font-size: 12px">
                                  <td>{{ strtoupper($mairie->name) }}</td>
                                  <td>{{ $mairie->email }}</td>
                                  <td>{{ $agentsCount[$mairie->name] ?? 0 }}</td>
                                  <td>{{ $caisseCount[$mairie->name] ?? 0 }}</td>
                                  <td>{{ $doctorCount[$mairie->name] ?? 0 }}</td>
                                  <td>{{ number_format($soldeRestantParCommune[$mairie->name], 0, ',', ' ') ?? 0 }} FCFA</td>
                                  <td class="text-center">
                                      <button type="button" class="delete" onclick="confirmArchive('{{ $mairie->id }}')">
                                          <i class="fas fa-folder"></i>
                                      </button>
                                      <form id="archive-form-{{ $mairie->id }}" action="{{ route('mairie.archive', $mairie->id) }}" method="POST" style="display: none;">
                                          @csrf
                                          @method('DELETE')
                                      </form>
                                  </td>
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="9" class="text-center">Aucune demande effectuée</td>
                              </tr>
                          @endforelse
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  </div>

<script>
  function confirmArchive(vendorId) {
    Swal.fire({
      title: 'Êtes-vous sûr?',
      text: "Vous ne pourrez pas revenir en arrière!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Oui, archiver!',
      cancelButtonText: 'Annuler'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('archive-form-' + vendorId).submit();
      }
    });
  }

  // Fonction de recherche
  document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#dataTable tbody tr');

    rows.forEach(row => {
      const cells = row.querySelectorAll('td');
      const match = Array.from(cells).some(cell => 
        cell.textContent.toLowerCase().includes(filter)
      );
      row.style.display = match ? '' : 'none';
    });
  });

  // Validation du formulaire
  document.querySelector('form').addEventListener('submit', function(event) {
    let form = event.target;
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
    }
    form.classList.add('was-validated');
  }, false);
</script>
@endsection