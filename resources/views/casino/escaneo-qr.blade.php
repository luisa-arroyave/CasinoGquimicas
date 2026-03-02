@extends('layouts.app')

@section('title', 'Escaneo QR - ' . config('app.name'))
@section('page-title', 'Escaneo QR - Entrega')

@section('content')
<div class="space-y-4 sm:space-y-6 max-w-2xl w-full min-w-0">
    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <label for="id_casino" class="block text-sm font-medium text-slate-700 mb-2">Punto de entrega (Casino)</label>
        <select id="id_casino" class="w-full rounded-lg border border-slate-300 px-4 py-3 sm:py-2.5 text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 min-h-[48px]">
            @forelse($casinos as $c)
                <option value="{{ $c->id_casino }}">{{ $c->nombre }}</option>
            @empty
                <option value="">No hay casinos activos</option>
            @endforelse
        </select>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <div class="flex items-center justify-between gap-4 mb-2">
            <h2 class="text-base sm:text-lg font-semibold text-slate-800">Contador del día</h2>
            <span id="contador-entregados" class="text-2xl sm:text-3xl font-bold text-emerald-600 shrink-0">0</span>
        </div>
        <p class="text-sm text-slate-500">Entregas registradas en este punto</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm relative">
        <h2 class="text-base sm:text-lg font-semibold text-slate-800 mb-3">Escanear código QR</h2>

        {{-- Campo oculto para lectores de código de barras (se comportan como teclado y envían Enter) --}}
        <input type="text" id="input-qr"
               class="absolute -left-[9999px] top-0 opacity-0"
               autocomplete="off">

        <button type="button" id="btn-toggle-camara" class="w-full sm:w-auto min-h-[48px] px-6 py-3 rounded-lg bg-slate-800 text-white font-medium hover:bg-slate-700 active:bg-slate-600 focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors touch-manipulation">
            Usar cámara para escanear
        </button>
        <div id="zona-camara" class="mt-4 hidden">
            <div id="lector-qr" class="rounded-lg overflow-hidden border border-slate-200 bg-slate-100 w-full max-w-sm mx-auto min-h-[280px] aspect-square max-h-[70vh]"></div>
            <p class="text-center text-sm text-slate-500 mt-2">Apunta la cámara al código QR. Mantén el código centrado y a una distancia estable.</p>
            <p class="text-center text-xs text-slate-400 mt-1">Si no detecta, puedes subir una foto del QR.</p>
            <label class="mt-2 flex justify-center">
                <span class="px-4 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium cursor-pointer hover:bg-slate-200">
                    Subir foto del QR
                </span>
                <input type="file" id="input-foto-qr" accept="image/*" capture="environment" class="hidden">
            </label>
        </div>
    </div>

    <div id="resultado" class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm hidden">
        <p id="resultado-mensaje" class="font-medium"></p>
        <p id="resultado-detalle" class="text-sm text-slate-600 mt-1"></p>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    const inputQr = document.getElementById('input-qr');
    const idCasinoSelect = document.getElementById('id_casino');
    const contadorEl = document.getElementById('contador-entregados');
    const resultadoEl = document.getElementById('resultado');
    const resultadoMensaje = document.getElementById('resultado-mensaje');
    const resultadoDetalle = document.getElementById('resultado-detalle');
    const zonaCamara = document.getElementById('zona-camara');
    const lectorQr = document.getElementById('lector-qr');
    const btnToggleCamara = document.getElementById('btn-toggle-camara');

    let scanner = null;
    let entregadosHoy = 0;
    let ultimoCodigoLeido = '';

    function playSuccessSound() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = 880;
            osc.type = 'sine';
            gain.gain.setValueAtTime(0.3, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.15);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.15);
        } catch (e) {}
    }

    function showResult(ok, mensaje, detalle, nuevoContador) {
        resultadoEl.classList.remove('hidden');
        resultadoEl.classList.remove('bg-red-50', 'border-red-200');
        resultadoEl.classList.remove('bg-green-50', 'border-green-200');
        if (ok) {
            resultadoEl.classList.add('bg-green-50', 'border-green-200');
            resultadoMensaje.classList.add('text-green-800');
            resultadoMensaje.classList.remove('text-red-800');
            playSuccessSound();
            if (typeof nuevoContador === 'number') {
                entregadosHoy = nuevoContador;
                contadorEl.textContent = nuevoContador;
            }
        } else {
            resultadoEl.classList.add('bg-red-50', 'border-red-200');
            resultadoMensaje.classList.add('text-red-800');
            resultadoMensaje.classList.remove('text-green-800');
        }
        resultadoMensaje.textContent = mensaje;
        resultadoDetalle.textContent = detalle || '';
        resultadoEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function validarQr(codigoQr) {
        const idCasino = idCasinoSelect.value;
        if (!idCasino) {
            showResult(false, 'Selecciona un punto de entrega (casino).', '');
            return;
        }
        if (!codigoQr || !codigoQr.trim()) {
            showResult(false, 'Escanea un código QR.', '');
            return;
        }

        fetch('{{ url("/api/consumo/validar-qr") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ codigo_qr: codigoQr.trim(), id_casino: parseInt(idCasino, 10) })
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                showResult(true, 'Consumo registrado correctamente.', data.consumo ? (data.consumo.nombres + ' · ' + data.consumo.horario) : '', data.entregados_hoy);
                ultimoCodigoLeido = '';
                try { if (scanner && typeof scanner.resume === 'function') scanner.resume(); } catch (e) {}
            } else {
                showResult(false, data.mensaje || 'Error al validar.', '');
            }
        })
        .catch(() => showResult(false, 'Error de conexión. Intenta de nuevo.', ''));
    }

    // Soporte para lector de código QR conectado como teclado:
    // escribe en input oculto y envía Enter al final.
    if (inputQr) {
        // Mantener el foco en el input para que reciba lo que escriba el lector
        const focusInput = () => inputQr.focus();
        focusInput();
        inputQr.addEventListener('blur', () => {
            // Pequeño delay para no pelear con otros focos legítimos
            setTimeout(focusInput, 50);
        });

        inputQr.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const valor = inputQr.value;
                inputQr.value = '';
                ultimoCodigoLeido = valor;
                validarQr(valor);
            }
        });
    }

    btnToggleCamara.addEventListener('click', function() {
        if (zonaCamara.classList.contains('hidden')) {
            zonaCamara.classList.remove('hidden');
            if (!window.Html5Qrcode) return;
            scanner = new Html5Qrcode('lector-qr');
            var config = { fps: 10 };
            scanner.start({ facingMode: 'environment' }, config, function(decodedText) {
                ultimoCodigoLeido = decodedText;
                // Pausar de inmediato para evitar lecturas repetidas
                if (scanner && scanner.isScanning && scanner.isScanning()) {
                    scanner.pause();
                }
                // Validar automáticamente sin pedir "Enviar"
                validarQr(decodedText);
            }).catch(function(err) {
                showResult(false, 'No se pudo iniciar la cámara.', err && err.message ? String(err.message) : '');
            });
        } else {
            zonaCamara.classList.add('hidden');
            if (scanner) { scanner.stop(); scanner = null; }
        }
    });

    function actualizarContadorInicial() {
        const idCasino = idCasinoSelect.value;
        if (!idCasino) return;
        fetch('{{ url("/api/consumo/entregados-hoy") }}?id_casino=' + idCasino, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => { entregadosHoy = data.entregados_hoy; contadorEl.textContent = data.entregados_hoy; })
        .catch(() => { contadorEl.textContent = '0'; });
    }
    actualizarContadorInicial();
    idCasinoSelect.addEventListener('change', actualizarContadorInicial);

    // Fallback: subir foto del QR si la cámara en vivo no detecta
    var inputFotoQr = document.getElementById('input-foto-qr');
    if (inputFotoQr) {
        inputFotoQr.addEventListener('change', function(e) {
            var file = e.target.files && e.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            if (!window.Html5Qrcode) return;
            inputFotoQr.value = '';
            var configCam = { fps: 10 };
            var onScan = function(t) { if (scanner && scanner.isScanning && scanner.isScanning()) scanner.pause(); validarQr(t); };
            var scanFileNow = function(sc) {
                sc.scanFile(file, false).then(function(decodedText) {
                    validarQr(decodedText);
                    if (sc === scanner) scanner.start({ facingMode: 'environment' }, configCam, onScan).catch(function() {});
                }).catch(function() {
                    showResult(false, 'No se detectó ningún código QR en la imagen.', 'Intenta con otra foto o escanea en vivo.');
                    if (sc === scanner) scanner.start({ facingMode: 'environment' }, configCam, onScan).catch(function() {});
                });
            };
            if (scanner && scanner.getState && scanner.getState() === 2) {
                scanner.stop().then(function() { scanFileNow(scanner); }).catch(function() { scanFileNow(scanner); });
            } else {
                var sc = new Html5Qrcode('lector-qr');
                scanFileNow(sc);
            }
        });
    }
})();
</script>
@endpush
@endsection
