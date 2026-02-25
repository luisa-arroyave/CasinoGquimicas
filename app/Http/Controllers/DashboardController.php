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
            $user = auth()->user();
            $empresasPermitidas = $user->empresasAcceso()->get()->pluck('id_empresa')->toArray();
            $filtrarEmpresas = count($empresasPermitidas) > 0;

            $hoy = Carbon::now();
            $mes = $hoy->month;
            $año = $hoy->year;

            $qEmpresas = Empresa::query()->withCount('usuarios')->orderBy('nombre');
            if ($filtrarEmpresas) {
                $qEmpresas->whereIn('id_empresa', $empresasPermitidas);
            }
            $empleadosPorEmpresa = $qEmpresas->get();

            $qBase = RegistroConsumo::query();
            if ($filtrarEmpresas) {
                $qBase->whereIn('id_empresa', $empresasPermitidas);
            }

            $valesQuincena1_15 = (clone $qBase)
                ->whereYear('fecha_consumo', $año)
                ->whereMonth('fecha_consumo', $mes)
                ->whereDay('fecha_consumo', '<=', 15)
                ->count();

            $valesQuincena16_31 = (clone $qBase)
                ->whereYear('fecha_consumo', $año)
                ->whereMonth('fecha_consumo', $mes)
                ->whereDay('fecha_consumo', '>=', 16)
                ->count();

            $consumoMesActual = (clone $qBase)
                ->whereYear('fecha_consumo', $año)
                ->whereMonth('fecha_consumo', $mes)
                ->count();

            $mesAnterior = $hoy->copy()->subMonth();
            $consumoMesAnterior = (clone $qBase)
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
