<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Obtiene la ruta a la que se debe redirigir si el usuario no está autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Como es API, no se redirige a ningún lado, solo devuelve 401.
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
