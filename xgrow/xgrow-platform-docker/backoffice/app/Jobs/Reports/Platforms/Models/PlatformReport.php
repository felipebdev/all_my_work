<?php

namespace App\Jobs\Reports\Platforms\Models;

use App\Services\Objects\PlatformFilter;
use App\Platform;
use Carbon\Carbon;

class PlatformReport extends BaseReport
{
    public function name(): string
    {
        return 'plataformas';
    }

    public function query(PlatformFilter $filters)
    {
        return $this->platformRepository->ListPlatformClient($filters);
    }

    public function header(): array
    {
        return [
            'ID', 'Criação', 'Atualização', 'Plataforma', 'Nome da Empresa', 'URL'
        ];
    }

    public function rows(): array
    {
        return [
            'id' => 'id',
            'created_at' => function ($data) {
                return $data ? $data->format('Y-m-d H:i:s') : '';
            },
            'updated_at' => function ($data) {
                return $data ? $data->format('Y-m-d H:i:s') : '';
            },
            'name' => 'name',
            'company_name' => 'company_name',
            'url' => 'url',
        ];

    }
}
