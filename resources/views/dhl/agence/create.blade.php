@extends('dhl.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<style>
  :root {
    --primary-color: #ffd83e;
    --primary-dark: #d40511;
    --secondary-color: #333333;
    --light-color: #f8f9fa;
    --dark-color: #212529;
    --border-radius: 12px;
    --box-shadow: 0 8px 20px rgba(249, 207, 3, 0.15);
  }

  .signup-card {
    max-width: 1000px;
    margin: 40px auto;
    background-color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    border: none;
    transition: transform 0.3s ease;
  }

  .signup-card:hover {
    transform: translateY(-5px);
  }

  .card-header {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary-dark));
    color: white;
    padding: 25px 30px;
    border-bottom: none;
    position: relative;
    overflow: hidden;
  }

  .card-header::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 100%);
  }

  .card-header h3 {
    font-weight: 700;
    margin: 0;
    font-size: 1.8rem;
    position: relative;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
  }

  .card-body {
    padding: 30px;
    background-color: #fff;
  }

  .form-label {
    font-weight: 600;
    color: var(--secondary-color);
    margin-bottom: 8px;
    display: flex;
    justify-content: center;
  }

  .form-label i {
    margin-right: 8px;
    text-align: center;
    color: var(--primary-dark);
  }

  .form-control {
    border: 2px solid #e0e0e0;
    border-radius: var(--border-radius);
    padding: 20px 15px;
    transition: all 0.3s;
    font-size: 0.95rem;
    width: 80%;
  }

  .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(249, 207, 3, 0.25);
  }

  .btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-color));
    color: #d40511;
    border: none;
    border-radius: var(--border-radius);
    padding: 14px 0;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s;
    width: 100%;
    margin-top: 10px;
    box-shadow: 0 4px 8px rgba(249, 207, 3, 0.3);
    cursor: pointer;
  }

  .btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-color));
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(249, 207, 3, 0.4);
    color: #e70914;
  }

  .btn-primary:active {
    transform: translateY(0);
  }

  .invalid-feedback {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 5px;
    font-weight: 500;
  }

  .input-group-text {
    background-color: var(--light-color);
    border: 2px solid #e0e0e0;
    color: var(--secondary-color);
  }

  /* Animation pour les messages flash */
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .alert-message {
    animation: fadeIn 0.5s ease-out;
    border-radius: var(--border-radius);
    margin-bottom: 25px;
    border-left: 4px solid var(--primary-color);
  }

  /* Style modernisé pour SweetAlert */
  .swal2-popup {
    border-radius: var(--border-radius) !important;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
  }

  .swal2-title {
    color: var(--secondary-color) !important;
  }

  .swal2-confirm {
    background-color: var(--primary-color) !important;
    color: var(--secondary-color) !important;
  }

  /* Effet de vague décoratif */
  .wave-decoration {
    height: 15px;
    background: linear-gradient(90deg, transparent, var(--primary-color), transparent);
    opacity: 0.3;
    margin: 20px 0;
    border-radius: 50%;
  }

  /* Section en deux colonnes */
  .two-columns {
    display: grid;
    text-align: center;
    width: 100%;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }

  .column {
    display: flex;
    flex-direction: column;
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
    
    .card-header h3 {
      font-size: 1.5rem;
    }

    .two-columns {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card signup-card">
        <div class="card-header text-center">
          <h3><i class="fas fa-mail-bulk"></i> Enregistrer une agence du DHL</h3>
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

          <div class="wave-decoration"></div>

          <form class="needs-validation" method="POST" enctype="multipart/form-data" action="{{ route('agency.store') }}" novalidate>
            @csrf
            @method('POST')

            <div class="two-columns">
              <!-- Première colonne -->
              <div class="column">
                <div class="mb-3">
                  <label for="validationCustom001"  class="form-label">
                    <i class="fas fa-user"></i> Nom de l'agence
                  </label>
                  <input type="text" class="form-control" name="name" id="validationCustom001" placeholder="Entrez le nom de l'agence" required>
                  @error('name')
                  <div class="invalid-feedback d-block">
                    {{ $message }}
                  </div>
                  @enderror
                </div>

                {{-- <div class="mb-3">
                  <label for="validationCustom002" class="form-label">
                    <i class="fas fa-user-tag"></i> Prénoms du livreur
                  </label>
                  <input type="text" class="form-control" name="prenom" id="validationCustom002" placeholder="Entrez le prénom du livreur" required>
                  @error('prenom')
                  <div class="invalid-feedback d-block">
                    {{ $message }}
                  </div>
                  @enderror
                </div> --}}

                <div class="mb-3">
                  <label for="validationCustom005" class="form-label">
                    <i class="fas fa-map-marker-alt"></i> Localisation
                  </label>
                  <input type="text" class="form-control" name="commune" id="validationCustom005" placeholder="Commune de l'agence" required>
                  @error('commune')
                  <div class="invalid-feedback d-block">
                    {{ $message }}
                  </div>
                  @enderror
                </div>
              </div>

              <!-- Deuxième colonne -->
              <div class="column">
                <div class="mb-3">
                  <label for="validationCustom003" class="form-label">
                    <i class="fas fa-envelope"></i> Email de l'agence
                  </label>
                  <input type="email" class="form-control" name="email" id="validationCustom003" placeholder="Entrez email de l'agence" required>
                  @error('email')
                  <div class="invalid-feedback d-block">
                    {{ $message }}
                  </div>
                  @enderror
                </div>
                

                <div class="mb-3">
                  <label for="validationCustom004" class="form-label">
                    <i class="fas fa-phone"></i> Contact de l'agence
                  </label>
                  <input type="text" class="form-control" name="contact" id="validationCustom004" placeholder="Numéro de téléphone de l'agence" required>
                  @error('contact')
                  <div class="invalid-feedback d-block">
                    {{ $message }}
                  </div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="wave-decoration"></div>

            <div class="d-grid mt-4">
              <button class="btn btn-primary" type="submit">
                <i class="fas fa-plus-circle me-2"></i> Créer le compte
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
  @if(Session::has('success'))
    Swal.fire({
      icon: 'success',
      title: 'Succès',
      text: '{{ Session::get('success') }}',
      confirmButtonColor: '#f9cf03',
      background: '#ffffff',
      timer: 3000
    });
  @endif

  @if(Session::has('error'))
    Swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: '{{ Session::get('error') }}',
      confirmButtonColor: '#f9cf03',
      background: '#ffffff'
    });
  @endif
</script>

@endsection