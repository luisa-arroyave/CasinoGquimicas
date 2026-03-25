<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaVisita;
use App\Models\Casino;
use App\Models\Empresa;
use App\Models\HorarioConsumo;
use App\Models\Precio;
use App\Models\RegistroConsumo;
use App\Models\Usuario;
use App\Models\Visitante;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ConsumoManualController extends Controller
{
    public function index(Request $request): View
    {
        $query = RegistroConsumo::with(['usuario', 'visitante', 'empresa', 'casino', 'horarioConsumo']);
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_consumo', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_consumo', '<=', $request->fecha_hasta);
        }
        $consumos = $query->orderByDesc('fecha_consumo')->orderByDesc('hora_consumo')->paginate(20)->withQueryString();
        return view('admin.consumos-manuales.index', compact('consumos'));
    }

    public function create(): View
    {
        $empresas = Empresa::where('activa', true)->orderBy('nombre')->get();
        $casinos = Casino::where('activo', true)->orderBy('nombre')->get();
        $horarios = HorarioConsumo::where('activo', true)->orderBy('hora_inicio')->get();
        $usuarios = Usuario::where('activo', true)->with('empresa')->orderBy('nombres')->get();
        $visitantes = Visitante::orderBy('nombre')->get();
        $precios = Precio::all()->keyBy(function ($p) {
            return ($p->id_casino ?? '') . '-' . $p->id_horario;
        })->map(function ($p) {
            return ['precio_empleado' => (float) $p->precio_empleado, 'precio_casino' => (float) $p->precio_casino];
        });
        $areasVisita = AreaVisita::where('activo', true)->orderBy('nombre')->get();
        return view('admin.consumos-manuales.create', compact('empresas', 'casinos', 'horarios', 'usuarios', 'visitantes', 'precios', 'areasVisita'));
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'tipo_consumidor' => 'required|in:usuario,visitante',
            'id_usuario' => 'required_if:tipo_consumidor,usuario|nullable|exists:usuarios,id_usuario',
            'id_visitante' => 'required_if:tipo_consumidor,visitante|nullable|exists:visitantes,id_visitante',
            'id_area_visita' => 'required_if:tipo_consumidor,visitante|nullable|exists:area_visita,id_area_visita',
            'id_empresa' => 'required|exists:empresas,id_empresa',
            'id_casino' => 'required|exists:casinos,id_casino',
            'id_horario' => 'required|exists:horarios_consumo,id_horario',
            'fecha_consumo' => 'required|date',
            'hora_consumo' => 'required|date_format:H:i',
            'precio_empleado' => 'required|numeric|min:0',
            'precio_casino' => 'required|numeric|min:0',
            'direccion_entrega' => 'nullable|string|max:500',
        ]);

        $casino = Casino::find($valid['id_casino']);
        $horario = HorarioConsumo::find($valid['id_horario']);
        $empresa = Empresa::find($valid['id_empresa']);
        $tipoPedido = strtolower(trim($casino->tipo_casino ?? '')) === 'domicilio' ? 'domicilio' : 'en_sitio';

        $idUsuario = $valid['tipo_consumidor'] === 'usuario' ? $valid['id_usuario'] : null;
        $idVisitante = $valid['tipo_consumidor'] === 'visitante' ? $valid['id_visitante'] : null;

        if ($idUsuario === null && $idVisitante === null) {
            return back()->withInput()->withErrors(['tipo_consumidor' => 'Debe seleccionar un usuario o un visitante.']);
        }

        $documento = null;
        $nombresConsumidor = null;
        $tipoUsuarioNombre = null;
        $empresaTemporalNombre = null;
        if ($idUsuario) {
            $usuario = Usuario::with(['rol', 'tipoUsuario', 'empresaTemporal'])->find($idUsuario);
            $documento = $usuario?->documento;
            $nombresConsumidor = $usuario?->nombres;
            $tipoUsuarioNombre = $usuario?->tipoUsuario?->nombre;
            $empresaTemporalNombre = $usuario?->empresaTemporal?->nombre;
        } else {
            $visitante = Visitante::find($idVisitante);
            $documento = $visitante?->documento;
            $nombresConsumidor = $visitante?->nombre;
            $tipoUsuarioNombre = 'Visitante';
        }

        $idAreaVisita = $valid['tipo_consumidor'] === 'visitante' ? ($valid['id_area_visita'] ?? null) : null;

        RegistroConsumo::create([
            'id_usuario' => $idUsuario,
            'id_visitante' => $idVisitante,
            'id_area_visita' => $idAreaVisita,
            'id_empresa' => $valid['id_empresa'],
            'id_casino' => $valid['id_casino'],
            'id_horario' => $valid['id_horario'],
            'documento' => $documento,
            'nombres_consumidor' => $nombresConsumidor,
            'tipo_usuario_nombre' => $tipoUsuarioNombre,
            'empresa_nombre' => $empresa?->nombre,
            'empresa_temporal_nombre' => $empresaTemporalNombre,
            'casino_nombre' => $casino->nombre,
            'horario_nombre' => $horario?->nombre,
            'tipo_comida' => $horario ? mb_strtoupper(trim((string) $horario->nombre), 'UTF-8') : null,
            'fecha_consumo' => $valid['fecha_consumo'],
            'hora_consumo' => $valid['hora_consumo'],
            'precio_empleado' => $valid['precio_empleado'],
            'precio_casino' => $valid['precio_casino'],
            'estado' => 'ENTREGADO',
            'tipo_pedido' => $tipoPedido,
            'direccion_entrega' => $tipoPedido === 'domicilio' ? ($valid['direccion_entrega'] ?? null) : null,
            'registrado_por' => null,
        ]);

        return redirect()->route('admin.consumos-manuales.index')->with('success', 'Consumo registrado correctamente.');
    }

    /**
     * Descargar plantilla Excel para importar consumos.
     */
    public function descargarPlantillaImportacion()
    {
        $empresas = Empresa::where('activa', true)->orderBy('nombre')->get(['id_empresa', 'nombre']);
        $casinos = Casino::where('activo', true)->orderBy('nombre')->get(['id_casino', 'nombre']);
        $horarios = HorarioConsumo::where('activo', true)->orderBy('hora_inicio')->get(['id_horario', 'nombre']);

        $spreadsheet = new Spreadsheet;
        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setTitle('Consumos');
        $hoja->fromArray([
            ['documento', 'nombres_consumidor', 'id_empresa', 'id_casino', 'tipo_comida', 'fecha_consumo', 'hora_consumo'],
            ['12345678', 'Juan Pérez', 1, 1, 'ALMUERZO', '2025-03-20', '12:00'],
        ]);
        $hoja->getStyle('A1:G1')->getFont()->setBold(true);
        foreach (range('A', 'G') as $col) {
            $hoja->getColumnDimension($col)->setAutoSize(true);
        }

        $refEmpresas = $spreadsheet->createSheet();
        $refEmpresas->setTitle('Referencia empresas');
        $refEmpresas->fromArray([['id_empresa', 'nombre']], null, 'A1');
        $refEmpresas->fromArray($empresas->map(fn ($e) => [$e->id_empresa, $e->nombre])->toArray(), null, 'A2');
        $refEmpresas->getStyle('A1:B1')->getFont()->setBold(true);

        $refCasinos = $spreadsheet->createSheet();
        $refCasinos->setTitle('Referencia casinos');
        $refCasinos->fromArray([['id_casino', 'nombre']], null, 'A1');
        $refCasinos->fromArray($casinos->map(fn ($c) => [$c->id_casino, $c->nombre])->toArray(), null, 'A2');
        $refCasinos->getStyle('A1:B1')->getFont()->setBold(true);

        $refHorarios = $spreadsheet->createSheet();
        $refHorarios->setTitle('Referencia horarios');
        $refHorarios->fromArray([['id_horario', 'nombre (tipo de comida)']], null, 'A1');
        $refHorarios->fromArray($horarios->map(fn ($h) => [$h->id_horario, $h->nombre])->toArray(), null, 'A2');
        $refHorarios->getStyle('A1:B1')->getFont()->setBold(true);
        $refHorarios->setCellValue('C1', 'En Consumos use tipo_comida = nombre (ej. ALMUERZO, CENA, REFRIGERIO)');

        $tempPath = storage_path('app/temp/plantilla-importar-consumos-' . uniqid() . '.xlsx');
        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, 'plantilla-importar-consumos.xlsx')->deleteFileAfterSend(true);
    }

    /**
     * Importar consumos desde archivo Excel.
     * Columnas: documento, nombres_consumidor, id_empresa, id_casino, tipo_comida (ALMUERZO, CENA, REFRIGERIO), fecha_consumo, hora_consumo (opcional)
     */
    public function importarExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'archivo.required' => 'Seleccione un archivo.',
            'archivo.mimes' => 'El archivo debe ser Excel (.xlsx, .xls) o CSV (.csv).',
        ]);

        $archivo = $request->file('archivo');
        $ext = strtolower($archivo->getClientOriginalExtension());
        $tempPath = $archivo->storeAs('temp', 'import-consumos-' . uniqid() . '.' . ($ext ?: 'xlsx'));
        $fullPath = str_replace('\\', '/', Storage::path($tempPath));

        try {
            $readerType = in_array($ext, ['csv', 'txt']) ? 'Csv' : 'Xlsx';
            $reader = IOFactory::createReader($readerType);
            $reader->setReadDataOnly(true);
            if ($readerType === 'Csv') {
                $reader->setDelimiter(';');
                if (($sample = file_get_contents($fullPath, false, null, 0, 200)) && strpos($sample, ',') !== false && strpos($sample, ';') === false) {
                    $reader->setDelimiter(',');
                }
                $reader->setEnclosure('"');
            }
            $spreadsheet = $reader->load($fullPath);
        } catch (\Throwable $e) {
            if (Storage::exists($tempPath)) {
                Storage::delete($tempPath);
            }
            return back()->with('error', 'No se pudo leer el archivo: ' . $e->getMessage());
        }
        if (Storage::exists($tempPath)) {
            Storage::delete($tempPath);
        }

        $hoja = $spreadsheet->getActiveSheet();
        $filas = $hoja->toArray();

        if (count($filas) < 2) {
            return back()->with('error', 'El archivo no tiene datos. La primera fila debe ser encabezados y desde la fila 2 los consumos.');
        }

        $encabezados = array_map(function ($h) {
            return strtolower(trim(str_replace([' ', '-'], '_', (string) $h)));
        }, $filas[0]);
        $creados = 0;
        $errores = [];
        /** @var array<string, int> clave "id_usuario|fecha|id_horario" => número de fila en el Excel */
        $clavesVistasEnArchivo = [];

        $horariosActivos = HorarioConsumo::where('activo', true)->orderBy('hora_inicio')->get();
        $horarioDefault = $horariosActivos->first();
        if (! $horarioDefault) {
            return back()->with('error', 'No hay horario de consumo activo. Configure al menos uno.');
        }

        for ($i = 1; $i < count($filas); $i++) {
            $fila = $filas[$i];
            $filaNum = $i + 1;
            $row = [];
            foreach ($encabezados as $idx => $h) {
                $row[$h] = $fila[$idx] ?? '';
            }

            $documento = trim((string) ($row['documento'] ?? $row['documento_consumidor'] ?? ''));
            $nombresConsumidor = trim((string) ($row['nombres_consumidor'] ?? $row['nombres'] ?? ''));
            $idEmpresa = $row['id_empresa'] ?? null;
            $idCasino = $row['id_casino'] ?? null;
            $fechaConsumo = $row['fecha_consumo'] ?? null;
            $horaConsumo = trim((string) ($row['hora_consumo'] ?? $row['hora'] ?? ''));

            if ($documento === '' && $nombresConsumidor === '') {
                continue;
            }
            if ($documento === '' || $nombresConsumidor === '') {
                $errores[] = "Fila {$filaNum}: documento y nombres_consumidor son obligatorios.";
                continue;
            }
            if (empty($idEmpresa) || empty($idCasino) || empty($fechaConsumo)) {
                $errores[] = "Fila {$filaNum}: id_empresa, id_casino y fecha_consumo son obligatorios.";
                continue;
            }

            $idEmpresa = (int) $idEmpresa;
            $idCasino = (int) $idCasino;

            $empresa = Empresa::find($idEmpresa);
            if (! $empresa) {
                $errores[] = "Fila {$filaNum}: id_empresa {$idEmpresa} no existe.";
                continue;
            }

            $casino = Casino::find($idCasino);
            if (! $casino) {
                $errores[] = "Fila {$filaNum}: id_casino {$idCasino} no existe.";
                continue;
            }

            $tipoComidaRaw = trim((string) ($row['tipo_comida'] ?? $row['tipo_pedido'] ?? $row['horario'] ?? ''));
            $horario = null;
            if ($tipoComidaRaw === '') {
                $horario = $horarioDefault;
            } else {
                $key = mb_strtoupper(trim($tipoComidaRaw), 'UTF-8');
                if ($key === 'REFIRGERIO') {
                    $key = 'REFRIGERIO';
                }
                foreach ($horariosActivos as $h) {
                    if (mb_strtoupper(trim((string) $h->nombre), 'UTF-8') === $key) {
                        $horario = $h;
                        break;
                    }
                }
                if (! $horario) {
                    $errores[] = "Fila {$filaNum}: tipo_comida «{$tipoComidaRaw}» no coincide con un horario activo (use el nombre exacto, ej. ALMUERZO, CENA, REFRIGERIO).";
                    continue;
                }
            }

            try {
                if (is_numeric($fechaConsumo)) {
                    $fechaCarbon = Carbon::instance(SpreadsheetDate::excelToDateTimeObject((float) $fechaConsumo));
                } else {
                    $fechaCarbon = Carbon::parse($fechaConsumo);
                }
            } catch (\Throwable $e) {
                $errores[] = "Fila {$filaNum}: fecha_consumo «{$fechaConsumo}» no válida.";
                continue;
            }

            $horaStr = '12:00:00';
            if ($horaConsumo !== '') {
                try {
                    if (is_numeric($horaConsumo)) {
                        $dt = SpreadsheetDate::excelToDateTimeObject((float) $horaConsumo);
                        $horaStr = $dt->format('H:i:s');
                    } else {
                        $parsed = Carbon::parse($horaConsumo);
                        $horaStr = $parsed->format('H:i:s');
                    }
                } catch (\Throwable $e) {
                    $horaStr = '12:00:00';
                }
            }

            $precio = Precio::where('id_horario', $horario->id_horario)
                ->where(function ($q) use ($casino) {
                    $q->where('id_casino', $casino->id_casino)->orWhereNull('id_casino');
                })
                ->orderByRaw('CASE WHEN id_casino IS NOT NULL THEN 0 ELSE 1 END')
                ->first();
            $precioEmpleado = $precio ? (float) $precio->precio_empleado : 0;
            $precioCasino = $precio ? (float) $precio->precio_casino : 0;

            $usuario = Usuario::where('documento', $documento)->where('activo', true)->first();
            $idUsuario = $usuario?->id_usuario;
            $tipoUsuarioNombre = $usuario?->tipoUsuario?->nombre ?? 'Importado';
            $empresaTemporalNombre = $usuario?->empresaTemporal?->nombre;

            $tipoPedidoDb = strtolower(trim($casino->tipo_casino ?? '')) === 'domicilio' ? 'domicilio' : 'en_sitio';

            $fechaStr = $fechaCarbon->toDateString();
            $nombreHorario = $horario->nombre;

            if ($idUsuario !== null) {
                $claveArchivo = $idUsuario . '|' . $fechaStr . '|' . $horario->id_horario;
                if (isset($clavesVistasEnArchivo[$claveArchivo])) {
                    $filaDup = $clavesVistasEnArchivo[$claveArchivo];
                    $errores[] = "Fila {$filaNum}: en el archivo hay otra fila (fila {$filaDup}) con el mismo usuario, la misma fecha ({$fechaStr}) y el mismo tipo de comida «{$nombreHorario}». Solo puede haber un registro por persona, fecha y tipo de comida.";
                    continue;
                }

                $yaExiste = RegistroConsumo::query()
                    ->where('id_usuario', $idUsuario)
                    ->whereDate('fecha_consumo', $fechaStr)
                    ->where('id_horario', $horario->id_horario)
                    ->exists();
                if ($yaExiste) {
                    $errores[] = "Fila {$filaNum}: ya existe un consumo para el documento {$documento} el {$fechaStr} para «{$nombreHorario}». La base de datos no permite duplicar la misma persona, fecha y tipo de comida (refrigerio, cena, etc.). Elimine el registro anterior o cambie fecha o tipo de comida.";
                    continue;
                }
                $clavesVistasEnArchivo[$claveArchivo] = $filaNum;
            }

            try {
                RegistroConsumo::create([
                    'id_usuario' => $idUsuario,
                    'id_visitante' => null,
                    'id_area_visita' => null,
                    'id_empresa' => $idEmpresa,
                    'id_casino' => $idCasino,
                    'id_horario' => $horario->id_horario,
                    'documento' => $documento,
                    'nombres_consumidor' => $nombresConsumidor,
                    'tipo_usuario_nombre' => $tipoUsuarioNombre,
                    'empresa_nombre' => $empresa->nombre,
                    'empresa_temporal_nombre' => $empresaTemporalNombre,
                    'casino_nombre' => $casino->nombre,
                    'horario_nombre' => $horario->nombre,
                    'tipo_comida' => mb_strtoupper(trim((string) $horario->nombre), 'UTF-8'),
                    'fecha_consumo' => $fechaStr,
                    'hora_consumo' => $horaStr,
                    'precio_empleado' => $precioEmpleado,
                    'precio_casino' => $precioCasino,
                    'estado' => 'ENTREGADO',
                    'tipo_pedido' => $tipoPedidoDb,
                    'registrado_por' => $request->user()?->id_usuario,
                ]);
            } catch (UniqueConstraintViolationException $e) {
                if (str_contains($e->getMessage(), 'uk_consumo_usuario')) {
                    $errores[] = "Fila {$filaNum}: no se pudo guardar: ya hay un consumo para este usuario el {$fechaStr} con el tipo de comida «{$nombreHorario}». Solo se permite un registro por persona, fecha y tipo de comida.";
                } else {
                    $errores[] = "Fila {$filaNum}: no se pudo guardar por un dato duplicado en el sistema. Revise que no repita la misma combinación en el archivo o en registros ya cargados.";
                }
                continue;
            }
            $creados++;
        }

        if ($creados > 0) {
            $msg = "Se importaron {$creados} consumo(s) correctamente.";
            if (count($errores) > 0) {
                $msg .= ' Algunas filas con errores fueron omitidas: ' . implode('; ', array_slice($errores, 0, 3));
                if (count($errores) > 3) {
                    $msg .= ' (+' . (count($errores) - 3) . ' más)';
                }
            }
            return back()->with('success', $msg);
        }

        $msgError = 'No se importó ningún consumo. Revise el formato del archivo.';
        if (count($errores) > 0) {
            $msgError = implode(' ', array_slice($errores, 0, 8));
            if (count($errores) > 8) {
                $msgError .= ' (+' . (count($errores) - 8) . ' errores más)';
            }
        }

        return back()->with('error', $msgError);
    }
}
