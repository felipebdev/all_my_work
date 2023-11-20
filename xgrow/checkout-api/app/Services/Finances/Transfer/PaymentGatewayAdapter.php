<?php

namespace App\Services\Finances\Transfer;

use App\Services\Finances\Transfer\Drivers\PagarmeTransfer;
use App\Services\Finances\Transfer\Drivers\VoidTransfer;
use Illuminate\Support\Manager;

class PaymentGatewayAdapter extends Manager
{
    public const DRIVER_PAGARME = 'pagarme';
    public const DRIVER_VOID = 'void';

    protected static string $defaultDriver = self::DRIVER_VOID;

    public function getDefaultDriver()
    {
        return config('app.payment_gateway.default') ?? self::$defaultDriver;
    }

    public function createPagarmeDriver(): PagarmeTransfer
    {
        return new PagarmeTransfer();
    }

    public function createVoidDriver(): VoidTransfer
    {
        return new VoidTransfer();
    }

}
