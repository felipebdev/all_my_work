<?php

namespace App\Jobs\Reports\Subscribers\Models;

use App\Repositories\Contracts\SubscriberRepositoryInterface;
use App\Services\Objects\SubscriberFilter;

abstract class BaseReport {
    protected $subscriberRepository;

    public function __construct() {
        $this->subscriberRepository = app()->make(SubscriberRepositoryInterface::class);
    }

    /*
    protected function changeStatus(string $status = null) {
        if ($status === 'active') return 'Ativo';
        if ($status === 'trial') return 'Trial';
        if ($status === 'canceled') return 'Cancelado';
        if ($status === 'lead') return 'Lead';
        if ($status === 'pending_payment') return 'Pagamento Pendente';
        return 'Inativo';
    }
    */

    abstract public function header();
    abstract public function rows();
    abstract public function query(SubscriberFilter $filters);
    abstract public function name();
}
