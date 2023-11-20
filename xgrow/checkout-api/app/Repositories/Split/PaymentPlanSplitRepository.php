<?php


namespace App\Repositories\Split;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaymentPlanSplitRepository
{
    /**
     * @param  array  $paymentIds
     * @return \Illuminate\Support\Collection
     */
    public static function getPaymentPlansByPaymentIds(array $paymentIds): Collection
    {
        $paymentPlans = DB::table('payment_plan')
            ->whereIn('payment_id', $paymentIds)
            ->orderBy('type', 'DESC') // [p]roduct before [o]rder_bump
            ->orderBy('plan_id', 'ASC')
            ->get();

        return $paymentPlans;
    }
}
