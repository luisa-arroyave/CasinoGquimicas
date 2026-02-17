<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Casino;
use App\Models\HorarioConsumo;
use App\Models\Precio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrecioController extends Controller
{
    public function index(): View
    {
        $precios = Precio::with(['horarioConsumo', 'casino'])->orderBy('id_horario')->paginate(15);
        return view('admin.precios.index', compact('precios'));
    }

    public function create(): View
    {
        $horarios = HorarioConsumo::where('activo', true)->orderBy('hora_inicio')->get();
        $casinos = Casino::where('activo', true)->orderBy('nombre')->get();
        return view('admin.precios.create', compact('horarios', 'casinos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'id_horario' => 'required|exists:horarios_consumo,id_horario',
            'id_casino' => 'nullable|exists:casinos,id_casino',
            'precio_empleado' => 'required|numeric|min:0',
            'precio_casino' => 'required|numeric|min:0',
        ]);
        Precio::create($valid);
        return redirect()->route('admin.precios.index')->with('success', 'Precio creado correctamente.');
    }

    public function edit(Precio $precio): View
    {
        $horarios = HorarioConsumo::where('activo', true)->orderBy('hora_inicio')->get();
        $casinos = Casino::where('activo', true)->orderBy('nombre')->get();
        return view('admin.precios.edit', compact('precio', 'horarios', 'casinos'));
    }

    public function update(Request $request, Precio $precio): RedirectResponse
    {
        $valid = $request->validate([
            'id_horario' => 'required|exists:horarios_consumo,id_horario',
            'id_casino' => 'nullable|exists:casinos,id_casino',
            'precio_empleado' => 'required|numeric|min:0',
            'precio_casino' => 'required|numeric|min:0',
        ]);
        $precio->update($valid);
        return redirect()->route('admin.precios.index')->with('success', 'Precio actualizado correctamente.');
    }

    public function destroy(Precio $precio): RedirectResponse
    {
        $precio->delete();
        return redirect()->route('admin.precios.index')->with('success', 'Precio eliminado correctamente.');
    }
}
