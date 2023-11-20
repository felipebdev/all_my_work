<?php

namespace App\Jobs\Reports\Subscribers\Models;

use App\Services\Objects\SubscriberFilter;
use App\Subscriber;
use Carbon\Carbon;

class SubscriberReport extends BaseReport
{
    public function name(): string
    {
        return 'alunos';
    }

    public function query(SubscriberFilter $filters)
    {
        return $this->subscriberRepository->listAll($filters);
    }

    public function header(): array
    {
        return [
            'Nome', 'E-mail', 'Celular', 'Telefone', 'Status', 'Tipo de documento', 'Nº do Documento', 'Data de cadastro'
        ];
    }

    public function rows(): array
    {
        return [
            'name' => 'name',
            'email' => 'email',
            'cel_phone' => 'cel_phone',
            'main_phone' => 'main_phone',
            'status' => function ($data) {
                return Subscriber::allStatus()[$data];
            },
            'document_type' => 'document_type',
            'document_number' => 'document_number',
            'created_at' => function ($data) {
                return $data ? $data->format('Y-m-d H:i:s') : '';
            }
        ];

    }
}
