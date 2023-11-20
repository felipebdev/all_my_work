<?php

namespace Tests\Feature\Services\Finances\Recipient;

use App\Services\Finances\Recipient\Contracts\RecipientManagerInterface;
use App\Services\Finances\Recipient\Drivers\PagarmeV4RecipientManager;
use App\Services\Finances\Recipient\Drivers\PagarmeV5RecipientManager;
use App\Services\Finances\Recipient\Drivers\VoidRecipientManager;
use App\Services\Finances\Recipient\RecipientManagerAdapter;
use InvalidArgumentException;
use Tests\TestCase;

class RecipientManagerAdapterTest extends TestCase
{

    private RecipientManagerAdapter $adapter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adapter = $this->app->make(RecipientManagerAdapter::class);
    }

    public function test_default_driver_implements_interface()
    {
        $gateway = $this->adapter->driver();
        $this->assertInstanceOf(RecipientManagerInterface::class, $gateway);
    }

    public function test_setting_pagarme_driver()
    {
        $gateway = $this->adapter->driver(RecipientManagerAdapter::DRIVER_PAGARME);
        $this->assertInstanceOf(PagarmeV5RecipientManager::class, $gateway);
    }

    public function test_setting_pagarme_v4_driver()
    {
        $gateway = $this->adapter->driver(RecipientManagerAdapter::DRIVER_PAGARME_V4);
        $this->assertInstanceOf(PagarmeV4RecipientManager::class, $gateway);
    }

    public function test_void_driver()
    {
        $gateway = $this->adapter->driver(RecipientManagerAdapter::DRIVER_VOID);
        $this->assertInstanceOf(VoidRecipientManager::class, $gateway);
    }

    public function test_non_existing_driver()
    {
        $this->expectException(InvalidArgumentException::class);
        $gateway = $this->adapter->driver('non_existing_driver');
    }

}
