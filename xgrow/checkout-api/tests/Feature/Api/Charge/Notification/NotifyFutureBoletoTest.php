<?php

namespace Tests\Feature\Api\Charge\Notification;

use App\Mail\BoletoPix\SendMailUpcomingBoletoPix;
use App\Payment;
use App\Services\Actions\NotifyUpcomingBoletoPixAction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Integration\MockPubSubTrait;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class NotifyFutureBoletoTest extends TestCase
{
    use MockPubSubTrait;
    use CreateSubscriberTrait;
    use LocalDatabaseIds;

    public function test_boleto_notification_future()
    {
        Mail::fake();

        $this->withoutMiddleware();

        $this->mockPubSubWithCount(1);

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

        $action = new NotifyUpcomingBoletoPixAction();
        $action->setSubscriberId($subscriberId);

        // D+27: 1st email

        Carbon::setTestNow(Carbon::now()->addDays(27));
        $action();
        Mail::assertSent(SendMailUpcomingBoletoPix::class, 1);

        // D+28: 2nd email

        Carbon::setTestNow(Carbon::now()->addDay());
        $action();
        Mail::assertSent(SendMailUpcomingBoletoPix::class, 2);

        // D+29: 3rd email

        Carbon::setTestNow(Carbon::now()->addDay());
        $action();
        Mail::assertSent(SendMailUpcomingBoletoPix::class, 3);

        // D+30: 4th: email

        Carbon::setTestNow(Carbon::now()->addDay());
        $action();
        Mail::assertSent(SendMailUpcomingBoletoPix::class, 4);

        // D+31: send 1st expired email instead of "future" email

        Carbon::setTestNow(Carbon::now()->addDay());
        $action();
        Mail::assertSent(SendMailUpcomingBoletoPix::class, 4); // still 4 emails
    }

}
