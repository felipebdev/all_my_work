<?php

namespace App\Http\Middleware;

use App\Services\Auth\ActionChecker;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class ActionMiddleware
{

    /**
     * Middleware to check user actions, allowing use of "|" as OR operator
     *
     * Usage examples on routes:
     * Route::middleware('action:role1-action1') // has action1
     * Route::middleware('action:role1-action1|role1-action3') // has action1 OR action2
     *
     * @notice For logic AND, use multiple "action:" middleware, eg:
     * Route::middleware(['action:role1-action1', 'action:role2-action1']) // has action1 and action2
     *
     * @param $request
     * @param  \Closure  $next
     * @param $action
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function handle($request, Closure $next, $action)
    {
        if (!app()->runningInConsole() and !ActionChecker::authorized($action)) {
            throw new AuthorizationException('Sem permissão de acesso');
        }

        return $next($request);
    }

}
