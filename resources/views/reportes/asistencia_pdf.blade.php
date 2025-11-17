<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .fecha-generacion {
            text-align: right;
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead {
            background-color: #f5f5f5;
            border-bottom: 2px solid #333;
        }
        th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .estado {
            text-align: center;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .presente {
            background-color: #d4edda;
            color: #155724;
        }
        .ausente {
            background-color: #f8d7da;
            color: #721c24;
        }
        .retardo {
            background-color: #fff3cd;
            color: #856404;
        }
        .justificada {
            background-color: #cfe2ff;
            color: #084298;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $titulo }}</h1>
    </div>

    <div class="fecha-generacion">
        Generado: {{ $fecha_generacion }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Docente</th>
                <th>Grupo</th>
                <th>Materia</th>
                <th>Estado</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row['fecha'] }}</td>
                    <td>{{ $row['docente'] }}</td>
                    <td>{{ $row['grupo'] }}</td>
                    <td>{{ $row['materia'] }}</td>
                    <td>
                        <span class="estado {{ strtolower($row['estado']) }}">
                            {{ $row['estado'] }}
                        </span>
                    </td>
                    <td>{{ $row['observaciones'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (count($data) === 0)
        <p style="text-align: center; color: #999; margin-top: 20px;">No hay datos disponibles</p>
    @endif
</body>
</html>
