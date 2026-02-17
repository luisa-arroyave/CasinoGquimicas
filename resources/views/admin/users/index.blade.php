@extends('layouts.app')

@section('title', 'Usuarios del sistema')
@section('page-title', 'Usuarios del sistema')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <h2 class="text-xl font-semibold text-slate-800">Cuentas de acceso (login)</h2>
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Nuevo usuario</a>
</div>
<div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Nombre</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Email</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Rol</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-600 uppercase">Empresas</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-600 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($users as $u)
            <tr class="hover:bg-slate-50">
                <td class="px-4 py-3 text-slate-800">{{ $u->name }}</td>
                <td class="px-4 py-3">{{ $u->email }}</td>
                <td class="px-4 py-3">{{ $u->role }}</td>
                <td class="px-4 py-3 text-sm text-slate-600">{{ $u->empresas->pluck('nombre')->join(', ') ?: '—' }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.users.edit', $u) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                    @if($u->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Eliminar este usuario?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">No hay usuarios del sistema.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
