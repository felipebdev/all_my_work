<?php

namespace Database\Factories;

use App\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $products = Product::pluck('id');
        $product_id = $this->faker->randomElement($products);
        $platform_id = Product::find($product_id)->platform_id;
        return [
            'product_id' => $product_id,
            'platform_id' => $platform_id,
            'name' => $this->faker->name,
            'status' => 1,
            'currency' => 'BRL',
            'price' => $this->faker->randomFloat(2, 100, 500),
        ];
    }
}
