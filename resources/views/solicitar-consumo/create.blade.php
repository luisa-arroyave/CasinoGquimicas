@extends('layouts.app')

@section('title', 'Solicitar consumo')
@section('page-title', 'Solicitar consumo')

@section('content')
<div class="max-w-xl space-y-6">
    <p class="text-slate-600">Seleccione el casino o punto de entrega. Si elige uno con entrega a domicilio, indique la dirección.</p>

    <form action="{{ route('solicitar-consumo.store') }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 space-y-4">
        @csrf
        <div>
            <label for="id_casino" class="block text-sm font-medium text-slate-700">Casino / Punto de entrega</label>
            <select name="id_casino" id="id_casino" required
                    class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500">
                <option value="">Seleccione</option>
                @foreach($casinos as $c)
                    <option value="{{ $c->id_casino }}"
                            data-tipo="{{ strtolower(trim($c->tipo_casino ?? '')) }}"
                            {{ old('id_casino') == $c->id_casino ? 'selected' : '' }}>
                        {{ $c->nombre }}{{ strtolower(trim($c->tipo_casino ?? '')) === 'domicilio' ? ' (a domicilio)' : '' }}
                    </option>
                @endforeach
            </select>
            @error('id_casino')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div id="campo-direccion" class="hidden">
            <label for="direccion_entrega" class="block text-sm font-medium text-slate-700">Direccion de entrega</label>
            <textarea name="direccion_entrega" id="direccion_entrega" rows="3"
                      class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-slate-500 focus:ring-slate-500"
                      placeholder="Calle, numero, piso, sector...">{{ old('direccion_entrega') }}</textarea>
            @error('direccion_entrega')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700">Solicitar consumo</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function() {
    var select = document.getElementById('id_casino');
    var campoDir = document.getElementById('campo-direccion');
    var inputDir = document.getElementById('direccion_entrega');

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
@endsection
