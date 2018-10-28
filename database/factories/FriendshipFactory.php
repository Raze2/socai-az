<?php

use Faker\Generator as Faker;

$factory->define(Social\Friendship::class, function (Faker $faker) {
	$user = $faker->numberBetween(1,800);
    return [
        'first_user' => $user,
        'second_user' => $faker->numberBetween(1,800),
        'acted_user' => $user,
        'status' => 'confirmed'
    ];
});
