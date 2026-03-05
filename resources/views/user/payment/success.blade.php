<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réussi - Plateau App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            background: #f8fafc;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .check-icon {
            background: #22c55e;
            color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: -40px auto 20px;
            box-shadow: 0 4px 10px rgba(34, 197, 94, 0.3);
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
            Votre paiement pour la demande d'extrait de <strong>{{ $type }}</strong> a été traité avec succès.
        </p>

        <div class="bg-slate-50 rounded-xl p-4 mb-8 text-left border border-slate-100">
            <div class="flex justify-between mb-2">
                <span class="text-slate-500 text-sm">Référence :</span>
                <span class="text-slate-800 font-mono font-bold">{{ $reference }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500 text-sm">Status Demande :</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  En attente
                </span>
            </div>
        </div>

        <div class="space-y-3">
            @php
                $route = match($type) {
                    'naissance' => 'user.extrait.index',
                    'mariage' => 'user.extrait.mariage.index',
                    'deces' => 'user.extrait.deces.index',
                    default => 'user.dashboard'
                };
            @endphp
            <a href="{{ route($route) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition duration-200">
                Consulter ma liste
            </a>
            <a href="{{ route('user.dashboard') }}" class="block w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3 px-4 rounded-xl transition duration-200">
                Retour au tableau de bord
            </a>
        </div>

        <p class="mt-8 text-slate-400 text-xs text-center">
            Un email et un SMS de confirmation vous seront envoyés sous peu.
        </p>
    </div>

</body>
</html>
