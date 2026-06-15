<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Demande Rejetée - Plateau App</title>
</head>
<body style="background-color: #f8fafc; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); overflow: hidden; border: 1px solid #e2e8f0;">
        <!-- Header -->
        <div style="background-color: #f1f5f9; padding: 30px; text-align: center; border-bottom: 1px solid #e2e8f0;">
            <img src="{{ $logoUrl }}" alt="Plateau-Apps Logo" style="max-height: 60px; max-width: 150px;">
        </div>

        <!-- Body -->
        <div style="padding: 40px 30px; color: #334155; line-height: 1.6;">
            <h2 style="color: #e11d48; margin-top: 0; font-size: 20px; font-weight: 700; text-align: center; margin-bottom: 25px;">
                Action requise : Votre demande a été rejetée
            </h2>

            <p style="font-size: 16px; margin-bottom: 20px;">
                Bonjour <strong>{{ $user->name }} {{ $user->prenom }}</strong>,
            </p>

            <p style="font-size: 15px; margin-bottom: 20px;">
                Nous vous informons que votre demande
                @if($type === 'naissance')
                    d'extrait de naissance
                @elseif($type === 'mariage')
                    d'extrait de mariage
                @elseif($type === 'deces')
                    d'extrait de décès
                @elseif($type === 'naissance_groupe')
                    groupée d'extraits de naissance
                @elseif($type === 'mariage_groupe')
                    groupée d'extraits de mariage
                @elseif($type === 'deces_groupe')
                    groupée d'extraits de décès
                @else
                    de document
                @endif
                (Réf : <strong>{{ $demande->reference }}</strong>) a été rejetée par l'agent de l'état civil.
            </p>

            <!-- Rejection Reason Card -->
            <div style="background-color: #fff1f2; border-left: 4px solid #f43f5e; padding: 20px; border-radius: 6px; margin: 25px 0;">
                <h4 style="margin-top: 0; margin-bottom: 8px; color: #be123c; font-size: 15px; font-weight: 600;">
                    Motif du rejet :
                </h4>
                <p style="margin: 0; font-size: 14px; color: #9f1239; white-space: pre-line;">
                    {!! nl2br(e($motif)) !!}
                </p>
            </div>

            <p style="font-size: 15px; margin-bottom: 25px;">
                Veuillez vous connecter à l'application Plateau App ou visiter votre espace personnel pour corriger les informations erronées afin que nous puissions traiter votre demande au plus vite.
            </p>

            <!-- CTA -->
            <div style="text-align: center; margin: 35px 0;">
                <a href="{{ route('user.dashboard') }}" 
                   style="background-color: #0f172a; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; display: inline-block; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.2);">
                    Accéder à mon espace
                </a>
            </div>

            <p style="font-size: 14px; color: #64748b; margin-top: 30px;">
                Cordialement,<br>
                <strong>L'équipe Plateau-Apps</strong>
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9;">
            <p style="margin: 0 0 5px 0;">Ceci est un email automatique, merci de ne pas y répondre.</p>
            <p style="margin: 0;">&copy; {{ date('Y') }} Plateau-Apps. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
