<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - Plateau App</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #6777ef 0%, #3b4ba8 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .maintenance-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .maintenance-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #6777ef 0%, #3b4ba8 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(103, 119, 239, 0.7);
            }
            70% {
                box-shadow: 0 0 0 20px rgba(103, 119, 239, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(103, 119, 239, 0);
            }
        }

        .maintenance-icon i {
            font-size: 50px;
            color: white;
        }

        h1 {
            color: #2d3748;
            font-size: 2rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .message {
            color: #718096;
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .status-badge {
            display: inline-block;
            background: linear-gradient(135deg, #6777ef 0%, #3b4ba8 100%);
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .contact-info {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
            color: #a0aec0;
            font-size: 0.9rem;
        }

        .contact-info a {
            color: #6777ef;
            text-decoration: none;
            font-weight: 600;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .logo {
            width: 150px;
            margin-bottom: 20px;
        }

        @media (max-width: 480px) {
            .maintenance-container {
                padding: 40px 25px;
            }

            h1 {
                font-size: 1.5rem;
            }

            .message {
                font-size: 1rem;
            }

            .maintenance-icon {
                width: 100px;
                height: 100px;
            }

            .maintenance-icon i {
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <img src="{{ asset('assets/assets/img/logo plateau.png') }}" alt="Plateau Logo" class="logo" 
             onerror="this.style.display='none'">
        
        <div class="maintenance-icon">
            <i class="fas fa-tools"></i>
        </div>

        <h1>Site en Maintenance</h1>
        
        <p class="message">
            {{ $message ?? 'Nous effectuons actuellement une maintenance. Veuillez réessayer plus tard.' }}
        </p>

        <div class="status-badge">
            <i class="fas fa-sync-alt fa-spin mr-2"></i>
            En cours de maintenance
        </div>

        <div class="contact-info">
            <p>Nous serons de retour sous peu.</p>
            <p>Pour toute urgence, contactez-nous à <a href="mailto:support@plateau-app.com">support@plateau-app.com</a></p>
        </div>
    </div>
</body>
</html>
