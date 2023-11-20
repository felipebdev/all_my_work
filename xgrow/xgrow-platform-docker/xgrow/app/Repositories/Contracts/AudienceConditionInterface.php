<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface AudienceConditionInterface
{
    public function generateQueryByAudienceId(string $platformId, int $audienceId): Builder;

    public function isSubscriberInAudience(int $subscriberId, int $audienceId): bool;

    /**
     * Return all subscribers based on platform and audiences IDs
     *
     * @param  string  $platformId
     * @param  iterable  $audienceIds List of audiences IDs
     * @return \Illuminate\Support\Collection Unique subscribers
     */
    public function subscribersFromAudienceIds(string $platformId, iterable $audienceIds): Collection;
}
