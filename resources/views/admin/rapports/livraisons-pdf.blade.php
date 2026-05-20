<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport des Livraisons - {{ $mois_nom }} {{ $annee }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #6777ef; color: white; }
        .total { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport des Livraisons</h1>
        <h2>{{ $mois_nom }} {{ $annee }}</h2>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Type Document</th>
                <th>Reference</th>
                <th>livreur</th>
                <th>Utilisateur</th>
                <th>Date Livraison</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($livraisons as $livraison)
            <tr>
                <td>{{ $livraison->type_document }}</td>
                <td>{{ $livraison->reference ?? 'N/A' }}</td>
                <td>{{ $livraison->livreur ? $livraison->livreur->name . ' ' . $livraison->livreur->prenom : 'Non attribué' }}</td>
                <td>{{ $livraison->user ? $livraison->user->name . ' ' . $livraison->user->prenom : 'N/A' }}</td>
                <td>{{ $livraison->updated_at->format('d/m/Y H:i') }}</td>
                <td>{{ number_format($livraison->montant_livraison ?? 0, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <p>Total des livraisons: {{ $total_livraisons }}</p>
        <p>Montant total: {{ number_format($total_montant, 0, ',', ' ') }} FCFA</p>
    </div>
</body>
</html>
