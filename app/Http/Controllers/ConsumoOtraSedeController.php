<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use App\Models\HorarioConsumo;
use App\Models\Precio;
use App\Models\RegistroConsumo;
use App\Models\Sede;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsumoOtraSedeController extends Controller
{
    /**
     * Formulario para solicitar consumo en otra sede. El usuario selecciona sede y casino.
     * Tipo de comida = horario vigente (deshabilitado, según hora actual).
     */
    public function create(): View
    {
        $usuario = auth()->user();
        if (! $usuario || ! $usuario->activo) {
            abort(403, 'Su cuenta no está activa. Contacte al administrador.');
        }

        $sedesPermitidasIds = $usuario->sedes_permitidas_ids;
        $sedes = Sede::whereIn('id_sede', $sedesPermitidasIds)
            ->with(['casinos' => fn ($q) => $q->where('activo', true)
                ->whereHas('empresas', fn ($eq) => $eq->where('empresas.id_empresa', $usuario->id_empresa))
                ->orderBy('nombre')])
            ->orderBy('nombre')
            ->get();

        $horarioVigente = HorarioConsumo::horarioVigente();
        $horariosDisponibles = HorarioConsumo::where('activo', true)->orderBy('hora_inicio')->get();

        $oldSede = null;
        if (old('id_casino')) {
            $casinoOld = Casino::find(old('id_casino'));
            $oldSede = $casinoOld?->id_sede;
        }

        // Casinos por sede para JS: { id_sede: [{ id_casino, nombre, tipo_casino }, ...] }
        $casinosPorSede = $sedes->mapWithKeys(function ($sede) {
            return [
                $sede->id_sede => $sede->casinos->map(fn ($c) => [
                    'id_casino' => $c->id_casino,
                    'nombre' => $c->nombre . (strtolower(trim($c->tipo_casino ?? '')) === 'domicilio' ? ' (a domicilio)' : ''),
                    'tipo' => strtolower(trim($c->tipo_casino ?? '')),
                ])->values()->toArray(),
            ];
        })->toArray();

        // Consumo ya solicitado para horario vigente
        $consumoActivo = null;
        $codigoQrActivo = null;
        if ($horarioVigente) {
            $consumoActivo = RegistroConsumo::with(['casino', 'horarioConsumo'])
                ->where('id_usuario', $usuario->id_usuario)
                ->whereDate('fecha_consumo', Carbon::today())
                ->where('id_horario', $horarioVigente->id_horario)
                ->where('estado', 'SOLICITADO')
                ->first();
            if ($consumoActivo) {
                $codigoQrActivo = 'CONSUMO-' . $consumoActivo->id_consumo;
            }
        }

        return view('consumo-otra-sede.create', [
            'sedes' => $sedes,
            'oldSede' => $oldSede,
            'casinosPorSede' => $casinosPorSede,
            'horarioVigente' => $horarioVigente,
            'horariosDisponibles' => $horariosDisponibles,
            'consumoActivo' => $consumoActivo,
            'codigoQrActivo' => $codigoQrActivo,
        ]);
    }

    /**
     * Registrar consumo en otra sede y redirigir al código QR.
     */
    public function store(Request $request): RedirectResponse
    {
        $usuario = auth()->user();
        if (! $usuario || ! $usuario->activo) {
            abort(403, 'Su cuenta no está activa.');
        }

        $valid = $request->validate([
            'id_casino' => ['required', 'exists:casinos,id_casino'],
        ]);

        $casino = Casino::where('activo', true)->findOrFail($valid['id_casino']);
        $sedesPermitidasIds = $usuario->sedes_permitidas_ids;

        if (empty($sedesPermitidasIds) || ! in_array($casino->id_sede, $sedesPermitidasIds, true)) {
            abort(403, 'No puede registrar consumo en ese casino.');
        }
        if (! $casino->empresas()->where('empresas.id_empresa', $usuario->id_empresa)->exists()) {
            abort(403, 'Su empresa no tiene acceso a ese casino.');
        }

        $esDomicilio = strtolower(trim($casino->tipo_casino ?? '')) === 'domicilio';

        $horario = HorarioConsumo::horarioVigente();
        if (! $horario) {
            return back()->withErrors(['horario' => 'No hay horario de consumo vigente en este momento.']);
        }

        $hoy = Carbon::today()->toDateString();
        $existente = RegistroConsumo::where('id_usuario', $usuario->id_usuario)
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

        $usuario->load(['rol', 'tipoUsuario', 'empresa', 'empresaTemporal']);
        $empresa = $usuario->empresa;
        $empresaTemporalNombre = $usuario->empresaTemporal?->nombre;

        $consumo = RegistroConsumo::create([
            'id_usuario' => $usuario->id_usuario,
            'id_visitante' => null,
            'id_empresa' => $usuario->id_empresa,
            'id_casino' => $casino->id_casino,
            'id_horario' => $horario->id_horario,
            'documento' => $usuario->documento,
            'nombres_consumidor' => $usuario->nombres,
            'tipo_usuario_nombre' => $usuario->tipoUsuario?->nombre ?? null,
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
            'direccion_entrega' => null,
            'registrado_por' => null,
        ]);

        return redirect()->route('consumo-otra-sede.mostrar-qr', $consumo)
            ->with('success', 'Consumo registrado. Presente el código QR en el casino seleccionado.');
    }

    /**
     * Mostrar código QR del consumo solicitado en otra sede.
     */
    public function mostrarQr(RegistroConsumo $consumo): View|RedirectResponse
    {
        $user = auth()->user();
        if ($consumo->id_usuario != $user->id_usuario) {
            abort(403, 'No puede ver el QR de otro empleado.');
        }
        if ($consumo->estado !== 'SOLICITADO') {
            return redirect()->route('consumo-otra-sede.create')->with('info', 'Este consumo ya fue validado.');
        }

        $codigoQr = 'CONSUMO-' . $consumo->id_consumo;
        $consumo->load(['casino', 'horarioConsumo']);

        return view('consumo-otra-sede.mostrar-qr', [
            'consumo' => $consumo,
            'codigoQr' => $codigoQr,
        ]);
    }
}
