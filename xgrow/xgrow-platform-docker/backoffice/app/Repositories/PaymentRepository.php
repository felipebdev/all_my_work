<?php

namespace App\Repositories;

use App\Payment;
use App\Services\Objects\PaymentFilter;
use Illuminate\Database\Eloquent\Builder;

class PaymentRepository
{


    /**
     * @param PaymentFilter|null $filter
     * @return Builder
     */
    public function listAll(?PaymentFilter $filter = null): Builder{
        return  Payment::when($filter,function ($query, $filter) {
            return Payment::when($filter->status, function ($query, $status) {
                        $query->where('payments.status', '=', $status);
                    })
                    ->when($filter->paymentDate, function ($query, $periodFilter) {
                        $query->whereBetween('payments.payment_date', [$periodFilter->startDate, $periodFilter->endDate]);
                    });
        });

    }

    /**
     * Get payments by platform
     * @param PaymentFilter $filter
     * @return Builder
     */
    public function listByPlatform(PaymentFilter $filter): Builder{
        return $this->listAll($filter)
            ->join('platforms', 'payments.platform_id', '=', 'platforms.id')
            ->when($filter->status, function ($query, $status) {
                $query->where('payments.status', '=', $status);
            })
            ->when($filter->clientId, function ($query, $clientId) {
                $query->where('platforms.customer_id', '=', $clientId);
            })
            ->when($filter->paymentDate, function ($query, $periodFilter) {
                $query->whereBetween('payments.payment_date', [$periodFilter->startDate, $periodFilter->endDate]);
            });
    }

    /**
     * Get payments by subscribers
     * @param PaymentFilter $filter
     * @return Builder
     */
    public function listBySubscribers(PaymentFilter $filter): Builder{
        return $this->listAll($filter)
            ->join('subscribers', 'payments.subscriber_id', '=', 'subscribers.id')
            ->when($filter->subscriberId, function ($query, $subscriberId) {
                $query->where('subscribers.id', '=', $subscriberId);
            });
    }

}
