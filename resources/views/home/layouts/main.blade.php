<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mairie du Plateau')</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" type="image/x-icon">

    <!-- CSS Vendors -->
    <link href="{{ asset('assets/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="{{ asset('assets/assets/vendor/aos/aos.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary: #1f4083;
            --secondary: #3b82f6;
            --accent: #10b981;
            --warning: #f59e0b;
            --dark: #0f172a;
            --light-bg: #f8fafc;
            --white: #ffffff;
            --glass: rgba(255, 255, 255, 0.9);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--dark);
            background-color: var(--white);
            overflow-x: hidden;
            width: 100%;
            position: relative;
        }

        /* --- NAVIGATION --- */
        .navbar-custom {
            padding: 0.8rem 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff !important;
            z-index: 1050;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 991px) {
            .navbar-collapse {
                background: #ffffff !important;
                padding: 1.5rem;
                border-radius: 24px;
                margin-top: 1rem;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
                border: 1px solid rgba(0, 0, 0, 0.05);
            }
        }

        .navbar-custom.scrolled {
            padding: 0.6rem 0;
            background: #ffffff !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        }

        .navbar-brand img {
            height: 52px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.02);
        }

        .navbar-brand .brand-text {
            border-left: 2px solid rgba(16, 58, 131, 0.15);
            padding-left: 15px;
            margin-left: 5px;
        }

        .navbar-brand .brand-text span {
            color: #103a83 !important;
        }

        .nav-link {
            font-weight: 600;
            color: #334155 !important;
            margin: 0 10px;
            font-size: 0.95rem;
            text-transform: none;
            letter-spacing: 0px;
            position: relative;
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            color: #103a83 !important;
        }

        .nav-link.active {
            color: #103a83 !important;
            font-weight: 700;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 3px;
            background: #103a83;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(-50%);
            border-radius: 10px;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 25px;
        }

        .btn-portal::after {
            display: none !important;
        }

        .dropdown-toggle::after {
            display: none !important;
        }

        .btn-portal {
            background: #103a83 !important;
            color: #ffffff !important;
            padding: 10px 28px !important;
            border-radius: 50px !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            text-transform: none !important;
            letter-spacing: 0.5px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border: 2px solid transparent !important;
            text-decoration: none !important;
            box-shadow: 0 4px 12px rgba(16, 58, 131, 0.15) !important;
        }

        .btn-portal:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 58, 131, 0.25) !important;
            background: #0d2e69 !important;
        }

        /* --- FOOTER --- */
        .footer {
            background: var(--dark);
            color: white;
            padding: 80px 0 30px;
            border-radius: 60px 60px 0 0;
            position: relative;
            margin-top: 100px;
        }

        @media (max-width: 991px) {
            .footer {
                padding: 60px 0 30px;
                border-radius: 40px 40px 0 0;
                margin-top: 60px;
            }
        }

        .footer-logo {
            height: 70px;
            margin-bottom: 2rem;
        }

        .social-link {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s;
            text-decoration: none;
        }

        .social-link:hover {
            background: var(--secondary);
            transform: translateY(-5px);
            color: white;
        }

        .px-10percent {
            padding-left: 10% !important;
            padding-right: 10% !important;
        }

        @stack('styles')
    </style>
</head>

<body>

    @include('home.layouts.navbar')

    <main>
        @yield('content')
    </main>

    @include('home.layouts.footer')


    <!-- SCRIPTS -->
    <script src="{{ asset('assets/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/assets/vendor/aos/aos.js') }}"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.getElementById('mainNav').classList.add('scrolled');
            } else {
                document.getElementById('mainNav').classList.remove('scrolled');
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
