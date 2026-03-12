<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AreaVisita;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaVisitaController extends Controller
{
    public function index(): View
    {
        $areas = AreaVisita::orderBy('nombre')->paginate(15);
        return view('admin.areas.index', compact('areas'));
    }

    public function create(): View
    {
        return view('admin.areas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'nombre' => 'required|string|max:100',
            'activo' => 'boolean',
        ]);
        $valid['activo'] = $request->boolean('activo');
        AreaVisita::create($valid);
        return redirect()->route('admin.areas.index')->with('success', 'Área creada correctamente.');
    }

    public function edit(AreaVisita $area): View
    {
        return view('admin.areas.edit', compact('area'));
    }

    public function update(Request $request, AreaVisita $area): RedirectResponse
    {
        $valid = $request->validate([
            'nombre' => 'required|string|max:100',
            'activo' => 'boolean',
        ]);
        $valid['activo'] = $request->boolean('activo');
        $area->update($valid);
        return redirect()->route('admin.areas.index')->with('success', 'Área actualizada correctamente.');
    }

    public function destroy(AreaVisita $area): RedirectResponse
    {
        $area->delete();
        return redirect()->route('admin.areas.index')->with('success', 'Área eliminada correctamente.');
    }
}
