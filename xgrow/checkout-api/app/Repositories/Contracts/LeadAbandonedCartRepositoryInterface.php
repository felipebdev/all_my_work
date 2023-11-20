<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Enumerable;

interface LeadAbandonedCartRepositoryInterface
{
    public const DEFAULT_ABANDONED_CART_MINUTES = 15;

    /**
     * List of Leads that reached time limit for abandoned cart
     *
     * @param  int  $minutes
     * @param  int  $chunkSize
     * @return \Illuminate\Support\Enumerable
     */
    public function listAbandonedLeads(
        int $minutes = self::DEFAULT_ABANDONED_CART_MINUTES,
        int $chunkSize = 1000
    ): Enumerable;

    /**
     * @param  string  $leadId
     * @return int
     */
    public function markLeadAsAbandoned(string $leadId): int;
}
