@extends('layouts.app')

@section('title', 'Editar empresa')
@section('page-title', 'Editar empresa')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('admin.empresas.update', $empresa) }}" method="POST" class="space-y-4 bg-white p-6 rounded-xl border border-slate-200">
        @csrf
        @method('PUT')
        <div>
            <label for="NIT" class="block text-sm font-medium text-slate-700">NIT</label>
            <input type="text" name="NIT" id="NIT" value="{{ old('NIT', $empresa->NIT) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('NIT')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="nombre" class="block text-sm font-medium text-slate-700">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $empresa->nombre) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="correos_cuenta_cobro" class="block text-sm font-medium text-slate-700">Correos para cuenta de cobro (opcional)</label>
            <textarea name="correos_cuenta_cobro" id="correos_cuenta_cobro" rows="2" placeholder="contabilidad@empresa.com, otro@empresa.com"
                      class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">{{ old('correos_cuenta_cobro', $empresa->correos_cuenta_cobro) }}</textarea>
            <p class="mt-1 text-xs text-slate-500">Varios correos separados por coma, punto y coma o espacio. Se usarán al enviar la cuenta de cobro desde el casino.</p>
            @error('correos_cuenta_cobro')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="activa" id="activa" value="1" {{ old('activa', $empresa->activa) ? 'checked' : '' }} class="rounded border-slate-300">
            <label for="activa" class="ml-2 text-sm text-slate-700">Activa</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Actualizar</button>
            <a href="{{ route('admin.empresas.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
</div>
@endsection
