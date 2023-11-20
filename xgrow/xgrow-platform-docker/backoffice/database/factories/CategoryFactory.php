<?php

namespace Database\Factories;

use App\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $platforms = Platform::pluck('id');
        $platform_id = $this->faker->randomElement($platforms);

        return [
            'platform_id' => $platform_id,
            'name' => $this->faker->name
        ];
    }
}

