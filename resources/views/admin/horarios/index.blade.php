@extends('layouts.app')

@section('title', 'Horarios')
@section('page-title', 'Horarios')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h2 class="text-xl font-semibold text-slate-800">Horarios de consumo</h2>
    <a href="{{ route('admin.horarios.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Nuevo horario</a>
</div>
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Nombre</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Inicio</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Fin</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Activo</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-600 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($horarios as $h)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $h->nombre }}</td>
                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }}</td>
                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }}</td>
                <td class="px-4 py-3">{{ $h->activo ? 'Sí' : 'No' }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.horarios.edit', $h) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                    <form action="{{ route('admin.horarios.destroy', $h) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este horario?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No hay horarios registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $horarios->links() }}</div>
@endsection
