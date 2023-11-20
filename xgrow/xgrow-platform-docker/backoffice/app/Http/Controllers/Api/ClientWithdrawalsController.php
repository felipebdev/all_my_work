<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ClientWithdrawalsService;
use Illuminate\Http\JsonResponse;
use MundiAPILib\APIException;

class ClientWithdrawalsController extends Controller
{

    private ClientWithdrawalsService $withDrawalsService;

    public function __construct(ClientWithdrawalsService $withDrawalsService){

        $this->withDrawalsService = $withDrawalsService;
    }

    /**
     * List recipient withdrawals
     * @return JsonResponse
     * @throws APIException
     */
    public function index()
    {
        return $this->withDrawalsService->getWithdrawalsByClient(0);
    }


}
