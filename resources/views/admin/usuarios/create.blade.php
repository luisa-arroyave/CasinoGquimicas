@extends('layouts.app')

@section('title', 'Nuevo usuario (empleado)')
@section('page-title', 'Nuevo usuario (empleado)')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-4 sm:space-y-5 bg-white p-4 sm:p-6 rounded-xl border border-slate-200 shadow-sm">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="documento" class="block text-sm font-medium text-slate-700 mb-1">Documento</label>
                <input type="text" name="documento" id="documento" value="{{ old('documento') }}" required class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('documento')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nombres" class="block text-sm font-medium text-slate-700 mb-1">Nombres</label>
                <input type="text" name="nombres" id="nombres" value="{{ old('nombres') }}" required class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('nombres')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email (opcional)</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="space-y-3">
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Contraseña (opcional)</label>
                    <input type="password" name="password" id="password" class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Repetir" class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                </div>
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="id_empresa" class="block text-sm font-medium text-slate-700 mb-1">Empresa</label>
                <select name="id_empresa" id="id_empresa" required class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @foreach($empresas as $e)
                        <option value="{{ $e->id_empresa }}" {{ old('id_empresa') == $e->id_empresa ? 'selected' : '' }}>{{ $e->nombre }}</option>
                    @endforeach
                </select>
                @error('id_empresa')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="id_rol" class="block text-sm font-medium text-slate-700 mb-1">Rol</label>
                <select name="id_rol" id="id_rol" required class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @foreach($roles as $r)
                        <option value="{{ $r->id_rol }}" data-role-nombre="{{ $r->nombre }}" {{ old('id_rol') == $r->id_rol ? 'selected' : '' }}>{{ $r->nombre }}</option>
                    @endforeach
                </select>
                @error('id_rol')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="id_tipo_usuario" class="block text-sm font-medium text-slate-700 mb-1">Tipo usuario</label>
                <select name="id_tipo_usuario" id="id_tipo_usuario" required class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @foreach($tipos as $t)
                        <option value="{{ $t->id_tipo_usuario }}" {{ old('id_tipo_usuario') == $t->id_tipo_usuario ? 'selected' : '' }}>{{ $t->nombre }}</option>
                    @endforeach
                </select>
                @error('id_tipo_usuario')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Empresas para ver consumos (solo administrador o gestión humana) --}}
        <div id="bloque-empresas-acceso" class="hidden rounded-xl border border-slate-200 bg-slate-50/50 p-4">
            <p class="text-sm font-medium text-slate-700 mb-2">Empresas cuyos registros de consumo puede ver</p>
            <p class="text-xs text-slate-500 mb-3">Seleccione una o varias empresas. Si no selecciona ninguna, verá todas.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                @foreach($empresas as $e)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="empresas[]" value="{{ $e->id_empresa }}" class="rounded border-slate-300 text-slate-600 focus:ring-slate-500"
                               {{ in_array($e->id_empresa, old('empresas', [])) ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">{{ $e->nombre }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="codigo_qr" class="block text-sm font-medium text-slate-700 mb-1">Código QR (opcional)</label>
                <input type="text" name="codigo_qr" id="codigo_qr" value="{{ old('codigo_qr') }}" class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('codigo_qr')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-2 pt-8">
                <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', true) ? 'checked' : '' }} class="rounded border-slate-300 h-5 w-5">
                <label for="activo" class="text-sm text-slate-700 cursor-pointer">Activo</label>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2 border-t border-slate-100">
            <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 border border-slate-300 rounded text-slate-700 hover:bg-slate-50 transition w-full sm:w-auto">Cancelar</a>
            <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-slate-800 text-white rounded hover:bg-slate-700 transition w-full sm:w-auto font-medium">Guardar</button>
        </div>
    </form>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('id_rol');
    var bloque = document.getElementById('bloque-empresas-acceso');
    function toggle() {
        var opt = sel.options[sel.selectedIndex];
        var nombre = opt ? (opt.getAttribute('data-role-nombre') || '') : '';
        if (nombre === 'administrador' || nombre === 'gestionhumana') {
            bloque.classList.remove('hidden');
        } else {
            bloque.classList.add('hidden');
            bloque.querySelectorAll('input[name="empresas[]"]').forEach(function(cb) { cb.checked = false; });
        }
    }
    sel.addEventListener('change', toggle);
    toggle();
});
</script>
@endpush
@endsection
