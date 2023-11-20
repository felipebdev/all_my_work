<?php


namespace App\Services\Finances;

use App\Services\Mundipagg\Calculator\Objects\OrderValues;

/**
 * Class Bookkeeper is intended to maintain the accountability and records of a business
 *
 * @package App\Services\Finances
 */
class Bookkeeper
{

    protected $mainId = null;

    protected OrderValues $mainValues;

    /**
     * @var array<int, \App\Services\Mundipagg\Calculator\Objects\OrderValues>
     */
    protected array $results = [];

    public function setMainValue($id, OrderValues $values): self
    {
        $this->mainId = $id;
        $this->mainValues = $values;
        return $this;
    }

    public function addValue($id, OrderValues $resultInBrl): self
    {
        if ($id == $this->mainId) {
            return $this->setMainValue($id, $resultInBrl);
        }
        $this->results[$id] = $resultInBrl;
        return $this;
    }

    public function getMainId()
    {
        return $this->mainId;
    }

    public function getMainValue(): OrderValues
    {
        return $this->mainValues;
    }


    public function getValues(): array
    {
        return $this->results;
    }

    public function getValuesIds(): array
    {
        return array_keys($this->results);
    }

    public function getValueById(int $id): ?OrderValues
    {
        if ($id == $this->mainId) {
            return $this->mainValues;
        }

        return $this->results[$id] ?? null;
    }

    /**
     * Includes main product
     *
     * @return array<int, \App\Services\Mundipagg\Calculator\Objects\OrderValues>
     */
    public function getAllValues(): array
    {
        if (is_null($this->mainId)) {
            return $this->results;
        }

        // merge preserving keys
        return array_replace([$this->mainId => $this->mainValues], $this->results);
    }

    public function getAllValuesIds(): array
    {
        $resultsIds = $this->getValuesIds();

        if (is_null($this->mainId)) {
            return $resultsIds;
        }

        return array_merge([$this->mainId], $resultsIds);
    }

    public function getTotalPrice(): int
    {
        $total = 0;
        foreach ($this->getAllValues() as $id => $value) {
            $total += $value->getPrice();
        }
        return $total;
    }

    public function getTotalCoupon(): int
    {
        $total = 0;
        foreach ($this->getAllValues() as $id => $value) {
            $total += $value->getCoupon();
        }
        return $total;
    }

    public function getTotalClientTax(): int
    {
        $total = 0;
        foreach ($this->getAllValues() as $id => $value) {
            $total += $value->getClientTaxTransaction();
        }
        return $total;
    }


}
