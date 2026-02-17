<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte consumos - Gestión Humana</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .meta { margin-bottom: 12px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 4px 6px; text-align: left; }
        th { background: #f5f5f5; font-weight: 600; }
        .text-right { text-align: right; }
        .resumen-empresa { margin-top: 12px; }
        .resumen-empresa table { font-size: 9px; }
        .totales { margin-top: 16px; padding: 10px; background: #f0f0f0; border: 1px solid #ccc; font-weight: bold; }
        .footer { margin-top: 16px; font-size: 8px; color: #888; }
    </style>
</head>
<body>
    <h1>Reporte de consumos - Gestión Humana</h1>
    <div class="meta">
        Período: {{ \Carbon\Carbon::parse($fecha_desde)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_hasta)->format('d/m/Y') }}<br>
        Generado: {{ now()->format('d/m/Y H:i') }}
    </div>

    <h2 style="font-size: 12px; margin-top: 12px;">Resumen por empresa</h2>
    <table class="resumen-empresa">
        <thead>
            <tr>
                <th>Empresa</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Total casino</th>
            </tr>
        </thead>
        <tbody>
            @foreach($porEmpresa as $row)
            <tr>
                <td>{{ $row['nombre'] }}</td>
                <td class="text-right">{{ $row['cantidad'] }}</td>
                <td class="text-right">$ {{ number_format($row['total_casino'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        Total vales: {{ $total_general }} — Valor total: $ {{ number_format($valor_total, 0, ',', '.') }}
    </div>

    <h2 style="font-size: 12px; margin-top: 16px;">Detalle de consumos</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Empresa</th>
                <th>Persona</th>
                <th>Horario</th>
                <th>Casino</th>
                <th class="text-right">P. empleado</th>
                <th class="text-right">P. casino</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consumos as $c)
            <tr>
                <td>{{ $c->fecha_consumo?->format('d/m/Y') }}</td>
                <td>{{ $c->hora_consumo ? (is_object($c->hora_consumo) ? $c->hora_consumo->format('H:i') : substr($c->hora_consumo, 0, 5)) : '—' }}</td>
                <td>{{ $c->empresa?->nombre ?? '—' }}</td>
                <td>{{ $c->usuario ? $c->usuario->nombres . ' (' . $c->usuario->documento . ')' : ($c->visitante ? $c->visitante->nombre : '—') }}</td>
                <td>{{ $c->horarioConsumo?->nombre ?? '—' }}</td>
                <td>{{ $c->casino?->nombre ?? '—' }}</td>
                <td class="text-right">$ {{ number_format($c->precio_empleado, 0, ',', '.') }}</td>
                <td class="text-right">$ {{ number_format($c->precio_casino, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">{{ config('app.name') }} — Reporte Gestión Humana</div>
</body>
</html>
