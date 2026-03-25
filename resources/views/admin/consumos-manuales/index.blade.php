@extends('layouts.app')

@section('title', 'Registro manual de consumos')
@section('page-title', 'Registro manual de consumos')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h2 class="text-xl font-semibold text-slate-800">Consumos registrados</h2>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.consumos-manuales.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Registrar consumo manual</a>
        <a href="{{ route('admin.consumos-manuales.plantilla-importar') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Descargar plantilla Excel
        </a>
    </div>
</div>

<div class="mb-6 p-4 rounded-xl border border-slate-200 bg-white">
    <h3 class="text-base font-semibold text-slate-800 mb-2">Importar desde Excel</h3>
    <p class="text-sm text-slate-600 mb-3">Suba un archivo con las columnas: documento, nombres_consumidor, id_empresa, id_casino, <strong>tipo_comida</strong> (ALMUERZO, CENA, REFRIGERIO, según hoja Referencia horarios), fecha_consumo, hora_consumo (opcional). Ese valor se guarda en base de datos. Si omite tipo_comida, se usa el primer horario activo. El resto (precios, estado ENTREGADO, domicilio o en sitio según el casino) se completa automáticamente.</p>
    <form action="{{ route('admin.consumos-manuales.importar') }}" method="POST" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
        @csrf
        <div>
            <input type="file" name="archivo" accept=".xlsx,.xls,.csv" required
                   class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
        </div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Importar</button>
    </form>
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
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Tipo comida</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Estado</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($consumos as $c)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $c->fecha_consumo->format('d/m/Y') }} {{ \Carbon\Carbon::parse($c->hora_consumo)->format('H:i') }}</td>
                <td class="px-4 py-3">{{ $c->display_consumidor }}</td>
                <td class="px-4 py-3">{{ $c->display_empresa }}</td>
                <td class="px-4 py-3">{{ $c->display_casino }}</td>
                <td class="px-4 py-3">{{ $c->tipo_comida ?? $c->display_horario }}</td>
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
