@extends('mairie.layouts.template')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    --border-radius: 12px;
    --box-shadow: 0 8px 24px rgba(0, 51, 196, 0.12);
    --transition: all 0.3s ease;
  }

  body {
    background: linear-gradient(135deg, #f5f7ff 0%, #e3e8ff 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    min-height: 100vh;
  }

  .page-title {
    color: var(--primary-dark);
    font-weight: 700;
    margin-bottom: 20px;
    font-size: 1.8rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
    font-weight: 500;
  }

  .breadcrumb-item a:hover {
    color: var(--primary-dark);
    text-decoration: underline;
  }

  .breadcrumb-item.active {
    color: var(--primary-dark);
    font-weight: 600;
  }

  .card {
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    margin-bottom: 25px;
    background-color: var(--secondary-color);
    overflow: hidden;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0, 51, 196, 0.15);
  }

  .card-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    color: var(--secondary-color);
    border-radius: 0 !important;
    padding: 18px 25px;
    font-weight: 600;
    border-bottom: none;
    font-size: 1.2rem;
  }

  .card-body {
    padding: 30px;
  }

  .info-section {
    background-color: #f8f9ff;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 25px;
    border-left: 4px solid var(--primary-color);
  }

  .info-title {
    color: var(--primary-dark);
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .info-detail {
    margin-bottom: 10px;
    display: flex;
  }

  .info-label {
    font-weight: 600;
    color: #495057;
    min-width: 140px;
  }

  .info-value {
    color: #212529;
    flex: 1;
  }

  .form-label {
    font-weight: 600;
    color: var(--primary-dark);
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .form-control {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 15px;
    transition: var(--transition);
    font-size: 1rem;
  }

  .form-control:focus {
    border-color: var(--primary-light);
    box-shadow: 0 0 0 0.2rem rgba(0, 51, 196, 0.15);
  }

  .btn {
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    border: none;
    box-shadow: 0 4px 8px rgba(0, 51, 196, 0.2);
  }

  .btn-primary:hover {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 51, 196, 0.25);
  }

  .btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    border: none;
    box-shadow: 0 4px 8px rgba(108, 117, 125, 0.2);
  }

  .btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268 0%, #495057 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(108, 117, 125, 0.25);
  }

  .divider {
    height: 2px;
    background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%);
    margin: 30px 0;
    border: none;
  }

  @media (max-width: 768px) {
    .card-body {
      padding: 20px;
    }
    
    .info-detail {
      flex-direction: column;
    }
    
    .info-label {
      min-width: auto;
      margin-bottom: 5px;
    }
    
    .btn {
      width: 100%;
      margin-bottom: 10px;
    }
    
    .d-flex.justify-content-between {
      flex-direction: column;
    }
  }
</style>

<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="page-title"><i class="fas fa-calendar-check me-2"></i>Confirmation de modification</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('mairie.dashboard') }}"><i class="fas fa-home"></i> Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('mairie.rendezvous.index') }}">Rendez-vous</a></li>
            <li class="breadcrumb-item active">Confirmation</li>
        </ol>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Modifier le rendez-vous</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rendezvous.update', $rendezvous->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-section">
                                    <h6 class="info-title"><i class="fas fa-user-tie"></i> Informations époux</h6>
                                    <div class="info-detail">
                                        <span class="info-label">Nom complet:</span>
                                        <span class="info-value">{{ $rendezvous->nom_epoux }} {{ $rendezvous->prenom_epoux }}</span>
                                    </div>
                                    <div class="info-detail">
                                        <span class="info-label">Date de naissance:</span>
                                        <span class="info-value">{{ $rendezvous->date_naissance_epoux }}</span>
                                    </div>
                                    <div class="info-detail">
                                        <span class="info-label">Lieu de naissance:</span>
                                        <span class="info-value">{{ $rendezvous->lieu_naissance_epoux }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-section">
                                    <h6 class="info-title"><i class="fas fa-user-female"></i> Informations épouse</h6>
                                    <div class="info-detail">
                                        <span class="info-label">Nom complet:</span>
                                        <span class="info-value">{{ $rendezvous->nom_epouse }} {{ $rendezvous->prenom_epouse }}</span>
                                    </div>
                                    <div class="info-detail">
                                        <span class="info-label">Date de naissance:</span>
                                        <span class="info-value">{{ $rendezvous->date_naissance_epouse }}</span>
                                    </div>
                                    <div class="info-detail">
                                        <span class="info-label">Lieu de naissance:</span>
                                        <span class="info-value">{{ $rendezvous->lieu_naissance_epouse }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="divider">

                        <h5 class="info-title mb-4"><i class="fas fa-calendar-alt"></i> Modifier les détails du rendez-vous</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="date_mariage_souhaitee" class="form-label">
                                    <i class="fas fa-calendar-day"></i> Nouvelle date souhaitée
                                </label>
                                <input type="date" name="date_mariage_souhaitee" id="date_mariage_souhaitee" 
                                       class="form-control" value="{{ $rendezvous->date_mariage_souhaitee }}" required>
                                <div class="form-text">Sélectionnez la nouvelle date pour votre mariage</div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="heure_souhaitee" class="form-label">
                                    <i class="fas fa-clock"></i> Nouvelle heure souhaitée
                                </label>
                                <input type="time" name="heure_souhaitee" id="heure_souhaitee" 
                                       class="form-control" value="{{ $rendezvous->heure_souhaitee }}" required>
                                <div class="form-text">Sélectionnez la nouvelle heure pour votre mariage</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('mairie.rendezvous.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle me-1"></i> Confirmer la modification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  // Ajouter un effet de focus amélioré pour les champs de formulaire
  document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
      this.parentElement.classList.add('focused');
    });
    
    input.addEventListener('blur', function() {
      this.parentElement.classList.remove('focused');
    });
  });
</script>
@endsection