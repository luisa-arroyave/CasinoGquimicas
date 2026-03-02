<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SedeController extends Controller
{
    public function index(): View
    {
        $sedes = Sede::withCount(['casinos', 'usuariosPrincipal'])->orderBy('nombre')->paginate(15);
        return view('admin.sedes.index', compact('sedes'));
    }

    public function create(): View
    {
        return view('admin.sedes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'nombre' => 'required|string|max:120',
        ]);
        Sede::create($valid);
        return redirect()->route('admin.sedes.index')->with('success', 'Sede creada correctamente.');
    }

    public function edit(Sede $sede): View
    {
        return view('admin.sedes.edit', compact('sede'));
    }

    public function update(Request $request, Sede $sede): RedirectResponse
    {
        $valid = $request->validate([
            'nombre' => 'required|string|max:120',
        ]);
        $sede->update($valid);
        return redirect()->route('admin.sedes.index')->with('success', 'Sede actualizada correctamente.');
    }

    public function destroy(Sede $sede): RedirectResponse
    {
        $sede->delete();
        return redirect()->route('admin.sedes.index')->with('success', 'Sede eliminada correctamente.');
    }
}
