<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $titre }}</title>
    <link rel="shortcut icon" href="{{asset('assets/assets/img/logo plateau.png')}}" />
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #1977cc;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #1977cc;
            margin: 0;
            font-size: 24px;
        }
        
        .header .subtitle {
            color: #6c757d;
            font-size: 14px;
        }
        
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #1977cc;
            color: white;
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 16px;
            margin-bottom: 15px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .stat-row {
            display: table-row;
        }
        
        .stat-cell {
            display: table-cell;
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }
        
        .stat-cell.header {
            background: #f8f9fa;
            font-weight: bold;
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #1977cc;
        }
        
        .stat-label {
            font-size: 12px;
            color: #6c757d;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .table th {
            background: #1977cc;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        
        .table td {
            padding: 8px 10px;
            border: 1px solid #ddd;
        }
        
        .table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .period-info {
            background: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .chart-placeholder {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border: 1px dashed #ccc;
            margin: 10px 0;
            font-style: italic;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport d'Activité - État Civil</h1>
        <div class="subtitle">
            Période d'analyse : {{ $months }} mois | Généré le : {{ $dateExport }}
        </div>
        <img src="assets/assets/img/logo plateau.png" alt="">
    </div>

    <!-- Période d'analyse -->
    <div class="period-info">
        📊 Rapport couvrant les {{ $months }} derniers mois
    </div>

    <!-- Statistiques globales -->
    <div class="section">
        <div class="section-title">📈 Statistiques Globales</div>
        
        <div class="stats-grid">
            <div class="stat-row">
                <div class="stat-cell header">Type de demande</div>
                <div class="stat-cell header">Total</div>
                <div class="stat-cell header">Terminées</div>
                <div class="stat-cell header">En cours</div>
                <div class="stat-cell header">En attente</div>
                <div class="stat-cell header">Taux de complétion</div>
            </div>
            
            <div class="stat-row">
                <div class="stat-cell">👶 Naissances</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['naissances']['total']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['naissances']['termine']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['naissances']['en_cours']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['naissances']['en_attente']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['naissances']['taux_completion'], 1) }}%</div>
            </div>
            
            <div class="stat-row">
                <div class="stat-cell">⚰️ Décès</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['deces']['total']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['deces']['termine']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['deces']['en_cours']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['deces']['en_attente']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['deces']['taux_completion'], 1) }}%</div>
            </div>
            
            <div class="stat-row">
                <div class="stat-cell">💍 Mariages</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['mariages']['total']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['mariages']['termine']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['mariages']['en_cours']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['mariages']['en_attente']) }}</div>
                <div class="stat-cell">{{ number_format($statsDetaillees['mariages']['taux_completion'], 1) }}%</div>
            </div>
        </div>
    </div>

    <!-- Évolution des demandes -->
    <div class="section">
        <div class="section-title">📊 Évolution des Demandes</div>
        
        <div class="chart-placeholder">
            Graphique d'évolution sur {{ $months }} mois<br>
            (Les graphiques interactifs ne sont pas disponibles en PDF)
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Naissances</th>
                    <th>Décès</th>
                    <th>Mariages</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donneesGraphiques['labels'] as $index => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $donneesGraphiques['datasets'][0]['data'][$index] }}</td>
                    <td>{{ $donneesGraphiques['datasets'][1]['data'][$index] }}</td>
                    <td>{{ $donneesGraphiques['datasets'][2]['data'][$index] }}</td>
                    <td><strong>{{ $donneesGraphiques['datasets'][0]['data'][$index] + $donneesGraphiques['datasets'][1]['data'][$index] + $donneesGraphiques['datasets'][2]['data'][$index] }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Top agents -->
    <div class="section">
        <div class="section-title">🏆 Classement des Agents</div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Rang</th>
                    <th>Agent</th>
                    <th>Demandes traitées</th>
                    <th>Taux de satisfaction</th>
                    <th>Performance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topAgents as $index => $agent)
                <tr>
                    <td>#{{ $index + 1 }}</td>
                    <td>{{ $agent['name'] }}</td>
                    <td>{{ $agent['demandes_traitees'] }}</td>
                    <td>{{ $agent['taux_satisfaction'] }}%</td>
                    <td>
                        @if($agent['taux_satisfaction'] >= 90)
                            <span class="badge badge-success">Excellente</span>
                        @elseif($agent['taux_satisfaction'] >= 80)
                            <span class="badge badge-info">Bonne</span>
                        @else
                            <span class="badge badge-warning">Moyenne</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Synthèse -->
    <div class="section">
        <div class="section-title">📋 Synthèse du Rapport</div>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 4px;">
            <p><strong>📈 Points clés :</strong></p>
            <ul>
                <li>Total des demandes : <strong>{{ number_format($statsDetaillees['naissances']['total'] + $statsDetaillees['deces']['total'] + $statsDetaillees['mariages']['total']) }}</strong></li>
                <li>Taux de complétion moyen : <strong>{{ number_format(($statsDetaillees['naissances']['taux_completion'] + $statsDetaillees['deces']['taux_completion'] + $statsDetaillees['mariages']['taux_completion']) / 3, 1) }}%</strong></li>
                <li>Demandes en attente : <strong>{{ number_format($statsDetaillees['naissances']['en_attente'] + $statsDetaillees['deces']['en_attente'] + $statsDetaillees['mariages']['en_attente']) }}</strong></li>
                <li>Période analysée : <strong>{{ $months }} mois</strong></li>
            </ul>
        </div>
    </div>

    <div class="footer">
        Rapport généré automatiquement par le Système d'État Civil<br>
        © {{ date('Y') }} - Tous droits réservés
    </div>
</body>
</html>