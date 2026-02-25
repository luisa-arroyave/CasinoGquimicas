<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\RegistroConsumo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class GestionHumanaController extends Controller
{
    /**
     * Reportes: filtro por fechas y empresa, ver consumo por empresa.
     * Si el usuario tiene empresas asignadas (administrador/gestión humana), solo ve esas.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $empresasPermitidas = $user->empresasAcceso()->get()->pluck('id_empresa')->toArray();

        if (count($empresasPermitidas) > 0) {
            $empresas = Empresa::where('activa', true)->whereIn('id_empresa', $empresasPermitidas)->orderBy('nombre')->get();
        } else {
            $empresas = Empresa::where('activa', true)->orderBy('nombre')->get();
        }

        $fechaDesde = $request->input('fecha_desde', Carbon::today()->subDays(7)->toDateString());
        $fechaHasta = $request->input('fecha_hasta', Carbon::today()->toDateString());
        $idEmpresa = $request->input('id_empresa');

        $query = RegistroConsumo::query()
            ->whereBetween('fecha_consumo', [$fechaDesde, $fechaHasta])
            ->with(['usuario', 'visitante', 'empresa', 'casino', 'horarioConsumo'])
            ->orderBy('fecha_consumo')
            ->orderBy('id_empresa')
            ->orderBy('hora_consumo');

        if (count($empresasPermitidas) > 0) {
            $query->whereIn('id_empresa', $empresasPermitidas);
        } elseif ($user->role === 'gestionhumana') {
            $query->where('id_empresa', $user->id_empresa);
        }

        if ($idEmpresa) {
            $query->where('id_empresa', $idEmpresa);
        }

        $consumos = $query->get();

        $porEmpresa = $consumos->groupBy('id_empresa')->map(function ($items, $idEmpresa) {
            $empresa = $items->first()->empresa;
            return [
                'empresa' => $empresa,
                'nombre' => $empresa?->nombre ?? 'Sin empresa',
                'cantidad' => $items->count(),
                'total_empleado' => round($items->sum('precio_empleado'), 2),
                'total_casino' => round($items->sum('precio_casino'), 2),
            ];
        })->values();

        $totales = [
            'cantidad' => $consumos->count(),
            'total_empleado' => round($consumos->sum('precio_empleado'), 2),
            'total_casino' => round($consumos->sum('precio_casino'), 2),
        ];

        return view('gestion-humana.index', [
            'empresas' => $empresas,
            'consumos' => $consumos,
            'porEmpresa' => $porEmpresa,
            'totales' => $totales,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
            'id_empresa' => $idEmpresa,
        ]);
    }

    /**
     * Descargar reporte en PDF.
     */
    public function exportarPdf(Request $request): Response
    {
        $request->validate([
            'fecha_desde' => ['required', 'date'],
            'fecha_hasta' => ['required', 'date', 'after_or_equal:fecha_desde'],
        ]);

        $user = $request->user();
        $empresasPermitidas = $user->empresasAcceso()->get()->pluck('id_empresa')->toArray();

        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $idEmpresa = $request->input('id_empresa');

        $query = RegistroConsumo::query()
            ->whereBetween('fecha_consumo', [$fechaDesde, $fechaHasta])
            ->with(['usuario', 'visitante', 'empresa', 'casino', 'horarioConsumo'])
            ->orderBy('id_empresa')
            ->orderBy('fecha_consumo')
            ->orderBy('hora_consumo');

        if (count($empresasPermitidas) > 0) {
            $query->whereIn('id_empresa', $empresasPermitidas);
        } elseif ($user->role === 'gestionhumana') {
            $query->where('id_empresa', $user->id_empresa);
        }
        if ($idEmpresa) {
            $query->where('id_empresa', $idEmpresa);
        }

        $consumos = $query->get();
        $porEmpresa = $consumos->groupBy('id_empresa')->map(fn ($items) => [
            'nombre' => $items->first()->empresa?->nombre ?? 'Sin empresa',
            'cantidad' => $items->count(),
            'total_casino' => round($items->sum('precio_casino'), 2),
        ])->values();

        $pdf = Pdf::loadView('gestion-humana.reporte-pdf', [
            'consumos' => $consumos,
            'porEmpresa' => $porEmpresa,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
            'total_general' => $consumos->count(),
            'valor_total' => round($consumos->sum('precio_casino'), 2),
        ]);

        $nombre = 'reporte-consumos-' . $fechaDesde . '-' . $fechaHasta . '.pdf';
        return $pdf->download($nombre);
    }

    /**
     * Exportar reporte en Excel (CSV con UTF-8 BOM para Excel).
     */
    public function exportarExcel(Request $request): Response
    {
        $request->validate([
            'fecha_desde' => ['required', 'date'],
            'fecha_hasta' => ['required', 'date', 'after_or_equal:fecha_desde'],
        ]);

        $user = $request->user();
        $empresasPermitidas = $user->empresasAcceso()->get()->pluck('id_empresa')->toArray();

        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $idEmpresa = $request->input('id_empresa');

        $query = RegistroConsumo::query()
            ->whereBetween('fecha_consumo', [$fechaDesde, $fechaHasta])
            ->with(['usuario', 'visitante', 'empresa', 'casino', 'horarioConsumo'])
            ->orderBy('id_empresa')
            ->orderBy('fecha_consumo')
            ->orderBy('hora_consumo');

        if (count($empresasPermitidas) > 0) {
            $query->whereIn('id_empresa', $empresasPermitidas);
        } elseif ($user->role === 'gestionhumana') {
            $query->where('id_empresa', $user->id_empresa);
        }
        if ($idEmpresa) {
            $query->where('id_empresa', $idEmpresa);
        }

        $consumos = $query->get();

        $nombre = 'reporte-consumos-' . $fechaDesde . '-' . $fechaHasta . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombre . '"',
        ];

        $callback = function () use ($consumos) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Fecha', 'Hora', 'Empresa', 'Casino', 'Horario', 'Persona', 'Documento', 'Estado', 'P. empleado', 'P. casino'], ';');
            foreach ($consumos as $c) {
                $persona = $c->usuario ? $c->usuario->nombres : ($c->visitante ? $c->visitante->nombre : '—');
                $doc = $c->usuario ? $c->usuario->documento : ($c->visitante?->documento ?? '—');
                fputcsv($out, [
                    $c->fecha_consumo?->format('Y-m-d'),
                    $c->hora_consumo ? (is_object($c->hora_consumo) ? $c->hora_consumo->format('H:i') : substr($c->hora_consumo, 0, 5)) : '',
                    $c->empresa?->nombre ?? '',
                    $c->casino?->nombre ?? '',
                    $c->horarioConsumo?->nombre ?? '',
                    $persona,
                    $doc,
                    $c->estado ?? '',
                    str_replace('.', ',', (string) $c->precio_empleado),
                    str_replace('.', ',', (string) $c->precio_casino),
                ], ';');
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
