<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\RegistroConsumo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

        // Solo empresas que el usuario tiene en "Empresas cuyos registros de consumo puede ver"
        if (count($empresasPermitidas) > 0) {
            $empresas = Empresa::where('activa', true)->whereIn('id_empresa', $empresasPermitidas)->orderBy('nombre')->get();
        } elseif ($user->role === 'gestionhumana' && $user->id_empresa) {
            $empresas = Empresa::where('activa', true)->where('id_empresa', $user->id_empresa)->orderBy('nombre')->get();
        } else {
            $empresas = Empresa::where('activa', true)->orderBy('nombre')->get();
        }

        $fechaDesde = $request->input('fecha_desde', Carbon::today()->subDays(7)->toDateString());
        $fechaHasta = $request->input('fecha_hasta', Carbon::today()->toDateString());
        $empresasSeleccionadas = $request->input('empresas', []);
        if (! is_array($empresasSeleccionadas)) {
            $empresasSeleccionadas = $empresasSeleccionadas ? [$empresasSeleccionadas] : [];
        }
        $empresasSeleccionadas = array_values(array_filter(array_map('intval', $empresasSeleccionadas)));
        $busquedaPersona = trim((string) $request->input('busqueda_persona', ''));

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

        if (! empty($empresasSeleccionadas)) {
            $query->whereIn('id_empresa', $empresasSeleccionadas);
        }

        if ($busquedaPersona !== '') {
            $term = '%' . $busquedaPersona . '%';
            $query->where(function ($q) use ($term) {
                $q->where('documento', 'like', $term)
                    ->orWhere('nombres_consumidor', 'like', $term)
                    ->orWhereHas('usuario', fn ($uq) => $uq->where('documento', 'like', $term)->orWhere('nombres', 'like', $term))
                    ->orWhereHas('visitante', fn ($vq) => $vq->where('documento', 'like', $term)->orWhere('nombre', 'like', $term));
            });
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
            'empresas_seleccionadas' => $empresasSeleccionadas,
            'busqueda_persona' => $busquedaPersona,
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
        $empresasSeleccionadas = $request->input('empresas', []);
        if (! is_array($empresasSeleccionadas)) {
            $empresasSeleccionadas = $empresasSeleccionadas ? [$empresasSeleccionadas] : [];
        }
        $empresasSeleccionadas = array_values(array_filter(array_map('intval', $empresasSeleccionadas)));
        $busquedaPersona = trim((string) $request->input('busqueda_persona', ''));

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
        if (! empty($empresasSeleccionadas)) {
            $query->whereIn('id_empresa', $empresasSeleccionadas);
        }
        if ($busquedaPersona !== '') {
            $term = '%' . $busquedaPersona . '%';
            $query->where(function ($q) use ($term) {
                $q->where('documento', 'like', $term)
                    ->orWhere('nombres_consumidor', 'like', $term)
                    ->orWhereHas('usuario', fn ($uq) => $uq->where('documento', 'like', $term)->orWhere('nombres', 'like', $term))
                    ->orWhereHas('visitante', fn ($vq) => $vq->where('documento', 'like', $term)->orWhere('nombre', 'like', $term));
            });
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
        $empresasSeleccionadas = $request->input('empresas', []);
        if (! is_array($empresasSeleccionadas)) {
            $empresasSeleccionadas = $empresasSeleccionadas ? [$empresasSeleccionadas] : [];
        }
        $empresasSeleccionadas = array_values(array_filter(array_map('intval', $empresasSeleccionadas)));
        $busquedaPersona = trim((string) $request->input('busqueda_persona', ''));

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
        if (! empty($empresasSeleccionadas)) {
            $query->whereIn('id_empresa', $empresasSeleccionadas);
        }
        if ($busquedaPersona !== '') {
            $term = '%' . $busquedaPersona . '%';
            $query->where(function ($q) use ($term) {
                $q->where('documento', 'like', $term)
                    ->orWhere('nombres_consumidor', 'like', $term)
                    ->orWhereHas('usuario', fn ($uq) => $uq->where('documento', 'like', $term)->orWhere('nombres', 'like', $term))
                    ->orWhereHas('visitante', fn ($vq) => $vq->where('documento', 'like', $term)->orWhere('nombre', 'like', $term));
            });
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

    /**
     * Reporte de Nómina: Excel con hojas "Resumen de la quincena" + una hoja por casino.
     */
    public function exportarExcelNomina(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $request->validate([
            'fecha_desde' => ['required', 'date'],
            'fecha_hasta' => ['required', 'date', 'after_or_equal:fecha_desde'],
        ]);

        $consumos = $this->obtenerConsumosFiltrados($request);

        $fechaDesde = Carbon::parse($request->input('fecha_desde'));
        $fechaHasta = Carbon::parse($request->input('fecha_hasta'));
        $periodo = CarbonPeriod::create($fechaDesde, $fechaHasta);

        $spreadsheet = new Spreadsheet();

        // Hoja 1: Resumen de la quincena (empresas en filas, días en columnas)
        $hojaResumen = $spreadsheet->getActiveSheet();
        $hojaResumen->setTitle('Resumen de la quincena');

        $fechasPeriodo = iterator_to_array($periodo);
        $empresasConConsumos = $consumos->groupBy('id_empresa')->mapWithKeys(function ($items, $idEmpresa) {
            $nombre = $items->first()->empresa_nombre ?? $items->first()->empresa?->nombre ?? 'Sin empresa';
            return [$idEmpresa => $nombre];
        })->sort()->all();

        $matriz = [];
        foreach ($consumos->groupBy('id_empresa') as $idEmpresa => $itemsEmpresa) {
            $porFecha = $itemsEmpresa->groupBy(fn ($c) => $c->fecha_consumo?->format('Y-m-d') ?? '');
            $matriz[$idEmpresa] = [];
            foreach ($fechasPeriodo as $fecha) {
                $fechaStr = $fecha->format('Y-m-d');
                $matriz[$idEmpresa][$fechaStr] = ($porFecha->get($fechaStr) ?? collect())->count();
            }
        }

        $numDias = count($fechasPeriodo);
        $colUltima = Coordinate::stringFromColumnIndex(2 + $numDias);
        $hojaResumen->mergeCells('A1:' . $colUltima . '1');
        $hojaResumen->setCellValue('A1', 'Reporte de Nómina - Resumen de la quincena');
        $this->aplicarEstiloTitulo($hojaResumen, 'A1:' . $colUltima . '1');
        $hojaResumen->setCellValue('A2', 'Período: ' . $fechaDesde->format('d/m/Y') . ' - ' . $fechaHasta->format('d/m/Y'));
        $hojaResumen->mergeCells('A2:' . $colUltima . '2');
        $hojaResumen->getStyle('A2:' . $colUltima . '2')->getFont()->setSize(10)->getColor()->setRGB('4B5563');

        $fila = 4;
        $hojaResumen->setCellValue('A' . $fila, 'Empresa');
        $col = 1;
        foreach ($fechasPeriodo as $fecha) {
            $col++;
            $letraCol = Coordinate::stringFromColumnIndex($col);
            $hojaResumen->setCellValue($letraCol . $fila, $fecha->format('d/m'));
        }
        $col++;
        $letraTotal = Coordinate::stringFromColumnIndex($col);
        $hojaResumen->setCellValue($letraTotal . $fila, 'Total');
        $this->aplicarEstiloEncabezadoTabla($hojaResumen, 'A' . $fila . ':' . $letraTotal . $fila);

        $fila = 5;
        foreach ($empresasConConsumos as $idEmpresa => $nombreEmpresa) {
            $hojaResumen->setCellValue('A' . $fila, $nombreEmpresa);
            $totalFila = 0;
            $col = 1;
            foreach ($fechasPeriodo as $fecha) {
                $col++;
                $fechaStr = $fecha->format('Y-m-d');
                $cantidad = $matriz[$idEmpresa][$fechaStr] ?? 0;
                $totalFila += $cantidad;
                $letraCol = Coordinate::stringFromColumnIndex($col);
                $hojaResumen->setCellValue($letraCol . $fila, $cantidad);
            }
            $col++;
            $letraColTotal = Coordinate::stringFromColumnIndex($col);
            $hojaResumen->setCellValue($letraColTotal . $fila, $totalFila);
            $fila++;
        }
        $ultimaFilaResumen = $fila - 1;
        if ($ultimaFilaResumen >= 5) {
            $this->aplicarBordesTabla($hojaResumen, 'A4:' . $letraTotal . $ultimaFilaResumen);
            $hojaResumen->getStyle('B5:' . $letraTotal . $ultimaFilaResumen)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $hojaResumen->getColumnDimension('A')->setWidth(30);
        for ($c = 2; $c <= $col; $c++) {
            $hojaResumen->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setWidth(10);
        }

        // Hojas adicionales: Detalle por Casino
        $casinosConConsumos = $consumos->groupBy('id_casino');
        foreach ($casinosConConsumos as $idCasino => $consumosCasino) {
            $casino = $consumosCasino->first()->casino;
            $nombreCasino = $casino?->nombre ?? 'Sin casino';
            $nombreHoja = $this->sanitizarNombreHoja($nombreCasino);

            $hoja = $spreadsheet->createSheet();
            $hoja->setTitle($nombreHoja);

            $hoja->mergeCells('A1:E1');
            $hoja->setCellValue('A1', 'Detalle por Casino - ' . $nombreCasino);
            $this->aplicarEstiloTitulo($hoja, 'A1:E1');
            $hoja->setCellValue('A3', 'Documento');
            $hoja->setCellValue('B3', 'Nombre');
            $hoja->setCellValue('C3', 'Empresa');
            $hoja->setCellValue('D3', 'Nº vales consumidos');
            $hoja->setCellValue('E3', 'Valor total');
            $this->aplicarEstiloEncabezadoTabla($hoja, 'A3:E3');

            $porDocumentoEmpresa = $consumosCasino->groupBy(function ($c) {
                $doc = $c->documento ?? ($c->usuario?->documento ?? $c->visitante?->documento ?? '—');
                $emp = $c->empresa_nombre ?? $c->empresa?->nombre ?? 'Sin empresa';
                return $doc . '|' . $emp;
            });

            $filaDet = 4;
            foreach ($porDocumentoEmpresa as $grupo => $items) {
                [$doc, $emp] = explode('|', $grupo, 2);
                $nombre = $items->first()->nombres_consumidor ?? $items->first()->usuario?->nombres ?? $items->first()->visitante?->nombre ?? '—';
                $hoja->setCellValue('A' . $filaDet, $doc);
                $hoja->setCellValue('B' . $filaDet, $nombre);
                $hoja->setCellValue('C' . $filaDet, $emp);
                $hoja->setCellValue('D' . $filaDet, $items->count());
                $hoja->setCellValue('E' . $filaDet, '$' . number_format(round($items->sum('precio_empleado'), 2), 0, ',', '.'));
                $filaDet++;
            }
            $this->aplicarBordesTabla($hoja, 'A3:E' . ($filaDet - 1));
            $hoja->getStyle('D4:E' . ($filaDet - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $hoja->getColumnDimension('A')->setWidth(16);
            $hoja->getColumnDimension('B')->setWidth(28);
            $hoja->getColumnDimension('C')->setWidth(30);
            $hoja->getColumnDimension('D')->setWidth(20);
            $hoja->getColumnDimension('E')->setWidth(16);
        }

        // Hojas adicionales: Invitados por Empresa (solo consumos de visitantes)
        $consumosInvitados = $consumos->filter(fn ($c) => $c->id_visitante !== null);
        $invitadosPorEmpresa = $consumosInvitados->groupBy('id_empresa');
        foreach ($invitadosPorEmpresa as $idEmpresa => $consumosEmpresa) {
            $nombreEmpresa = $consumosEmpresa->first()->empresa_nombre ?? $consumosEmpresa->first()->empresa?->nombre ?? 'Sin empresa';
            $nombreHoja = $this->sanitizarNombreHoja('Inv. ' . $nombreEmpresa);

            $hoja = $spreadsheet->createSheet();
            $hoja->setTitle($nombreHoja);

            $hoja->mergeCells('A1:E1');
            $hoja->setCellValue('A1', 'Invitados - ' . $nombreEmpresa);
            $this->aplicarEstiloTitulo($hoja, 'A1:E1');
            $hoja->setCellValue('A3', 'Documento');
            $hoja->setCellValue('B3', 'Nombre');
            $hoja->setCellValue('C3', 'Empresa');
            $hoja->setCellValue('D3', 'Nº vales consumidos');
            $hoja->setCellValue('E3', 'Valor total');
            $this->aplicarEstiloEncabezadoTabla($hoja, 'A3:E3');

            $porDocumento = $consumosEmpresa->groupBy(function ($c) {
                $doc = $c->documento ?? $c->visitante?->documento ?? '—';
                return $doc;
            });

            $filaInv = 4;
            foreach ($porDocumento as $doc => $items) {
                $nombre = $items->first()->nombres_consumidor ?? $items->first()->visitante?->nombre ?? '—';
                $emp = $items->first()->empresa_nombre ?? $items->first()->empresa?->nombre ?? 'Sin empresa';
                $hoja->setCellValue('A' . $filaInv, $doc);
                $hoja->setCellValue('B' . $filaInv, $nombre);
                $hoja->setCellValue('C' . $filaInv, $emp);
                $hoja->setCellValue('D' . $filaInv, $items->count());
                $hoja->setCellValue('E' . $filaInv, '$' . number_format(round($items->sum('precio_empleado'), 2), 0, ',', '.'));
                $filaInv++;
            }
            $this->aplicarBordesTabla($hoja, 'A3:E' . ($filaInv - 1));
            $hoja->getStyle('D4:E' . ($filaInv - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $hoja->getColumnDimension('A')->setWidth(16);
            $hoja->getColumnDimension('B')->setWidth(28);
            $hoja->getColumnDimension('C')->setWidth(30);
            $hoja->getColumnDimension('D')->setWidth(20);
            $hoja->getColumnDimension('E')->setWidth(16);
        }

        // Hoja: Temporales (tipo_usuario_nombre = TEMPORAL)
        $consumosTemporales = $consumos->filter(fn ($c) => strtoupper(trim((string) ($c->tipo_usuario_nombre ?? ''))) === 'TEMPORAL');
        if ($consumosTemporales->isNotEmpty()) {
            $hoja = $spreadsheet->createSheet();
            $hoja->setTitle($this->sanitizarNombreHoja('Temporales'));

            $hoja->mergeCells('A1:E1');
            $hoja->setCellValue('A1', 'Temporales');
            $this->aplicarEstiloTitulo($hoja, 'A1:E1');
            $hoja->setCellValue('A3', 'Documento');
            $hoja->setCellValue('B3', 'Nombre');
            $hoja->setCellValue('C3', 'Empresa');
            $hoja->setCellValue('D3', 'Nº vales consumidos');
            $hoja->setCellValue('E3', 'Valor total');
            $this->aplicarEstiloEncabezadoTabla($hoja, 'A3:E3');

            $porDocumentoEmpresa = $consumosTemporales->groupBy(function ($c) {
                $doc = $c->documento ?? ($c->usuario?->documento ?? $c->visitante?->documento ?? '—');
                $emp = $c->empresa_nombre ?? $c->empresa?->nombre ?? 'Sin empresa';
                return $doc . '|' . $emp;
            });

            $filaTemp = 4;
            foreach ($porDocumentoEmpresa as $grupo => $items) {
                [$doc, $emp] = explode('|', $grupo, 2);
                $nombre = $items->first()->nombres_consumidor ?? $items->first()->usuario?->nombres ?? $items->first()->visitante?->nombre ?? '—';
                $hoja->setCellValue('A' . $filaTemp, $doc);
                $hoja->setCellValue('B' . $filaTemp, $nombre);
                $hoja->setCellValue('C' . $filaTemp, $emp);
                $hoja->setCellValue('D' . $filaTemp, $items->count());
                $hoja->setCellValue('E' . $filaTemp, '$' . number_format(round($items->sum('precio_empleado'), 2), 0, ',', '.'));
                $filaTemp++;
            }
            $this->aplicarBordesTabla($hoja, 'A3:E' . ($filaTemp - 1));
            $hoja->getStyle('D4:E' . ($filaTemp - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $hoja->getColumnDimension('A')->setWidth(16);
            $hoja->getColumnDimension('B')->setWidth(28);
            $hoja->getColumnDimension('C')->setWidth(30);
            $hoja->getColumnDimension('D')->setWidth(20);
            $hoja->getColumnDimension('E')->setWidth(16);
        }

        $this->agregarHojaData($spreadsheet, $consumos);
        return $this->descargarExcel($spreadsheet, 'reporte-nomina-' . $fechaDesde->format('Y-m-d') . '-' . $fechaHasta->format('Y-m-d'));
    }

    /**
     * Informe por Colaborador: Excel individual con consumos y total.
     */
    public function exportarExcelInformeColaborador(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $request->validate([
            'fecha_desde' => ['required', 'date'],
            'fecha_hasta' => ['required', 'date', 'after_or_equal:fecha_desde'],
            'busqueda_persona' => ['required', 'string', 'min:1'],
        ]);

        $consumos = $this->obtenerConsumosFiltrados($request);

        if ($consumos->isEmpty()) {
            return back()->with('error', 'No se encontraron consumos para el criterio de búsqueda. Ingrese documento o nombre de la persona.');
        }

        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        $spreadsheet = new Spreadsheet();
        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setTitle('Informe por Colaborador');

        $hoja->mergeCells('A1:F1');
        $hoja->setCellValue('A1', 'Informe por Colaborador');
        $this->aplicarEstiloTitulo($hoja, 'A1:F1');
        $hoja->setCellValue('A2', 'Período: ' . Carbon::parse($fechaDesde)->format('d/m/Y') . ' - ' . Carbon::parse($fechaHasta)->format('d/m/Y'));
        $hoja->mergeCells('A2:F2');
        $hoja->getStyle('A2:F2')->getFont()->setSize(10)->getColor()->setRGB('4B5563');
        $hoja->setCellValue('A4', 'Fecha');
        $hoja->setCellValue('B4', 'Documento');
        $hoja->setCellValue('C4', 'Nombre');
        $hoja->setCellValue('D4', 'Empresa');
        $hoja->setCellValue('E4', 'Casino');
        $hoja->setCellValue('F4', 'Tipo de empleado');
        $this->aplicarEstiloEncabezadoTabla($hoja, 'A4:F4');

        $fila = 5;
        foreach ($consumos as $c) {
            $nombre = $c->nombres_consumidor ?? $c->usuario?->nombres ?? $c->visitante?->nombre ?? '—';
            $hoja->setCellValue('A' . $fila, $c->fecha_consumo?->format('d/m/Y'));
            $hoja->setCellValue('B' . $fila, $c->documento ?? $c->usuario?->documento ?? $c->visitante?->documento ?? '—');
            $hoja->setCellValue('C' . $fila, $nombre);
            $hoja->setCellValue('D' . $fila, $c->empresa_nombre ?? $c->empresa?->nombre ?? '—');
            $hoja->setCellValue('E' . $fila, $c->casino_nombre ?? $c->casino?->nombre ?? '—');
            $hoja->setCellValue('F' . $fila, $c->tipo_usuario_nombre ?? '—');
            $fila++;
        }

        $valorTotal = round($consumos->sum('precio_empleado'), 2);
        $fila += 2;
        $hoja->mergeCells('A' . $fila . ':F' . $fila);
        $hoja->setCellValue('A' . $fila, 'Total consumido: $' . number_format($valorTotal, 0, ',', '.'));
        $hoja->getStyle('A' . $fila)->getFont()->setBold(true)->setSize(12);
        $hoja->getStyle('A' . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('E5E7EB'));
        $hoja->getStyle('A' . $fila)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $hoja->getStyle('A' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $ultimaFilaDatos = $fila - 3;
        if ($ultimaFilaDatos >= 5) {
            $this->aplicarBordesTabla($hoja, 'A4:F' . $ultimaFilaDatos);
        }
        $hoja->getColumnDimension('A')->setWidth(12);
        $hoja->getColumnDimension('B')->setWidth(16);
        $hoja->getColumnDimension('C')->setWidth(28);
        $hoja->getColumnDimension('D')->setWidth(28);
        $hoja->getColumnDimension('E')->setWidth(22);
        $hoja->getColumnDimension('F')->setWidth(18);

        $this->agregarHojaData($spreadsheet, $consumos);
        $nombreArchivo = 'informe-colaborador-' . $fechaDesde . '-' . $fechaHasta . '.xlsx';
        return $this->descargarExcel($spreadsheet, str_replace('.xlsx', '', $nombreArchivo));
    }

    /**
     * Reporte Temporales: Excel con consumos de tipo_usuario_nombre = 'TEMPORAL'.
     */
    public function exportarExcelTemporales(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $request->validate([
            'fecha_desde' => ['required', 'date'],
            'fecha_hasta' => ['required', 'date', 'after_or_equal:fecha_desde'],
        ]);

        $consumos = $this->obtenerConsumosFiltrados($request);
        $consumosTemporales = $consumos->filter(fn ($c) => strtoupper(trim((string) ($c->tipo_usuario_nombre ?? ''))) === 'TEMPORAL');

        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        $spreadsheet = new Spreadsheet();
        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setTitle('Reporte Temporales');

        $hoja->mergeCells('A1:F1');
        $hoja->setCellValue('A1', 'Reporte Temporales');
        $this->aplicarEstiloTitulo($hoja, 'A1:F1');
        $hoja->setCellValue('A2', 'Período: ' . Carbon::parse($fechaDesde)->format('d/m/Y') . ' - ' . Carbon::parse($fechaHasta)->format('d/m/Y'));
        $hoja->mergeCells('A2:F2');
        $hoja->getStyle('A2:F2')->getFont()->setSize(10)->getColor()->setRGB('4B5563');
        $hoja->setCellValue('A4', 'Documento');
        $hoja->setCellValue('B4', 'Nombre');
        $hoja->setCellValue('C4', 'Empresa');
        $hoja->setCellValue('D4', 'Casino');
        $hoja->setCellValue('E4', 'Cantidad de vales');
        $hoja->setCellValue('F4', 'Total');
        $this->aplicarEstiloEncabezadoTabla($hoja, 'A4:F4');

        $sep = "\0";
        $porPersonaEmpresaCasino = $consumosTemporales->groupBy(function ($c) use ($sep) {
            $doc = $c->documento ?? $c->usuario?->documento ?? $c->visitante?->documento ?? '—';
            $emp = $c->empresa_nombre ?? $c->empresa?->nombre ?? '—';
            $cas = $c->casino_nombre ?? $c->casino?->nombre ?? '—';
            return $doc . $sep . $emp . $sep . $cas;
        });

        $fila = 5;
        foreach ($porPersonaEmpresaCasino as $grupo => $items) {
            $partes = explode($sep, $grupo, 3);
            $doc = $partes[0] ?? '—';
            $emp = $partes[1] ?? '—';
            $cas = $partes[2] ?? '—';
            $nombre = $items->first()->nombres_consumidor ?? $items->first()->usuario?->nombres ?? $items->first()->visitante?->nombre ?? '—';
            $cantidad = $items->count();
            $totalGrupo = round($items->sum('precio_empleado'), 2);
            $hoja->setCellValue('A' . $fila, $doc);
            $hoja->setCellValue('B' . $fila, $nombre);
            $hoja->setCellValue('C' . $fila, $emp);
            $hoja->setCellValue('D' . $fila, $cas);
            $hoja->setCellValue('E' . $fila, $cantidad);
            $hoja->setCellValue('F' . $fila, '$' . number_format($totalGrupo, 0, ',', '.'));
            $fila++;
        }

        $valorTotal = round($consumosTemporales->sum('precio_empleado'), 2);
        $ultimaFilaDatos = $fila - 1;
        $fila += 2;
        $hoja->mergeCells('A' . $fila . ':F' . $fila);
        $hoja->setCellValue('A' . $fila, 'Total consumido: $' . number_format($valorTotal, 0, ',', '.'));
        $hoja->getStyle('A' . $fila)->getFont()->setBold(true)->setSize(12);
        $hoja->getStyle('A' . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('E5E7EB'));
        $hoja->getStyle('A' . $fila)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $hoja->getStyle('A' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        if ($ultimaFilaDatos >= 5) {
            $this->aplicarBordesTabla($hoja, 'A4:F' . $ultimaFilaDatos);
            $hoja->getStyle('E5:F' . $ultimaFilaDatos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $hoja->getColumnDimension('A')->setWidth(16);
        $hoja->getColumnDimension('B')->setWidth(28);
        $hoja->getColumnDimension('C')->setWidth(28);
        $hoja->getColumnDimension('D')->setWidth(22);
        $hoja->getColumnDimension('E')->setWidth(18);
        $hoja->getColumnDimension('F')->setWidth(14);

        $this->agregarHojaData($spreadsheet, $consumos);
        $nombreArchivo = 'reporte-temporales-' . $fechaDesde . '-' . $fechaHasta . '.xlsx';
        return $this->descargarExcel($spreadsheet, str_replace('.xlsx', '', $nombreArchivo));
    }

    /**
     * Inserta la hoja "Data" como primera hoja con todos los registros del rango de fechas.
     * Columnas: Fecha, documento, nombre, empresa, casino, tipo_usuario, precioempleado.
     */
    private function agregarHojaData(Spreadsheet $spreadsheet, \Illuminate\Support\Collection $consumos): void
    {
        $hojaData = $spreadsheet->createSheet(0);
        $hojaData->setTitle('Data');

        $hojaData->setCellValue('A1', 'Fecha');
        $hojaData->setCellValue('B1', 'Documento');
        $hojaData->setCellValue('C1', 'Nombre');
        $hojaData->setCellValue('D1', 'Empresa');
        $hojaData->setCellValue('E1', 'Casino');
        $hojaData->setCellValue('F1', 'Tipo usuario');
        $hojaData->setCellValue('G1', 'Precio empleado');
        $this->aplicarEstiloEncabezadoTabla($hojaData, 'A1:G1');

        $fila = 2;
        foreach ($consumos as $c) {
            $nombre = $c->nombres_consumidor ?? $c->usuario?->nombres ?? $c->visitante?->nombre ?? '—';
            $precio = round((float) ($c->precio_empleado ?? 0), 2);
            $hojaData->setCellValue('A' . $fila, $c->fecha_consumo?->format('d/m/Y'));
            $hojaData->setCellValue('B' . $fila, $c->documento ?? $c->usuario?->documento ?? $c->visitante?->documento ?? '—');
            $hojaData->setCellValue('C' . $fila, $nombre);
            $hojaData->setCellValue('D' . $fila, $c->empresa_nombre ?? $c->empresa?->nombre ?? '—');
            $hojaData->setCellValue('E' . $fila, $c->casino_nombre ?? $c->casino?->nombre ?? '—');
            $tipoUsuario = $c->usuario?->tipoUsuario?->nombre ?? $c->tipo_usuario_nombre ?? '—';
            $hojaData->setCellValue('F' . $fila, $tipoUsuario);
            $hojaData->setCellValue('G' . $fila, $precio);
            $fila++;
        }

        $ultimaFila = $fila - 1;
        if ($ultimaFila >= 2) {
            $this->aplicarBordesTabla($hojaData, 'A1:G' . $ultimaFila);
            $hojaData->getStyle('G2:G' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $hojaData->getColumnDimension('A')->setWidth(12);
        $hojaData->getColumnDimension('B')->setWidth(16);
        $hojaData->getColumnDimension('C')->setWidth(28);
        $hojaData->getColumnDimension('D')->setWidth(28);
        $hojaData->getColumnDimension('E')->setWidth(22);
        $hojaData->getColumnDimension('F')->setWidth(16);
        $hojaData->getColumnDimension('G')->setWidth(14);
    }

    private function sanitizarNombreHoja(string $nombre): string
    {
        $nombre = preg_replace('/[\*\?\:\[\]\/\\\\]/', '', $nombre);
        return mb_substr($nombre, 0, 31);
    }

    private function aplicarEstiloTitulo($hoja, string $rango): void
    {
        $hoja->getStyle($rango)->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('1E293B');
        $hoja->getStyle($rango)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $hoja->getStyle($rango)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('F1F5F9'));
        $hoja->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $hoja->getRowDimension(1)->setRowHeight(28);
    }

    private function aplicarEstiloEncabezadoTabla($hoja, string $rango): void
    {
        $hoja->getStyle($rango)->getFont()->setBold(true)->setSize(11)->getColor()->setRGB('FFFFFF');
        $hoja->getStyle($rango)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('475569'));
        $hoja->getStyle($rango)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        $hoja->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    private function aplicarBordesTabla($hoja, string $rango): void
    {
        $hoja->getStyle($rango)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $hoja->getStyle($rango)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM);
        $hoja->getStyle($rango)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function descargarExcel(Spreadsheet $spreadsheet, string $nombreBase): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $tempPath = storage_path('app/temp/' . $nombreBase . '-' . uniqid() . '.xlsx');
        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);
        $response = response()->download($tempPath, $nombreBase . '.xlsx')->deleteFileAfterSend(true);
        return $response;
    }

    private function obtenerConsumosFiltrados(Request $request): \Illuminate\Support\Collection
    {
        $user = $request->user();
        $empresasPermitidas = $user->empresasAcceso()->get()->pluck('id_empresa')->toArray();

        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $empresasSeleccionadas = $request->input('empresas', []);
        if (! is_array($empresasSeleccionadas)) {
            $empresasSeleccionadas = $empresasSeleccionadas ? [$empresasSeleccionadas] : [];
        }
        $empresasSeleccionadas = array_values(array_filter(array_map('intval', $empresasSeleccionadas)));
        $busquedaPersona = trim((string) $request->input('busqueda_persona', ''));

        $query = RegistroConsumo::query()
            ->whereBetween('fecha_consumo', [$fechaDesde, $fechaHasta])
            ->with(['usuario.tipoUsuario', 'visitante', 'empresa', 'casino', 'horarioConsumo'])
            ->orderBy('fecha_consumo')
            ->orderBy('id_empresa')
            ->orderBy('hora_consumo');

        if (count($empresasPermitidas) > 0) {
            $query->whereIn('id_empresa', $empresasPermitidas);
        } elseif ($user->role === 'gestionhumana') {
            $query->where('id_empresa', $user->id_empresa);
        }
        if (! empty($empresasSeleccionadas)) {
            $query->whereIn('id_empresa', $empresasSeleccionadas);
        }
        if ($busquedaPersona !== '') {
            $term = '%' . $busquedaPersona . '%';
            $query->where(function ($q) use ($term) {
                $q->where('documento', 'like', $term)
                    ->orWhere('nombres_consumidor', 'like', $term)
                    ->orWhereHas('usuario', fn ($uq) => $uq->where('documento', 'like', $term)->orWhere('nombres', 'like', $term))
                    ->orWhereHas('visitante', fn ($vq) => $vq->where('documento', 'like', $term)->orWhere('nombre', 'like', $term));
            });
        }

        return $query->get();
    }
}
