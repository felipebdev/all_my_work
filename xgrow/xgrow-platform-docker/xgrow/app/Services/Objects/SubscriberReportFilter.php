<?php

namespace App\Services\Objects;

use Illuminate\Support\Facades\Log;

class SubscriberReportFilter
{
    public ?string $search = null;
    public ?array $plans = null;
    public ?array $subscriberStatus = null;
    public ?PeriodFilter $createdPeriod = null;
    public ?PeriodFilter $lastAccessedPeriod = null;
    public ?bool $neverAccessed = null;

    public function __construct(
        ?string $search = null,
        ?array $plans = null,
        ?array $subscriberStatus = null,
        ?string $createdStartDate = null,
        ?string $createdEndDate = null,
        ?string $lastAccessedStartDate = null,
        ?string $lastAccessedEndDate = null,
        ?bool $neverAccessed = null,
        ?bool $emailWrongFilter = null
    ) {
        $this->search = $search;
        $this->plans = $plans;
        $this->subscriberStatus = $subscriberStatus;
        if (validateDate($createdStartDate, 'Y-m-d') && validateDate($createdEndDate, 'Y-m-d')) {
            try {
                $this->createdPeriod = new PeriodFilter($createdStartDate, $createdEndDate);
            } catch (\Exception $e) {
                Log::error('Erro ao converter data no filtro. ' . $e->getMessage());
            }
        }
        if (validateDate($lastAccessedStartDate, 'Y-m-d') && validateDate($lastAccessedEndDate, 'Y-m-d')) {
            try {
                $this->lastAccessedPeriod = new PeriodFilter($lastAccessedStartDate, $lastAccessedEndDate);
            } catch (\Exception $e) {
                Log::error('Erro ao converter data no filtro. ' . $e->getMessage());
            }
        }
        $this->neverAccessed = $neverAccessed;
        $this->emailWrongFilter = $emailWrongFilter;
    }
}
