<?php

namespace App\Services\Objects;

use Illuminate\Support\Facades\Log;

class LeadReportFilter
{
    public $search = null;
    public $plans = null;
    public $createdPeriod = null;
    public $onlyFailedTransactions = null;

    public function __construct(
        string $search = null,
        array $plans = null,
        string $createdStartDate = null,
        string $createdEndDate = null,
        string $onlyFailedTransactions = null
    )
    {
        $this->search = $search;
        $this->plans = $plans;
        $this->onlyFailedTransactions = filter_var($onlyFailedTransactions, FILTER_VALIDATE_BOOLEAN);
        if (validateDate($createdStartDate, 'Y-m-d') && validateDate($createdEndDate, 'Y-m-d')) {
            try {
                $this->createdPeriod = new PeriodFilter($createdStartDate, $createdEndDate);
            } catch (\Exception $e) {
                Log::error('Erro ao converter data no filtro. ' . $e->getMessage());
            }
        }
    }
}
