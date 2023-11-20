<?php

namespace App\Services\Subscriptions;

use App\Payment;
use App\Plan;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Services\Contracts\SubscriptionServiceInterface;
use App\Subscriber;
use App\Subscription;
use App\Utils\TriggerIntegrationJob;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService implements SubscriptionServiceInterface {

    use TriggerIntegrationJob;

    private $subscriptionRepository;
    private $paymentRepository;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        PaymentRepositoryInterface $paymentRepository
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->paymentRepository = $paymentRepository;
    }

    public function hasActiveSubscription($subscriber_id, $platform_id, $plan_id): bool
    {
        return Subscription::where('subscriber_id', '=', $subscriber_id)
            ->where('platform_id', '=', $platform_id)
            ->where('plan_id', '=', $plan_id)
            ->whereNull('canceled_at')
            ->exists();
    }

    public function markSubscriptionWithFailedPayment(Subscriber $subscriber, Plan $plan): bool
    {
        $return = false;
        $activeSubscription = Subscription::where('subscriber_id', '=', $subscriber->id)
            ->where('platform_id', '=', $subscriber->platform_id)
            ->where('plan_id', '=', $plan->id)
            ->whereNull('canceled_at')
            ->first();

        if( $activeSubscription ) {
            $activeSubscription->status = Subscription::STATUS_FAILED;
            $activeSubscription->status_updated_at = Carbon::now();
            $return = $activeSubscription->save();
        }
        return $return;
    }

    public function cancelSubscription(
        Subscriber $subscriber,
        Plan $plan,
        ?string $subscriptionCancellationReason = null
    ): bool
    {
        $activeSubscription = Subscription::where('subscriber_id', '=', $subscriber->id)
            ->where('platform_id', '=', $subscriber->platform_id)
            ->where('plan_id', '=', $plan->id)
            ->whereNull('canceled_at')
            ->first();

        if (is_null($activeSubscription)) {
            return false;
        }

        $now = Carbon::now();

        $activeSubscription->status = Subscription::STATUS_CANCELED;
        $activeSubscription->status_updated_at = $now;
        $activeSubscription->canceled_at = $now;
        $activeSubscription->cancellation_reason = $subscriptionCancellationReason;

        return $activeSubscription->save();
    }

    public function enableSubscriptionByPayment(Payment $payment) {
        if ($payment->status !== Payment::STATUS_PAID) return;
        try {
            if (!empty($payment->order_number)) {
                $this->subscriptionRepository->update(
                    ['order_number' => $payment->order_number],
                    [
                        'canceled_at' => null,
                        'status' => Subscription::STATUS_ACTIVE,
                        'status_updated_at' => Carbon::now(),
                    ],
                    $payment->platform_id
                );
            }
            else {
                $startDate = Carbon::createFromTimeString($payment->created_at, config('app.timezone'))->subMinutes(2);
                $endDate = Carbon::createFromTimeString($payment->created_at, config('app.timezone'))->addMinutes(2);
                $plans = $payment->plans->pluck('id')->toArray();

                $subscriptions = $this->subscriptionRepository->allBySubscriberAndPlans(
                    $payment->subscriber->id,
                    $plans,
                    $payment->platform_id,
                    [
                       'canceled_at' => ['op' => '!=', 'value' => null],
                       'raw' => "(subscriptions.created_at >= '{$startDate}' AND
                                subscriptions.created_at <= '{$endDate}')"
                    ],
                    ['id'],
                    count($plans)
                );

                $ids = $subscriptions->pluck('id')->toArray();
                if (!empty($ids)) {
                    $this->subscriptionRepository->updateById(
                        $ids,
                        [
                            'canceled_at' => null,
                            'status' => Subscription::STATUS_ACTIVE,
                            'status_updated_at' => Carbon::now()
                        ]
                    );
                }
            }
        }
        catch(Exception $e) {
            Log::error(
                'Can not enable subscription > ',
                [
                    'error' => $e->getMessage(),
                    'payload' => [
                        'payment_id' => $payment->id
                    ]
                ]
            );

            throw $e;
        }
    }

    public function cancelSubscriptionsAndPayments(
        string $orderNumber,
        ?string $cancellationReason = null,
        ?int $cancellationUserId = null
    ): bool {
        $allPayments = Payment::where('order_number', $orderNumber)->with('plans')->get();

        $subscriber = $allPayments->first()->subscriber;

        DB::beginTransaction();

        $plans = $allPayments->map->plans->flatten(1)->unique('id');

        foreach ($plans as $plan) {
            $canceled = $this->cancelSubscription($subscriber, $plan, $cancellationReason);
            if (!$canceled) {
                DB::rollBack();
                return false;
            }
        }

        $status = [
            Payment::STATUS_FAILED,
            Payment::STATUS_PENDING,
        ];

        $payments = $allPayments->whereIn('status', $status);
        foreach ($payments as $payment) {
            $payment->status = Payment::STATUS_CANCELED;
            $payment->cancellation_at = Carbon::now();
            $payment->cancellation_reason = $cancellationReason;
            if ($cancellationUserId) {
                $payment->cancellation_user = $cancellationUserId;
            } else {
                $payment->cancellation_origin = 'subscriber';
            }

            if (is_null($cancellationUserId)) {
                $payment->notes = implode('; ', array_filter([$payment->notes, 'Automatically canceled']));
            }

            $canceled = $payment->save();
            if (!$canceled) {
                DB::rollBack();
                return false;
            }
        }

        DB::commit();

        return true;
    }

    public function cancelUnpaidPayments(
        string $orderNumber,
        ?string $cancellationReason = null,
        ?int $cancellationUserId = null
    ): bool {
        DB::beginTransaction();

        $status = [
            Payment::STATUS_FAILED,
            Payment::STATUS_PENDING,
        ];

        $unpaidPayments = Payment::where('order_number', $orderNumber)->where('status', $status)->get();

        foreach ($unpaidPayments as $payment) {
            $payment->status = Payment::STATUS_CANCELED;
            $payment->cancellation_at = Carbon::now();
            $payment->cancellation_reason = $cancellationReason;
            if ($cancellationUserId) {
                $payment->cancellation_user = $cancellationUserId;
            }

            if (is_null($cancellationUserId)) {
                $payment->notes = implode('; ', array_filter([$payment->notes, 'Automatically canceled']));
            }

            $canceled = $payment->save();
            if (!$canceled) {
                DB::rollBack();
                return false;
            }
        }

        DB::commit();

        return true;
    }

}
