<?php

namespace App\Services\Actions;

use App\ChargeRuler;
use App\Constants\LogKeys;
use App\Logs\ChargeLog;
use App\Logs\XgrowLog;
use App\Payment;
use App\Services\ChargeRulerSettings;
use App\Services\Charges\NoLimitRetryChargeService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class RunChargeRulerForNoLimitAction
{
    private NoLimitRetryChargeService $noLimitRetry;

    private Carbon $baseDate;

    private ?string $platformId = null;
    private ?int $subscriberId = null;
    private ?int $paymentId = null;

    public function __construct(array $debugOptions = [])
    {
        $this->noLimitRetry = app()->make(NoLimitRetryChargeService::class);

        $dryRun = $debugOptions['dry-run'] ?? false;
        if ($dryRun) {
            $this->noLimitRetry->enableDryRunDebug();
        }

        $skipEmail = $debugOptions['skip-email'] ?? false;
        $this->noLimitRetry->skipEmail($skipEmail);

        $fakeBaseDate = $debugOptions['base-date'] ?? null;
        if ($fakeBaseDate) {
            $this->noLimitRetry->setBaseDateDebug($fakeBaseDate);
            $this->baseDate = Carbon::createFromFormat('Y-m-d', $fakeBaseDate);
        } else {
            $this->baseDate = Carbon::now();
        }

        $this->platformId = $debugOptions['platform_id'] ?? null;
        $this->subscriberId = $debugOptions['subscriber_id'] ?? null;
        $this->paymentId = $debugOptions['payment_id'] ?? null;
    }

    public function __invoke(): int
    {
        $this->logBegin();

        Redis::set(LogKeys::CHARGE_RULER_NOLIMIT_AFFECTED, 0);

        $total = 0;
        try {
            $rules = ChargeRulerSettings::defaultChargesForNolimit();

            foreach ($rules as $rule) {
                $total += $this->processRule($rule);
            }
        } catch (Exception $e) {
            report($e);
        }

        $this->logEnd($total);

        return $total;
    }

    private function processRule(ChargeRuler $rule): int
    {
        $paymentDate = $this->baseDate->clone()->subDays($rule->interval)->toDateString();

        $payments = $this->listFailedPayment($paymentDate);

        Log::withContext(['rule_interval' => $rule->interval ?? null]);
        Log::withContext(['rule_id' => $rule->id ?? null]);
        Log::info('No-Limit retry command processing rule', [
            'total_affected' => $payments->count() ?? null,
        ]);

        Log::info(LogKeys::CHARGE_RULER_NOLIMIT_FOUND, ['value' => $payments->count()]);

        $total = 0;
        foreach ($payments as $payment) {
            //ChargeLog::includePaymentContext($payment);
            $result = $this->processPaymentRule($rule, $payment);
            if ($result) {
                $total++;
                Redis::incr(LogKeys::CHARGE_RULER_NOLIMIT_AFFECTED);
            }
        }

        return $total;
    }

    /**
     * List no-limit payments with credit card failed with payment date in a given date
     *
     * @param  string  $paymentDate  Payment date on YYYY-MM-DD format
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function listFailedPayment(string $paymentDate)
    {
        $platformId = $this->platformId;
        $subscriberId = $this->subscriberId;
        $paymentId = $this->paymentId;

        return Payment::with(['platform', 'subscriber'])
            ->where('type', Payment::TYPE_UNLIMITED)
            ->where('type_payment', Payment::TYPE_PAYMENT_CREDIT_CARD)
            ->whereIn('status', [Payment::STATUS_FAILED])
            ->where('payment_date', $paymentDate)
            ->when($platformId, function ($query, $platformId) {
                $query->where('platform_id', $platformId);
            })
            ->when($subscriberId, function ($query, $subscriberId) {
                $query->where('subscriber_id', $subscriberId);
            })
            ->when($paymentId, function ($query, $paymentId) {
                $query->where('id', $paymentId);
            })
            ->get();
    }

    private function processPaymentRule(ChargeRuler $rule, Payment $payment): bool
    {
        try {
            return $this->noLimitRetry->retryChargePayment($payment, $rule->email_id);
        } catch (Exception $exception) {
            $this->logException($exception, $payment);
        }

        return false;
    }

    private function logBegin(): void
    {
        $uuid = (string) Str::uuid();
        Log::withContext(['schedule-trace-id' => $uuid]);
        Log::withContext(['running_origin' => 'ruler_nolimit']);
        Log::withContext(['hostname-dispatcher' => gethostname()]);

        Log::info(LogKeys::CHARGE_RULER_NOLIMIT_LASTSTART, ['value' => Carbon::now()->toDateTimeString()]);

        Redis::set(LogKeys::CHARGE_RULER_NOLIMIT_LASTSTART, Carbon::now()->toDateTimeString());
        Redis::set(LogKeys::CHARGE_RULER_NOLIMIT_LASTTRACE, $uuid);

        // @todo Delete on ChargeLog deprecation
        ChargeLog::withContext(['schedule-trace-id' => $uuid]);
    }

    private function logEnd(int $total): void
    {
        Log::info(LogKeys::CHARGE_RULER_NOLIMIT_TOTAL, ['value' => $total]);
        Log::info(LogKeys::CHARGE_RULER_NOLIMIT_LASTEND, ['value' => Carbon::now()->toDateTimeString()]);

        Redis::set(LogKeys::CHARGE_RULER_NOLIMIT_TOTAL, $total);
        Redis::set(LogKeys::CHARGE_RULER_NOLIMIT_LASTEND, Carbon::now()->toDateTimeString());

        Log::withoutContext();
    }

    private function logException(Exception $exception, $payment)
    {
        // Exception on charge ruler needs immediate attention
        Log::emergency(LogKeys::CHARGE_RULER_NOLIMIT_EXCEPTION, [
            'platform' => $payment->platform->id ?? null,
            'payment' => $payment->id ?? null,
            'subscriber_id' => $payment->subscriber->id ?? null,
            'exception' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ],
        ]);
    }
}
