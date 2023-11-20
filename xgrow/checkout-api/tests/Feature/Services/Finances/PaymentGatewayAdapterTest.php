<?php

namespace Tests\Feature\Services\Finances;

use App\Services\Finances\Transfer\Contracts\TransferInterface;
use App\Services\Finances\Transfer\Drivers\PagarmeTransfer;
use App\Services\Finances\Transfer\Drivers\VoidTransfer;
use App\Services\Finances\Transfer\PaymentGatewayAdapter;
use InvalidArgumentException;
use Tests\TestCase;

class PaymentGatewayAdapterTest extends TestCase
{
    private PaymentGatewayAdapter $paymentGatewayAdapter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentGatewayAdapter = $this->app->make(PaymentGatewayAdapter::class);
    }

    public function test_default_driver()
    {
        $gateway = $this->paymentGatewayAdapter->driver();
        $this->assertInstanceOf(TransferInterface::class, $gateway);
    }

    public function test_setting_void_driver()
    {
        $gateway = $this->paymentGatewayAdapter->driver(PaymentGatewayAdapter::DRIVER_VOID);
        $this->assertInstanceOf(VoidTransfer::class, $gateway);
        $this->assertNotInstanceOf(PagarmeTransfer::class, $gateway);
    }


    public function test_setting_pagarme_driver()
    {
        $gateway = $this->paymentGatewayAdapter->driver(PaymentGatewayAdapter::DRIVER_PAGARME);
        $this->assertNotInstanceOf(VoidTransfer::class, $gateway);
        $this->assertInstanceOf(PagarmeTransfer::class, $gateway);
    }

    public function test_non_existing_driver()
    {
        $this->expectException(InvalidArgumentException::class);
        $gateway = $this->paymentGatewayAdapter->driver('non_existing_driver');
    }
}
