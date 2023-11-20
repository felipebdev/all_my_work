<?php

namespace App\Jobs\Reports\Leads\Models;

use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Services\Objects\LeadReportFilter;

abstract class BaseReport {
    protected $leadRepository;

    public function __construct() {
        $this->leadRepository = app()->make(LeadRepositoryInterface::class);
    }

    protected function changeStatus(string $status = null) {
        if ($status === 'active') return 'Ativo';
        if ($status === 'trial') return 'Trial';
        if ($status === 'canceled') return 'Cancelado';
        if ($status === 'lead') return 'Lead';
        if ($status === 'pending_payment') return 'Pagamento Pendente';
        return 'Inativo';
    }

    abstract public function header();
    abstract public function rows();
    abstract public function query(string $platformId, LeadReportFilter $filters);
    abstract public function name();
}
