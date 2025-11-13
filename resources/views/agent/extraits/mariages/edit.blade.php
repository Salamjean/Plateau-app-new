@extends('agent.layouts.template')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="{{asset('dasboard/edit.css')}}">

<div class="dashboard-container">
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
     <a href="{{ route('agent.demandes.wedding.index') }}" class="btn-action">
       <i class="fas fa-arrow-left btn-icon"></i>Retour
     </a>
   </div>
 </div>

 <div class="form-container">
   <div class="info-card">
     <div class="info-header">
       <i class="fas fa-info-circle info-icon"></i>
       <h4 class="info-title">Informations sur la demande</h4>
     </div>
     <div class="info-grid">
       <div class="info-item">
         <span class="info-label">Demandeur</span>
         <span class="info-value">{{ $mariage->user->name.' '.$mariage->user->prenom ?? 'N/A' }}</span>
       </div>
       <div class="info-item">
         <span class="info-label">Date de demande</span>
         <span class="info-value">{{ $mariage->created_at->format('d/m/Y à H:i') }}</span>
       </div>
       <div class="info-item">
         <span class="info-label">Statut actuel</span>
         <span class="info-value">
           @if($mariage->etat == 'en attente')
             <span class="status-badge status-pending">En attente</span>
           @elseif($mariage->etat == 'réçu')
             <span class="status-badge status-recu">En cours</span>
           @elseif($mariage->etat == 'rejetée')
             <span class="status-badge status-rejected">Rejetée</span>
           @else
             <span class="status-badge status-termine">Terminé</span>
           @endif
         </span>
       </div>
       
       @if($mariage->etat == 'rejetée' && $mariage->motif_de_rejet)
       <div class="info-item info-item-full">
         <span class="info-label">Motif de Rejet</span>
         <span class="info-value" style="white-space: pre-wrap;">{{ $mariage->motif_de_rejet }}</span>
       </div>
       @endif
     </div>
   </div>

   <form action="{{ route('agent.demandes.wedding.update', $mariage->id) }}" method="POST" id="update-etat-form">
     @csrf
     @method('POST')
     
     <input type="hidden" name="motif_de_rejet" id="motif_de_rejet_input">
     
     <div class="form-section">
       <h4 class="section-title">
         <i class="fas fa-cog"></i>Modifier le statut de la demande
       </h4>
       
       <div class="form-group">
         <label class="form-label">
           <i class="fas fa-tasks"></i>Nouveau statut
         </label>
         <select class="form-select" name="etat" id="etat_select" required>
           <option value="">Sélectionnez un statut</option>
           @foreach($etats as $etat)
             <option value="{{ $etat }}" {{ $mariage->etat == $etat ? 'selected' : '' }}>
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
   // ... (partie animation, inchangée) ...
   const formContainer = document.querySelector('.form-container');
   formContainer.style.opacity = '0';
   formContainer.style.transform = 'translateY(20px)';
   setTimeout(() => {
     formContainer.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
     formContainer.style.opacity = '1';
     formContainer.style.transform = 'translateY(0)';
   }, 100);

   // --- Logique de soumission mise à jour ---
   const form = document.getElementById('update-etat-form');
   const etatSelect = document.getElementById('etat_select');
   
   form.addEventListener('submit', function(e) {
     // On empêche la soumission pour TOUS les cas afin de vérifier
     e.preventDefault();
     
     const selectedEtat = etatSelect.value;
     
     // CAS 1: 'rejetée'
     if (selectedEtat === 'rejetée') {
       Swal.fire({
         title: 'Motif de Rejet',
         text: 'Veuillez saisir le motif du rejet :',
         input: 'textarea',
         inputPlaceholder: 'Expliquez pourquoi la demande est rejetée...',
         inputAttributes: {
           'aria-label': 'Saisir le motif ici'
         },
         showCancelButton: true,
         confirmButtonColor: '#d33', // Rouge pour rejeter
         cancelButtonColor: '#3085d6',
         confirmButtonText: 'Oui, rejeter',
         cancelButtonText: 'Annuler',
         inputValidator: (value) => {
           if (!value) {
             return 'Vous devez obligatoirement saisir un motif !'
           }
         }
       }).then((result) => {
         if (result.isConfirmed) {
           // Si confirmé, on met la valeur du motif dans le champ caché
           document.getElementById('motif_de_rejet_input').value = result.value;
           // Et on soumet le formulaire
           form.submit();
         }
       });
       
     // CAS 2: 'terminé' (votre logique existante)
     } else if (selectedEtat === 'terminé') {
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
       
     // CAS 3: Tous les autres statuts ('en attente', 'réçu')
     } else {
       // Pas besoin de confirmation, on soumet directement
       form.submit();
     }
   });
 });
</script>

<style>
 .status-badge.status-rejected {
   background-color: #ffebee; /* Fond rouge très clair */
   color: #d32f2f; /* Texte rouge foncé */
   border: 1px solid #d32f2f; /* Bordure rouge foncé */
 }
 .info-item-full {
   grid-column: 1 / -1; /* Fait en sorte que le motif prenne toute la largeur */
 }
</style>
@endsection