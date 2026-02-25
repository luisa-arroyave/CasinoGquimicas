<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmpresaController extends Controller
{
    public function index(): View
    {
        $empresas = Empresa::withCount('usuarios')->orderBy('nombre')->paginate(15);
        return view('admin.empresas.index', compact('empresas'));
    }

    public function create(): View
    {
        return view('admin.empresas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'NIT' => 'required|string|max:20',
            'nombre' => 'required|string|max:255',
            'activa' => 'boolean',
            'correos_cuenta_cobro' => 'nullable|string|max:1000',
        ]);
        $valid['activa'] = $request->boolean('activa');
        Empresa::create($valid);
        return redirect()->route('admin.empresas.index')->with('success', 'Empresa creada correctamente.');
    }

    public function edit(Empresa $empresa): View
    {
        return view('admin.empresas.edit', compact('empresa'));
    }

    public function update(Request $request, Empresa $empresa): RedirectResponse
    {
        $valid = $request->validate([
            'NIT' => 'required|string|max:20',
            'nombre' => 'required|string|max:255',
            'activa' => 'boolean',
            'correos_cuenta_cobro' => 'nullable|string|max:1000',
        ]);
        $valid['activa'] = $request->boolean('activa');
        $empresa->update($valid);
        return redirect()->route('admin.empresas.index')->with('success', 'Empresa actualizada correctamente.');
    }

    public function destroy(Empresa $empresa): RedirectResponse
    {
        $empresa->delete();
        return redirect()->route('admin.empresas.index')->with('success', 'Empresa eliminada correctamente.');
    }
}
