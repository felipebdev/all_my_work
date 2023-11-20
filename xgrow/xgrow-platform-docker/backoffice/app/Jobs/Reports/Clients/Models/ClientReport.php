<?php

namespace App\Jobs\Reports\Clients\Models;

use App\Services\Objects\ClientFilter;
use App\Client;
use Carbon\Carbon;

class ClientReport extends BaseReport
{
    public function name(): string
    {
        return 'clientes';
    }

    public function query(ClientFilter $filters)
    {
        return $this->clientRepository->listAll($filters);
    }

    public function header(): array
    {
        return [
            'Nome', 'Sobrenome', 'CPF', 'CNPJ', 'E-Mail', 'Data Criação'
        ];
    }

    public function rows(): array
    {
        return [
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'cpf' => 'cpf',
            'cnpj' => 'cnpj',
            'email' => 'email',
            'created_at' => function ($data) {
                return $data ? $data->format('Y-m-d H:i:s') : '';
            },
        ];

    }
}
