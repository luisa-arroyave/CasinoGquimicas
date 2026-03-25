<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HorarioConsumo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function index(): View
    {
        $horarios = HorarioConsumo::orderBy('hora_inicio')->paginate(15);
        return view('admin.horarios.index', compact('horarios'));
    }

    public function create(): View
    {
        return view('admin.horarios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'nombre' => 'required|string|max:100',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
            'activo' => 'boolean',
        ]);
        $valid['activo'] = $request->boolean('activo');
        HorarioConsumo::create($valid);
        return redirect()->route('admin.horarios.index')->with('success', 'Horario creado correctamente.');
    }

    public function edit(HorarioConsumo $horario): View
    {
        return view('admin.horarios.edit', compact('horario'));
    }

    public function update(Request $request, HorarioConsumo $horario): RedirectResponse
    {
        $valid = $request->validate([
            'nombre' => 'required|string|max:100',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
            'activo' => 'boolean',
        ]);
        $valid['activo'] = $request->boolean('activo');
        $horario->update($valid);
        return redirect()->route('admin.horarios.index')->with('success', 'Horario actualizado correctamente.');
    }

    public function destroy(HorarioConsumo $horario): RedirectResponse
    {
        $horario->delete();
        return redirect()->route('admin.horarios.index')->with('success', 'Horario eliminado correctamente.');
    }
}
