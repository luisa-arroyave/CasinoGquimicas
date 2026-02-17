@extends('layouts.app')

@section('title', 'Empresas')
@section('page-title', 'Empresas')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-lg sm:text-xl font-semibold text-slate-800">Listado de empresas</h2>
        <a href="{{ route('admin.empresas.create') }}" class="inline-flex items-center justify-center gap-2 min-h-[44px] px-4 py-3 sm:py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-700 active:bg-slate-600 transition-colors touch-manipulation text-sm font-medium w-full sm:w-auto">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nueva empresa
        </a>
    </div>

    <x-responsive-table-wrapper>
        <table class="min-w-full divide-y divide-slate-200 table-cards-mobile">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">NIT</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Nombre</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Activa</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Usuarios</th>
                    <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($empresas as $e)
                <tr class="hover:bg-slate-50 transition-colors even:bg-slate-50/50">
                    <td class="px-4 sm:px-6 py-3 text-sm text-slate-800 whitespace-nowrap" data-label="NIT">{{ $e->NIT }}</td>
                    <td class="px-4 sm:px-6 py-3 text-sm text-slate-800" data-label="Nombre">{{ $e->nombre }}</td>
                    <td class="px-4 sm:px-6 py-3 text-sm" data-label="Activa">{{ $e->activa ? 'Sí' : 'No' }}</td>
                    <td class="px-4 sm:px-6 py-3 text-sm" data-label="Usuarios">{{ $e->usuarios_count ?? 0 }}</td>
                    <td class="px-4 sm:px-6 py-3 text-right text-sm" data-label="Acciones">
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <a href="{{ route('admin.empresas.edit', $e) }}" class="inline-flex items-center min-h-[44px] sm:min-h-0 px-3 py-2.5 sm:py-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors touch-manipulation">Editar</a>
                            <form action="{{ route('admin.empresas.destroy', $e) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta empresa?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center min-h-[44px] sm:min-h-0 px-3 py-2.5 sm:py-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors touch-manipulation">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 sm:px-6 py-12 text-center text-slate-500 text-sm">No hay empresas registradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-responsive-table-wrapper>

    <div class="mt-4 overflow-x-auto">{{ $empresas->links() }}</div>
</div>
@endsection
