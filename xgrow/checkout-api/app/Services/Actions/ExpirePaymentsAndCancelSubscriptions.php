<?php

namespace App\Services\Actions;

use App\Payment;
use App\Subscription;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;

class ExpirePaymentsAndCancelSubscriptions
{

    public function __invoke()
    {
        $this->expirePixAndSubscriptions(Carbon::now());
        $this->expireBoletosAndSubscriptions(Carbon::now());
    }

    private function expireBoletosAndSubscriptions(DateTimeInterface $dateTime): int
    {
        $payments = $this->getExpiredBoletos($dateTime);

        $payments->each(function (Payment $payment) {
            $payment->update([
                'status' => Payment::STATUS_EXPIRED,
            ]);

            $payment->paymentPlans()->update([
                    'status' => Payment::STATUS_EXPIRED,
            ]);

            $payment->subscription->update([
                'canceled_at' => Carbon::now(),
                'status' => Subscription::STATUS_CANCELED,
                'status_updated_at' => Carbon::now(),
                'cancellation_reason' => 'Boleto expirado',
            ]);

        });

        $paymentsAffected = $payments->count();

        return $paymentsAffected;
    }

    private function getExpiredBoletos(DateTimeInterface $dateTime): Collection
    {
        return Payment::where('type_payment', Payment::TYPE_PAYMENT_BILLET)
            ->where('status', Payment::STATUS_PENDING)
            ->where('expires_at', '<=', $dateTime)
            ->with('subscription:id,order_number')
            ->get(['id', 'order_number']);
    }

    private function expirePixAndSubscriptions(DateTimeInterface $dateTime): int
    {
        $payments = $this->getExpiredPix($dateTime);

        $payments->each(function (Payment $payment) {
            $payment->update([
                'status' => Payment::STATUS_EXPIRED,
            ]);

            $payment->paymentPlans()->update(
                [
                    'status' => Payment::STATUS_EXPIRED
                ]
            );

            $payment->subscription->update([
                'canceled_at' => Carbon::now(),
                'status' => Subscription::STATUS_CANCELED,
                'status_updated_at' => Carbon::now(),
                'cancellation_reason' => 'PIX expirado',
            ]);

        });

        $paymentsAffected = $payments->count();

        return $paymentsAffected;
    }

    private function getExpiredPix(DateTimeInterface $dateTime)
    {
        return Payment::where('type_payment', Payment::TYPE_PAYMENT_PIX)
          ->where('status', Payment::STATUS_PENDING)
            ->where('expires_at', '<=', $dateTime)
            ->with('subscription:id,order_number')
            ->get(['id', 'order_number']);
    }

}
