<?php

namespace Tests\Feature\Routes\Api\Student;

use App\Mail\SendMailTwoFactorCode;
use App\Payment;
use App\Subscriber;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Helper\JwtStudentToken;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class StudentTwoFactorMailCodeRefundTest extends TestCase
{
    use MockPubSubTrait;
    use WithFaker;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    public function test_example()
    {
        $this->withoutMiddleware();

        $this->mockPubSub();

        // lead

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        // buy

        $token = MundipaggToken::randomValidCvv($this->faker->creditCardNumber('American Express'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            "order_bump" => [],
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 1,
                    "value" => "100.00"
                ]
            ],
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        // Refund

        $this->withMiddleware(); // re-enable middlewares

        Mail::fake(); // enable mail capture

        // prepare

        $payment = Payment::query()->where('status', Payment::STATUS_PAID)->latest('id')->first();
        $subscriber = Subscriber::find($payment->subscriber_id);
        $jwt = JwtStudentToken::generateToken($subscriber->email, [$subscriber->id], [$subscriber->plan_id]);

        // Request code

        /** {{ @see \App\Http\Controllers\Api\RefundController::sendTwoFactorCode() }} * */
        $responseSendcode = $this
            ->withToken($jwt)
            ->post('/api/students/refund-by-students/sendcode', [
                'order_code' => $payment->order_code,
                'email' => $subscriber->email,
            ]);

        $responseSendcode->assertStatus(200);

        // check email

        $pin = null;
        Mail::assertSent(function (SendMailTwoFactorCode $mail) use (&$pin) {
            // uses Reflection to access "private $pin"
            $reflection = new \ReflectionObject($mail);
            $property = $reflection->getProperty('pin');
            $property->setAccessible(true);

            $pin = $property->getValue($mail);

            return is_numeric($pin);
        }, 1);

        // check code

        /** {{ @see \App\Http\Controllers\Api\RefundController::checkTwoFactorCode() }} * */

        $queryParams = [
            'order_code' => $payment->order_code,
            'email' => $subscriber->email,
            'code' => "$pin",
        ];

        $responseCheckCode = $this
            ->withToken($jwt)
            ->get('/api/students/refund-by-students/checkcode?'.http_build_query($queryParams));

        $responseCheckCode->assertStatus(200);

        // test wrong code

        $responseWithWrongCode = $this
            ->withToken($jwt)
            ->post('/api/students/refund-by-students', [
                'payment_method' => 'credit_card',
                'payment_id' => "{$payment->id}",
                'reason' => 'Estorno de teste',
                'code' => '0111',
            ]);

        $responseWithWrongCode->assertStatus(401);

        // assert payment status
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);

        // test correct code

        $responseWithCorretCode = $this
            ->withToken($jwt)
            ->post('/api/students/refund-by-students', [
                'payment_method' => 'credit_card',
                'payment_id' => "{$payment->id}",
                'reason' => 'Estorno de teste',
                'code' => $pin,
            ]);

        $responseWithCorretCode->assertStatus(200);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'refunded',
        ]);
    }
}
