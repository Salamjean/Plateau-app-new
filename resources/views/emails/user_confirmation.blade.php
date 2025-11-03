<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmation d'inscription</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { max-width: 150px; height: auto; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 5px; }
        .button { display: inline-block; padding: 12px 24px; background: #1977cc; text-decoration: none; border-radius: 4px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $logoUrl }}" alt="Plateau-Apps Logo" class="logo">
            <h1>Bienvenue sur Plateau-Apps !</h1>
        </div>

        <div class="content">
            <p>Bonjour <strong>{{ $user->prenom }} {{ $user->name }}</strong>,</p>
            
            <p>Nous vous remercions pour votre inscription sur notre plateforme.</p>
            
            <p>Votre compte a été créé avec succès. Vous pouvez dès maintenant vous connecter et profiter de tous nos services.</p>
            
            <p><strong>Vos informations de compte :</strong></p>
            <ul>
                <li><strong>Email :</strong> {{ $user->email }}</li>
                <li><strong>Commune :</strong> {{ $user->commune }}</li>
                <li><strong>Contact :</strong> {{ $user->indicatif }}{{ $user->contact }}</li>
            </ul>

            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ route('login') }}" class="button" style="color:white">Se connecter</a>
            </p>

            <p>Si vous n'êtes pas à l'origine de cette inscription, veuillez ignorer cet email.</p>
        </div>

        <div class="footer">
            <p>© {{ date('Y') }} Plateau-Apps. Tous droits réservés.</p>
            <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>