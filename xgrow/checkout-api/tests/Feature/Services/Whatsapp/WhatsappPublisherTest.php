<?php

namespace Tests\Feature\Services\Whatsapp;

use App\Payment;
use App\Services\Whatsapp\WhatsappPublisher;
use Jobcloud\Avro\Validator\RecordRegistry;
use Jobcloud\Avro\Validator\Validator;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\TestCase;

class WhatsappPublisherTest extends TestCase
{
    use MockPubSubTrait;

    private WhatsappPublisher $whatsappPublisher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->whatsappPublisher = $this->app->make(WhatsappPublisher::class);
    }

    private function getSchemaFromFile(string $filename): string
    {
        $path = base_path().'/app/Services/Whatsapp/Schemas/'.$filename;

        return file_get_contents($path);
    }

    public function test_boleto_created_schema()
    {
        $this->mockPubSub();

        $avroSchema = $this->getSchemaFromFile('boleto-created-schema.json');
        $recordRegistry = RecordRegistry::fromSchema($avroSchema);
        $validator = new Validator($recordRegistry);

        $json = $this->whatsappPublisher->boletoCreated(Payment::first());

        $validationErrors = $validator->validate($json, 'com.xgrow.boletoCreated');

        $this->assertEmpty($validationErrors);
    }

    public function test_pix_created_schema()
    {
        $this->mockPubSub();

        $json = $this->whatsappPublisher->pixCreated(Payment::first());

        $avroSchema = $this->getSchemaFromFile('pix-created-schema.json');

        $recordRegistry = RecordRegistry::fromSchema($avroSchema);
        $validator = new Validator($recordRegistry);

        $validationErrors = $validator->validate($json, 'com.xgrow.pixCreated');

        $this->assertEmpty($validationErrors);
    }


    public function test_payment_confirmed_schema()
    {
        $this->mockPubSub();

        $json = $this->whatsappPublisher->paymentConfirmed(Payment::first());

        $avroSchema = $this->getSchemaFromFile('payment-confirmed-schema.json');

        $recordRegistry = RecordRegistry::fromSchema($avroSchema);
        $validator = new Validator($recordRegistry);

        $validationErrors = $validator->validate($json, 'com.xgrow.paymentConfirmed');

        $this->assertEmpty($validationErrors);
    }

}
