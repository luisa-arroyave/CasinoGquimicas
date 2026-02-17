@extends('layouts.app')

@section('title', 'Roles')
@section('page-title', 'Roles')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h2 class="text-xl font-semibold text-slate-800">Roles empresariales</h2>
    <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Nuevo rol</a>
</div>
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Nombre</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Usuarios</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-600 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($roles as $r)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $r->nombre }}</td>
                <td class="px-4 py-3">{{ $r->usuarios_count ?? 0 }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.roles.edit', $r) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                    <form action="{{ route('admin.roles.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este rol?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No hay roles.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $roles->links() }}</div>
@endsection
