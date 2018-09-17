<?php

use Faker\Generator as Faker;

$factory->define(App\Like::class, function (Faker $faker) {
    return [
        'post_id' => $faker->numberBetween(1,6000),
        'user_id' => $faker->numberBetween(1,2000)
    ];
});
