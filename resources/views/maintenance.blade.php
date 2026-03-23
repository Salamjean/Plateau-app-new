<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site en Maintenance - Mairie du Plateau</title>
    <link href="{{asset('assets/assets/img/logo plateau.png')}}" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #001a41;
            --secondary-color: #ff5a1f;
            --text-color: #ffffff;
        }

        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }

        .bg-image {
            background-image: linear-gradient(rgba(0, 26, 65, 0.8), rgba(0, 26, 65, 0.8)), url("{{asset('assets/assets/img/C.jpg')}}");
            height: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .content {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            max-width: 600px;
            width: 90%;
            animation: fadeIn 1.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            width: 120px;
            margin-bottom: 2rem;
        }

        h1 {
            color: var(--secondary-color);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        p {
            color: var(--text-color);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .maintenance-icon {
            font-size: 4rem;
            color: var(--secondary-color);
            margin-bottom: 1.5rem;
        }

        .footer {
            position: absolute;
            bottom: 2rem;
            width: 100%;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-left-color: var(--secondary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 2rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div class="bg-image">
        <div class="content">
            <img src="{{asset('assets/assets/img/logo plateau.png')}}" alt="Mairie du Plateau" class="logo">
            
            <div class="spinner"></div>
            
            <h1>Site en Maintenance</h1>
            <p>
                Nous travaillons actuellement à l'amélioration de notre plateforme pour mieux vous servir. 
                Veuillez nous excuser pour ce désagrément. 
                Nous serons de retour très bientôt !
            </p>
            
            <div style="color: var(--secondary-color); font-weight: 600;">
                Mairie du Plateau - À votre service
            </div>
        </div>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Mairie du Plateau. Tous droits réservés.
    </div>

</body>
</html>
