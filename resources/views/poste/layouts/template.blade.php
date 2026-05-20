<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Courrier - Espace Distribution</title>

    <!-- Polices et Icônes -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- CSS de base -->
    <link rel="stylesheet" href="{{ asset('assetsPoster/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" />

    <style>
        :root {
            --sidebar-width: 260px;
            --navbar-height: 70px;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fe;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            transition: background 0.3s ease;
        }

        /* État Mini-Sidebar */
        body.sidebar-mini {
            --sidebar-width: 80px;
        }

        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar override */
        .custom-sidebar {
            width: var(--sidebar-width) !important;
            transition: width var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        .page-content {
            padding: 25px;
            margin-top: var(--navbar-height);
            flex-grow: 1;
        }

        /* Navbar override */
        .modern-navbar {
            left: var(--sidebar-width) !important;
            transition: left var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        /* Reset legacy MDC conflicts */
        .mdc-drawer-app-content {
            margin-left: 0 !important;
            width: 100% !important;
            display: block !important;
        }

        @media (max-width: 991px) {
            :root {
                --sidebar-width: 0px;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            body.sidebar-mini {
                --sidebar-width: 260px;
            }

            body:not(.sidebar-mini) .custom-sidebar {
                transform: translateX(-100%);
            }
        }
    </style>
</head>

<body>

    <div class="layout-wrapper">
        <!-- Sidebar -->
        @include('poste.layouts.sidebar_new')

        <div class="main-content">
            <!-- Navbar -->
            @include('poste.layouts.navbar_new')

            <!-- Main Content -->
            <div class="page-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assetsPoster/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const sidebarToggle = document.getElementById('sidebarToggle');
            const profileMenuBtn = document.getElementById('profileMenuBtn');
            const profileDropdown = document.getElementById('profileDropdown');

            if (localStorage.getItem('sidebar-mini') === 'true') {
                body.classList.add('sidebar-mini');
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    body.classList.toggle('sidebar-mini');
                    localStorage.setItem('sidebar-mini', body.classList.contains('sidebar-mini'));
                });
            }

            if (profileMenuBtn && profileDropdown) {
                profileMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const isVisible = profileDropdown.style.visibility === 'visible';
                    profileDropdown.style.visibility = isVisible ? 'hidden' : 'visible';
                    profileDropdown.style.opacity = isVisible ? '0' : '1';
                    profileDropdown.style.transform = isVisible ? 'translateY(10px)' : 'translateY(0)';
                });
                document.addEventListener('click', function() {
                    profileDropdown.style.visibility = 'hidden';
                    profileDropdown.style.opacity = '0';
                    profileDropdown.style.transform = 'translateY(10px)';
                });
            }

            const hasSubmenu = document.querySelectorAll('.has-submenu');
            hasSubmenu.forEach(item => {
                item.addEventListener('click', function(e) {
                    if (body.classList.contains('sidebar-mini')) return;
                    e.preventDefault();
                    e.stopPropagation();
                    const submenuId = this.getAttribute('data-submenu');
                    const submenu = document.getElementById(submenuId);
                    const chevron = this.querySelector('.chevron');
                    if (submenu) {
                        const isActive = submenu.classList.contains('active');
                        submenu.classList.toggle('active');
                        if (chevron) {
                            chevron.style.transform = isActive ? 'rotate(0deg)' : 'rotate(90deg)';
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
