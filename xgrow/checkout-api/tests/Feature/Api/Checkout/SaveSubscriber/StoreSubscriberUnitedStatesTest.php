<?php

namespace Tests\Feature\Api\Checkout\SaveSubscriber;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Modules\Integration\Queue\Jobs\HandleIntegration;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class StoreSubscriberUnitedStatesTest extends TestCase
{
    use withFaker;
    use LocalDatabaseIds;
    //use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = $this->makeFaker('en_US');
    }

    public function test_allow_subscriber_no_document_for_us()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '1',
            'phone_area_code' => $this->faker->areaCode(),
            'phone_number' => $this->faker->numerify('#######'),
            'user_ip' => $this->faker->ipv4(true),
            'document_number' => null,
            'document_type' => null,
            'country' => 'US',
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        Queue::assertPushedOn('xgrow-jobs:integrations-checkout', HandleIntegration::class);
    }

    public function test_allow_subscriber_natural_person_from_usa()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '1',
            'phone_area_code' => $this->faker->areaCode(),
            'phone_number' => $this->faker->numerify('#######'),
            'user_ip' => $this->faker->ipv4(true),
            'document_number' => $this->onlyDigits($this->faker->ssn()), // social security number
            'document_type' => 'other_natural',
            'country' => 'US',
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        Queue::assertPushedOn('xgrow-jobs:integrations-checkout', HandleIntegration::class);
    }

    public function test_allow_subscriber_legal_person_from_usa()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '1',
            'phone_area_code' => $this->faker->areaCode(),
            'phone_number' => $this->faker->numerify('#######'),
            'user_ip' => $this->faker->ipv4(true),
            'document_number' => $this->onlyDigits($this->faker->ein()), // Employer Identification Number
            'document_type' => 'other_legal',
            'country' => 'US',
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        Queue::assertPushedOn('xgrow-jobs:integrations-checkout', HandleIntegration::class);
    }


    public function test_allow_uncommon_international_phone_lengths()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '1',
            'phone_area_code' => $this->faker->numerify('###'),
            'phone_number' => $this->faker->numerify('##########'),
            'user_ip' => $this->faker->ipv4(true),
            'document_number' => $this->onlyDigits($this->faker->ein()), // Employer Identification Number
            'document_type' => 'other_legal',
            'country' => 'US',
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        Queue::assertPushedOn('xgrow-jobs:integrations-checkout', HandleIntegration::class);
    }

    public function test_block_subscriber_missing_document_type_for_us_document_number()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '55',
            'phone_area_code' => $this->faker->areaCode(),
            'phone_number' => $this->faker->numerify('#######'),
            'user_ip' => $this->faker->ipv4(true),
            'document_number' => $this->onlyDigits($this->faker->ssn()),
            'document_type' => null,
            'country' => 'US',
        ]);

        $response->assertStatus(400);

        Queue::assertNothingPushed();
    }

    public function test_block_subscriber_missing_document_number_for_natural_person_from_usa()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '55',
            'phone_area_code' => $this->faker->areaCode(),
            'phone_number' => $this->faker->numerify('#######'),
            'user_ip' => $this->faker->ipv4(true),
            'document_number' => null,
            'document_type' => 'other_natural',
            'country' => 'US',
        ]);

        $response->assertStatus(400);

        Queue::assertNothingPushed();
    }

    public function test_block_subscriber_missing_document_number_for_legal_person_from_usa()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '55',
            'phone_area_code' => $this->faker->areaCode(),
            'phone_number' => $this->faker->numerify('#######'),
            'user_ip' => $this->faker->ipv4(true),
            'document_number' => null,
            'document_type' => 'other_legal',
            'country' => 'US',
        ]);

        $response->assertStatus(400);

        Queue::assertNothingPushed();
    }

    private function onlyDigits(?string $text): string
    {
        return preg_replace('/[^0-9]/', '', $text ?? '');
    }


}
