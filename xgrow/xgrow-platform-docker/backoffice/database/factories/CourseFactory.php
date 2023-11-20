<?php

namespace Database\Factories;

use App\Author;
use App\Plan;
use App\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $authors = Author::pluck('id');
        $author_id = $this->faker->randomElement($authors);
        $platform_id = Author::find($author_id)->platform_id;
        $plans = Plan::where('platform_id', $platform_id)->get()->pluck('id');
        return [
            'name' => substr($this->faker->sentence(3), 0, -1),
            'platform_id' => $platform_id,
            'author_id' => $author_id,
            'template_id' => 0,
            'plan_id' => $this->faker->randomElement($plans)
        ];
    }
}
