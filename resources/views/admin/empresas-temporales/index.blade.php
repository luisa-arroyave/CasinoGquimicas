@extends('layouts.app')

@section('title', 'Empresas temporales')
@section('page-title', 'Empresas temporales')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h2 class="text-xl font-semibold text-slate-800">Empresas temporales</h2>
    <a href="{{ route('admin.empresas-temporales.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Nueva empresa temporal</a>
</div>
@if(session('success'))
    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-800 text-sm">{{ session('error') }}</div>
@endif
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Nombre</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Estado</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Usuarios</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-600 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($empresas as $e)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $e->nombre }}</td>
                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs {{ $e->activa ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">{{ $e->activa ? 'Activa' : 'Inactiva' }}</span></td>
                <td class="px-4 py-3">{{ $e->usuarios_count ?? 0 }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.empresas-temporales.edit', $e) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                    <form action="{{ route('admin.empresas-temporales.destroy', $e) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta empresa temporal?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No hay empresas temporales. <a href="{{ route('admin.empresas-temporales.create') }}" class="text-slate-700 underline">Crear una</a></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $empresas->links() }}</div>
@endsection
