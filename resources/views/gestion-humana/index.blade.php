@extends('layouts.app')

@section('title', 'Reportes para Descuentos de Casino - ' . config('app.name'))
@section('page-title', 'Reportes para Descuentos de Casino')

@section('content')
<div class="space-y-4 sm:space-y-6">
    @if(session('error'))
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800 text-sm">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    {{-- Filtros y descarga --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <h2 class="text-base sm:text-lg font-semibold text-slate-800 mb-4">Reportes por fechas</h2>
        <form method="get" action="{{ route('reportes.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="fecha_desde" class="block text-sm font-medium text-slate-700 mb-1">Desde</label>
                    <input type="date" name="fecha_desde" id="fecha_desde" value="{{ $fecha_desde }}" required
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 min-h-[48px] text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
                </div>
                <div>
                    <label for="fecha_hasta" class="block text-sm font-medium text-slate-700 mb-1">Hasta</label>
                    <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ $fecha_hasta }}" required
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 min-h-[48px] text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
                </div>
                <div>
                    <label for="busqueda_persona" class="block text-sm font-medium text-slate-700 mb-1">Nombre o cédula (opcional)</label>
                    <input type="text" name="busqueda_persona" id="busqueda_persona" value="{{ $busqueda_persona ?? '' }}"
                           placeholder="Filtrar por persona..."
                           class="w-full rounded-lg border border-slate-300 px-4 py-2.5 min-h-[48px] text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
                </div>
            </div>
            <div>
                <p class="block text-sm font-medium text-slate-700 mb-2">Sedes (opcional)</p>
                <p class="text-xs text-slate-500 mb-2">Marque una o varias sedes para ver <strong>todos</strong> los consumos registrados en los casinos de esas sedes (cualquier empresa). Si no marca ninguna, se aplica el alcance habitual por empresa según su perfil.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 max-h-32 overflow-y-auto rounded-lg border border-slate-300 bg-slate-50/50 p-3">
                    @php $marcadasSede = $sedes_marcadas_formulario ?? []; @endphp
                    @foreach($sedes_opciones_filtro as $op)
                        @if($op['tipo'] === 'grupo')
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="sedes[]" value="grupo:{{ $op['clave'] }}" class="rounded border-slate-300 text-slate-600 focus:ring-slate-500"
                                       {{ in_array('grupo:' . $op['clave'], $marcadasSede, true) ? 'checked' : '' }}>
                                <span class="text-sm text-slate-700">{{ $op['etiqueta'] }}</span>
                            </label>
                        @else
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="sedes[]" value="{{ $op['id_sede'] }}" class="rounded border-slate-300 text-slate-600 focus:ring-slate-500"
                                       {{ in_array((string) $op['id_sede'], $marcadasSede, true) ? 'checked' : '' }}>
                                <span class="text-sm text-slate-700">{{ $op['etiqueta'] }}</span>
                            </label>
                        @endif
                    @endforeach
                    @if(empty($sedes_opciones_filtro))
                        <p class="text-sm text-slate-500 col-span-full">No hay sedes disponibles para sus reportes.</p>
                    @endif
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="min-h-[48px] px-4 py-2.5 rounded-lg bg-slate-800 text-white font-medium hover:bg-slate-700 active:bg-slate-600 transition touch-manipulation w-full sm:w-auto">Consultar</button>
            </div>
        </form>
        <div class="mt-4">
            <p class="text-sm font-medium text-slate-700 mb-2">Reportes especiales en Excel (.xlsx)</p>
            <form id="form-reportes-excel" class="flex flex-col sm:flex-row flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label for="tipo_reporte" class="block text-xs text-slate-500 mb-1">Seleccione el informe a descargar</label>
                    <select name="tipo_reporte" id="tipo_reporte" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 min-h-[48px] text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
                        <option value="">— Seleccione un informe —</option>
                        <option value="nomina">Reporte de Nómina</option>
                        <option value="colaborador">Informe por Colaborador</option>
                        <option value="temporales">Reporte Temporales</option>
                    </select>
                </div>
                <button type="submit" id="btn-descargar-reporte" class="min-h-[48px] px-4 py-2.5 rounded-lg bg-emerald-600 text-white font-medium hover:bg-emerald-700 active:bg-emerald-800 transition touch-manipulation inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Descargar
                </button>
            </form>
            <p class="text-xs text-slate-500 mt-2">Para el Informe por Colaborador debe ingresar nombre o cédula en el campo de búsqueda.</p>
        </div>
    </div>

    {{-- Resumen por empresa --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <h2 class="text-base sm:text-lg font-semibold text-slate-800 mb-4">Consumo por empresa</h2>
        <x-responsive-table-wrapper>
            <table class="min-w-full divide-y divide-slate-200 table-cards-mobile">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Empresa</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Cantidad vales</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Total empleado</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Total casino</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($porEmpresa as $row)
                    <tr class="bg-white hover:bg-slate-50 transition-colors even:bg-slate-50/50">
                        <td class="px-4 sm:px-6 py-3 text-sm font-medium text-slate-900" data-label="Empresa">{{ $row['nombre'] }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-right text-slate-900" data-label="Cantidad vales">{{ $row['cantidad'] }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-right text-slate-900" data-label="Total empleado">$ {{ number_format($row['total_empleado'], 0, ',', '.') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-right text-slate-900" data-label="Total casino">$ {{ number_format($row['total_casino'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @if($porEmpresa->isEmpty())
                    <tr><td colspan="4" class="px-4 sm:px-6 py-8 text-center text-slate-500 text-sm">No hay datos en el período.</td></tr>
                    @endif
                </tbody>
                <tfoot class="bg-slate-50 font-semibold">
                    <tr>
                        <td class="px-4 sm:px-6 py-3 text-sm text-slate-900">Total</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-right text-slate-900">{{ $totales['cantidad'] }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-right text-slate-900">$ {{ number_format($totales['total_empleado'], 0, ',', '.') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-right text-slate-900">$ {{ number_format($totales['total_casino'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </x-responsive-table-wrapper>
    </div>

    {{-- Resumen por casino --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <h2 class="text-base sm:text-lg font-semibold text-slate-800 mb-4">Consumo por casino</h2>
        <x-responsive-table-wrapper>
            <table class="min-w-full divide-y divide-slate-200 table-cards-mobile">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Casino</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Cantidad vales</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Total empleado</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Total casino</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($porCasino as $row)
                    <tr class="bg-white hover:bg-slate-50 transition-colors even:bg-slate-50/50">
                        <td class="px-4 sm:px-6 py-3 text-sm font-medium text-slate-900" data-label="Casino">{{ $row['nombre'] }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-right text-slate-900" data-label="Cantidad vales">{{ $row['cantidad'] }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-right text-slate-900" data-label="Total empleado">$ {{ number_format($row['total_empleado'], 0, ',', '.') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-right text-slate-900" data-label="Total casino">$ {{ number_format($row['total_casino'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @if($porCasino->isEmpty())
                    <tr><td colspan="4" class="px-4 sm:px-6 py-8 text-center text-slate-500 text-sm">No hay datos en el período.</td></tr>
                    @endif
                </tbody>
            </table>
        </x-responsive-table-wrapper>
    </div>

    {{-- Detalle de consumos --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-slate-200">
            <h2 class="text-base sm:text-lg font-semibold text-slate-800">Detalle de consumos</h2>
        </div>
        <div class="w-full min-w-0 overflow-x-auto overflow-y-auto max-h-[400px]">
            <table class="min-w-full divide-y divide-slate-200 table-cards-mobile">
                    <thead class="bg-slate-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600 uppercase">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600 uppercase">Hora</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600 uppercase">Empresa</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600 uppercase">Persona</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600 uppercase">Horario</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-slate-600 uppercase">Casino</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-slate-600 uppercase">P. empleado</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-slate-600 uppercase">P. casino</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($consumos as $c)
                        <tr class="bg-white hover:bg-slate-50 transition-colors even:bg-slate-50/50">
                            <td class="px-4 py-2 text-sm text-slate-900" data-label="Fecha">{{ $c->fecha_consumo?->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm text-slate-900" data-label="Hora">{{ $c->hora_consumo ? (is_object($c->hora_consumo) ? $c->hora_consumo->format('H:i') : substr($c->hora_consumo, 0, 5)) : '—' }}</td>
                            <td class="px-4 py-2 text-sm text-slate-600" data-label="Empresa">{{ $c->display_empresa }}</td>
                            <td class="px-4 py-2 text-sm text-slate-900" data-label="Persona">{{ $c->display_consumidor }}</td>
                            <td class="px-4 py-2 text-sm text-slate-600" data-label="Horario">{{ $c->display_horario }}</td>
                            <td class="px-4 py-2 text-sm text-slate-600" data-label="Casino">{{ $c->display_casino }}</td>
                            <td class="px-4 py-2 text-sm text-right text-slate-900" data-label="P. empleado">{{ $c->display_precio_empleado }}</td>
                            <td class="px-4 py-2 text-sm text-right text-slate-900" data-label="P. casino">{{ $c->display_precio_casino }}</td>
                        </tr>
                        @endforeach
                        @if($consumos->isEmpty())
                        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500 text-sm">No hay consumos en el período.</td></tr>
                        @endif
                    </tbody>
                </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    const form = document.getElementById('form-reportes-excel');
    const select = document.getElementById('tipo_reporte');
    const btn = document.getElementById('btn-descargar-reporte');

    const rutas = {
        nomina: @json(route('reportes.exportar-excel-nomina')),
        colaborador: @json(route('reportes.exportar-excel-informe-colaborador')),
        temporales: @json(route('reportes.exportar-excel-temporales'))
    };

    select.addEventListener('change', function() {
        btn.disabled = !this.value;
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const tipo = select.value;
        if (!tipo) return;

        if (tipo === 'colaborador') {
            const busqueda = document.getElementById('busqueda_persona')?.value?.trim() || '';
            if (!busqueda) {
                alert('Para el Informe por Colaborador debe ingresar nombre o cédula en el campo de búsqueda.');
                return;
            }
        }

        const params = new URLSearchParams();
        const fechaDesde = document.getElementById('fecha_desde')?.value;
        const fechaHasta = document.getElementById('fecha_hasta')?.value;
        if (fechaDesde) params.set('fecha_desde', fechaDesde);
        if (fechaHasta) params.set('fecha_hasta', fechaHasta);

        document.querySelectorAll('input[name="sedes[]"]:checked').forEach(function(cb) {
            params.append('sedes[]', cb.value);
        });

        const busquedaPersona = document.getElementById('busqueda_persona')?.value?.trim() || '';
        if (busquedaPersona) params.set('busqueda_persona', busquedaPersona);

        const baseUrl = rutas[tipo];
        const sep = baseUrl.includes('?') ? '&' : '?';
        const url = baseUrl + sep + params.toString();
        window.location.href = url;
    });
})();
</script>
@endpush
