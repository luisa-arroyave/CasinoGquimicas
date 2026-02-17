<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('empresas')->orderBy('name')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $empresas = Empresa::orderBy('nombre')->get();
        return view('admin.users.create', compact('empresas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $valid = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:administrador,gestionhumana,casino,operativo,empleado',
            'empresas' => 'nullable|array',
            'empresas.*' => 'exists:empresas,id_empresa',
        ]);
        $valid['password'] = Hash::make($valid['password']);
        $empresas = $valid['empresas'] ?? [];
        unset($valid['empresas']);
        $user = User::create($valid);
        $user->empresas()->sync($empresas);
        return redirect()->route('admin.users.index')->with('success', 'Usuario del sistema creado correctamente.');
    }

    public function edit(User $user): View
    {
        $user->load('empresas');
        $empresas = Empresa::orderBy('nombre')->get();
        return view('admin.users.edit', compact('user', 'empresas'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $valid = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|in:administrador,gestionhumana,casino,operativo,empleado',
            'empresas' => 'nullable|array',
            'empresas.*' => 'exists:empresas,id_empresa',
        ]);
        if (! empty($valid['password'])) {
            $valid['password'] = Hash::make($valid['password']);
        } else {
            unset($valid['password']);
        }
        $empresas = $valid['empresas'] ?? [];
        unset($valid['empresas']);
        $user->update($valid);
        $user->empresas()->sync($empresas);
        return redirect()->route('admin.users.index')->with('success', 'Usuario del sistema actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'No puede eliminar su propia cuenta.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario del sistema eliminado correctamente.');
    }
}
