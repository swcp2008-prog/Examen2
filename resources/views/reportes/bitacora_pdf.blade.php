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
            font-size: 12px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        tr:hover {
            background-color: #f9f9f9;
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
                <th>Fecha y Hora</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Tabla</th>
                <th>IP Origen</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row['fecha'] }}</td>
                    <td>{{ $row['usuario'] }}</td>
                    <td>{{ $row['accion'] }}</td>
                    <td>{{ $row['tabla'] }}</td>
                    <td>{{ $row['ip'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (count($data) === 0)
        <p style="text-align: center; color: #999; margin-top: 20px;">No hay datos disponibles</p>
    @endif
</body>
</html>
