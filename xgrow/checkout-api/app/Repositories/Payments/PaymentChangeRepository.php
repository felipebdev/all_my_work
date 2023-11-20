<?php

namespace App\Repositories\Payments;

use App\Payment;
use App\PaymentMethodChange;
use Carbon\Carbon;

class PaymentChangeRepository
{

    /**
     * @param  \App\Payment  $original
     * @param  array  $newSettings
     * @return \App\PaymentMethodChange
     */
    public function saveHistory(Payment $original, array $newSettings): PaymentMethodChange
    {

        $modified = $original->replicate()->fill($newSettings);

        return PaymentMethodChange::create([
            'payment_id' => $original->id,
            'origin' => PaymentMethodChange::ORIGIN_SUBSCRIBER,
            'type_payment_old' => $original->type_payment,
            'type_payment_new' => $modified->type_payment,
            'installments_old' => $original->installments,
            'installments_new' => $modified->installments,
            'order_code_old' => $original->order_code,
            'order_code_new' => $modified->order_code,
            'charge_id_old' => $original->charge_id,
            'charge_id_new' => $modified->charge_id,
            'charge_code_old' => $original->charge_code,
            'charge_code_new' => $modified->charge_code,
            'boleto_line_old' => $original->boleto_line ?? null,
            'boleto_line_new' => $modified->boleto_line ?? null,
            'pix_qrcode_url_old' => $original->pix_qrcode_url ?? null,
            'pix_qrcode_url_new' => $modified->pix_qrcode_url ?? null,
        ]);
    }

    public function listChangesInGivenInterval(array $subscriptionIds, int $intervalInSeconds)
    {
        return PaymentMethodChange::query()
            ->join('payments', 'payments.id', '=', 'payment_method_change.payment_id')
            ->whereIn('payments.subscriber_id', $subscriptionIds)
            ->where('payment_method_change.created_at', '>', Carbon::now()->subSeconds($intervalInSeconds))
            ->latest('payment_method_change.created_at')
            ->get();
    }
}
