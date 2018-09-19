<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Goods::class, function (Faker $faker) {
    $actual    = rand(100, 1000);
    $reserve   = rand(10, 50);
    $available = ($actual - $reserve);

    return [
        'name'               => $faker->word,
        'brand'              => $faker->word,
        'description'        => $faker->realText(),
        'quantity_actual'    => $actual,
        'quantity_reserve'   => $reserve,
        'quantity_available' => $available,
        'price'              => $faker->randomFloat(2, 10, 1000),
        'article'            => str_random(8),
        'country'            => $faker->country,
    ];
});
