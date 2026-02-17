@extends('layouts.app')

@section('title', 'Nuevo precio')
@section('page-title', 'Nuevo precio')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('admin.precios.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded-xl border border-slate-200">
        @csrf
        <div>
            <label for="id_horario" class="block text-sm font-medium text-slate-700">Horario</label>
            <select name="id_horario" id="id_horario" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @foreach($horarios as $h)
                    <option value="{{ $h->id_horario }}" {{ old('id_horario') == $h->id_horario ? 'selected' : '' }}>{{ $h->nombre }}</option>
                @endforeach
            </select>
            @error('id_horario')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="id_casino" class="block text-sm font-medium text-slate-700">Casino (vacio = global)</label>
            <select name="id_casino" id="id_casino" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                <option value="">Global</option>
                @foreach($casinos as $c)
                    <option value="{{ $c->id_casino }}" {{ old('id_casino') == $c->id_casino ? 'selected' : '' }}>{{ $c->nombre }}</option>
                @endforeach
            </select>
            @error('id_casino')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="precio_empleado" class="block text-sm font-medium text-slate-700">Precio empleado</label>
            <input type="number" name="precio_empleado" id="precio_empleado" value="{{ old('precio_empleado', 0) }}" step="0.01" min="0" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('precio_empleado')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="precio_casino" class="block text-sm font-medium text-slate-700">Precio casino</label>
            <input type="number" name="precio_casino" id="precio_casino" value="{{ old('precio_casino', 0) }}" step="0.01" min="0" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('precio_casino')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Guardar</button>
            <a href="{{ route('admin.precios.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
</div>
@endsection
