<?php

namespace App\Services\Finances;

use App\Services\Finances\Refund\Drivers\PagarmeRefund;
use App\Services\Finances\Refund\Drivers\VoidRefund;
use Illuminate\Support\Manager;

class PaymentGatewayRefundAdapter extends Manager
{
    public const DRIVER_PAGARME = 'pagarme';
    public const DRIVER_VOID = 'void';

    protected static string $defaultDriver = self::DRIVER_PAGARME;

    public function getDefaultDriver()
    {
        return config('app.payment_gateway.default') ?? self::$defaultDriver;
    }

    public function createPagarmeDriver(): PagarmeRefund
    {
        return new PagarmeRefund();
    }

    public function createVoidDriver(): VoidRefund
    {
        return new VoidRefund();
    }

}
