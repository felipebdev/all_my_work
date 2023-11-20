<?php

namespace App\Jobs\Reports\Leads\Models;

use App\Services\Objects\LeadReportFilter;

class LeadReport extends BaseReport
{
    public function name()
    {
        return 'leads';
    }

    public function query(string $platformId, LeadReportFilter $filters)
    {
        return $this->leadRepository->reportLead($platformId, $filters);
    }

    public function header()
    {
        return [
            'Nome', 'E-mail', 'Telefone', 'Data de Cadastro', 'Produto'
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
            'plan_name' => 'plan_name',
        ];
    }
}
