@extends('layouts.app')

@section('title', 'Pedidos a domicilio')
@section('page-title', 'Pedidos a domicilio pendientes')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <form method="GET" class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-3">
        <label for="fecha" class="text-sm font-medium text-slate-700">Fecha</label>
        <input type="date" name="fecha" id="fecha" value="{{ request('fecha', date('Y-m-d')) }}" class="rounded-lg border-slate-300 shadow-sm min-h-[48px] px-4 py-2.5 w-full sm:w-auto">
        <button type="submit" class="min-h-[48px] px-4 py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-700 active:bg-slate-600 transition touch-manipulation font-medium w-full sm:w-auto">Filtrar</button>
    </form>

    <x-responsive-table-wrapper>
        <table class="min-w-full divide-y divide-slate-200 table-cards-mobile">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Fecha / Hora</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Consumidor</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Empresa</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Casino / Horario</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Dirección</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Estado</th>
                    <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($pedidos as $p)
                <tr class="hover:bg-slate-50 transition-colors even:bg-slate-50/50">
                    <td class="px-4 sm:px-6 py-3 text-sm text-slate-800" data-label="Fecha / Hora">
                        {{ $p->fecha_consumo->format('d/m/Y') }} {{ \Carbon\Carbon::parse($p->hora_consumo)->format('H:i') }}
                    </td>
                    <td class="px-4 sm:px-6 py-3 text-sm" data-label="Consumidor">
                        @if($p->usuario)
                            {{ $p->usuario->nombres }} ({{ $p->usuario->documento }})
                        @else
                            {{ $p->visitante->nombre ?? '-' }} (visitante)
                        @endif
                    </td>
                    <td class="px-4 sm:px-6 py-3 text-sm" data-label="Empresa">{{ $p->empresa->nombre ?? '-' }}</td>
                    <td class="px-4 sm:px-6 py-3 text-sm" data-label="Casino / Horario">{{ $p->casino->nombre ?? '-' }} / {{ $p->horarioConsumo->nombre ?? '-' }}</td>
                    <td class="px-4 sm:px-6 py-3 text-sm text-slate-600" data-label="Dirección">{{ $p->direccion_entrega ?? '-' }}</td>
                    <td class="px-4 sm:px-6 py-3 text-sm" data-label="Estado">
                        <span class="px-2 py-0.5 text-xs font-medium rounded {{ $p->estado === 'ENTREGADO' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ $p->estado }}</span>
                    </td>
                    <td class="px-4 sm:px-6 py-3 text-right text-sm" data-label="Acción">
                        @if($p->estado !== 'ENTREGADO')
                        <form action="{{ route('operativo.pedidos.marcar-entregado', $p) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="min-h-[44px] sm:min-h-0 inline-flex items-center justify-center px-3 py-2.5 sm:py-1.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 rounded-lg transition touch-manipulation w-full sm:w-auto">Marcar ENTREGADO</button>
                        </form>
                        @else
                        <span class="text-slate-400 text-sm">Entregado</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 sm:px-6 py-12 text-center text-slate-500 text-sm">No hay pedidos a domicilio pendientes para la fecha seleccionada.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-responsive-table-wrapper>

    <div class="mt-4 overflow-x-auto">{{ $pedidos->links() }}</div>
</div>
@endsection
