<?php

namespace App\Services\Actions;

use App\Logs\ChargeLog;
use App\Services\Charges\SubscriptionChargeService;
use Carbon\Carbon;

abstract class BaseRegularChargeForSubscription
{

    protected SubscriptionChargeService $subscriptionChargeService;

    protected bool $dryRun = false;
    protected ?string $platformId = null;
    protected ?int $subscriberId = null;
    protected ?int $recurrenceId = null;

    public function __construct(array $debugOptions = [])
    {
        $this->subscriptionChargeService = app()->make(SubscriptionChargeService::class);

        $this->dryRun = $debugOptions['dry-run'] ?? false;
        if ($this->dryRun) {
            ChargeLog::withContext(['simulation' => true]);
            $this->subscriptionChargeService->enableDryRun();
        }

        $skipEmail = $debugOptions['skip-email'] ?? false;
        $this->subscriptionChargeService->skipEmail($skipEmail);

        $baseDate = $debugOptions['base-date'] ?? null;
        if ($baseDate) {
            $date = Carbon::createFromFormat('Y-m-d', $baseDate);
            $this->subscriptionChargeService->setBaseDate($date);
        }

        $this->platformId = $debugOptions['platform_id'] ?? null;
        $this->subscriberId = $debugOptions['subscriber_id'] ?? null;
        $this->recurrenceId = $debugOptions['recurrence_id'] ?? null;
    }

    abstract public function __invoke();

}
