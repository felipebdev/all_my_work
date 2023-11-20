<?php

namespace Tests\Feature\Api\Charge\Notification;

use App\Mail\BoletoPix\SendMailExpiredBoletoPix;
use App\Payment;
use App\Services\Actions\NotifyExpiredBoletoPixAction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class NotifyExpiredBoletoTest extends TestCase
{

    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    public function test_boleto_notification_future()
    {
        Mail::fake();

        $this->withoutMiddleware();

        $this->mockPubSubWithCount(2);

        $subscriberId = $this->createSubscriber($this->platformId, $this->subscriptionPlanId);

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->subscriptionPlanId}", [
            'payment_method' => 'boleto',
            'subscriber_id' => $subscriberId,
            'platform_id' => $this->platformId,
            'plan_id' => $this->subscriptionPlanId, // 100
            'order_bump' => []
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $orderCode = $response->json('*.order_code')[0] ?? null;

        $payment = Payment::where('order_code', $orderCode)->first();

        $postback = $this->json('POST', '/api/checkout/order/paid', [
            'id' => 'hook_WmekPehanFGr79y2',
            'type' => 'charge.paid',
            'data' => [
                'id' => $payment->charge_id,
                'payment_method' => 'boleto',
                'status' => 'paid',
                'order' => [
                    'code' => $payment->order_code,
                ]
            ]
        ]);

        $postback->assertStatus(200);

        // schedule action

        $action = new NotifyExpiredBoletoPixAction();
        $action->setSubscriberId($subscriberId);

        // D+31: send 1st expired email instead of "future" email
        Carbon::setTestNow(Carbon::now()->addDays(31));
        $action();
        Mail::assertSent(SendMailExpiredBoletoPix::class, 1);

        // D+32: send 2nd expired email

        Carbon::setTestNow(Carbon::now()->addDay());
        $action();
        Mail::assertSent(SendMailExpiredBoletoPix::class, 2);

        // D+33: send 3rd expired email

        Carbon::setTestNow(Carbon::now()->addDay());
        $action();
        Mail::assertSent(SendMailExpiredBoletoPix::class, 3);

        // D+34: send 4th expired email

        Carbon::setTestNow(Carbon::now()->addDay());
        $action();
        Mail::assertSent(SendMailExpiredBoletoPix::class, 4);

        // D+35: send 5th expired email

        Carbon::setTestNow(Carbon::now()->addDay());
        $action();
        Mail::assertSent(SendMailExpiredBoletoPix::class, 5);

        // D+36: no more emails

        Carbon::setTestNow(Carbon::now()->addDay());
        $action();
        Mail::assertSent(SendMailExpiredBoletoPix::class, 5); // still 5 emails

    }

}
