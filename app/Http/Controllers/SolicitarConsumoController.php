<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use App\Models\HorarioConsumo;
use App\Models\Precio;
use App\Models\RegistroConsumo;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitarConsumoController extends Controller
{
    /**
     * Formulario para solicitar consumo: seleccionar casino. Si casino tiene tipo_casino=domicilio, es pedido a domicilio.
     */
    public function create(): View
    {
        $usuarioEmpresarial = Usuario::where('email', auth()->user()->email)
            ->where('activo', true)
            ->first();

        if (! $usuarioEmpresarial) {
            abort(403, 'Su cuenta no está asociada a un empleado activo. Contacte al administrador.');
        }

        $casinos = Casino::where('activo', true)->orderBy('nombre')->get();

        return view('solicitar-consumo.create', [
            'casinos' => $casinos,
            'usuarioEmpresarial' => $usuarioEmpresarial,
        ]);
    }

    /**
     * Guardar solicitud de consumo. Si casino tiene tipo_casino=domicilio, crear pedido a domicilio con direccion.
     */
    public function store(Request $request): RedirectResponse
    {
        $usuarioEmpresarial = Usuario::where('email', auth()->user()->email)
            ->where('activo', true)
            ->first();

        if (! $usuarioEmpresarial) {
            abort(403, 'Su cuenta no está asociada a un empleado activo.');
        }

        $casino = Casino::where('activo', true)->findOrFail($request->input('id_casino'));

        $valid = $request->validate([
            'id_casino' => 'required|exists:casinos,id_casino',
            'direccion_entrega' => 'nullable|string|max:500',
        ]);

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

        RegistroConsumo::create([
            'id_usuario' => $usuarioEmpresarial->id_usuario,
            'id_visitante' => null,
            'id_empresa' => $usuarioEmpresarial->id_empresa,
            'id_casino' => $casino->id_casino,
            'id_horario' => $horario->id_horario,
            'fecha_consumo' => $hoy,
            'hora_consumo' => Carbon::now()->format('H:i:s'),
            'precio_empleado' => $precioEmpleado,
            'precio_casino' => $precioCasino,
            'estado' => 'SOLICITADO',
            'tipo_pedido' => $esDomicilio ? 'domicilio' : 'en_sitio',
            'direccion_entrega' => $esDomicilio ? trim($valid['direccion_entrega']) : null,
            'registrado_por' => null,
        ]);

        $mensaje = $esDomicilio
            ? 'Pedido a domicilio solicitado correctamente. Será entregado en la dirección indicada.'
            : 'Consumo solicitado correctamente. Diríjase al punto de entrega para recoger.';

        return redirect()->route('solicitar-consumo.create')->with('success', $mensaje);
    }
}
