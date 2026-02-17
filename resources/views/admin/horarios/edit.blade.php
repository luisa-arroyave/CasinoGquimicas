@extends('layouts.app')

@section('title', 'Editar horario')
@section('page-title', 'Editar horario')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('admin.horarios.update', $horario) }}" method="POST" class="space-y-4 bg-white p-6 rounded-xl border border-slate-200">
        @csrf
        @method('PUT')
        <div>
            <label for="nombre" class="block text-sm font-medium text-slate-700">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $horario->nombre) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="hora_inicio" class="block text-sm font-medium text-slate-700">Hora inicio</label>
            <input type="time" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio', \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i')) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('hora_inicio')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="hora_fin" class="block text-sm font-medium text-slate-700">Hora fin</label>
            <input type="time" name="hora_fin" id="hora_fin" value="{{ old('hora_fin', \Carbon\Carbon::parse($horario->hora_fin)->format('H:i')) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('hora_fin')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center">
            <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', $horario->activo) ? 'checked' : '' }} class="rounded border-slate-300">
            <label for="activo" class="ml-2 text-sm text-slate-700">Activo</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Actualizar</button>
            <a href="{{ route('admin.horarios.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
</div>
@endsection
