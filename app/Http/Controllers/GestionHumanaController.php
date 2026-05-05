<?php

namespace App\Http\Controllers;

use App\Models\Casino;
use App\Models\Empresa;
use App\Models\RegistroConsumo;
use App\Models\Sede;
use App\Models\Usuario;
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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GestionHumanaController extends Controller
{
    /**
     * Reportes: filtro por fechas y sedes (casinos de esas sedes). Resúmenes por empresa y por casino.
     * Sin sedes marcadas: aplica el alcance por empresa del usuario (igual que antes, sin filtro por sede).
     */
    public function index(Request $request): View
    {
        /** @var Usuario $user */
        $user = $request->user();
        $empresasPermitidas = $user->empresasAcceso()->get()->pluck('id_empresa')->toArray();

        $sedes = $this->sedesDisponiblesParaFiltro($user, $empresasPermitidas);

        $fechaDesde = $request->input('fecha_desde', Carbon::today()->subDays(7)->toDateString());
        $fechaHasta = $request->input('fecha_hasta', Carbon::today()->toDateString());
        $sedesInputRaw = $request->input('sedes', []);
        if (! is_array($sedesInputRaw)) {
            $sedesInputRaw = $sedesInputRaw !== null && $sedesInputRaw !== '' ? [$sedesInputRaw] : [];
        }
        $sedesSeleccionadas = $this->expandirSedesSeleccionadas($sedesInputRaw, $sedes);
        $sedesMarcadasFormulario = $this->valoresCheckboxSedeRequest($request->old('sedes', $sedesInputRaw));
        $opcionesFiltroSede = $this->opcionesFiltroSedeAgrupadas($sedes);
        $busquedaPersona = trim((string) $request->input('busqueda_persona', ''));

        $query = RegistroConsumo::query()
            ->whereBetween('fecha_consumo', [$fechaDesde, $fechaHasta])
            ->with(['usuario', 'visitante', 'empresa', 'casino', 'horarioConsumo'])
            ->orderBy('fecha_consumo')
            ->orderBy('id_casino')
            ->orderBy('id_empresa')
            ->orderBy('hora_consumo');

        $this->aplicarAlcanceReporte($query, $user, $empresasPermitidas, $sedesSeleccionadas);

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

        $porCasino = $consumos->groupBy('id_casino')->map(function ($items) {
            $casino = $items->first()->casino;
            return [
                'casino' => $casino,
                'nombre' => $casino?->nombre ?? 'Sin casino',
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
            'sedes_opciones_filtro' => $opcionesFiltroSede,
            'sedes_marcadas_formulario' => $sedesMarcadasFormulario,
            'consumos' => $consumos,
            'porEmpresa' => $porEmpresa,
            'porCasino' => $porCasino,
            'totales' => $totales,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
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

        /** @var Usuario $user */
        $user = $request->user();
        $empresasPermitidas = $user->empresasAcceso()->get()->pluck('id_empresa')->toArray();

        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $sedesDisponibles = $this->sedesDisponiblesParaFiltro($user, $empresasPermitidas);
        $sedesInputRaw = $request->input('sedes', []);
        if (! is_array($sedesInputRaw)) {
            $sedesInputRaw = $sedesInputRaw !== null && $sedesInputRaw !== '' ? [$sedesInputRaw] : [];
        }
        $sedesSeleccionadas = $this->expandirSedesSeleccionadas($sedesInputRaw, $sedesDisponibles);
        $busquedaPersona = trim((string) $request->input('busqueda_persona', ''));

        $query = RegistroConsumo::query()
            ->whereBetween('fecha_consumo', [$fechaDesde, $fechaHasta])
            ->with(['usuario', 'visitante', 'empresa', 'casino', 'horarioConsumo'])
            ->orderBy('id_casino')
            ->orderBy('id_empresa')
            ->orderBy('fecha_consumo')
            ->orderBy('hora_consumo');

        $this->aplicarAlcanceReporte($query, $user, $empresasPermitidas, $sedesSeleccionadas);
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

        /** @var Usuario $user */
        $user = $request->user();
        $empresasPermitidas = $user->empresasAcceso()->get()->pluck('id_empresa')->toArray();

        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $sedesDisponibles = $this->sedesDisponiblesParaFiltro($user, $empresasPermitidas);
        $sedesInputRaw = $request->input('sedes', []);
        if (! is_array($sedesInputRaw)) {
            $sedesInputRaw = $sedesInputRaw !== null && $sedesInputRaw !== '' ? [$sedesInputRaw] : [];
        }
        $sedesSeleccionadas = $this->expandirSedesSeleccionadas($sedesInputRaw, $sedesDisponibles);
        $busquedaPersona = trim((string) $request->input('busqueda_persona', ''));

        $query = RegistroConsumo::query()
            ->whereBetween('fecha_consumo', [$fechaDesde, $fechaHasta])
            ->with(['usuario', 'visitante', 'empresa', 'casino', 'horarioConsumo'])
            ->orderBy('id_casino')
            ->orderBy('id_empresa')
            ->orderBy('fecha_consumo')
            ->orderBy('hora_consumo');

        $this->aplicarAlcanceReporte($query, $user, $empresasPermitidas, $sedesSeleccionadas);
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

        // Hoja 1: Data (primera hoja con todos los registros)
        $this->agregarHojaData($spreadsheet, $consumos);
        $spreadsheet->removeSheetByIndex(1); // elimina la hoja por defecto que quedó vacía

        // Hoja 2: Resumen de la quincena (empresas en filas, días en columnas)
        $hojaResumen = $spreadsheet->createSheet();
        $hojaResumen->setTitle('Resumen de la quincena');

        $fechasPeriodo = iterator_to_array($periodo);
        $numDias = count($fechasPeriodo);
        $colUltima = Coordinate::stringFromColumnIndex(2 + $numDias);
        $hojaResumen->mergeCells('A1:' . $colUltima . '1');
        $hojaResumen->setCellValue('A1', 'Reporte de Nómina - Resumen de la quincena');
        $this->aplicarEstiloTitulo($hojaResumen, 'A1:' . $colUltima . '1');
        $hojaResumen->setCellValue('A2', 'Período: ' . $fechaDesde->format('d/m/Y') . ' - ' . $fechaHasta->format('d/m/Y'));
        $hojaResumen->mergeCells('A2:' . $colUltima . '2');
        $hojaResumen->getStyle('A2:' . $colUltima . '2')->getFont()->setSize(10)->getColor()->setRGB('4B5563');

        $fila = 4;
        $porCasinoResumen = $consumos->groupBy('id_casino')->sortBy(function (\Illuminate\Support\Collection $itemsCas) {
            $p = $itemsCas->first();

            return mb_strtolower(trim((string) ($p->casino_nombre ?? $p->casino?->nombre ?? 'Sin casino')));
        });
        foreach ($porCasinoResumen as $itemsCasinoRes) {
            $primRes = $itemsCasinoRes->first();
            $nomCasinoRes = trim((string) ($primRes->casino_nombre ?? $primRes->casino?->nombre ?? 'Sin casino'));
            $fila = $this->agregarTablaResumenQuincenaUnCasino(
                $hojaResumen,
                $itemsCasinoRes,
                $nomCasinoRes,
                $fechasPeriodo,
                $colUltima,
                $fila
            );
        }

        $hojaResumen->getColumnDimension('A')->setWidth(42);
        for ($c = 2; $c <= 2 + $numDias; $c++) {
            $hojaResumen->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setWidth(10);
        }

        /** @var Usuario $userNom */
        $userNom = $request->user();
        $empPermNom = $userNom->empresasAcceso()->get()->pluck('id_empresa')->toArray();
        $sedesDispNom = $this->sedesDisponiblesParaFiltro($userNom, $empPermNom);
        $sedesRawNom = $request->input('sedes', []);
        if (! is_array($sedesRawNom)) {
            $sedesRawNom = $sedesRawNom !== null && $sedesRawNom !== '' ? [$sedesRawNom] : [];
        }
        $sedesSelNom = $this->expandirSedesSeleccionadas($sedesRawNom, $sedesDispNom);
        if ($this->nominaDisparadorLayoutIbcActivo($sedesSelNom)) {
            $idEmpIbc = $this->resolverIdEmpresaPrincipalNominaIbc();
            if ($idEmpIbc !== null) {
                $nombreEmpIbc = (string) (Empresa::query()->whereKey($idEmpIbc)->value('nombre') ?? 'IBC');
                $this->agregarHojasNominaLayoutIbc($spreadsheet, $consumos, $idEmpIbc, $nombreEmpIbc, $fechaDesde, $fechaHasta);
            }
        }

        $invitados = $consumos->filter(fn (RegistroConsumo $c) => $this->esInvitadoNomina($c));
        if ($invitados->isNotEmpty()) {
            $titulosHojaUsadosInv = [];
            foreach ($spreadsheet->getAllSheets() as $wsInv) {
                $titulosHojaUsadosInv[$wsInv->getTitle()] = true;
            }
            $hojaInv = $spreadsheet->createSheet();
            $hojaInv->setTitle($this->tituloHojaNominaUnico('INVITADOS', $titulosHojaUsadosInv));
            $hojaInv->mergeCells('A1:D1');
            $hojaInv->setCellValue('A1', 'INVITADOS');
            $this->aplicarEstiloTitulo($hojaInv, 'A1:D1');
            $filaInv = 3;
            foreach ($invitados->groupBy('id_casino')->sortKeys() as $itemsCasinoInv) {
                $primInv = $itemsCasinoInv->first();
                $nomCasInv = trim((string) ($primInv->casino_nombre ?? $primInv->casino?->nombre ?? 'Sin casino'));
                $filaInv = $this->agregarBloqueTablaNominaDocumentos($hojaInv, $filaInv, 'INVITADOS — ' . $nomCasInv, $itemsCasinoInv);
                $filaInv += 2;
            }
            $this->ajustarAnchoColumnasNominaDetalle($hojaInv);
        }

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
     * Columnas: Fecha, documento, nombre, empresa, casino, tipo_usuario, Empresa_temporal, empresa_contratista, precio empleado.
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
        $hojaData->setCellValue('G1', 'Empresa_temporal');
        $hojaData->setCellValue('H1', 'empresa_contratista');
        $hojaData->setCellValue('I1', 'Precio empleado');
        $this->aplicarEstiloEncabezadoTabla($hojaData, 'A1:I1');

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
            $hojaData->setCellValue('G' . $fila, $c->empresa_temporal_nombre ?? '—');
            $hojaData->setCellValue('H' . $fila, $c->empresa_contratista_nombre ?? '—');
            $hojaData->setCellValue('I' . $fila, $precio);
            $fila++;
        }

        $ultimaFila = $fila - 1;
        if ($ultimaFila >= 2) {
            $this->aplicarBordesTabla($hojaData, 'A1:I' . $ultimaFila);
            $hojaData->getStyle('I2:I' . $ultimaFila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }
        $hojaData->getColumnDimension('A')->setWidth(12);
        $hojaData->getColumnDimension('B')->setWidth(16);
        $hojaData->getColumnDimension('C')->setWidth(28);
        $hojaData->getColumnDimension('D')->setWidth(28);
        $hojaData->getColumnDimension('E')->setWidth(22);
        $hojaData->getColumnDimension('F')->setWidth(16);
        $hojaData->getColumnDimension('G')->setWidth(22);
        $hojaData->getColumnDimension('H')->setWidth(22);
        $hojaData->getColumnDimension('I')->setWidth(14);
    }

    /**
     * Clave interna para agrupar el resumen: empleados por id_empresa, temporales por nombre de empresa temporal, contratistas por nombre de contratista.
     */
    private function claveAgrupacionResumenQuincena(RegistroConsumo $c): string
    {
        $tipoNom = strtoupper(trim((string) ($c->tipo_usuario_nombre ?? $c->usuario?->tipoUsuario?->nombre ?? '')));
        if ($tipoNom === 'TEMPORAL') {
            $nom = trim((string) ($c->empresa_temporal_nombre ?? ''));

            return 'T|' . ($nom !== '' ? $nom : '— Sin empresa temporal');
        }
        if ($tipoNom === 'CONTRATISTA') {
            $nom = trim((string) ($c->empresa_contratista_nombre ?? ''));

            return 'C|' . ($nom !== '' ? $nom : '— Sin empresa contratista');
        }

        $idEf = $this->idEmpresaEfectivaResumenQuincena($c);
        if ($idEf === 0) {
            $nom = mb_strtolower(trim((string) ($c->empresa_nombre ?? $c->empresa?->nombre ?? '')));

            return 'E|0|' . ($nom !== '' ? $nom : 'sin');
        }

        return 'E|' . $idEf;
    }

    /**
     * Tipo de colaborador para nómina: si hay usuario vinculado, usar su tipoUsuario (fuente de verdad);
     * si no, el nombre snapshot en el registro (visitantes / legado).
     */
    private function tipoUsuarioNominaEfectivo(RegistroConsumo $c): string
    {
        if ($c->id_usuario && $c->usuario?->tipoUsuario) {
            $nom = $c->usuario->tipoUsuario->nombre;

            return strtoupper(trim((string) $nom));
        }

        return strtoupper(trim((string) ($c->tipo_usuario_nombre ?? '')));
    }

    /**
     * Consumos de invitados / visitantes para la hoja INVITADOS del layout de nómina.
     */
    private function esInvitadoNomina(RegistroConsumo $c): bool
    {
        if ($this->tipoUsuarioNominaEfectivo($c) === 'INVITADO') {
            return true;
        }
        if ($c->id_visitante && ! $c->id_usuario) {
            return true;
        }
        $snap = strtoupper(trim((string) ($c->tipo_usuario_nombre ?? '')));

        return in_array($snap, ['VISITANTE', 'INVITADO'], true);
    }

    /**
     * Empresa empleadora para el resumen: la del usuario (colaborador), no solo la del registro.
     * Así los consumos de quien trabaja en otra empresa (p. ej. QBC) no se mezclan con la empresa del casino o del id_empresa mal cargado en el registro.
     */
    private function idEmpresaEfectivaResumenQuincena(RegistroConsumo $c): int
    {
        if ($c->id_usuario && $c->usuario !== null) {
            $uid = $c->usuario->getAttribute('id_empresa');
            if ($uid !== null && (int) $uid !== 0) {
                return (int) $uid;
            }
        }

        return (int) ($c->id_empresa ?? 0);
    }

    private function nombreEmpresaEfectivaResumenQuincena(RegistroConsumo $c): string
    {
        if ($c->id_usuario && $c->usuario !== null) {
            $nom = $c->usuario->empresa?->nombre;
            if (is_string($nom) && trim($nom) !== '') {
                return trim($nom);
            }
        }

        $idEf = $this->idEmpresaEfectivaResumenQuincena($c);
        if ($idEf !== 0 && $c->relationLoaded('empresa') && $c->empresa !== null && (int) $c->empresa->getKey() === $idEf) {
            $nom = $c->empresa->nombre;
            if (is_string($nom) && trim($nom) !== '') {
                return trim($nom);
            }
        }

        $snap = trim((string) ($c->empresa_nombre ?? ''));
        if ($snap !== '') {
            return $snap;
        }

        $nom = $c->empresa?->nombre;
        if (is_string($nom) && trim($nom) !== '') {
            return trim($nom);
        }

        return 'Sin empresa';
    }

    /**
     * Texto de la primera columna en "Resumen de la quincena".
     */
    private function etiquetaFilaResumenQuincena(RegistroConsumo $c): string
    {
        $tipoNom = strtoupper(trim((string) ($c->tipo_usuario_nombre ?? $c->usuario?->tipoUsuario?->nombre ?? '')));
        if ($tipoNom === 'TEMPORAL') {
            $nom = trim((string) ($c->empresa_temporal_nombre ?? ''));

            return 'Temporal — ' . ($nom !== '' ? $nom : 'Sin empresa temporal');
        }
        if ($tipoNom === 'CONTRATISTA') {
            $nom = trim((string) ($c->empresa_contratista_nombre ?? ''));

            return 'Contratista — ' . ($nom !== '' ? $nom : 'Sin empresa contratista');
        }

        return $this->nombreEmpresaEfectivaResumenQuincena($c);
    }

    /**
     * Orden de bloques en el resumen: empresas, luego temporales, luego contratistas.
     */
    private function ordenCategoriaResumenQuincena(RegistroConsumo $c): int
    {
        $tipoNom = strtoupper(trim((string) ($c->tipo_usuario_nombre ?? $c->usuario?->tipoUsuario?->nombre ?? '')));
        if ($tipoNom === 'TEMPORAL') {
            return 1;
        }
        if ($tipoNom === 'CONTRATISTA') {
            return 2;
        }

        return 0;
    }

    /**
     * Una tabla de resumen (empresa × días) solo con consumos de un restaurante; deja filas en blanco debajo para separar del siguiente bloque.
     *
     * @param  array<int, \Carbon\Carbon>  $fechasPeriodo
     * @return int siguiente fila para otro bloque
     */
    private function agregarTablaResumenQuincenaUnCasino(
        Worksheet $hoja,
        \Illuminate\Support\Collection $consumosCasino,
        string $nombreRestaurante,
        array $fechasPeriodo,
        string $colUltima,
        int $filaInicio
    ): int {
        $hoja->mergeCells('A' . $filaInicio . ':' . $colUltima . $filaInicio);
        $hoja->setCellValue('A' . $filaInicio, 'Restaurante: ' . $nombreRestaurante);
        $hoja->getStyle('A' . $filaInicio)->getFont()->setBold(true)->setSize(12);
        $hoja->getStyle('A' . $filaInicio)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);

        $porClaveResumen = $consumosCasino->groupBy(fn (RegistroConsumo $c) => $this->claveAgrupacionResumenQuincena($c));

        $filasOrdenadas = $porClaveResumen->map(function (\Illuminate\Support\Collection $items, string $clave) {
            $primero = $items->first();

            return [
                'clave' => $clave,
                'etiqueta' => $this->etiquetaFilaResumenQuincena($primero),
                'orden' => $this->ordenCategoriaResumenQuincena($primero),
            ];
        })->values()->sortBy(fn (array $row) => sprintf('%d-%s', $row['orden'], mb_strtolower($row['etiqueta'])))->values()->all();

        $matriz = [];
        foreach ($porClaveResumen as $clave => $itemsGrupo) {
            $porFecha = $itemsGrupo->groupBy(fn ($c) => $c->fecha_consumo?->format('Y-m-d') ?? '');
            $matriz[$clave] = [];
            foreach ($fechasPeriodo as $fecha) {
                $fechaStr = $fecha->format('Y-m-d');
                $matriz[$clave][$fechaStr] = ($porFecha->get($fechaStr) ?? collect())->count();
            }
        }

        $fila = $filaInicio + 2;
        $hoja->setCellValue('A' . $fila, 'Empresa');
        $col = 1;
        foreach ($fechasPeriodo as $fecha) {
            $col++;
            $letraCol = Coordinate::stringFromColumnIndex($col);
            $hoja->setCellValue($letraCol . $fila, $fecha->format('d/m'));
        }
        $col++;
        $letraTotalCalculada = Coordinate::stringFromColumnIndex($col);
        $hoja->setCellValue($letraTotalCalculada . $fila, 'Total');
        $this->aplicarEstiloEncabezadoTabla($hoja, 'A' . $fila . ':' . $letraTotalCalculada . $fila);
        $filaEncabezado = $fila;
        $fila++;
        $filaPrimeraDato = $fila;

        foreach ($filasOrdenadas as $meta) {
            $clave = $meta['clave'];
            $hoja->setCellValue('A' . $fila, $meta['etiqueta']);
            $totalFila = 0;
            $col = 1;
            foreach ($fechasPeriodo as $fecha) {
                $col++;
                $fechaStr = $fecha->format('Y-m-d');
                $cantidad = $matriz[$clave][$fechaStr] ?? 0;
                $totalFila += $cantidad;
                $letraCol = Coordinate::stringFromColumnIndex($col);
                $hoja->setCellValue($letraCol . $fila, $cantidad);
            }
            $col++;
            $letraColTotal = Coordinate::stringFromColumnIndex($col);
            $hoja->setCellValue($letraColTotal . $fila, $totalFila);
            $fila++;
        }

        $ultimaFilaDatos = $fila - 1;
        if ($ultimaFilaDatos >= $filaPrimeraDato) {
            $this->aplicarBordesTabla($hoja, 'A' . $filaEncabezado . ':' . $letraTotalCalculada . $ultimaFilaDatos);
            $hoja->getStyle('B' . $filaPrimeraDato . ':' . $letraTotalCalculada . $ultimaFilaDatos)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        } elseif ($filasOrdenadas === []) {
            $this->aplicarBordesTabla($hoja, 'A' . $filaEncabezado . ':' . $letraTotalCalculada . $filaEncabezado);
        }

        return $fila + 2;
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

    private function nominaDisparadorLayoutIbcActivo(array $sedesSeleccionadasIds): bool
    {
        if ($sedesSeleccionadasIds === []) {
            return false;
        }
        $triggers = config('reportes.nomina_layout_ibc.sedes_nombres_disparador', []);
        if (! is_array($triggers) || $triggers === []) {
            return false;
        }
        $needles = array_map(fn ($n) => mb_strtoupper(trim((string) $n)), $triggers);
        $sedes = Sede::query()->whereIn('id_sede', $sedesSeleccionadasIds)->get(['id_sede', 'nombre']);
        foreach ($sedes as $sede) {
            if (in_array(mb_strtoupper(trim((string) $sede->nombre)), $needles, true)) {
                return true;
            }
        }

        return false;
    }

    private function resolverIdEmpresaPrincipalNominaIbc(): ?int
    {
        $nombres = config('reportes.nomina_layout_ibc.empresa_principal_nombres', ['IBC']);
        if (! is_array($nombres)) {
            return null;
        }
        foreach ($nombres as $nom) {
            $row = Empresa::query()
                ->whereRaw('UPPER(TRIM(nombre)) = ?', [mb_strtoupper(trim((string) $nom))])
                ->first();
            if ($row !== null) {
                return (int) $row->id_empresa;
            }
        }

        return null;
    }

    /**
     * Hojas adicionales del layout IBC (después de Data + Resumen), según filtro sede configurado.
     */
    private function agregarHojasNominaLayoutIbc(
        Spreadsheet $spreadsheet,
        \Illuminate\Support\Collection $consumos,
        int $idEmpresaIbc,
        string $nombreEmpresaIbc,
        Carbon $fechaDesdeNomina,
        Carbon $fechaHastaNomina
    ): void {
        $titulosHojaUsados = [];

        // 1) FIJO y SENA, empresa empleadora IBC — una hoja por casino
        $fijoSenaIbc = $consumos->filter(function (RegistroConsumo $c) use ($idEmpresaIbc) {
            $t = $this->tipoUsuarioNominaEfectivo($c);
            if (! in_array($t, ['FIJO', 'SENA'], true)) {
                return false;
            }

            return $this->idEmpresaEfectivaResumenQuincena($c) === $idEmpresaIbc;
        });

        foreach ($fijoSenaIbc->groupBy('id_casino')->sortKeys() as $itemsCasino) {
            if ($itemsCasino->isEmpty()) {
                continue;
            }
            $prim = $itemsCasino->first();
            $nomCasino = trim((string) ($prim->casino_nombre ?? $prim->casino?->nombre ?? 'Sin casino'));
            $tituloCelda = $nombreEmpresaIbc . ' - ' . $nomCasino;
            $nombreHoja = $this->tituloHojaNominaUnico($tituloCelda, $titulosHojaUsados);
            $hoja = $spreadsheet->createSheet();
            $hoja->setTitle($nombreHoja);
            $hoja->mergeCells('A1:D1');
            $hoja->setCellValue('A1', $tituloCelda);
            $this->aplicarEstiloTitulo($hoja, 'A1:D1');
            $this->agregarBloqueTablaNominaDocumentos($hoja, 3, null, $itemsCasino);
            $this->ajustarAnchoColumnasNominaDetalle($hoja);
        }

        // 2) Contratistas — una hoja por empresa contratista (nombre de hoja = empresa); tablas por casino dentro de la hoja
        $contratistas = $consumos->filter(fn (RegistroConsumo $c) => $this->tipoUsuarioNominaEfectivo($c) === 'CONTRATISTA');
        if ($contratistas->isNotEmpty()) {
            $porEmpresaContr = $contratistas->groupBy(function (RegistroConsumo $c) {
                return mb_strtoupper(trim((string) ($c->empresa_contratista_nombre ?? '')));
            })->sortKeys();
            foreach ($porEmpresaContr as $itemsEmpresaContr) {
                $primContr = $itemsEmpresaContr->first();
                $nomHojaContr = trim((string) ($primContr->empresa_contratista_nombre ?? ''));
                if ($nomHojaContr === '') {
                    $nomHojaContr = 'Sin empresa contratista';
                }
                $hoja = $spreadsheet->createSheet();
                $hoja->setTitle($this->tituloHojaNominaUnico($nomHojaContr, $titulosHojaUsados));
                $fila = 1;
                $gruposContr = $itemsEmpresaContr->groupBy(function (RegistroConsumo $c) {
                    return (string) ($c->id_casino ?? '0');
                })->sortKeys();
                foreach ($gruposContr as $itemsGrupo) {
                    $p = $itemsGrupo->first();
                    $nomCont = trim((string) ($p->empresa_contratista_nombre ?? 'Sin empresa contratista'));
                    $nomCas = trim((string) ($p->casino_nombre ?? $p->casino?->nombre ?? 'Sin casino'));
                    $fila = $this->agregarBloqueTablaNominaDocumentos($hoja, $fila, $nomCont . ' — ' . $nomCas, $itemsGrupo);
                    $fila += 2;
                }
                $fila = $this->agregarDetallePorPersonaContratista($hoja, $fila, $itemsEmpresaContr, $fechaDesdeNomina, $fechaHastaNomina);
                $this->ajustarAnchoColumnasNominaContratista($hoja);
            }
        }

        // 3) Consumos de otras empresas en IBC (empleador distinto de IBC; sin contratistas; temporales IBC van en hoja TEMPORALES; invitados en hoja INVITADOS)
        $otrasEmp = $consumos->filter(function (RegistroConsumo $c) use ($idEmpresaIbc) {
            if ($this->esInvitadoNomina($c)) {
                return false;
            }
            $t = $this->tipoUsuarioNominaEfectivo($c);
            if ($t === 'CONTRATISTA') {
                return false;
            }
            if ($t === 'TEMPORAL' && $this->idEmpresaEfectivaResumenQuincena($c) === $idEmpresaIbc) {
                return false;
            }

            return $this->idEmpresaEfectivaResumenQuincena($c) !== $idEmpresaIbc;
        });
        if ($otrasEmp->isNotEmpty()) {
            $hoja = $spreadsheet->createSheet();
            $hoja->setTitle($this->tituloHojaNominaUnico('Otras empr en IBC', $titulosHojaUsados));
            $hoja->mergeCells('A1:D1');
            $hoja->setCellValue('A1', 'Consumos de otras empresas en IBC');
            $this->aplicarEstiloTitulo($hoja, 'A1:D1');
            $fila = 3;
            foreach ($otrasEmp->groupBy(fn (RegistroConsumo $c) => $this->idEmpresaEfectivaResumenQuincena($c))->sortKeys() as $itemsGrupo) {
                if ($itemsGrupo->isEmpty()) {
                    continue;
                }
                $p = $itemsGrupo->first();
                $nomEmpBloque = $this->nombreEmpresaEfectivaResumenQuincena($p);
                $fila = $this->agregarBloqueTablaNominaDocumentos($hoja, $fila, $nomEmpBloque, $itemsGrupo);
                $fila += 2;
            }
            $this->ajustarAnchoColumnasNominaDetalle($hoja);
        }

        // 4) Temporales con empresa empleadora IBC — una hoja, tabla por restaurante (casino)
        $tempIbc = $consumos->filter(function (RegistroConsumo $c) use ($idEmpresaIbc) {
            if ($this->tipoUsuarioNominaEfectivo($c) !== 'TEMPORAL') {
                return false;
            }

            return $this->idEmpresaEfectivaResumenQuincena($c) === $idEmpresaIbc;
        });
        if ($tempIbc->isNotEmpty()) {
            $hoja = $spreadsheet->createSheet();
            $hoja->setTitle($this->tituloHojaNominaUnico('TEMPORALES', $titulosHojaUsados));
            $hoja->mergeCells('A1:D1');
            $hoja->setCellValue('A1', 'TEMPORALES');
            $this->aplicarEstiloTitulo($hoja, 'A1:D1');
            $fila = 3;
            foreach ($tempIbc->groupBy('id_casino')->sortKeys() as $itemsGrupo) {
                $p = $itemsGrupo->first();
                $nomCas = trim((string) ($p->casino_nombre ?? $p->casino?->nombre ?? 'Sin casino'));
                $fila = $this->agregarBloqueTablaNominaDocumentos($hoja, $fila, 'TEMPORALES — ' . $nomCas, $itemsGrupo);
                $fila += 2;
            }
            $this->ajustarAnchoColumnasNominaDetalle($hoja);
        }
    }

    /**
     * Tabla detallada por colaborador: una subtabla por persona con filas = cada consumo en el rango (fecha, documento, nombre, tipo de comida, precio).
     */
    private function agregarDetallePorPersonaContratista(
        Worksheet $hoja,
        int $filaInicio,
        \Illuminate\Support\Collection $itemsEmpresaContr,
        Carbon $fechaDesdeNomina,
        Carbon $fechaHastaNomina
    ): int {
        $fila = $filaInicio + 2;
        $hoja->mergeCells('A' . $fila . ':E' . $fila);
        $hoja->setCellValue(
            'A' . $fila,
            'Detalle por persona — ' . $fechaDesdeNomina->format('d/m/Y') . ' al ' . $fechaHastaNomina->format('d/m/Y')
        );
        $hoja->getStyle('A' . $fila)->getFont()->setBold(true)->setSize(12);
        $hoja->getStyle('A' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
        $fila += 2;

        $porPersona = $itemsEmpresaContr->groupBy(function (RegistroConsumo $c) {
            return trim((string) ($c->documento ?? $c->usuario?->documento ?? $c->visitante?->documento ?? '—'));
        })->sortKeys();

        foreach ($porPersona as $itemsPersona) {
            $prim = $itemsPersona->first();
            $doc = trim((string) ($prim->documento ?? $prim->usuario?->documento ?? $prim->visitante?->documento ?? '—'));
            $nombre = $prim->nombres_consumidor ?? $prim->usuario?->nombres ?? $prim->visitante?->nombre ?? '—';
            $hoja->mergeCells('A' . $fila . ':E' . $fila);
            $hoja->setCellValue('A' . $fila, $doc . ' — ' . $nombre);
            $hoja->getStyle('A' . $fila)->getFont()->setBold(true);
            $hoja->getStyle('A' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $fila += 2;

            $filaEnc = $fila;
            $hoja->setCellValue('A' . $fila, 'Fecha');
            $hoja->setCellValue('B' . $fila, 'Documento');
            $hoja->setCellValue('C' . $fila, 'Nombre');
            $hoja->setCellValue('D' . $fila, 'Tipo comida');
            $hoja->setCellValue('E' . $fila, 'Precio');
            $this->aplicarEstiloEncabezadoTabla($hoja, 'A' . $fila . ':E' . $fila);
            $fila++;
            $filaDatosIni = $fila;

            $ordenados = $itemsPersona->sortBy(function (RegistroConsumo $c) {
                $fd = $c->fecha_consumo?->format('Y-m-d') ?? '';
                $h = $c->hora_consumo;
                $hs = '';
                if ($h instanceof \DateTimeInterface) {
                    $hs = $h->format('H:i:s');
                } elseif (is_string($h) && $h !== '') {
                    $hs = $h;
                }

                return $fd . ' ' . $hs;
            })->values();

            foreach ($ordenados as $c) {
                $hoja->setCellValue('A' . $fila, $c->fecha_consumo?->format('d/m/Y') ?? '—');
                $hoja->setCellValue('B' . $fila, $doc);
                $hoja->setCellValue('C' . $fila, $nombre);
                $tipoComida = trim((string) ($c->tipo_comida ?? $c->horario_nombre ?? ''));
                $hoja->setCellValue('D' . $fila, $tipoComida !== '' ? $tipoComida : '—');
                $precio = round((float) $c->precio_empleado, 2);
                $hoja->setCellValue('E' . $fila, '$' . number_format($precio, 0, ',', '.'));
                $fila++;
            }
            $filaFin = $fila - 1;
            if ($filaFin >= $filaDatosIni) {
                $this->aplicarBordesTabla($hoja, 'A' . $filaEnc . ':E' . $filaFin);
                $hoja->getStyle('E' . $filaDatosIni . ':E' . $filaFin)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
            $fila += 2;
        }

        return $fila;
    }

    private function agregarBloqueTablaNominaDocumentos(Worksheet $hoja, int $filaInicio, ?string $tituloBloque, \Illuminate\Support\Collection $itemsConsumo): int
    {
        $fila = $filaInicio;
        if ($tituloBloque !== null && $tituloBloque !== '') {
            $hoja->mergeCells('A' . $fila . ':D' . $fila);
            $hoja->setCellValue('A' . $fila, $tituloBloque);
            $hoja->getStyle('A' . $fila)->getFont()->setBold(true)->setSize(12);
            $hoja->getStyle('A' . $fila)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $fila += 2;
        }

        $filaEnc = $fila;
        $hoja->setCellValue('A' . $fila, 'Documento');
        $hoja->setCellValue('B' . $fila, 'Nombre');
        $hoja->setCellValue('C' . $fila, 'Cantidad de vales');
        $hoja->setCellValue('D' . $fila, 'Total');
        $this->aplicarEstiloEncabezadoTabla($hoja, 'A' . $fila . ':D' . $fila);
        $fila++;
        $porDoc = $itemsConsumo->groupBy(function (RegistroConsumo $c) {
            return trim((string) ($c->documento ?? $c->usuario?->documento ?? $c->visitante?->documento ?? '—'));
        });
        $filaDatosIni = $fila;
        foreach ($porDoc->sortKeys() as $doc => $regs) {
            $nombre = $regs->first()->nombres_consumidor ?? $regs->first()->usuario?->nombres ?? $regs->first()->visitante?->nombre ?? '—';
            $hoja->setCellValue('A' . $fila, $doc);
            $hoja->setCellValue('B' . $fila, $nombre);
            $hoja->setCellValue('C' . $fila, $regs->count());
            $tot = round((float) $regs->sum('precio_empleado'), 2);
            $hoja->setCellValue('D' . $fila, '$' . number_format($tot, 0, ',', '.'));
            $fila++;
        }
        $filaFin = $fila - 1;
        if ($filaFin >= $filaDatosIni) {
            $this->aplicarBordesTabla($hoja, 'A' . $filaEnc . ':D' . $filaFin);
            $hoja->getStyle('C' . $filaDatosIni . ':D' . $filaFin)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        return $fila;
    }

    private function ajustarAnchoColumnasNominaDetalle(Worksheet $hoja): void
    {
        $hoja->getColumnDimension('A')->setWidth(18);
        $hoja->getColumnDimension('B')->setWidth(34);
        $hoja->getColumnDimension('C')->setWidth(18);
        $hoja->getColumnDimension('D')->setWidth(16);
    }

    /** Anchos para hoja contratista: resumen (A–D) + detalle por persona (incl. E). */
    private function ajustarAnchoColumnasNominaContratista(Worksheet $hoja): void
    {
        $hoja->getColumnDimension('A')->setWidth(14);
        $hoja->getColumnDimension('B')->setWidth(18);
        $hoja->getColumnDimension('C')->setWidth(30);
        $hoja->getColumnDimension('D')->setWidth(20);
        $hoja->getColumnDimension('E')->setWidth(14);
    }

    /**
     * Títulos de hoja Excel (máx. 31 caracteres, sin caracteres prohibidos).
     *
     * @param  array<string, bool>  $usados
     */
    private function tituloHojaNominaUnico(string $preferido, array &$usados): string
    {
        $base = $this->sanitizarTituloHojaNomina($preferido);
        if ($base === '') {
            $base = 'Hoja';
        }
        $candidato = $base;
        $i = 2;
        while (isset($usados[$candidato])) {
            $suf = ' ' . $i;
            $maxBase = 31 - mb_strlen($suf);
            $candidato = mb_substr($base, 0, $maxBase) . $suf;
            $i++;
        }
        $usados[$candidato] = true;

        return $candidato;
    }

    private function sanitizarTituloHojaNomina(string $nombre): string
    {
        $nombre = preg_replace('/[\*\?\:\[\]\/\\\\]/', '', $nombre);

        return mb_substr(trim($nombre), 0, 31);
    }

    private function obtenerConsumosFiltrados(Request $request): \Illuminate\Support\Collection
    {
        /** @var Usuario $user */
        $user = $request->user();
        $empresasPermitidas = $user->empresasAcceso()->get()->pluck('id_empresa')->toArray();

        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');
        $sedesDisponibles = $this->sedesDisponiblesParaFiltro($user, $empresasPermitidas);
        $sedesInputRaw = $request->input('sedes', []);
        if (! is_array($sedesInputRaw)) {
            $sedesInputRaw = $sedesInputRaw !== null && $sedesInputRaw !== '' ? [$sedesInputRaw] : [];
        }
        $sedesSeleccionadas = $this->expandirSedesSeleccionadas($sedesInputRaw, $sedesDisponibles);
        $busquedaPersona = trim((string) $request->input('busqueda_persona', ''));

        $query = RegistroConsumo::query()
            ->whereBetween('fecha_consumo', [$fechaDesde, $fechaHasta])
            ->with(['usuario.tipoUsuario', 'usuario.empresa', 'visitante', 'empresa', 'casino', 'horarioConsumo'])
            ->orderBy('fecha_consumo')
            ->orderBy('id_casino')
            ->orderBy('id_empresa')
            ->orderBy('hora_consumo');

        $this->aplicarAlcanceReporte($query, $user, $empresasPermitidas, $sedesSeleccionadas);
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

    /**
     * @param  array<int, int>  $empresasPermitidas
     * @param  array<int, int>  $sedesSeleccionadas  IDs de sede permitidos para el usuario (ya validados)
     */
    private function aplicarAlcanceReporte(
        \Illuminate\Database\Eloquent\Builder $query,
        Usuario $user,
        array $empresasPermitidas,
        array $sedesSeleccionadas
    ): void {
        if ($sedesSeleccionadas !== []) {
            $idsCasino = $this->idsCasinosEnSedes($sedesSeleccionadas);
            $query->whereIn('id_casino', $idsCasino);

            return;
        }

        if (count($empresasPermitidas) > 0) {
            $query->whereIn('id_empresa', $empresasPermitidas);
        } elseif ($user->role === 'gestionhumana') {
            $query->where('id_empresa', $user->id_empresa);
        }
    }

    /**
     * Casinos ubicados en las sedes indicadas (incluye eliminados en soft delete para no perder histórico).
     *
     * @param  array<int, int>  $sedesIds
     * @return array<int, int>
     */
    private function idsCasinosEnSedes(array $sedesIds): array
    {
        if ($sedesIds === []) {
            return [];
        }

        return Casino::withTrashed()
            ->whereIn('id_sede', $sedesIds)
            ->pluck('id_casino')
            ->unique()
            ->all();
    }

    /**
     * @param  array<int, int>  $empresasPermitidas
     * @return \Illuminate\Support\Collection<int, Sede>
     */
    private function sedesDisponiblesParaFiltro(Usuario $user, array $empresasPermitidas): \Illuminate\Support\Collection
    {
        $q = Sede::query()->orderBy('nombre');

        if (count($empresasPermitidas) > 0) {
            $q->whereHas('casinos', function ($cq) use ($empresasPermitidas) {
                $cq->whereHas('empresas', function ($eq) use ($empresasPermitidas) {
                    $eq->whereIn('empresas.id_empresa', $empresasPermitidas);
                });
            });
        } elseif ($user->role === 'gestionhumana' && $user->id_empresa) {
            $q->whereHas('casinos', function ($cq) use ($user) {
                $cq->whereHas('empresas', function ($eq) use ($user) {
                    $eq->where('empresas.id_empresa', $user->id_empresa);
                });
            });
        }

        return $q->get();
    }

    /**
     * Opciones del filtro sede: grupos configurados primero; sedes sueltas (las que no entran en un grupo completo).
     *
     * @param  \Illuminate\Support\Collection<int, Sede>  $sedes
     * @return list<array{type: 'grupo', clave: string, etiqueta: string, ids: list<int>}|array{type: 'sede', id_sede: int, etiqueta: string}>
     */
    private function opcionesFiltroSedeAgrupadas(\Illuminate\Support\Collection $sedes): array
    {
        $gruposConfig = config('reportes.sedes_agrupadas', []);
        $out = [];
        /** @var array<string, bool> $nombresExcluir */
        $nombresExcluir = [];

        foreach ($gruposConfig as $g) {
            $clave = (string) ($g['clave'] ?? '');
            $etiqueta = (string) ($g['etiqueta'] ?? $clave);
            $nombres = $g['nombres'] ?? [];
            if ($clave === '' || ! is_array($nombres) || $nombres === []) {
                continue;
            }
            $idsEncontrados = [];
            $completo = true;
            foreach ($nombres as $nomBuscado) {
                $needle = mb_strtoupper(trim((string) $nomBuscado));
                $found = null;
                foreach ($sedes as $s) {
                    if (mb_strtoupper(trim((string) $s->nombre)) === $needle) {
                        $found = (int) $s->id_sede;
                        break;
                    }
                }
                if ($found === null) {
                    $completo = false;
                    break;
                }
                $idsEncontrados[] = $found;
            }
            if (! $completo) {
                continue;
            }
            $idsEncontrados = array_values(array_unique($idsEncontrados));
            if (count($idsEncontrados) !== count($nombres)) {
                continue;
            }
            foreach ($nombres as $n) {
                $nombresExcluir[mb_strtoupper(trim((string) $n))] = true;
            }
            $out[] = ['tipo' => 'grupo', 'clave' => $clave, 'etiqueta' => $etiqueta, 'ids' => $idsEncontrados];
        }

        foreach ($sedes as $s) {
            $key = mb_strtoupper(trim((string) $s->nombre));
            if (isset($nombresExcluir[$key])) {
                continue;
            }
            $out[] = ['tipo' => 'sede', 'id_sede' => (int) $s->id_sede, 'etiqueta' => (string) $s->nombre];
        }

        return $out;
    }

    /**
     * @param  list<string|int>  $inputRaw  valores de sedes[] (ids o "grupo:clave")
     * @param  \Illuminate\Support\Collection<int, Sede>  $sedesDisponibles
     * @return array<int, int>
     */
    private function expandirSedesSeleccionadas(array $inputRaw, \Illuminate\Support\Collection $sedesDisponibles): array
    {
        $permitidos = $sedesDisponibles->pluck('id_sede')->all();
        $ids = [];
        foreach ($inputRaw as $v) {
            if ($v === null || $v === '') {
                continue;
            }
            $v = is_string($v) ? trim($v) : (string) $v;
            if ($v === '') {
                continue;
            }
            if (str_starts_with($v, 'grupo:')) {
                $clave = substr($v, strlen('grupo:'));
                foreach (config('reportes.sedes_agrupadas', []) as $g) {
                    if ((string) ($g['clave'] ?? '') !== $clave) {
                        continue;
                    }
                    foreach (($g['nombres'] ?? []) as $nomBuscado) {
                        foreach ($sedesDisponibles as $s) {
                            if (mb_strtoupper(trim((string) $s->nombre)) === mb_strtoupper(trim((string) $nomBuscado))) {
                                $ids[] = (int) $s->id_sede;
                                break;
                            }
                        }
                    }
                }
                continue;
            }
            $ids[] = (int) $v;
        }
        $ids = array_values(array_unique(array_filter($ids, fn (int $x) => $x > 0)));

        return array_values(array_intersect($ids, $permitidos));
    }

    /**
     * Valores recibidos en sedes[] para mantener el estado de los checkboxes (incl. "grupo:clave").
     *
     * @return list<string>
     */
    private function valoresCheckboxSedeRequest(mixed $input): array
    {
        if (! is_array($input)) {
            $input = $input !== null && $input !== '' ? [$input] : [];
        }
        $out = [];
        foreach ($input as $v) {
            if ($v === null || $v === '') {
                continue;
            }
            $v = is_string($v) ? trim($v) : (string) $v;
            if ($v === '') {
                continue;
            }
            if (str_starts_with($v, 'grupo:')) {
                $out[] = $v;
                continue;
            }
            $i = (int) $v;
            if ($i > 0) {
                $out[] = (string) $i;
            }
        }

        return array_values(array_unique($out, SORT_STRING));
    }
}
