@extends('layouts.app')

@section('title', 'Editar visitante')
@section('page-title', 'Editar visitante')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('admin.visitantes.update', $visitante) }}" method="POST" class="space-y-4 bg-white p-6 rounded-xl border border-slate-200">
        @csrf
        @method('PUT')
        <div>
            <label for="nombre" class="block text-sm font-medium text-slate-700">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $visitante->nombre) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="documento" class="block text-sm font-medium text-slate-700">Documento</label>
            <input type="text" name="documento" id="documento" value="{{ old('documento', $visitante->documento) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('documento')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="empresa_visita" class="block text-sm font-medium text-slate-700">Empresa que visita</label>
            <select name="empresa_visita" id="empresa_visita" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 min-h-[44px]">
                <option value="">Seleccione...</option>
                @foreach($empresas as $e)
                    <option value="{{ $e->nombre }}" {{ old('empresa_visita', $visitante->empresa_visita) == $e->nombre ? 'selected' : '' }}>{{ $e->nombre }}</option>
                @endforeach
            </select>
            @error('empresa_visita')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="area_visita" class="block text-sm font-medium text-slate-700">Área que visita</label>
            <input type="text" name="area_visita" id="area_visita" value="{{ old('area_visita', $visitante->area_visita) }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('area_visita')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Actualizar</button>
            <a href="{{ route('admin.visitantes.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
</div>
@endsection
