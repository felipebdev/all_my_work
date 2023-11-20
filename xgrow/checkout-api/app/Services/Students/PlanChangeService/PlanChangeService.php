<?php

namespace App\Services\Students\PlanChangeService;

use App\Exceptions\Students\OperationNotAllowedException;
use App\Exceptions\Students\PlanNotFoundException;
use App\Exceptions\Students\SubscriptionNotFoundException;
use App\Plan;
use App\Repositories\Plans\StudentPlanChangeRepository;
use App\Subscription;

class PlanChangeService
{

    private StudentPlanChangeRepository $planChangeRepository;

    public function __construct(StudentPlanChangeRepository $planChangeRepository)
    {
        $this->planChangeRepository = $planChangeRepository;
    }

    public function listAvailablePlans(string $productId): array
    {
        $plans = $this->planChangeRepository->listAvailablePlansByProductId($productId);

        return $plans->map(fn(Plan $plan, $key) => [
            'id' => $plan->id ?? null,
            'name' => $plan->name ?? null,
            'message' => $plan->message ?? null,
            'recurrence' => $plan->recurrence ?? null,
            'currency' => $plan->currency ?? null,
            'price' => $plan->getPrice() ?? null, //Check promotional price
            'original_price' => $plan->price ?? null,
            'discount' => $plan->discount ?? null,
            'freedays' => $plan->freedays ?? null,
            'freedays_type' => $plan->freedays_type ?? null,
            'charge_until' => $plan->charge_until ?? null,
            'type_plan' => $plan->type_plan ?? null,
            'installment' => $plan->installment ?? null,
            'description' => $plan->description ?? null,
            'image_id' => $plan->image_id ?? null,
            'image_url' => $plan->image->filename ?? null,
            'message_success_checkout' => null,
            //'learning_area_type' => $this->getLearningAreaType($item->plans->product),
            'use_promotional_price' => $plan->use_promotional_price ?? null,
            'recurrence_description' => $plan->recurrence_description ?? null,
            'promotional_periods' => $plan->promotional_periods ?? null,
            'promotional_price' => $plan->promotional_price ?? null
        ])->toArray();
    }

    /**
     * @param  string  $subscriptionId
     * @param  string  $productId
     * @param  string  $toPlanId
     * @return \App\Subscription
     * @throws \App\Exceptions\Students\OperationNotAllowedException
     * @throws \App\Exceptions\Students\PlanNotFoundException
     * @throws \App\Exceptions\Students\RecurrenceNotFoundException
     * @throws \App\Exceptions\Students\SubscriptionNotFoundException
     */
    public function changeSubscriptionToAnotherPlan(
        string $subscriptionId,
        string $productId,
        string $toPlanId
    ): Subscription {
        $subscription = Subscription::query()
            ->where('id', $subscriptionId)
            ->first();

        if (!$subscription) {
            throw new SubscriptionNotFoundException();
        }

        $toPlan = Plan::query()
            ->where('product_id', $productId)
            ->where('id', $toPlanId)
            ->first();

        if (!$toPlan) {
            throw new PlanNotFoundException();
        }

        if ($subscription->plan_id == $toPlan->id) {
            throw new OperationNotAllowedException();
        }

        if (!$toPlan->allow_change) {
            throw new OperationNotAllowedException();
        }

        return $this->planChangeRepository->changeSubscription($subscription, $toPlan);
    }

}
