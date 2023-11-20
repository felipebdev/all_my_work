<?php

namespace Tests\Feature\Services\Finances\Balance;

use App\Services\Finances\Balance\Drivers\PagarmeRecipientBalance;
use App\Services\Finances\Balance\RecipientBalanceAdapter;
use InvalidArgumentException;
use Tests\TestCase;

class RecipientBalanceAdapterTest extends TestCase
{

    private RecipientBalanceAdapter $adapter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = $this->app->make(RecipientBalanceAdapter::class);
    }

    public function test_setting_pagarme_driver()
    {
        $gateway = $this->adapter->driver(RecipientBalanceAdapter::DRIVER_PAGARME);
        $this->assertInstanceOf(PagarmeRecipientBalance::class, $gateway);
    }

    public function test_non_existing_driver()
    {
        $this->expectException(InvalidArgumentException::class);
        $gateway = $this->adapter->driver('non_existing_driver');
    }

}
