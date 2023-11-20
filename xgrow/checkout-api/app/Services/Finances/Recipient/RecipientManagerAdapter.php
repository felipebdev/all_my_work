<?php

namespace App\Services\Finances\Recipient;

use App\Services\Finances\Recipient\Drivers\PagarmeV4RecipientManager;
use App\Services\Finances\Recipient\Drivers\PagarmeV5RecipientManager;
use App\Services\Finances\Recipient\Drivers\VoidRecipientManager;
use Illuminate\Support\Manager;

use function config;

/**
 * This class instantiates the correct driver for the recipient manager.
 */
class RecipientManagerAdapter extends Manager
{
    public const DRIVER_PAGARME = 'pagarme';
    public const DRIVER_PAGARME_V4 = 'pagarmev4';
    public const DRIVER_VOID = 'void';

    protected static string $defaultDriver = self::DRIVER_PAGARME;

    public function getDefaultDriver()
    {
        return config('app.payment_gateway.default') ?? self::$defaultDriver;
    }

    public function createPagarmeDriver(): PagarmeV5RecipientManager
    {
        return new PagarmeV5RecipientManager();
    }

    public function createPagarmev4Driver(): PagarmeV4RecipientManager
    {
        return new PagarmeV4RecipientManager();
    }

    public function createVoidDriver(): VoidRecipientManager
    {
        return new VoidRecipientManager();
    }

}
