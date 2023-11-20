<?php

namespace App\Repositories\Affiliations;

use App\ProducerProduct;
use Carbon\Carbon;

/**
 * This class is the aggregate root for handling affiliation to a product (aka "contracts")
 */
class AffiliationProductRepository
{
    public function getProducerProductById(int $producerProductId): ?ProducerProduct
    {
        return ProducerProduct::find($producerProductId);
    }

    /**
     * Cancel Producer Product (aka "contract")
     *
     * @param  \App\ProducerProduct  $producerProduct
     * @return bool
     */
    public function cancelProducerProduct(ProducerProduct $producerProduct): bool
    {
        $producerProduct->status = ProducerProduct::STATUS_CANCELED;
        $producerProduct->canceled_at = Carbon::now();

        return $producerProduct->save();
    }
}
