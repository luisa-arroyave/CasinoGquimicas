<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Comprobar que el usuario autenticado tenga uno de los roles indicados.
     * Uso: ->middleware('role:admin,manager')
     *
     * @param  array<string>  $roles  Slugs de roles permitidos (ej: admin, manager)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();
        $userRole = $user->role ?? null;

        if (! $userRole || ! in_array($userRole, $roles, true)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
