<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Casino;
use App\Models\Empresa;
use App\Models\HorarioConsumo;
use App\Models\Precio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CasinoController extends Controller
{
    public function index(): View
    {
        $casinos = Casino::with('empresa')->orderBy('nombre')->paginate(15);
        return view('admin.casinos.index', compact('casinos'));
    }

    public function create(): View
    {
        $empresas = Empresa::where('activa', true)->orderBy('nombre')->get();
        $horarios = HorarioConsumo::where('activo', true)->orderBy('hora_inicio')->get();
        return view('admin.casinos.create', compact('empresas', 'horarios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'NIT' => 'required|string|max:20',
            'nombre' => 'required|string|max:255',
            'id_empresa' => 'required|exists:empresas,id_empresa',
            'tipo_casino' => 'nullable|string|max:50',
            'activo' => 'boolean',
        ]);
        $valid['activo'] = $request->boolean('activo');
        $casino = Casino::create($valid);

        $this->syncPreciosFromRequest($request, $casino);

        return redirect()->route('admin.casinos.index')->with('success', 'Casino creado correctamente.');
    }

    public function edit(Casino $casino): View
    {
        $empresas = Empresa::where('activa', true)->orderBy('nombre')->get();
        $horarios = HorarioConsumo::where('activo', true)->orderBy('hora_inicio')->get();
        $casino->load('precios.horarioConsumo');
        return view('admin.casinos.edit', compact('casino', 'empresas', 'horarios'));
    }

    public function update(Request $request, Casino $casino): RedirectResponse
    {
        $valid = $request->validate([
            'NIT' => 'required|string|max:20',
            'nombre' => 'required|string|max:255',
            'id_empresa' => 'required|exists:empresas,id_empresa',
            'tipo_casino' => 'nullable|string|max:50',
            'activo' => 'boolean',
        ]);
        $valid['activo'] = $request->boolean('activo');
        $casino->update($valid);

        $this->syncPreciosFromRequest($request, $casino);

        return redirect()->route('admin.casinos.index')->with('success', 'Casino actualizado correctamente.');
    }

    /**
     * Crea o actualiza precios del casino según el array precios[] del request.
     */
    private function syncPreciosFromRequest(Request $request, Casino $casino): void
    {
        $precios = $request->input('precios', []);
        $horarioIds = array_keys(array_filter($precios, function ($p) {
            return isset($p['precio_empleado']) || isset($p['precio_casino']);
        }));

        foreach ($precios as $idHorario => $row) {
            $idHorario = (int) $idHorario;
            if ($idHorario <= 0) {
                continue;
            }
            $pe = isset($row['precio_empleado']) ? (float) $row['precio_empleado'] : 0;
            $pc = isset($row['precio_casino']) ? (float) $row['precio_casino'] : 0;
            Precio::updateOrCreate(
                [
                    'id_casino' => $casino->id_casino,
                    'id_horario' => $idHorario,
                ],
                [
                    'precio_empleado' => $pe,
                    'precio_casino' => $pc,
                ]
            );
        }
    }

    public function destroy(Casino $casino): RedirectResponse
    {
        $casino->delete();
        return redirect()->route('admin.casinos.index')->with('success', 'Casino eliminado correctamente.');
    }
}
