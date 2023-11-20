<?php

namespace App\Services\Checkout;

use App\Repositories\Banks\Banks;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use JsonException;
use stdClass;
use App\Client;
use App\Platform;

class BankAccountService
{

    private CheckoutBaseService $checkoutBaseService;

    private const GET_CODES = [
        404 => 'Recebedor não encontrado',
        500 => 'Recebedor inexistente',
    ];

    public function __construct(CheckoutBaseService $checkoutBaseService)
    {
        $this->checkoutBaseService = $checkoutBaseService;
    }

    /**
     * @param  string  $userId
     * @return \stdClass
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function get(string $userId): stdClass
    {
        try {
            $req = $this->checkoutBaseService->connectionConfig(null, $userId);

            $res = $req->get('bank-account');

            return $this->transform(json_decode($res->getBody()));
        } catch (BadResponseException $e) {
            $code = $e->getCode();
            $body = (string) $e->getResponse()->getBody();

            Log::warning('bank-account response error', [
                'code' => $code,
                'body' => $body,
            ]);

            if (self::GET_CODES[$code] ?? null) {
                try {
                    $response = json_decode($body, $associative = false, $depth = 512, JSON_THROW_ON_ERROR);
                    return $response;
                } catch (JsonException $e) {
                    // Invalid response, log to investigate it further
                    if (app()->bound('sentry')) {
                        \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($e, $body): void {
                            $scope->setContext('bank-account-response', ['body' => $body]);
                            \Sentry\captureException($e);
                        });
                    }
                }
            }

            throw $e;
        }
    }

    public function transform(stdClass $data): stdClass
    {
        $data->bank_name = Banks::getBankNameByCode($data->bank_code);

        return $data;
    }

    /**
     * Create Bank Account
     *
     * @param mixed $data
     * @return array
     */
    public function createBankAccount($platformId, $data): array
    {
        try {
            $req = $this->checkoutBaseService->connectionConfig($platformId, Auth::user()->id, [
                'acting_as' => 'client',
            ]);
            $res = $req->post('bank-account', ['json' => $data->all()]);
            $stream = $res->getBody();
            return [
                'error' => false,
                'message' => 'Dados criados com sucesso.',
                'response' => json_decode($stream->getContents(), true),
            ];
        } catch (BadResponseException $e) {
            throw new \Exception(\json_decode($e->getResponse()->getBody()->getContents())->message, 422);
        }

    }


}
