<?php

namespace App\Services\Objects;

class ProducerReportFilter extends FillableObject
{
    public ?string $searchTerm = null;
    public ?array $productsId = null;
    public ?array $producerProductStatusId = null;

    public function setSearchTerm(?string $searchTerm): ProducerReportFilter
    {
        $this->searchTerm = $searchTerm;
        return $this;
    }

    public function setProductsId(?array $productsId): ProducerReportFilter
    {
        $this->productsId = $productsId;
        return $this;
    }

    public function setProducerProductStatusId(?array $producerProductStatusId): void
    {
        $this->producerProductStatusId = $producerProductStatusId;
    }

}
