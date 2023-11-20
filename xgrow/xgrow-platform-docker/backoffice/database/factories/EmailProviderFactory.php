<?php

namespace Database\Factories;

use App\EmailProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->slug(2),
            'from_name' => $this->faker->name(),
            'from_address' => $this->faker->email(),
            'driver' => $this->faker->randomElement(EmailProvider::DRIVERS),
            'settings' => "{}",
        ];
    }
}
