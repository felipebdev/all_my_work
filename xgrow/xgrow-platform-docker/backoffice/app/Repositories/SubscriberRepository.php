<?php

namespace App\Repositories;

use App\Services\Objects\SubscriberFilter;
use App\Subscriber;
use Illuminate\Database\Eloquent\Builder;

class SubscriberRepository
{


    /**
     * Get Subscribers
     * @param SubscriberFilter|null $filter
     * @return Builder
     */
    public function listAll(?SubscriberFilter $filter = null): Builder{
        return  Subscriber::when($filter,function ($query, $filter) {
            return Subscriber::when($filter->search, function ($query, $search) {
                    $query->where('subscribers.name', 'LIKE', "%{$search}%");
                    $query->orWhere('subscribers.email', '=', $search);
                    $query->orWhere('subscribers.document_number', '=', $search);
                })
                ->when($filter->status, function ($query, $status) {
                    $query->where('subscribers.status', '=', $status);
                })
                ->when(is_null($filter->status), function ($query) {
                    $query->where('subscribers.status', '<>', 'lead');
                })
                ->when($filter->status, function ($query, $status) {
                    $query->where('subscribers.status', '=', $status);
                })
                ->when($filter->createdPeriod, function ($query, $periodFilter) {
                    $query->whereBetween('subscribers.created_at', [$periodFilter->startDate, $periodFilter->endDate]);
                })
                ->when($filter->subscribersId, function ($query, $subscribersId) {
                    $query->whereIn('subscribers.id', $subscribersId);
                })
                ->when($filter->emails, function ($query, $emails) {
                    $query->whereIn('subscribers.email', $emails);
                })
                ->when($filter->documentNumber, function ($query, $documentNumber) {
                    $query->where('subscribers.document_number', '=', $documentNumber);
                });
        });
    }

    /**
     * Get subscribers by client
     * @param SubscriberFilter|null $filter
     * @return Builder
     */
    public function listSubscriberClient(?SubscriberFilter $filter = null): Builder{
        return $this->listAll($filter)
            ->join('platforms', 'subscribers.platform_id', '=', 'platforms.id')
            ->when($filter,function ($query, $filter) {
                return $query->when($filter->clientId, function ($query, $clientId) {
                        $query->where('platforms.customer_id', $clientId);
                    });
            });
    }

    /**
     * Change subscriber status
     *
     * @param int $id
     * @param string $status
     * @return false|mixed
     */
    public function changeStatus(int $id, string $status)
    {
        $subscriber = Subscriber::find($id);
        $subscriber->status = $status;
        $subscriber->save();
        return $subscriber;
    }

    /**
     * Delete subscriber
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->delete();
    }

}
