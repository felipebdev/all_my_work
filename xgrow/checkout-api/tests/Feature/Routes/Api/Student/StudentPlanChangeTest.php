<?php

namespace Tests\Feature\Routes\Api\Student;

use App\Plan;
use App\Subscription;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class StudentPlanChangeTest extends TestCase
{

    use MockPubSubTrait;
    use LocalDatabaseIds;
    use CreateSubscriberTrait;

    public const EXPIRATION_IN_MINUTES = 100;

    public $defaultJwtAlgorithm = 'HS256';

    private $secret;

    protected function setUp(): void
    {
        parent::setUp();

        Plan::query()->update(['allow_change' => true]);

        $this->secret = config('jwtplatform.jwt_students');
        if (!$this->secret) {
            $this->markTestSkipped('Config jwtplatform.jwt_students not found, check env JWT_STUDENTS_SECRET');
        }
    }

    public function test_get_available_plans()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriptionProduct = 2;

        $response = $this->get("/api/students/products/{$subscriptionProduct}/change");

        //dump($response->json());

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'response' => [
                '*' => [
                    'id',
                    'name',
                    'message',
                    'recurrence',
                    'currency',
                    'price',
                    'original_price',
                    'discount',
                    'freedays',
                    'freedays_type',
                    'charge_until',
                    'type_plan',
                    'installment',
                    'description',
                    'image_id',
                    'image_url',
                    'message_success_checkout',
                    'use_promotional_price',
                    'recurrence_description',
                    'promotional_periods',
                    'promotional_price',
                ],
            ],
        ]);
    }

    public function test_can_change_plan()
    {
        $this->withoutMiddleware();

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $token = MundipaggToken::cardOk($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            'payment_method' => 'credit_card',
            'subscriber_id' => $subscriberId,
            'platform_id' => $this->platformId,
            'plan_id' => $this->subscriptionPlanId, // 100
            'cc_info' => [
                [
                    'token' => "$token",
                    'installment' => 1,
                    'value' => '100.00'
                ]
            ],
        ]);

        $response->assertSuccessful();

        $this->withMiddleware();

        $subscriber = self::lastSubscriberRequest();

        $token = JWT::encode([
            'email' => $subscriber['email'] ?? 1,
            'subscribers_ids' => [
                $subscriberId
            ],
            'products_ids' => [
                $this->subscriptionPlanId,
            ],
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addMinutes(self::EXPIRATION_IN_MINUTES)->timestamp,
        ], $this->secret, $this->defaultJwtAlgorithm);

        $subscriptionId = Subscription::latest('id')->first()->id;

        $response = $this->withHeaders(['authorization' => "Bearer {$token}"])
            ->post("/api/students/products/2/change", [
                'subscription_id' => $subscriptionId,
                'new_plan_id' => 5,
            ]);

        //dump($response->json());

        $response->assertSuccessful();
    }
}
