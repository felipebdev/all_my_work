<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EmailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'area' => '1',
            'subject' => $this->faker->text(30),
            'message' => $this->faker->text(100),
            'from' => $this->faker->unique()->safeEmail(),
        ];
    }
}
