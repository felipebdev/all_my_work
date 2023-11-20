<?php

namespace App\Services\Actions;

use App\Constants\LogKeys;
use App\Mail\BoletoPix\SendMailCancelSubscriptionBoletoPix;
use App\Payment;
use App\Recurrence;
use App\Services\EmailTaggedService;
use App\Services\Pagarme\PagarmeSdkV5\PagarmeClient;
use App\Subscriber;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RunFiveDaysExpiredPaymentsCancelSubscriptions
{
    private PagarmeClient $pagarmeClient;

    public function __construct()
    {
        $this->pagarmeClient = app()->make(PagarmeClient::class);
    }

    public function __invoke()
    {
        $this->expirePixAndBoletoSubscriptions(Carbon::now());
    }

    private function expirePixAndBoletoSubscriptions($date): int
    {
        $fiveDaysAgo = $date->subDays(5)->format('Y-m-d');
        $recurrences = $this->getExpiredPixAndBoletos($fiveDaysAgo);

        $recurrences->each(function (Recurrence $recurrence) {
            Subscription::find($recurrence->subscription_id)->update([
                'canceled_at' => Carbon::now(),
                'status' => Subscription::STATUS_CANCELED,
                'status_updated_at' => Carbon::now(),
                'cancellation_reason' => 'Pagamento expirado a mais de 5 dias',
            ]);

            $recurrence->payments->each(function (Payment $payment) {
                $isCreditCard = $payment->type_payment == Payment::TYPE_PAYMENT_CREDIT_CARD;
                $isMultimeansCard = Str::of($payment->multiple_means_type ?? '')->lower()->contains('c');
                $isPending = $payment->status == Payment::STATUS_PENDING;

                if ($isCreditCard && $isMultimeansCard && $isPending) {
                    $this->pagarmeClient->cancelByChargeId($payment->charge_id);
                }
            });

            $mail = new SendMailCancelSubscriptionBoletoPix($recurrence);
            $platformId = $recurrence->subscriber->platform_id;
            $email = $recurrence->subscriber->email;
            EmailTaggedService::mail($platformId, 'CANCEL_SUBSCRIPTION_BOLETO_PIX', $mail, [$email]);

            $this->saveLastRecurrenceIdProcessedToday($recurrence->id);
        });

        return $recurrences->count();
    }

    private function getExpiredPixAndBoletos($date): Collection
    {
        $nextPaymentDate = ' DATE(recurrences.last_payment + INTERVAL recurrences.recurrence DAY) ';
        $sqlCondition = "{$nextPaymentDate} <= '{$date}'";

        return Recurrence::
        selectRaw('
                recurrences.id AS recurrence_id,
                recurrences.plan_id AS plan_id,
                subscriptions.id AS subscription_id,
                subscribers.id AS subscriber_id
            ')
            ->join('subscriptions', function ($query) {
                $query->on('subscriptions.subscriber_id', '=', 'recurrences.subscriber_id')
                    ->on('subscriptions.plan_id', '=', 'recurrences.plan_id');
            })
            ->join('subscribers', 'subscribers.id', '=', 'recurrences.subscriber_id')
            ->where('subscribers.status', Subscriber::STATUS_ACTIVE)
            ->where('recurrences.id', '>', $this->getLastRecurrenceIdProcessedToday())
            ->whereIn('recurrences.payment_method', [Recurrence::PAYMENT_METHOD_BOLETO, Recurrence::PAYMENT_METHOD_PIX])
            ->whereNull('subscriptions.canceled_at')
            ->whereNull('subscriptions.payment_pendent')
            ->whereNotNull('subscriptions.order_number') // order_number required
            ->whereRaw($sqlCondition)
            ->groupBy('subscriptions.id')
            ->orderBy('recurrences.id')
            ->get();

    }

    private function saveLastRecurrenceIdProcessedToday($recurrenceId): void
    {
        $ttlInSeconds = 60 * 60 * 23; // 23h

        Cache::put(
            LogKeys::CRON_RUN_FIVE_DAYS_CANCEL_SUBSCRIPTIONS_LAST_RECURRENCE_ID_TODAY,
            $recurrenceId,
            $ttlInSeconds
        );
    }

    private function getLastRecurrenceIdProcessedToday(): int
    {
        return Cache::get(LogKeys::CRON_RUN_FIVE_DAYS_CANCEL_SUBSCRIPTIONS_LAST_RECURRENCE_ID_TODAY, 0);
    }

}
