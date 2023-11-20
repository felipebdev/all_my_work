<?php

namespace Database\Factories;

use App\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class BackPermissionFactory extends Factory
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
            'name' => $this->faker->name
        ];
    }
}
