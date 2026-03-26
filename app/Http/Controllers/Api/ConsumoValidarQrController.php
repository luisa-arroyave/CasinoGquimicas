<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Casino;
use App\Models\HorarioConsumo;
use App\Models\Precio;
use App\Models\RegistroConsumo;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsumoValidarQrController extends Controller
{
    /**
     * Contador de entregas del día por casino (para mostrar en vista).
     * GET /api/consumo/entregados-hoy?id_casino=1
     */
    public function entregadosHoy(Request $request): JsonResponse
    {
        $request->validate([
            'id_casino' => ['required', 'integer', 'exists:casinos,id_casino'],
        ]);

        $count = RegistroConsumo::where('id_casino', (int) $request->input('id_casino'))
            ->whereDate('fecha_consumo', Carbon::today())
            ->where('estado', 'ENTREGADO')
            ->count();

        return response()->json(['entregados_hoy' => $count]);
    }

    /**
     * Validar QR, confirmar consumo en estado SOLICITADO y cambiar a ENTREGADO.
     * POST /api/consumo/validar-qr
     */
    public function validarQr(Request $request): JsonResponse
    {
        $request->validate([
            'codigo_qr' => ['required', 'string', 'max:255'],
            'id_casino' => ['required', 'integer', 'exists:casinos,id_casino'],
        ], [
            'codigo_qr.required' => 'El código QR es obligatorio.',
            'id_casino.required' => 'El casino es obligatorio.',
            'id_casino.exists' => 'Casino no válido.',
        ]);

        $codigoQr = trim($request->input('codigo_qr'));
        $idCasino = (int) $request->input('id_casino');

        // Algunos lectores QR de celular interpretan CONSUMO:10 como URL y envían http://consumo:10/
        if (preg_match('#^https?://consumo:(\d+)/?$#i', $codigoQr, $urlMatch)) {
            $codigoQr = 'CONSUMO-' . $urlMatch[1];
        }

        $user = $request->user();
        if ($user && $user->role === 'casino' && $user->id_casino_asignado && (int) $user->id_casino_asignado !== $idCasino) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No está autorizado para operar sobre este casino.',
            ], 403);
        }

        $consumo = null;

        // Formato: CONSUMO:id o CONSUMO6 o con espacios/caracteres extra (cámara, lectores)
        if (preg_match('/CONSUMO\D*(\d+)/', $codigoQr, $m)) {
            $consumo = RegistroConsumo::with(['usuario', 'horarioConsumo'])
                ->where('id_consumo', (int) $m[1])
                ->where('estado', 'SOLICITADO')
                ->first();

            if (! $consumo) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'Código QR no válido o consumo ya fue entregado.',
                ], 404);
            }
            if ($consumo->id_casino != $idCasino) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'Este consumo corresponde a otro punto de entrega. Seleccione el casino correcto.',
                ], 400);
            }
            $casino = Casino::find($idCasino);
            if ($casino && ! $casino->empresas()->where('empresas.id_empresa', $consumo->id_empresa)->exists()) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'Este consumo no corresponde a una empresa atendida por este casino.',
                ], 403);
            }
        } else {
            // Formato legacy: codigo_qr del usuario (tabla usuarios)
            $usuario = Usuario::where('codigo_qr', $codigoQr)
                ->where('activo', true)
                ->first();

            if (! $usuario) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'QR no válido o usuario inactivo.',
                ], 404);
            }

            $horario = HorarioConsumo::horarioVigente();
            if (! $horario) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'No hay horario de consumo vigente en este momento.',
                ], 400);
            }

            $hoy = Carbon::today()->toDateString();
            $periodoQr = RegistroConsumo::periodoTurnoDiaDesdeFechaHora($hoy, Carbon::now()->format('H:i:s'));
            $consumo = RegistroConsumo::with(['usuario', 'horarioConsumo'])
                ->where('id_usuario', $usuario->id_usuario)
                ->where('id_casino', $idCasino)
                ->where('id_horario', $horario->id_horario)
                ->whereDate('fecha_consumo', $hoy)
                ->where('periodo_turno_dia', $periodoQr)
                ->where('estado', 'SOLICITADO')
                ->first();

            if (! $consumo) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'No hay consumo solicitado para este horario (' . $horario->nombre . ').',
                ], 404);
            }
            $casino = Casino::find($idCasino);
            if ($casino && ! $casino->empresas()->where('empresas.id_empresa', $consumo->id_empresa)->exists()) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'Este consumo no corresponde a una empresa atendida por este casino.',
                ], 403);
            }
        }

        $usuario = $consumo->usuario;
        $horario = $consumo->horarioConsumo;
        $hoy = Carbon::parse($consumo->fecha_consumo)->toDateString();

        // Confirmar: cambiar estado a ENTREGADO y registrar hora real
        $payloadEntrega = [
            'estado' => 'ENTREGADO',
            'hora_consumo' => Carbon::now()->format('H:i:s'),
        ];
        if (empty($consumo->tipo_comida) && $horario) {
            $payloadEntrega['tipo_comida'] = mb_strtoupper(trim((string) $horario->nombre), 'UTF-8');
        }
        $consumo->update($payloadEntrega);

        // Contador de entregados hoy en este casino (para actualizar vista)
        $entregadosHoy = RegistroConsumo::where('id_casino', $idCasino)
            ->whereDate('fecha_consumo', $hoy)
            ->where('estado', 'ENTREGADO')
            ->count();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Entrega registrada correctamente.',
            'consumo' => [
                'id_consumo' => $consumo->id_consumo,
                'nombres' => $usuario?->nombres ?? '—',
                'documento' => $usuario?->documento ?? '—',
                'horario' => $horario?->nombre ?? '—',
                'hora_entrega' => $consumo->hora_consumo,
            ],
            'entregados_hoy' => $entregadosHoy,
        ]);
    }

    /**
     * Registrar consumo por cédula (sin QR). Crea registro con estado ENTREGADO.
     * POST /api/consumo/registrar-por-cedula
     */
    public function registrarPorCedula(Request $request): JsonResponse
    {
        $request->validate([
            'documento' => ['required', 'string', 'max:50'],
            'id_casino' => ['required', 'integer', 'exists:casinos,id_casino'],
        ], [
            'documento.required' => 'El número de cédula es obligatorio.',
            'id_casino.required' => 'El casino es obligatorio.',
            'id_casino.exists' => 'Casino no válido.',
        ]);

        $documento = trim($request->input('documento'));
        $idCasino = (int) $request->input('id_casino');

        $user = $request->user();
        if ($user && $user->role === 'casino' && $user->id_casino_asignado && (int) $user->id_casino_asignado !== $idCasino) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No está autorizado para operar sobre este casino.',
            ], 403);
        }

        $usuario = Usuario::where('documento', $documento)
            ->where('activo', true)
            ->with(['empresa', 'tipoUsuario', 'empresaTemporal', 'empresaContratista'])
            ->first();

        if (! $usuario) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Cédula no encontrada o usuario inactivo.',
            ], 404);
        }

        $horario = HorarioConsumo::horarioVigente();
        if (! $horario) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'No hay horario de consumo vigente en este momento.',
            ], 400);
        }

        $casino = Casino::find($idCasino);
        if ($casino && ! $casino->empresas()->where('empresas.id_empresa', $usuario->id_empresa)->exists()) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Este empleado no pertenece a una empresa atendida por este casino.',
            ], 403);
        }

        $hoy = Carbon::today()->toDateString();
        $horaCedula = Carbon::now()->format('H:i:s');
        $periodoCedula = RegistroConsumo::periodoTurnoDiaDesdeFechaHora($hoy, $horaCedula);

        $existente = RegistroConsumo::where('id_usuario', $usuario->id_usuario)
            ->where('id_horario', $horario->id_horario)
            ->whereDate('fecha_consumo', $hoy)
            ->where('periodo_turno_dia', $periodoCedula)
            ->first();

        if ($existente) {
            if ($existente->estado === 'ENTREGADO') {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'Ya se registró la entrega para este empleado hoy (' . $horario->nombre . ') en este periodo de turno.',
                ], 400);
            }
            $updCedula = [
                'estado' => 'ENTREGADO',
                'hora_consumo' => $horaCedula,
            ];
            if (empty($existente->tipo_comida)) {
                $updCedula['tipo_comida'] = mb_strtoupper(trim((string) $horario->nombre), 'UTF-8');
            }
            $existente->update($updCedula);
            $consumo = $existente->fresh(['usuario', 'horarioConsumo']);
        } else {
            $precio = Precio::where('id_horario', $horario->id_horario)
                ->where(function ($q) use ($casino) {
                    $q->where('id_casino', $casino->id_casino)->orWhereNull('id_casino');
                })
                ->orderByRaw('CASE WHEN id_casino IS NOT NULL THEN 0 ELSE 1 END')
                ->first();

            $precioEmpleado = $precio ? (float) $precio->precio_empleado : 0;
            $precioCasino = $precio ? (float) $precio->precio_casino : 0;

            $consumo = RegistroConsumo::create([
                'id_usuario' => $usuario->id_usuario,
                'id_visitante' => null,
                'id_empresa' => $usuario->id_empresa,
                'id_casino' => $idCasino,
                'id_horario' => $horario->id_horario,
                'documento' => $usuario->documento,
                'nombres_consumidor' => $usuario->nombres,
                'tipo_usuario_nombre' => $usuario->tipoUsuario?->nombre ?? null,
                'empresa_nombre' => $usuario->empresa?->nombre,
                'empresa_temporal_nombre' => $usuario->nombreEmpresaTemporalParaRegistroConsumo(),
                'empresa_contratista_nombre' => $usuario->nombreEmpresaContratistaParaRegistroConsumo(),
                'casino_nombre' => $casino->nombre,
                'horario_nombre' => $horario->nombre,
                'tipo_comida' => mb_strtoupper(trim((string) $horario->nombre), 'UTF-8'),
                'fecha_consumo' => $hoy,
                'hora_consumo' => $horaCedula,
                'precio_casino' => $precioCasino,
                'precio_empleado' => $precioEmpleado,
                'estado' => 'ENTREGADO',
                'tipo_pedido' => 'en_sitio',
                'registrado_por' => $user?->id_usuario ?? null,
            ]);
            $consumo->load(['usuario', 'horarioConsumo']);
        }

        $entregadosHoy = RegistroConsumo::where('id_casino', $idCasino)
            ->whereDate('fecha_consumo', $hoy)
            ->where('estado', 'ENTREGADO')
            ->count();

        return response()->json([
            'ok' => true,
            'mensaje' => 'Consumo registrado correctamente.',
            'consumo' => [
                'id_consumo' => $consumo->id_consumo,
                'nombres' => $consumo->usuario?->nombres ?? $consumo->nombres_consumidor ?? '—',
                'documento' => $consumo->documento ?? '—',
                'horario' => $consumo->horarioConsumo?->nombre ?? $consumo->horario_nombre ?? '—',
                'hora_entrega' => $consumo->hora_consumo,
            ],
            'entregados_hoy' => $entregadosHoy,
        ]);
    }
}
