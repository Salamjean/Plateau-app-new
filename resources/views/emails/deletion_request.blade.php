<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 10px; }
        .header { background: #001a41; color: white; padding: 10px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; }
        .footer { font-size: 0.8em; color: #777; text-align: center; margin-top: 20px; }
        .label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Demande de suppression de compte</h2>
        </div>
        <div class="content">
            <p>Une nouvelle demande de suppression de compte a été soumise depuis le site Plateau App.</p>
            <p><span class="label">Nom :</span> {{ $name }}</p>
            <p><span class="label">Email :</span> {{ $email }}</p>
            <p><span class="label">Motif :</span></p>
            <p>{{ $reason ?? 'Aucun motif fourni.' }}</p>
        </div>
        <div class="footer">
            Ceci est un message automatique généré par le système Plateau App.
        </div>
    </div>
</body>
</html>
