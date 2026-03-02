@extends('layouts.app')

@section('title', 'Sedes')
@section('page-title', 'Sedes')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-lg sm:text-xl font-semibold text-slate-800">Sedes (lugares físicos)</h2>
        <a href="{{ route('admin.sedes.create') }}" class="inline-flex items-center justify-center gap-2 min-h-[44px] px-4 py-3 sm:py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-700 transition-colors text-sm font-medium w-full sm:w-auto">
            Nueva sede
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Nombre</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Casinos</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Usuarios (sede principal)</th>
                    <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($sedes as $s)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 sm:px-6 py-3 text-sm text-slate-800">{{ $s->nombre }}</td>
                    <td class="px-4 sm:px-6 py-3 text-sm">{{ $s->casinos_count ?? 0 }}</td>
                    <td class="px-4 sm:px-6 py-3 text-sm">{{ $s->usuarios_principal_count ?? 0 }}</td>
                    <td class="px-4 sm:px-6 py-3 text-right">
                        <a href="{{ route('admin.sedes.edit', $s) }}" class="text-slate-600 hover:text-slate-900">Editar</a>
                        <form action="{{ route('admin.sedes.destroy', $s) }}" method="POST" class="inline ml-2" onsubmit="return confirm('¿Eliminar esta sede?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 sm:px-6 py-8 text-center text-slate-500">No hay sedes. Cree una para asociar casinos y usuarios.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sedes->hasPages())
    <div class="mt-4">{{ $sedes->links() }}</div>
    @endif
</div>
@endsection
