<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\RegistroConsumo;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mostrar el panel según el rol del usuario.
     */
    public function index(): View
    {
        $role = auth()->user()->role ?? 'empleado';
        $view = 'dashboard.' . $role;

        if (! view()->exists($view)) {
            $view = 'dashboard.empleado';
        }

        if ($role === 'administrador') {
            $hoy = Carbon::now();
            $mes = $hoy->month;
            $año = $hoy->year;

            $empleadosPorEmpresa = Empresa::query()
                ->withCount('usuarios')
                ->orderBy('nombre')
                ->get();

            $valesQuincena1_15 = RegistroConsumo::query()
                ->whereYear('fecha_consumo', $año)
                ->whereMonth('fecha_consumo', $mes)
                ->whereDay('fecha_consumo', '<=', 15)
                ->count();

            $valesQuincena16_31 = RegistroConsumo::query()
                ->whereYear('fecha_consumo', $año)
                ->whereMonth('fecha_consumo', $mes)
                ->whereDay('fecha_consumo', '>=', 16)
                ->count();

            $consumoMesActual = RegistroConsumo::query()
                ->whereYear('fecha_consumo', $año)
                ->whereMonth('fecha_consumo', $mes)
                ->count();

            $mesAnterior = $hoy->copy()->subMonth();
            $consumoMesAnterior = RegistroConsumo::query()
                ->whereYear('fecha_consumo', $mesAnterior->year)
                ->whereMonth('fecha_consumo', $mesAnterior->month)
                ->count();

            return view($view, [
                'empleadosPorEmpresa'  => $empleadosPorEmpresa,
                'valesQuincena1_15'    => $valesQuincena1_15,
                'valesQuincena16_31'   => $valesQuincena16_31,
                'mesActual'            => $hoy->translatedFormat('F Y'),
                'mesAnterior'           => $mesAnterior->translatedFormat('F Y'),
                'consumoMesActual'     => $consumoMesActual,
                'consumoMesAnterior'   => $consumoMesAnterior,
            ]);
        }

        return view($view);
    }
}
