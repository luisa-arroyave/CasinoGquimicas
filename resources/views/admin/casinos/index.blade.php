@extends('layouts.app')

@section('title', 'Casinos')
@section('page-title', 'Casinos')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h2 class="text-xl font-semibold text-slate-800">Listado de casinos</h2>
    <a href="{{ route('admin.casinos.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Nuevo casino</a>
</div>
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">NIT</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Nombre</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Empresa</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Tipo</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Activo</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-600 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($casinos as $c)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $c->NIT }}</td>
                <td class="px-4 py-3 text-slate-800">{{ $c->nombre }}</td>
                <td class="px-4 py-3">{{ $c->empresa->nombre ?? '-' }}</td>
                <td class="px-4 py-3">{{ $c->tipo_casino ?? '-' }}</td>
                <td class="px-4 py-3">{{ $c->activo ? 'Sí' : 'No' }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.casinos.edit', $c) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                    <form action="{{ route('admin.casinos.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este casino?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-500">No hay casinos registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $casinos->links() }}</div>
@endsection
