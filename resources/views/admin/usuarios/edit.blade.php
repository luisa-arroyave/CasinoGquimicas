@extends('layouts.app')

@section('title', 'Editar usuario (empleado)')
@section('page-title', 'Editar usuario (empleado)')

@section('content')
<div class="max-w-4xl">
    @if ($errors->has('documento'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-900 shadow-sm" role="alert">
            <p class="font-semibold text-sm">Documento no disponible</p>
            <p class="mt-1 text-sm">{{ $errors->first('documento') }}</p>
        </div>
    @endif
    <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST" class="space-y-4 sm:space-y-5 bg-white p-4 sm:p-6 rounded-xl border border-slate-200 shadow-sm">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="documento" class="block text-sm font-medium text-slate-700 mb-1">Documento</label>
                <input type="text" name="documento" id="documento" value="{{ old('documento', $usuario->documento) }}" required class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 @error('documento') border-red-400 ring-1 ring-red-200 @enderror">
                @error('documento')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nombres" class="block text-sm font-medium text-slate-700 mb-1">Nombres</label>
                <input type="text" name="nombres" id="nombres" value="{{ old('nombres', $usuario->nombres) }}" required class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('nombres')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label for="id_empresa" class="block text-sm font-medium text-slate-700 mb-1">Empresa</label>
                <select name="id_empresa" id="id_empresa" required class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @foreach($empresas as $e)
                        <option value="{{ $e->id_empresa }}" {{ old('id_empresa', $usuario->id_empresa) == $e->id_empresa ? 'selected' : '' }}>{{ $e->nombre }}</option>
                    @endforeach
                </select>
                @error('id_empresa')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="id_rol" class="block text-sm font-medium text-slate-700 mb-1">Rol</label>
                <select name="id_rol" id="id_rol" required class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @foreach($roles as $r)
                        <option value="{{ $r->id_rol }}" data-role-nombre="{{ $r->nombre }}" {{ old('id_rol', $usuario->id_rol) == $r->id_rol ? 'selected' : '' }}>{{ $r->nombre }}</option>
                    @endforeach
                </select>
                @error('id_rol')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="id_tipo_usuario" class="block text-sm font-medium text-slate-700 mb-1">Tipo usuario</label>
                <select name="id_tipo_usuario" id="id_tipo_usuario" required class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    @foreach($tipos as $t)
                        <option value="{{ $t->id_tipo_usuario }}" data-tipo-nombre="{{ $t->nombre }}" {{ old('id_tipo_usuario', $usuario->id_tipo_usuario) == $t->id_tipo_usuario ? 'selected' : '' }}>{{ $t->nombre }}</option>
                    @endforeach
                </select>
                @error('id_tipo_usuario')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Empresa temporal (solo cuando tipo es Temporal) --}}
        <div id="bloque-empresa-temporal" class="hidden rounded-xl border border-slate-200 bg-slate-50/50 p-4">
            <p class="text-sm font-medium text-slate-700 mb-2">Empresa temporal</p>
            <p class="text-xs text-slate-500 mb-2">Seleccione la empresa temporal del colaborador.</p>
            <div class="max-w-sm">
                <select name="id_empresa_temporal" id="id_empresa_temporal" class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    <option value="">— Seleccione empresa temporal —</option>
                    @foreach($empresasTemporales as $et)
                        <option value="{{ $et->id_empresa_temporal }}" {{ old('id_empresa_temporal', $usuario->id_empresa_temporal) == $et->id_empresa_temporal ? 'selected' : '' }}>{{ $et->nombre }}</option>
                    @endforeach
                </select>
                @error('id_empresa_temporal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Empresa contratista (solo cuando tipo es Contratista) --}}
        <div id="bloque-empresa-contratista" class="hidden rounded-xl border border-slate-200 bg-slate-50/50 p-4">
            <p class="text-sm font-medium text-slate-700 mb-2">Empresa contratista</p>
            <p class="text-xs text-slate-500 mb-2">Seleccione la empresa contratista del colaborador.</p>
            <div class="max-w-sm">
                <select name="id_empresa_contratista" id="id_empresa_contratista" class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    <option value="">— Seleccione empresa contratista —</option>
                    @foreach($empresasContratistas as $ec)
                        <option value="{{ $ec->id_empresa_contratista }}" {{ old('id_empresa_contratista', $usuario->id_empresa_contratista) == $ec->id_empresa_contratista ? 'selected' : '' }}>{{ $ec->nombre }}</option>
                    @endforeach
                </select>
                @error('id_empresa_contratista')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        @php $empresasAccesoIds = old('empresas', $usuario->empresasAcceso->pluck('id_empresa')->toArray()); @endphp
        {{-- Empresas para ver consumos (solo administrador o gestión humana) --}}
        <div id="bloque-empresas-acceso" class="hidden rounded-xl border border-slate-200 bg-slate-50/50 p-4">
            <p class="text-sm font-medium text-slate-700 mb-2">Empresas cuyos registros de consumo puede ver</p>
            <p class="text-xs text-slate-500 mb-3">Seleccione una o varias empresas. Si no selecciona ninguna, verá todas.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                @foreach($empresas as $e)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="empresas[]" value="{{ $e->id_empresa }}" class="rounded border-slate-300 text-slate-600 focus:ring-slate-500"
                               {{ in_array($e->id_empresa, $empresasAccesoIds) ? 'checked' : '' }}>
                        <span class="text-sm text-slate-700">{{ $e->nombre }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Casino asignado (solo rol casino) --}}
        <div id="bloque-casino-asignado" class="hidden rounded-xl border border-slate-200 bg-slate-50/50 p-4">
            <p class="text-sm font-medium text-slate-700 mb-2">Casino asignado</p>
            <p class="text-xs text-slate-500 mb-3">Usuario con rol casino solo verá datos de este casino.</p>
            <div class="max-w-sm">
                <select name="id_casino_asignado" id="id_casino_asignado" class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    <option value="">— Seleccione un casino —</option>
                    @foreach($casinos as $c)
                        <option value="{{ $c->id_casino }}" {{ old('id_casino_asignado', $usuario->id_casino_asignado) == $c->id_casino ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
                @error('id_casino_asignado')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Sede principal y sedes adicionales (empleados) --}}
        <div id="bloque-sede" class="hidden rounded-xl border border-slate-200 bg-slate-50/50 p-4 space-y-4">
            <div>
                <label for="id_sede_principal" class="block text-sm font-medium text-slate-700 mb-1">Sede principal</label>
                <p class="text-xs text-slate-500 mb-2">Sede donde suele estar el empleado.</p>
                <select name="id_sede_principal" id="id_sede_principal" class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 max-w-sm">
                    <option value="">— Sin sede —</option>
                    @foreach($sedes as $s)
                        <option value="{{ $s->id_sede }}" {{ old('id_sede_principal', $usuario->id_sede_principal) == $s->id_sede ? 'selected' : '' }}>{{ $s->nombre }}</option>
                    @endforeach
                </select>
                @error('id_sede_principal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @php $sedesAccesoIds = old('sedes', $usuario->sedesAcceso->pluck('id_sede')->toArray()); @endphp
            <div>
                <p class="text-sm font-medium text-slate-700 mb-2">Sedes adicionales (registrar consumo en otra sede)</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto">
                    @foreach($sedes as $s)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="sedes[]" value="{{ $s->id_sede }}" class="rounded border-slate-300 text-slate-600 focus:ring-slate-500" {{ in_array($s->id_sede, $sedesAccesoIds) ? 'checked' : '' }}>
                            <span class="text-sm text-slate-700">{{ $s->nombre }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="codigo_qr" class="block text-sm font-medium text-slate-700 mb-1">Código QR (opcional)</label>
                <input type="text" name="codigo_qr" id="codigo_qr" value="{{ old('codigo_qr', $usuario->codigo_qr) }}" class="mt-0 block w-full border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('codigo_qr')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-2 pt-8">
                <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', $usuario->activo) ? 'checked' : '' }} class="rounded border-slate-300 h-5 w-5">
                <label for="activo" class="text-sm text-slate-700 cursor-pointer">Activo</label>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2 border-t border-slate-100">
            <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 border border-slate-300 rounded text-slate-700 hover:bg-slate-50 transition w-full sm:w-auto">Cancelar</a>
            <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-4 py-2.5 bg-slate-800 text-white rounded hover:bg-slate-700 transition w-full sm:w-auto font-medium">Actualizar</button>
        </div>
    </form>

    <div class="mt-6 rounded-xl border border-slate-200 bg-white p-4 sm:p-6">
        <label class="block text-sm font-medium text-slate-700 mb-2">Restablecer contraseña</label>
        <p class="text-sm text-slate-600 mb-3">Restablece la contraseña al número de documento ({{ $usuario->documento }}). El usuario deberá cambiarla en el próximo inicio de sesión.</p>
        <form action="{{ route('admin.usuarios.resetear-clave', $usuario) }}" method="POST" class="inline" onsubmit="return confirm('¿Restablecer contraseña al documento ({{ $usuario->documento }})? El usuario deberá cambiarla en el próximo inicio de sesión.');">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-amber-300 bg-amber-50 text-amber-800 rounded hover:bg-amber-100 transition font-medium text-sm">Restablecer contraseña</button>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('id_rol');
    var selTipo = document.getElementById('id_tipo_usuario');
    var bloque = document.getElementById('bloque-empresas-acceso');
    var bloqueCasino = document.getElementById('bloque-casino-asignado');
    var selectCasino = document.getElementById('id_casino_asignado');
    var bloqueSede = document.getElementById('bloque-sede');
    var bloqueEmpresaTemp = document.getElementById('bloque-empresa-temporal');
    var selectEmpresaTemp = document.getElementById('id_empresa_temporal');
    var bloqueEmpresaContr = document.getElementById('bloque-empresa-contratista');
    var selectEmpresaContr = document.getElementById('id_empresa_contratista');
    function toggleTipo() {
        var optTipo = selTipo ? selTipo.options[selTipo.selectedIndex] : null;
        var tipoNombre = optTipo ? (optTipo.getAttribute('data-tipo-nombre') || optTipo.textContent || '').trim().toLowerCase() : '';
        if (bloqueEmpresaTemp && selectEmpresaTemp) {
            if (tipoNombre === 'temporal') {
                bloqueEmpresaTemp.classList.remove('hidden');
                selectEmpresaTemp.setAttribute('required', 'required');
            } else {
                bloqueEmpresaTemp.classList.add('hidden');
                selectEmpresaTemp.value = '';
                selectEmpresaTemp.removeAttribute('required');
            }
        }
        if (bloqueEmpresaContr && selectEmpresaContr) {
            if (tipoNombre === 'contratista') {
                bloqueEmpresaContr.classList.remove('hidden');
                selectEmpresaContr.setAttribute('required', 'required');
            } else {
                bloqueEmpresaContr.classList.add('hidden');
                selectEmpresaContr.value = '';
                selectEmpresaContr.removeAttribute('required');
            }
        }
    }
    if (selTipo) selTipo.addEventListener('change', toggleTipo);
    toggleTipo();
    function toggle() {
        var opt = sel.options[sel.selectedIndex];
        var nombre = opt ? (opt.getAttribute('data-role-nombre') || '') : '';
        if (nombre === 'administrador' || nombre === 'gestionhumana') {
            bloque.classList.remove('hidden');
        } else {
            bloque.classList.add('hidden');
            if (bloque) bloque.querySelectorAll('input[name="empresas[]"]').forEach(function(cb) { cb.checked = false; });
        }
        if (bloqueCasino) {
            if (nombre === 'casino') {
                bloqueCasino.classList.remove('hidden');
            } else {
                bloqueCasino.classList.add('hidden');
                if (selectCasino) selectCasino.value = '';
            }
        }
        if (bloqueSede) {
            if (nombre === 'empleado') {
                bloqueSede.classList.remove('hidden');
            } else {
                bloqueSede.classList.add('hidden');
                var sedePrincipal = document.getElementById('id_sede_principal');
                if (sedePrincipal) sedePrincipal.value = '';
                if (bloqueSede) bloqueSede.querySelectorAll('input[name="sedes[]"]').forEach(function(cb) { cb.checked = false; });
            }
        }
    }
    sel.addEventListener('change', toggle);
    toggle();
});
</script>
@endpush
@endsection
