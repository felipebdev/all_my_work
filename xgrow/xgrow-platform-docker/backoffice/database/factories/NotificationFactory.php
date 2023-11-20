<?php

use App\Notification;
use Faker\Generator as Faker;

$factory->define(Notification::class, function (Faker $faker) {
    return [
        'title'         => $faker->text(20),
        'body'          => $faker->text(400),
    ];
});
