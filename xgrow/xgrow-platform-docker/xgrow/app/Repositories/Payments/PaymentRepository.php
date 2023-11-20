<?php

namespace App\Repositories\Payments;

use App\Http\Controllers\Mundipagg\RecipientController;
use App\Payment;
use App\Platform;
use App\Services\Checkout\BalanceService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Services\Objects\SaleReportFilter;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Services\Objects\PeriodFilter;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function model()
    {
        return Payment::class;
    }

    public function update(array $where, array $data)
    {
        return $this->model
            ->where($where)
            ->platform()
            ->update($data);
    }

    public function batchUpdate(array $ids, array $data)
    {
        return $this->model
            ->whereIn('id', $ids)
            ->update($data);
    }

    public function allByOrderNumberAndStatus(
        string $orderNumber,
        array $status,
        array $columns = ['*']
    ) {
        return $this->model
            ->select($columns)
            ->platform()
            ->where(['order_number' => $orderNumber])
            ->whereIn('status', $status)
            ->get();
    }

    public function getFromId(
        int $id,
        int $limit = 1,
        array $where = [],
        array $columns = ['*']
    ) {
        $query = $this->model
            ->select($columns)
            ->platform()
            ->where('id', '>=', $id);

        if (!empty($where)) {
            $query->where($where);
        }

        return $query->limit($limit)->get();
    }

    public function reportTransactionSale(string $platformId, SaleReportFilter $filters)
    {
        $platform = Platform::with(['client:id,percent_split'])->findOrFail($platformId);

        $clientTax = ($platform->client->percent_split) ? (100 - $platform->client->percent_split) / 100 : 0;

        $query = $this->model
            ->select(
                DB::raw(
                    "plans.id AS plans_id,
                    plans.name AS plans_name,
                    plans.freedays_type AS freedays_type,
                    plans.freedays AS freedays,
                    plans.price AS plans_price,
                    plans.order_bump_plan_id,
                    products.id AS product_id,
                    CASE
                         WHEN payments.type IN ('U', 'R') THEN CONCAT(products.name, ' [R]')
                         WHEN payments.type IN ('P') THEN products.name
                    END AS product_name,
                    subscribers.id AS subscribers_id,
                    subscribers.name AS subscribers_name,
                    subscribers.email AS subscribers_email,
                    subscribers.document_type,
                    subscribers.document_number AS document_number,
                    subscribers.cel_phone,
                    subscribers.address_street,
                    subscribers.address_number,
                    subscribers.address_comp,
                    subscribers.address_district,
                    subscribers.address_zipcode,
                    subscribers.address_city,
                    subscribers.address_state,
                    subscribers.address_country,
                    (
                        SELECT GROUP_CONCAT(DISTINCT p.order_bump_plan_id)
                        FROM payment_plan pp
                        JOIN plans p ON p.id = pp.plan_id
                        WHERE pp.payment_id = payments.id
                            AND payments.platform_id = '{$platformId}'
                        GROUP BY pp.payment_id
                        ORDER BY pp.created_at ASC
                    ) AS order_bump,
                    (
                        SELECT GROUP_CONCAT(DISTINCT p.id)
                        FROM payments p
                        WHERE p.order_code = payments.order_code
                            AND payments.platform_id = '{$platformId}'
                    ) AS payment_multiple_cards_id,
                    subscriptions.created_at as subscription_created_at,
                    subscriptions.canceled_at as cancellation_date,
                    payments.installments,
                    payments.type AS payment_type,
                    payments.service_value,
                    payments.price,
                    payments.plans_value,
                    TRUNCATE(plans.price * {$clientTax}, 2) AS tax_value,
                    {$clientTax} as client_tax,
                    payments.customer_value AS customer_value,
                    payments.payment_date AS payment_payment_date,
                    payments.created_at AS payment_created_at,
                    payments.updated_at AS payment_updated_at,
                    payments.status AS payments_status,
                    payments.order_code AS transactions_id,
                    payments.type_payment,
                    payments.payment_source,
                    payments.charge_code,
                    payments.id AS payment_id,
                    NULL AS commission,
                    payments.order_number AS payment_order_number,
                    payments.multiple_means AS payment_multiple_means,
                    coupons.id AS coupon_id,
                    coupons.code AS coupon_code,
                    coupons.value AS coupon_value,
                    coupons.value_type AS coupon_type,
                    payment_plan.tax_value AS payment_plan_tax_value,
                    payment_plan.plan_value AS payment_plan_plan_value,
                    payment_plan.plan_price AS payment_plan_plan_price,
                    payment_plan.coupon_id AS payment_plan_coupon_id,
                    payment_plan.coupon_code AS payment_plan_coupon_code,
                    payment_plan.coupon_value AS payment_plan_coupon_value,
                    payment_plan.type AS payment_plan_type,
                    payment_plan.customer_value AS payment_plan_customer_value,
                    CASE payments.type
                         WHEN 'U' THEN CONCAT(payments.installment_number, '/', payments.installments)
                         WHEN 'R' THEN CONCAT(payments.installment_number, '/', IF(plans.charge_until = 0, '∞', plans.charge_until))
                         WHEN 'P' THEN CONCAT(payments.installments, 'x')
                    END AS actual_installment"
                )
            )
            ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            //->leftJoin('payment_plan_split', 'payment_plan_split.payment_plan_id', '=', 'payment_plan.id')
            ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->leftJoin('products', 'plans.product_id', '=', 'products.id')
            ->leftJoin('coupons', 'coupons.id', '=', 'payments.coupon_id')
            ->leftJoin('payment_recurrence', 'payments.id', '=', 'payment_recurrence.payment_id')
            ->leftJoin('recurrences', 'payment_recurrence.recurrence_id', '=', 'recurrences.id')
            ->leftJoin('subscriptions', ['subscriptions.plan_id' => 'plans.id', 'subscriptions.subscriber_id' => 'subscribers.id'])
            ->whereRaw('(`subscriptions`.`order_number` = `payments`.`order_number` OR ( `subscriptions`.`order_number` IS NULL ))')
            ->where('payments.platform_id', '=', $platformId)
            //->where('payment_plan_split.type', '=', 'C')
            ->when($filters->products, function ($query, $productsId) {
                $query->whereIn('products.id', $productsId);
            })
            ->when($filters->paymentMethod, function ($query, $paymentMethodFilter) {
                $query->whereIn('payments.type_payment', $paymentMethodFilter);
            })
            ->when($filters->paymentStatus, function ($query, $statusPaymentFilter) {
                $query->whereIn('payments.status', $statusPaymentFilter);
            })
            ->when($filters->paymentType, function ($query, $paymentTypeFilter) {
                $query->whereIn('payments.type', $paymentTypeFilter);
            })
            ->when($filters->paymentPeriod, function ($query, $periodFilter) {
                $query->whereBetween('payments.payment_date', [$periodFilter->startDate, $periodFilter->endDate]);
            })
            ->when($filters->accessionPeriod, function ($query, $periodFilter) {
                $query->whereBetween('payments.created_at', [$periodFilter->startDate, $periodFilter->endDate]);
            })
            ->when($filters->onlyPaymentWithMultipleMeans, function ($query, $multipleMeans) {
                $query->where('payments.multiple_means', $multipleMeans);
            })
            ->when($filters->onlyPaymentWithCoupon, function ($query, $hasCoupon) {
                $query->where('payments.coupon_id', '>', 0);
            })
            ->when($filters->search, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('subscribers.email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('subscribers.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('subscribers.document_number', 'like', '%' . $searchTerm . '%')
                        ->orWhere('payments.charge_code', 'like', '%' . $searchTerm . '%')
                        ->orWhere('payments.order_number', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('payments.created_at', 'DESC');
        //dd($query->toSql());
        return $query;
    }

    /**
     * @deprecated Replaced by reportTransactionSale() on v0.23
     */
    public function reportSingleSale(string $platformId, SaleReportFilter $filters)
    {
        $platform = Platform::with([
            'client' => function ($query) {
                $query->select('id', 'percent_split');
            }
        ])
            ->findOrFail($platformId);

        $clientTax = ($platform->client->percent_split) ? (100 - $platform->client->percent_split) / 100 : 0;

        return $this->model
            ->select(
                DB::raw(
                    "plans.id AS plans_id,
                    plans.name AS plans_name,
                    plans.freedays_type AS freedays_type,
                    plans.freedays AS freedays,
                    plans.price AS plans_price,
                    plans.order_bump_plan_id,
                    subscribers.id AS subscribers_id,
                    subscribers.name AS subscribers_name,
                    subscribers.email AS subscribers_email,
                    subscribers.document_type,
                    subscribers.document_number AS document_number,
                    subscribers.cel_phone,
                    subscribers.address_street,
                    subscribers.address_number,
                    subscribers.address_comp,
                    subscribers.address_district,
                    subscribers.address_zipcode,
                    subscribers.address_city,
                    subscribers.address_state,
                    subscribers.address_country,
                    (
                        SELECT GROUP_CONCAT(DISTINCT p.order_bump_plan_id)
                        FROM payment_plan pp
                        JOIN plans p ON p.id = pp.plan_id
                        WHERE pp.payment_id = payments.id
                            AND payments.platform_id = '{$platformId}'
                        GROUP BY pp.payment_id
                        ORDER BY pp.created_at ASC
                    ) AS order_bump,
                    (
                        SELECT GROUP_CONCAT(DISTINCT p.id)
                        FROM payments p
                        WHERE p.order_code = payments.order_code
                            AND payments.platform_id = '{$platformId}'
                    ) AS payment_multiple_cards_id,
                    payments.installments,
                    payments.service_value,
                    payments.price,
                    payments.plans_value,
                    TRUNCATE(plans.price * {$clientTax}, 2) AS tax_value,
                    {$clientTax} as client_tax,
                    payments.customer_value AS customer_value,
                    payments.payment_date,
                    payments.created_at AS payment_created_at,
                    payments.updated_at AS payment_updated_at,
                    payments.status AS payments_status,
                    payments.order_code AS transactions_id,
                    payments.type_payment,
                    payments.charge_code,
                    payments.id AS payment_id,
                    payments.order_number AS payment_order_number,
                    coupons.id AS coupon_id,
                    coupons.code AS coupon_code,
                    coupons.value AS coupon_value,
                    coupons.value_type AS coupon_type,
                    payment_plan.tax_value AS payment_plan_tax_value,
                    payment_plan.plan_value AS payment_plan_plan_value,
                    payment_plan.plan_price AS payment_plan_plan_price,
                    payment_plan.coupon_id AS payment_plan_coupon_id,
                    payment_plan.coupon_code AS payment_plan_coupon_code,
                    payment_plan.coupon_value AS payment_plan_coupon_value,
                    payment_plan.type AS payment_plan_type,
                    payment_plan.customer_value AS payment_plan_customer_value"
                )
            )
            ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->leftJoin('coupons', 'coupons.id', '=', 'payments.coupon_id')
            ->where('payments.type', 'P')
            ->where('payments.platform_id', '=', $platformId)
            ->when($filters->plans, function ($query, $plansId) {
                $query->whereIn('plans.id', $plansId);
            })
            ->when($filters->paymentMethod, function ($query, $typePaymentFilter) {
                $query->whereIn('payments.type_payment', $typePaymentFilter);
            })
            ->when($filters->paymentStatus, function ($query, $statusPaymentFilter) {
                $query->WhereIn('payments.status', $statusPaymentFilter);
            })
            ->when($filters->paymentPeriod, function ($query, $periodFilter) {
                $query->whereBetween('payments.payment_date', [$periodFilter->startDate, $periodFilter->endDate]);
            })
            ->when($filters->accessionPeriod, function ($query, $periodFilter) {
                $query->whereBetween('payments.created_at', [$periodFilter->startDate, $periodFilter->endDate]);
            })
            ->when($filters->search, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('subscribers.email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('subscribers.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('subscribers.document_number', 'like', '%' . $searchTerm . '%')
                        ->orWhere('payments.charge_code', 'like', '%' . $searchTerm . '%')
                        ->orWhere('payments.order_number', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('payments.created_at', 'DESC');
    }

    public function reportSubscriberSale(string $platformId, SaleReportFilter $filters)
    {
        return $this->model
            ->select(
                DB::raw(
                    '
                plans.id AS plans_id,
                plans.name AS plans_name,
                products.id AS product_id,
                products.name AS product_name,
                subscribers.name AS subscribers_name,
                subscribers.email AS subscribers_email,
                recurrences.recurrence,
                CONCAT(recurrences.current_charge, "/", IF(plans.charge_until = 0, "∞", plans.charge_until)) AS actual_installment,
                payments.installments AS totalInstallments,
                subscriptions.status as subscription_status,
                subscriptions.created_at as subscription_date,
                subscriptions.canceled_at as cancellation_date,
                GROUP_CONCAT(payments.status SEPARATOR ", ") AS payments_status,
                payments.created_at,
                payments.payment_date,
                payments.type_payment,
                MIN(coupons.id) AS coupon_id,
                MIN(coupons.code) AS coupon_code,
                MIN(coupons.value) AS coupon_value,
                MIN(coupons.value_type) AS coupon_type,
                payments.price,
                payments.plans_value,
                payments.tax_value,
                payments.customer_value AS customer_value,
                subscribers.document_type,
                subscribers.document_number,
                subscribers.cel_phone,
                subscribers.address_zipcode,
                subscribers.address_street,
                subscribers.address_number,
                subscribers.address_comp,
                subscribers.address_district,
                subscribers.address_city,
                subscribers.address_state,
                subscribers.address_country,
                GROUP_CONCAT(payments.order_code SEPARATOR ", ") AS transactions_id,
                plans.freedays_type,
	            plans.freedays,
                subscribers.id AS subscribers_id,
                subscribers.status AS subscribers_status,
                recurrences.last_payment,
                payments.service_value,
                payments.id AS payment_id,
                payments.price AS payment_plan_plan_price,
                payments.order_number AS payment_order_number'
                )
            )
            ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
            ->leftJoin('payment_recurrence', 'payment_recurrence.payment_id', '=', 'payments.id')
            ->leftJoin('recurrences', 'recurrences.id', 'payment_recurrence.recurrence_id')
            ->leftJoin('plans', 'plans.id', '=', 'recurrences.plan_id')
            ->leftJoin('products', 'products.id', '=', 'plans.product_id')
            ->leftJoin('coupons', 'coupons.id', '=', 'payments.coupon_id')
            ->leftJoin('subscriptions', ['subscriptions.plan_id' => 'plans.id', 'subscriptions.subscriber_id' => 'subscribers.id'])
            ->join(DB::raw("(SELECT max(payments.id) as id
	            FROM payments
                WHERE payments.status IN ('paid', 'canceled')
                AND payments.platform_id = '{$platformId}'
                AND payments.type = 'R'
                GROUP BY IFNULL(payments.order_number, payments.customer_id)
            ) as recent_payments"), function ($join) {
                $join->on('recent_payments.id', '=', 'payments.id');
            })
            ->whereRaw('(`subscriptions`.`order_number` = `payments`.`order_number` OR ( `subscriptions`.`order_number` IS NULL ))')
            ->where('payments.type', 'R')
            ->where('payments.platform_id', '=', $platformId)
            ->when($filters->products, function ($query, $productsId) {
                $query->whereIn('products.id', $productsId);
            })
            ->when($filters->paymentMethod, function ($query, $typePaymentFilter) {
                $query->whereIn('payments.type_payment', $typePaymentFilter);
            })
            ->when($filters->paymentStatus, function ($query, $statusPaymentFilter) {
                $query->whereIn('payments.status', $statusPaymentFilter);
            })
            ->when($filters->paymentPeriod, function ($query, $periodFilter) {
                $query->whereBetween('payments.payment_date', [$periodFilter->startDate, $periodFilter->endDate]);
            })
            ->when($filters->accessionPeriod, function ($query, $periodFilter) {
                $query->whereBetween('subscriptions.created_at', [$periodFilter->startDate, $periodFilter->endDate]);
            })
            ->when($filters->cancelPeriod, function ($query, PeriodFilter $cancelPeriod) {
                $query->whereBetween('subscriptions.canceled_at', [$cancelPeriod->startDate, $cancelPeriod->endDate]);
            })
            ->when($filters->subscriptionStatus, function ($query, $subscriptionStatus) {
                $query->whereIn('subscriptions.status', $subscriptionStatus);
            })
            ->when($filters->lastPaymentPeriod, function ($query, PeriodFilter $lastPayment) {
                $query->whereBetween('recurrences.last_payment', [$lastPayment->startDate, $lastPayment->endDate]);
            })
            ->when($filters->search, function ($query, $searchTerm) {
                $query->where(function ($query) use ($searchTerm) {
                    $query->where('subscribers.email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('subscribers.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('charge_code', 'like', '%' . $searchTerm . '%')
                        ->orWhere('document_number', 'like', '%' . $searchTerm . '%')
                        ->orWhere('payments.order_number', 'like', '%' . $searchTerm . '%');
                });
            })
            ->groupBy(['plans.id', 'subscribers.id', 'payments.order_number'])
            ->orderBy('subscriptions.created_at', 'DESC');
    }

    public function reportNoLimitSale(string $platformId, SaleReportFilter $filters)
    {
        $platform = Platform::with(['client:id,percent_split'])->findOrFail($platformId);

        $percentSplit = $platform->client->percent_split ?? 100;
        $clientTax = (100 - $percentSplit) / 100;

        return  $this->model
            ->select(
                DB::raw(
                    "plans.id AS plan_id,
                    plans.name AS plan_name,
                    plans.freedays_type AS plan_freedays_type,
                    plans.freedays AS plan_freedays,
                    plans.price AS plan_price,
                    plans.price AS plan_original_price,
                    plans.order_bump_plan_id AS plan_order_bump_id,
                    products.id AS product_id,
                    products.name AS product_name,
                    subscriptions.status as subscription_status,
                    subscriptions.canceled_at as cancellation_date,
                    subscribers.id AS subscriber_id,
                    subscribers.name AS subscriber_name,
                    subscribers.email AS subscriber_email,
                    subscribers.document_type AS subscriber_document_type,
                    subscribers.document_number AS subscriber_document_number,
                    subscribers.cel_phone AS subscriber_cellphone,
                    subscribers.address_street AS subscriber_street,
                    subscribers.address_number AS subscriber_number,
                    subscribers.address_comp AS subscriber_comp,
                    subscribers.address_district AS subscriber_district,
                    subscribers.address_zipcode AS subscriber_zipcode,
                    subscribers.address_city AS subscriber_city,
                    subscribers.address_state AS subscriber_state,
                    subscribers.address_country AS subscriber_country,
                    (
                        SELECT GROUP_CONCAT(DISTINCT p.order_bump_plan_id)
                        FROM payment_plan pp
                        JOIN plans p ON p.id = pp.plan_id
                        WHERE pp.payment_id = payments.id
                        GROUP BY pp.payment_id
                        ORDER BY pp.created_at DESC
                    ) AS payment_order_bump,
                    payments.installments AS payment_installments,
                    payments.installment_number AS payment_installment_number,
                    payments.service_value AS payment_service_value,
                    payments.price AS payment_price,
                    payments.plans_value AS payment_plan_value,
                    payments.tax_value AS payment_tax_value,
                    {$clientTax} as client_tax,
                    payments.customer_value AS payment_customer_value,
                    payments.payment_date AS payment_date,
                    payments.created_at AS payment_created_at,
                    payments.updated_at AS payment_updated_at,
                    payments.status AS payment_status,
                    payments.order_code AS payment_transaction_id,
                    payments.type_payment AS payment_type,
                    payments.charge_code AS payment_charge_code,
                    payments.id AS payment_id,
                    payments.order_number AS payment_order_number,
                    coupons.id AS coupon_id,
                    coupons.code AS coupon_code,
                    coupons.value AS coupon_value,
                    coupons.value AS coupon_original_value,
                    coupons.value_type AS coupon_type,
                    payment_plan.tax_value AS payment_plan_tax_value,
                    payment_plan.plan_value AS payment_plan_plan_value,
                    payment_plan.plan_price AS payment_plan_plan_price,
                    payment_plan.coupon_id AS payment_plan_coupon_id,
                    payment_plan.coupon_code AS payment_plan_coupon_code,
                    payment_plan.coupon_value AS payment_plan_coupon_value,
                    payment_plan.type AS payment_plan_type,
                    payment_plan.customer_value AS payment_plan_customer_value"
                )
            )
            ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->leftJoin('products', 'products.id', '=', 'plans.product_id')
            ->leftJoin('coupons', 'coupons.id', '=', 'payments.coupon_id')
            ->leftJoin('subscriptions', ['subscriptions.plan_id' => 'plans.id', 'subscriptions.subscriber_id' => 'subscribers.id'])
            ->join(DB::raw('(SELECT max(payments.id) as id
	            FROM payments
                WHERE payments.status IN ("paid")
                  AND installment_number > 0
                GROUP BY IFNULL(payments.order_number, payments.customer_id)
            ) as recent_payments'), function ($join) {
                $join->on('recent_payments.id', '=', 'payments.id');
            })
            ->where('payments.type', Payment::TYPE_UNLIMITED)
            ->where('payments.platform_id', '=', $platformId)
            ->when($filters->products, function ($query, $productsId) {
                $query->whereIn('products.id', $productsId);
            })
            ->when($filters->paymentMethod, function ($query, $typePaymentFilter) {
                $query->whereIn('payments.type_payment', $typePaymentFilter);
            })
            ->when($filters->subscriptionStatus, function ($query, $subscriptionStatus) {
                $query->WhereIn('subscriptions.status', $subscriptionStatus);
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
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('subscribers.email', 'like', '%' . $searchTerm . '%')
                        ->orWhere('subscribers.name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('subscribers.document_number', 'like', '%' . $searchTerm . '%')
                        ->orWhere('payments.charge_code', 'like', '%' . $searchTerm . '%')
                        ->orWhere('payments.order_number', 'like', '%' . $searchTerm . '%');
                });
            })
            ->orderBy('payments.id', 'DESC');
    }

    /**
     * Número de transações
     */
    public function totalTransactions(
        PeriodFilter $filter,
        ?array $paymentsStatus = null,
        string $platformId = null
    ) {
        return $this->model
            ->onPaymentDatePeriod($filter)
            ->when($paymentsStatus, function ($query, $status) {
                $query->whereIn('payments.status', $status);
            })
            ->platform($platformId)
            ->count() ?? 0;
    }

    public function totalTransactionsByStatus(
        PeriodFilter $filter,
        string $platformId = null
    ) {
        return $this->model
            ->onTransactionsPeriod($filter);
    }

    public function averageTicketPrice(
        PeriodFilter $filter,
        string $platformId = null
    ) {
        return $this->model
            ->select(DB::raw('ROUND(SUM(customer_value)/COUNT(*),2) AS total'))
            ->where('payments.status', 'paid')
            ->onPeriod($filter)
            ->platform($platformId)
            ->first()
            ->total ?? 0;
    }

    public function sumTransactions(
        PeriodFilter $filter = null,
        string $status = 'paid',
        string $platformId = null
    ) {
        $values = $this->model
            ->onSalesByStatus($filter);

        return $values->total_volume;
    }

    public function sumTransactionsByType(
        PeriodFilter $filter = null,
        string $type,
        array $status = ['*'],
        string $platformId = null
    ) {
        return $this->model
            ->where('payments.type', $type)
            ->whereIn('payments.status', $status)
            ->onPeriod($filter)
            ->platform($platformId)
            ->sum('payments.customer_value');
    }

    private function getAvailableAmount(): float
    {
        try {
            /** @var BalanceService $balanceService */
            $balanceService = resolve(BalanceService::class);
            $data = $balanceService->getUserClientBalance(Auth::user()->platform_id, Auth::user()->id);
            return ($data->available ?? 0) / 100;
        } catch (ClientException $e) {
            return 0;
        }
    }

    public function totalBilling(
        PeriodFilter $filter,
        string $platformId = null
    ) {
        $values = $this->model
            ->onSalesByStatus($filter);

        return $values->paid;
    }

    public function salesForecast(
        PeriodFilter $filter,
        string $platformId = null
    ) {
        $totalBilling = $this->totalBilling($filter, $platformId);

        $totalPendingPixOrBankSlip = $this->model
            ->whereIn('payments.type_payment', [Payment::TYPE_PAYMENT_BILLET, Payment::TYPE_PAYMENT_PIX])
            ->where('payments.status', Payment::STATUS_PENDING)
            ->onPeriod($filter)
            ->platform($platformId)
            ->sum('payments.customer_value') ?? 0;

        $totalPendingNoLimit = $this->model
            ->where('payments.type', Payment::TYPE_UNLIMITED)
            ->whereIn('payments.status', [Payment::STATUS_PENDING, Payment::STATUS_FAILED])
            ->onPeriod($filter)
            ->platform($platformId)
            ->sum('payments.customer_value') ?? 0;

        return $totalBilling + $totalPendingPixOrBankSlip + $totalPendingNoLimit;
    }

    public function percentTypePayment(
        PeriodFilter $filter,
        ?array $paymentsStatus = null,
        string $platformId = null
    ) {
        $totalPayments = $this->totalTransactions($filter, $paymentsStatus, $platformId);

        return $this->model
            ->select(DB::raw("
                type_payment,
                ROUND(COUNT(payments.id)*100/{$totalPayments}, 2) AS percent
            "))
            ->onPaymentDatePeriod($filter)
            ->when($paymentsStatus, function ($query, $status) {
                $query->whereIn('payments.status', $status);
            })
            ->platform($platformId)
            ->groupBy('payments.type_payment')
            ->get();
    }

    public function totalCardMultiples(
        PeriodFilter $filter,
        string $platformId = null
    ) {
        return $this->model
            ->select(DB::raw("
                IF(payments.multiple_means != 0, 'multiple', 'single') AS card,
                COUNT(payments.id) AS count
            "))
            ->onPaymentDatePeriod($filter)
            ->where('payments.type_payment', Payment::TYPE_PAYMENT_CREDIT_CARD)
            ->where('payments.status', '=', Payment::STATUS_PAID)
            ->platform($platformId)
            ->groupBy('payments.multiple_means')
            ->get();
    }

    public function totalGeneratedVsPaid(
        PeriodFilter $filter,
        string $typePayment,
        string $platformId = null
    ) {
        $total = $this->model
            ->where('payments.type_payment', $typePayment)
            ->where('payments.status', '<>', Payment::STATUS_CANCELED)
            ->onPaymentDatePeriod($filter)
            ->platform($platformId)
            ->count() ?? 0;

        $paid = $this->model
            ->where('payments.type_payment', $typePayment)
            ->where('payments.status', Payment::STATUS_PAID)
            ->onPaymentDatePeriod($filter)
            ->platform($platformId)
            ->count() ?? 0;

        return ['generated' => $total, 'paid' => $paid];
    }

    public function transactionSumByStatus(
        PeriodFilter $filter,
        string $platformId = null
    ) {
        $toReceiveSum = $this->model
            ->select(DB::raw("
                'receive',
                SUM(payment_plan.customer_value) AS total
            "))
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->where('payments.status', 'pending')
            ->where('payments.type', 'U')
            ->onPeriod($filter)
            ->platform($platformId)
            ->groupBy('payments.status');

        return $this->model
            ->select(DB::raw("
                payments.status,
                SUM(payment_plan.customer_value) AS total
            "))
            ->whereRaw(DB::raw('
                payments.status in (
                    "paid",
                    "canceled",
                    "chargeback",
                    CASE WHEN (payments.status = "pending" AND payments.type <> "U") THEN "pending" END
                )
            '))
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->onPeriod($filter)
            ->platform($platformId)
            ->groupBy('payments.status')
            ->union($toReceiveSum)
            ->get();
    }

    public function transactionCountByStatus(
        PeriodFilter $filter,
        string $platformId = null
    ) {
        $countToReceive = $this->model
            ->select(DB::raw("
                'receive',
                COUNT(payments.id)
            "))
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->where('payments.status', 'pending')
            ->where('payments.type', 'U')
            ->onPeriod($filter)
            ->platform($platformId);

        return $this->model
            ->select(DB::raw("
                payments.status,
                COUNT(payments.id) AS count
            "))
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->whereRaw(DB::raw('
                payments.status in (
                    "paid",
                    "canceled",
                    "chargeback",
                    CASE WHEN (payments.status = "pending" AND payments.type <> "U") THEN "pending" END
                )
            '))
            ->onPeriod($filter)
            ->platform($platformId)
            ->groupBy('payments.status')
            ->union($countToReceive)
            ->get();
    }

    public function graphTransactionByStatus(
        PeriodFilter $filter,
        string $platformId = null
    ) {
        $values = $this->model
            ->onSalesByStatus($filter);
        return [
            [
                "value" => $values->receivable,
                "name" => "A Receber"
            ],
            [
                "value" => $values->paid,
                "name" => "Paga"
            ],
            [
                "value" => $values->pending,
                "name" => "Pendente"
            ],
            [
                "value" => $values->refunded,
                "name" => "Estornada"
            ],
            [
                "value" =>  $values->chargeback,
                "name" => "Chargeback"
            ],
        ];
    }

    // public function graphCreditCardStatusTransactions(
    //     PeriodFilter $filter,
    //     string $platformId = null
    // ) {
    //     $approved = $this->model
    //         ->select(DB::raw('
    //             COUNT(payments.id) AS value,
    //             "0000" AS name
    //         '))
    //         ->where('payments.status', Payment::STATUS_PAID)
    //         ->where('payments.type_payment', Payment::TYPE_PAYMENT_CREDIT_CARD)
    //         ->onPeriod($filter)
    //         ->platform($platformId);

    //     $failed = $this->model
    //         ->select(DB::raw('
    //             COUNT(payments.id) AS value,
    //             "9999" AS name
    //         '))
    //         ->where('payments.status', Payment::STATUS_FAILED)
    //         ->where('payments.type_payment', Payment::TYPE_PAYMENT_CREDIT_CARD)
    //         ->onPeriod($filter)
    //         ->platform($platformId);

    //     return Transaction::select(DB::raw("
    //             COUNT(transactions.id) AS value,
    //             transactions.transaction_code AS name
    //         "))
    //         ->onPeriod($filter)
    //         ->platform($platformId)
    //         ->groupBy('transactions.transaction_code')
    //         ->union($approved)
    //         ->union($failed)
    //         ->get();
    // }

    public function graphCreditCardStatusTransactions(
        PeriodFilter $filter,
        string $platformId = null
    ) {

        return Transaction::select(DB::raw("
                COUNT(transactions.id) AS value,
                transactions.transaction_message AS name
            "))
            ->onPeriod($filter)
            ->platform($platformId)
            ->groupBy('transactions.transaction_message')
            ->get();
    }

    public function graphTransactionsByInstallments(
        PeriodFilter $filter,
        string $platformId = null
    ) {
        return $this->model
            ->select(DB::raw('
                payments.installments,
                COUNT(payments.id) AS count,
                SUM(payments.customer_value) AS total
            '))
            ->where('payments.status', 'paid')
            ->where('payments.type_payment', 'credit_card')
            ->where(function ($query) {
                $query->where('payments.type', 'P')
                    ->orWhere('payments.type', 'U');
            })
            ->onPeriod($filter)
            ->platform($platformId)
            ->groupBy('payments.installments')
            ->orderBy('payments.installments')
            ->get();
    }

    public function transactionsDate(
        PeriodFilter $filter,
        string $status = null,
        string $platformId = null
    ) {
        $query = $this->model
            ->select(DB::raw('
                DATE_FORMAT(payments.payment_date,"%d/%m/%Y") AS date,
                SUM(payments.customer_value) AS total
            '));

        if (!is_null($status)) {
            $query->where('payments.status', '=', $status);
        }

        return  $query->whereBetween('payments.payment_date', [$filter->startDate, $filter->endDate])
            ->platform($platformId)
            ->groupBy('payments.payment_date')
            ->get();
    }

    public function totalAntecipationFees(
        string $status = 'paid',
        string $platformId = null
    ) {
        return $this->model
            ->where('payments.payment_date', '>=', Carbon::now()->addDays(-30))
            ->where('payments.type', '!=', 'U')
            ->status($status)
            ->platform($platformId)
            ->sum('payments.antecipation_value');
    }

    public function getBySubscriberAndPlansOnPeriod(
        string $subscriberId,
        array $plans,
        array $where = [],
        string $platformId = null,
        PeriodFilter $period = null,
        array $columns = ['*']
    ) {
        $columns = implode(',', $columns);
        $query = $this->model
            ->select(DB::raw("
                {$columns},
                GROUP_CONCAT(payment_plan.plan_id) AS plans
            "))
            ->join('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->platform($platformId)
            ->where('payments.subscriber_id', $subscriberId);

        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        $plans = implode(',', $plans);
        return $query->onPeriod($period)
            ->groupBy('payments.id')
            ->havingRaw("plans='{$plans}'")
            ->get();
    }

    public function getTotalByAndTypesAndStatus(
        array $types,
        string $status,
        array $where = [],
        string $column = 'payments.customer_value',
        string $platformId = null,
        SaleReportFilter $filters = null
    ) {
        $typeList = [Payment::TYPE_SALE, Payment::TYPE_UNLIMITED, Payment::TYPE_SUBSCRIPTION];
        $statusList = array_keys(Payment::listStatus());

        $filteredTypes = array_intersect($types, $typeList);

        if ($filteredTypes != $types) {
            return 0;
        }

        if (count($filteredTypes) == 0) {
            return 0;
        }

        if (!in_array($status, $statusList)) {
            return 0;
        }

        $subquery = $this->model
            ->select(DB::raw("{$column} AS value"))
            ->whereIn('payments.type', $types)
            ->where('payments.status', $status)
            ->platform($platformId);

        $this->setWhere($subquery, $where);

        if ($filters instanceof SaleReportFilter) {
            $subquery
                ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
                ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
                ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
                ->leftJoin('subscriptions', ['subscriptions.plan_id' => 'plans.id', 'subscriptions.subscriber_id' => 'subscribers.id'])
                ->whereRaw('(`subscriptions`.`order_number` = `payments`.`order_number` OR ( `subscriptions`.`order_number` IS NULL ))')
                ->when($filters->products, function ($query, $productsId) {
                    $query->join('products', 'plans.product_id', '=', 'products.id')
                        ->whereIn('products.id', $productsId);
                })
                ->when($filters->paymentMethod, function ($query, $typePaymentFilter) {
                    $query->whereIn('payments.type_payment', $typePaymentFilter);
                })
                ->when($filters->paymentStatus, function ($query, $statusPaymentFilter) {
                    $query->WhereIn('payments.status', $statusPaymentFilter);
                })
                ->when($filters->paymentType, function ($query, $paymentTypeFilter) {
                    $query->whereIn('payments.type', $paymentTypeFilter);
                })
                ->when($filters->subscriptionStatus, function ($query, $subscriptionStatus) {
                    $query->WhereIn('subscriptions.status', $subscriptionStatus);
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
                ->when($filters->onlyPaymentWithMultipleMeans, function ($query, $multipleMeans) {
                    $query->where('payments.multiple_means', $multipleMeans);
                })
                ->when($filters->onlyPaymentWithCoupon, function ($query, $hasCoupon) {
                    $query->where('payments.coupon_id', '>', 0);
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

        $subquerySql = $subquery->toSql();
        return DB::table(DB::raw("($subquerySql) as subquery"))
            ->mergeBindings($subquery->getQuery())
            ->sum('subquery.value');
    }

    public function getSubscriberByOrderNumber(Request $request): array
    {
        try {

            if (!$request->filled('orderNumber')) {
                return [
                    'error' => true,
                    'message' => 'É necessário informar o order number',
                    'data' => []
                ];
            }

            $subscriber = $this->model
                ->select('subscribers.name', 'subscribers.email')
                ->join('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
                ->where('payments.order_number', $request->input('orderNumber'))->first();

            if ($subscriber) {
                return [
                    'error' => false,
                    'message' => 'Sucesso',
                    'data' => $subscriber
                ];
            } else {
                return [
                    'error' => true,
                    'message' => 'Comprador não encontrado',
                    'data' => []
                ];
            }
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => 'Erro inesperado.',
                'data' => [$e->getMessage()]
            ];
        }
    }
}
