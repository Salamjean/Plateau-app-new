<!DOCTYPE html>
<html>
<head>
    <title>Plateau-Apps - Réinitialisation de votre mot de passe Agent</title>
</head>
<body>
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <img src="{{ $logoUrl }}" alt="Logo Plateau-Apps" width="150">
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
                <h1 style="color: #1f4083;">Réinitialisation de votre mot de passe</h1>
                <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte Agent de l'Etat Civil.</p>
                <p>Cliquez sur le bouton ci-dessous pour procéder à la mise à jour.</p>
                <p>Saisissez le code OTP suivant : <strong style="font-size: 1.25rem; color: #1f4083; letter-spacing: 2px;">{{ $code }}</strong> dans le formulaire qui apparaîtra.</p>
                <p style="margin-top: 30px;">
                    <a href="{{ url('/validate-agent-account/' . $email) }}" style="background-color: #1f4083; border: none; color: white; padding: 12px 25px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; cursor: pointer; border-radius: 5px; font-weight: bold;">
                        Réinitialiser mon mot de passe
                    </a>
                </p>
                <p style="margin-top: 30px; font-size: 0.9rem; color: #777;">Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet e-mail.</p>
                <p style="margin-top: 10px; font-size: 0.9rem; color: #777;">Merci d'utiliser notre application Plateau-Apps.</p>
            </td>
        </tr>
    </table>
</body>
</html>
