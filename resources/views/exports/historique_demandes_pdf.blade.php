<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Demandes de Courses</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
            color: #007bff;
        }

        .header p {
            font-size: 10px;
            color: #666;
        }

        .filters {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 10px;
        }

        .filters-row {
            margin-bottom: 5px;
        }

        .filters-row strong {
            min-width: 120px;
            display: inline-block;
        }

        .statistics {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            border-radius: 4px;
            width: 100%;
        }

        .stat-box {
            text-align: center;
        }

        .stat-box .number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .stat-box .label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background-color: #007bff;
            color: white;
        }

        thead th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            border: 1px solid #0056b3;
        }

        tbody td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }

        .status-creee {
            background-color: #dc3545;
        }

        .status-affectee {
            background-color: #ffc107;
            color: #333;
        }

        .status-demarree {
            background-color: #17a2b8;
        }

        .status-terminee {
            background-color: #28a745;
        }

        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: right;
            font-size: 9px;
            color: #999;
        }

        .page-break {
            page-break-after: always;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Historique des Demandes de Courses</h1>
        <p>Rapport généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    @if(isset($filters))
    <div class="filters">
        <div class="filters-row">
            <strong>Période:</strong> {{ $filters['debut']->format('d/m/Y') }} au {{ $filters['fin']->format('d/m/Y') }}
        </div>
        @if(!empty($filters['vehicule_id']))
        <div class="filters-row">
            <strong>Véhicule:</strong> {{ $filters['vehicule_id'] }}
        </div>
        @endif
        @if(!empty($filters['chauffeur_id']))
        <div class="filters-row">
            <strong>Chauffeur:</strong> {{ $filters['chauffeur_id'] }}
        </div>
        @endif
        @if(!empty($filters['point_destination']))
        <div class="filters-row">
            <strong>Destination:</strong> {{ $filters['point_destination'] }}
        </div>
        @endif
    </div>
    @endif

    @if(isset($statistics))
    <div class="statistics">
        <div class="stat-box">
            <div class="number">{{ $statistics['nouvelles'] }}</div>
            <div class="label">Demandes Créées</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $statistics['encours'] }}</div>
            <div class="label">Demandes en Cours</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $statistics['terminees'] }}</div>
            <div class="label">Demandes Terminées</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ $statistics['total'] }}</div>
            <div class="label">Total</div>
        </div>
    </div>
    @endif

    @if(!empty($demandes))
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Demandeur</th>
                    <th>Entité</th>
                    <th>Chauffeur</th>
                    <th>Véhicule</th>
                    <th>Type</th>
                    <th>Effectif</th>
                    <th>Trafic</th>
                    <th>Objet</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandes as $demande)
                    <tr>
                        <td>{{ $demande['reference'] }}</td>
                        <td>{{ $demande['date_demande'] }}</td>
                        <td>{{ $demande['demandeur'] }}</td>
                        <td>{{ $demande['entite'] }}</td>
                        <td>{{ $demande['chauffeur'] }}</td>
                        <td>{{ $demande['vehicule'] }}</td>
                        <td>{{ $demande['type_vehicule'] }}</td>
                        <td style="text-align: center;">{{ $demande['effectif'] }}</td>
                        <td>{{ $demande['trafic'] }}</td>
                        <td>{{ Str::limit($demande['objet'], 30) }}</td>
                        <td>
                            <span class="status-badge status-{{ Str::lower($demande['statut']) }}">
                                {{ $demande['statut'] }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>Aucune demande trouvée pour les critères sélectionnés.</p>
        </div>
    @endif

    <div class="footer">
        <p>Archivé le {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
