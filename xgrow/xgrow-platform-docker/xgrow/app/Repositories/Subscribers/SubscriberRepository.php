<?php

namespace App\Repositories\Subscribers;

use App\Payment;
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
            ->selectRaw("
                subscribers.id,
                subscribers.document_number,
                subscribers.main_phone,
                subscribers.cel_phone,
                subscribers.name,
                subscribers.email,
                subscribers.email_bounce_id,
                subscribers.email_bounce_description,
                DATE_FORMAT(subscribers.login, '%Y-%m-%dT%TZ') as login,
                subscribers.created_at as created,
                subscriptions.status,
                GROUP_CONCAT(DISTINCT products.name) AS products_name,
                GROUP_CONCAT(DISTINCT products.id)  AS products_id,
                CONCAT(',', GROUP_CONCAT(DISTINCT products.id), ',')  AS product_id,
                GROUP_CONCAT(plans.description) AS plans_description,
                subscribers.has_problem_access"
            )
            ->leftJoin('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriber_id')
            ->leftJoin('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->leftJoin('products', 'plans.product_id', '=', 'products.id')
            ->where('subscribers.platform_id', '=', $platformId)
            ->where('subscribers.status', '!=', Subscriber::STATUS_LEAD)
            ->whereNull('subscribers.deleted_at')
            ->when($filters->search, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('subscribers.email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('subscribers.name', 'like', '%' . $searchTerm . '%');
                });
            })
            ->when($filters->plans, function ($query, $planId) {
                $ids = implode('|', array_map('intval', $planId));
                $query->havingRaw("product_id REGEXP ',({$ids}),' ");
            })
            ->when($filters->subscriberStatus, function ($query, $subscriptionStatus) {
                $query->WhereIn('subscriptions.status', $subscriptionStatus);
            })
            ->when($filters->createdPeriod, function ($query, $createdPeriod) {
                $query->whereBetween('subscribers.created_at', [$createdPeriod->startDate, $createdPeriod->endDate]);
            })
            ->when($filters->lastAccessedPeriod, function ($query, $lastAccessedPeriod) {
                $query->whereBetween(
                    'subscribers.login',
                    [$lastAccessedPeriod->startDate, $lastAccessedPeriod->endDate]
                );
            })
            ->when($filters->neverAccessed, function ($query, $neverAccessed) {
                if ($neverAccessed) {
                    $query->whereNull('subscribers.login');
                }
            })
            ->when($filters->emailWrongFilter, function ($query, $emailWrongFilter) {
                if ($emailWrongFilter) {
                    $query->whereNotNull('subscribers.email_bounce_id');
                }
            })
            ->groupBy('subscribers.id');
    }

    /**
     * @param $subscriberId
     * @return mixed
     */
    public function listSubscriberPayments($subscriberId, int $offset)
    {
        return Payment::select(
            'payments.id AS payment_id',
            'payment_plan.id AS payment_plan_id',
            'payments.multiple_means AS payment_multiple_means',
            'payments.order_number AS payment_order_number',
            'payments.order_code AS payment_order_code',
            'payments.payment_date',
            'products.name AS products_name',
            'plans.name AS plans_name',
            'payments.status AS payment_status',
            DB::raw("CASE payments.type_payment
                    WHEN 'credit_card' THEN 'Cartão de Crédito'
                    WHEN 'boleto' THEN 'Boleto'
                    WHEN 'pix' THEN 'PIX'
                END AS payment_type"),
            DB::raw("CASE payments.payment_source
                    WHEN 'C' THEN 'Checkout'
                    WHEN 'L' THEN 'Área de Aprendizado'
                    WHEN 'A' THEN 'Automática'
                    WHEN 'O' THEN 'One Click Buy'
                END AS payment_source"),
            'payment_plan.plan_value AS payment_plan_plan_value',
            DB::raw("CASE
                    WHEN payment_plan.customer_value IS null THEN
                    IF(payment_plan.type = 'order_bump', 0, payments.customer_value)
                    ELSE
                    payment_plan.customer_value
                END AS payment_plan_customer_value")
        )
            ->join('subscribers', 'payments.subscriber_id', '=', 'subscribers.id')
            ->join('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->join('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->join('products', 'plans.product_id', '=', 'products.id')
            ->where('subscriber_id', '=', $subscriberId)
            ->orderBy('payments.id', 'ASC')
            ->paginate($offset);
    }
}
