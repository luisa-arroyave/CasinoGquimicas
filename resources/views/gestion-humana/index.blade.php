@extends('layouts.app')

@section('title', 'Gestión Humana - Reportes - ' . config('app.name'))
@section('page-title', 'Gestión Humana - Reportes')

@section('content')
<div class="space-y-4 sm:space-y-6">
    {{-- Filtros y descarga --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <h2 class="text-base sm:text-lg font-semibold text-slate-800 mb-4">Reportes por fechas</h2>
        <form method="get" action="{{ route('gestion-humana.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                <div class="sm:col-span-2 lg:col-span-1">
                    <label for="id_empresa" class="block text-sm font-medium text-slate-700 mb-1">Empresa (opcional)</label>
                    <select name="id_empresa" id="id_empresa" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 min-h-[48px] text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20">
                        <option value="">Todas las empresas</option>
                        @foreach($empresas as $e)
                            <option value="{{ $e->id_empresa }}" {{ $id_empresa == $e->id_empresa ? 'selected' : '' }}>{{ $e->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit" class="min-h-[48px] px-4 py-2.5 rounded-lg bg-slate-800 text-white font-medium hover:bg-slate-700 active:bg-slate-600 transition touch-manipulation w-full sm:w-auto">Consultar</button>
            </div>
        </form>
        <div class="mt-4 flex flex-col sm:flex-row flex-wrap gap-3">
            <form method="get" action="{{ route('gestion-humana.exportar-pdf') }}" class="inline w-full sm:w-auto" target="_blank">
                <input type="hidden" name="fecha_desde" value="{{ $fecha_desde }}">
                <input type="hidden" name="fecha_hasta" value="{{ $fecha_hasta }}">
                @if($id_empresa)<input type="hidden" name="id_empresa" value="{{ $id_empresa }}">@endif
                <button type="submit" class="w-full sm:w-auto min-h-[48px] inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 active:bg-red-800 transition touch-manipulation">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Descargar PDF
                </button>
            </form>
            <form method="get" action="{{ route('gestion-humana.exportar-excel') }}" class="inline w-full sm:w-auto">
                <input type="hidden" name="fecha_desde" value="{{ $fecha_desde }}">
                <input type="hidden" name="fecha_hasta" value="{{ $fecha_hasta }}">
                @if($id_empresa)<input type="hidden" name="id_empresa" value="{{ $id_empresa }}">@endif
                <button type="submit" class="w-full sm:w-auto min-h-[48px] inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 text-white font-medium hover:bg-emerald-700 active:bg-emerald-800 transition touch-manipulation">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar Excel (CSV)
                </button>
            </form>
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
                            <td class="px-4 py-2 text-sm text-slate-600" data-label="Empresa">{{ $c->empresa?->nombre ?? '—' }}</td>
                            <td class="px-4 py-2 text-sm text-slate-900" data-label="Persona">{{ $c->usuario ? $c->usuario->nombres . ' (' . $c->usuario->documento . ')' : ($c->visitante ? $c->visitante->nombre : '—') }}</td>
                            <td class="px-4 py-2 text-sm text-slate-600" data-label="Horario">{{ $c->horarioConsumo?->nombre ?? '—' }}</td>
                            <td class="px-4 py-2 text-sm text-slate-600" data-label="Casino">{{ $c->casino?->nombre ?? '—' }}</td>
                            <td class="px-4 py-2 text-sm text-right text-slate-900" data-label="P. empleado">$ {{ number_format($c->precio_empleado, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-sm text-right text-slate-900" data-label="P. casino">$ {{ number_format($c->precio_casino, 0, ',', '.') }}</td>
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
