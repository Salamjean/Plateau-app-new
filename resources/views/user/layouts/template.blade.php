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
         // Toggle Sidebar
         $('#sidebarCollapse, #sidebarClose, #sidebarOverlay').on('click', function () {
            $('#sidebar').toggleClass('active');
            $('#sidebarOverlay').toggleClass('active');
            $('#content').toggleClass('active');
         });
      });
   </script>
   
   @stack('scripts')
</body>
</html>