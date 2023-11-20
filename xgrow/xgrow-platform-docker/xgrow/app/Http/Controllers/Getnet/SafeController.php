<?php

namespace App\Http\Controllers\Getnet;

use Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\Getnet\SafeService as GetnetSafeService;

class SafeController extends Controller
{
    private $getnetClientService;

    public function __construct($platform_id)
    {

        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $this->getnetSafeService = new GetnetSafeService($platform_id);
            return $next($request);
        });

    }

    public function index()
    {

    }

}
