@extends('layouts.app')

@section('title', 'Nueva empresa')
@section('page-title', 'Nueva empresa')

@section('content')
<div class="w-full max-w-lg min-w-0">
    <form action="{{ route('admin.empresas.store') }}" method="POST" class="space-y-4 sm:space-y-5 bg-white p-4 sm:p-6 rounded-xl border border-slate-200 shadow-sm">
        @csrf
        <div class="grid grid-cols-1 gap-4 md:gap-5">
            <div>
                <label for="NIT" class="block text-sm font-medium text-slate-700 mb-1">NIT</label>
                <input type="text" name="NIT" id="NIT" value="{{ old('NIT') }}" required
                       class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 min-h-[48px] px-4 py-2.5">
                @error('NIT')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nombre" class="block text-sm font-medium text-slate-700 mb-1">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                       class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 min-h-[48px] px-4 py-2.5">
                @error('nombre')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex items-center gap-2 min-h-[48px]">
            <input type="checkbox" name="activa" id="activa" value="1" {{ old('activa', true) ? 'checked' : '' }} class="h-5 w-5 rounded border-slate-300">
            <label for="activa" class="text-sm text-slate-700">Activa</label>
        </div>
        <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">
            <a href="{{ route('admin.empresas.index') }}" class="inline-flex items-center justify-center min-h-[48px] px-4 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 active:bg-slate-100 transition touch-manipulation text-sm font-medium">Cancelar</a>
            <button type="submit" class="inline-flex items-center justify-center min-h-[48px] px-4 py-2.5 bg-slate-800 text-white rounded-lg hover:bg-slate-700 active:bg-slate-600 transition touch-manipulation text-sm font-medium">Guardar</button>
        </div>
    </form>
</div>
@endsection
