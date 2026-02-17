<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Visitante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitanteController extends Controller
{
    public function index(): View
    {
        $visitantes = Visitante::orderBy('nombre')->paginate(15);
        return view('admin.visitantes.index', compact('visitantes'));
    }

    public function create(): View
    {
        $empresas = Empresa::orderBy('nombre')->get();
        return view('admin.visitantes.create', compact('empresas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|string|max:50',
            'empresa_visita' => 'nullable|string|max:255',
            'area_visita' => 'nullable|string|max:100',
        ]);
        Visitante::create($valid);
        return redirect()->route('admin.visitantes.index')->with('success', 'Visitante creado correctamente.');
    }

    public function edit(Visitante $visitante): View
    {
        $empresas = Empresa::orderBy('nombre')->get();
        return view('admin.visitantes.edit', compact('visitante', 'empresas'));
    }

    public function update(Request $request, Visitante $visitante): RedirectResponse
    {
        $valid = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|string|max:50',
            'empresa_visita' => 'nullable|string|max:255',
            'area_visita' => 'nullable|string|max:100',
        ]);
        $visitante->update($valid);
        return redirect()->route('admin.visitantes.index')->with('success', 'Visitante actualizado correctamente.');
    }

    public function destroy(Visitante $visitante): RedirectResponse
    {
        $visitante->delete();
        return redirect()->route('admin.visitantes.index')->with('success', 'Visitante eliminado correctamente.');
    }
}
