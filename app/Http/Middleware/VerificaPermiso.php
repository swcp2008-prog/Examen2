<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificaPermiso
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$permisos): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $usuario = auth()->user();

        // Si el usuario no tiene rol, denegar acceso
        if (!$usuario->rol) {
            return response()->view('errors.403', [], 403);
        }

        // Obtener los permisos del rol del usuario
        $permisosUsuario = $usuario->rol->permisos->pluck('nombre')->toArray();

        // Verificar si tiene al menos uno de los permisos requeridos
        foreach ($permisos as $permiso) {
            if (in_array($permiso, $permisosUsuario)) {
                return $next($request);
            }
        }

        return response()->view('errors.403', [], 403);
    }
}
