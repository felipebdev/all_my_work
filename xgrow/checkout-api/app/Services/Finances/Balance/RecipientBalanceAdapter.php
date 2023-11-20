<?php

namespace App\Services\Finances\Balance;

use App\Services\Finances\Balance\Drivers\PagarmeRecipientBalance;
use Illuminate\Support\Manager;

use function config;

class RecipientBalanceAdapter extends Manager
{
    public const DRIVER_PAGARME = 'pagarme';

    protected static string $defaultDriver = self::DRIVER_PAGARME;

    public function getDefaultDriver()
    {
        return config('app.payment_gateway.default') ?? self::$defaultDriver;
    }

    public function createPagarmeDriver(): PagarmeRecipientBalance
    {
        return new PagarmeRecipientBalance();
    }

}
