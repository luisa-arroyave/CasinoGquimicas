@extends('layouts.app')

@section('title', 'Panel Casino - ' . config('app.name'))
@section('page-title', 'Panel Casino')

@section('content')
<div class="space-y-6" x-data="panelCasino()" x-init="init()">
    {{-- Cabecera: casino actual y fecha (día de la semana) --}}
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-slate-500">Casino / Punto</p>
                <p class="mt-1 text-base font-semibold text-slate-800">
                    {{ $casino?->nombre ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Fecha</p>
                <p class="mt-1 text-base font-semibold text-slate-800">
                    {{ \Carbon\Carbon::parse($fecha_desde)->translatedFormat('l d \\d\\e F \\d\\e Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Marcador de consumos ENTREGADOS (período seleccionado) --}}
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">CONTADOR</p>
            <p class="mt-1 text-3xl font-bold text-slate-800" x-text="totales.cantidad_vales">0</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total valor casino</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600" x-text="formatPeso(totales.total_precio_casino)">$ 0</p>
        </div>
    </div>

    {{-- Consumos en tiempo real --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between relative">
            <h2 class="text-lg font-semibold text-slate-800">Consumos del período</h2>
            <div class="flex items-center gap-4">
                <span class="text-sm text-slate-500" x-show="esHoy" x-transition>Actualización automática cada 3 s</span>
                <div class="flex items-center gap-1 text-xs text-slate-500">
                    <span class="inline-block w-2.5 h-2.5 rounded-full"
                          :class="lectorActivo ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                    <span x-text="lectorActivo ? 'Lector activo' : 'Lector inactivo'"></span>
                </div>
                <button type="button" @click="actualizarDatos()" class="text-sm text-slate-600 hover:text-slate-800 underline">Actualizar</button>
            </div>
            {{-- Input oculto para lector QR como teclado --}}
            <input type="text" id="input-qr-panel"
                   class="absolute -left-[9999px] top-0 opacity-0"
                   autocomplete="off">
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Empresa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200" x-ref="tbody">
                    @foreach($consumos as $c)
                    <tr class="bg-white hover:bg-slate-50">
                        <td class="px-6 py-3 text-sm text-slate-900">{{ $c->fecha_consumo?->format('d/m/Y') }}</td>
                        <td class="px-6 py-3 text-sm text-slate-900">{{ $c->hora_consumo ? (is_object($c->hora_consumo) ? $c->hora_consumo->format('H:i') : substr($c->hora_consumo, 0, 5)) : '—' }}</td>
                        <td class="px-6 py-3 text-sm text-slate-900">{{ $c->usuario ? $c->usuario->nombres . ' (' . $c->usuario->documento . ')' : ($c->visitante ? $c->visitante->nombre : '—') }}</td>
                        <td class="px-6 py-3 text-sm text-slate-600">{{ $c->empresa?->nombre ?? '—' }}</td>
                    </tr>
                    @endforeach
                    @if($consumos->isEmpty())
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">No hay consumos en el período seleccionado.</td>
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
    const urlValidarQr = '{{ url("/api/consumo/validar-qr") }}';
    const hoyStr = new Date().toISOString().slice(0, 10);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
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
        lectorActivo: false,
        lectorTimeout: null,
        get esHoy() {
            return this.fechaDesde === hoyStr && this.fechaHasta === hoyStr;
        },
        init() {
            const self = this;
            if (this.esHoy && this.idCasino) {
                this.intervalo = setInterval(() => this.actualizarDatos(), 3000);
            }

            // Soporte para lector QR conectado como teclado: escribe en input oculto y envía Enter
            const inputQrPanel = document.getElementById('input-qr-panel');
            if (inputQrPanel) {
                const focusInput = () => inputQrPanel.focus();
                focusInput();
                inputQrPanel.addEventListener('blur', () => {
                    setTimeout(focusInput, 50);
                });
                inputQrPanel.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const valor = inputQrPanel.value;
                        inputQrPanel.value = '';
                        if (!valor || !valor.trim()) return;
                        self.enviarQr(valor);
                    }
                });
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
            const rows = this.consumos.length ? this.consumos.map(c => {
                const fecha = c.fecha_consumo ? c.fecha_consumo.split('-').reverse().join('/') : '—';
                const hora = c.hora_consumo ? String(c.hora_consumo).substr(0, 5) : '—';
                const nombre = c.persona || '—';
                const empresa = c.empresa || '—';
                return `<tr class="bg-white hover:bg-slate-50"><td class="px-6 py-3 text-sm text-slate-900">${fecha}</td><td class="px-6 py-3 text-sm text-slate-900">${hora}</td><td class="px-6 py-3 text-sm text-slate-900">${nombre}</td><td class="px-6 py-3 text-sm text-slate-600">${empresa}</td></tr>`;
            }).join('') : '<tr><td colspan="4" class="px-6 py-8 text-center text-slate-500">No hay consumos en el período seleccionado.</td></tr>';
            tbody.innerHTML = rows;
        },
        enviarQr(codigoQr) {
            if (!this.idCasino) return;
            const payload = {
                codigo_qr: String(codigoQr).trim(),
                id_casino: parseInt(this.idCasino, 10)
            };
            fetch(urlValidarQr, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json())
            .then(data => {
                if (data && data.ok) {
                    // Marcar lector como activo cuando se valide correctamente un QR
                    this.lectorActivo = true;
                    if (this.lectorTimeout) clearTimeout(this.lectorTimeout);
                    this.lectorTimeout = setTimeout(() => { this.lectorActivo = false; }, 5000);

                    // Refrescar tabla y totales inmediatamente al validar el QR
                    this.actualizarDatos();
                } else {
                    console.warn('Error al validar QR en panel:', data);
                }
            })
            .catch(err => {
                console.error('Error de red al validar QR en panel:', err);
            });
        }
    };
}
</script>
@endpush
@endsection
