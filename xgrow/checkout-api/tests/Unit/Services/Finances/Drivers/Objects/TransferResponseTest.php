<?php

namespace Tests\Unit\Services\Finances\Drivers\Objects;

use App\Services\Finances\Transfer\Objects\TransferResponse;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use stdClass;

class TransferResponseTest extends TestCase
{
    private stdClass $sample;

    protected function setUp(): void
    {
        parent::setUp();

        $obj = new stdClass();
        $obj->object = "transfer";
        $obj->id = 1;
        $obj->amount = 1234;
        $obj->type = "ted";
        $obj->status = "pending_transfer";
        $obj->source_type = "recipient";
        $obj->source_id = "re_ckmns1txw01sw0h9tjm2wmv7u";
        $obj->target_type = "bank_account";
        $obj->target_id = "18677574";
        $obj->fee = 56;
        $obj->funding_date = null;
        $obj->funding_estimated_date = "2021-12-14T03:00:00.000Z";
        $obj->transaction_id = null;
        $obj->date_created = "2021-12-13T12:50:03.270Z";
        $obj->date_updated = "2021-12-13T12:50:03.270Z";
        $obj->metadata = [];
        $obj->bank_account = [
            "object" => "bank_account",
            "id" => 18677574,
            "bank_code" => "014",
            "agencia" => "123",
            "agencia_dv" => "1",
            "conta" => "123456",
            "conta_dv" => "1",
            "type" => "conta_corrente",
            "document_type" => "cpf",
            "document_number" => "01234567890",
            "legal_name" => "BOB BURNQUIST",
            "charge_transfer_fees" => false,
            "pix_key" => null,
            "date_created" => "2021-03-26T13:23:37.214Z"
        ];

        $this->sample = $obj;
    }

    public function test_from_pagarme_object()
    {
        $obj = $this->sample;

        $transfer = TransferResponse::fromPagarmeObject($obj);

        $this->assertInstanceOf(TransferResponse::class, $transfer);

        $this->assertEquals($obj->id, $transfer->getId());
        $this->assertEquals($obj->amount, $transfer->getAmount());
        $this->assertEquals($obj->source_id, $transfer->getSourceId());
        $this->assertEquals($obj->target_id, $transfer->getTargetId());
        $this->assertEquals($obj->transaction_id, $transfer->getTransactionId());
        $this->assertInstanceOf(Carbon::class, $transfer->getCreatedAt());
        $this->assertEquals($obj->metadata, $transfer->getMetadata());
    }

    public function test_json_serialize()
    {
        $obj = $this->sample;

        $transfer = TransferResponse::fromPagarmeObject($obj);

        $serialized = $transfer->jsonSerialize();

        $this->assertInstanceOf(TransferResponse::class, $transfer);

        $this->assertEquals($obj->id, $serialized['id']);
        $this->assertEquals($obj->amount, $serialized['amount']);
        $this->assertEquals($obj->source_id, $serialized['source_id']);
        $this->assertEquals($obj->target_id, $serialized['target_id']);
        $this->assertEquals($obj->transaction_id, $serialized['transaction_id']);
        $this->assertEquals('2021-12-13T12:50:03+0000', $serialized['created_at']);
        $this->assertEquals($obj->metadata, $serialized['metadata']);
    }

}
