<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Plateau App - User</title>
   
   <!-- Favicon -->
   <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
   
   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="{{asset('userAssets/css/bootstrap.min.css')}}" />
   
   <!-- Custom Modern CSS (Primary) -->
   <link rel="stylesheet" href="{{asset('userAssets/css/custom.css')}}" />
   
   <!-- Font Awesome 6.5.1 -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
   <!-- SweetAlert2 -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
   <div class="inner_container">
      <div class="sidebar-overlay" id="sidebarOverlay"></div>
      <!-- Sidebar -->
      @include('user.layouts.sidebar')
      
      <div id="content">
         <!-- Topbar -->
         @include('user.layouts.navbar')
         
         <div class="dashboard_inner">
            <div class="container-fluid">
               @yield('content')
            </div>
         </div>
      </div>
   </div>

   <!-- jQuery & Scripts -->
   <script src="{{asset('userAssets/js/jquery.min.js')}}"></script>
   <script src="{{asset('userAssets/js/popper.min.js')}}"></script>
   <script src="{{asset('userAssets/js/bootstrap.min.js')}}"></script>
   <script src="{{asset('userAssets/js/perfect-scrollbar.min.js')}}"></script>
   
   <script>
      $(document).ready(function () {
         var isMobile = function() { return $(window).width() < 992; };

         // Ouvre/ferme la sidebar
         function toggleSidebar() {
            $('#sidebar').toggleClass('active');
            $('#sidebarOverlay').toggleClass('active');
            if (!isMobile()) {
               $('#content').toggleClass('active');
            }
            // Bloquer/débloquer le scroll du body sur mobile
            if (isMobile()) {
               $('body').toggleClass('sidebar-open', $('#sidebar').hasClass('active'));
            }
         }

         $('#sidebarCollapse, #sidebarClose, #sidebarOverlay').on('click', toggleSidebar);

         // Fermer la sidebar au clic d'un lien final sur mobile (ignore les dropdowns)
         if (isMobile()) {
            $('#sidebar a:not([data-toggle="collapse"])').on('click', function() {
               if ($('#sidebar').hasClass('active')) {
                  toggleSidebar();
               }
            });
         }

         // Adapter au redimensionnement
         $(window).on('resize', function() {
            if (!isMobile()) {
               $('body').removeClass('sidebar-open');
               $('#sidebarOverlay').removeClass('active');
            }
         });
      });
   </script>
   
   @stack('scripts')

   @if(Auth::check() && empty(Auth::user()->contact))
   <script>
       document.addEventListener('DOMContentLoaded', function () {
           Swal.fire({
               title: 'Numéro de téléphone requis',
               html: `
                   <p class="text-muted small mb-3">Afin de vous notifier de l'avancée de vos démarches administratives, votre numéro de téléphone est obligatoire.</p>
                   <div class="input-group mb-3 text-left">
                       <select id="swal-indicatif" class="form-control" style="max-width: 120px; border-top-right-radius: 0; border-bottom-right-radius: 0;">
                           <option value="+225" selected>🇨🇮 +225</option>
                           <option value="+33">🇫🇷 +33</option>
                           <option value="+1">🇺🇸 +1</option>
                       </select>
                       <input type="text" id="swal-contact" class="form-control" placeholder="Ex: 0700000000" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                   </div>
               `,
               icon: 'info',
               allowOutsideClick: false,
               allowEscapeKey: false,
               showCancelButton: false,
               confirmButtonText: 'Enregistrer mon numéro <i class="fas fa-check ml-1"></i>',
               confirmButtonColor: '#1977cc',
               preConfirm: () => {
                   const indicatif = document.getElementById('swal-indicatif').value;
                   const contact = document.getElementById('swal-contact').value;
                   if (!contact) {
                       Swal.showValidationMessage('Veuillez entrer votre numéro de téléphone');
                       return false;
                   }
                   if (contact.length > 10) {
                       Swal.showValidationMessage('Le numéro ne doit pas dépasser 10 chiffres');
                       return false;
                   }
                   return { indicatif: indicatif, contact: contact }
               }
           }).then((result) => {
               if (result.isConfirmed) {
                   Swal.fire({
                       title: 'Enregistrement...',
                       allowOutsideClick: false,
                       didOpen: () => {
                           Swal.showLoading()
                       }
                   });

                   fetch('{{ route("user.profile.update.contact") }}', {
                       method: 'POST',
                       headers: {
                           'Content-Type': 'application/json',
                           'X-CSRF-TOKEN': '{{ csrf_token() }}'
                       },
                       body: JSON.stringify(result.value)
                   })
                   .then(response => response.json())
                   .then(data => {
                       if (data.success) {
                           Swal.fire({
                               icon: 'success',
                               title: 'Succès',
                               text: data.message,
                               timer: 2000,
                               showConfirmButton: false
                           }).then(() => {
                               window.location.reload();
                           });
                       } else {
                           Swal.fire('Erreur', "Une erreur est survenue lors de l'enregistrement.", 'error').then(() => {
                               window.location.reload();
                           });
                       }
                   }).catch(error => {
                       Swal.fire('Erreur', 'Impossible de joindre le serveur.', 'error').then(() => {
                           window.location.reload();
                       });
                   });
               }
           });
       });
   </script>
   @endif
</body>
</html>