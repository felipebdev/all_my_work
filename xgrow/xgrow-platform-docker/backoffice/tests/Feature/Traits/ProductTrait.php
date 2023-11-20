<?php

namespace Tests\Feature\Traits;

use App\Plan;
use App\PlanCategory;
use App\Platform;
use App\Product;

trait ProductTrait
{

    /**
     * Generate products with specific values
     * @param int $clientId
     * @return void
     */
    private function createProducts(int $clientId)
    {
        $category = PlanCategory::factory()->create(['name' => 'First Category']);
        $platform = Platform::factory()->create(
            [
                'name' => 'My Platform',
                'customer_id' => $clientId
            ]
        )->first();
        $product = Product::factory()->create(
            [
                'name' => 'First',
                'description' => 'Item 1',
                'analysis_status' => 'under_analysis',
                'category_id' => $category->id
            ]
        );
        Plan::factory()->create(
            [
                'product_id' => $product->id,
                'price' => 110.50,
                'platform_id' => $platform->id
            ]
        );

        $product = Product::factory()->create(
            [
                'name' => 'Second',
                'description' => 'Item 2',
                'analysis_status' => 'approved'
            ]
        );
        Plan::factory()->create(
            [
                'product_id' => $product->id,
                'platform_id' => $platform->id,
            ]
        );

        $product = Product::factory()->create(
            [
                'name' => 'Third',
                'description' => 'Item 3',
                'analysis_status' => 'approved'
            ]
        );
        Plan::factory()->create(
            [
                'product_id' => $product->id,
                'platform_id' => $platform->id
            ]
        );
    }

}
