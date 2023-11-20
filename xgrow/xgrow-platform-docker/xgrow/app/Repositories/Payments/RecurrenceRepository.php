<?php

namespace App\Repositories\Payments;

use App\Payment;
use App\Recurrence;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\RecurrenceRepositoryInterface;
use App\Services\Objects\PeriodFilter;
use App\Services\Objects\SaleReportFilter;
use Illuminate\Support\Facades\DB;

class RecurrenceRepository extends BaseRepository implements RecurrenceRepositoryInterface {

    public function model() {
        return Recurrence::class;
    }

    public function countByPaymentStatus(
        string $status,
        array $where = [],
        string $platformId = null,
        SaleReportFilter $filters = null
    ) {
        $subquery = $this->model
            ->select('recurrences.id AS value')
            ->join('payment_recurrence', 'payment_recurrence.recurrence_id', 'recurrences.id')
            ->join('payments', 'payments.id', 'payment_recurrence.payment_id')
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
            ->leftJoin('subscriptions', ['subscriptions.plan_id' => 'plans.id', 'subscriptions.subscriber_id' => 'subscribers.id'])
            ->whereRaw('(`subscriptions`.`order_number` = `payments`.`order_number` OR ( `subscriptions`.`order_number` IS NULL ))')
            ->where('payments.type', Payment::TYPE_SUBSCRIPTION)
            ->where('payments.platform_id', '=', $platformId);

        $this->setWhere($subquery, $where);

        if ($filters instanceof SaleReportFilter) {
            $subquery->when($filters->plans, function ($query, $plansId) {
                $query->whereIn('payment_plan.plan_id', $plansId);
            })
            ->when($filters->products, function ($query, $productsId) {
                $query->join('products', 'plans.product_id', '=', 'products.id')
                    ->whereIn('products.id', $productsId);
            })
            ->when($filters->paymentStatus, function ($query, $statusPaymentFilter) {
                $query->whereIn('payments.status', $statusPaymentFilter);
            })
            ->when($filters->paymentMethod, function ($query, $typePaymentFilter) {
                $query->whereIn('payments.type_payment', $typePaymentFilter);
            })
            ->when($filters->subscriptionStatus, function ($query, $subscriptionStatus) {
                $query->whereIn('subscriptions.status', $subscriptionStatus);
            })
            ->when($filters->paymentPeriod, function ($query, $periodFilter) {
                $query->whereBetween('payments.payment_date', [$periodFilter->startDate, $periodFilter->endDate]);
            })
            ->when($filters->accessionPeriod, function ($query, $periodFilter) {
                $query->whereBetween('payments.created_at', [$periodFilter->startDate, $periodFilter->endDate]);
            })
            ->when($filters->cancelPeriod, function ($query, PeriodFilter $cancelPeriod) {
                $query->whereBetween('subscriptions.canceled_at', [$cancelPeriod->startDate, $cancelPeriod->endDate]);
            })
            ->when($filters->lastPaymentPeriod, function ($query, PeriodFilter $lastPayment) {
                $query->whereBetween('payments.payment_date', [$lastPayment->startDate, $lastPayment->endDate]);
            })
            ->when($filters->search, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                        $q->orWhere('subscribers.email', 'like', '%' . $searchTerm . '%')
                            ->orWhere('subscribers.name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('subscribers.document_number', 'like', '%' . $searchTerm . '%')
                            ->orWhere('payments.charge_code', 'like', '%' . $searchTerm . '%')
                            ->orWhere('payments.order_number', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        $subquery->groupBy('recurrences.id');
            //->havingRaw("MAX(payments.status) = '{$status}'");

        $subquerySql = $subquery->toSql();
        return DB::table(DB::raw("($subquerySql) as subquery"))
            ->mergeBindings($subquery->getQuery())
            ->count('*');
    }

    /**
     * @deprecated
     */
    public function getTotalByPaymentStatus(
        string $status,
        array $where = [],
        string $column = 'payments.customer_value',
        string $platformId = null,
        SaleReportFilter $filters = null
    ) {
        $subquery = $this->model
            ->select(DB::raw("{$column} AS value"))
            ->join('payment_recurrence', 'payment_recurrence.recurrence_id', 'recurrences.id')
            ->join('payments', 'payments.id', 'payment_recurrence.payment_id')
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
            ->leftJoin('subscriptions', ['subscriptions.plan_id' => 'plans.id', 'subscriptions.subscriber_id' => 'subscribers.id'])
            ->where('payments.type', Payment::TYPE_SUBSCRIPTION)
            ->where('payments.platform_id', '=', $platformId);

        $this->setWhere($subquery, $where);

        if ($filters instanceof SaleReportFilter) {
            $subquery->when($filters->plans, function ($query, $plansId) {
                $query->whereIn('payment_plan.plan_id', $plansId);
            })
            ->when($filters->products, function ($query, $productsId) {
                $query->join('products', 'plans.product_id', '=', 'products.id')
                    ->whereIn('products.id', $productsId);
            })
            ->when($filters->paymentStatus, function ($query, $statusPaymentFilter) {
                $query->WhereIn('payments.status', $statusPaymentFilter);
            })
            ->when($filters->paymentMethod, function ($query, $typePaymentFilter) {
                $query->whereIn('payments.type_payment', $typePaymentFilter);
            })
            ->when($filters->subscriptionStatus, function ($query, $subscriptionStatus) {
                $query->whereIn('subscriptions.status', $subscriptionStatus);
            })
            ->when($filters->paymentPeriod, function ($query, $periodFilter) {
                $query->whereBetween('payments.payment_date', [$periodFilter->startDate, $periodFilter->endDate]);
            })
            ->when($filters->accessionPeriod, function ($query, $periodFilter) {
                $query->whereBetween('payments.created_at', [$periodFilter->startDate, $periodFilter->endDate]);
            })
            ->when($filters->cancelPeriod, function ($query, PeriodFilter $cancelPeriod) {
                $query->whereBetween('subscriptions.canceled_at', [$cancelPeriod->startDate, $cancelPeriod->endDate]);
            })
            ->when($filters->lastPaymentPeriod, function ($query, PeriodFilter $lastPayment) {
                $query->whereBetween('payments.payment_date', [$lastPayment->startDate, $lastPayment->endDate]);
            })
            ->when($filters->search, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                        $q->orWhere('subscribers.email', 'like', '%' . $searchTerm . '%')
                            ->orWhere('subscribers.name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('subscribers.document_number', 'like', '%' . $searchTerm . '%')
                            ->orWhere('payments.charge_code', 'like', '%' . $searchTerm . '%')
                            ->orWhere('payments.order_number', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        $subquery->groupBy('recurrences.id')
            ->havingRaw("MAX(payments.status) = '{$status}'");

        $subquerySql = $subquery->toSql();
        return DB::table(DB::raw("($subquerySql) as subquery"))
            ->mergeBindings($subquery->getQuery())
            ->sum('subquery.value');
    }

    public function getSubscriptionTotal(string $column, array $where, string $platformId, SaleReportFilter $filters)
    {
        $subquery = $this->model
            ->selectRaw("{$column} AS value")
            ->join('payment_recurrence', 'payment_recurrence.recurrence_id', 'recurrences.id')
            ->join('payments', 'payments.id', 'payment_recurrence.payment_id')
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
            ->leftJoin('subscriptions', ['subscriptions.plan_id' => 'plans.id', 'subscriptions.subscriber_id' => 'subscribers.id'])
            ->where('payments.type', Payment::TYPE_SUBSCRIPTION)
            ->where('payments.platform_id', '=', $platformId);

        $this->setWhere($subquery, $where);

        if ($filters instanceof SaleReportFilter) {
            $subquery->when($filters->plans, function ($query, $plansId) {
                $query->whereIn('payment_plan.plan_id', $plansId);
            })
                ->when($filters->products, function ($query, $productsId) {
                    $query->join('products', 'plans.product_id', '=', 'products.id')
                        ->whereIn('products.id', $productsId);
                })
                ->when($filters->paymentStatus, function ($query, $statusPaymentFilter) {
                    $query->WhereIn('payments.status', $statusPaymentFilter);
                })
                ->when($filters->paymentMethod, function ($query, $typePaymentFilter) {
                    $query->whereIn('payments.type_payment', $typePaymentFilter);
                })
                ->when($filters->subscriptionStatus, function ($query, $subscriptionStatus) {
                    $query->whereIn('subscriptions.status', $subscriptionStatus);
                })
                ->when($filters->paymentPeriod, function ($query, $periodFilter) {
                    $query->whereBetween('payments.payment_date', [$periodFilter->startDate, $periodFilter->endDate]);
                })
                ->when($filters->accessionPeriod, function ($query, $periodFilter) {
                    $query->whereBetween('payments.created_at', [$periodFilter->startDate, $periodFilter->endDate]);
                })
                ->when($filters->cancelPeriod, function ($query, PeriodFilter $cancelPeriod) {
                    $query->whereBetween('subscriptions.canceled_at', [$cancelPeriod->startDate, $cancelPeriod->endDate]);
                })
                ->when($filters->lastPaymentPeriod, function ($query, PeriodFilter $lastPayment) {
                    $query->whereBetween('payments.payment_date', [$lastPayment->startDate, $lastPayment->endDate]);
                })
                ->when($filters->search, function ($query, $searchTerm) {
                    $query->where(function ($q) use ($searchTerm) {
                        $q->orWhere('subscribers.email', 'like', '%' . $searchTerm . '%')
                            ->orWhere('subscribers.name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('subscribers.document_number', 'like', '%' . $searchTerm . '%')
                            ->orWhere('payments.charge_code', 'like', '%' . $searchTerm . '%')
                            ->orWhere('payments.order_number', 'like', '%' . $searchTerm . '%');
                    });
                });
        }

        $subquery->groupBy('recurrences.id');

        $subquerySql = $subquery->toSql();
        return DB::table(DB::raw("($subquerySql) as subquery"))
            ->mergeBindings($subquery->getQuery())
            ->sum('subquery.value');
    }

}
