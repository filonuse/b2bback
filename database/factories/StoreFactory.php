<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Store::class, function (Faker $faker) {
    $address = \App\Models\Address::all()->random(1)->first();

    return [
        'name'       => $faker->companySuffix . ' ' . strtoupper(str_random(3)),
        'legal_data' => $faker->unique()->company,
        'address_id' => $address->id,
    ];
});
