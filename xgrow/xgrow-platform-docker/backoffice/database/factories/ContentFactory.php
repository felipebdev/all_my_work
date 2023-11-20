<?php

namespace Database\Factories;

use App\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $authors = Author::pluck('id');

        return [
            'title' => substr($this->faker->sentence(3), 0, -1),
            'comments' => 0,
            'published' => $this->faker->boolean(),
            'author_id' => $this->faker->randomElement($authors),
        ];
    }
}
