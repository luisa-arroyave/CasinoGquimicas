<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Casino;
use App\Models\Empresa;
use App\Models\EmpresaContratista;
use App\Models\EmpresaTemporal;
use App\Models\HorarioConsumo;
use App\Models\Role;
use App\Models\Sede;
use App\Models\TipoUsuario;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $query = Usuario::with(['empresa', 'rol', 'tipoUsuario']);
        if ($request->filled('empresa')) {
            $query->where('id_empresa', $request->empresa);
        }
        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->where(function ($qry) use ($q) {
                $qry->where('documento', 'like', "%{$q}%")
                    ->orWhere('nombres', 'like', "%{$q}%");
            });
        }
        $usuarios = $query->orderBy('nombres')->paginate(15)->withQueryString();
        $empresas = Empresa::orderBy('nombre')->get();
        return view('admin.usuarios.index', compact('usuarios', 'empresas'));
    }

    public function create(): View
    {
        $empresas = Empresa::orderBy('nombre')->get();
        $roles = Role::orderBy('nombre')->get();
        $tipos = TipoUsuario::orderBy('nombre')->get();
        $empresasTemporales = EmpresaTemporal::where('activa', true)->orderBy('nombre')->get();
        $empresasContratistas = EmpresaContratista::where('activa', true)->orderBy('nombre')->get();
        $casinos = Casino::where('activo', true)->orderBy('nombre')->get();
        $sedes = Sede::orderBy('nombre')->get();
        return view('admin.usuarios.create', compact('empresas', 'roles', 'tipos', 'empresasTemporales', 'empresasContratistas', 'casinos', 'sedes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'documento' => ['required', 'string', 'max:50', Rule::unique('usuarios', 'documento')],
            'nombres' => 'required|string|max:255',
            'id_empresa' => 'required|exists:empresas,id_empresa',
            'id_rol' => 'required|exists:roles,id_rol',
            'id_tipo_usuario' => 'required|exists:tipos_usuario,id_tipo_usuario',
            'id_empresa_temporal' => 'nullable|exists:empresas_temporales,id_empresa_temporal',
            'id_empresa_contratista' => 'nullable|exists:empresas_contratistas,id_empresa_contratista',
            'codigo_qr' => 'nullable|string|max:100',
            'activo' => 'boolean',
            'empresas' => 'nullable|array',
            'empresas.*' => 'exists:empresas,id_empresa',
            'id_casino_asignado' => 'nullable|exists:casinos,id_casino',
            'id_sede_principal' => 'nullable|exists:sedes,id_sede',
            'sedes' => 'nullable|array',
            'sedes.*' => 'exists:sedes,id_sede',
        ], [
            'documento.unique' => 'Este documento ya está registrado: el usuario ya existe. No puede crear otro con el mismo número (incluye usuarios eliminados del listado).',
        ]);
        $valid['activo'] = $request->boolean('activo');
        $rol = Role::find($valid['id_rol']);
        if ($rol && $rol->nombre === 'casino') {
            if (! $request->filled('id_casino_asignado')) {
                return back()->withInput()->withErrors(['id_casino_asignado' => 'Debe asignar un casino para usuarios con rol casino.']);
            }
        } else {
            $valid['id_casino_asignado'] = null;
        }
        $valid['password_hash'] = Hash::make($valid['documento']);
        $valid['cambiar_clave_obligatorio'] = true;
        $tipoUsuario = TipoUsuario::find($valid['id_tipo_usuario']);
        $tipoNombre = $tipoUsuario ? strtoupper(trim($tipoUsuario->nombre)) : '';
        if ($tipoNombre === 'TEMPORAL') {
            if (! $request->filled('id_empresa_temporal')) {
                return back()->withInput()->withErrors(['id_empresa_temporal' => 'Debe seleccionar una empresa temporal para usuarios con tipo Temporal.']);
            }
            $valid['id_empresa_contratista'] = null;
        } elseif ($tipoNombre === 'CONTRATISTA') {
            if (! $request->filled('id_empresa_contratista')) {
                return back()->withInput()->withErrors(['id_empresa_contratista' => 'Debe seleccionar una empresa contratista para usuarios con tipo Contratista.']);
            }
            $valid['id_empresa_temporal'] = null;
        } else {
            $valid['id_empresa_temporal'] = null;
            $valid['id_empresa_contratista'] = null;
        }
        unset($valid['empresas'], $valid['sedes']);
        try {
            $usuario = Usuario::create($valid);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return back()->withInput()->withErrors([
                    'documento' => 'Este documento ya está registrado: el usuario ya existe.',
                ]);
            }
            throw $e;
        }
        $this->syncEmpresasAcceso($usuario, $request->input('empresas', []));
        $this->syncSedesAcceso($usuario, $request->input('sedes', []));
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(Usuario $usuario): View
    {
        $usuario->load('empresasAcceso');
        $empresas = Empresa::orderBy('nombre')->get();
        $roles = Role::orderBy('nombre')->get();
        $tipos = TipoUsuario::orderBy('nombre')->get();
        $empresasTemporales = EmpresaTemporal::where('activa', true)->orderBy('nombre')->get();
        $empresasContratistas = EmpresaContratista::where('activa', true)->orderBy('nombre')->get();
        $usuario->load('sedesAcceso');
        $casinos = Casino::where('activo', true)->orderBy('nombre')->get();
        $sedes = Sede::orderBy('nombre')->get();
        return view('admin.usuarios.edit', compact('usuario', 'empresas', 'roles', 'tipos', 'empresasTemporales', 'empresasContratistas', 'casinos', 'sedes'));
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $valid = $request->validate([
            'documento' => ['required', 'string', 'max:50', Rule::unique('usuarios', 'documento')->ignore($usuario->id_usuario, 'id_usuario')],
            'nombres' => 'required|string|max:255',
            'id_empresa' => 'required|exists:empresas,id_empresa',
            'id_rol' => 'required|exists:roles,id_rol',
            'id_tipo_usuario' => 'required|exists:tipos_usuario,id_tipo_usuario',
            'id_empresa_temporal' => 'nullable|exists:empresas_temporales,id_empresa_temporal',
            'id_empresa_contratista' => 'nullable|exists:empresas_contratistas,id_empresa_contratista',
            'codigo_qr' => 'nullable|string|max:100',
            'activo' => 'boolean',
            'empresas' => 'nullable|array',
            'empresas.*' => 'exists:empresas,id_empresa',
            'id_casino_asignado' => 'nullable|exists:casinos,id_casino',
            'id_sede_principal' => 'nullable|exists:sedes,id_sede',
            'sedes' => 'nullable|array',
            'sedes.*' => 'exists:sedes,id_sede',
        ], [
            'documento.unique' => 'Este documento ya pertenece a otro usuario. Elija un documento distinto.',
        ]);
        $valid['activo'] = $request->boolean('activo');
        $rol = Role::find($valid['id_rol']);
        if ($rol && $rol->nombre === 'casino') {
            if (! $request->filled('id_casino_asignado')) {
                return back()->withInput()->withErrors(['id_casino_asignado' => 'Debe asignar un casino para usuarios con rol casino.']);
            }
        } else {
            $valid['id_casino_asignado'] = null;
        }
        $tipoUsuario = TipoUsuario::find($valid['id_tipo_usuario']);
        $tipoNombre = $tipoUsuario ? strtoupper(trim($tipoUsuario->nombre)) : '';
        if ($tipoNombre === 'TEMPORAL') {
            if (! $request->filled('id_empresa_temporal')) {
                return back()->withInput()->withErrors(['id_empresa_temporal' => 'Debe seleccionar una empresa temporal para usuarios con tipo Temporal.']);
            }
            $valid['id_empresa_contratista'] = null;
        } elseif ($tipoNombre === 'CONTRATISTA') {
            if (! $request->filled('id_empresa_contratista')) {
                return back()->withInput()->withErrors(['id_empresa_contratista' => 'Debe seleccionar una empresa contratista para usuarios con tipo Contratista.']);
            }
            $valid['id_empresa_temporal'] = null;
        } else {
            $valid['id_empresa_temporal'] = null;
            $valid['id_empresa_contratista'] = null;
        }
        unset($valid['empresas'], $valid['sedes']);
        try {
            $usuario->update($valid);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return back()->withInput()->withErrors([
                    'documento' => 'Este documento ya pertenece a otro usuario.',
                ]);
            }
            throw $e;
        }
        $this->syncEmpresasAcceso($usuario, $request->input('empresas', []));
        $this->syncSedesAcceso($usuario, $request->input('sedes', []));
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Sincronizar empresas de acceso (solo para rol administrador o gestionhumana).
     */
    private function syncEmpresasAcceso(Usuario $usuario, array $empresasIds): void
    {
        $rol = $usuario->rol;
        if (! $rol || ! in_array($rol->nombre, ['administrador', 'gestionhumana'], true)) {
            $usuario->empresasAcceso()->sync([]);
            return;
        }
        $usuario->empresasAcceso()->sync($empresasIds);
    }

    /**
     * Sincronizar sedes adicionales donde puede registrar consumo (empleados en varias sedes).
     */
    private function syncSedesAcceso(Usuario $usuario, array $sedesIds): void
    {
        $usuario->sedesAcceso()->sync($sedesIds);
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
        $usuario->forceDelete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente de la base de datos.');
    }

    /**
     * Resetear la contraseña del usuario al documento. Obliga a cambiar en próximo login.
     */
    public function resetearClave(Usuario $usuario): RedirectResponse
    {
        $usuario->password_hash = Hash::make($usuario->documento);
        $usuario->cambiar_clave_obligatorio = true;
        $usuario->save();

        return back()->with('success', "Clave resetada. La contraseña temporal es el documento ({$usuario->documento}). El usuario deberá cambiarla en el próximo inicio de sesión.");
    }

    /**
     * Listado Excel de todos los usuarios: mismas columnas que importar consumos; tipo_comida, fecha y hora vacíos.
     */
    public function descargarPlantillaImportacion(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $usuarios = Usuario::query()
            ->orderBy('nombres')
            ->get(['documento', 'nombres', 'id_empresa', 'id_casino_asignado']);

        $spreadsheet = new Spreadsheet;
        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setTitle('Usuarios');

        $headers = ['documento', 'nombres_consumidor', 'id_empresa', 'id_casino', 'tipo_comida', 'fecha_consumo', 'hora_consumo'];
        $hoja->fromArray($headers, null, 'A1');
        $hoja->getStyle('A1:G1')->getFont()->setBold(true);

        $fila = 2;
        foreach ($usuarios as $u) {
            $hoja->fromArray([
                [
                    $u->documento,
                    $u->nombres,
                    $u->id_empresa,
                    $u->id_casino_asignado ?? '',
                    '',
                    '',
                    '',
                ],
            ], null, 'A' . $fila);
            $fila++;
        }

        foreach (range('A', 'G') as $col) {
            $hoja->getColumnDimension($col)->setAutoSize(true);
        }

        $empresas = Empresa::where('activa', true)->orderBy('nombre')->get(['id_empresa', 'nombre']);
        $casinos = Casino::where('activo', true)->orderBy('nombre')->get(['id_casino', 'nombre']);
        $horarios = HorarioConsumo::where('activo', true)->orderBy('hora_inicio')->get(['id_horario', 'nombre']);

        $refEmp = $spreadsheet->createSheet();
        $refEmp->setTitle('Referencia empresas');
        $refEmp->fromArray([['id_empresa', 'nombre']], null, 'A1');
        $refEmp->fromArray($empresas->map(fn ($e) => [$e->id_empresa, $e->nombre])->toArray(), null, 'A2');
        $refEmp->getStyle('A1:B1')->getFont()->setBold(true);

        $refCas = $spreadsheet->createSheet();
        $refCas->setTitle('Referencia casinos');
        $refCas->fromArray([['id_casino', 'nombre']], null, 'A1');
        $refCas->fromArray($casinos->map(fn ($c) => [$c->id_casino, $c->nombre])->toArray(), null, 'A2');
        $refCas->getStyle('A1:B1')->getFont()->setBold(true);

        $refHor = $spreadsheet->createSheet();
        $refHor->setTitle('Referencia horarios');
        $refHor->fromArray([['id_horario', 'nombre (tipo de comida)']], null, 'A1');
        $refHor->fromArray($horarios->map(fn ($h) => [$h->id_horario, $h->nombre])->toArray(), null, 'A2');
        $refHor->getStyle('A1:B1')->getFont()->setBold(true);

        $tempPath = storage_path('app/temp/plantilla-usuarios-listado-' . uniqid() . '.xlsx');
        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, 'plantilla-usuarios-listado.xlsx')->deleteFileAfterSend(true);
    }

    /**
     * Plantilla de ejemplo para importar usuarios (empresa, rol, tipo_usuario por nombre).
     */
    public function descargarPlantillaEjemploImportarUsuarios(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet;
        $hoja = $spreadsheet->getActiveSheet();
        $hoja->setTitle('Usuarios');

        $headers = ['documento', 'nombres', 'empresa', 'rol', 'tipo_usuario', 'empresa_temporal', 'empresa_contratista', 'casino_asignado', 'sede_principal', 'activo'];
        $hoja->fromArray($headers, null, 'A1');
        $hoja->fromArray([
            ['12345678', 'Juan Pérez', 'Mi Empresa', 'empleado', 'FIJO', '', '', '', 'Sede Central', 'Si'],
            ['87654321', 'María López', 'Otra Empresa', 'empleado', 'TEMPORAL', 'Empresa Temp 1', '', '', 'Sede Norte', 'Si'],
            ['11111111', 'Carlos Ruiz', 'Mi Empresa', 'empleado', 'CONTRATISTA', '', 'Nombre empresa contratista', '', 'Sede Central', 'Si'],
        ], null, 'A2');

        $hojaRef = $spreadsheet->createSheet();
        $hojaRef->setTitle('Referencia');
        $hojaRef->setCellValue('A1', 'Columna');
        $hojaRef->setCellValue('B1', 'Valores permitidos');
        $hojaRef->setCellValue('A2', 'empresa');
        $hojaRef->setCellValue('B2', 'Nombre exacto de la empresa (ver tabla empresas)');
        $hojaRef->setCellValue('A3', 'rol');
        $hojaRef->setCellValue('B3', 'empleado, administrador, gestionhumana, casino, operativo');
        $hojaRef->setCellValue('A4', 'tipo_usuario');
        $hojaRef->setCellValue('B4', 'FIJO, TEMPORAL, SENA, PASANTE, CONTRATISTA');
        $hojaRef->setCellValue('A5', 'empresa_temporal');
        $hojaRef->setCellValue('B5', 'Solo si tipo_usuario=TEMPORAL. Nombre de empresa temporal.');
        $hojaRef->setCellValue('A6', 'empresa_contratista');
        $hojaRef->setCellValue('B6', 'Solo si tipo_usuario=CONTRATISTA. Nombre o NIT de empresa contratista activa (columna empresa_contratista).');
        $hojaRef->setCellValue('A7', 'casino_asignado');
        $hojaRef->setCellValue('B7', 'Solo si rol=casino. Nombre del casino.');
        $hojaRef->setCellValue('A8', 'sede_principal');
        $hojaRef->setCellValue('B8', 'Nombre de la sede (para empleados).');
        $hojaRef->setCellValue('A9', 'activo');
        $hojaRef->setCellValue('B9', 'Si o No (default: Si)');

        $tempPath = storage_path('app/temp/plantilla-importar-usuarios-' . uniqid() . '.xlsx');
        if (! is_dir(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, 'plantilla-ejemplo-importar-usuarios.xlsx')->deleteFileAfterSend(true);
    }

    /**
     * Importar usuarios desde archivo Excel.
     */
    public function importar(Request $request): RedirectResponse
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'archivo.required' => 'Seleccione un archivo.',
            'archivo.mimes' => 'El archivo debe ser Excel (.xlsx, .xls) o CSV (.csv).',
        ]);

        $archivo = $request->file('archivo');
        $ext = strtolower($archivo->getClientOriginalExtension());
        $tempPath = $archivo->storeAs('temp', 'import-' . uniqid() . '.' . ($ext ?: 'xlsx'));
        $fullPath = str_replace('\\', '/', Storage::path($tempPath));

        if (! class_exists('ZipArchive') && in_array($ext, ['xlsx', 'xls'])) {
            if (Storage::exists($tempPath)) {
                Storage::delete($tempPath);
            }
            return back()->with('error', 'La extensión Zip de PHP no está habilitada. Use un archivo CSV en su lugar (guarde el Excel como CSV desde Excel).');
        }

        try {
            $readerType = in_array($ext, ['csv', 'txt']) ? 'Csv' : 'Xlsx';
            $reader = IOFactory::createReader($readerType);
            $reader->setReadDataOnly(true);
            if ($readerType === 'Csv') {
                $reader->setDelimiter(';');
                if (($sample = @file_get_contents($fullPath, false, null, 0, 400)) && str_contains($sample, ',') && ! str_contains($sample, ';')) {
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
            return back()->with('error', 'El archivo no tiene datos. La primera fila debe ser encabezados y desde la fila 2 los usuarios.');
        }

        $encabezados = array_map(static function ($h) {
            return strtolower(trim(preg_replace('/[\s\-]+/u', '_', (string) $h)));
        }, $filas[0]);
        $creados = 0;
        $errores = [];

        for ($i = 1; $i < count($filas); $i++) {
            $fila = $filas[$i];
            $filaNum = $i + 1;
            $row = array_combine(array_pad($encabezados, count($fila), ''), array_pad($fila, count($encabezados), ''));

            $documentoRaw = $row['documento'] ?? '';
            $documento = is_numeric($documentoRaw) && $documentoRaw !== ''
                ? (string) (int) (float) $documentoRaw
                : trim((string) $documentoRaw);
            $nombres = trim((string) ($row['nombres'] ?? ''));

            if ($documento === '' && $nombres === '') {
                continue;
            }
            if ($documento === '' || $nombres === '') {
                $errores[] = "Fila {$filaNum}: documento y nombres son obligatorios.";
                continue;
            }

            $empresaNombre = trim((string) ($row['empresa'] ?? ''));
            $empresa = $empresaNombre === '' ? null : Empresa::whereRaw('LOWER(TRIM(nombre)) = ?', [mb_strtolower($empresaNombre, 'UTF-8')])->first();
            if (! $empresa) {
                $errores[] = "Fila {$filaNum}: empresa no encontrada («{$empresaNombre}»).";
                continue;
            }

            $rolNombre = trim((string) ($row['rol'] ?? ''));
            $rol = $rolNombre === '' ? null : Role::whereRaw('LOWER(TRIM(nombre)) = ?', [mb_strtolower($rolNombre, 'UTF-8')])->first();
            if (! $rol) {
                $errores[] = "Fila {$filaNum}: rol no encontrado («{$rolNombre}»).";
                continue;
            }

            $tipoUsuarioRaw = trim((string) ($row['tipo_usuario'] ?? ''));
            $tipoUsuario = TipoUsuario::whereRaw('UPPER(TRIM(nombre)) = ?', [strtoupper($tipoUsuarioRaw)])->first();
            if (! $tipoUsuario) {
                $errores[] = "Fila {$filaNum}: tipo_usuario no encontrado («{$tipoUsuarioRaw}»).";
                continue;
            }

            $existente = Usuario::withTrashed()->where('documento', $documento)->first();
            if ($existente) {
                $estado = $existente->trashed() ? ' (usuario eliminado; restaure o cambie el documento en BD)' : '';
                $errores[] = "Fila {$filaNum}: ya existe un usuario con documento {$documento}.{$estado}";
                continue;
            }

            $idCasino = null;
            if (strtolower($rol->nombre) === 'casino') {
                $casino = Casino::where('nombre', trim((string) ($row['casino_asignado'] ?? '')))->where('activo', true)->first();
                if (! $casino) {
                    $errores[] = "Fila {$filaNum}: rol casino requiere casino_asignado válido.";
                    continue;
                }
                $idCasino = $casino->id_casino;
            }

            $idEmpresaTemporal = null;
            $idEmpresaContratista = null;
            $tipoNombre = strtoupper(trim($tipoUsuario->nombre));
            if ($tipoNombre === 'TEMPORAL') {
                $nombreTemp = trim((string) ($row['empresa_temporal'] ?? ''));
                $empTemp = $nombreTemp === '' ? null : EmpresaTemporal::query()
                    ->where('activa', true)
                    ->whereRaw('LOWER(TRIM(nombre)) = ?', [mb_strtolower($nombreTemp, 'UTF-8')])
                    ->first();
                if (! $empTemp) {
                    $errores[] = "Fila {$filaNum}: tipo TEMPORAL requiere columna empresa_temporal con el nombre de una empresa temporal activa (coincide sin importar mayúsculas). Valor recibido: «{$nombreTemp}».";
                    continue;
                }
                $idEmpresaTemporal = $empTemp->id_empresa_temporal;
            } elseif ($tipoNombre === 'CONTRATISTA') {
                $rawCont = trim((string) ($row['empresa_contratista'] ?? ''));
                $empCont = null;
                if ($rawCont !== '') {
                    $empCont = EmpresaContratista::query()
                        ->where('activa', true)
                        ->where(function ($q) use ($rawCont) {
                            $q->whereRaw('LOWER(TRIM(nombre)) = ?', [mb_strtolower($rawCont, 'UTF-8')])
                                ->orWhere('nit', $rawCont);
                        })
                        ->first();
                }
                if (! $empCont) {
                    $errores[] = "Fila {$filaNum}: tipo CONTRATISTA requiere columna empresa_contratista con el nombre o NIT de una empresa contratista activa. Valor recibido: «{$rawCont}».";
                    continue;
                }
                $idEmpresaContratista = $empCont->id_empresa_contratista;
            }

            $idSede = null;
            $sedeNombre = trim((string) ($row['sede_principal'] ?? ''));
            if ($sedeNombre !== '') {
                $sede = Sede::where('nombre', $sedeNombre)->first();
                if ($sede) {
                    $idSede = $sede->id_sede;
                }
            }

            $activo = ! in_array(strtolower(trim((string) ($row['activo'] ?? 'Si'))), ['no', '0', 'false'], true);

            Usuario::create([
                'documento' => $documento,
                'nombres' => $nombres,
                'password_hash' => Hash::make($documento),
                'cambiar_clave_obligatorio' => true,
                'id_empresa' => $empresa->id_empresa,
                'id_rol' => $rol->id_rol,
                'id_tipo_usuario' => $tipoUsuario->id_tipo_usuario,
                'id_empresa_temporal' => $idEmpresaTemporal,
                'id_empresa_contratista' => $idEmpresaContratista,
                'id_casino_asignado' => $idCasino,
                'id_sede_principal' => $idSede,
                'activo' => $activo,
            ]);
            $creados++;
        }

        if ($creados > 0) {
            $msg = "Se importaron {$creados} usuario(s) correctamente.";
            if (count($errores) > 0) {
                $msg .= ' Errores: ' . implode(' ', array_slice($errores, 0, 5));
                if (count($errores) > 5) {
                    $msg .= ' (+' . (count($errores) - 5) . ' más)';
                }
            }
            return back()->with('success', $msg);
        }

        return back()->with('error', count($errores) > 0 ? implode(' ', $errores) : 'No se importó ningún usuario. Revise el formato del archivo.');
    }
}
