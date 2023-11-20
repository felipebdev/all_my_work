<?php

namespace Database\Factories;

use App\PlatformUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProducerFactory extends Factory
{

    public function definition()
    {
        $this->faker->addProvider(new \Faker\Provider\pt_BR\Person($this->faker));

        return [
            'platform_id' => '00000000-0000-0000-0000-000000000000',
            'platform_user_id' => PlatformUser::factory(),
            'accepted_terms' => 1,
            //'document_type' => NULL,
            'document' => $this->faker->cpf($formatted = true),
            'holder_name' => $this->faker->userName(),
            'account_type' => 'savings',
            'bank' => '001',
            'branch' => '0123',
            'account' => '2345',
            'branch_check_digit' => '1',
            'account_check_digit' => '1',
            'document_verified' => 0,
            //'recipient_id' => NULL,
        ];
    }

    public function withRecipientId()
    {
        return $this->state(function (array $attributes) {
            return [
                'recipient_id' => 'rp_0000000000000000',
            ];
        });
    }


}
