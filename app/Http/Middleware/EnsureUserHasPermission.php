<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /**
     * Comprobar que el usuario autenticado tenga el permiso indicado (vía su rol).
     * Uso: ->middleware('permission:users.create')
     *
     * @param  string  $permission  Slug del permiso requerido
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();
        $hasPermission = method_exists($user, 'hasPermission') ? $user->hasPermission($permission) : false;
        if (! $hasPermission) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return $next($request);
    }
}
