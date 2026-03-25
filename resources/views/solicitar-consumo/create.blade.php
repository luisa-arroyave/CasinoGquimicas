@extends('layouts.app')

@section('title', 'Solicitar consumo')
@section('page-title', 'Solicitar consumo')

@section('content')
<div class="max-w-xl space-y-6">
    @if(!$horarioVigente)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 sm:p-6 text-amber-900">
            <p class="font-medium">No hay horario de consumo vigente en este momento.</p>
            <p class="mt-2 text-sm">Puede solicitar consumo en los siguientes horarios:</p>
            <ul class="mt-2 list-disc list-inside text-sm space-y-1">
                @foreach($horariosDisponibles as $h)
                    <li>{{ $h->nombre }} ({{ \Carbon\Carbon::parse($h->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($h->hora_fin)->format('H:i') }})</li>
                @endforeach
            </ul>
        </div>
    @elseif($casinos->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 sm:p-6 text-amber-900">
            <p class="font-medium">No hay casinos disponibles para su empresa.</p>
            <p class="mt-2 text-sm">Contacte al administrador para configurar casinos asignados a su empresa.</p>
        </div>
    @else
        {{-- Código QR activo: cuando ya tiene un consumo SOLICITADO para el horario vigente (según horarios de las comidas) --}}
        @if($codigoQrActivo ?? null)
            <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50/50 p-4 sm:p-6 shadow-sm" id="bloque-qr-activo">
                <p class="text-emerald-800 font-medium mb-1">Código activo para {{ $consumoActivo->horarioConsumo->nombre ?? 'este horario' }}</p>
                <p class="text-slate-600 text-sm mb-4">Presente este código QR en el casino seleccionado para que lo validen.</p>
                <div class="flex flex-col items-center gap-3">
                    <div class="rounded-xl border-2 border-slate-200 bg-white p-4 inline-block">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&amp;data={{ urlencode($codigoQrActivo) }}" alt="Código QR" width="220" height="220" class="block">
                    </div>
                    <p class="text-sm text-slate-600">{{ $consumoActivo->casino->nombre ?? '' }}</p>
                </div>
                <p class="mt-4 text-sm text-slate-600">Ya tiene un consumo solicitado para este horario. Puede solicitar otro en el próximo horario de comida.</p>
            </div>
        @endif

        @if(!($codigoQrActivo ?? null))
        <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
            <p class="text-slate-600 text-sm sm:text-base mb-6">Solicite su consumo para el horario actual. Seleccione el casino y, si aplica, la dirección.</p>

            <form action="{{ route('solicitar-consumo.store') }}" method="POST" class="space-y-5" id="form-solicitar">
                @csrf

                {{-- Valores automáticos (solo informativos o ocultos). Precios y estado se asignan en el backend. --}}
                <div class="rounded-lg bg-slate-50 p-3 sm:p-4 space-y-3">
                    <div>
                        @if(!empty($mostrarSelectorTipoComidaIbc) && $horariosTipoComidaIbc->isNotEmpty())
                            <label for="id_horario" class="block text-xs font-medium text-slate-500 uppercase tracking-wide">Tipo de comida</label>
                            <select name="id_horario" id="id_horario" required
                                    class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 min-h-[48px] px-4 py-2.5 text-slate-900">
                                <option value="">Seleccione refrigerio o cena…</option>
                                @foreach($horariosTipoComidaIbc as $hTipo)
                                    <option value="{{ $hTipo->id_horario }}" {{ (string) old('id_horario') === (string) $hTipo->id_horario ? 'selected' : '' }}>{{ $hTipo->nombre }}</option>
                                @endforeach
                            </select>
                            @error('id_horario')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            <p class="text-xs text-slate-500 mt-1">En este horario debe indicar si corresponde refrigerio o cena.</p>
                        @else
                            <span class="text-xs font-medium text-slate-500 uppercase tracking-wide">Tipo de comida</span>
                            <p class="mt-0.5 text-base font-medium text-slate-800">{{ $horarioVigente->nombre }}</p>
                            <p class="text-xs text-slate-500">Según la hora actual</p>
                        @endif
                    </div>
                    <div>
                        <label for="id_casino" class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Seleccione casino</label>
                        <select name="id_casino" id="id_casino" required
                                class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 min-h-[48px] px-4 py-2.5 text-slate-900">
                            <option value="">Seleccione casino...</option>
                            @foreach($casinos as $c)
                                <option value="{{ $c->id_casino }}"
                                        data-tipo="{{ strtolower(trim($c->tipo_casino ?? '')) }}"
                                        {{ old('id_casino', $casinoPorDefecto?->id_casino ?? $casinos->first()?->id_casino) == $c->id_casino ? 'selected' : '' }}>
                                    {{ $c->nombre }}{{ strtolower(trim($c->tipo_casino ?? '')) === 'domicilio' ? ' (a domicilio)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_casino')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div id="campo-direccion" class="{{ ($casinoPorDefecto ?? $casinos->first()) && strtolower(trim(($casinoPorDefecto ?? $casinos->first())->tipo_casino ?? '')) === 'domicilio' ? '' : 'hidden' }}">
                    <label for="direccion_entrega" class="block text-sm font-medium text-slate-700 mb-1">Dirección de entrega <span class="text-red-500">*</span></label>
                    <textarea name="direccion_entrega" id="direccion_entrega" rows="3"
                              class="block w-full rounded-lg border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 min-h-[48px] px-4 py-2.5 text-slate-900 resize-y"
                              placeholder="Calle, número, piso, sector...">{{ old('direccion_entrega') }}</textarea>
                    @error('direccion_entrega')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto min-h-[48px] px-6 py-3 rounded-xl bg-slate-800 text-white font-medium hover:bg-slate-700 active:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                        Registrar consumo
                    </button>
                </div>
            </form>
        </div>
        @endif
    @endif
</div>

@if($horarioVigente)
@push('scripts')
<script>
(function() {
    var select = document.getElementById('id_casino');
    var campoDir = document.getElementById('campo-direccion');
    var inputDir = document.getElementById('direccion_entrega');

    if (!select) {
        if (campoDir && inputDir) {
            var esDomicilio = {{ ($casinoPorDefecto && strtolower(trim($casinoPorDefecto->tipo_casino ?? '')) === 'domicilio') ? 'true' : 'false' }};
            campoDir.classList.toggle('hidden', !esDomicilio);
            inputDir.required = esDomicilio;
        }
        return;
    }

    function toggleDireccion() {
        var opt = select.options[select.selectedIndex];
        var tipo = opt ? (opt.getAttribute('data-tipo') || '') : '';
        var esDomicilio = tipo === 'domicilio';
        campoDir.classList.toggle('hidden', !esDomicilio);
        inputDir.required = esDomicilio;
    }

    select.addEventListener('change', toggleDireccion);
    toggleDireccion();
})();
</script>
@endpush
@endif
@endsection
