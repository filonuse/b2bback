<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Address::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\uk_UA\Address($faker));

    return [
        'address' => $faker->address,
        'lat'     => $faker->latitude($min = 47.149416, $max = 52.379371),
        'lng'     => $faker->longitude($min = 22.137159, $max = 40.220480),
    ];
});
