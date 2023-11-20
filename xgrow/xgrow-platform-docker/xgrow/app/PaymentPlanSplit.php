<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class PaymentPlanSplit extends Model
{
    /**
     * @var string
     */
    protected $table = 'payment_plan_split';

    /**
     * @var string[]
     */
    protected $fillable = [
        'client_id',
        'platform_id',
        'product_id',
        'order_code',
        'plan_id',
        'payment_plan_id',
        'percent',
        'value',
        'anticipation_value',
        'type',
        'producer_product_id',
    ];


    /**
     * @param string $platfomId
     * @param $betweenDates
     * @return float|int
     */
    public static function getTotalCommission(string $platfomId, $betweenDates)
    {
        return array_sum(
            self::select('payment_plan_split.value as value')
                ->join('payment_plan', 'payment_plan_split.plan_id', '=', 'payment_plan.id')
                ->join('payments', 'payment_plan.payment_id', '=', 'payments.id')
                ->where('payment_plan_split.platform_id', $platfomId)
                ->where('payment_plan_split.type', 'C')
                ->where('payments.status', 'paid')
                ->when($betweenDates, function ($query, $betweenDates) {
                    return $query->whereBetween('payments.payment_date', [
                        Carbon::parse($betweenDates->startDate)->format('Y-m-d'),
                        Carbon::parse($betweenDates->endDate)->format('Y-m-d')
                    ]);
                })
                ->get()
                ->pluck('value')
                ->toArray()
        );
    }

    public static function getCommisions($paymentId)
    {
        return [
            'producer' => self::getCommisionsByType($paymentId, 'C')[0],
            'co_producers' => self::getCommisionsByType($paymentId, 'P')
        ];
    }

    public static function getCommisionsByType($paymentId, $type)
    {
        $payments = Payment::select(
            DB::raw('sum(payment_plan.customer_value) as transaction_value, platforms_users.name'),
            DB::raw('sum(payment_plan_split.value) as commission, platforms_users.name'),
            'platforms_users.name'
        )
            ->join('payment_plan', 'payment_plan.payment_id', 'payments.id')
            ->join('payment_plan_split', 'payment_plan_split.payment_plan_id', 'payment_plan.id')
            ->leftJoin('producer_products', 'payment_plan_split.producer_product_id', 'producer_products.id')
            ->leftJoin('products', 'producer_products.product_id', 'products.id')
            ->leftJoin('producers', 'producer_products.producer_id', 'producers.id')
            ->leftJoin('platforms_users', 'producers.platform_user_id', 'platforms_users.id')
            ->where('payments.id', $paymentId)
            ->where('payment_plan_split.type', $type)
            ->get()
            ->toArray();

        foreach ($payments as $key => $payment) {
            if ($payment['commission'] === null) {
                unset($payments[$key]);
            }
        }

        return $payments;
    }
}
