<?php

namespace App\Repositories\Leads;

use App\Lead;
use App\Repositories\Contracts\LeadAbandonedCartRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Enumerable;

class LeadAbandonedCartRepository implements LeadAbandonedCartRepositoryInterface
{

    public function listAbandonedLeads(
        int $minutes = self::DEFAULT_ABANDONED_CART_MINUTES,
        int $chunkSize = 1000
    ): Enumerable {
        $pastMinutes = Carbon::now()->subMinutes($minutes);

        $abandoned = Lead::query()
            ->where('cart_status', Lead::CART_STATUS_INITIATED)
            ->where('cart_status_updated_at', '<', $pastMinutes)
            ->lazyById($chunkSize);

        return $abandoned;
    }

    public function markLeadAsAbandoned(string $leadId): int
    {
        $totalAffected = Lead::query()
            ->where([
                'id' => $leadId,
            ])->update([
                'cart_status' => Lead::CART_STATUS_ABANDONED,
                'cart_status_updated_at' => Carbon::now(),
            ]);

        return $totalAffected;
    }
}
