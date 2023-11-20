<?php

namespace App\Repositories\Leads;

use App\Repositories\BaseRepository;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Services\Objects\LeadReportFilter;
use App\Subscriber;
use Illuminate\Support\Facades\DB;

/**
 * @deprecated Replaced by LeadCartRepository
 */
class LeadRepository extends BaseRepository implements LeadRepositoryInterface
{
    public function model()
    {
        return Subscriber::class;
    }

    public function reportLead(string $platformId, LeadReportFilter $filters)
    {
        return $this->model
            ->select(
                DB::raw(
                    "subscribers.id,
                    subscribers.name,
                    subscribers.email,
                    subscribers.cel_phone,
                    subscribers.created_at as created,
                    products.name AS products_name,
                    products.id AS product_id,
                    plans.description AS plan_description,
                    (
                        SELECT COUNT(transactions.id)
                        FROM transactions
                        WHERE transactions.status='failed'
                            AND transactions.platform_id='{$platformId}'
                            AND transactions.subscriber_id=subscribers.id
                    ) AS failed_transactions"
                )
            )
            ->leftJoin('plans', 'plans.id', '=', 'subscribers.plan_id')
            ->leftJoin('products', 'plans.product_id', '=', 'products.id')
            ->where('subscribers.platform_id', '=', $platformId)
            ->where('subscribers.status', '=', Subscriber::STATUS_LEAD)
            ->when($filters->search, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('subscribers.email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('subscribers.name', 'like', '%' . $searchTerm . '%');
                });
            })
            ->when($filters->plans, function ($query, $planId) {
                $query->whereIn('product_id', $planId);
            })
            ->when($filters->createdPeriod, function ($query, $createdPeriod) {
                $query->whereBetween('subscribers.created_at', [$createdPeriod->startDate, $createdPeriod->endDate]);
            })
            ->when($filters->onlyFailedTransactions, function ($query, $value) {
                if (!empty($value)) {
                    $query->havingRaw('failed_transactions > 0');
                }
            })
            ->orderBy('subscribers.created_at', 'DESC');
    }
}
