<?php

namespace App\Repositories\Dashboard;

use App\Repositories\BaseRepository;
use App\Repositories\Contracts\PeriodRangeInterface;
use App\Payment;
use DateTime;
use DatePeriod;
use DateInterval;
use Illuminate\Support\Facades\DB;

class DailyPeriod extends BaseRepository implements PeriodRangeInterface
{

    public function model()
    {
        return Payment::class;
    }

    public function getLabel($start, $end): array
    {

        $start    = new DateTime($start);
        $end      = new DateTime($end);
        $interval = DateInterval::createFromDateString('1 day');
        $period   = new DatePeriod($start, $interval, $end);

        $days = array();

        foreach ($period as $dt) {
            array_push($days, $dt->format("d-M"));
        }
        return $days;
    }


    public function getSalesProduct($start, $end, $product_id, $platform_id): array
    {
        $start    = new DateTime($start);
        $end      = new DateTime($end);
        $interval = DateInterval::createFromDateString('1 day');
        $period   = new DatePeriod($start, $interval, $end);

        $data = array();

        foreach ($period as $dt) {
            $date = $dt->format("Y-m-d");
            $subQuery = $this->model->select(
                'payments.id AS payments_id',
                'payment_plan.id AS payment_plan_id',
                DB::raw(
                    "CASE payments.type
                    WHEN 'U' THEN CONCAT(payments.installment_number, '/', payments.installments)
                    WHEN 'R' THEN CONCAT(payments.installment_number, '/', IF(plans.charge_until = 0, '∞', plans.charge_until))
                    WHEN 'P' THEN CONCAT(payments.installments, 'x')
                END AS actual_installment"
                ),
                'subscriptions.canceled_at as cancellation_date',
                DB::raw(
                    "
                    CASE
                        WHEN payment_plan.customer_value IS null THEN
                            IF(payment_plan.type = 'order_bump', 0, payments.customer_value)
                    ELSE
                        payment_plan.customer_value - (
                            SUM(
                                CASE WHEN payment_plan_split.type IN ('P', 'A')
                                    THEN payment_plan_split.value
                                ELSE 0 END
                            )
                        )
                    END
                        AS commission
                "
                )
            )
                ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
                ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
                ->leftJoin('payment_plan_split', 'payment_plan_split.payment_plan_id', '=', 'payment_plan.id')
                ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
                ->leftJoin('products', 'plans.product_id', '=', 'products.id')
                ->leftJoin('coupons', 'coupons.id', '=', 'payments.coupon_id')
                ->leftJoin('payment_recurrence', 'payments.id', '=', 'payment_recurrence.payment_id')
                ->leftJoin('recurrences', 'payment_recurrence.recurrence_id', '=', 'recurrences.id')
                ->leftJoin('subscriptions', ['subscriptions.plan_id' => 'plans.id', 'subscriptions.subscriber_id' => 'subscribers.id'])
                ->whereRaw('(`subscriptions`.`order_number` = `payments`.`order_number` OR ( `subscriptions`.`order_number` IS NULL ))')
                ->where('payment_plan.status', '=', 'paid')
                ->when($product_id != 0, function ($q) use ($product_id) {
                    return $q->where('products.id', $product_id);
                })
                ->whereDate('payments.payment_date', $date)
                ->where('payments.platform_id', $platform_id)
                ->groupBy(
                    'payments_id',
                    'payment_plan.id',
                    'actual_installment',
                    'cancellation_date'
                );

            $subquerySql = $subQuery->toSql();

            $total =  DB::table(DB::raw("($subquerySql) as subquery"))
                ->mergeBindings($subQuery->getQuery())
                ->sum("subquery.commission");

            array_push($data, $total);
        }
        return $data;
    }
}
