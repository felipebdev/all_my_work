<?php

namespace App\Services\Contracts;

interface PlatformableInterface
{
    /**
     * PlatformableInterface constructor.
     *
     * Initial platform can be null due to Laravel Lifecycle
     *
     * @param  string|null  $platformId
     */
    public function __construct(?string $platformId);


    /**
     * Define platform for service
     *
     * @param  string  $platformId
     * @return mixed
     */
    public function setPlatformId(string $platformId);

    /**
     * Return platform defined for service
     *
     * @return string|null
     */
    public function getPlatformId(): ?string;
}
