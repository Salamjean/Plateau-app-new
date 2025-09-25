@extends('agent.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="{{asset('dasboard/edit.css')}}">
<div class="dashboard-container">
  <!-- Notifications -->
  @if ($errors->any())
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Erreur',
        html: `<ul class="text-left">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
        confirmButtonColor: '#007e00',
        background: 'white'
      });
    </script>
  @endif

  <div class="page-title">
    <h2>
      <i class="fas fa-edit me-2"></i>Modifier l'état de la demande
    </h2>
    <div class="page-actions">
      <a href="{{ route('agent.demandes.deces.index') }}" class="btn-action">
        <i class="fas fa-arrow-left btn-icon"></i>Retour
      </a>
    </div>
  </div>

  <div class="form-container">
    <!-- Carte d'information sur la demande -->
    <div class="info-card">
      <div class="info-header">
        <i class="fas fa-info-circle info-icon"></i>
        <h4 class="info-title">Informations sur la demande</h4>
      </div>
      <div class="info-grid">
        <div class="info-item">
          <span class="info-label">Reference de la demande</span>
          <span class="info-value">{{ $deces->reference }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Demandeur</span>
          <span class="info-value">{{ $deces->user->name.' '.$deces->user->prenom ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Date de demande</span>
          <span class="info-value">{{ $deces->created_at->format('d/m/Y à H:i') }}</span>
        </div>
        <div class="info-item">
          <span class="info-label">Statut actuel</span>
          <span class="info-value">
            @if($deces->etat == 'en attente')
              <span class="status-badge status-pending">En attente</span>
            @elseif($deces->etat == 'réçu')
              <span class="status-badge status-recu">Réçu</span>
            @else
              <span class="status-badge status-termine">Terminé</span>
            @endif
          </span>
        </div>
      </div>
    </div>

    <!-- Formulaire de modification -->
    <form action="{{ route('agent.demandes.deces.update', $deces->id) }}" method="POST">
      @csrf
      @method('POST')
      
      <div class="form-section">
        <h4 class="section-title">
          <i class="fas fa-cog"></i>Modifier le statut de la demande
        </h4>
        
        <div class="form-group">
          <label class="form-label">
            <i class="fas fa-tasks"></i>Nouveau statut
          </label>
          <select class="form-select" name="etat" required>
            <option value="">Sélectionnez un statut</option>
            @foreach($etats as $etat)
              <option value="{{ $etat }}" {{ $deces->etat == $etat ? 'selected' : '' }}>
                {{ ucfirst($etat) }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="action-buttons">
        <button type="submit" class="btn-action btn-secondary">
          <i class="fas fa-save btn-icon"></i>Enregistrer les modifications
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Animation d'entrée
    const formContainer = document.querySelector('.form-container');
    formContainer.style.opacity = '0';
    formContainer.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
      formContainer.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      formContainer.style.opacity = '1';
      formContainer.style.transform = 'translateY(0)';
    }, 100);

    // Confirmation avant soumission
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
      const etatSelect = document.querySelector('select[name="etat"]');
      if (etatSelect.value === 'terminé') {
        e.preventDefault();
        Swal.fire({
          title: 'Confirmer la finalisation',
          text: 'Êtes-vous sûr de vouloir marquer cette demande comme terminée ? Cette action est irréversible.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#1977cc',
          cancelButtonColor: 'red',
          confirmButtonText: 'Oui, terminer',
          cancelButtonText: 'Annuler'
        }).then((result) => {
          if (result.isConfirmed) {
            form.submit();
          }
        });
      }
    });
  });
</script>
@endsection