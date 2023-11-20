<?php

namespace App\Http\Middleware;

use App\Services\Auth\RoleChecker;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class RoleMiddleware
{

    /**
     * Middleware to check user roles, allowing use of "|" as OR operator
     *
     * Usage examples on routes:
     * Route::middleware('role:role1') // has role1
     * Route::middleware('role:role1|role2') // has role1 OR role2
     *
     * @notice For logic AND, use multiple "role:" middleware, eg:
     * Route::middleware(['role:role1', 'role:role2]) // has role1 and role2
     *
     * @param $request
     * @param  \Closure  $next
     * @param $role
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle($request, Closure $next, $role)
    {
        if (!RoleChecker::authorized($role)) {
            throw new AuthorizationException('Sem permissão de acesso');
        }

        return $next($request);
    }

}
