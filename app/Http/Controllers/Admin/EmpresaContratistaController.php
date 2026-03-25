<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmpresaContratista;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmpresaContratistaController extends Controller
{
    public function index(): View
    {
        $empresas = EmpresaContratista::withCount('usuarios')->orderBy('nombre')->paginate(15);

        return view('admin.empresas-contratistas.index', compact('empresas'));
    }

    public function create(): View
    {
        return view('admin.empresas-contratistas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'nit' => ['required', 'string', 'max:30', Rule::unique('empresas_contratistas', 'nit')],
            'nombre' => 'required|string|max:255',
            'activa' => 'boolean',
        ]);
        $valid['activa'] = $request->boolean('activa', true);
        EmpresaContratista::create($valid);

        return redirect()->route('admin.empresas-contratistas.index')->with('success', 'Empresa contratista creada correctamente.');
    }

    public function edit(EmpresaContratista $empresaContratista): View
    {
        return view('admin.empresas-contratistas.edit', compact('empresaContratista'));
    }

    public function update(Request $request, EmpresaContratista $empresaContratista): RedirectResponse
    {
        $valid = $request->validate([
            'nit' => ['required', 'string', 'max:30', Rule::unique('empresas_contratistas', 'nit')->ignore($empresaContratista->id_empresa_contratista, 'id_empresa_contratista')],
            'nombre' => 'required|string|max:255',
            'activa' => 'boolean',
        ]);
        $valid['activa'] = $request->boolean('activa');
        $empresaContratista->update($valid);

        return redirect()->route('admin.empresas-contratistas.index')->with('success', 'Empresa contratista actualizada correctamente.');
    }

    public function destroy(EmpresaContratista $empresaContratista): RedirectResponse
    {
        if ($empresaContratista->usuarios()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: hay usuarios asociados.');
        }
        $empresaContratista->delete();

        return redirect()->route('admin.empresas-contratistas.index')->with('success', 'Empresa contratista eliminada correctamente.');
    }
}
