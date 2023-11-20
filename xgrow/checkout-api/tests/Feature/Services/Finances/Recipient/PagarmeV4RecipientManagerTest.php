<?php

namespace Tests\Feature\Services\Finances\Recipient;

use App\Services\Finances\Recipient\Drivers\PagarmeV4RecipientManager;
use App\Services\Finances\Recipient\Objects\RecipientResponse;
use Tests\TestCase;

class PagarmeV4RecipientManagerTest extends TestCase
{
    public const XGROW_RECIPIENT = 'rp_0QJDrnwTxTM1ZvnN';

    private PagarmeV4RecipientManager $pagarmeV4RecipientManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pagarmeV4RecipientManager = $this->app->make(PagarmeV4RecipientManager::class);
    }

    public function test_can_retrieve_recipient()
    {
        $recipient = $this->pagarmeV4RecipientManager->obtainRecipient(self::XGROW_RECIPIENT);

        $this->assertInstanceOf(RecipientResponse::class, $recipient);
    }

    public function test_can_retrieve_anticipation_delay()
    {
        $delay = $this->pagarmeV4RecipientManager->getAnticipationDelay(self::XGROW_RECIPIENT);

        $this->assertIsInt($delay);
    }
}
