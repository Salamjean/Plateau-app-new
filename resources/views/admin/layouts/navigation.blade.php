<nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Bouton Sidebar -->
  <button id="sidebarToggleTop" class="btn btn-primary  rounded-circle mr-3">
    <i class="fa fa-bars"></i>
  </button>



  <!-- Icônes du Topbar -->
  <ul class="navbar-nav ml-auto align-items-center">
    
    
  
  <!-- Modal pour afficher les détails de l'alerte -->
  

    <!-- Messages -->

    <!-- Profil Utilisateur -->
    <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" 
           aria-haspopup="true" aria-expanded="false">
          <!-- Image de profil -->
          @php
            $user = Auth::guard('admin')->user();
          @endphp
          <img class="img-profile rounded-circle" src="{{ asset('assets/images/profiles/kks.jpeg') }}" 
               alt="Logo {{ $user->name }}">
          <span class="ml-2 d-none d-lg-inline text-white small">{{ $user->name }}</span>
      </a>
      <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
        
        <a class="dropdown-item" href="{{ route('admin.logout') }}">
          <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Déconnexion
        </a>
      </div>
    </li>
  </ul>
</nav>
<script>
document.querySelectorAll('.alert-item').forEach(item => {
  item.addEventListener('click', function(event) {
    const message = item.getAttribute('data-message');
    const date = item.getAttribute('data-date');
    const id = item.getAttribute('data-id');

    // Affichage du pop-up avec SweetAlert2
    Swal.fire({
      title: 'Détail de l\'alerte',
      html: `<p><strong>Message :</strong> ${message}</p><p><strong>Créée le :</strong> ${date}</p>`,
      icon: 'info',
      confirmButtonText: 'Fermer',
      didClose: () => markAsRead(id, item)  // Marquer l'alerte comme lue après la fermeture
    });
  });
});

function markAsRead(alertId, alertItem) {
  // Envoyer une requête POST pour marquer l'alerte comme lue
  fetch(`/alerts/${alertId}/mark-as-read`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json',
    },
  }).then(response => {
    if (response.ok) {
      console.log('Alerte marquée comme lue');
      
      // Supprimer l'alerte du DOM
      alertItem.remove();
      
      // Mettre à jour le compteur d'alertes
      updateAlertBadge();
    } else {
      console.error('Erreur lors de la mise à jour de l\'alerte.');
    }
  }).catch(error => console.error('Erreur : ', error));
}

function updateAlertBadge() {
  // Récupérer le nombre actuel d'alertes
  const alertCount = document.querySelectorAll('.alert-item').length;
  
  // Mettre à jour le badge d'alertes avec le nouveau nombre
  const badge = document.querySelector('.badge-counter');
  
  if (badge) {
    // Si le nombre d'alertes est 0, masquer le badge
    if (alertCount === 0) {
      badge.style.display = 'none';
    } else {
      badge.style.display = 'inline-block';
      badge.textContent = alertCount;
    }
  }
}

</script>

