<?php


namespace App\Services\Checkout;


use App\Http\Traits\CustomResponseTrait;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WithdrawService
{
    use CustomResponseTrait;

    private CheckoutBaseService $checkoutBaseService;

    public function __construct(CheckoutBaseService $checkoutBaseService)
    {
        $this->checkoutBaseService = $checkoutBaseService;
    }

    public function createTransfer(String $platformId, String $actingAs, array $request): array
    {
        try {
            $req = $this->checkoutBaseService->connectionConfig($platformId, Auth::user()->id, [
                'acting_as' => $actingAs,
            ]);

            $res = $req->post('transfers', ['json' => $request]);

            return ['data' => json_decode($res->getBody()), 'code' => 200];

        } catch (ClientException $e) {

            Log::info('Resposta da criacao de saque do afiliado', ['resp' => (string)$e->getResponse()->getBody()]);

            return ['data' => json_decode($e->getResponse()->getBody()), 'code' => 400];
        }

    }
}
