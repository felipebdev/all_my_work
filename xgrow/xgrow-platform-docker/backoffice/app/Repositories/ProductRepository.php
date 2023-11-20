<?php

namespace App\Repositories;

use App\Product;
use App\Plan;
use App\Services\Objects\ProductFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\Storage\UploadedImage;


class ProductRepository
{

    /**
     * Get Products
     * @param ProductFilter|null $filter
     * @return Builder
     */
    public function listAll(?ProductFilter $filter = null): Builder{
        return  Product::when($filter,function ($query, $filter) {
            return Product::when($filter->id, function ($query, $id) {
                $query->where('products.id', $id);
                })
                ->when($filter->search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('products.name', 'LIKE', "%{$search}%");
                    });
                })
                ->when($filter->platformId, function ($query, $platformId) {
                    $query->where('products.platform_id', $platformId);
                })
                ->when($filter->analysisStatus, function ($query, $platformId) {
                    $query->where('products.analysis_status', $platformId);
                })
                ->when($filter->createdPeriod, function ($query, $periodFilter) {
                    $query->whereBetween('products.created_at', [$periodFilter->startDate, $periodFilter->endDate]);
                });
        });
    }

    /**
     * Get products by client
     * @param ProductFilter $filter
     * @return Builder
     */
    public function listProductClient(ProductFilter $filter): Builder{
        return $this->listAll($filter)
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->join('platforms', 'products.platform_id', '=', 'platforms.id')
            ->join('plans', 'plans.product_id', '=', 'products.id')
            ->leftJoin('plan_categories', 'products.category_id', '=', 'plan_categories.id')
            ->leftJoin('files', function($q){
                $q->on('files.filable_id', '=', 'products.id')
                    ->where('files.filable_type', Product::class);
            })
            ->when($filter->clientId, function ($query, $clientId) {
                $query->where('platforms.customer_id', $clientId);
            });
    }

    /**
     * Get products by client
     * @param ProductFilter $filter
     * @return Builder
     */
    public function listProductPlatform(ProductFilter $filter): Builder{
        return $this->listAll($filter)
            ->join('platforms', 'products.platform_id', '=', 'platforms.id');
    }

    /**
     * Get all Products
     *
     * @param ProductFilter $filter
     * @return Builder
     */
    public function listProductsClientsAndPlans(ProductFilter $filter): Builder
    {
        return $this->listAll($filter)
            ->leftJoin('platforms', 'products.platform_id', '=', 'platforms.id')
            ->leftJoin('clients', 'platforms.customer_id', '=', 'clients.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('plans', 'products.id', '=', 'plans.id');
    }

    /**
     * Store product
     *
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return Product::create([
            'name' => $data['name'],
            'platform_id' => $data['platform_id'],
            'analysis_status' => $data['analysis_status'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Get Product by ID
     *
     * @param int $id
     * @return mixed
     */
    public function findById(int $id)
    {
        $query = Product::select(
            'products.id as product_id',
            'products.name as product_name',
            'categories.name as category_name',
            DB::raw('CONCAT(clients.first_name, " ", clients.last_name) as customer_name'),
            'platforms.name as platform_name',
            'plans.price',
            'products.analysis_status',
            'plans.type_plan',
            'plans.price',
            'plans.payment_method_boleto',
            'plans.payment_method_credit_card',
            'plans.payment_method_pix',
            'plans.payment_method_multiple_cards',
            'plans.installment',
            'plans.checkout_layout',
            'plans.checkout_address'
            )
            ->leftJoin('platforms', 'products.platform_id', '=', 'platforms.id')
            ->leftJoin('clients', 'platforms.customer_id', '=', 'clients.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('plans', 'products.id', '=', 'plans.id');

        return $query->findOrFail($id);
    }

    /**
     * Update the product
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'name' => $data['name'],
            'platform_id' => $data['platform_id'],
            'analysis_status' => $data['analysis_status'],
            'updated_at' => Carbon::now(),
        ]);
        return $this->findById($id);
    }

    /**
     * Delete Product
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
    }

    /**
     * Change product status
     *
     * @param int $id
     * @param array $data
     * @return false|mixed
     */
    public function changeStatus(int $id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->analysis_status = $data['analysis_status'];
        $product->save();
        return $this->findById($id);
    }
}
