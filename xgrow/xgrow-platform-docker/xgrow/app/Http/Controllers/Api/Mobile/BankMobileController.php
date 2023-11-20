<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Client;
use App\Http\Controllers\Controller;
use App\Platform;
use App\Http\Controllers\Mundipagg\RecipientController;
use App\Repositories\Banks\Banks;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 *
 */
class BankMobileController extends Controller
{

    /**
     * @var RecipientController
     */
    private $recipientController;

    public function __construct(RecipientController $recipientController)
    {
        $this->recipientController = $recipientController;
    }

    /**
     * @return JsonResponse
     */
    public function bankBranches(): JsonResponse
    {
        return response()->json(Banks::getBankList());
    }

    /**
     * @return JsonResponse
     */
    public function bankInformation(): JsonResponse
    {
        $platform = Platform::where('id', '=', Auth::user()->platform_id)->first();

        $client = Client::where('id', $platform->customer_id)->first();

        if ($client->bank === null) {
            return response()->json(null, 404);
        }

        $bankInfo = [
            'bank' => Banks::getBankNameByCode($client->bank) ?? '',
            'branch' => $client->branch,
            'account' => $client->account . '-' . $client->account_check_digit
        ];

        return response()->json($bankInfo);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function updateBankInformation(Request $request): JsonResponse
    {
        $platform = Platform::where('id', '=', Auth::user()->platform_id)->first();

        $client = Client::where('id', $platform->customer_id)->first();

        $request["type_person"] = $client->type_person;
        $request["document"] = $client->cpf ?? $client->cnpj;
        $request["phone_number"] = $client->phone_number;
        $request["holder_name"] = $client->holder_name;
        $request["bank"] = $request->bank;
        $request["account_type"] = $client->account_type;
        $request["branch"] = $request->branch;
        $request["branch_check_digit"] = $request->branch_check_digit;
        $request["account"] = $request->account;
        $request["account_check_digit"] = $request->account_check_digit;
        $request["zipcode"] = $client->zipzipcode;
        $request["address"] = $client->address;
        $request["number"] = $client->number;
        $request["district"] = $client->district;
        $request["city"] = $client->city;
        $request["state"] = $client->state;
        $request["complement"] = $client->complement;

        $changeRecipientBankAccount = $this->recipientController->changeRecipientBankAccount($request);

        if (method_exists($changeRecipientBankAccount, 'status') && $changeRecipientBankAccount->status() != 200) {

            return response()->json($changeRecipientBankAccount);
        } else {

            $client->bank = $request->bank;
            $client->branch = $request->branch;
            $client->branch_check_digit = $request->branch_check_digit;
            $client->account = $request->account;
            $client->account_check_digit = $request->account_check_digit;
            $client->save();
        }

        return response()->json($changeRecipientBankAccount);
    }
}
