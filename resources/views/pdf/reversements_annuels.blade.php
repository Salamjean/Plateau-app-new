<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport de Reversements Annuels - {{ $year }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 13px;
            line-height: 1.5;
        }
        .header {
            border-bottom: 2px solid #1f4083;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header table {
            width: 100%;
        }
        .logo-section {
            font-size: 24px;
            font-weight: bold;
            color: #1f4083;
        }
        .title-section {
            text-align: right;
        }
        .title-section h2 {
            margin: 0;
            color: #1f4083;
            font-size: 20px;
        }
        .title-section p {
            margin: 5px 0 0 0;
            color: #777;
            font-size: 12px;
        }
        .info-section {
            margin-bottom: 30px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .info-section table {
            width: 100%;
        }
        .info-section td {
            padding: 4px 0;
        }
        .info-label {
            font-weight: bold;
            color: #4a5568;
            width: 150px;
        }
        .info-value {
            color: #2d3748;
        }
        .table-title {
            font-size: 15px;
            font-weight: bold;
            color: #1f4083;
            margin-bottom: 15px;
            border-left: 4px solid #1f4083;
            padding-left: 8px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.data-table th {
            background-color: #1f4083;
            color: white;
            text-align: left;
            padding: 10px 12px;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            border: 1px solid #1f4083;
        }
        table.data-table td {
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .total-row {
            font-weight: bold;
            background-color: #edf2f7 !important;
            border-top: 2px solid #1f4083;
        }
        .total-row td {
            border-bottom: 2px solid #1f4083;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td class="logo-section" style="width: 50%;">
                    PLATEAU APP
                </td>
                <td class="title-section" style="width: 50%; text-align: right;">
                    <h2 style="margin: 0; color: #1f4083; font-size: 20px;">RAPPORT DE REVERSEMENT</h2>
                    <p style="margin: 5px 0 0 0; color: #777; font-size: 12px;">Récapitulatif Annuel - {{ $year }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <table style="width: 100%;">
            <tr>
                <td class="info-label" style="font-weight: bold; color: #4a5568; width: 100px;">Commune :</td>
                <td class="info-value" style="color: #2d3748;">{{ $commune }}</td>
                <td class="info-label" style="font-weight: bold; color: #4a5568; text-align: right; width: 100px;">Généré le :</td>
                <td class="info-value" style="color: #2d3748; text-align: right;">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="info-label" style="font-weight: bold; color: #4a5568; width: 100px;">Généré par :</td>
                <td class="info-value" style="color: #2d3748;">{{ $roleLabel }} ({{ $userName }})</td>
                <td class="info-label" style="font-weight: bold; color: #4a5568; text-align: right; width: 100px;">Période :</td>
                <td class="info-value" style="color: #2d3748; text-align: right;">01/01/{{ $year }} au 31/12/{{ $year }}</td>
            </tr>
        </table>
    </div>

    <div class="table-title">Détail des Reversements Mensuels</div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center">Mois</th>
                <th class="text-center">Nombre de Transactions</th>
                <th class="text-center">Montant Cumulé Reversé</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotal = 0;
                $totalTransactions = 0;
            @endphp
            @foreach($monthlyReport as $report)
                @php
                    $grandTotal += $report['total_montant'];
                    $totalTransactions += $report['count'];
                @endphp
                <tr>
                    <td class="text-center">{{ $report['label'] }}</td>
                    <td class="text-center">{{ $report['count'] }}</td>
                    <td class="text-center" style="color: #e71d36; font-weight: bold;">
                        {{ number_format($report['total_montant'], 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td style="font-weight: bold;">TOTAL ANNUEL</td>
                <td class="text-center" style="font-weight: bold;">{{ $totalTransactions }}</td>
                <td class="text-center" style="color: #e71d36; font-size: 14px; font-weight: bold;">
                    {{ number_format($grandTotal, 0, ',', ' ') }} FCFA
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 60px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; text-align: left; vertical-align: top;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">Cachet de la Commune</p>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <p style="margin: 0; font-weight: bold; text-decoration: underline;">Visa de l'Agent Autorisé</p>
                    <p style="margin: 40px 0 0 0; font-style: italic; color: #555;">{{ $userName }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Plateau-App © 2026 - Système de gestion et d'état civil de la commune du Plateau.
    </div>

</body>
</html>
