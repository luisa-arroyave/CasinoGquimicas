<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Procesar login (por número de documento, usando tabla usuarios).
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'documento' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'documento.required' => 'El número de documento es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'documento' => __('Las credenciales no coinciden con nuestros registros.'),
        ])->onlyInput('documento');
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Mostrar formulario de registro.
     *
     * En este sistema, los usuarios se crean desde Admin → Usuarios,
     * así que se deshabilita el registro público.
     */
    public function showRegisterForm(): View
    {
        abort(404);
    }

    /**
     * Procesar registro de nuevo usuario (deshabilitado).
     */
    public function register(Request $request): RedirectResponse
    {
        abort(404);
    }
}
