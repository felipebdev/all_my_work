<?php

namespace App\Services\Finances\Contracts;

/**
 * Implement this interface for saving raw data
 *
 * Raw data can be of ANY type, be careful unpacking raw data.
 */
interface SavesRawData
{
    /**
     * Set raw data, allows any variable type
     *
     * @param mixed $data
     * @return $this
     */
    public function setRawData($data): self;

    /**
     * Return raw data previously set,
     *
     * @return mixed Raw data
     */
    public function getRawData();
}
