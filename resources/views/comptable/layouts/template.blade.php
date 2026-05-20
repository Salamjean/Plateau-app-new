<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Comptable - Régie - Plateau</title>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assetsPoste/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsPoste/assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assetsPoste/assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsPoste/assets/vendors/jvectormap/jquery-jvectormap.css') }}">
    <!-- End plugin css for this page -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assetsPoste/assets/css/demo/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" />

    <style>
        body {
            background: #f7fafc;
        }
    </style>
</head>

<body>
    <script src="{{ asset('assetsPoste/assets/js/preloader.js') }}"></script>

    <!-- Sidebar -->
    @include('comptable.layouts.sidebar_new')

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Navbar -->
        @include('comptable.layouts.navbar_new')

        <!-- Page Content -->
        <div class="page-wrapper" style="background: #f7fafc;">
            @yield('content')
        </div>
    </div>

    <!-- plugins:js -->
    <script src="{{ asset('assetsPoste/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <script src="{{ asset('assetsPoste/assets/vendors/chartjs/Chart.min.js') }}"></script>
    <script src="{{ asset('assetsPoste/assets/vendors/jvectormap/jquery-jvectormap.min.js') }}"></script>
    <script src="{{ asset('assetsPoste/assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="{{ asset('assetsPoste/assets/js/material.js') }}"></script>
    <script src="{{ asset('assetsPoste/assets/js/misc.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="{{ asset('assetsPoste/assets/js/dashboard.js') }}"></script>
    <!-- End custom js for this page-->
</body>

</html>
