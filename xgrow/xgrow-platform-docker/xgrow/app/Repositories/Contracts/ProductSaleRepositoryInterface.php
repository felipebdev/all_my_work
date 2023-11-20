<?php

namespace App\Repositories\Contracts;

interface ProductSaleRepositoryInterface extends BaseRepositoryInterface
{

    /**
     * Get data from product sale graph
     *
     * @param  string  $start
     * @param  string  $end
     * @param  int  $product_id
     * @return array
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(string  $start, string  $end, int  $product_id): array;

}
