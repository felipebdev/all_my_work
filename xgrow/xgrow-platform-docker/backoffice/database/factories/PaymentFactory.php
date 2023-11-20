<?php

namespace Database\Factories;

use App\Payment;
use App\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
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
            'platform_id' => $this->faker->randomElement($platforms),
            'id_webhook' => 1,
            'price' => $this->faker->randomFloat(2, 100, 500),
            'payment_date' => $this->faker->date('Y-m-d'),
            'status' => $this->faker->randomElement(Payment::listStatus())
        ];
    }
}
