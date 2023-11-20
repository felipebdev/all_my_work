<?php

namespace App\Repositories\Affiliations\Objects;

use App\Services\Objects\PeriodFilter;
use Illuminate\Support\Facades\Log;

class AffiliationFilter
{
    public ?string $search = null;
    public ?string $productName = null;
    public ?PeriodFilter $createdPeriod = null;
    public array $products = [];
    public array $affiliationStatus = [];
    public array $names = [];
    public array $emails = [];

    public static function fromArray(array $request): self
    {
        $createdPeriodFilter = isset($request['created_period_filter'])
            ? explode('-', $request['created_period_filter'])
            : ['', ''];

        return new self(
            $request['search'] ?? null,
            $request['product_name'] ?? null,
            parseBrDate($createdPeriodFilter[0]),
            parseBrDate($createdPeriodFilter[1]),
            $request['products'] ?? [],
            $request['affiliation_status'] ?? [],
            $request['names'] ?? [],
            $request['emails'] ?? [],
        );
    }

    public static function empty(): self
    {
        return new self();
    }

    /**
     * @param  string|null  $search
     * @param  string|null  $productName
     * @param  string|null  $createdStartDate
     * @param  string|null  $createdEndDate
     * @param  array  $products
     * @param  array  $affiliationStatus
     * @param  array  $names
     * @param  array  $emails
     */
    public function __construct(
        ?string $search = null,
        ?string $productName = null,
        ?string $createdStartDate = null,
        ?string $createdEndDate = null,
        array $products = [],
        array $affiliationStatus = [],
        array $names = [],
        array $emails = []
    ) {
        $this->search = $search;
        $this->productName = $productName;

        if (validateDate($createdStartDate, 'Y-m-d') && validateDate($createdEndDate, 'Y-m-d')) {
            try {
                $this->createdPeriod = new PeriodFilter($createdStartDate, $createdEndDate);
            } catch (\Exception $e) {
                Log::error('Erro ao converter data no filtro. '.$e->getMessage());
            }
        }

        $this->products = $products;
        $this->affiliationStatus = $affiliationStatus;
        $this->names = $names;
        $this->emails = $emails;
    }
}
