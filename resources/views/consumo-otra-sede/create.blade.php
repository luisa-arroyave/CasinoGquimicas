@extends('layouts.app')

@section('title', 'Consumo en otra sede')
@section('page-title', 'Consumo en otra sede')

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
    @elseif($sedes->isEmpty())
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 sm:p-6 text-amber-900">
            <p class="font-medium">No tiene sedes asignadas para consumo en otra sede.</p>
            <p class="mt-2 text-sm">Contacte al administrador para que le asignen acceso a otras sedes.</p>
        </div>
    @else
        @if($codigoQrActivo ?? null)
            <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50/50 p-4 sm:p-6 shadow-sm">
                <p class="text-emerald-800 font-medium mb-1">Código activo para {{ $consumoActivo->horarioConsumo->nombre ?? 'este horario' }}</p>
                <p class="text-slate-600 text-sm mb-4">Presente este código QR en el casino seleccionado para que lo validen.</p>
                <div class="flex flex-col items-center gap-3">
                    <div class="rounded-xl border-2 border-slate-200 bg-white p-4 inline-block">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&amp;data={{ urlencode($codigoQrActivo) }}" alt="Código QR" width="220" height="220" class="block">
                    </div>
                    <p class="text-sm text-slate-600">{{ $consumoActivo->casino->nombre ?? '' }}</p>
                </div>
                <p class="mt-4 text-sm text-slate-600">Ya tiene un consumo solicitado para este horario.</p>
            </div>
        @else
        <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
            <p class="text-slate-600 text-sm sm:text-base mb-6">Seleccione la sede y el casino donde desea retirar su consumo. El tipo de comida se determina según el horario actual.</p>

            <form action="{{ route('consumo-otra-sede.store') }}" method="POST" class="space-y-5" id="form-consumo-otra-sede">
                @csrf

                <div class="rounded-lg bg-slate-50 p-3 sm:p-4 space-y-4">
                    <div class="opacity-90">
                        <label class="block text-xs font-medium text-slate-500 uppercase tracking-wide">Tipo de comida</label>
                        <p class="mt-0.5 text-base font-medium text-slate-800">{{ $horarioVigente->nombre }}</p>
                        <p class="text-xs text-slate-500">Según la hora actual (deshabilitado)</p>
                    </div>

                    <div>
                        <label for="id_sede" class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Seleccione sede</label>
                        <select name="id_sede" id="id_sede" required
                                class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 min-h-[48px] px-4 py-2.5 text-slate-900">
                            <option value="">Seleccione sede...</option>
                            @foreach($sedes as $sede)
                                <option value="{{ $sede->id_sede }}" {{ (old('id_sede') ?? $oldSede ?? null) == $sede->id_sede ? 'selected' : '' }}>{{ $sede->nombre }}</option>
                            @endforeach
                        </select>
                        @error('id_sede')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="id_casino" class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Seleccione casino</label>
                        <select name="id_casino" id="id_casino" required disabled
                                class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 min-h-[48px] px-4 py-2.5 text-slate-900 bg-slate-50">
                            <option value="">Primero seleccione una sede</option>
                        </select>
                        @error('id_casino')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="btn-registrar" class="w-full sm:w-auto min-h-[48px] px-6 py-3 rounded-xl bg-slate-800 text-white font-medium hover:bg-slate-700 active:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                        Registrar consumo
                    </button>
                </div>
            </form>
        </div>
        @endif
    @endif
</div>

@if($horarioVigente && $sedes->isNotEmpty() && !($codigoQrActivo ?? null))
@push('scripts')
<script>
(function() {
    var casinosPorSede = @json($casinosPorSede ?? []);
    var oldCasino = @json(old('id_casino'));
    var selectSede = document.getElementById('id_sede');
    var selectCasino = document.getElementById('id_casino');
    var btnRegistrar = document.getElementById('btn-registrar');

    function actualizarCasinos() {
        var idSede = selectSede ? selectSede.value : '';
        var casinos = idSede ? (casinosPorSede[idSede] || []) : [];

        if (selectCasino) {
            selectCasino.innerHTML = '<option value="">Seleccione casino...</option>';
            selectCasino.disabled = !idSede || casinos.length === 0;
            selectCasino.classList.toggle('bg-slate-50', selectCasino.disabled);
            selectCasino.classList.toggle('bg-white', !selectCasino.disabled);

            casinos.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.id_casino;
                opt.textContent = c.nombre;
                opt.setAttribute('data-tipo', c.tipo || '');
                if (oldCasino && String(c.id_casino) === String(oldCasino)) opt.selected = true;
                selectCasino.appendChild(opt);
            });
        }

        actualizarBoton();
    }

    function actualizarBoton() {
        if (btnRegistrar) {
            var casinoVal = selectCasino ? selectCasino.value : '';
            btnRegistrar.disabled = !casinoVal;
        }
    }

    if (selectSede) selectSede.addEventListener('change', actualizarCasinos);
    if (selectCasino) selectCasino.addEventListener('change', actualizarBoton);

    actualizarCasinos();
})();
</script>
@endpush
@endif
@endsection
