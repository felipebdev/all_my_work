<?php

namespace App\Traits;

use DateTimeInterface;

/**
 * Laravel 7 updated date serialization to ISO-8601 format (eg: 2019-12-02T20:01:00.283041Z);
 * this trait adds backwards compatibility setting to legacy format (eg: 2019-12-02 20:01:00).
 *
 * Use this trait on every model where legacy date is required.
 *
 * Reference: https://laravel.com/docs/7.x/upgrade#date-serialization
 */
trait LegacyDateSerialization
{

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
