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

        <label class="block cursor-pointer">
            <span class="flex items-center justify-center gap-2 min-h-[48px] px-6 py-3 rounded-lg bg-emerald-600 text-white font-medium hover:bg-emerald-700 active:bg-emerald-600 focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors touch-manipulation">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Subir foto del QR
            </span>
            <input type="file" id="input-foto-qr" accept="image/*" capture="environment" class="hidden">
        </label>
        <p class="text-xs text-slate-500 mt-2">Tome una foto del QR o seleccione una imagen.</p>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
        <h2 class="text-base sm:text-lg font-semibold text-slate-800 mb-3">Registrar por cédula</h2>
        <p class="text-sm text-slate-600 mb-3">Ingrese el número de cédula del empleado para registrar la entrega.</p>
        <div class="flex gap-2">
            <input type="text" id="input-cedula" placeholder="Número de cédula"
                   class="flex-1 rounded-lg border border-slate-300 px-4 py-3 sm:py-2.5 text-slate-900 focus:border-slate-500 focus:ring-2 focus:ring-slate-500/20 min-h-[48px]"
                   autocomplete="off">
            <button type="button" id="btn-registrar-cedula"
                    class="shrink-0 flex items-center justify-center gap-2 min-h-[48px] px-6 py-3 rounded-lg bg-slate-600 text-white font-medium hover:bg-slate-700 active:bg-slate-600 focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Registrar
            </button>
        </div>
    </div>

    <div id="lector-qr-file" aria-hidden="true" style="position:fixed;left:-9999px;top:0;width:260px;height:260px"></div>
    <div id="resultado" class="rounded-xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm hidden">
        <p id="resultado-mensaje" class="font-medium"></p>
        <p id="resultado-detalle" class="text-sm text-slate-600 mt-1"></p>
        <button type="button" id="btn-cancelar-validacion" class="mt-3 hidden text-sm font-medium text-slate-600 underline hover:text-slate-800">
            Cancelar e intentar de nuevo
        </button>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    {{-- URL: mismo origen que la página (https, IP o localhost) --}}
    var basePath = (function() {
        var fromConfig = '{{ parse_url(config("app.url"), PHP_URL_PATH) ?? "" }}'.replace(/\/$/, '');
        var fromPage = (window.location.pathname || '').replace(/\/casino\/escaneo-qr.*$/i, '');
        return fromConfig || fromPage || '';
    })();
    var urlValidarQr = window.location.origin + (basePath ? basePath.replace(/\/$/, '') : '') + '/api/consumo/validar-qr';
    var urlRegistrarCedula = window.location.origin + (basePath ? basePath.replace(/\/$/, '') : '') + '/api/consumo/registrar-por-cedula';
    const inputQr = document.getElementById('input-qr');
    const idCasinoSelect = document.getElementById('id_casino');
    const contadorEl = document.getElementById('contador-entregados');
    const resultadoEl = document.getElementById('resultado');
    const resultadoMensaje = document.getElementById('resultado-mensaje');
    const resultadoDetalle = document.getElementById('resultado-detalle');
    const btnCancelar = document.getElementById('btn-cancelar-validacion');

    let xhrActual = null;
    let entregadosHoy = 0;
    let ultimoCodigoLeido = '';
    let validandoEnCurso = false;

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

    function showDetectado() {
        resultadoEl.classList.remove('hidden');
        resultadoEl.classList.remove('bg-red-50', 'border-red-200', 'bg-green-50', 'border-green-200');
        resultadoEl.classList.add('bg-sky-50', 'border-sky-200');
        resultadoMensaje.classList.remove('text-red-800', 'text-green-800');
        resultadoMensaje.classList.add('text-sky-800');
        resultadoMensaje.textContent = 'Código detectado. Validando...';
        resultadoDetalle.textContent = '';
        if (btnCancelar) { btnCancelar.classList.remove('hidden'); }
        resultadoEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        playSuccessSound();
    }

    function showResult(ok, mensaje, detalle, nuevoContador) {
        resultadoEl.classList.remove('hidden');
        resultadoEl.classList.remove('bg-red-50', 'border-red-200', 'bg-green-50', 'border-green-200', 'bg-sky-50', 'border-sky-200');
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
        if (btnCancelar) { btnCancelar.classList.add('hidden'); }
        resultadoEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function cancelarValidacion() {
        validandoEnCurso = false;
        if (xhrActual) { try { xhrActual.abort(); } catch(e) {} xhrActual = null; }
        showResult(false, 'Cancelado. Puede escanear de nuevo.', '');
        resumeScanner();
    }

    function validarQr(codigoQr) {
        try {
        var idCasino = idCasinoSelect ? idCasinoSelect.value : '';
        if (!idCasino) {
            showResult(false, 'Selecciona un punto de entrega (casino).', '');
            return;
        }
        if (!codigoQr || !String(codigoQr).trim()) {
            showResult(false, 'Escanea un código QR.', '');
            return;
        }
        if (validandoEnCurso) return;
        validandoEnCurso = true;
        if (xhrActual) { try { xhrActual.abort(); } catch(e) {} }

        var xhr = new XMLHttpRequest();
        xhrActual = xhr;
        xhr.open('POST', urlValidarQr, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken || '');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.timeout = 8000;
        xhr.withCredentials = true;

        xhr.onload = function() {
            xhrActual = null;
            validandoEnCurso = false;
            var data = {};
            try { data = JSON.parse(xhr.responseText || '{}'); } catch(e) {}
            if (xhr.status >= 200 && xhr.status < 300) {
                if (data && data.ok) {
                    showResult(true, 'Consumo registrado correctamente.', data.consumo ? (data.consumo.nombres + ' · ' + data.consumo.horario) : '', data.entregados_hoy);
                    ultimoCodigoLeido = '';
                } else {
                    showResult(false, (data && data.mensaje) || 'Error al validar.', '');
                    resumeScanner();
                }
            } else {
                showResult(false, (data && data.mensaje) || (data && data.message) || ('Error ' + xhr.status), '');
                resumeScanner();
            }
        };
        xhr.onerror = function() {
            xhrActual = null;
            validandoEnCurso = false;
            showResult(false, 'Error de conexión. Revisa la red e intenta de nuevo.', '');
            resumeScanner();
        };
        xhr.ontimeout = function() {
            xhrActual = null;
            validandoEnCurso = false;
            showResult(false, 'Tiempo de espera agotado. Revisa la conexión.', '');
            resumeScanner();
        };
        xhr.send(JSON.stringify({ codigo_qr: String(codigoQr).trim(), id_casino: parseInt(idCasino, 10) }));

        // Respaldo a 5 segundos: si seguimos en Validando, forzar
        setTimeout(function() {
            if (validandoEnCurso) {
                validandoEnCurso = false;
                if (xhrActual) { try { xhrActual.abort(); } catch(e) {} xhrActual = null; }
                if (resultadoMensaje && resultadoMensaje.textContent.indexOf('Validando') >= 0) {
                    showResult(false, 'No se pudo completar. Revisa tu conexión e intenta de nuevo.', '');
                    resumeScanner();
                }
            }
        }, 5000);
        } catch (e) {
            validandoEnCurso = false;
            xhrActual = null;
            showResult(false, 'Error. Intenta de nuevo.', e && e.message ? String(e.message) : '');
            resumeScanner();
        }
    }

    function resumeScanner() {}

    if (btnCancelar) btnCancelar.addEventListener('click', cancelarValidacion);

    // Soporte para lector de código QR conectado como teclado:
    // escribe en input oculto y envía Enter al final.
    if (inputQr) {
        const inputCedulaEl = document.getElementById('input-cedula');
        const focusInput = () => inputQr.focus();
        focusInput();
        inputQr.addEventListener('blur', () => {
            setTimeout(function() {
                var active = document.activeElement;
                if (active && (active === inputCedulaEl || active === idCasinoSelect || (active.id && active.id.indexOf('cedula') >= 0))) {
                    return;
                }
                focusInput();
            }, 50);
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

    function actualizarContadorInicial() {
        const idCasino = idCasinoSelect.value;
        if (!idCasino) return;
        var urlEntregados = window.location.origin + (basePath ? basePath.replace(/\/$/, '') : '') + '/api/consumo/entregados-hoy';
        fetch(urlEntregados + '?id_casino=' + idCasino, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => { entregadosHoy = data.entregados_hoy; contadorEl.textContent = data.entregados_hoy; })
        .catch(() => { contadorEl.textContent = '0'; });
    }
    actualizarContadorInicial();
    idCasinoSelect.addEventListener('change', actualizarContadorInicial);

    function redimensionarImagenParaMovil(file) {
        return new Promise(function(resolve, reject) {
            var maxSize = 1200;
            var img = new Image();
            var url = URL.createObjectURL(file);
            img.onload = function() {
                URL.revokeObjectURL(url);
                var w = img.naturalWidth || img.width;
                var h = img.naturalHeight || img.height;
                if (w <= maxSize && h <= maxSize) {
                    resolve(file);
                    return;
                }
                var scale = Math.min(maxSize / w, maxSize / h);
                var nw = Math.round(w * scale);
                var nh = Math.round(h * scale);
                var canvas = document.createElement('canvas');
                canvas.width = nw;
                canvas.height = nh;
                var ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, nw, nh);
                canvas.toBlob(function(blob) {
                    if (blob) resolve(new File([blob], 'qr.jpg', { type: 'image/jpeg' }));
                    else resolve(file);
                }, 'image/jpeg', 0.92);
            };
            img.onerror = function() {
                URL.revokeObjectURL(url);
                resolve(file);
            };
            img.src = url;
        });
    }

    var inputFotoQr = document.getElementById('input-foto-qr');
    if (inputFotoQr) {
        inputFotoQr.addEventListener('change', function(e) {
            var file = e.target.files && e.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            if (!window.Html5Qrcode) { showResult(false, 'Error: librería QR no cargada.', ''); return; }
            inputFotoQr.value = '';
            resultadoEl.classList.remove('hidden');
            resultadoEl.classList.remove('bg-red-50','bg-green-50');
            resultadoEl.classList.add('bg-sky-50','border-sky-200');
            resultadoMensaje.textContent = 'Analizando imagen...';
            resultadoDetalle.textContent = '';
            if (btnCancelar) btnCancelar.classList.add('hidden');
            redimensionarImagenParaMovil(file).then(function(archivo) {
                var sc = new Html5Qrcode('lector-qr-file');
                return sc.scanFile(archivo, false).then(function(decodedText) {
                    if (!decodedText || !String(decodedText).trim()) {
                        showResult(false, 'No se detectó código QR en la imagen.', 'Asegúrese de que el QR sea claro y esté completo.');
                        return;
                    }
                    showDetectado();
                    validarQr(decodedText);
                });
            }).catch(function(err) {
                showResult(false, 'No se detectó código QR en la imagen.', 'Tome una foto más nítida. Evite formatos HEIC (iPhone: use formato más compatible en Ajustes).');
            });
        });
    }

    var inputCedula = document.getElementById('input-cedula');
    var btnRegistrarCedula = document.getElementById('btn-registrar-cedula');
    if (inputCedula && btnRegistrarCedula) {
        function registrarPorCedula() {
            var idCasino = idCasinoSelect ? idCasinoSelect.value : '';
            var documento = (inputCedula.value || '').trim().replace(/\s/g, '');
            if (!idCasino) {
                showResult(false, 'Selecciona un punto de entrega (casino).', '');
                return;
            }
            if (!documento) {
                showResult(false, 'Ingresa el número de cédula.', '');
                inputCedula.focus();
                return;
            }
            resultadoEl.classList.remove('hidden');
            resultadoEl.classList.remove('bg-red-50', 'border-red-200', 'bg-green-50', 'border-green-200');
            resultadoEl.classList.add('bg-sky-50', 'border-sky-200');
            resultadoMensaje.textContent = 'Registrando...';
            resultadoDetalle.textContent = '';
            if (btnCancelar) btnCancelar.classList.add('hidden');
            btnRegistrarCedula.disabled = true;

            fetch(urlRegistrarCedula, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ documento: documento, id_casino: parseInt(idCasino, 10) })
            })
            .then(function(r) { return r.json().then(function(d) { return { status: r.status, data: d }; }); })
            .then(function(res) {
                if (res.status >= 200 && res.status < 300 && res.data && res.data.ok) {
                    showResult(true, 'Consumo registrado correctamente.', res.data.consumo ? (res.data.consumo.nombres + ' · ' + res.data.consumo.horario) : '', res.data.entregados_hoy);
                    inputCedula.value = '';
                    inputCedula.focus();
                } else {
                    showResult(false, (res.data && res.data.mensaje) || 'Error al registrar.', '');
                }
            })
            .catch(function() {
                showResult(false, 'Error de conexión. Revisa la red e intenta de nuevo.', '');
            })
            .finally(function() {
                btnRegistrarCedula.disabled = false;
            });
        }
        btnRegistrarCedula.addEventListener('click', registrarPorCedula);
        inputCedula.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                registrarPorCedula();
            }
        });
    }
})();
</script>
@endpush
@endsection
