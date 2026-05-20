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
            background: var(--primary);
            z-index: 1050;
        }

        @media (max-width: 991px) {
            .navbar-collapse {
                background: var(--primary);
                padding: 1rem;
                border-radius: 20px;
                margin-top: 1rem;
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            }
        }

        .navbar-custom.scrolled {
            padding: 0.6rem 0;
            background: var(--primary);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand img {
            height: 60px;
            width: auto;
            transition: transform 0.3s ease;
            filter: drop-shadow(0 0 5px rgba(255, 255, 255, 0.2));
        }

        .navbar-brand:hover img {
            transform: rotate(-5deg) scale(1.05);
        }

        .navbar-brand .brand-text {
            border-left: 2px solid rgba(255, 255, 255, 0.3);
            padding-left: 15px;
            margin-left: 5px;
        }

        .nav-link {
            font-weight: 700;
            color: rgba(255, 255, 255, 0.8) !important;
            margin: 0 12px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
        }

        .nav-link:hover {
            color: white !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            width: 0;
            height: 2px;
            background: var(--secondary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 80%;
        }

        .btn-portal {
            background: var(--white);
            color: var(--primary) !important;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
        }

        .btn-portal:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            background: var(--light-bg);
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
