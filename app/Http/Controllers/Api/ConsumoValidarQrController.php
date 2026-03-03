<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HorarioConsumo;
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
            $consumo = RegistroConsumo::with(['usuario', 'horarioConsumo'])
                ->where('id_usuario', $usuario->id_usuario)
                ->where('id_casino', $idCasino)
                ->where('id_horario', $horario->id_horario)
                ->whereDate('fecha_consumo', $hoy)
                ->where('estado', 'SOLICITADO')
                ->first();

            if (! $consumo) {
                return response()->json([
                    'ok' => false,
                    'mensaje' => 'No hay consumo solicitado para este horario (' . $horario->nombre . ').',
                ], 404);
            }
        }

        $usuario = $consumo->usuario;
        $horario = $consumo->horarioConsumo;
        $hoy = Carbon::parse($consumo->fecha_consumo)->toDateString();

        // Confirmar: cambiar estado a ENTREGADO y registrar hora real
        $consumo->update([
            'estado' => 'ENTREGADO',
            'hora_consumo' => Carbon::now()->format('H:i:s'),
        ]);

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
}
