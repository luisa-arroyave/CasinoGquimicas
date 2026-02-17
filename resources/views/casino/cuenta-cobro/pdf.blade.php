<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Cuenta de cobro - {{ $casino->nombre }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { margin-bottom: 16px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; font-weight: 600; }
        .text-right { text-align: right; }
        .totales { margin-top: 20px; padding: 12px; background: #f9f9f9; border: 1px solid #ddd; }
        .totales .fila { display: flex; justify-content: space-between; padding: 4px 0; }
        .totales .gran-total { font-size: 14px; font-weight: bold; margin-top: 8px; padding-top: 8px; border-top: 2px solid #333; }
        .footer { margin-top: 24px; font-size: 9px; color: #888; }
    </style>
</head>
<body>
    <h1>Cuenta de cobro</h1>
    <div class="meta">
        <strong>{{ $casino->nombre }}</strong><br>
        Período: {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}<br>
        Generado: {{ now()->format('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Horario</th>
                <th>Persona</th>
                <th>Empresa</th>
                <th class="text-right">P. casino</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consumos as $c)
            <tr>
                <td>{{ $c->fecha_consumo?->format('d/m/Y') }}</td>
                <td>{{ $c->hora_consumo ? (is_object($c->hora_consumo) ? $c->hora_consumo->format('H:i') : substr($c->hora_consumo, 0, 5)) : '—' }}</td>
                <td>{{ $c->horarioConsumo?->nombre ?? '—' }}</td>
                <td>{{ $c->usuario ? $c->usuario->nombres . ' (' . $c->usuario->documento . ')' : ($c->visitante ? $c->visitante->nombre : '—') }}</td>
                <td>{{ $c->empresa?->nombre ?? '—' }}</td>
                <td class="text-right">$ {{ number_format($c->precio_casino, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        <div class="fila"><span>Total almuerzos / vales:</span><strong>{{ $total_almuerzos }}</strong></div>
        <div class="fila gran-total"><span>Valor total:</span><span>$ {{ number_format($valor_total, 0, ',', '.') }}</span></div>
    </div>

    <div class="footer">
        {{ config('app.name') }} — Cuenta de cobro #{{ $cuenta->id_cuenta }}
    </div>
</body>
</html>
