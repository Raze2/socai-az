<?php

use Faker\Generator as Faker;

$factory->define(App\Friendship::class, function (Faker $faker) {
	$user = factory('App\User')->create()->id;
    return [
        'first_user' => $user,
        'second_user' => $user,
        'acted_user' => factory('App\User')->create()->id,
        'status' => 'confirmed'
    ];
});
