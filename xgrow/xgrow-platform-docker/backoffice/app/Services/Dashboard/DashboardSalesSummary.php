<?php

namespace App\Services\Dashboard;

use App\Payment;

class DashboardSalesSummary
{

    /**
     * @var Payment
     */
    private Payment $payment;

    /**
     * @param Payment $payment
     */
    public function __construct(
        Payment $payment
    )
    {
        $this->payment = $payment;
    }

    /**
     * Get Sales Summary
     * @param null|string dateStart
     * @param null|string dateEnd
     * @return array
     */
    public function getInfo(?string $dateStart, ?string $dateEnd): array
    {

        $paymentStatus = ['paid', 'canceled', [ 'chargeback' => ['refunded', 'chargeback']], 'pending'];

        $salesSummary = [];

        foreach($paymentStatus as $status){
            $key = $type = $status;
            if(is_array($status)){
                $key = array_key_first($status);
                $type = is_array($status[$key]) ? implode($status[$key], "','") : $status[$key];
            }
            $payment = $this->payment
                                    ->selectRaw('sum(customer_value) total')
                                    ->whereRaw("status IN ('$type')")
                                    ->when($dateStart, function($query) use($dateStart){
                                            $query->where('payment_date', '>=', $dateStart);
                                    })
                                    ->when($dateEnd, function($query) use($dateEnd){
                                        $query->where('payment_date', '<=', $dateEnd);
                                    })->first();

            $total =  number_format((float) $payment['total'], 2, '.', '');
            $salesSummary[$key] = $total;
        }

        return $salesSummary;

    }

}
