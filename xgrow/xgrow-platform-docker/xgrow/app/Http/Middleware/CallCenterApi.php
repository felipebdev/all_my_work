<?php

namespace App\Http\Middleware;

use App\Attendant;
use Closure;
use Exception;
use Illuminate\Http\Request;

class CallCenterApi
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

            $request = request();
            $api_token = $request->bearerToken();

            $attendant = Attendant::where('api_token', $api_token)->first();

            if (!$attendant){
                return $this->unauthorizedUser();
            }else{
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
