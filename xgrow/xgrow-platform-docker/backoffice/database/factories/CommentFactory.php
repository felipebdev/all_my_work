<?php

use App\Comment;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'text'=> $faker->text(20),
        
    ];
});
