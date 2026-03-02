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

        if ($role === 'casino') {
            $user = auth()->user();
            $hoy = Carbon::now();
            $mesAnterior = $hoy->copy()->subMonth();
            $qBase = RegistroConsumo::query();
            if ($user && $user->id_casino_asignado) {
                $qBase->where('id_casino', $user->id_casino_asignado);
            }
            $consumoMesActual = (clone $qBase)
                ->whereYear('fecha_consumo', $hoy->year)
                ->whereMonth('fecha_consumo', $hoy->month)
                ->count();
            $consumoMesAnterior = (clone $qBase)
                ->whereYear('fecha_consumo', $mesAnterior->year)
                ->whereMonth('fecha_consumo', $mesAnterior->month)
                ->count();
            $valorTotalAlmuerzosMesActual = (clone $qBase)
                ->whereYear('fecha_consumo', $hoy->year)
                ->whereMonth('fecha_consumo', $hoy->month)
                ->sum('precio_casino');

            $inicioSemana = $hoy->copy()->startOfWeek();
            $finSemana = $hoy->copy()->endOfWeek();
            $consumosPorDia = (clone $qBase)
                ->whereBetween('fecha_consumo', [$inicioSemana->toDateString(), $finSemana->toDateString()])
                ->selectRaw('fecha_consumo, COUNT(*) as total')
                ->groupBy('fecha_consumo')
                ->pluck('total', 'fecha_consumo')
                ->toArray();
            $semanaLabels = [];
            $semanaData = [];
            for ($fecha = $inicioSemana->copy(); $fecha->lte($finSemana); $fecha->addDay()) {
                $key = $fecha->toDateString();
                $semanaLabels[] = $fecha->translatedFormat('D d/m');
                $semanaData[] = (int) ($consumosPorDia[$key] ?? 0);
            }

            return view($view, [
                'consumoMesActual'              => $consumoMesActual,
                'consumoMesAnterior'            => $consumoMesAnterior,
                'valorTotalAlmuerzosMesActual'  => (float) $valorTotalAlmuerzosMesActual,
                'mesActual'                     => $hoy->translatedFormat('F Y'),
                'mesAnterior'                   => $mesAnterior->translatedFormat('F Y'),
                'semanaLabels'                  => $semanaLabels,
                'semanaData'                    => $semanaData,
            ]);
        }

        return view($view);
    }
}
