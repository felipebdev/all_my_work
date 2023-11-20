<?php

namespace App\Services\Checkout;

class BalanceService
{
    private CheckoutBaseService $checkoutBaseService;

    public function __construct(CheckoutBaseService $checkoutBaseService)
    {
        $this->checkoutBaseService = $checkoutBaseService;
    }

    /**
     * Get balance of a user from a platform (client)
     *
     * @param  string  $platformId
     * @param $userId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getUserClientBalance(string $platformId, $userId)
    {
        $req = $this->checkoutBaseService->connectionConfig($platformId, $userId, [
            'acting_as' => 'client',
        ]);
        $res = $req->get('balance');
        $stream = $res->getBody();
        return json_decode($stream->getContents());
    }

}
