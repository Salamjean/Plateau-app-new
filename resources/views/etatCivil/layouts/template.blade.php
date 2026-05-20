<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Espace - Etat - Civil</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assetsPoste/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsPoste/assets/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assetsPoste/assets/vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsPoste/assets/vendors/jvectormap/jquery-jvectormap.css') }}">
    <!-- End plugin css for this page -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/assets/css/demo/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" />

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Masquer dashboard.js si nécessaire pour éviter les conflits */
        .chartjs-hidden-iframe {
            display: none !important;
        }

        /* Compensation pour la navbar fixed */
        .page-wrapper {
            margin-top: 70px;
            /* Ajustez selon la hauteur de votre navbar */
            padding: 20px 0;
        }

        /* Main wrapper adjustment for custom sidebar */
        .main-wrapper {
            margin-left: 260px;
            transition: margin-left 0.3s ease;
        }

        /* Style when sidebar is collapsed on desktop */
        .body-wrapper.sidebar-collapsed .main-wrapper {
            margin-left: 70px;
        }

        @media (max-width: 991px) {
            .main-wrapper {
                margin-left: 0 !important;
            }

            .body-wrapper.sidebar-collapsed .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <script src="{{ asset('assetsPoste/assets/js/preloader.js') }}"></script>
    <div class="body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        @include('etatCivil.layouts.sidebar_new')
        <!-- partial -->
        <div class="main-wrapper mdc-drawer-app-content">
            <!-- partial:partials/_navbar.html -->
            @include('etatCivil.layouts.navbar')
            <!-- partial -->
            <div class="page-wrapper">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- plugins:js -->
    <script src="{{ asset('assetsPoste/assets/vendors/js/vendor.bundle.base.js') }} "></script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('assetsPoste/assets/vendors/jvectormap/jquery-jvectormap.min.js') }}"></script>
    <script src="{{ asset('assetsPoste/assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="{{ asset('assetsPoste/assets/js/material.js') }}"></script>
    <script src="{{ asset('assetsPoste/assets/js/misc.js') }}"></script>
    <!-- endinject -->

    @stack('scripts')
</body>


</html>
