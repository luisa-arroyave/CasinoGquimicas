<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Role;
use App\Models\TipoUsuario;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $query = Usuario::with(['empresa', 'rol', 'tipoUsuario']);
        if ($request->filled('empresa')) {
            $query->where('id_empresa', $request->empresa);
        }
        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->where(function ($qry) use ($q) {
                $qry->where('documento', 'like', "%{$q}%")
                    ->orWhere('nombres', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }
        $usuarios = $query->orderBy('nombres')->paginate(15)->withQueryString();
        $empresas = Empresa::orderBy('nombre')->get();
        return view('admin.usuarios.index', compact('usuarios', 'empresas'));
    }

    public function create(): View
    {
        $empresas = Empresa::orderBy('nombre')->get();
        $roles = Role::orderBy('nombre')->get();
        $tipos = TipoUsuario::orderBy('nombre')->get();
        return view('admin.usuarios.create', compact('empresas', 'roles', 'tipos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'documento' => 'required|string|max:50',
            'nombres' => 'required|string|max:255',
            'email' => 'nullable|email',
            'password' => 'nullable|string|min:6|confirmed',
            'id_empresa' => 'required|exists:empresas,id_empresa',
            'id_rol' => 'required|exists:roles,id_rol',
            'id_tipo_usuario' => 'required|exists:tipos_usuario,id_tipo_usuario',
            'codigo_qr' => 'nullable|string|max:100',
            'activo' => 'boolean',
            'empresas' => 'nullable|array',
            'empresas.*' => 'exists:empresas,id_empresa',
        ]);
        $valid['activo'] = $request->boolean('activo');
        if (! empty($valid['password'])) {
            $valid['password_hash'] = Hash::make($valid['password']);
        }
        unset($valid['password'], $valid['empresas']);
        $usuario = Usuario::create($valid);
        $this->syncEmpresasAcceso($usuario, $request->input('empresas', []));
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    public function edit(Usuario $usuario): View
    {
        $usuario->load('empresasAcceso');
        $empresas = Empresa::orderBy('nombre')->get();
        $roles = Role::orderBy('nombre')->get();
        $tipos = TipoUsuario::orderBy('nombre')->get();
        return view('admin.usuarios.edit', compact('usuario', 'empresas', 'roles', 'tipos'));
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $valid = $request->validate([
            'documento' => 'required|string|max:50',
            'nombres' => 'required|string|max:255',
            'email' => 'nullable|email',
            'password' => 'nullable|string|min:6|confirmed',
            'id_empresa' => 'required|exists:empresas,id_empresa',
            'id_rol' => 'required|exists:roles,id_rol',
            'id_tipo_usuario' => 'required|exists:tipos_usuario,id_tipo_usuario',
            'codigo_qr' => 'nullable|string|max:100',
            'activo' => 'boolean',
            'empresas' => 'nullable|array',
            'empresas.*' => 'exists:empresas,id_empresa',
        ]);
        $valid['activo'] = $request->boolean('activo');
        if (! empty($valid['password'])) {
            $valid['password_hash'] = Hash::make($valid['password']);
        }
        unset($valid['password'], $valid['empresas']);
        $usuario->update($valid);
        $this->syncEmpresasAcceso($usuario, $request->input('empresas', []));
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Sincronizar empresas de acceso (solo para rol administrador o gestionhumana).
     */
    private function syncEmpresasAcceso(Usuario $usuario, array $empresasIds): void
    {
        $rol = $usuario->rol;
        if (! $rol || ! in_array($rol->nombre, ['administrador', 'gestionhumana'], true)) {
            $usuario->empresasAcceso()->sync([]);
            return;
        }
        $usuario->empresasAcceso()->sync($empresasIds);
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
        $usuario->delete();
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
