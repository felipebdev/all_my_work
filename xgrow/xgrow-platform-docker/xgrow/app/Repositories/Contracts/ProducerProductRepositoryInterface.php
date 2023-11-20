<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface ProducerProductRepositoryInterface extends BaseRepositoryInterface
{

    public function reportProducerProducts(int $producerId): Builder;

    /**
     * List products of a given producer
     *
     * @param  int  $producerId
     * @return \Illuminate\Support\Collection<\App\ProducerProduct>
     */
    public function listProducerProductsByProducerId(int $producerId): Collection;


    /**
     * Check if producer already has an active contract associated to product
     *
     * @param  int  $producerId
     * @param  int  $productId
     * @return bool
     */
    public function hasActiveContract(int $producerId, int $productId): bool;

    /**
     * List all active contracts associated to product
     *
     * @param  int  $productId
     * @return \Illuminate\Support\Collection
     */
    public function listActiveContracts(int $productId): Collection;

    /**
     * Get total percent on active contracts associated to given product
     *
     * @param  int  $productId
     * @return float
     */
    public function totalPercentWithActiveContracts(int $productId): float;

    /**
     * Cancel Producer Product
     *
     * @param  int  $producerProductId
     * @return bool true if successfully canceled, false otherwise
     * @throw \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function cancelProducerProduct(int $producerProductId): bool;

}
