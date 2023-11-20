<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Payment;
use Faker\Generator as Faker;

$factory->define(Payment::class, function (Faker $faker) {
    return [
        'platform_id' => '1197ebe1-7931-40e6-a64d-e5922236c958', //Mudar se usar em outra plataforma para teste
        'price' => $faker->randomElement([100.00, 970.99, 50.10, 380.00]),
        'payment_date' => '2020-04-04',
        'type_payment' => $faker->randomElement(['credit_card', 'boleto', 'pix']),
        'status' => $faker->randomElement(['paid', 'pending', 'canceled']),
        'created_at' => '2020-12-23 21:17:24', 'updated_at' => '2020-12-23 21:17:24',
        'subscriber_id' => $faker->randomElement([14588, 14653, 14832, 14848, 14856, 14871, 14861]),
        'id_webhook' => 0,
    ];
});
