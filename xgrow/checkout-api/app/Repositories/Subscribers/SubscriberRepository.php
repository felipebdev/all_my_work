<?php

namespace App\Repositories\Subscribers;

use App\Repositories\BaseRepository;
use App\Repositories\Contracts\SubscriberRepositoryInterface;
use App\Services\Objects\SubscriberReportFilter;
use App\Subscriber;
use Illuminate\Support\Facades\DB;

class SubscriberRepository extends BaseRepository implements SubscriberRepositoryInterface
{
    public function model()
    {
        return Subscriber::class;
    }

    public function reportSubscriber(string $platformId, SubscriberReportFilter $filters)
    {
        return $this->model
            ->select(
                DB::raw(
                    "subscribers.id,
                        subscribers.document_number,
                        subscribers.main_phone,
                        subscribers.cel_phone,
                        subscribers.name,
                        subscribers.email,
                        subscribers.last_acess,
                        subscribers.created_at as created,
                        subscribers.status,
                        GROUP_CONCAT(DISTINCT plans.name) AS plans_name,
                        CONCAT(',', GROUP_CONCAT(DISTINCT plans.id), ',')  AS plans_id,
                        GROUP_CONCAT(plans.description)  AS plans_description,
                        subscribers.has_problem_access"
                )
            )

            ->leftJoin('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriber_id')
            ->leftJoin('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscribers.platform_id', '=', $platformId)
            ->where('subscribers.status', '!=', Subscriber::STATUS_LEAD)
            ->when($filters->search, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('subscribers.email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('subscribers.name', 'like', '%' . $searchTerm . '%');
                });
            })
            ->when($filters->plans, function ($query, $planId) {
                $ids = implode('|', array_map('intval', $planId));
                $query->havingRaw("plans_id REGEXP ',({$ids}),' ");
            })
            ->when($filters->subscriberStatus, function ($query, $subscriberStatus) {
                $query->WhereIn('subscribers.status', $subscriberStatus);
            })
            ->when($filters->createdPeriod, function ($query, $createdPeriod) {
                $query->whereBetween('subscribers.created_at', [$createdPeriod->startDate, $createdPeriod->endDate]);
            })
            ->when($filters->lastAccessedPeriod, function ($query, $lastAccessedPeriod) {
                $query->whereBetween('subscribers.last_acess', [$lastAccessedPeriod->startDate, $lastAccessedPeriod->endDate]);
            })
            ->when($filters->neverAccessed, function ($query, $neverAccessed) {
                if($neverAccessed){
                    $query->whereNull('subscribers.last_acess');
                }
            })
            ->groupBy('subscribers.id')
            ->orderBy('subscribers.created_at', 'DESC');
    }
}
