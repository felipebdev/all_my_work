<?php

namespace Database\Factories;

use App\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $platforms = Platform::pluck('id');
        return [
            'name' => $this->faker->name,
            'platform_id' => $this->faker->randomElement($platforms)
        ];
    }
}
