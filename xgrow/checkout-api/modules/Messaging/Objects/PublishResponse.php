<?php

namespace Modules\Messaging\Objects;

class PublishResponse
{

    protected bool $isSuccessful;

    /**
     * @var mixed|null Store raw response
     */
    protected $rawData = null;

    public static function ok($rawData = null): self
    {
        return new self(true, $rawData);
    }

    public static function failed($rawData = null): self
    {
        return new self(false, $rawData);
    }

    /**
     * PubSubResponse constructor.
     *
     * @param  bool  $isSuccessful  True if publishing process ran accordingly expectations, false otherwise
     * @param  mixed|null  $rawData
     */
    public function __construct(bool $isSuccessful, $rawData = null)
    {
        $this->isSuccessful = $isSuccessful;
        $this->rawData = $rawData;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * Return raw data (CAUTION, raw data can be of any type)
     *
     * @return mixed|null
     */
    public function getRawData()
    {
        return $this->rawData;
    }


}
