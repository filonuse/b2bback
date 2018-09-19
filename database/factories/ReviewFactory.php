<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Review::class, function (Faker $faker) {
    // exclude the admin
    $User = \App\Models\User::where('id', '!=', 1)->pluck('id');
    $Goods = \App\Models\Goods::all()->pluck('id');

    $type = collect(['User', 'Goods'])->random(1)->first();

    return [
        'from_user_id' => $User->random(),
        'review'       => $faker->realText(),
        'estimate'     => rand(1, 5),
        'reviewable_id' => $$type->random(),
        'reviewable_type' => "App\\Models\\$type",
    ];
});
