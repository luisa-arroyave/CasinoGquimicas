<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Si el usuario debe cambiar su contraseña, redirigir a la página de cambio
     * excepto cuando ya está en esa ruta o en logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->cambiar_clave_obligatorio) {
            if ($request->routeIs('cambiar-clave.*') || $request->routeIs('logout')) {
                return $next($request);
            }
            return redirect()->route('cambiar-clave.show');
        }

        return $next($request);
    }
}
