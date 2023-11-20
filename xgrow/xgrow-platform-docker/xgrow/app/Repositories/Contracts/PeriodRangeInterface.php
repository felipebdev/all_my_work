<?php
namespace App\Repositories\Contracts;

interface PeriodRangeInterface
{

    /**
     * Get labels by period
     *
     * @param  string  $start
     * @param  string  $end
     * @return array
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getLabel(string  $start, string  $end): array;

    /**
     * Get sales product by period
     *
     * @param  string  $start
     * @param  string  $end
     * @param  int  $product_id
     * @param  string  $platform_id
     * @return array
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getSalesProduct(string  $start, string  $end, int  $product_id, string $platform_id): array;

}
