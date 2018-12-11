<?php

use Faker\Generator as Faker;

$factory->define(Social\Friendship::class, function (Faker $faker) {
	$user = $faker->numberBetween(1,1000);
    return [
        'first_user' => $user,
        'second_user' => $faker->numberBetween(1,1000),
        'acted_user' => $user,
        'status' => $faker->randomElement(['confirmed', 'pending', 'blocked'])
    ];
});
