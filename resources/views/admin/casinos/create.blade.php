@extends('layouts.app')

@section('title', 'Nuevo casino')
@section('page-title', 'Nuevo casino')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.casinos.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded-xl border border-slate-200">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="NIT" class="block text-sm font-medium text-slate-700">NIT</label>
                <input type="text" name="NIT" id="NIT" value="{{ old('NIT') }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('NIT')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nombre" class="block text-sm font-medium text-slate-700">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <p class="block text-sm font-medium text-slate-700 mb-1">Empresas asignadas</p>
                <p class="text-xs text-slate-500 mb-2">Seleccione las empresas cuyos empleados pueden consumir en este casino.</p>
                <div class="rounded-lg border border-slate-300 bg-slate-50/50 p-4 max-h-48 overflow-y-auto">
                    <div class="flex flex-wrap gap-x-6 gap-y-2">
                        @foreach($empresas as $e)
                        <label class="flex items-center gap-2 cursor-pointer hover:bg-slate-100 rounded px-2 py-1 -mx-2 -my-1">
                            <input type="checkbox" name="empresas[]" value="{{ $e->id_empresa }}" {{ in_array($e->id_empresa, old('empresas', [])) ? 'checked' : '' }} class="rounded border-slate-300 text-slate-800 focus:ring-slate-500">
                            <span class="text-sm text-slate-700">{{ $e->nombre }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @error('empresas')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="id_sede" class="block text-sm font-medium text-slate-700">Sede</label>
                <select name="id_sede" id="id_sede" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 min-h-[48px]">
                    <option value="">— Sin sede —</option>
                    @foreach($sedes as $s)
                        <option value="{{ $s->id_sede }}" {{ old('id_sede') == $s->id_sede ? 'selected' : '' }}>{{ $s->nombre }}</option>
                    @endforeach
                </select>
                @error('id_sede')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tipo_casino" class="block text-sm font-medium text-slate-700">Tipo casino</label>
                <select name="tipo_casino" id="tipo_casino" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 min-h-[48px]">
                    <option value="">Seleccione...</option>
                    <option value="INTERNO" {{ old('tipo_casino') === 'INTERNO' ? 'selected' : '' }}>INTERNO</option>
                    <option value="EXTERNO" {{ old('tipo_casino') === 'EXTERNO' ? 'selected' : '' }}>EXTERNO</option>
                    <option value="DOMICILIO" {{ old('tipo_casino') === 'DOMICILIO' ? 'selected' : '' }}>DOMICILIO</option>
                </select>
                @error('tipo_casino')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', true) ? 'checked' : '' }} class="rounded border-slate-300">
            <label for="activo" class="ml-2 text-sm text-slate-700">Activo</label>
        </div>

        <div class="border-t border-slate-200 pt-4 mt-6">
            <h3 class="text-sm font-semibold text-slate-800 mb-3">Precios por horario</h3>
            <p class="text-xs text-slate-500 mb-3">Opcional: defina el precio empleado y precio casino por cada horario de consumo.</p>
            <div class="space-y-3">
                @foreach($horarios as $h)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-end border-b border-slate-100 pb-3">
                    <p class="text-sm font-medium text-slate-700 sm:col-span-1">{{ $h->nombre }}</p>
                    <div>
                        <label for="precios_{{ $h->id_horario }}_empleado" class="block text-xs text-slate-500">P. empleado</label>
                        <input type="number" name="precios[{{ $h->id_horario }}][precio_empleado]" id="precios_{{ $h->id_horario }}_empleado" value="{{ old("precios.{$h->id_horario}.precio_empleado", '') }}" step="0.01" min="0" placeholder="0" class="mt-0.5 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm min-h-[40px]">
                    </div>
                    <div>
                        <label for="precios_{{ $h->id_horario }}_casino" class="block text-xs text-slate-500">P. casino</label>
                        <input type="number" name="precios[{{ $h->id_horario }}][precio_casino]" id="precios_{{ $h->id_horario }}_casino" value="{{ old("precios.{$h->id_horario}.precio_casino", '') }}" step="0.01" min="0" placeholder="0" class="mt-0.5 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 text-sm min-h-[40px]">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3 pt-4">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Guardar</button>
            <a href="{{ route('admin.casinos.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
</div>
@endsection
