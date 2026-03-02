<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Casino;
use App\Models\Empresa;
use App\Models\HorarioConsumo;
use App\Models\Precio;
use App\Models\RegistroConsumo;
use App\Models\Usuario;
use App\Models\Visitante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        return view('admin.consumos-manuales.create', compact('empresas', 'casinos', 'horarios', 'usuarios', 'visitantes', 'precios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'tipo_consumidor' => 'required|in:usuario,visitante',
            'id_usuario' => 'required_if:tipo_consumidor,usuario|nullable|exists:usuarios,id_usuario',
            'id_visitante' => 'required_if:tipo_consumidor,visitante|nullable|exists:visitantes,id_visitante',
            'id_empresa' => 'required|exists:empresas,id_empresa',
            'id_casino' => 'required|exists:casinos,id_casino',
            'id_horario' => 'required|exists:horarios_consumo,id_horario',
            'fecha_consumo' => 'required|date',
            'hora_consumo' => 'required|date_format:H:i',
            'precio_empleado' => 'required|numeric|min:0',
            'precio_casino' => 'required|numeric|min:0',
            'estado' => 'required|string|max:20',
            'direccion_entrega' => 'nullable|string|max:500',
        ]);

        $casino = \App\Models\Casino::find($valid['id_casino']);
        $tipoPedido = strtolower(trim($casino->tipo_casino ?? '')) === 'domicilio' ? 'domicilio' : 'en_sitio';

        $idUsuario = $valid['tipo_consumidor'] === 'usuario' ? $valid['id_usuario'] : null;
        $idVisitante = $valid['tipo_consumidor'] === 'visitante' ? $valid['id_visitante'] : null;

        if ($idUsuario === null && $idVisitante === null) {
            return back()->withInput()->withErrors(['tipo_consumidor' => 'Debe seleccionar un usuario o un visitante.']);
        }

        RegistroConsumo::create([
            'id_usuario' => $idUsuario,
            'id_visitante' => $idVisitante,
            'id_empresa' => $valid['id_empresa'],
            'id_casino' => $valid['id_casino'],
            'id_horario' => $valid['id_horario'],
            'fecha_consumo' => $valid['fecha_consumo'],
            'hora_consumo' => $valid['hora_consumo'],
            'precio_empleado' => $valid['precio_empleado'],
            'precio_casino' => $valid['precio_casino'],
            'estado' => $valid['estado'],
            'tipo_pedido' => $tipoPedido,
            'direccion_entrega' => $tipoPedido === 'domicilio' ? ($valid['direccion_entrega'] ?? null) : null,
            'registrado_por' => null,
        ]);

        return redirect()->route('admin.consumos-manuales.index')->with('success', 'Consumo registrado correctamente.');
    }
}
