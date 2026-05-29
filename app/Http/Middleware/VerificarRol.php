<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerificarRol
{
    /**
     * Verifica que el usuario autenticado tenga el rol requerido.
     *
     * Uso en rutas: middleware('rol:admin') o middleware('rol:cliente')
     *
     * @param  string  $rol  Nombre del rol esperado (coincide con roles.nombre)
     */
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        // Si no está autenticado, redirige al login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Compara el nombre del rol del usuario con el rol requerido
        if (Auth::user()->rol->nombre !== $rol) {
            abort(403, 'No tenés permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
