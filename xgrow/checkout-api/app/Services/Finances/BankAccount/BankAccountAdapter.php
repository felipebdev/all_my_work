<?php

namespace App\Services\Finances\BankAccount;

use App\Services\Finances\BankAccount\Drivers\PagarmeBankAccount;
use App\Services\Finances\BankAccount\Drivers\VoidBankAccount;
use Illuminate\Support\Manager;

class BankAccountAdapter extends Manager
{
    public const DRIVER_PAGARME = 'pagarme';
    public const DRIVER_VOID = 'void';

    protected static string $defaultDriver = self::DRIVER_PAGARME;

    public function getDefaultDriver()
    {
        return config('app.payment_gateway.default') ?? self::$defaultDriver;
    }

    public function createPagarmeDriver(): PagarmeBankAccount
    {
        return new PagarmeBankAccount();
    }

    public function createVoidDriver(): VoidBankAccount
    {
        return new VoidBankAccount();
    }

}
