<?php

use Faker\Generator as Faker;

$factory->define(Social\Friendship::class, function (Faker $faker) {
	$user = $faker->numberBetween(1,500);
    return [
        'first_user' => $user,
        'second_user' => $faker->numberBetween(1,500),
        'acted_user' => $user,
        'status' => $faker->randomElement(['confirmed', 'pending', 'blocked'])
    ];
});
