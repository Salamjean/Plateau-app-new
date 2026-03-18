<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supprimer mon compte - Plateau App</title>
    
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('userAssets/css/bootstrap.min.css')}}" />
    
    <style>
        :root {
            --primary-color: #001a41;
            --secondary-color: #ff5a1f;
            --danger-color: #dc3545;
            --bg-gradient: linear-gradient(135deg, #001a41 0%, #000d21 100%);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #fff;
        }

        .delete-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container img {
            max-width: 120px;
        }

        .warning-icon {
            font-size: 60px;
            color: var(--secondary-color);
            margin-bottom: 20px;
            text-align: center;
            display: block;
        }

        h1 {
            font-weight: 700;
            font-size: 24px;
            text-align: center;
            margin-bottom: 15px;
            color: #fff;
        }

        .alert-text {
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 30px;
            line-height: 1.6;
            font-weight: 300;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 12px 15px;
            color: #fff;
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(255, 90, 31, 0.2);
            outline: none;
        }

        .btn-delete {
            background: var(--secondary-color);
            border: none;
            border-radius: 12px;
            padding: 14px;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-delete:hover {
            background: #e64a19;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 90, 31, 0.3);
        }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .btn-cancel:hover {
            color: #fff;
        }

        .error-msg {
            background: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #ff8a8a;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="delete-card">
        <div class="logo-container">
            <img src="{{asset('assets/assets/img/logo plateau.png')}}" alt="Logo Plateau">
        </div>

        <i class="fas fa-exclamation-triangle warning-icon"></i>
        
        <h1>Supprimer votre compte</h1>
        <p class="alert-text">
            Cette action est irréversible. Toutes vos données, y compris vos dossiers et historiques, seront définitivement supprimées.
        </p>

        @if (session('success'))
            <div class="alert alert-success" style="background: rgba(40, 167, 69, 0.2); border: 1px solid rgba(40, 167, 69, 0.4); color: #81c784; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="error-msg">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error-msg">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('user.profile.send-delete-request') }}" method="POST">
            @csrf

            <div class="form-group mb-3">
                <label for="name">Nom Complet</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Entrez votre nom" value="{{ old('name', auth()->check() ? auth()->user()->name . ' ' . auth()->user()->prenom : '') }}" required>
            </div>

            <div class="form-group mb-3">
                <label for="email">Adresse Email du compte</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="votre@email.com" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" required>
            </div>

            <div class="form-group mb-4">
                <label for="reason">Motif de la suppression (facultatif)</label>
                <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Pourquoi souhaitez-vous supprimer votre compte ?">{{ old('reason') }}</textarea>
            </div>

            <button type="submit" class="btn-delete">
                Envoyer la demande
            </button>
        </form>

        <a href="{{ auth()->check() ? route('user.profile.show') : route('home') }}" class="btn-cancel">
            <i class="fas fa-arrow-left me-2"></i> {{ auth()->check() ? "Retour au profil" : "Retour à l'accueil" }}
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="{{asset('userAssets/js/jquery.min.js')}}"></script>
    <script src="{{asset('userAssets/js/bootstrap.min.js')}}"></script>
</body>
</html>
