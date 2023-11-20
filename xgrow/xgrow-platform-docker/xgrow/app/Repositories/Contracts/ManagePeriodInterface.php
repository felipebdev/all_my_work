<?php
namespace App\Repositories\Contracts;

interface ManagePeriodInterface
{

    /**
     * Get labels by period
     *
     * @return array
     */
    public function getLabel(): array;

    /**
     * Get sales product by period
     *
     * @param  int  $product_id
     * @return array
     */
    public function getSalesProduct(int  $product_id): array;

}
