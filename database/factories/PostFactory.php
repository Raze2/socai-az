<?php

use Faker\Generator as Faker;

$factory->define(Social\Post::class, function (Faker $faker) {
    return [
        'body' => $faker->text(),
        'privacy' => $faker->randomElement(['public', 'friends', 'private']),
        'user_id' => $faker->numberBetween(1,500)
    ];
});
