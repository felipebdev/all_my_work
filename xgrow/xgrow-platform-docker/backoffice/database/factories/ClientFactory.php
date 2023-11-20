<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'type_person' => 'F',
            'cpf' => $this->faker->randomDigit(),
            'fantasy_name' => $this->faker->text(100),
            'company_name' => $this->faker->company,
            'company_url' => $this->faker->url,
            'address' => substr($this->faker->address, 0, 50),
            'number' => $this->faker->randomDigit(),
            'complement' => $this->faker->randomDigit(),
            'district' => $this->faker->word(),
            'city' => $this->faker->city,
            'state' => $this->faker->randomElement(['SP', 'MG', 'RJ', 'PR', 'BA']),
            'zipcode' => $this->faker->randomDigit(),
        ];
    }
}
