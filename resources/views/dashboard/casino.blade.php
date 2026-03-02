@extends('layouts.app')

@section('title', 'Dashboard Casino - ' . config('app.name'))
@section('page-title', 'Dashboard Casino')

@section('content')
<div class="space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-slate-800">Indicadores de Consumo</h2>
        <p class="mt-2 text-slate-600">
            Comparativa de consumos: mes anterior vs mes actual.
        </p>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
            <div class="min-w-0 h-full min-h-[220px] flex">
                <div class="w-full h-full min-h-[220px]">
                    <canvas id="chart-consumos-mensuales"></canvas>
                </div>
            </div>
            <div class="flex flex-col gap-4 min-w-0">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-6 shadow-sm flex gap-4">
                    <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-500">Valor total almuerzos (lo que va del mes)</p>
                        <p class="mt-2 text-2xl font-bold text-slate-800">$ {{ number_format($valorTotalAlmuerzosMesActual ?? 0, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $mesActual ?? '' }}</p>
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-6 shadow-sm flex gap-4">
                    <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-sky-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m0 4h10v6H9V9zm0 0V7m0 2h10"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-500">Número total de registros del mes</p>
                        <p class="mt-2 text-2xl font-bold text-slate-800">{{ number_format($consumoMesActual ?? 0, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $mesActual ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-8 pt-6 border-t border-slate-200">
            <h3 class="text-base font-semibold text-slate-800">Consumos por día de la semana (semana en curso)</h3>
            <p class="mt-1 text-sm text-slate-500">Cantidad de registros por día, con la fecha.</p>
            <div class="mt-4 w-full h-44 min-h-0">
                <canvas id="chart-consumos-semana"></canvas>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('chart-consumos-mensuales');
    if (!ctx) return;
    var mesAnterior = @json($mesAnterior ?? 'Mes anterior');
    var mesActual = @json($mesActual ?? 'Mes actual');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [mesAnterior, mesActual],
            datasets: [{
                label: 'Consumos',
                data: [{{ (int) ($consumoMesAnterior ?? 0) }}, {{ (int) ($consumoMesActual ?? 0) }}],
                backgroundColor: ['rgba(100, 116, 139, 0.7)', 'rgba(30, 41, 59, 0.9)'],
                borderColor: ['rgb(100, 116, 139)', 'rgb(30, 41, 59)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
    var ctxSemana = document.getElementById('chart-consumos-semana');
    if (ctxSemana) {
        // Desvanecido de azules para los días de la semana
        var pasteles = [
            'rgba(239, 246, 255, 0.9)', // azul muy claro
            'rgba(219, 234, 254, 0.9)',
            'rgba(191, 219, 254, 0.9)',
            'rgba(147, 197, 253, 0.9)',
            'rgba(96, 165, 250, 0.9)',
            'rgba(59, 130, 246, 0.9)',
            'rgba(37, 99, 235, 0.9)'   // azul más intenso
        ];
        var pastelesBorde = [
            'rgb(239, 246, 255)',
            'rgb(219, 234, 254)',
            'rgb(191, 219, 254)',
            'rgb(147, 197, 253)',
            'rgb(96, 165, 250)',
            'rgb(59, 130, 246)',
            'rgb(37, 99, 235)'
        ];
        new Chart(ctxSemana, {
            type: 'bar',
            data: {
                labels: @json($semanaLabels ?? []),
                datasets: [{
                    label: 'Consumos',
                    data: @json($semanaData ?? []),
                    backgroundColor: pasteles,
                    borderColor: pastelesBorde,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
