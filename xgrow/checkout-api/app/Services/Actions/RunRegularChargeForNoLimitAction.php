<?php

namespace App\Services\Actions;

use App\Constants\LogKeys;
use App\Logs\ChargeLog;
use App\Payment;
use App\Services\Charges\NoLimitRegularChargeService;
use App\Services\Contracts\SubscriptionServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

/**
 * Process Unlimited order pending payments
 */
class RunRegularChargeForNoLimitAction
{

    private NoLimitRegularChargeService $noLimitRegularChargeService;

    private Carbon $baseDate;

    private ?string $platformId = null;
    private ?int $subscriberId = null;
    private array $paymentIds = [];

    public function __construct(array $debugOptions = [])
    {
        $this->subscriptionService = app()->make(SubscriptionServiceInterface::class);
        $this->noLimitRegularChargeService = app()->make(NoLimitRegularChargeService::class);

        $dryRun = $debugOptions['dry-run'] ?? false;
        $this->noLimitRegularChargeService->enableDryRun($dryRun);
        Log::withContext(['simulation' => $dryRun]);

        $skipEmail = $debugOptions['skip-email'] ?? false;
        $this->noLimitRegularChargeService->enableSkipEmail($skipEmail);

        $updateDate = $debugOptions['update-date'] ?? false;
        $this->noLimitRegularChargeService->enableUpdateDate($updateDate);

        $fakeBaseDate = $debugOptions['base-date'] ?? null;
        $this->baseDate = $fakeBaseDate ? Carbon::createFromFormat('Y-m-d', $fakeBaseDate) : Carbon::now();

        $this->platformId = $debugOptions['platform_id'] ?? null;
        $this->subscriberId = $debugOptions['subscriber_id'] ?? null;
        $this->paymentIds = $debugOptions['payment_ids'] ?? [];
    }

    public function __invoke(): int
    {
        $this->logBegin();

        Redis::set(LogKeys::CHARGE_REGULAR_NOLIMIT_AFFECTED, 0);

        Log::info('Processing unlimited sell started');
        $payments = Payment::select(
            'payments.id',
            'payments.status',
            'payments.payment_date',
            'payments.subscriber_id',
            'payments.installment_number',
            'payments.order_number',
            'payments.installments'
        )
            ->with('plans:id,name')
            ->whereDate('payment_date', '=', $this->baseDate->toDateTime())
            ->where('type', '=', Payment::TYPE_UNLIMITED)
            ->where('status', '=', Payment::STATUS_PENDING)
            ->when($this->platformId, function ($query, $platformId) {
                $query->where('platform_id', $platformId);
            })
            ->when($this->subscriberId, function ($query, $subscriberId) {
                $query->where('subscriber_id', $subscriberId);
            })
            ->when(count($this->paymentIds) > 0, function ($query, $paymentId) {
                $query->whereIn('id', $this->paymentIds);
            })
            ->get();

        Log::info(LogKeys::CHARGE_REGULAR_NOLIMIT_FOUND, ['value' => $payments->count()]);

        $uniqueId = null;
        $paymentsToProcess = [];
        foreach ($payments as $key => $value) {
            if (!empty($value->order_number)) {
                if (!array_key_exists($value->order_number, $paymentsToProcess)) {
                    $paymentsToProcess[$value->order_number] = [];
                }

                array_push($paymentsToProcess[$value->order_number], $value);
            } elseif ($key === 0 ||
                $value->subscriber_id !== $payments[$key - 1]->subscriber_id || // se alunos diferentes: novo sem limite
                $value->installment_number < $payments[$key - 1]->installment_number || //se nº parcela for menor do que anterior: novo sem limite
                !($value->plans->diff($payments[$key - 1]->plans)->isEmpty()) //se produtos diferentes: novo sem limite
            ) {
                $uniqueId = strtoupper(uniqid());
                $paymentsToProcess[$uniqueId] = [$value];
            } else {
                array_push($paymentsToProcess[$uniqueId], $value);
            }
        }

        Log::info(LogKeys::CHARGE_REGULAR_NOLIMIT_GROUPED, ['value' => count($paymentsToProcess)]);

        $total = 0;
        foreach ($paymentsToProcess as $key => $value) {
            if (in_array_field('canceled', 'status', $value)) {
                Log::debug("Payment ignored (status canceled)", ['order_number' => $key]);
                continue;
            }

            foreach ($value as $payment) {
                $result = $this->noLimitRegularChargeService->dispatchSingleNoLimitPayment($payment);
                if ($result) {
                    $total++;
                    Redis::incr(LogKeys::CHARGE_REGULAR_NOLIMIT_AFFECTED);
                }
            }
        }

        $this->logEnd($total);

        return $total;
    }

    private function logBegin(): void
    {
        $uuid = (string) Str::uuid();
        Log::withContext(['schedule-trace-id' => $uuid]);
        Log::withContext(['running_origin' => 'charge_nolimit']);
        Log::withContext(['hostname-dispatcher' => gethostname()]);

        Log::info(LogKeys::CHARGE_REGULAR_NOLIMIT_LASTSTART, ['value' => Carbon::now()->toDateTimeString()]);

        Redis::set(LogKeys::CHARGE_REGULAR_NOLIMIT_LASTSTART, Carbon::now()->toDateTimeString());
        Redis::set(LogKeys::CHARGE_REGULAR_NOLIMIT_LASTTRACE, $uuid);

        // @todo Delete on ChargeLog deprecation
        ChargeLog::withContext(['schedule-trace-id' => $uuid]);
    }

    private function logEnd(int $total): void
    {
        Log::info(LogKeys::CHARGE_REGULAR_NOLIMIT_TOTAL, ['value' => $total]);
        Log::info(LogKeys::CHARGE_REGULAR_NOLIMIT_LASTEND, ['value' => Carbon::now()->toDateTimeString()]);

        Redis::set(LogKeys::CHARGE_REGULAR_NOLIMIT_TOTAL, $total);
        Redis::set(LogKeys::CHARGE_REGULAR_NOLIMIT_LASTEND, Carbon::now()->toDateTimeString());

        Log::withoutContext();
    }

}
