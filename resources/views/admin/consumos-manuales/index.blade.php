@extends('layouts.app')

@section('title', 'Registro manual de consumos')
@section('page-title', 'Registro manual de consumos')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h2 class="text-xl font-semibold text-slate-800">Consumos registrados</h2>
    <a href="{{ route('admin.consumos-manuales.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Registrar consumo manual</a>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-3">
    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="rounded-lg border-slate-300 shadow-sm">
    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="rounded-lg border-slate-300 shadow-sm">
    <button type="submit" class="px-4 py-2 bg-slate-200 rounded-lg hover:bg-slate-300">Filtrar</button>
</form>
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Fecha / Hora</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Consumidor</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Empresa</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Casino</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Horario</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Estado</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($consumos as $c)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $c->fecha_consumo->format('d/m/Y') }} {{ \Carbon\Carbon::parse($c->hora_consumo)->format('H:i') }}</td>
                <td class="px-4 py-3">
                    @if($c->usuario)
                        {{ $c->usuario->nombres }} ({{ $c->usuario->documento }})
                    @else
                        {{ $c->visitante->nombre ?? '-' }} (visitante)
                    @endif
                </td>
                <td class="px-4 py-3">{{ $c->empresa->nombre ?? '-' }}</td>
                <td class="px-4 py-3">{{ $c->casino->nombre ?? '-' }}</td>
                <td class="px-4 py-3">{{ $c->horarioConsumo->nombre ?? '-' }}</td>
                <td class="px-4 py-3">{{ $c->estado }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No hay consumos.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $consumos->links() }}</div>
@endsection
