<?php

namespace App\Jobs\Reports\Sales\Models;

use App\Payment;
use App\Services\Objects\SaleReportFilter;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Subscription;

abstract class BaseReport {
    protected PaymentRepositoryInterface $paymentRepository;

    public function __construct() {
        $this->paymentRepository = app()->make(PaymentRepositoryInterface::class);
    }

    protected function changeStatus(string $status = null) {
        return Payment::listStatus()[$status] ?? '-';
    }

    protected function subscriptionStatus(string $subscriptionStatus = null)
    {
        $list = Subscription::listStatus();
        return $list[$subscriptionStatus] ?? '-';
    }

    protected function paymentMethod(string $payment = null) {
        if ($payment === 'boleto') return 'Boleto';
        if ($payment === 'pix') return 'Pix';
        if ($payment === 'credit_card') return 'Cartão de Crédito';
        return '-';
    }

    protected function recurrenceLabel(int $recurrence = null)
    {
        if ($recurrence === 7) return 'Semanal';
        if ($recurrence === 30) return 'Mensal';
        if ($recurrence === 60) return 'Bimestral';
        if ($recurrence === 90) return 'Trimestral';
        if ($recurrence === 180) return 'Semestral';
        if ($recurrence === 360) return 'Anual';
        return '-';
    }

    abstract public function header();
    abstract public function rows();
    abstract public function query(string $platformId, SaleReportFilter $filters);
    abstract public function name();
}
