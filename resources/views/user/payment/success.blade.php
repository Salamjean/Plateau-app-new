<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réussi - Plateau App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <style>
        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 50%, #ecfdf5 100%);
            min-height: 100vh;
        }

        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        }

        .check-icon {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            margin: -45px auto 20px;
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.4);
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full success-card p-8 text-center animate__animated animate__zoomIn">
        <div class="check-icon animate__animated animate__bounceIn animate__delay-1s">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="text-2xl font-bold text-slate-800 mb-2">Paiement Réussi !</h1>
        <p class="text-slate-600 mb-6">
            Votre paiement pour la demande d'extrait de <strong class="text-blue-600">{{ $type }}</strong> a été
            traité avec succès.
        </p>

        <div class="bg-slate-50 rounded-xl p-5 mb-8 text-left border border-slate-100">
            <div class="flex justify-between mb-3">
                <span class="text-slate-500 text-sm">Référence :</span>
                <span class="text-slate-800 font-mono font-bold">{{ $reference }}</span>
            </div>
            <div class="flex justify-between mb-3">
                <span class="text-slate-500 text-sm">Type :</span>
                <span class="text-slate-800 font-semibold capitalize">{{ $type }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 text-sm">Statut :</span>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="fas fa-check-circle mr-1"></i> Payé
                </span>
            </div>
        </div>

        <div class="space-y-3">
            @php
                $listUrl = match ($type) {
                    'naissance', 'naissance_groupe' => url('/user/extract/index'),
                    'mariage', 'mariage_groupe' => url('/user/wedding/index'),
                    'deces', 'deces_groupe' => url('/user/extract/death/index'),
                    default => url('/user/dashboard'),
                };
            @endphp
            <a href="{{ $listUrl }}"
                onclick="if(isMobile && typeof deepLink !== 'undefined') { safeRedirect(deepLink); return false; }"
                class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 shadow-lg shadow-blue-200">
                <i class="fas fa-list mr-2"></i> Consulter mes demandes
            </a>
            <a href="{{ url('/user/dashboard') }}"
                onclick="if(isMobile && typeof deepLink !== 'undefined') { safeRedirect(deepLink); return false; }"
                class="block w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 px-4 rounded-xl transition duration-200">
                <i class="fas fa-home mr-2"></i> Retour au tableau de bord
            </a>
        </div>

        <p class="mt-8 text-slate-400 text-xs text-center">
            <i class="fas fa-envelope mr-1"></i> Un email et un SMS de confirmation vous seront envoyés sous peu.
        </p>
    </div>

    <script>
        var isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        var deepLink = "plateauapps://app/payment-result?status=success&transactionId={{ $reference }}";

        function safeRedirect(url) {
            if (url.startsWith('plateauapps://') || url.startsWith('/') || url.startsWith(window.location.origin)) {
                window.location.href = url;
            } else {
                console.error('URL de redirection non sécurisée:', url);
                window.location.href = '/user/dashboard';
            }
        }

        // Stocker le résultat dans localStorage (résiste aux ruptures COOP de Wave)
        try {
            localStorage.setItem('plateauPaymentResult', JSON.stringify({
                status: 'success',
                reference: '{{ $reference }}',
                listUrl: '{{ $listUrl }}',
                timestamp: Date.now()
            }));
        } catch (e) {}

        // Notifier l'opener si accessible (sans COOP)
        if (window.opener && window.opener !== window) {
            try {
                window.opener.paymentSuccess = true;
                window.opener.paymentSuccessUrl = '{{ $listUrl }}';
                window.opener.paymentType = '{{ $type }}';
            } catch (e) {}
        }

        // Détecter si on est dans un popup (via window.opener OU window.name)
        // PaymentPopup est le nom réellement utilisé à l'ouverture depuis le formulaire.
        var isPopup = (window.opener && window.opener !== window) || window.name === 'WavePaymentPopup' || window.name ===
            'PaymentPopup';

        if (isPopup) {
            // Toujours fermer le popup après 3 secondes (même si opener est null à cause du COOP)
            setTimeout(function() {
                window.close();
            }, 3000);
        } else {
            if (isMobile) {
                setTimeout(function() {
                    if (typeof deepLink !== 'undefined') {
                        safeRedirect(deepLink);
                    }
                }, 1000);
            }
            // Rediriger vers la liste après 5 secondes (cas fenêtre principale)
            setTimeout(function() {
                if (!isMobile) {
                    safeRedirect('{{ $listUrl }}');
                }
            }, 5000);
        }
    </script>

</body>

</html>
