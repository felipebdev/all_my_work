<?php

namespace Tests\Feature\Routes\Api\Student;

use App\Payment;
use App\PaymentMethodChange;
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

class StudentCreditCardRateLimitedChangeTest extends TestCase
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

        $this->faker->addProvider(new \Faker\Provider\pt_BR\Payment($this->faker));
    }

    public function test_change_payment_sale_rate_limit_blocks()
    {
        $this->markTestIncomplete('Needs adjustment');

        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::insufficientBalance($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 3,
                    "value" => "100"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
        ]);

        $response->assertSuccessful();

        $this->withMiddleware();

        $subscriber = self::lastSubscriberRequest();

        $token = JWT::encode([
            'email' => $subscriber['email'],
            'subscribers_ids' => [
                $subscriberId
            ],
            'products_ids' => [
                $this->salePlanId,
            ],
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addMinutes(self::EXPIRATION_IN_MINUTES)->timestamp,
        ], $this->secret, $this->defaultJwtAlgorithm);

        $paymentId = Payment::latest('id')->first()->id;

        // first change

        $creditCardToken = MundipaggToken::cardOk($this->faker->creditCardNumber('American Express')); // Amex

        $response = $this->withHeaders(['authorization' => "Bearer {$token}"])
            ->post('api/students/payments/methods', [
                'payment_id' => $paymentId,
                'payment_method' => 'credit_card',
                'cc_info' => [
                    'token' => $creditCardToken,
                ]
            ]);

        $response->assertSuccessful();

        // second change

        $creditCardToken = MundipaggToken::cardOk($this->faker->creditCardNumber('Discover Card')); // Discover

        $response = $this->withHeaders(['authorization' => "Bearer {$token}"])
            ->post('api/students/payments/methods', [
                'payment_id' => $paymentId,
                'payment_method' => 'credit_card',
                'cc_info' => [
                    'token' => $creditCardToken,
                ]
            ]);

        $response->assertSuccessful();

        // third change

        $creditCardToken = MundipaggToken::cardOk($this->faker->creditCardNumber('Elo'));

        $response = $this->withHeaders(['authorization' => "Bearer {$token}"])
            ->post('api/students/payments/methods', [
                'payment_id' => $paymentId,
                'payment_method' => 'credit_card',
                'cc_info' => [
                    'token' => $creditCardToken,
                ]
            ]);

        $response->assertSuccessful();

        // fourth change : must fail


        $creditCardToken = MundipaggToken::cardOk($this->faker->creditCardNumber('Hipercard')); // Mastercard

        $response = $this->withHeaders(['authorization' => "Bearer {$token}"])
            ->post('api/students/payments/methods', [
                'payment_id' => $paymentId,
                'payment_method' => 'credit_card',
                'cc_info' => [
                    'token' => $creditCardToken,
                ]
            ]);

        $response->assertStatus(Response::HTTP_TOO_MANY_REQUESTS);

    }

    public function test_change_payment_sale_rate_limit_allows()
    {
        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::insufficientBalance($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 3,
                    "value" => "100"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
        ]);

        $response->assertSuccessful();

        $this->withMiddleware();

        $subscriber = self::lastSubscriberRequest();

        $token = JWT::encode([
            'email' => $subscriber['email'],
            'subscribers_ids' => [
                $subscriberId
            ],
            'products_ids' => [
                $this->salePlanId,
            ],
            'iat' => Carbon::now()->timestamp,
            'exp' => Carbon::now()->addMinutes(self::EXPIRATION_IN_MINUTES)->timestamp,
        ], $this->secret, $this->defaultJwtAlgorithm);

        $paymentId = Payment::latest('id')->first()->id;

        // first change

        $creditCardToken = MundipaggToken::cardOk($this->faker->creditCardNumber('American Express')); // Amex

        $response = $this->withHeaders(['authorization' => "Bearer {$token}"])
            ->post('api/students/payments/methods', [
                'payment_id' => $paymentId,
                'payment_method' => 'credit_card',
                'cc_info' => [
                    'token' => $creditCardToken,
                ]
            ]);

        $response->assertSuccessful();

        // second change

        $creditCardToken = MundipaggToken::cardOk($this->faker->creditCardNumber('Discover Card')); // Discover

        $response = $this->withHeaders(['authorization' => "Bearer {$token}"])
            ->post('api/students/payments/methods', [
                'payment_id' => $paymentId,
                'payment_method' => 'credit_card',
                'cc_info' => [
                    'token' => $creditCardToken,
                ]
            ]);

        $response->assertSuccessful();

        // third change

        $creditCardToken = MundipaggToken::cardOk($this->faker->creditCardNumber('Elo'));

        $response = $this->withHeaders(['authorization' => "Bearer {$token}"])
            ->post('api/students/payments/methods', [
                'payment_id' => $paymentId,
                'payment_method' => 'credit_card',
                'cc_info' => [
                    'token' => $creditCardToken,
                ]
            ]);

        $response->assertSuccessful();

        // fourth change after 24h: must success

        Carbon::setTestNow(Carbon::now()->addHours(24));

        $creditCardToken = MundipaggToken::cardOk($this->faker->creditCardNumber('Hipercard')); // Mastercard

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
