<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Confirmation Demande Extrait de Naissance</title>
</head>
<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="{{ $logoUrl }}" alt="Plateau-Apps Logo" style="max-width: 150px;">
        </div>
        
        <h2 style="color: #333; text-align: center;">Confirmation de votre demande d'extrait de naissance</h2>
        
        <p>Bonjour <strong>{{ $user->name }} {{ $user->prenom }}</strong>,</p>
        
        <p>Votre demande d'extrait de naissance a bien été enregistrée et transmise à la mairie du plateau.</p>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h3 style="margin-top: 0;">Détails de votre demande :</h3>
            <p><strong>Référence :</strong> {{ $naissance->reference }}</p>
            <p><strong>Type :</strong> {{ $naissance->type }}</p>
            <p><strong>Nom :</strong> {{ $naissance->name }}</p>
            <p><strong>Prénom :</strong> {{ $naissance->prenom }}</p>
            <p><strong>Quantité :</strong> {{ $naissance->quantite }}</p>
            <p><strong>État :</strong> {{ $naissance->etat }}</p>
            <p><strong>Date de la demande :</strong> {{ $naissance->created_at->format('d/m/Y à H:i') }}</p>
        </div>
        
        <p>Vous pouvez suivre l'état de votre demande en cliquant sur le lien ci-dessous :</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="https://plateau-apps.com/home/search" 
               style="background: #007bff; color: white; padding: 12px 25px; 
                      text-decoration: none; border-radius: 5px; display: inline-block;">
                Suivre ma demande
            </a>
        </div>
        
        <p>Nous vous tiendrons informé de l'avancement du traitement de votre demande.</p>
        
        <p>Cordialement,<br>L'équipe Plateau-Apps</p>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; 
                    font-size: 12px; color: #666; text-align: center;">
            <p>Ceci est un email automatique, merci de ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
