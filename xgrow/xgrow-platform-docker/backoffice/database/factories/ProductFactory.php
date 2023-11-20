<?php

namespace Database\Factories;

use App\PlanCategory;
use App\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $platforms = Platform::pluck('id');
        $categories = PlanCategory::pluck('id');
        return [
            'name' => ucwords(substr($this->faker->sentence(3), 0, -1)),
            'platform_id' => $this->faker->randomElement($platforms),
            'category_id' => $this->faker->randomElement($categories),
        ];
    }
}
