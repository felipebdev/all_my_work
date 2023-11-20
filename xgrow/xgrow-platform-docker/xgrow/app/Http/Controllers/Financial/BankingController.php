<?php

namespace App\Http\Controllers\Financial;

use App\PlatformUser;
use App\Services\Checkout\BalanceService;
use App\Services\Checkout\BankAccountService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use function response;

/**
 * This controller uses Financial API
 */
class BankingController
{
    private BankAccountService $bankAccountService;
    private BalanceService $balanceService;

    public function __construct(BankAccountService $bankAccountService, BalanceService $balanceService)
    {
        $this->bankAccountService = $bankAccountService;
        $this->balanceService = $balanceService;
    }

    public function getBankAccountData()
    {
        $owner = $this->getPlatformOwner(Auth::user()->platform_id);

        $userId = Auth::user()->id;

        if ($owner) {
            $userId = $owner->id;
        }

        $data = $this->bankAccountService->get($userId);

        $converted = [
            'default_bank_account' => [
                'holder_name' => $data->legal_name ?? null,
                'bank' => $data->bank_code ?? null,
                'branch_number' => $data->agency ?? null,
                'branch_check_digit' => $data->agency_digit ?? null,
                'account_number' => $data->account ?? null,
                'account_check_digit' => $data->account_digit ?? null,
            ],
            'document' => $data->document_number ?? null,
        ];

        return response()->json($converted);
    }

    /**
     * Get balance of logged user
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getClientBalance()
    {
        try {
            $platformId = Auth::user()->platform_id;

            $owner = $this->getPlatformOwner($platformId);

            $userId = Auth::user()->id;

            if ($owner) {
                $userId = $owner->id;
            }

            $data = $this->balanceService->getUserClientBalance($platformId, $userId);

            $response = [
                'current_amount' => $data->current,
                'available_amount' => $data->available,
                'waiting_funds_amount' => $data->pending,
                'transferred_amount' => $data->transferred,
                'anticipation_amount' => $data->anticipation,
            ];

            return response()->json($response);
        } catch (ClientException $e) {
            if ($e->getCode() == 404) {
                $body = $e->getResponse()->getBody();
                $json = json_decode($body);
                return response()->json($json, Response::HTTP_OK); // Recebedor não encontrado na plataforma
            }

            return response()->json($e, Response::HTTP_BAD_REQUEST);
        }
    }

    public function getPlatformOwner($platformId)
    {
        return PlatformUser::select(
            'platforms_users.id',
        )
            ->join('clients', 'platforms_users.email', '=', 'clients.email')
            ->join('platforms', 'platforms.customer_id', '=', 'clients.id')
            ->where('platforms.id', $platformId)
            ->first();
    }

}
