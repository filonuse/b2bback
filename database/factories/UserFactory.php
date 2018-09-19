<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\uk_UA\Address($faker));
    $faker->addProvider(new \Faker\Provider\uk_UA\Person($faker));
    $faker->addProvider(new \Faker\Provider\uk_UA\Company($faker));
    $faker->addProvider(new \Faker\Provider\uk_UA\PhoneNumber($faker));
    $faker->addProvider(new \Faker\Provider\uk_UA\Text($faker));

    return [
        'name'          => $faker->name,
        'legal_name'    => $faker->company,
        'email'         => $faker->unique()->safeEmail,
        'phone'         => $faker->unique()->phoneNumber,
        'password'      => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'official_data' => $faker->address,
        'requisites'    => $faker->bankAccountNumber,
    ];
});
