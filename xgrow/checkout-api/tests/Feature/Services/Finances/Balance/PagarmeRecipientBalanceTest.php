<?php

namespace Tests\Feature\Services\Finances\Balance;

use App\Services\Finances\Balance\Contracts\RecipientBalanceInterface;
use App\Services\Finances\Balance\Drivers\PagarmeRecipientBalance;
use App\Services\Finances\Balance\Objects\BalanceResponse;
use Tests\TestCase;

class PagarmeRecipientBalanceTest extends TestCase
{
    private PagarmeRecipientBalance $pagarmeBalance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pagarmeBalance = $this->app->make(PagarmeRecipientBalance::class);
    }

    public function test_pagarme_implements_recipient_balance_interface()
    {
        $this->assertInstanceOf(RecipientBalanceInterface::class, $this->pagarmeBalance);
    }

    public function test_pagarme_get_balance_by_recipient_id()
    {
        $this->markTestSkipped('Fix: get a valid recipient id');

        $recipientId = 1;
        $balance = $this->pagarmeBalance->getBalance($recipientId);

        $this->assertInstanceOf(BalanceResponse::class, $balance);
    }

}
