<?php

namespace App\Exports;

use App\Repositories\Contracts\AudienceConditionInterface;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithCustomQuerySize;
use Maatwebsite\Excel\Concerns\WithMapping;

class AudienceExport implements FromQuery, WithCustomQuerySize, WithMapping
{
    use Exportable;
    use RegistersEventListeners;

    private $platformId;
    private $conditions;

    public function __construct(string $platformId, array $conditions)
    {
        $this->platformId = $platformId;
        $this->conditions = $conditions;
    }

    public function querySize(): int
    {
        return 500;
    }

    public function query()
    {
        /** @var $repo AudienceConditionInterface */
        $repo = resolve(AudienceConditionInterface::class);
        return $repo->generateQueryFromArray($this->platformId, $this->conditions);
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->plan_name,
            $this->formatPaymentType($row->subscriber_status),
            $row->created_at,
            $row->last_acess,
            $this->formatPaymentType($row->payment_type),
            $row->payment_method,
            $row->payment_status,
        ];
    }

    private function formatSubscriberStatus($status)
    {
        $map = [
            'active' => 'Ativo',
            'trial' => 'Trial',
            'canceled' => 'Cancelado',
            'lead' => 'Lead',
            'pending_payment' => 'Pagamento pendente',
        ];

        return $map[$status] ?? '';
    }

    private function formatPaymentType($type)
    {
        $map = [
            'P' => 'Venda única',
            'R' => 'Assinatura',
            'U' => 'Sem limite',
        ];

        return $map[$type] ?? '';
    }
}
