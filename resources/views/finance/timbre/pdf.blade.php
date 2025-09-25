<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Statistiques Timbres - {{ $month }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #0033c4; margin-bottom: 5px; }
        .header h2 { color: #555; margin-top: 0; }
        .stats-table { 
            width: 100%; 
            border-collapse: collapse;
            margin: 20px 0;
        }
        .stats-table th, .stats-table td { 
            padding: 12px; 
            text-align: left; 
            border-bottom: 1px solid #ddd;
        }
        .stats-table th { 
            background-color: #0033c4; 
            color: white; 
        }
        .total-row { 
            font-weight: bold; 
            background-color: #f8f9fa;
        }
        .total-row td {
            border-top: 2px solid #0033c4;
        }
        .footer { 
            margin-top: 50px; 
            text-align: center; 
            font-size: 12px; 
            color: #666;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
         .logo1 {
            position: absolute;
            margin-left: 75%;
            margin-bottom: 20%;
            margin-top: 2;
            height: 110px;
            left: 40px;
        }
        .logo2 {
            height: 130px;
            position: absolute;
            margin-bottom: 20%;
            left: -50px;
            margin-left: 20px;
            margin-top: 0;
        }
        .logoo {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="logo_g">
        <div class="logoo">
            <img src="assets/assets/img/logo plateau.png" class="logo2" alt="Logo">
            <img src="assets/assets/img/stott.png" class="logo1" alt="Logo">
        </div>
    </div>
    <div class="logo">
        <h2>CAISSE TIMBRES</h2>
    </div>
    
    <div class="header">
        <h1>Rapport des Ventes de Timbres</h1>
        <h2>{{ $month }}</h2>
    </div>
    
    <div class="info-box">
        <p><strong>Période:</strong> {{ $month }}</p>
        <p><strong>Date de génération:</strong> {{ date('d/m/Y à H:i') }}</p>
    </div>
    
    <table class="stats-table">
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Quantité</th>
                <th>Prix Unitaire</th>
                <th>Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Timbres vendus</td>
                <td>{{ number_format($stampCount, 0, ',', ' ') }}</td>
                <td>{{ number_format($timbrePrice, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($stampCount * $timbrePrice, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr class="total-row">
                <td colspan="3"><strong>TOTAL GÉNÉRÉ</strong></td>
                <td><strong>{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</strong></td>
            </tr>
        </tbody>
    </table>
    
    <div class="info-box">
        <p><strong>Résumé:</strong> {{ $stampCount }} timbre(s) vendu(s) pour un montant total de {{ number_format($totalAmount, 0, ',', ' ') }} FCFA</p>
    </div>
    
    <div class="footer">
        <p>Document généré automatiquement par la mairie du plateau</p>
        <p>© {{ date('Y') }} - Tous droits réservés</p>
    </div>
</body>
</html>