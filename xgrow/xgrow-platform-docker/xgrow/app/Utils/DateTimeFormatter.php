<?php

namespace App\Utils;

use Carbon\Carbon;

class DateTimeFormatter
{

    /**
     * Parse a local datetime string and format as ISO8601 (eg: 2001-12-31T12:34:56-03:00)
     *
     * @param  string|null  $datetime
     * @return string|null
     */
    public static function fromLocalToIso8601String(?string $datetime): ?string
    {
        if (is_null($datetime)) {
            return null;
        }

        return Carbon::parse($datetime)->toIso8601String();
    }

    /**
     * Parse UTC datetime string and format as ISO8601 (eg: 2001-12-31T12:34:56-03:00)
     *
     * @param  string|null  $datetime
     * @return string|null
     */
    public static function fromUtcToIso8601String(?string $datetime): ?string
    {
        if (is_null($datetime)) {
            return null;
        }

        return Carbon::parse($datetime, 'UTC')->toIso8601String();
    }
}