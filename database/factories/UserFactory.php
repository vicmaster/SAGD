<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function ($faker) {
    return [
        'email' => $faker->email,
        'password' => Hash::make(str_random(10)),
        'remember_token' => str_random(10),
        'morphable_id' => $faker->randomDigit,
        'morphable_type' => $faker->word
    ];
});
