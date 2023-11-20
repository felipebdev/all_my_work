<?php

namespace App\Http\Middleware;

use App\Producer;
use App\Http\Traits\CustomResponseTrait;
use App\Platform;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckProducers
{
    use CustomResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $platformId = request()->route()->parameters()['platformId'];

        if (!Producer::where('platform_id', $platformId)->where('platform_user_id', Auth::user()->id) || !Platform::find($platformId)) {
            return $this->customJsonResponse(
                'Usuário sem permissão para acessar a plataforma ou a plataforma informada não existe',
                403,
                ['errors' => 'Usuário sem permissão para acessar a plataforma ou a plataforma informada não existe']
            );
        }

        return $next($request);
    }
}
