<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
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
         try {
            if (!(isset(auth('api')->user()->platform_id)) or auth('api')->user()->platform_id != $request->platform_id){
                return $this->unauthorizedUser();
            }else{
                try {
                    $user = JWTAuth::parseToken()->authenticate();
                } catch (Exception $e) {
                    if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                        return response()->json(['status' => 'Token is Invalid']);
                    }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                        return response()->json(['status' => 'Token is Expired']);
                    }else{
                        return response()->json(['status' => 'Authorization Token not found']);
                    }
                }

                //último acesso
                date_default_timezone_set('America/Sao_Paulo');
                $dataHora = date('Y-m-d H:i:s');
                auth('api')->user()->update(['last_acess' => $dataHora]);

                return $next($request);
            }
        }catch (Exception $e) {
            return $this->unauthorizedUser();
        }
        
    }

    private function unauthorizedUser(){
       return response()->json(['status' => 'Unauthorized user'], 401);
    }

}
