<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class PlatformUserFactory extends Factory
{

    public function definition()
    {
        $userName = $this->faker->userName;
        return [
            'name' => $userName,
            'email' => $userName. '@xgrow.com',
            'password' => Hash::make('password'),
            'logout' => 0,
            'thumb_id' => 0,
            'permission_id' => null,
            'active' => 1,
            'two_factor_enabled' => 0,
            'accepted_terms' => 1,
        ];
    }
}
