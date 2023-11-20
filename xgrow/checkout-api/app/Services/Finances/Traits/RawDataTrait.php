<?php

namespace App\Services\Finances\Traits;

/**
 * This trait allows raw data saving.
 *
 * @see \App\Services\Finances\Contracts\SavesRawData
 */
trait RawDataTrait
{

    protected $rawData = null;

    public function getRawData()
    {
        return $this->rawData;
    }

    public function setRawData($data): self
    {
        $this->rawData = $data;
        return $this;
    }
}
