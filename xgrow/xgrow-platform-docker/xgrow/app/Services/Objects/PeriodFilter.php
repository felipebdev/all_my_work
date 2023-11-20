<?php

namespace App\Services\Objects;

use DateTime;
use Exception;

class PeriodFilter {
    public $startDate;    
    public $endDate;
    public $format;
    public $startTime;
    public $endTime;

    public function __construct(
        string $startDate, 
        string $endDate,
        string $format = 'Y-m-d',
        string $startTime = '00:00:00',
        string $endTime = '23:59:59'
    ) {
        if (!validateDate($startDate, $format) || 
            !validateDate($endDate, $format) 
        ) {
            throw new Exception('Date invalid');
        }

        if (!validateDate($startTime, 'H:i:s') ||
            !validateDate($endTime, 'H:i:s')
        ) {
            throw new Exception('Time is invalid');
        }

        if ($format !== 'Y-m-d') {
            $startDate = DateTime::createFromFormat($format, $startDate)
                ->format('Y-m-d');

            $endDate = DateTime::createFromFormat($format, $endDate)
                ->format('Y-m-d');
        }

        $this->startDate = "{$startDate}T{$startTime}";
        $this->endDate = "{$endDate}T{$endTime}";
        $this->format = $format;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }
}