<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code de Réinitialisation de Mot de Passe</title>
    <style>
        /* Styles généraux */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            background-color: #edf2f7;
            color: #718096;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        /* En-tête */
        .header {
            background-color: #1977cc; /* Bleu institutionnel */
            padding: 25px;
            text-align: center;
        }
        .header img {
            max-width: 150px;
        }
        /* Contenu principal */
        .content {
            padding: 30px 40px;
            line-height: 1.6;
            font-size: 16px;
        }
        .content p {
            margin-top: 0;
        }
        /* Le code de réinitialisation */
        .code-box {
            background-color: #edf2f7;
            border-radius: 6px;
            margin: 25px auto;
            padding: 15px 20px;
            text-align: center;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #2d3748;
            letter-spacing: 8px; /* Espacement pour la lisibilité */
        }
        /* Bouton (même si c'est un code, un style de bouton est parfois utilisé) */
        .button {
            display: inline-block;
            background-color: #1977cc;
            color: #ffffff;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
        }
        /* Pied de page */
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #a0aec0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête avec le logo -->
        <div class="header">
            <img src="{{ $logoUrl }}" alt="Logo Plateau-Apps">
        </div>

        <!-- Contenu du message -->
        <div class="content">
            <h1 style="color: #2d3748; font-size: 24px;">Bonjour, {{ $userName }} !</h1>
            <p>
                Vous recevez cet email car une demande de réinitialisation de mot de passe a été effectuée pour votre compte sur notre application.
            </p>
            <p>
                Utilisez le code ci-dessous pour procéder à la réinitialisation.
            </p>

            <!-- Boîte contenant le code -->
            <div class="code-box">
                <p style="margin:0; font-size: 14px; color:#718096;">Votre code de vérification :</p>
                <div class="code">{{ $code }}</div>
            </div>

            <p>
                Ce code expirera dans <strong>{{ $expirationMinutes }} minutes</strong>.
            </p>
            <p>
                Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email en toute sécurité.
            </p>
            <p>
                Cordialement,<br>
                L'équipe de Plateau-Apps
            </p>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            &copy; {{ date('Y') }} Plateau-Apps. Tous droits réservés.
        </div>
    </div>
</body>
</html>