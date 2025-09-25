<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport Financier - {{ $agent->name }} {{ $agent->prenom }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-top: 100px;
            margin-bottom: 20px;
            border-bottom: 2px solid #1977cc;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #1977cc;
            margin: 0;
            font-size: 20px;
        }
        .header h2 {
            color: #1977cc;
            margin: 5px 0 0;
            font-size: 16px;
        }
        .info-agent {
            margin-bottom: 20px;
        }
        .info-agent table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-agent td {
            padding: 5px;
            vertical-align: top;
        }
        .info-agent .label {
            font-weight: bold;
            width: 30%;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .table th {
            background-color: #1977cc;
            color: white;
        }
        .table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
        .logo1 {
            position: absolute;
            margin-left: 70%;
            margin-bottom: 25%;
            margin-top: 2;
            height: 150px;
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
        .logo {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="logo_g">
        <div class="logo">
            <img src="assets/assets/img/logo plateau.png" class="logo2" alt="Logo">
            <img src="assets/assets/img/stott.png" class="logo1" alt="Logo">
        </div>
    </div>
    <div class="header">
        <h1>RAPPORT FINANCIER ANNUEL</h1>
        <h2>Année : {{ $currentYear }}</h2>
    </div>
    
    <div class="info-agent">
        <table>
            <tr>
                <td class="label">Agent :</td>
                <td>{{ $agent->name }} {{ $agent->prenom }}</td>
                <td class="label">Email :</td>
                <td>{{ $agent->email }}</td>
            </tr>
            <tr>
                <td class="label">Contact :</td>
                <td>{{ $agent->contact }}</td>
                <td class="label">Commune :</td>
                <td>{{ $agent->commune }}</td>
            </tr>
        </table>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Mois</th>
                <th>Timbres vendus</th>
                <th>Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $data)
            <tr>
                <td>{{ $data['month'] }} {{ $data['year'] }}</td>
                <td>{{ number_format(abs($data['timbres']), 0, ',', ' ') }}</td>
                <td>{{ number_format(abs($data['montant']), 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td>TOTAL {{ $currentYear }}</td>
                <td>{{ number_format(abs($totalTimbres), 0, ',', ' ') }}</td>
                <td>{{ number_format(abs($totalAmount), 0, ',', ' ') }} FCFA</td>
            </tr>
        </tbody>
    </table>
    
    <div class="note">
        <p><strong>Note :</strong> 1 timbre = 500 FCFA</p>
    </div>
    
    <div class="footer">
        Généré le : {{ now()->format('d/m/Y à H:i') }}
    </div>
</body>
</html>