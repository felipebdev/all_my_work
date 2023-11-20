<?php

namespace Tests\Feature\Api\Checkout\SaveSubscriber;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Modules\Integration\Queue\Jobs\HandleIntegration;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class StoreSubscriberBrazilTest extends TestCase
{

    use withFaker;
    use LocalDatabaseIds;

    //use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = $this->makeFaker('pt_BR');
    }

    public function planId(): int
    {
        return $this->salePlanId;
    }

    public function test_allow_subscriber_with_cpf()
    {
        //Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '55',
            'phone_area_code' => '10',
            'phone_number' => $this->faker->cellphone($formatted = false, $includes9 = true),
            'user_ip' => $this->faker->ipv4(),
            'document_number' => $this->faker->cpf($formatted = false),
            'document_type' => 'cpf',
            'country' => 'BR',
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        //Queue::assertPushedOn('xgrow-jobs:integrations:', HandleIntegration::class);
    }

    public function test_allow_subscriber_with_cnpj()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '55',
            'phone_area_code' => '10',
            'phone_number' => $this->faker->cellphone($formatted = false, $includes9 = true),
            'user_ip' => $this->faker->ipv4(),
            'document_number' => $this->faker->cnpj($formatted = false),
            'document_type' => 'cnpj',
            'country' => 'BR',
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        Queue::assertPushedOn('xgrow-jobs:integrations-checkout', HandleIntegration::class);
    }

    public function test_block_subscriber_without_document_info()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '55',
            'phone_area_code' => '10',
            'phone_number' => $this->faker->cellphone($formatted = false, $includes9 = true),
            //'user_ip' => $this->faker->ipv4(),
            'document_number' => null,
            'document_type' => null,
            'country' => 'BR',
        ]);

        $response->assertStatus(400);

        Queue::assertNothingPushed();
    }

    public function test_block_subscriber_without_cpf_number()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '55',
            'phone_area_code' => '10',
            'phone_number' => $this->faker->cellphone($formatted = false, $includes9 = true),
            'user_ip' => $this->faker->ipv4(),
            'document_number' => null,
            'document_type' => 'cpf',
            'country' => 'BR',
        ]);

        $response->assertStatus(400);

        Queue::assertNothingPushed();
    }

    public function test_block_subscriber_without_cnpj_number()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '55',
            'phone_area_code' => '10',
            'phone_number' => $this->faker->cellphone($formatted = false, $includes9 = true),
            'user_ip' => $this->faker->ipv4(),
            'document_number' => null,
            'document_type' => 'cnpj',
            'country' => 'BR',
        ]);

        $response->assertStatus(400);

        Queue::assertNothingPushed();
    }

    public function test_block_subscriber_without_document_type_specification()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->salePlanId,
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '55',
            'phone_area_code' => '10',
            'phone_number' => $this->faker->cellphone($formatted = false, $includes9 = true),
            'user_ip' => $this->faker->ipv4(),
            'document_number' => $this->faker->cpf($formatted = false),
            'document_type' => null,
            'country' => 'BR',
        ]);

        $response->assertStatus(400);

        Queue::assertNothingPushed();
    }


    public function test_block_subscriber_with_bad_phone_area_code()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->planId(),
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '55',
            'phone_area_code' => '100', // bad area code
            'phone_number' => $this->faker->cellphone($formatted = false, $includes9 = true),
            'user_ip' => $this->faker->ipv4(),
            'document_number' => $this->faker->cpf($formatted = false),
            'document_type' => 'cpf',
            'country' => 'BR',
        ]);

        $response->assertStatus(400);

        Queue::assertNothingPushed();
    }


    public function test_block_subscriber_with_bad_phone_number()
    {
        Queue::fake();

        $this->withoutMiddleware();

        $response = $this->postJson('/api/checkout/subscriber', [
            'platform_id' => $this->platformId,
            'plan_id' => $this->planId(),
            'email' => $this->faker->userName().'@xgrow.com',
            'name' => $this->faker->name(),
            'phone_country_code' => '55',
            'phone_area_code' => '10',
            'phone_number' => $this->faker->numerify('#######'),
            'user_ip' => $this->faker->ipv4(),
            'document_number' => $this->faker->cpf($formatted = false),
            'document_type' => 'cpf',
            'country' => 'BR',
        ]);

        $response->assertStatus(400);

        Queue::assertNothingPushed();
    }


}
