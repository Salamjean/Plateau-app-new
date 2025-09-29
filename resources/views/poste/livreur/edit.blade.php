@extends('poste.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<style>
  :root {
    --primary-color: #1977cc;
    --primary-dark: #1977cc;
    --secondary-color: #ea8c51;
    --light-color: #f8f9fa;
    --dark-color: #212529;
    --border-radius: 12px;
    --box-shadow: 0 8px 20px rgba(6, 99, 78, 0.15);
  }

  .edit-card {
    max-width: 1000px;
    margin: 40px auto;
    background-color: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    border: none;
    transition: transform 0.3s ease;
  }

  .edit-card:hover {
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
    color: var(--dark-color);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
  }

  .form-label i {
    margin-right: 8px;
    text-align: center;
    color: var(--primary-dark);
    width: 20px;
  }

  .form-control {
    border: 2px solid #e0e0e0;
    border-radius: var(--border-radius);
    padding: 12px 80px;
    transition: all 0.3s;
    font-size: 0.95rem;
  }

  .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(25, 119, 204, 0.25);
  }

  .btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    border: none;
    border-radius: var(--border-radius);
    padding: 12px 30px;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s;
    margin-top: 10px;
    box-shadow: 0 4px 8px rgba(25, 119, 204, 0.3);
    cursor: pointer;
  }

  .btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(25, 119, 204, 0.4);
    color: white;
  }

  .btn-secondary {
    background: linear-gradient(135deg, #6c757d, #5a6268);
    color: white;
    border: none;
    border-radius: var(--border-radius);
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s;
    margin-top: 10px;
    margin-right: 10px;
  }

  .btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268, #6c757d);
    transform: translateY(-2px);
    color: white;
  }

  .invalid-feedback {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 5px;
    font-weight: 500;
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

  /* Style pour SweetAlert */
  .swal2-popup {
    border-radius: var(--border-radius) !important;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
  }

  .swal2-title {
    color: var(--dark-color) !important;
  }

  /* Section en deux colonnes */
  .two-columns {
    display: flex;
    justify-content: space-around;
    padding: 30px 150px;
    gap: 20px;
    margin-bottom: 20px;
  }

  .column {
    display: flex;
    flex-direction: column;
  }

  /* Section disponibilité */
  .availability-section {
    background-color: #f8f9fa;
    border-radius: var(--border-radius);
    padding: 20px;
    margin: 20px 0;
    border-left: 4px solid var(--secondary-color);
  }

  .form-check {
    margin-bottom: 15px;
  }

  .form-check-input {
    width: 20px;
    height: 20px;
    margin-right: 10px;
  }

  .form-check-label {
    font-weight: 600;
    color: var(--dark-color);
    display: flex;
    align-items: center;
  }

  /* Badge pour l'état actuel */
  .current-status {
    display: inline-block;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: 600;
    margin-left: 10px;
  }

  .status-available {
    background-color: #d4edda;
    color: #155724;
  }

  .status-unavailable {
    background-color: #f8d7da;
    color: #721c24;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .edit-card {
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

    .btn-group {
      width: 100%;
    }

    .btn {
      width: 100%;
      margin-bottom: 10px;
    }
  }
</style>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card edit-card">
        <div class="card-header text-center">
          <h3><i class="fas fa-user-edit me-2"></i> Modifier le livreur</h3>
        </div>
        
        <div class="card-body">
          <!-- Messages de notification -->
          @if (Session::get('success'))
            <div class="alert alert-success alert-message">
              <i class="fas fa-check-circle me-2"></i> {{ Session::get('success') }}
            </div>
          @endif

          @if (Session::get('error'))
            <div class="alert alert-danger alert-message">
              <i class="fas fa-exclamation-circle me-2"></i> {{ Session::get('error') }}
            </div>
          @endif

          <!-- Informations du livreur -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="d-flex align-items-center justify-content-between">
                <h5 class="text-primary">
                  <i class="fas fa-user me-2"></i> Le livreur : {{ $livreur->name }} {{ $livreur->prenom }}
                </h5>
                <span class="current-status {{ $livreur->disponible ? 'status-available' : 'status-unavailable' }}">
                  {{ $livreur->disponible ? 'Disponible' : 'Indisponible' }}
                </span>
              </div>
            </div>
          </div>

          <form class="needs-validation" method="POST" action="{{ route('poste.livreur.update', $livreur->id) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="two-columns">
              <!-- Première colonne -->
              <div class="column">
                <div class="mb-3">
                  <label for="name" class="form-label">
                    <i class="fas fa-user"></i> Nom du livreur
                  </label>
                  <input type="text" class="form-control" name="name" id="name" 
                         value="{{ old('name', $livreur->name) }}" placeholder="Entrez le nom du livreur" required>
                  @error('name')
                  <div class="invalid-feedback d-block">
                    {{ $message }}
                  </div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email du livreur
                  </label>
                  <input type="email" class="form-control" name="email" id="email" 
                         value="{{ old('email', $livreur->email) }}" placeholder="Entrez son email" required>
                  @error('email')
                  <div class="invalid-feedback d-block">
                    {{ $message }}
                  </div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="commune" class="form-label">
                    <i class="fas fa-map-marker-alt"></i> Lieu de résidence
                  </label>
                  <input type="text" class="form-control" name="commune" id="commune" 
                         value="{{ old('commune', $livreur->commune) }}" placeholder="Lieu de résidence" required>
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
                  <label for="prenom" class="form-label">
                    <i class="fas fa-user-tag"></i> Prénoms du livreur
                  </label>
                  <input type="text" class="form-control" name="prenom" id="prenom" 
                         value="{{ old('prenom', $livreur->prenom) }}" placeholder="Entrez le prénom du livreur" required>
                  @error('prenom')
                  <div class="invalid-feedback d-block">
                    {{ $message }}
                  </div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="contact" class="form-label">
                    <i class="fas fa-phone"></i> Contact du livreur
                  </label>
                  <input type="text" class="form-control" name="contact" id="contact" 
                         value="{{ old('contact', $livreur->contact) }}" placeholder="Numéro de téléphone" required>
                  @error('contact')
                  <div class="invalid-feedback d-block">
                    {{ $message }}
                  </div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="cas_urgence" class="form-label">
                    <i class="fas fa-phone-alt"></i> Contact en cas d'urgence
                  </label>
                  <input type="text" class="form-control" name="cas_urgence" id="cas_urgence" 
                         value="{{ old('cas_urgence', $livreur->cas_urgence) }}" placeholder="Numéro d'urgence">
                  @error('cas_urgence')
                  <div class="invalid-feedback d-block">
                    {{ $message }}
                  </div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Boutons d'action -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mt-4">
              <div>
                <a href="{{ route('delivery.index') }}" class="btn btn-secondary">
                  <i class="fas fa-arrow-left me-2"></i> Retour à la liste
                </a>
              </div>
              
              <div class="d-flex flex-wrap">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-2"></i> Enregistrer les modifications
                </button>
              </div>
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
      title: 'Modification réussie',
      text: '{{ Session::get('success') }}',
      confirmButtonColor: '#1977cc',
      background: '#ffffff',
      timer: 3000
    });
  @endif

  @if(Session::has('error'))
    Swal.fire({
      icon: 'error',
      title: 'Erreur',
      text: '{{ Session::get('error') }}',
      confirmButtonColor: '#1977cc',
      background: '#ffffff'
    });
  @endif

  // Confirmation avant réinitialisation
  document.querySelector('button[type="reset"]').addEventListener('click', function(e) {
    e.preventDefault();
    Swal.fire({
      title: 'Réinitialiser le formulaire ?',
      text: 'Toutes les modifications non enregistrées seront perdues.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#1977cc',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Oui, réinitialiser',
      cancelButtonText: 'Annuler'
    }).then((result) => {
      if (result.isConfirmed) {
        document.querySelector('form').reset();
        Swal.fire('Réinitialisé !', 'Le formulaire a été réinitialisé.', 'success');
      }
    });
  });
</script>

@endsection