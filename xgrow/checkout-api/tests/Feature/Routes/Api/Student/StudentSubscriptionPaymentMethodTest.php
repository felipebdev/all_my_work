<?php

namespace Tests\Feature\Routes\Api\Student;

use App\Payment;
use App\Services\Finances\PaymentChange\PaymentChangeService;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Response;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

use function config;

class StudentSubscriptionPaymentMethodTest extends TestCase
{

    use MockPubSubTrait;
    use LocalDatabaseIds;
    use CreateSubscriberTrait;

    public const EXPIRATION_IN_MINUTES = 100;

    private PaymentChangeService $paymentChangeService;

    public $defaultJwtAlgorithm = 'HS256';

    private $secret;

    protected function setUp(): void
    {
        parent::setUp();

        $this->secret = config('jwtplatform.jwt_students');
        if (!$this->secret) {
            $this->markTestSkipped('Config jwtplatform.jwt_students not found, check env JWT_STUDENTS_SECRET');
        }

        $this->paymentChangeService = $this->app->make(PaymentChangeService::class);
    }

    public function test_subscription_credit_card_change_method_fails()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

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

        $paymentId = Payment::latest('id')->first()->id;

        $response = $this->withHeaders(['authorization' => "Bearer {$token}"])
            ->post('api/students/payments/methods', [
                'payment_id' => $paymentId,
                'payment_method' => 'boleto',
            ]);

        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function test_subscription_pix_to_boleto_works()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            'payment_method' => 'pix',
            'subscriber_id' => $subscriberId,
            'platform_id' => $this->platformId,
            'plan_id' => $this->subscriptionPlanId, // 100
            'cc_info' => [],
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

        $paymentId = Payment::latest('id')->first()->id;

        $response = $this->withHeaders(['authorization' => "Bearer {$token}"])
            ->post('api/students/payments/methods', [
                'payment_id' => $paymentId,
                'payment_method' => 'boleto', //  'boleto', 'pix',
            ]);

        $response->assertSuccessful();
    }

    public function test_subscription_boleto_to_cc_works()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            'payment_method' => 'boleto',
            'subscriber_id' => $subscriberId,
            'platform_id' => $this->platformId,
            'plan_id' => $this->subscriptionPlanId, // 100
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

        $paymentId = Payment::latest('id')->first()->id;

        $creditCardToken = MundipaggToken::cardOk($this->faker->creditCardNumber('Visa'));

        $response = $this->withHeaders(['authorization' => "Bearer {$token}"])
            ->post('api/students/payments/methods', [
                'payment_id' => $paymentId,
                'payment_method' => 'credit_card',
                'cc_info' => [
                    'token' => $creditCardToken,
                ]
            ]);

        $response->assertSuccessful();
    }

}
