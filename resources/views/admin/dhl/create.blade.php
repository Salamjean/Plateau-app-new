@extends('admin.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<style>
  .signup-card {
    max-width: 800px;
    margin: 40px auto;
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 51, 196, 0.1);
    overflow: hidden;
    border: none;
  }

  .card-header {
    background-color: #6777ef;
    color: white;
    padding: 25px 30px;
    border-bottom: none;
  }

  .card-header h3 {
    font-weight: 600;
    margin: 0;
    font-size: 1.5rem;
  }

  .card-body {
    padding: 30px;
  }

  .form-label {
    font-weight: 500;
    color: #6777ef;
    margin-bottom: 8px;
  }

  .form-control {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px 15px;
    transition: all 0.3s;
  }

  .form-control:focus {
    border-color: #6777ef;
    box-shadow: 0 0 0 0.25rem rgba(0, 51, 196, 0.15);
  }

  .btn-primary {
    background-color: #6777ef;
    border: none;
    border-radius: 8px;
    padding: 12px 0;
    font-weight: 500;
    letter-spacing: 0.5px;
    transition: all 0.3s;
    width: 100%;
    margin-top: 10px;
  }

  .btn-primary:hover {
    background-color: #0028a0;
    transform: translateY(-2px);
  }

  .btn-primary:active {
    transform: translateY(0);
  }

  .invalid-feedback {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 5px;
  }

  .input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #e0e0e0;
  }

  /* Animation pour les messages flash */
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .alert-message {
    animation: fadeIn 0.5s ease-out;
    border-radius: 8px;
    margin-bottom: 25px;
  }

  /* Style modernisé pour SweetAlert */
  .swal2-popup {
    border-radius: 12px !important;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
  }

  .swal2-title {
    color: #6777ef !important;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .signup-card {
      margin: 20px 15px;
      border-radius: 12px;
    }
    
    .card-body {
      padding: 20px;
    }
  }
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card signup-card">
        <div class="card-header text-center">
          <h3>Enregistré du DHL sur e-ci</h3>
        </div>
        
        <div class="card-body">
          @if (Session::get('success1'))
            <div class="alert alert-success alert-message">
              {{ Session::get('success1') }}
            </div>
          @endif

          @if (Session::get('success'))
            <div class="alert alert-success alert-message">
              {{ Session::get('success') }}
            </div>
          @endif

          @if (Session::get('error'))
            <div class="alert alert-danger alert-message">
              {{ Session::get('error') }}
            </div>
          @endif

          <form class="needs-validation" method="POST" enctype="multipart/form-data" action="{{ route('dhl.store') }}" novalidate>
            @csrf
            @method('POST')

            <div class="row mb-4">
              <div class="col-md-6 mb-3">
                <label for="validationCustom001" class="form-label">Nom du responsable</label>
                <input type="text" class="form-control" name="name" id="validationCustom001" placeholder="Entrez le nom du responsable du DHL" required>
                @error('name')
                <div class="invalid-feedback d-block">
                  {{ $message }}
                </div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="validationCustom002" class="form-label">Prénoms du responsable du DHL</label>
                <input type="text" class="form-control" name="prenom" id="validationCustom002" placeholder="Entrez le prénom du responsable du DHL" required>
                @error('prenom')
                <div class="invalid-feedback d-block">
                  {{ $message }}
                </div>
                @enderror
              </div>
            </div>

            <div class="row mb-4">
              <div class="col-md-6 mb-3">
                <label for="validationCustom003" class="form-label">Email du DHL</label>
                <input type="email" class="form-control" name="email" id="validationCustom003" placeholder="Entrez le mail du DHL " required>
                @error('email')
                <div class="invalid-feedback d-block">
                  {{ $message }}
                </div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="validationCustom004" class="form-label">Contact du DHL</label>
                <input type="text" class="form-control" name="contact" id="validationCustom004" placeholder="Entrez le contact du DHL" required>
                @error('contact')
                <div class="invalid-feedback d-block">
                  {{ $message }}
                </div>
                @enderror
              </div>
            </div>

            <div class="mb-4">
              <label for="validationCustom005" class="form-label">Situation géographique du DHL</label>
              <input type="text" class="form-control" name="commune" id="validationCustom005" placeholder="Entrez la Situation géographique du DHL" required>
              @error('commune')
              <div class="invalid-feedback d-block">
                {{ $message }}
              </div>
              @enderror
            </div>

            <div class="d-grid">
              <button class="btn btn-primary" type="submit">
                <i class="fas fa-user-plus me-2"></i> Créer le compte
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Validation du formulaire
  (function() {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();

  // Gestion des messages flash avec SweetAlert
  @if(Session::has('success')
    Swal.fire({
      icon: 'success',
      title: 'Succès',
      text: '{{ Session::get('success') }}',
      confirmButtonColor: '#6777ef',
      timer: 3000
    });
  @endif

  @if(Session::has('error'))
    Swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: '{{ Session::get('error') }}',
      confirmButtonColor: '#6777ef'
    });
  @endif
</script>

@endsection