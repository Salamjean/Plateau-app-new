<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de Mot de Passe</title>
    <style>
        /* Styles identiques à l'autre email pour la cohérence */
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
        .header {
            background-color: #1f4083;
            padding: 25px;
            text-align: center;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            padding: 30px 40px;
            line-height: 1.6;
            font-size: 16px;
        }
        .content p {
            margin-top: 0;
        }
        /* Style du bouton principal */
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #1f4083;
            color: #ffffff !important; /* Important pour forcer la couleur sur certains clients mail */
            padding: 15px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
        }
        /* Style pour l'URL au cas où le bouton ne s'afficherait pas */
        .link {
            word-break: break-all;
            font-size: 12px;
            color: #a0aec0;
        }
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
                Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.
            </p>

            <!-- Conteneur du bouton -->
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="button">Réinitialiser le mot de passe</a>
            </div>

            <p>
                Ce lien de réinitialisation expirera dans <strong>{{ $expirationMinutes }} minutes</strong>.
            </p>
            <p>
                Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email en toute sécurité.
            </p>
            <p>
                Cordialement,<br>
                L'équipe de Plateau-Apps
            </p>
            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
            <p style="text-align: center; font-size: 14px; color: #718096;">
                Si vous rencontrez des difficultés pour cliquer sur le bouton "Réinitialiser le mot de passe", copiez et collez l'URL ci-dessous dans votre navigateur web :
                <br>
                <span class="link">{{ $resetUrl }}</span>
            </p>
        </div>

        <!-- Pied de page -->
        <div class="footer">
            &copy; {{ date('Y') }} Plateau-Apps. Tous droits réservés.
        </div>
    </div>
</body>
</html>
