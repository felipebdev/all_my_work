<?php

namespace App\Rules;

use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Rules\Objects\DefaultActiveStrategy;
use App\Rules\Objects\OrderbumpActiveStrategy;
use App\Rules\Objects\SubscriptionActiveStrategy;
use Illuminate\Contracts\Validation\Rule;

class SubscriptionActiveRule implements Rule
{
    private $platformId;
    private $subscriberId;
    private $planId;
    private $orderbump;
    private $plans;
    private $subscriptionRepository;
    private $subscriptions = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        string $platformId, 
        string $subscriberId, 
        string $planId, 
        ?array $orderBump
    ) {
        $this->platformId = $platformId;
        $this->subscriberId = $subscriberId;
        $this->planId = $planId;
        $this->orderbump = $orderBump;
        $this->subscriptionRepository = app()->make(SubscriptionRepositoryInterface::class);

        $this->plans = [$planId];
        if (!empty($orderBump)) {
            $this->plans = array_merge($this->plans, $orderBump);
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $subscriptions = $this->subscriptionRepository->allBySubscriberAndPlans(
            $this->subscriberId,
            $this->plans,
            $this->platformId,
            ['canceled_at' => null, 'payment_pendent' => null],
            ['plan_id']
        );

        $this->subscriptions = $subscriptions->unique('plan_id')->pluck('plan_id')->toArray();
        return !($subscriptions->count() > 0);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $class = (empty($this->orderbump)) ? new DefaultActiveStrategy($this->planId, $this->subscriptions)
            : new OrderbumpActiveStrategy($this->planId, $this->subscriptions, $this->orderbump);
        
        $message = (new SubscriptionActiveStrategy($class))->getMessage();

        return $message;
    }
}
