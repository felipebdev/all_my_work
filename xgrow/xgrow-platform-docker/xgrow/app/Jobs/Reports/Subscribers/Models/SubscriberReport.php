<?php

namespace App\Jobs\Reports\Subscribers\Models;

use App\Services\Objects\SubscriberReportFilter;

class SubscriberReport extends BaseReport
{
    public function name()
    {
        return 'alunos';
    }

    public function query(string $platformId, SubscriberReportFilter $filters)
    {
        return $this->subscriberRepository->reportSubscriber($platformId, $filters);
    }

    public function header()
    {
        return [
            'Nome', 'E-mail', 'Telefone', 'Data de Cadastro', 'Status', 'Último Acesso', 'Produto'
        ];
    }

    public function rows()
    {
        return [
            'name' => 'name',
            'email' => 'email',
            'cel_phone' => 'cel_phone',
            'created' => function ($data) {
                return $data ? date('d/m/Y H:i:s', strtotime($data)) : ' - ';
            },
            'status' => function ($data) {
                return $this->changeStatus($data);
            },
            'login' => function ($data) {
                return $data ? date('d/m/Y H:i:s', strtotime($data)) : ' - ';
            },
            'products_name' => 'products_name',
        ];
    }
}
