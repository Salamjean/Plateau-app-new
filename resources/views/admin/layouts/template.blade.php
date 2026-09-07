<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="{{asset('assets/assets/img/logo plateau.png')}}" rel="icon">
  <title>KKS-Technologies-Dashboard</title>
  <link href="{{ asset("assets1/vendor/fontawesome-free/css/all.min.css") }}" rel="stylesheet" type="text/css">
  <link href="{{ asset("assets1/vendor/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css">
  <link href="{{ asset("assets1/css/ruang-admin.min.css") }}" rel="stylesheet">
  <style>
      @media (max-width: 768px) {
          .table-responsive {
              overflow-x: auto !important;
              -webkit-overflow-scrolling: touch;
              display: block;
              width: 100%;
          }
          .table-responsive table {
              min-width: 800px;
          }
          .table-responsive th, .table-responsive td {
              white-space: nowrap !important;
          }
      }
  </style>
</head>

<body id="page-top" >
  <div id="wrapper">
    <!-- Sidebar -->
    @include('admin.layouts.sidebar')
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        @include('admin.layouts.navigation')
        <!-- Topbar -->

        <!-- Container Fluid-->
        @yield('content')
        <!---Container Fluid-->
      </div>
       
    </div>
  </div>

  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="{{ asset("assets1/vendor/jquery/jquery.min.js") }}"></script>
  <script src="{{ asset("assets1/vendor/bootstrap/js/bootstrap.bundle.min.js") }}"></script>
  <script src="{{ asset("assets1/vendor/jquery-easing/jquery.easing.min.js") }}"></script>
  <script src="{{ asset("assets1/js/ruang-admin.min.js") }}"></script>
  <script src="{{ asset("assets1/vendor/chart.js/Chart.min.js") }}"></script>
  <script src="{{ asset("assets1/js/demo/chart-area-demo.js") }}"></script>  
</body>

</html>
