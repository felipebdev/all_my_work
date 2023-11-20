<?php

namespace App\Repositories\Plans;

use App\Exceptions\Students\RecurrenceNotFoundException;
use App\Plan;
use App\Recurrence;
use App\RecurrencePlanChange;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StudentPlanChangeRepository
{

    /**
     * List of related plans available to change
     *
     * @param  string  $productId
     * @return \Illuminate\Support\Collection
     */
    public function listAvailablePlansByProductId(string $productId): Collection
    {
        $plans = Plan::query()
            ->where('product_id', $productId)
            ->where('allow_change', true)
            ->get();

        return $plans;
    }

    /**
     * Change Subscription (and related Recurrence) to another Plan
     *
     * @param  \App\Subscription  $subscription
     * @param  \App\Plan  $toPlan
     * @return \App\Subscription
     * @throws \App\Exceptions\Students\RecurrenceNotFoundException
     */
    public function changeSubscription(Subscription $subscription, Plan $toPlan): Subscription
    {
        $recurrence = Recurrence::query()
            ->where('plan_id', $subscription->plan_id)
            ->where('subscriber_id', $subscription->subscriber_id)
            ->first();

        if (!$recurrence) {
            throw new RecurrenceNotFoundException();
        }

        $fromPlanId = $recurrence->plan_id;

        $recurrence->plan_id = $toPlan->id;
        $recurrence->recurrence = $toPlan->recurrence;
        $recurrence->default_installments = 1; // force single installment on plan change
        $recurrence->save();

        $subscription->plan_id = $toPlan->id;
        $subscription->save();

        RecurrencePlanChange::insert([
            'origin' => 'subscriber', // subscriber or producer
            'recurrence_id' => $recurrence->id,
            'old_plan_id' => $fromPlanId,
            'new_plan_id' => $toPlan->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return $subscription;
    }

}
