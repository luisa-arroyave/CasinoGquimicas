<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; }
        .box { max-width: 600px; margin: 0 auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #eee; }
        .total { font-weight: bold; font-size: 1.1em; }
    </style>
</head>
<body>
    <div class="box">
        <p>Se adjunta el soporte para factura para contabilidad.</p>
        <table>
            <tr><th>Casino</th><td>{{ $cuenta->casino->nombre ?? '—' }}</td></tr>
            <tr><th>Período</th><td>{{ $cuenta->fecha_inicio->format('d/m/Y') }} - {{ $cuenta->fecha_fin->format('d/m/Y') }}</td></tr>
            <tr><th>Total vales / almuerzos</th><td>{{ $cuenta->total_vales }}</td></tr>
            <tr><th class="total">Valor total</th><td class="total">$ {{ number_format($cuenta->valor_total, 0, ',', '.') }}</td></tr>
        </table>
        <p>Saludos,<br>{{ config('app.name') }}</p>
    </div>
</body>
</html>
