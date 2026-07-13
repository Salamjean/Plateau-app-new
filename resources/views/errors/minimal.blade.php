<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Plateau Smart City</title>
    <link rel="shortcut icon" href="{{ asset('assets/assets/img/logo plateau.png') }}" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #1977cc;
            --primary-dark: #1f4083;
            --bg-color: #f4f7f6;
            --text-color: #2c3e50;
        }
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-color);
        }
        .error-container {
            text-align: center;
            padding: 50px 40px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            max-width: 600px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }
        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), #4facfe);
        }
        .error-code {
            font-size: 130px;
            font-weight: 900;
            line-height: 1;
            margin: 0;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }
        .error-message {
            font-size: 26px;
            font-weight: 800;
            margin-bottom: 15px;
            color: var(--primary-dark);
        }
        .error-description {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 35px;
            line-height: 1.6;
        }
        .btn-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary);
            color: white;
            padding: 14px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(25, 119, 204, 0.3);
        }
        .btn-home i {
            margin-right: 8px;
        }
        .btn-home:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(25, 119, 204, 0.4);
            color: white;
        }
        .error-illustration {
            font-size: 90px;
            color: #e0e6ed;
            margin-bottom: 20px;
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .logo {
            margin-bottom: 35px;
        }
        .logo img {
            height: 55px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('assets/assets/img/plateau-mart1.png') }}" alt="Plateau Smart City" style="background-color:#1977CC">
            </a>
        </div>
        <div class="error-illustration">
            @yield('icon')
        </div>
        <h1 class="error-code">@yield('code')</h1>
        <div class="error-message">@yield('message')</div>
        <div class="error-description">@yield('description')</div>
        <a href="{{ url('/') }}" class="btn-home">
            <i class="fas fa-home"></i> Retour à l'accueil
        </a>
    </div>
</body>
</html>
