<?php

namespace App\Services\Charges;

use App\Jobs\MundipaggUnlimitedOrderRetry;
use App\Logs\ChargeLog;
use App\Payment;
use App\Services\ChargeRulerSettings;
use App\Subscriber;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NoLimitRetryChargeService
{

    private bool $dryRunDebug = false;

    private ?string $baseDateDebug = null;

    private bool $skipEmail = false;

    public function enableDryRunDebug(bool $mode = true): self
    {
        $this->dryRunDebug = $mode;
        return $this;
    }

    public function setBaseDateDebug(?string $date = null): self
    {
        $this->baseDateDebug = $date;
        return $this;
    }

    public function skipEmail(bool $skipEmail = true): self
    {
        $this->skipEmail = $skipEmail;
        return $this;
    }

    public function retryChargePayment(Payment $payment, int $mailId): bool
    {
        ChargeLog::withContext(['charge-trace-id' => (string) Str::uuid()]);

        if (!$this->shouldRetryChargePayment($payment)) {
            return false;
        }

        if ($this->dryRunDebug) {
            $this->writeCsvDebugFile($payment);
            return true; // fake retry
        }

        $payment->increment('attempts');

        ChargeLog::info('No-Limit retry dispatched', ['payment_id' => $payment->id ?? null]);

        // skip non-canceling email
        $shouldSkipMail = ChargeRulerSettings::isCancelRequired($mailId)
            ? $this->skipEmail
            : true;

        MundipaggUnlimitedOrderRetry::dispatch($payment, $mailId, $shouldSkipMail);
        return true;
    }

    public function shouldRetryChargePayment(Payment $payment): bool
    {
        $subscriber = $payment->subscriber;

        // check that subscriber is active
        if ($subscriber->status != Subscriber::STATUS_ACTIVE) {
            ChargeLog::info('No-Limit retry ignored: subscriber is not active', [
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        // check that payment is really "sem limite"
        if ($payment->type != Payment::TYPE_UNLIMITED) {
            ChargeLog::info('No-Limit retry ignored: wrong payment type', [
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        // check that is really a retry
        if ($payment->status != Payment::STATUS_FAILED) {
            ChargeLog::info('No-Limit retry ignored: payment is not failed', [
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        // check if payment is really credit card
        if ($payment->type_payment != Payment::TYPE_PAYMENT_CREDIT_CARD) {
            ChargeLog::info('No-Limit retry ignored: not a credit card', [
                'payment_id' => $payment->id ?? null,
                'subscriber_id' => $subscriber->id ?? null,
            ]);
            return false;
        }

        // check for existing transaction on same day to prevent duplicated charge
        $transactionToday = Transaction::query()
            ->where('payment_id', $payment->id)
            ->where('origin', Transaction::ORIGIN_RULER)
            ->whereRaw('DATE(created_at) = ?', [Carbon::now()->toDateString()])
            ->first();

        if ($transactionToday) {
            ChargeLog::info('No-Limit retry ignored: transaction already exists on current day for this payment', [
                'transaction' => $transactionToday->toArray(),
            ]);

            return false;
        }

        return true;
    }

    private function writeCsvDebugFile(Payment $payment): void
    {
        $info = [
            Carbon::now()->toDateTimeString(),
            $this->baseDateDebug ?? Carbon::now()->toDateString(),
            $payment->platform->name ?? '',
            $payment->subscriber->name ?? '',
            $payment->subscriber->email ?? '',
            $payment->id ?? '',
            $payment->order_code ?? '',
            $payment->payment_date ?? '',
        ];

        echo 'Wrote to no-limit-charge-log.csv: '.join(';', $info);
        Storage::disk('local')->append('no-limit-charge-log.csv', join(';', $info));
    }
}
