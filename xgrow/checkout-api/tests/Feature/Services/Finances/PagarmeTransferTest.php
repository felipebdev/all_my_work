<?php

namespace Tests\Feature\Services\Finances;

use App\Exceptions\Finances\TransferNotFoundException;
use App\Services\Finances\Transfer\Contracts\TransferInterface;
use App\Services\Finances\Transfer\Drivers\PagarmeTransfer;
use App\Services\Finances\Transfer\Objects\TransferFilter;
use App\Services\Finances\Transfer\Objects\TransferResponse;
use Tests\TestCase;

class PagarmeTransferTest extends TestCase
{
    private PagarmeTransfer $pagarmeTransfer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pagarmeTransfer = $this->app->make(PagarmeTransfer::class);
    }

    public function test_pagarme_implements_interface()
    {
        $this->assertInstanceOf(TransferInterface::class, $this->pagarmeTransfer);
    }

    public function test_pagarme_get_transfers_by_recipient_id()
    {
        $filter = TransferFilter::fromArray(['recipient_id' => 're_ckmns1txw01sw0h9tjm2wmv7u']);
        $transfers = $this->pagarmeTransfer->listTransfers($filter);

        $this->assertContainsOnlyInstancesOf(TransferResponse::class, $transfers);
    }

    public function test_empty_list_transfers()
    {
        $filter = TransferFilter::fromArray(['recipient_id' => 'non_existent_recipient_id']);
        $transfers = $this->pagarmeTransfer->listTransfers($filter);

        $this->assertEquals([], $transfers);
    }

    public function test_transfer_not_found()
    {
        $this->expectException(TransferNotFoundException::class);
        $transfer = $this->pagarmeTransfer->getTransfer('0');
    }

    public function test_cancel_transfer_not_found()
    {
        $this->expectException(TransferNotFoundException::class);
        $transfer = $this->pagarmeTransfer->cancelTransfer('0');
    }

}
