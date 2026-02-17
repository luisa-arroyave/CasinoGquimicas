@extends('layouts.app')

@section('title', 'Visitantes')
@section('page-title', 'Visitantes')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-lg sm:text-xl font-semibold text-slate-800">Listado de visitantes</h2>
        <a href="{{ route('admin.visitantes.create') }}" class="inline-flex items-center justify-center gap-2 min-h-[44px] px-4 py-3 sm:py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-700 active:bg-slate-600 transition-colors touch-manipulation text-sm font-medium w-full sm:w-auto">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo visitante
        </a>
    </div>

    <x-responsive-table-wrapper>
        <table class="min-w-full divide-y divide-slate-200 table-cards-mobile">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Nombre</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Documento</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Empresa visita</th>
                    <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Área visita</th>
                    <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 bg-white">
                @forelse($visitantes as $v)
                <tr class="hover:bg-slate-50 transition-colors even:bg-slate-50/50">
                    <td class="px-4 sm:px-6 py-3 text-sm text-slate-800" data-label="Nombre">{{ $v->nombre }}</td>
                    <td class="px-4 sm:px-6 py-3 text-sm text-slate-800" data-label="Documento">{{ $v->documento }}</td>
                    <td class="px-4 sm:px-6 py-3 text-sm text-slate-600" data-label="Empresa visita">{{ $v->empresa_visita ?? '—' }}</td>
                    <td class="px-4 sm:px-6 py-3 text-sm text-slate-600" data-label="Área visita">{{ $v->area_visita ?? '—' }}</td>
                    <td class="px-4 sm:px-6 py-3 text-right text-sm" data-label="Acciones">
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <a href="{{ route('admin.visitantes.edit', $v) }}" class="inline-flex items-center min-h-[44px] sm:min-h-0 px-3 py-2.5 sm:py-1.5 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors touch-manipulation">Editar</a>
                            <form action="{{ route('admin.visitantes.destroy', $v) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este visitante?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center min-h-[44px] sm:min-h-0 px-3 py-2.5 sm:py-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors touch-manipulation">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 sm:px-6 py-12 text-center text-slate-500 text-sm">No hay visitantes registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-responsive-table-wrapper>

    <div class="mt-4 overflow-x-auto">{{ $visitantes->links() }}</div>
</div>
@endsection
