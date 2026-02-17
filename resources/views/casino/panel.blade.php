@extends('layouts.app')

@section('title', 'Panel Casino - ' . config('app.name'))
@section('page-title', 'Panel Casino')

@section('content')
<div class="space-y-6" x-data="panelCasino()" x-init="init()">
    {{-- Filtros: casino y rango de fechas --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="get" action="{{ route('casino.panel') }}" class="flex flex-wrap items-end gap-4" @submit.prevent="consultar()">
            <div>
                <label for="id_casino" class="block text-sm font-medium text-slate-700 mb-1">Casino / Punto</label>
                <select name="id_casino" id="id_casino" x-model="idCasino" @change="actualizarDatos()"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500 min-w-[200px]">
                    @foreach($casinos as $c)
                        <option value="{{ $c->id_casino }}" {{ $casino && $casino->id_casino == $c->id_casino ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="fecha_desde" class="block text-sm font-medium text-slate-700 mb-1">Desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" x-model="fechaDesde"
                       class="rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500">
            </div>
            <div>
                <label for="fecha_hasta" class="block text-sm font-medium text-slate-700 mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" x-model="fechaHasta"
                       class="rounded-lg border border-slate-300 px-4 py-2 text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white font-medium hover:bg-slate-700">
                Consultar
            </button>
            <button type="button" @click="hoy()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 font-medium hover:bg-slate-50">
                Hoy
            </button>
        </form>
    </div>

    {{-- Contador de vales y totales diarios --}}
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Vales del período</p>
            <p class="mt-1 text-3xl font-bold text-slate-800" x-text="totales.cantidad_vales">0</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total valor empleado</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600" x-text="formatPeso(totales.total_precio_empleado)">$ 0</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total valor casino</p>
            <p class="mt-1 text-2xl font-bold text-slate-800" x-text="formatPeso(totales.total_precio_casino)">$ 0</p>
        </div>
    </div>

    {{-- Consumos en tiempo real --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">Consumos del período</h2>
            <span class="text-sm text-slate-500" x-show="esHoy" x-transition>Actualización automática cada 30 s</span>
            <button type="button" @click="actualizarDatos()" class="text-sm text-slate-600 hover:text-slate-800 underline">Actualizar</button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Horario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Persona</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Empresa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">P. empleado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">P. casino</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200" x-ref="tbody">
                    @foreach($consumos as $c)
                    <tr class="bg-white hover:bg-slate-50">
                        <td class="px-6 py-3 text-sm text-slate-900">{{ $c->fecha_consumo?->format('d/m/Y') }}</td>
                        <td class="px-6 py-3 text-sm text-slate-900">{{ $c->hora_consumo ? (is_object($c->hora_consumo) ? $c->hora_consumo->format('H:i') : substr($c->hora_consumo, 0, 5)) : '—' }}</td>
                        <td class="px-6 py-3 text-sm text-slate-600">{{ $c->horarioConsumo?->nombre ?? '—' }}</td>
                        <td class="px-6 py-3 text-sm text-slate-900">{{ $c->usuario ? $c->usuario->nombres . ' (' . $c->usuario->documento . ')' : ($c->visitante ? $c->visitante->nombre : '—') }}</td>
                        <td class="px-6 py-3 text-sm text-slate-600">{{ $c->empresa?->nombre ?? '—' }}</td>
                        <td class="px-6 py-3"><span class="px-2 py-0.5 text-xs font-medium rounded {{ $c->estado === 'ENTREGADO' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ $c->estado }}</span></td>
                        <td class="px-6 py-3 text-sm text-right text-slate-900">$ {{ number_format($c->precio_empleado, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-sm text-right text-slate-900">$ {{ number_format($c->precio_casino, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @if($consumos->isEmpty())
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-slate-500">No hay consumos en el período seleccionado.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/alpinejs@3/dist/cdn.min.js" defer></script>
<script>
function panelCasino() {
    const urlDatos = '{{ url("/casino/panel/datos") }}';
    const hoyStr = new Date().toISOString().slice(0, 10);
    return {
        idCasino: '{{ $casino?->id_casino ?? "" }}',
        fechaDesde: '{{ $fecha_desde }}',
        fechaHasta: '{{ $fecha_hasta }}',
        totales: {
            cantidad_vales: {{ $totales['cantidad_vales'] }},
            total_precio_empleado: {{ $totales['total_precio_empleado'] }},
            total_precio_casino: {{ $totales['total_precio_casino'] }},
        },
        consumos: [],
        intervalo: null,
        get esHoy() {
            return this.fechaDesde === hoyStr && this.fechaHasta === hoyStr;
        },
        init() {
            if (this.esHoy && this.idCasino) {
                this.intervalo = setInterval(() => this.actualizarDatos(), 30000);
            }
        },
        formatPeso(n) {
            return '$ ' + (typeof n === 'number' ? n.toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }) : n);
        },
        consultar() {
            window.location.href = '{{ route("casino.panel") }}?id_casino=' + this.idCasino + '&fecha_desde=' + this.fechaDesde + '&fecha_hasta=' + this.fechaHasta;
        },
        hoy() {
            this.fechaDesde = hoyStr;
            this.fechaHasta = hoyStr;
            this.consultar();
        },
        actualizarDatos() {
            if (!this.idCasino) return;
            fetch(urlDatos + '?id_casino=' + this.idCasino + '&fecha_desde=' + this.fechaDesde + '&fecha_hasta=' + this.fechaHasta, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                this.totales = data.totales;
                this.consumos = data.consumos || [];
                this.renderTabla();
            })
            .catch(() => {});
        },
        renderTabla() {
            const tbody = this.$refs.tbody;
            if (!tbody) return;
            const fmt = (n) => (typeof n === 'number' ? n : parseFloat(n)).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            const rows = this.consumos.length ? this.consumos.map(c => {
                const fecha = c.fecha_consumo ? c.fecha_consumo.split('-').reverse().join('/') : '—';
                const hora = c.hora_consumo ? String(c.hora_consumo).substr(0, 5) : '—';
                const estadoClass = c.estado === 'ENTREGADO' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800';
                return `<tr class="bg-white hover:bg-slate-50"><td class="px-6 py-3 text-sm text-slate-900">${fecha}</td><td class="px-6 py-3 text-sm text-slate-900">${hora}</td><td class="px-6 py-3 text-sm text-slate-600">${c.horario || '—'}</td><td class="px-6 py-3 text-sm text-slate-900">${c.persona || '—'}</td><td class="px-6 py-3 text-sm text-slate-600">${c.empresa || '—'}</td><td class="px-6 py-3"><span class="px-2 py-0.5 text-xs font-medium rounded ${estadoClass}">${c.estado || '—'}</span></td><td class="px-6 py-3 text-sm text-right text-slate-900">$ ${fmt(c.precio_empleado)}</td><td class="px-6 py-3 text-sm text-right text-slate-900">$ ${fmt(c.precio_casino)}</td></tr>`;
            }).join('') : '<tr><td colspan="8" class="px-6 py-8 text-center text-slate-500">No hay consumos en el período seleccionado.</td></tr>';
            tbody.innerHTML = rows;
        }
    };
}
</script>
@endpush
@endsection
