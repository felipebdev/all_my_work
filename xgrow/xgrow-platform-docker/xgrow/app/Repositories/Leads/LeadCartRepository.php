<?php

namespace App\Repositories\Leads;

use App\Lead;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Services\Objects\LeadReportFilter;
use App\Services\Objects\PeriodFilter;

/**
 * This class is responsible for handling the lead repository using "leads" table
 */
class LeadCartRepository extends BaseRepository implements LeadRepositoryInterface
{
    /**
     * List of cart_status that are considered Lead
     */
    public const LEAD_CART_STATUS_LIST = [
        Lead::CART_STATUS_INITIATED,
        Lead::CART_STATUS_ABANDONED,
        Lead::CART_STATUS_ORDERED,
        Lead::CART_STATUS_DENIED,
    ];

    public function model()
    {
        return Lead::class;
    }

    public function reportLead(string $platformId, LeadReportFilter $filters)
    {
        return $this->model
            ->selectRaw('
                leads.id,
                leads.name,
                leads.email,
                leads.cel_phone,
                leads.created_at as created,
                products.name AS products_name,
                products.id AS product_id,
                plans.description AS plan_description
            ')
            ->leftJoin('plans', 'plans.id', '=', 'leads.plan_id')
            ->leftJoin('products', 'plans.product_id', '=', 'products.id')
            ->where('leads.platform_id', '=', $platformId)
            ->whereIn('leads.cart_status', self::LEAD_CART_STATUS_LIST)
            ->when($filters->search, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('leads.email', 'like', '%'.$searchTerm.'%')
                        ->orWhere('leads.name', 'like', '%'.$searchTerm.'%')
                        ->orWhere('leads.cel_phone', 'like', '%'.$searchTerm.'%');
                });
            })
            ->when($filters->plans, function ($query, $planId) {
                $query->whereIn('product_id', $planId);
            })
            ->when($filters->createdPeriod, function ($query, PeriodFilter $createdPeriod) {
                $query->whereBetween('leads.created_at', [$createdPeriod->startDate, $createdPeriod->endDate]);
            })
            ->when($filters->onlyFailedTransactions, function ($query, $value) {
                $query->where('leads.cart_status', '=', Lead::CART_STATUS_DENIED);
            })
            ->orderBy('leads.created_at', 'DESC');
    }
}
