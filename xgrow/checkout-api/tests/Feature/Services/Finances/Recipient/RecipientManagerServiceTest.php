<?php

namespace Tests\Feature\Services\Finances\Recipient;

use App\Exceptions\Finances\InvalidRecipientException;
use App\Exceptions\Finances\RecipientAlreadyExistsException;
use App\Platform;
use App\Producer;
use App\Services\Finances\Recipient\Objects\RecipientResponse;
use App\Services\Finances\Recipient\RecipientManagerService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

use function env;

class RecipientManagerServiceTest extends TestCase
{
    use LocalDatabaseIds;
    use DatabaseTransactions;

    protected $seed = true;

    private RecipientManagerService $recipientManagerService;

    protected function setUp(): void
    {
        parent::setUp();

        if (!env('MUNDIPAGG_SECRET_KEY')) {
            $this->markTestSkipped('Mundipagg secret key is not set');
        }

        $this->recipientManagerService = $this->app->make(RecipientManagerService::class);
    }

    public function test_can_create_xgrow_recipient_and_store()
    {
        DB::table('configs')->update(['recipient_id' => null]); // unset xgrow recipient_id

        $recipient = $this->recipientManagerService->createXgrowRecipientUsingFirstConfigAndStore();

        $this->assertDatabaseHas('configs', [
            'recipient_id' => $recipient->getId(),
        ]);
    }

    public function test_exception_when_xgrow_recipient_already_exists()
    {
        DB::table('configs')->update(['recipient_id' => 'rp_0000000000000000']); // set xgrow recipient_id

        $this->expectException(RecipientAlreadyExistsException::class);
        $this->recipientManagerService->createXgrowRecipientUsingFirstConfigAndStore();
    }

    public function test_can_create_platform_recipient_and_store()
    {
        DB::table('platforms')
            ->where('id', $this->platformId)
            ->update(['recipient_id' => null]); // unset recipient_id

        $platform = Platform::find($this->platformId);

        $recipient = $this->recipientManagerService->createPlatformRecipientAndStore($platform, $this->platformUserId);

        $this->assertDatabaseHas('platforms', [
            'recipient_id' => $recipient->getId(),
        ]);
    }

    public function test_exception_when_platform_recipient_already_exists()
    {
        DB::table('platforms')
            ->where('id', $this->platformId)
            ->update(['recipient_id' => 'rp_0000000000000000']); // set recipient_id

        $platform = Platform::find($this->platformId);

        $this->expectException(RecipientAlreadyExistsException::class);
        $this->recipientManagerService->createPlatformRecipientAndStore($platform, $this->platformUserId);
    }

    public function test_can_create_producer_recipient_and_store()
    {
        $this->markTestSkipped('Factory for producer needs adjustments, skipping');

        $producer = Producer::factory()->create();

        $recipient = $this->recipientManagerService->createProducerRecipientAndStore($producer);

        $this->assertDatabaseHas('producers', [
            'id' => $producer->id,
            'recipient_id' => $recipient->getId(),
        ]);
    }

    public function test_exception_when_producer_recipient_already_exists()
    {
        $producer = Producer::factory()->withRecipientId()->create();

        $this->expectException(RecipientAlreadyExistsException::class);
        $this->recipientManagerService->createProducerRecipientAndStore($producer);
    }

    public function test_obtain_non_existing_recipient()
    {
        $this->expectException(InvalidRecipientException::class);
        $this->recipientManagerService->obtainRecipient('123');
    }

    public function test_obtain_existing_recipient()
    {
        $platform = Platform::find($this->platformId);

        $recipientId = $platform->recipient_id ?? $platform->client->recipient_id;

        $recipient = $this->recipientManagerService->obtainRecipient($recipientId);

        $this->assertInstanceOf(RecipientResponse::class, $recipient);
    }

}
