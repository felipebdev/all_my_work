<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Requests\UpdateBankInformationFirstAccessRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Repositories\Banks\Banks;
use App\Services\Checkout\BankAccountService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BankDataController extends Controller
{
    use CustomResponseTrait;

    private BankAccountService $bankAccountService;

    public function __construct(BankAccountService $bankAccountService)
    {
        $this->bankAccountService = $bankAccountService;
    }

    public function getBankList()
    {
        return Banks::getBankList();
    }

    /**
     * @param UpdateBankInformationFirstAccessRequest $request
     * @return JsonResponse
     */
    public function updateBankInformationFirstAccess(UpdateBankInformationFirstAccessRequest $request): JsonResponse
    {
        try {
            $client = Client::where('email', Auth::user()->email)->first();

            $client->holder_name = $request->input('holder_name');
            $client->bank = $request->input('bank');
            $client->branch = $request->input('branch');
            $client->branch_check_digit = $request->input('branch_check_digit');
            $client->account = $request->input('account');
            $client->account_check_digit = $request->input('account_check_digit');
            $client->account_type = $request->input('account_type');
            $client->save();

            return $this->customJsonResponse('Dados bancarios atualizados com sucesso!', 201);
        } catch (Exception $e) {
            return $this->customJsonResponse(
                'Falha ao realizar ação',
                $e->getCode(),
                ['errors' => json_decode($e->getMessage())]
            );
        }
    }
}
