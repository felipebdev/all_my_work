<?php

namespace App\Console\Commands;

use App\Payment;
use App\PaymentPlan;
use App\Services\Finances\Refund\Drivers\PagarmeRefund;
use Carbon\Carbon;
use Illuminate\Console\Command;

class VerifyPendingRefundBoletoCommand extends Command
{
    protected $signature = 'xgrow:verify-pending-refund-boleto ';

    protected $description = 'Check payment status on Pagar.me for boletos with pending_refund';

    public function handle(PagarmeRefund $pagarmeRefund)
    {
        $paymentPlans = PaymentPlan::query()
            ->where('updated_at', '>', Carbon::now()->subDays(5))
            ->where('status', PaymentPlan::STATUS_PENDING_REFUND)
            ->get();

        foreach ($paymentPlans as $paymenPlan) {
            $payment = $paymenPlan->payment;

            $chargeId = $payment->charge_id;

            $transactionStatus = $pagarmeRefund->checkTransactionStatus($chargeId);

            if ($transactionStatus == 'paid') {
                $paymenPlan->status = Payment::STATUS_PAID;
                $paymenPlan->refund_failed_at = Carbon::now();
                $paymenPlan->save();
            }
        }

        return self::SUCCESS;
    }
}
