<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use App\Models\RegistroConsumo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CasinoPanelController extends Controller
{
    /**
     * Panel casino: consumos del día, contador de vales, totales, consulta por rango.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Casino::where('activo', true);
        if ($user && $user->role === 'casino' && $user->id_casino_asignado) {
            $query->where('id_casino', $user->id_casino_asignado);
        }
        $casinos = $query->orderBy('nombre')->get();
        $idCasino = $request->input('id_casino');
        if ($user && $user->role === 'casino' && $user->id_casino_asignado) {
            $idCasino = $user->id_casino_asignado;
        }
        $idCasino = $idCasino ?? $casinos->first()?->id_casino;
        $fechaDesde = $request->input('fecha_desde', Carbon::today()->toDateString());
        $fechaHasta = $request->input('fecha_hasta', Carbon::today()->toDateString());

        $casino = $casinos->firstWhere('id_casino', (int) $idCasino);
        if (! $casino) {
            $casino = $casinos->first();
        }
        $idCasino = $casino?->id_casino;

        $consumos = RegistroConsumo::query()
            ->where('id_casino', $idCasino)
            ->where('estado', 'ENTREGADO')
            ->whereBetween('fecha_consumo', [$fechaDesde, $fechaHasta])
            ->with(['usuario', 'visitante', 'horarioConsumo', 'empresa'])
            ->orderBy('fecha_consumo')
            ->orderBy('hora_consumo')
            ->get();

        // Totales del período (solo consumos ENTREGADOS)
        $totales = [
            'cantidad_vales' => $consumos->count(),
            'total_precio_empleado' => $consumos->sum('precio_empleado'),
            'total_precio_casino' => $consumos->sum('precio_casino'),
        ];

        return view('casino.panel', [
            'casinos' => $casinos,
            'casino' => $casino,
            'consumos' => $consumos,
            'totales' => $totales,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ]);
    }

    /**
     * Datos JSON para actualización en tiempo real (consumos, contador, totales).
     */
    public function datos(Request $request)
    {
        $request->validate([
            'id_casino' => ['required', 'integer', 'exists:casinos,id_casino'],
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
        ]);

        $user = $request->user();
        $idCasino = (int) $request->input('id_casino');
        if ($user && $user->role === 'casino' && $user->id_casino_asignado) {
            $idCasino = (int) $user->id_casino_asignado;
        }
        $fechaDesde = $request->input('fecha_desde', Carbon::today()->toDateString());
        $fechaHasta = $request->input('fecha_hasta', Carbon::today()->toDateString());

        $consumos = RegistroConsumo::query()
            ->where('id_casino', $idCasino)
            ->where('estado', 'ENTREGADO')
            ->whereBetween('fecha_consumo', [$fechaDesde, $fechaHasta])
            ->with(['usuario:id_usuario,nombres,documento', 'visitante:id_visitante,nombre,documento', 'horarioConsumo:id_horario,nombre', 'empresa:id_empresa,nombre'])
            ->orderBy('fecha_consumo')
            ->orderBy('hora_consumo')
            ->get();

        // Totales del período (solo consumos ENTREGADOS)
        $totales = [
            'cantidad_vales' => $consumos->count(),
            'total_precio_empleado' => round($consumos->sum('precio_empleado'), 2),
            'total_precio_casino' => round($consumos->sum('precio_casino'), 2),
        ];

        return response()->json([
            'consumos' => $consumos->map(fn ($c) => [
                'id_consumo' => $c->id_consumo,
                'fecha_consumo' => $c->fecha_consumo?->format('Y-m-d'),
                'hora_consumo' => $c->hora_consumo ? (is_string($c->hora_consumo) ? substr($c->hora_consumo, 0, 8) : $c->hora_consumo->format('H:i:s')) : null,
                'horario' => $c->horarioConsumo?->nombre,
                'persona' => $c->usuario ? $c->usuario->nombres . ' (' . $c->usuario->documento . ')' : ($c->visitante ? $c->visitante->nombre : '—'),
                'empresa' => $c->empresa?->nombre,
                'estado' => $c->estado,
                'precio_empleado' => (float) $c->precio_empleado,
                'precio_casino' => (float) $c->precio_casino,
            ]),
            'totales' => $totales,
        ]);
    }
}
