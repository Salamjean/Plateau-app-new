@extends('admin.layouts.template')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<style>
  .signup-container {
    max-width: 70%;
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

  /* Style pour les erreurs de validation */
  .was-validated .form-control:invalid {
    border-color: #dc3545;
  }

  .was-validated .form-control:valid {
    border-color: #28a745;
  }

  .invalid-feedback {
    display: block;
    font-size: 0.875rem;
  }
</style>

<div class="signup-container">
  <h6>Inscrire une mairie</h6>
  <div class="ms-panel-body">
    <form class="needs-validation" method="POST" action="{{ route('admin.store') }}" novalidate>
        @csrf
        @method('POST')

        <div class="row">
            @if (Session::get('success1'))
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Suppression réussie',
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

        <!-- Formulaire principal -->
        <div class="form-row mb-3">
            <div class="col-md-6">
              <label for="validationCustom002" class="form-label">Sélectionnez une commune</label>
              <input id="validationCustom002" name="name" value="plateau" class="form-control" required readonly>
              </input>
              @error('name')
              <div class="text-danger text-center">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="validationCustom004" class="form-label">Email</label>
              <input type="email" class="form-control" name="email" id="validationCustom004" value="{{ old('email') }}" placeholder="Entrez son email" required>
              @error('email')
              <div class="text-danger text-center">{{ $message }}</div>
              @enderror
          </div>
        </div>

        <button class="btn btn-primary" type="submit">Créer le compte</button>
    </form>
  </div>
</div>
<script>
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