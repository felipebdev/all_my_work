<?php

use App\Message;
use Faker\Generator as Faker;

$factory->define(Message::class, function (Faker $faker) {
    return [
        'title'         => $faker->text(20),
        'body'          => $faker->text(400),
    ];
});
