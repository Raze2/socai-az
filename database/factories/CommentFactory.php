<?php

use Faker\Generator as Faker;

$factory->define(Social\Comment::class, function (Faker $faker) {
    return [
    	'body' => $faker->text(),
        'post_id' => $faker->numberBetween(5000,10000),
        'user_id' => $faker->numberBetween(1,1000)
    ];
});
