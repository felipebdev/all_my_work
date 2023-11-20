<?php

namespace App\Services\Checkout;

use App\Services\Checkout\CheckoutBaseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

class TransferService
{
    private CheckoutBaseService $checkoutBaseService;

    public function __construct(CheckoutBaseService $checkoutBaseService)
    {
        $this->checkoutBaseService = $checkoutBaseService;
    }

    /** List All Transfers
     * @return void
     * @throws BindingResolutionException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws DomainException
     */
    public function listTransfers()
    {
        // $res = $this->checkoutBaseService->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
        $res = $this->checkoutBaseService->connectionConfig('3eb10706-83f3-4334-8eaa-f0b91e5206aa', 238, ['recipient_id' => 're_ckmns1txw01sw0h9tjm2wmv7u']);
        $res->get('transfers');
        dd($res);
    }
}
