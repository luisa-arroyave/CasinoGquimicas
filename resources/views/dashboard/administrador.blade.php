@extends('layouts.app')

@section('title', 'Dashboard Administrador - ' . config('app.name'))
@section('page-title', 'Dashboard Administrador')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <h2 class="text-base sm:text-lg font-semibold text-slate-800">Panel de Administración</h2>
        <p class="mt-2 text-sm sm:text-base text-slate-600">
            Tienes acceso completo al sistema: empresas, usuarios, casinos, reportes y configuración.
        </p>
    </div>

    {{-- Tarjetas con datos: empleados por empresa, vales quincena 1-15 y 16-31 --}}
    <div class="grid grid-cols-1 gap-3 sm:gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
            <p class="text-xs sm:text-sm font-medium text-slate-500">Vales quincena 1–15</p>
            <p class="mt-1 text-2xl sm:text-3xl font-semibold text-slate-800">{{ number_format($valesQuincena1_15) }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $mesActual }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
            <p class="text-xs sm:text-sm font-medium text-slate-500">Vales quincena 16–31</p>
            <p class="mt-1 text-2xl sm:text-3xl font-semibold text-slate-800">{{ number_format($valesQuincena16_31) }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $mesActual }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm sm:col-span-2 xl:col-span-1">
            <p class="text-xs sm:text-sm font-medium text-slate-500">Total empleados</p>
            <p class="mt-1 text-2xl sm:text-3xl font-semibold text-slate-800">{{ number_format($empleadosPorEmpresa->sum('usuarios_count')) }}</p>
            <p class="mt-1 text-xs text-slate-500">En {{ $empleadosPorEmpresa->count() }} empresa(s)</p>
        </div>
    </div>

    {{-- Gráfica: consumo mes actual vs mes anterior --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <h2 class="text-base sm:text-lg font-semibold text-slate-800 mb-3">Consumos: mes actual vs mes anterior</h2>
        <div class="max-w-xs sm:max-w-sm mx-auto">
            <canvas id="chart-consumo-mensual" height="140"></canvas>
        </div>
        <p class="mt-2 text-xs text-slate-500">{{ $mesActual }} vs {{ $mesAnterior }}</p>
    </div>

    {{-- Detalle: empleados por empresa --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <h2 class="text-base sm:text-lg font-semibold text-slate-800 mb-4">Número de empleados por empresa</h2>
        @if($empleadosPorEmpresa->isEmpty())
            <p class="text-sm text-slate-500">No hay empresas registradas.</p>
        @else
            <x-responsive-table-wrapper>
                <table class="min-w-full divide-y divide-slate-200 table-cards-mobile">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Empresa</th>
                            <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Empleados</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($empleadosPorEmpresa as $empresa)
                        <tr class="hover:bg-slate-50 transition-colors even:bg-slate-50/50">
                            <td class="px-4 sm:px-6 py-3 text-sm font-medium text-slate-900" data-label="Empresa">{{ $empresa->nombre }}</td>
                            <td class="px-4 sm:px-6 py-3 text-sm text-right text-slate-900" data-label="Empleados">{{ number_format($empresa->usuarios_count) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-responsive-table-wrapper>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function () {
    var ctx = document.getElementById('chart-consumo-mensual');
    if (!ctx) return;
    var mesActual = {{ (int) $consumoMesActual }};
    var mesAnterior = {{ (int) $consumoMesAnterior }};
    var maxVal = Math.max(mesActual, mesAnterior, 1);
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode([$mesAnterior, $mesActual]) !!},
            datasets: [{
                label: 'Consumos',
                data: [mesAnterior, mesActual],
                backgroundColor: ['rgba(100, 116, 139, 0.7)', 'rgba(30, 41, 59, 0.9)'],
                borderColor: ['rgb(100, 116, 139)', 'rgb(30, 41, 59)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.8,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: Math.ceil(maxVal * 1.1) || 1,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
})();
</script>
@endpush
@endsection
