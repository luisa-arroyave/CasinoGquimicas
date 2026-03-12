<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use App\Models\HorarioConsumo;
use App\Models\Precio;
use App\Models\RegistroConsumo;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitarConsumoController extends Controller
{
    /**
     * Formulario para solicitar consumo. Tipo de comida = horario vigente por hora. Casino = de la empresa del usuario.
     */
    public function create(Request $request): View
    {
        $usuarioEmpresarial = auth()->user();

        if (! $usuarioEmpresarial || ! $usuarioEmpresarial->activo) {
            abort(403, 'Su cuenta no está activa. Contacte al administrador.');
        }

        $horarioVigente = HorarioConsumo::horarioVigente();

        $sedesPermitidasIds = $usuarioEmpresarial->sedes_permitidas_ids;
        $sedeElegida = null;
        if ($request->filled('id_sede') && in_array((int) $request->id_sede, $sedesPermitidasIds, true)) {
            $sedeElegida = (int) $request->id_sede;
        }
        $sedeIdsParaCasinos = ! empty($sedesPermitidasIds)
            ? ($sedeElegida ? [$sedeElegida] : $sedesPermitidasIds)
            : [];
        $casinos = Casino::where('activo', true)
            ->whereHas('empresas', fn ($q) => $q->where('empresas.id_empresa', $usuarioEmpresarial->id_empresa));
        if (! empty($sedeIdsParaCasinos)) {
            $casinos->whereIn('id_sede', $sedeIdsParaCasinos);
        }
        $casinos = $casinos->orderBy('nombre')->get();

        $casinoPorDefecto = $casinos->count() === 1 ? $casinos->first() : null;
        if (! $casinoPorDefecto && ! $sedeElegida && $usuarioEmpresarial->id_sede_principal && $casinos->isNotEmpty()) {
            $casinoPorDefecto = $casinos->firstWhere('id_sede', $usuarioEmpresarial->id_sede_principal) ?? $casinos->first();
        }
        $horariosDisponibles = HorarioConsumo::where('activo', true)->orderBy('hora_inicio')->get();

        // Consumo ya solicitado para el horario vigente (código activo según horario de la comida)
        $consumoActivo = null;
        $codigoQrActivo = null;
        if ($horarioVigente) {
            $consumoActivo = RegistroConsumo::with(['casino', 'horarioConsumo'])
                ->where('id_usuario', $usuarioEmpresarial->id_usuario)
                ->whereDate('fecha_consumo', Carbon::today())
                ->where('id_horario', $horarioVigente->id_horario)
                ->where('estado', 'SOLICITADO')
                ->first();
            if ($consumoActivo) {
                $codigoQrActivo = 'CONSUMO-' . $consumoActivo->id_consumo;
            }
        }

        $sedesParaOtraSede = count($sedesPermitidasIds) > 1 && $horarioVigente
            ? \App\Models\Sede::whereIn('id_sede', $sedesPermitidasIds)->orderBy('nombre')->get()
            : collect([]);

        return view('solicitar-consumo.create', [
            'casinos' => $casinos,
            'usuarioEmpresarial' => $usuarioEmpresarial,
            'horarioVigente' => $horarioVigente,
            'casinoPorDefecto' => $casinoPorDefecto,
            'horariosDisponibles' => $horariosDisponibles,
            'consumoActivo' => $consumoActivo,
            'codigoQrActivo' => $codigoQrActivo,
            'sedesParaOtraSede' => $sedesParaOtraSede,
            'sedeElegida' => $sedeElegida,
        ]);
    }

    /**
     * Guardar solicitud de consumo. Si casino tiene tipo_casino=domicilio, crear pedido a domicilio con direccion.
     */
    public function store(Request $request): RedirectResponse
    {
        $usuarioEmpresarial = auth()->user();

        if (! $usuarioEmpresarial || ! $usuarioEmpresarial->activo) {
            abort(403, 'Su cuenta no está activa.');
        }

        $valid = $request->validate([
            'id_casino' => ['required', 'exists:casinos,id_casino'],
            'direccion_entrega' => 'nullable|string|max:500',
        ]);

        $casino = Casino::where('activo', true)->findOrFail($valid['id_casino']);
        $sedesPermitidasIds = $usuarioEmpresarial->sedes_permitidas_ids;
        if (! empty($sedesPermitidasIds)) {
            if (! in_array($casino->id_sede, $sedesPermitidasIds)) {
                abort(403, 'No puede registrar consumo en ese punto de entrega.');
            }
        }
        if (! $casino->empresas()->where('empresas.id_empresa', $usuarioEmpresarial->id_empresa)->exists()) {
            abort(403, 'Su empresa no tiene acceso a ese punto de entrega.');
        }

        $esDomicilio = strtolower(trim($casino->tipo_casino ?? '')) === 'domicilio';

        if ($esDomicilio && empty(trim($valid['direccion_entrega'] ?? ''))) {
            return back()->withInput()->withErrors(['direccion_entrega' => 'La dirección de entrega es obligatoria para pedidos a domicilio.']);
        }

        $horario = HorarioConsumo::horarioVigente();
        if (! $horario) {
            return back()->withErrors(['horario' => 'No hay horario de consumo vigente en este momento.']);
        }

        $hoy = Carbon::today()->toDateString();

        // Evitar duplicado: un consumo por usuario, fecha, horario (unique en BD)
        $existente = RegistroConsumo::where('id_usuario', $usuarioEmpresarial->id_usuario)
            ->where('id_horario', $horario->id_horario)
            ->whereDate('fecha_consumo', $hoy)
            ->first();

        if ($existente) {
            return back()->withErrors(['id_casino' => 'Ya tiene un consumo solicitado para ' . $horario->nombre . ' hoy.']);
        }

        $precio = Precio::where('id_horario', $horario->id_horario)
            ->where(function ($q) use ($casino) {
                $q->where('id_casino', $casino->id_casino)->orWhereNull('id_casino');
            })
            ->orderByRaw('CASE WHEN id_casino IS NOT NULL THEN 0 ELSE 1 END')
            ->first();

        $precioEmpleado = $precio ? (float) $precio->precio_empleado : 0;
        $precioCasino = $precio ? (float) $precio->precio_casino : 0;

        $usuarioEmpresarial->load(['rol', 'tipoUsuario', 'empresa', 'empresaTemporal']);
        $empresa = $usuarioEmpresarial->empresa;
        $empresaTemporalNombre = $usuarioEmpresarial->empresaTemporal?->nombre;

        $consumo = RegistroConsumo::create([
            'id_usuario' => $usuarioEmpresarial->id_usuario,
            'id_visitante' => null,
            'id_empresa' => $usuarioEmpresarial->id_empresa,
            'id_casino' => $casino->id_casino,
            'id_horario' => $horario->id_horario,
            'documento' => $usuarioEmpresarial->documento,
            'nombres_consumidor' => $usuarioEmpresarial->nombres,
            'tipo_usuario_nombre' => $usuarioEmpresarial->tipoUsuario?->nombre ?? null,
            'empresa_nombre' => $empresa?->nombre,
            'empresa_temporal_nombre' => $empresaTemporalNombre,
            'casino_nombre' => $casino->nombre,
            'horario_nombre' => $horario->nombre,
            'fecha_consumo' => $hoy,
            'hora_consumo' => Carbon::now()->format('H:i:s'),
            'precio_empleado' => $precioEmpleado,
            'precio_casino' => $precioCasino,
            'estado' => 'SOLICITADO',
            'tipo_pedido' => $esDomicilio ? 'domicilio' : 'en_sitio',
            'direccion_entrega' => $esDomicilio ? trim($valid['direccion_entrega']) : null,
            'registrado_por' => null,
        ]);

        return redirect()->route('solicitar-consumo.create')
            ->with('success', 'Consumo registrado. Presente el código QR en el punto de entrega.');
    }

    /**
     * Mostrar código QR del consumo recién registrado para que el empleado lo presente en el casino.
     */
    public function mostrarQr(RegistroConsumo $consumo): View|RedirectResponse
    {
        $user = auth()->user();
        if ($consumo->id_usuario != $user->id_usuario) {
            abort(403, 'No puede ver el QR de otro empleado.');
        }
        if ($consumo->estado !== 'SOLICITADO') {
            return redirect()->route('solicitar-consumo.create')->with('info', 'Este consumo ya fue validado en el punto de entrega.');
        }

        $codigoQr = 'CONSUMO-' . $consumo->id_consumo;
        $consumo->load(['casino', 'horarioConsumo']);

        return view('solicitar-consumo.mostrar-qr', [
            'consumo' => $consumo,
            'codigoQr' => $codigoQr,
        ]);
    }
}
