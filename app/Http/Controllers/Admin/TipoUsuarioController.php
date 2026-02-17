<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoUsuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipoUsuarioController extends Controller
{
    public function index(): View
    {
        $tipos = TipoUsuario::withCount('usuarios')->orderBy('nombre')->paginate(15);
        return view('admin.tipos-usuario.index', compact('tipos'));
    }

    public function create(): View
    {
        return view('admin.tipos-usuario.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate(['nombre' => 'required|string|max:100']);
        TipoUsuario::create($valid);
        return redirect()->route('admin.tipos-usuario.index')->with('success', 'Tipo de usuario creado correctamente.');
    }

    public function edit(TipoUsuario $tipos_usuario): View
    {
        return view('admin.tipos-usuario.edit', ['tipoUsuario' => $tipos_usuario]);
    }

    public function update(Request $request, TipoUsuario $tipos_usuario): RedirectResponse
    {
        $valid = $request->validate(['nombre' => 'required|string|max:100']);
        $tipos_usuario->update($valid);
        return redirect()->route('admin.tipos-usuario.index')->with('success', 'Tipo de usuario actualizado correctamente.');
    }

    public function destroy(TipoUsuario $tipos_usuario): RedirectResponse
    {
        $tipos_usuario->delete();
        return redirect()->route('admin.tipos-usuario.index')->with('success', 'Tipo de usuario eliminado correctamente.');
    }
}
