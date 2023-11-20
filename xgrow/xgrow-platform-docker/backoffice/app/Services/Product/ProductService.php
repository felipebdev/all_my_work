<?php

namespace App\Services\Product;

use App\Repositories\PlanRepository;
use App\Repositories\ProductRepository;
use App\Services\Objects\ProductFilter;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * @var ProductRepository
     */
    private ProductRepository $product;

    /**
     * @var PlanRepository
     */
    private PlanRepository $plan;

    /**
     * @param ProductRepository $product
     * @param PlanRepository $plan
     */
    public function __construct(ProductRepository $product, PlanRepository $plan)
    {
        $this->product = $product;
        $this->plan = $plan;
    }

    /**
     * List products
     * @return object
     */
    public function listProducts($inputs)
    {
        $platformId = $inputs['platform_id'] ?? null;
        $search = $inputs['search'] ?? null;

        $filter = (new ProductFilter())
            ->setSearch($search)
            ->setPlatformId($platformId);

        return $this->product->listProductPlatform($filter)
            ->select('products.id as product_id',
                'products.name as product_name',
                'platforms.name as platform_name')->get();
    }

    /**
     *
     * Get Products
     * @param array $inputs
     * @return mixed
     */
    public function getProducts(array $inputs)
    {
        $status = $inputs['status'] ?? null;
        $search = $inputs['search'] ?? null;

        $filter = (new ProductFilter())
            ->setSearch($search)
             ->setAnalysisStatus($status);

        return $this->product->listProductsClientsAndPlans($filter)
                             ->select('products.id as product_id',
                                        'products.name as product_name',
                                        'categories.name as category_name',
                                        DB::raw('CONCAT(clients.first_name, " ", clients.last_name) as customer_name'),
                                        'platforms.name as platform_name',
                                        DB::raw('COALESCE(plans.price, "0.00") price'),
                                        'platforms.id as platform_id',
                                        'platforms.customer_id as customer_id',
                                        'products.analysis_status')
                            ->orderBy('products.name', 'ASC');
    }

    /**
     * Store the product.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $product = $this->product->create($data);
        $data['product_id'] = $product->id;
        $this->plan->create($data);
        return $this->product->findById($product->id);
    }

    /**
     * Display the specified product.
     *
     *
     * @param int $id
     * @return mixed
     */
    public function getProductById(int $id)
    {
        return $this->product->findById($id);
    }

    /**
     * Update the specified product.
     *
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data)
    {
        return $this->product->update($id, $data);
    }

    /**
     * Remove the specified product.
     *
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id)
    {
        $this->plan->deleteByProductId($id);
        $this->product->delete($id);
    }

    /**
     * Change product status
     *
     *
     * @param int $id
     * @param array $data
     * @return false|mixed
     */
    public function changeStatus(int $id, array $data)
    {
        return $this->product->changeStatus($id, $data);
    }

    /**
     * Change product summary
     *
     *
     * @return false|mixed
     */
    public function getProductSummary()
    {
        $filter = new ProductFilter;
        $approved = $this->product->listProductsClientsAndPlans(
            $filter->setAnalysisStatus('approved'))->count();

        $refused = $this->product->listProductsClientsAndPlans(
            $filter->setAnalysisStatus('refused'))->count();

        $under_analysis = $this->product->listProductsClientsAndPlans(
            $filter->setAnalysisStatus('under_analysis'))->count();

        $blocked = $this->product->listProductsClientsAndPlans(
            $filter->setAnalysisStatus('blocked'))->count();

        return [
            'approved' => $approved,
            'refused' => $refused,
            'under_analysis' => $under_analysis,
            'blocked' => $blocked,
        ];
    }

}
