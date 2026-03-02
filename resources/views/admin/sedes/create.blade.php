@extends('layouts.app')

@section('title', 'Nueva sede')
@section('page-title', 'Nueva sede')

@section('content')
<div class="max-w-xl">
    <form action="{{ route('admin.sedes.store') }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 space-y-4">
        @csrf
        <div>
            <label for="nombre" class="block text-sm font-medium text-slate-700">Nombre</label>
            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required placeholder="Ej. PLANTACALOTO, OFICINASCALI" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
            @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Guardar</button>
            <a href="{{ route('admin.sedes.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
</div>
@endsection
