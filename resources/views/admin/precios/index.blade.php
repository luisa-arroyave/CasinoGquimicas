@extends('layouts.app')

@section('title', 'Precios')
@section('page-title', 'Precios')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h2 class="text-xl font-semibold text-slate-800">Tarifas por horario y casino</h2>
    <a href="{{ route('admin.precios.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Nuevo precio</a>
</div>
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Horario</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Casino</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Precio empleado</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Precio casino</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-600 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($precios as $p)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $p->horarioConsumo->nombre ?? '-' }}</td>
                <td class="px-4 py-3">{{ $p->casino ? $p->casino->nombre : 'Global' }}</td>
                <td class="px-4 py-3">{{ number_format($p->precio_empleado, 2) }}</td>
                <td class="px-4 py-3">{{ number_format($p->precio_casino, 2) }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.precios.edit', $p) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                    <form action="{{ route('admin.precios.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Eliminar?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No hay precios configurados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $precios->links() }}</div>
@endsection
