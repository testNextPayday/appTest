<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(\App\Models\Staff::class, function (Faker $faker) {
    return [
        'firstname' => $faker->firstname,
        'lastname' => $faker->lastname,
        'midname' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->unique()->phoneNumber,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'avatar' => 'public/defaults/avatars/default.png',
        'remember_token' => Str::random(10),
        'reference'=>'USC-'.time().rand(1,7).$faker->randomElement(range(1,100)),
        'wallet' => 0.00,
        'escrow' => 0.00
    ];
});
