@extends('layouts.app')

@section('title', 'Editar empresa contratista')
@section('page-title', 'Editar empresa contratista')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('admin.empresas-contratistas.update', $empresaContratista) }}" method="POST" class="space-y-4 bg-white p-6 rounded-xl border border-slate-200">
        @csrf
        @method('PUT')
        <div>
            <label for="nit" class="block text-sm font-medium text-slate-700">NIT</label>
            <input type="text" name="nit" id="nit" value="{{ old('nit', $empresaContratista->nit) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('nit')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="nombre" class="block text-sm font-medium text-slate-700">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $empresaContratista->nombre) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="activa" id="activa" value="1" {{ old('activa', $empresaContratista->activa) ? 'checked' : '' }} class="rounded border-slate-300 h-4 w-4">
            <label for="activa" class="text-sm text-slate-700">Activa</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Actualizar</button>
            <a href="{{ route('admin.empresas-contratistas.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
</div>
@endsection
