<?php

namespace App\Repositories\Affiliation;

use App\Plan;
use App\Producer;
use App\ProducerProduct;
use Illuminate\Support\Collection;

class AffiliateProductRepository
{

    public function getAffiliateActiveContractsByPlanIds(string $affiliateId, array $planIds): Collection
    {
        $productIds = Plan::whereIn('id', $planIds)->get()->pluck('product_id')->toArray();

        return $this->getAffiliateActiveContractsByProductIds($affiliateId, $productIds);
    }


    /**
     * @param  string  $affiliateId
     * @param  array  $productIds
     * @return \Illuminate\Support\Collection<ProducerProduct>
     */
    public function getAffiliateActiveContractsByProductIds(string $affiliateId, array $productIds): Collection
    {
        return ProducerProduct::where('producer_id', $affiliateId)
            ->whereIn('product_id', $productIds)
            ->where('status', ProducerProduct::STATUS_ACTIVE)
            ->whereRelation('producer', 'type', Producer::TYPE_AFFILIATE)
            ->where(function ($query) {
                $query->whereRaw('contract_limit >= CURDATE()');
                $query->orWhereNull('contract_limit');
            })
            ->get();
    }
}
