<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 *
 */
class ProductMobileController extends Controller
{
    /**
     * @var Product
     */
    private $model;

    /**
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    /**
     * @return JsonResponse
     */
    public function getAllProducts(): JsonResponse
    {
        $products = $this->model->where('platform_id', Auth::user()->platform_id)
            ->with('plans')
            ->get()
            ->toArray();

        $myProducts = [];

        foreach ($products as $product) {

            foreach ($product['plans'] as $key => $plan) {
                if ($plan['id'] != $product['favorite_plan']) {
                    unset($product['plans'][$key]);
                }
            }

            $myProducts[] = [
                'product_name' => $product['name'],
                'price' => floatval(end($product['plans'])['price']),
                'status' => $product['status'] === 1 ? 'enabled' : 'disabled',
                'link' => config('app.url_checkout') . '/' . end($product['plans'])['platform_id'] . '/' . base64_encode(end($product['plans'])['id'])
            ];
        }

        return sizeof($myProducts) > 0 ? response()->json($myProducts) : response()->json(['error' => 'data_not_found'], 404);
    }
}
