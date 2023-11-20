<?php

namespace App\Jobs\Reports\Producers\Models;

use App\Repositories\Contracts\ProducerRepositoryInterface;
use App\Services\Objects\ProducerReportFilter;

class ProducersReport
{
    protected $producerRepository;

    public function __construct(ProducerRepositoryInterface $producerRepository)
    {
        $this->producerRepository = $producerRepository;
    }

    public function name()
    {
        return 'producers';
    }

    public function query(string $platformId, ProducerReportFilter $filters)
    {
        return $this->producerRepository->reportProducers($platformId, $filters);
    }

    public function header()
    {
        return [
            'Nome',
            'E-mail',
            'Produto',
            'Porcentagem'
        ];
    }

    public function rows()
    {
        return [
            'platforms_users_name' => 'platforms_users_name',
            'platforms_users_email' => 'platforms_users_email',
            'products_name' => 'products_name',
            'producer_products_percent' => 'producer_products_percent',
        ];
    }
}
