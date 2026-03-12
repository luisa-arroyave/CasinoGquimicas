<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmpresaTemporal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmpresaTemporalController extends Controller
{
    public function index(): View
    {
        $empresas = EmpresaTemporal::withCount('usuarios')->orderBy('nombre')->paginate(15);
        return view('admin.empresas-temporales.index', compact('empresas'));
    }

    public function create(): View
    {
        return view('admin.empresas-temporales.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'nombre' => 'required|string|max:255',
            'activa' => 'boolean',
        ]);
        $valid['activa'] = $request->boolean('activa', true);
        EmpresaTemporal::create($valid);
        return redirect()->route('admin.empresas-temporales.index')->with('success', 'Empresa temporal creada correctamente.');
    }

    public function edit(EmpresaTemporal $empresa_temporal): View
    {
        return view('admin.empresas-temporales.edit', ['empresaTemporal' => $empresa_temporal]);
    }

    public function update(Request $request, EmpresaTemporal $empresa_temporal): RedirectResponse
    {
        $valid = $request->validate([
            'nombre' => 'required|string|max:255',
            'activa' => 'boolean',
        ]);
        $valid['activa'] = $request->boolean('activa');
        $empresa_temporal->update($valid);
        return redirect()->route('admin.empresas-temporales.index')->with('success', 'Empresa temporal actualizada correctamente.');
    }

    public function destroy(EmpresaTemporal $empresa_temporal): RedirectResponse
    {
        if ($empresa_temporal->usuarios()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: hay usuarios asociados.');
        }
        $empresa_temporal->delete();
        return redirect()->route('admin.empresas-temporales.index')->with('success', 'Empresa temporal eliminada correctamente.');
    }
}
