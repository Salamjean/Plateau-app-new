
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>DHL - Dashboard</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{asset('assetsDhl/assets/vendors/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('assetsDhl/assets/vendors/css/vendor.bundle.base.css')}}">
  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="{{asset('assetsDhl/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
  <link rel="stylesheet" href="{{asset('assetsDhl/assets/vendors/jvectormap/jquery-jvectormap.cs')}}s">
  <!-- End plugin css for this page -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="{{asset('assetsDhl/assets/css/demo/style.css')}}">
  <!-- End layout styles -->
   <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
</head>
<body>
<script src="{{asset('assetsDhl/assets/js/preloader.js')}}"></script>
  <div class="body-wrapper">
    <!-- partial:partials/_sidebar.html -->
    @include('dhl.layouts.sidebar')
    <!-- partial -->
    <div class="main-wrapper mdc-drawer-app-content">
      <!-- partial:partials/_navbar.html -->
      @include('dhl.layouts.navbar')
      <!-- partial -->
      <div class="page-wrapper mdc-toolbar-fixed-adjust">
        @yield('content')
      </div>
    </div>
  </div>
  <!-- plugins:js -->
  <script src="{{asset('assetsDhl/assets/vendors/js/vendor.bundle.base.js')}} "></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  <script src="{{asset('assetsDhl/assets/vendors/chartjs/Chart.min.js')}}"></script>
  <script src="{{asset('assetsDhl/assets/vendors/jvectormap/jquery-jvectormap.min.js')}}"></script>
  <script src="{{asset('assetsDhl/assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
  <!-- End plugin js for this page-->
  <!-- inject:js -->
  <script src="{{asset('assetsDhl/assets/js/material.js')}}"></script>
  <script src="{{asset('assetsDhl/assets/js/misc.js')}}"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="{{asset('assetsDhl/assets/js/dashboard.js')}}"></script>
  <!-- End custom js for this page-->
</body>
</html> 