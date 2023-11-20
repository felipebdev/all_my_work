<?php

namespace App\Services\Actions;

use App\Recurrence;
use App\Subscriber;

abstract class NotifyBoletoPixActionTemplate
{
    protected $platformId = null;
    protected $recurrenceId = null;
    protected $subscriberId = null;

    protected array $paymentTypes = [
        Recurrence::PAYMENT_METHOD_BOLETO,
        Recurrence::PAYMENT_METHOD_PIX
    ];

    abstract public function __invoke();

    public function setPlatformId($platformId)
    {
        $this->platformId = $platformId;
        return $this;
    }

    public function setRecurrenceId($recurrenceId)
    {
        $this->recurrenceId = $recurrenceId;
        return $this;
    }

    public function setSubscriberId($subscriberId)
    {
        $this->subscriberId = $subscriberId;
        return $this;
    }

    /**
     * @param  string  $begin
     * @param  string  $end
     * @return \Illuminate\Database\Eloquent\Collection<Recurrence>
     */
    protected function getRecurrencesWithNextPaymentDateBetween(string $begin, string $end)
    {
        $nextPaymentDate = 'DATE(recurrences.last_payment + INTERVAL recurrences.recurrence DAY)';
        $sqlCondition = "{$nextPaymentDate} BETWEEN ? AND ?";

        $recurrences = Recurrence::query()
            ->selectRaw("{$nextPaymentDate} as next_payment_date")
            ->selectRaw('
                recurrences.id AS id,
                recurrences.subscriber_id,
                recurrences.recurrence,
                recurrences.last_invoice AS last_invoice,
                recurrences.last_payment AS last_payment,
                recurrences.current_charge AS current_charge,
                recurrences.type,
                recurrences.payment_method,
                recurrences.total_charges AS total_charges,
                recurrences.plan_id,
                recurrences.order_number AS order_number
            ')
            ->join('subscriptions', function ($query) {
                $query->on('subscriptions.subscriber_id', '=', 'recurrences.subscriber_id')
                    ->on('subscriptions.plan_id', '=', 'recurrences.plan_id');
            })
            ->leftJoin('subscribers', 'subscribers.id', '=', 'recurrences.subscriber_id')
            ->leftJoin('plans', 'plans.id', '=', 'recurrences.plan_id')
            ->whereRaw($sqlCondition, [$begin, $end])
            ->whereIn('recurrences.payment_method', $this->paymentTypes)
            ->whereNull('subscriptions.canceled_at') // subscription not canceld
            ->whereNull('subscriptions.payment_pendent') // subscription not pending
            ->whereNotNull('subscriptions.order_number') // order_number required
            ->where('subscribers.status', Subscriber::STATUS_ACTIVE) // active subscriber only
            ->where('plans.status', '=', 1) // active plan
            ->when($this->platformId, function ($query, $platformId) {
                $query->where('subscriptions.platform_id', $platformId);
            })
            ->when($this->subscriberId, function ($query, $subscriberId) {
                $query->where('subscribers.id', $subscriberId);
            })
            ->when($this->recurrenceId, function ($query, $recurrenceId) {
                $query->where('recurrences.id', $recurrenceId);
            })
            ->groupBy('subscriptions.id')
            ->orderBy('recurrences.id')
            ->get();

        return $recurrences;
    }
}
