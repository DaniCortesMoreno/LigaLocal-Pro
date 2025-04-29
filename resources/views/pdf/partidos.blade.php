<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Partidos - {{ $tournament->nombre }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Partidos del Torneo "{{ $tournament->nombre }}"</h2>

    <table>
        <thead>
            <tr>
                <th>Equipo 1</th>
                <th>Goles</th>
                <th>Equipo 2</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($partidos as $partido)
                <tr>
                    <td>{{ $partido->equipo1->nombre ?? 'Pendiente' }}</td>
                    <td>{{ $partido->goles_equipo1 ?? '-' }} - {{ $partido->goles_equipo2 ?? '-' }}</td>
                    <td>{{ $partido->equipo2->nombre ?? 'Pendiente' }}</td>
                    <td>{{ \Carbon\Carbon::parse($partido->fecha_partido)->format('d/m/Y H:i') }}</td>
                    <td>{{ ucfirst($partido->estado_partido) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
