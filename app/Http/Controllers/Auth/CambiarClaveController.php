<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CambiarClaveController extends Controller
{
    /**
     * Mostrar formulario para cambiar contraseña (primera vez u obligatorio).
     */
    public function showForm(): View
    {
        return view('auth.cambiar-clave');
    }

    /**
     * Procesar cambio de contraseña.
     */
    public function cambiar(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $valid = $request->validate([
            'password_actual' => ['required'],
            'password' => ['required', 'confirmed', 'min:6'],
        ], [
            'password_actual.required' => 'La contraseña actual es obligatoria.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        if (! Hash::check($valid['password_actual'], $user->getAuthPassword())) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.'])->withInput();
        }

        $user->password_hash = Hash::make($valid['password']);
        $user->cambiar_clave_obligatorio = false;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Contraseña actualizada correctamente. Ya puede usar su nueva contraseña en el próximo inicio de sesión.');
    }
}
