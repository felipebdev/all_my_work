<?php

namespace Database\Factories;

use App\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlatformFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $clients = Client::pluck('id');
        return [
            'id' => $this->faker->uuid,
            'name' => $this->faker->company,
            'url' => $this->faker->url,
            'customer_id' => $this->faker->randomElement($clients),
            'name_slug'=>$this->faker->slug,
        ];
    }
}
