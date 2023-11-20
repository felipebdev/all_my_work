<?php

namespace App\Http\Controllers\Getnet;

use App\Services\Getnet\PlanService;
use Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\Getnet\ClientService as GetnetClientService;

class ClientController extends Controller
{
    private $getnetClientService;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $this->getnetClientService = new GetnetClientService($user->platform_id);
            return $next($request);
        });
    }

    public function index()
    {
        $customers = $this->getnetClientService->index();

        return view('getnet.clients.index', compact('customers'));
    }

    public function getCustomer($customerId)
    {
        $customer = $this->getnetClientService->getCustomer($customerId);

        return view('getnet.clients.edit', compact('customer'));
    }

    public function store(Request $request)
    {
        // é usado no SubscriberController\store;
    }
}
