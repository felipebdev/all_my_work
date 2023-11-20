<?php

namespace App\Repositories\Contracts;
use App\Services\Objects\SubscriberReportFilter;

interface SubscriberRepositoryInterface
{
    public function reportSubscriber(
        string $platformId,
        SubscriberReportFilter $filters
    );
}
