@extends('layouts.app')

@section('title', 'Registrar consumo manual')
@section('page-title', 'Registrar consumo manual')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.consumos-manuales.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded-xl border border-slate-200">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de consumidor</label>
            <div class="flex gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" name="tipo_consumidor" value="usuario" {{ old('tipo_consumidor', 'usuario') == 'usuario' ? 'checked' : '' }} class="rounded border-slate-300">
                    <span class="ml-2">Empleado (usuario)</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="tipo_consumidor" value="visitante" {{ old('tipo_consumidor') == 'visitante' ? 'checked' : '' }} class="rounded border-slate-300">
                    <span class="ml-2">Visitante</span>
                </label>
            </div>
            @error('tipo_consumidor')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div id="campo-usuario">
            <label for="id_usuario" class="block text-sm font-medium text-slate-700">Empleado</label>
            <select name="id_usuario" id="id_usuario" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                <option value="">Seleccione empleado</option>
                @foreach($usuarios as $u)
                    <option value="{{ $u->id_usuario }}" {{ old('id_usuario') == $u->id_usuario ? 'selected' : '' }} data-empresa="{{ $u->id_empresa }}">{{ $u->nombres }} - {{ $u->documento }} ({{ $u->empresa->nombre ?? '' }})</option>
                @endforeach
            </select>
            @error('id_usuario')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div id="campo-visitante" class="hidden space-y-4">
            <div>
                <label for="id_visitante" class="block text-sm font-medium text-slate-700">Visitante</label>
                <select name="id_visitante" id="id_visitante" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    <option value="">Seleccione visitante</option>
                    @foreach($visitantes as $v)
                        <option value="{{ $v->id_visitante }}" {{ old('id_visitante') == $v->id_visitante ? 'selected' : '' }}>{{ $v->nombre }} - {{ $v->documento }}</option>
                    @endforeach
                </select>
                @error('id_visitante')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="id_area_visita" class="block text-sm font-medium text-slate-700">Área de visita</label>
                <select name="id_area_visita" id="id_area_visita" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                    <option value="">Seleccione área</option>
                    @foreach($areasVisita as $a)
                        <option value="{{ $a->id_area_visita }}" {{ old('id_area_visita') == $a->id_area_visita ? 'selected' : '' }}>{{ $a->nombre }}</option>
                    @endforeach
                </select>
                @error('id_area_visita')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label for="id_empresa" class="block text-sm font-medium text-slate-700">Empresa</label>
            <select name="id_empresa" id="id_empresa" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @foreach($empresas as $e)
                    <option value="{{ $e->id_empresa }}" {{ old('id_empresa') == $e->id_empresa ? 'selected' : '' }}>{{ $e->nombre }}</option>
                @endforeach
            </select>
            @error('id_empresa')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="id_casino" class="block text-sm font-medium text-slate-700">Casino</label>
            <select name="id_casino" id="id_casino" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @foreach($casinos as $c)
                    <option value="{{ $c->id_casino }}" data-tipo="{{ strtolower(trim($c->tipo_casino ?? '')) }}" {{ old('id_casino') == $c->id_casino ? 'selected' : '' }}>{{ $c->nombre }}{{ strtolower(trim($c->tipo_casino ?? '')) === 'domicilio' ? ' (a domicilio)' : '' }}</option>
                @endforeach
            </select>
            @error('id_casino')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="id_horario" class="block text-sm font-medium text-slate-700">Horario de consumo</label>
            <select name="id_horario" id="id_horario" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @foreach($horarios as $h)
                    <option value="{{ $h->id_horario }}" {{ old('id_horario') == $h->id_horario ? 'selected' : '' }}>{{ $h->nombre }}</option>
                @endforeach
            </select>
            @error('id_horario')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="fecha_consumo" class="block text-sm font-medium text-slate-700">Fecha consumo</label>
                <input type="date" name="fecha_consumo" id="fecha_consumo" value="{{ old('fecha_consumo', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('fecha_consumo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="hora_consumo" class="block text-sm font-medium text-slate-700">Hora consumo</label>
                <input type="time" name="hora_consumo" id="hora_consumo" value="{{ old('hora_consumo', date('H:i')) }}" required class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                @error('hora_consumo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="precio_empleado" class="block text-sm font-medium text-slate-700">Precio empleado</label>
                <input type="number" name="precio_empleado" id="precio_empleado" value="{{ old('precio_empleado', 0) }}" step="0.01" min="0" required readonly class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm bg-slate-50 cursor-not-allowed">
                <p class="mt-1 text-xs text-slate-500">Según precio asignado al casino y horario seleccionado.</p>
                @error('precio_empleado')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="precio_casino" class="block text-sm font-medium text-slate-700">Precio casino</label>
                <input type="number" name="precio_casino" id="precio_casino" value="{{ old('precio_casino', 0) }}" step="0.01" min="0" required readonly class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm bg-slate-50 cursor-not-allowed">
                <p class="mt-1 text-xs text-slate-500">Según precio asignado al casino y horario seleccionado.</p>
                @error('precio_casino')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
        <div id="campo-direccion" class="hidden">
            <label for="direccion_entrega" class="block text-sm font-medium text-slate-700">Direccion de entrega (solo si casino es a domicilio)</label>
            <input type="text" name="direccion_entrega" id="direccion_entrega" value="{{ old('direccion_entrega') }}" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500" placeholder="Calle, numero, piso...">
            @error('direccion_entrega')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Registrar consumo</button>
            <a href="{{ route('admin.consumos-manuales.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Cancelar</a>
        </div>
    </form>
</div>
@push('scripts')
<script>
var preciosPorCasinoHorario = @json($precios);
function actualizarPrecios() {
    var idCasino = document.getElementById('id_casino').value;
    var idHorario = document.getElementById('id_horario').value;
    var key = idCasino + '-' + idHorario;
    var datos = preciosPorCasinoHorario[key];
    var precioEmp = document.getElementById('precio_empleado');
    var precioCas = document.getElementById('precio_casino');
    if (datos) {
        precioEmp.value = datos.precio_empleado;
        precioCas.value = datos.precio_casino;
    } else {
        precioEmp.value = '0';
        precioCas.value = '0';
    }
}
document.querySelectorAll('input[name="tipo_consumidor"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var esUsuario = this.value === 'usuario';
        document.getElementById('campo-usuario').classList.toggle('hidden', !esUsuario);
        document.getElementById('campo-visitante').classList.toggle('hidden', esUsuario);
        document.getElementById('id_usuario').required = esUsuario;
        var campoVisitante = document.getElementById('id_visitante');
        var campoArea = document.getElementById('id_area_visita');
        if (campoVisitante) campoVisitante.required = !esUsuario;
        if (campoArea) campoArea.required = !esUsuario;
    });
});
var tipo = document.querySelector('input[name="tipo_consumidor"]:checked');
if (tipo) tipo.dispatchEvent(new Event('change'));
document.getElementById('id_casino').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    var esDomicilio = opt && (opt.getAttribute('data-tipo') || '') === 'domicilio';
    document.getElementById('campo-direccion').classList.toggle('hidden', !esDomicilio);
    actualizarPrecios();
});
document.getElementById('id_horario').addEventListener('change', actualizarPrecios);
document.getElementById('id_casino').dispatchEvent(new Event('change'));
actualizarPrecios();
</script>
@endpush
@endsection
