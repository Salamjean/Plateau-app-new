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
    <!-- Filtres -->
    <div class="card mb-4" style="border-top: 3px solid #6777ef; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.birth') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="demandeur" class="form-label font-weight-bold text-secondary small mb-2">NOM DU DEMANDEUR</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-user text-muted"></i></span>
                        <input type="text" name="demandeur" id="demandeur" class="form-control border-start-0" placeholder="Rechercher par nom ou prénom..." value="{{ $searchDemandeur }}" onchange="this.form.submit()">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="reference" class="form-label font-weight-bold text-secondary small mb-2">RÉFÉRENCE</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-hashtag text-muted"></i></span>
                        <input type="text" name="reference" id="reference" class="form-control border-start-0" placeholder="Ex: birth-..." value="{{ $searchReference }}" onchange="this.form.submit()">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="month" class="form-label font-weight-bold text-secondary small mb-2">MOIS DE DEMANDE</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-calendar text-muted"></i></span>
                        <select name="month" id="month" class="form-select border-start-0" onchange="this.form.submit()">
                            <option value="">Tous les mois</option>
                            @foreach ($availableMonths as $month)
                                @php
                                    $carbonDate = \Carbon\Carbon::createFromFormat('Y-m', $month);
                                    $formattedMonth = ucfirst($carbonDate->translatedFormat('F Y'));
                                @endphp
                                <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                                    {{ $formattedMonth }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2 d-grid gap-2">
                    <button type="submit" class="btn text-white w-100 py-2" style="background-color: #6777ef; border-radius: 8px; font-weight: 500; border: none; transition: background-color 0.2s;">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('admin.birth') }}" class="btn btn-secondary w-100 py-2" style="border-radius: 8px; font-weight: 500;">
                        <i class="fas fa-undo me-1"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row col-12">
      <div class="col-lg-12">
          <div class="card mb-4">
              <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Liste de toutes les demandes d'extraits de naissance</h6>
              </div>
              <div class="table-responsive p-3">
                  
                  <!-- Tableau des naissances -->
                  <table class="table align-items-center table-flush" id="dataTable">
                      <thead class="bg-navbar text-white">
                          <tr style="font-size: 12px" class="text-center">
                              <th>Demandeur</th>
                              <th>Reference</th>
                              <th>Nombre de copies</th>
                              <th>Statut de traitement</th>
                              <th>Statut de livraison</th>
                              <th>Agent de la mairie</th>
                              <th>Date de demande</th>
                          </tr>
                      </thead>
                      <tbody>
                          @forelse ($naissances as $naissance)
                              <tr style="font-size: 12px"  class="text-center">
                                  <td>
                                      <div class="fw-bold">{{ strtoupper($naissance->user->name ?? '').' '.strtoupper($naissance->user->prenom ?? '') }}</div>
                                      @php
                                          $contactNav = $naissance->contact_destinataire ?: ($naissance->user->contact ?? '');
                                      @endphp
                                      @if ($contactNav)
                                          <div class="text-muted" style="font-size: 11px;"><i class="fas fa-phone-alt me-1"></i>{{ $contactNav }}</div>
                                      @endif
                                  </td>
                                  <td>{{ $naissance->reference }}</td>
                                  <td>
                                      @php
                                          $qty = (int) ($naissance->quantite ?? (($naissance->qty_simple ?? 0) + ($naissance->qty_integral ?? 0)));
                                      @endphp
                                      <span class="badge bg-light text-dark border fw-bold">{{ $qty > 0 ? $qty : 1 }}</span>
                                  </td>
                                  <td>{{ $naissance->etat}}</td>
                                  <td>
                                      @if (!empty($naissance->statut_livraison))
                                          @php
                                              $st = strtolower($naissance->statut_livraison);
                                              $badgeClass = in_array($st, ['livré', 'livre', 'récupéré', 'recupere', 'retiré', 'retire', 'terminé', 'termine']) ? 'bg-success' : (in_array($st, ['en cours', 'disponible']) ? 'bg-warning text-dark' : 'bg-secondary');
                                          @endphp
                                          <span class="badge {{ $badgeClass }}">
                                              {{ $naissance->statut_livraison }}
                                          </span>
                                      @else
                                          <span class="badge bg-secondary">
                                              {{ strtolower($naissance->choix_option) == 'livraison' ? 'en attente' : 'Retrait sur place' }}
                                          </span>
                                      @endif
                                  </td>
                                  <td>{{ $naissance->agent ? $naissance->agent->name.' '.$naissance->agent->prenom : 'non attribué' }}</td>

                                  <td>{{ $naissance->created_at->format('d/m/Y H:i')}}</td>
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="7" class="text-center">Aucune demande effectuée</td>
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
