<?php

namespace App\Http\Middleware\Roles;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class Integrations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Gate::denies('integrations')) {
            throw new AuthorizationException('Sem permissão de acesso');
        }

        return $next($request);
    }
}
